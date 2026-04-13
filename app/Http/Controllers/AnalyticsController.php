<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    private function applyPeriodFilter($query, Request $request)
    {
        // 1. Constrain to operation hours only: 15:00 to 23:59:59 (ignoring morning tests)
        $query->whereTime('orders.created_at', '>=', '15:00:00')
              ->whereTime('orders.created_at', '<=', '23:59:59');

        $period = $request->get('period', 'Today');
        $now = Carbon::now();

        // 2. Also constrain by day boundary
        if ($period === 'Today') {
            $query->whereDate('orders.created_at', $now->toDateString());
        } elseif ($period === 'This Week') {
            $query->where('orders.created_at', '>=', $now->copy()->startOfWeek()->toDateString());
        } elseif ($period === 'This Month') {
            $query->where('orders.created_at', '>=', $now->copy()->startOfMonth()->toDateString());
        }

        return $query;
    }

    public function summary(Request $request)
    {
        $paidStatuses = ['completed', 'done'];

        // Build base query mapped to period
        $queryBase = $this->applyPeriodFilter(Order::query(), $request);

        $revenue = (clone $queryBase)->whereIn('status', $paidStatuses)->sum('total');
        $orders  = (clone $queryBase)->count();
        $avg     = (clone $queryBase)->whereIn('status', $paidStatuses)->avg('total') ?? 0;

        return response()->json([
            'period_revenue' => (float) $revenue,
            'period_orders'  => $orders,
            'avg_order_value'=> (float) $avg,
        ]);
    }

    public function chart(Request $request)
    {
        $paidStatuses = ['completed', 'done'];
        $period = $request->get('period', 'Today');
        $chartData = [];

        if ($period === 'Today') {
            // Hourly blocks from 15:00 to 23:00
            for ($h = 15; $h <= 23; $h++) {
                $start = Carbon::today()->setTime($h, 0, 0);
                $end = Carbon::today()->setTime($h, 59, 59);

                $revenue = Order::whereBetween('created_at', [$start, $end])->whereIn('status', $paidStatuses)->sum('total');
                $orders = Order::whereBetween('created_at', [$start, $end])->count();
                $chartData[] = [
                    'date' => sprintf('%02d:00', $h),
                    'revenue' => (float)$revenue,
                    'orders' => $orders
                ];
            }
        } else {
            // Daily blocks: 7 for Week, 30 for Month
            $days = ($period === 'This Month') ? 29 : 6;
            for ($i = $days; $i >= 0; $i--) {
                $start = Carbon::today()->subDays($i)->setTime(15, 0, 0);
                $end = Carbon::today()->subDays($i)->endOfDay();

                $revenue = Order::whereBetween('created_at', [$start, $end])->whereIn('status', $paidStatuses)->sum('total');
                $orders = Order::whereBetween('created_at', [$start, $end])->count();

                $chartData[] = [
                    'date' => $start->format('d M'),
                    'revenue' => (float)$revenue,
                    'orders' => $orders
                ];
            }
        }

        return response()->json($chartData);
    }

    public function topMenus(Request $request)
    {
        $query = OrderItem::select('menus.name', DB::raw('SUM(order_items.quantity) as total_sold'), DB::raw('SUM(order_items.subtotal) as revenue'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id');

        $this->applyPeriodFilter($query, $request);

        $topMenus = $query->groupBy('menus.id', 'menus.name')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->name,
                    'total_sold' => (int) $item->total_sold,
                    'revenue' => (float) $item->revenue
                ];
            });

        return response()->json($topMenus);
    }

    public function paymentMethods(Request $request)
    {
        $query = Order::select('payment_method as method', DB::raw('COUNT(*) as total'), DB::raw('SUM(total) as revenue'));
        $this->applyPeriodFilter($query, $request);

        $paymentMethods = $query->groupBy('payment_method')
            ->get()
            ->map(function ($item) {
                return [
                    'method' => $item->method ?? 'Belum Dibayar',
                    'total' => (int) $item->total,
                    'revenue' => (float) $item->revenue
                ];
            });

        return response()->json($paymentMethods);
    }

    public function peakHours(Request $request)
    {
        $query = Order::select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as orders'));
        $this->applyPeriodFilter($query, $request);

        $peakHours = $query->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Limit to operational hours (15 to 23)
        $fullDay = [];
        $existingHours = $peakHours->pluck('orders', 'hour')->toArray();

        for ($h = 15; $h <= 23; $h++) {
            $fullDay[] = [
                'hour' => sprintf('%02d:00', $h),
                'orders' => $existingHours[$h] ?? 0
            ];
        }

        return response()->json($fullDay);
    }
}

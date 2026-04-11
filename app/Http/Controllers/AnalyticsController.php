<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function summary()
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        $todayRevenue = Order::whereDate('created_at', $today)->where('status', 'completed')->sum('total');
        $weekRevenue = Order::where('created_at', '>=', $startOfWeek)->where('status', 'completed')->sum('total');
        $monthRevenue = Order::where('created_at', '>=', $startOfMonth)->where('status', 'completed')->sum('total');

        $todayOrders = Order::whereDate('created_at', $today)->count();
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'completed')->count();
        $avgOrderValue = Order::where('status', 'completed')->avg('total') ?? 0;

        return response()->json([
            'today_revenue' => (float) $todayRevenue,
            'this_week_revenue' => (float) $weekRevenue,
            'this_month_revenue' => (float) $monthRevenue,
            'today_orders' => $todayOrders,
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'completed_orders' => $completedOrders,
            'avg_order_value' => (float) $avgOrderValue,
        ]);
    }

    public function chart()
    {
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $revenue = Order::whereDate('created_at', $date)->sum('total');
            $orders = Order::whereDate('created_at', $date)->count();

            $last7Days[] = [
                'date' => $date->format('Y-m-d'),
                'revenue' => (float)$revenue,
                'orders' => $orders
            ];
        }

        return response()->json($last7Days);
    }

    public function topMenus()
    {
        $topMenus = OrderItem::select('menus.name', DB::raw('SUM(order_items.quantity) as total_sold'), DB::raw('SUM(order_items.subtotal) as revenue'))
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->groupBy('menus.id', 'menus.name')
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

    public function paymentMethods()
    {
        $paymentMethods = Order::select('payment_method as method', DB::raw('COUNT(*) as total'), DB::raw('SUM(total) as revenue'))
            ->groupBy('payment_method')
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

    public function peakHours()
    {
        $peakHours = Order::select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as orders'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Ensure all 24 hours are present
        $fullDay = [];
        $existingHours = $peakHours->pluck('orders', 'hour')->toArray();

        for ($h = 0; $h < 24; $h++) {
            $fullDay[] = [
                'hour' => sprintf('%02d:00', $h),
                'orders' => $existingHours[$h] ?? 0
            ];
        }

        return response()->json($fullDay);
    }
}

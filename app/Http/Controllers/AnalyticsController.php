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
            // Hourly blocks for the full day to catch all orders
            for ($h = 0; $h <= 23; $h++) {
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
                $start = Carbon::today()->subDays($i)->startOfDay();
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
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->whereIn('orders.status', ['completed']);

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
        $query = Order::select('payment_method as method', DB::raw('COUNT(*) as total'), DB::raw('SUM(total) as revenue'))
            ->whereIn('status', ['completed']);
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

        $fullDay = [];
        $existingHours = $peakHours->pluck('orders', 'hour')->toArray();

        for ($h = 0; $h <= 23; $h++) {
            $fullDay[] = [
                'hour' => sprintf('%02d:00', $h),
                'orders' => $existingHours[$h] ?? 0
            ];
        }

        return response()->json($fullDay);
    }

    public function dashboardStats()
    {
        $today = Carbon::today();

        $totalOrdersToday = Order::whereDate('created_at', $today)->count();
        $pendingOrders    = Order::whereDate('created_at', $today)->where('status', 'pending')->count();
        $todayRevenue     = Order::whereDate('created_at', $today)
                                ->whereIn('status', ['completed', 'done'])
                                ->sum('total');
        $incompleteOrders = Order::whereDate('created_at', $today)
                                ->whereNotIn('status', ['completed', 'cancelled', 'done'])
                                ->count();

        return response()->json([
            'total_orders_today' => $totalOrdersToday,
            'pending_orders'     => $pendingOrders,
            'today_revenue'      => (float) $todayRevenue,
            'incomplete_orders'  => $incompleteOrders,
        ]);
    }

    public function transactionHistory(Request $request)
    {
        $query = Order::select(
            'id',
            'order_number',
            'customer_name',
            'total',
            'payment_method',
            DB::raw("CONVERT_TZ(orders.created_at, '+00:00', '+07:00') as created_at")
        )->where('status', 'completed');

        $period = $request->get('period');
        if ($period !== 'Semua' && $period !== 'all') {
            $this->applyPeriodFilter($query, $request);
        }

        $transactions = $query->orderBy('orders.created_at', 'desc')
            ->limit(500)
            ->get();

        return response()->json($transactions);
    }

    public function exportDaily(Request $request)
    {
        // Auth manual — route ini diluar auth:sanctum
        $token = $request->query('token');
        if (!$token) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $pat = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        if (!$pat) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        auth()->loginUsingId($pat->tokenable_id);

        // Ambil period dari query param, default 'Today'
        $period = $request->query('period', 'Today');
        $skipFilter = ($period === 'Semua' || $period === 'all');
        $req = new Request(['period' => $period]);
        $date = Carbon::today()->format('Y-m-d');

        // Transactions
        $query = Order::select(
            'id',
            'order_number',
            'customer_name',
            'total',
            'payment_method',
            DB::raw("CONVERT_TZ(orders.created_at, '+00:00', '+07:00') as created_at")
        )->where('status', 'completed');
        if (!$skipFilter) {
            $this->applyPeriodFilter($query, $req);
        }
        $transactions = $query->orderBy('orders.created_at', 'desc')->get();

        $totalRevenue = $transactions->sum('total');
        $totalOrders = $transactions->count();
        $avgOrder = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Payment Methods
        $paymentQuery = Order::select('payment_method as method', DB::raw('COUNT(*) as total'), DB::raw('SUM(total) as revenue'))
            ->whereIn('status', ['completed']);
        if (!$skipFilter) {
            $this->applyPeriodFilter($paymentQuery, $req);
        }
        $paymentMethods = $paymentQuery->groupBy('payment_method')->get()->map(function($i) {
            return ['method' => $i->method ?: 'Belum Dibayar', 'total' => $i->total, 'revenue' => $i->revenue];
        })->toArray();

        // Top Menus
        $topMenuQuery = \App\Models\OrderItem::select('menus.name', DB::raw('SUM(order_items.quantity) as total_sold'), DB::raw('SUM(order_items.subtotal) as revenue'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->whereIn('orders.status', ['completed']);
        if (!$skipFilter) {
            $this->applyPeriodFilter($topMenuQuery, $req);
        }
        $topMenus = $topMenuQuery->groupBy('menus.id', 'menus.name')
            ->orderBy('total_sold', 'desc')->limit(5)->get()->toArray();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.daily-report', [
            'transactions' => $transactions,
            'date' => $date,
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'avgOrder' => $avgOrder,
            'paymentMethods' => $paymentMethods,
            'topMenus' => $topMenus
        ]);

        $filename = 'laporan-harian-' . $date . '.pdf';
        return $pdf->download($filename);
    }

    public function exportMonthly(Request $request)
    {
        // Auth manual — route ini diluar auth:sanctum
        $token = $request->query('token');
        if (!$token) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $pat = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        if (!$pat) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        auth()->loginUsingId($pat->tokenable_id);

        $period = $request->query('period', 'This Month');
        $month = Carbon::now()->format('Y-m');
        $filename = 'laporan-bulanan-' . $month . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\MonthlyReportExport($month, $period), $filename);
    }
}

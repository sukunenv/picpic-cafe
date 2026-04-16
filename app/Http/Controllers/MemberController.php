<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\PointTransaction;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MemberController extends Controller
{
    private function getTierConfig()
    {
        $settings = SystemSetting::where('group', 'loyalty')->get()->pluck('value', 'key');
        
        return [
            'silver_min' => (int)($settings['tier_silver_min'] ?? 501),
            'gold_min' => (int)($settings['tier_gold_min'] ?? 1501),
        ];
    }

    private function getTier($points, $config)
    {
        if ($points >= $config['gold_min']) return 'Gold';
        if ($points >= $config['silver_min']) return 'Silver';
        return 'Regular';
    }

    public function index(Request $request)
    {
        $tierConfig = $this->getTierConfig();
        
        $query = User::where('role', 'member');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->tier && strtolower($request->tier) !== 'semua') {
            $tier = strtolower($request->tier);
            if ($tier === 'gold') {
                $query->where('points', '>=', $tierConfig['gold_min']);
            } elseif ($tier === 'silver') {
                $query->where('points', '>=', $tierConfig['silver_min'])
                      ->where('points', '<', $tierConfig['gold_min']);
            } elseif ($tier === 'regular') {
                $query->where('points', '<', $tierConfig['silver_min']);
            }
        }

        $perPage = $request->get('per_page', 10);
        $paginator = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Calculate Tier
        $paginator->getCollection()->transform(function ($user) use ($tierConfig) {
            $user->tier = $this->getTier($user->points, $tierConfig);
            return $user;
        });

        return response()->json($paginator);
    }

    public function show($id)
    {
        $user = User::where('role', 'member')->findOrFail($id);
        $tierConfig = $this->getTierConfig();
        
        $user->tier = $this->getTier($user->points, $tierConfig);
        
        $orders = Order::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total_orders' => $orders->count(),
            'total_spent'  => (float)$orders->where('status', 'completed')->sum('total'),
            'last_order'   => $orders->first() ? $orders->first()->created_at : null,
        ];

        return response()->json([
            'member' => $user,
            'stats' => $stats,
            'orders' => $orders
        ]);
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->deleted_at) {
            $user->restore();
            $message = 'Akun berhasil diaktifkan kembali.';
        } else {
            $user->delete();
            $message = 'Akun berhasil dinonaktifkan.';
        }

        return response()->json([
            'message' => $message,
            'is_active' => !$user->deleted_at
        ]);
    }

    public function resetPoints(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $oldPoints = $user->points;

        $user->points = 0;
        $user->save();

        // Catat ke ledger
        PointTransaction::record(
            userId     : $user->id,
            type       : 'reset',
            amount     : -$oldPoints,
            balanceAfter: 0,
            description: 'Reset poin oleh admin (dari ' . $oldPoints . ' poin)',
            orderId    : null,
            performedBy: $request->user()?->id
        );

        return response()->json([
            'message' => 'Poin member berhasil direset menjadi 0.',
            'points'  => 0
        ]);
    }
}

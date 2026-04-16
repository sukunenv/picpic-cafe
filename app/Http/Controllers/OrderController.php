<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Cart;
use App\Models\PointTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // If user is authenticated and not admin, scope to their orders only
        $query = Order::with(['orderItems.menu']);

        if ($user && !$user->is_admin) {
            $query->where('user_id', $user->id);
        }

        if ($request->has('status') && $request->status !== 'Semua') {
            $status = strtolower($request->status);
            // Map text filters to database values if needed (Lunas -> completed)
            if ($status === 'lunas') $status = 'completed';
            $query->where('status', $status);
        }

        $perPage = $request->get('per_page', 10);
        $orders = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        
        // Handle POS mode (items sent directly)
        if ($request->has('items')) {
            $items = $request->items;
            if (empty($items)) {
                return response()->json(['message' => 'No items provided'], 400);
            }

            DB::beginTransaction();
            try {
                $subtotal = 0;
                foreach ($items as $item) {
                    $subtotal += $item['price'] * $item['quantity'];
                }
                $total = $subtotal;

                $order = Order::create([
                    'user_id' => $user->id,
                    'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                    'status' => 'pending',
                    'subtotal' => $subtotal,
                    'total' => $total,
                    'notes' => $request->notes,
                    'customer_name' => $request->customer_name,
                    'table_number' => $request->table_number,
                    'payment_method' => $request->payment_method,
                ]);

                foreach ($items as $item) {
                    $order->orderItems()->create([
                        'menu_id' => $item['menu_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'subtotal' => $item['price'] * $item['quantity'],
                        'notes' => $item['notes'] ?? null,
                    ]);
                }

                DB::commit();
                return response()->json($order->load('orderItems.menu'), 201);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['message' => 'Failed to create order', 'error' => $e->getMessage()], 500);
            }
        }

        // Regular cart-based mode
        $carts = Cart::where('user_id', $user->id)->with('menu')->get();

        if ($carts->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        DB::beginTransaction();
        try {
            $subtotal = 0;
            foreach ($carts as $cart) {
                $subtotal += $cart->menu->price * $cart->quantity;
            }

            $total = $subtotal;

            $status = $request->payment_method === 'cash' ? 'pending' : 'waiting_confirmation';

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'status' => $status,
                'subtotal' => $subtotal,
                'total' => $total,
                'notes' => $request->notes,
                'customer_name' => $user->name,
                'payment_method' => $request->payment_method,
            ]);

            foreach ($carts as $cart) {
                $order->orderItems()->create([
                    'menu_id' => $cart->menu_id,
                    'quantity' => $cart->quantity,
                    'price' => $cart->menu->price,
                    'subtotal' => $cart->menu->price * $cart->quantity,
                    'notes' => $cart->notes,
                ]);
            }

            Cart::where('user_id', $user->id)->delete();

            DB::commit();

            return response()->json($order->load('orderItems.menu'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create order', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(Request $request, Order $order)
    {
        $user = $request->user();

        // Admin can view any order; customers can only view their own
        if (!$user->is_admin && $order->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($order->load(['orderItems.menu']));
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|string|in:pending,completed,cancelled'
        ]);

        // --- IDEMPOTENT CHECK ---
        // Jika status yang dikirim sama dengan status sekarang,
        // langsung return response sukses tanpa proses apapun
        if ($order->status === $request->status) {
            return response()->json([
                'message' => 'Order status sudah ' . $order->status . ', tidak ada perubahan.',
                'order'   => $order->load(['orderItems.menu']),
            ]);
        }

        $oldStatus = $order->status;

        try {
            DB::transaction(function () use ($request, $order, $oldStatus) {
                // 1) Update status order
                $order->status = $request->status;
                $order->save();

                // 2) Loyalty Point logic — hanya saat status berubah KE completed
                //    Guard points_awarded mencegah double-add walau request dikirim ulang
                if (
                    $oldStatus !== 'completed'
                    && $order->status === 'completed'
                    && $order->user
                    && !$order->points_awarded
                ) {
                    $value      = \App\Models\SystemSetting::where('key', 'point_value')->value('value') ?? 5000;
                    $multiplier = \App\Models\SystemSetting::where('key', 'point_multiplier')->value('value') ?? 10;
                    $months     = \App\Models\SystemSetting::where('key', 'point_expiry_months')->value('value') ?? 3;

                    $points = (int) floor($order->total / $value) * $multiplier;

                    if ($points > 0) {
                        $user = $order->user;

                        // 3) Tambah poin member
                        $user->increment('points', $points);
                        $user->point_expires_at = now()->addMonths((int) $months);
                        $user->save();

                        // 4) Tandai order sudah diberi poin (idempotency flag)
                        $order->points_awarded = true;
                        $order->saveQuietly();

                        // 5) Catat ke ledger — semua dalam satu transaksi atomik
                        PointTransaction::record(
                            userId      : $user->id,
                            type        : 'earn',
                            amount      : $points,
                            balanceAfter: $user->points,
                            description : 'Poin dari order #' . $order->order_number,
                            orderId     : $order->id,
                            performedBy : $request->user()?->id
                        );
                    }
                }
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengupdate status order.',
                'error'   => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Order status updated successfully',
            'order' => $order->load(['orderItems.menu'])
        ]);
    }
}

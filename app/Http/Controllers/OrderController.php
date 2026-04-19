<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Cart;
use App\Models\Menu;
use App\Models\MenuVariant;
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
        $query = Order::with(['orderItems.menu.category', 'orderItems.variant']);

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
        $orders  = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        // Handle POS mode (items sent directly) - sering dipakai Admin/Kasir
        if ($request->has('items')) {
            $items = $request->items;
            if (empty($items)) {
                return response()->json(['message' => 'No items provided'], 400);
            }

            DB::beginTransaction();
            try {
                $subtotal       = 0;
                $processedItems = [];

                // SECURITY: Jangan percaya harga dari frontend. Ambil harga dari Database.
                foreach ($items as $item) {
                    $menu = Menu::findOrFail($item['menu_id']);

                    // Jika menu punya varian, variant_id wajib ada
                    if ($menu->variants()->exists()) {
                        if (empty($item['variant_id'])) {
                            DB::rollBack();
                            return response()->json([
                                'message' => "Menu \"{$menu->name}\" memiliki varian. Harap pilih varian terlebih dahulu.",
                            ], 422);
                        }

                        $variant = MenuVariant::where('id', $item['variant_id'])
                                              ->where('menu_id', $menu->id)
                                              ->firstOrFail();

                        $itemPrice = $variant->price;
                    } else {
                        $itemPrice = $menu->price;
                    }

                    $itemQty      = (int) $item['quantity'];
                    $itemSubtotal = $itemPrice * $itemQty;
                    $subtotal    += $itemSubtotal;

                    $processedItems[] = [
                        'menu_id'    => $menu->id,
                        'variant_id' => $item['variant_id'] ?? null,
                        'quantity'   => $itemQty,
                        'price'      => $itemPrice,
                        'subtotal'   => $itemSubtotal,
                        'notes'      => $item['notes'] ?? null,
                    ];
                }

                $softOpeningStart = '2026-04-19';
                $softOpeningEnd   = '2026-04-24';
                $today = now()->toDateString();
                $isSoftOpeningRange = ($today >= $softOpeningStart && $today <= $softOpeningEnd);

                // If Kasir sends is_soft_opening=false explicitly, we can override, but default is auto
                $applyDiscount = $request->get('is_soft_opening', $isSoftOpeningRange);

                $discountAmount = 0;
                $discountPercent = 0;
                if ($applyDiscount) {
                    $discountPercent = 25;
                    $discountAmount = $subtotal * 0.25;
                }
                $total = $subtotal - $discountAmount;

                $order = Order::create([
                    'user_id'        => $request->user_id ?? $user->id,
                    'order_number'   => 'ORD-' . strtoupper(Str::random(10)),
                    'status'         => 'pending',
                    'subtotal'       => $subtotal,
                    'discount_amount' => $discountAmount,
                    'discount_percent' => $discountPercent,
                    'total'          => $total,
                    'notes'          => $request->notes,
                    'customer_name'  => $request->customer_name,
                    'table_number'   => $request->table_number,
                    'payment_method' => $request->payment_method,
                ]);

                foreach ($processedItems as $pItem) {
                    $order->orderItems()->create($pItem);
                }

                DB::commit();
                return response()->json($order->load('orderItems.menu.category', 'orderItems.variant'), 201);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['message' => 'Failed to create order', 'error' => $e->getMessage()], 500);
            }
        }

        // Regular cart-based mode - dipakai Member di Web
        $carts = Cart::where('user_id', $user->id)->with(['menu', 'menu.variants', 'variant'])->get();

        if ($carts->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        DB::beginTransaction();
        try {
            $subtotal = 0;
            foreach ($carts as $cart) {
                // SECURITY: Gunakan harga dari model yang di-load dari DB, bukan input.
                // Jika cart punya variant, ambil harga dari variant. Jika tidak, dari menu.
                if ($cart->variant_id && $cart->variant) {
                    $itemPrice = $cart->variant->price;
                } else {
                    $itemPrice = $cart->menu->price;
                }
                $subtotal += $itemPrice * $cart->quantity;
            }

            $softOpeningStart = '2026-04-19';
            $softOpeningEnd   = '2026-04-24';
            $today = now()->toDateString();
            $isSoftOpeningRange = ($today >= $softOpeningStart && $today <= $softOpeningEnd);

            $discountAmount = 0;
            $discountPercent = 0;
            if ($isSoftOpeningRange) {
                $discountPercent = 25;
                $discountAmount = $subtotal * 0.25;
            }
            $total = $subtotal - $discountAmount;

            $status = $request->payment_method === 'cash' ? 'pending' : 'waiting_confirmation';

            $order = Order::create([
                'user_id'        => $user->id,
                'order_number'   => 'ORD-' . strtoupper(Str::random(10)),
                'status'         => $status,
                'subtotal'       => $subtotal,
                'discount_amount' => $discountAmount,
                'discount_percent' => $discountPercent,
                'total'          => $total,
                'notes'          => $request->notes,
                'customer_name'  => $user->name,
                'payment_method' => $request->payment_method,
            ]);

            foreach ($carts as $cart) {
                if ($cart->variant_id && $cart->variant) {
                    $itemPrice = $cart->variant->price;
                } else {
                    $itemPrice = $cart->menu->price;
                }

                $order->orderItems()->create([
                    'menu_id'    => $cart->menu_id,
                    'variant_id' => $cart->variant_id,
                    'quantity'   => $cart->quantity,
                    'price'      => $itemPrice,
                    'subtotal'   => $itemPrice * $cart->quantity,
                    'notes'      => $cart->notes,
                ]);
            }

            Cart::where('user_id', $user->id)->delete();

            DB::commit();
            return response()->json($order->load('orderItems.menu.category', 'orderItems.variant'), 201);
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

        return response()->json($order->load(['orderItems.menu.category', 'orderItems.variant']));
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
                'order'   => $order->load(['orderItems.menu.category', 'orderItems.variant']),
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
                            performedBy : request()->user()?->id
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
            'order'   => $order->load(['orderItems.menu.category', 'orderItems.variant'])
        ]);
    }
}

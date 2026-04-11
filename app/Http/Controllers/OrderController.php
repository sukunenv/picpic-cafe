<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
                       ->with(['orderItems.menu'])
                       ->get();
        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $user = $request->user();
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

            // Total equals subtotal for now
            $total = $subtotal;

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'total' => $total,
                'notes' => $request->notes,
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
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($order->load(['orderItems.menu']));
    }

    public function update(Request $request, Order $order) {}
    public function destroy(Order $order) {}
}

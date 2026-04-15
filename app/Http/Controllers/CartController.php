<?php
 
namespace App\Http\Controllers;
 
use App\Models\Cart;
use Illuminate\Http\Request;
 
class CartController extends Controller
{
    public function index(Request $request)
    {
        $carts = Cart::where('user_id', $request->user()->id)->with('menu')->get();
        return response()->json($carts);
    }
 
    public function store(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);
 
        $cart = Cart::where('user_id', $request->user()->id)
                    ->where('menu_id', $request->menu_id)
                    ->first();
 
        if ($cart) {
            $cart->update([
                'quantity' => $cart->quantity + $request->quantity,
                'notes' => $request->notes ?? $cart->notes
            ]);
        } else {
            $cart = Cart::create([
                'user_id' => $request->user()->id,
                'menu_id' => $request->menu_id,
                'quantity' => $request->quantity,
                'notes' => $request->notes
            ]);
        }
 
        return response()->json($cart->load('menu'), 201);
    }
 

    public function destroy(Cart $cart)
    {
        if ($cart->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $cart->delete();
        return response()->json(['message' => 'Item removed from cart']);
    }

    public function clear(Request $request)
    {
        Cart::where('user_id', $request->user()->id)->delete();
        return response()->json(['message' => 'Cart cleared successfully']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Menu;
use App\Models\MenuVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $carts = Cart::where('user_id', $request->user()->id)
                     ->with(['menu', 'variant'])
                     ->get();
        return response()->json($carts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'menu_id'    => 'required|exists:menus,id',
            'quantity'   => 'required|integer|min:1',
            'notes'      => 'nullable|string',
            'variant_id' => 'nullable|exists:menu_variants,id',
        ]);

        $menu = Menu::findOrFail($request->menu_id);

        // Jika menu punya varian, variant_id wajib diisi
        if ($menu->variants()->exists() && empty($request->variant_id)) {
            return response()->json([
                'message' => "Menu \"{$menu->name}\" memiliki varian. Harap pilih varian terlebih dahulu.",
            ], 422);
        }

        // Pastikan variant_id (jika ada) memang milik menu ini
        if ($request->variant_id) {
            $variantExists = MenuVariant::where('id', $request->variant_id)
                                        ->where('menu_id', $menu->id)
                                        ->exists();
            if (!$variantExists) {
                return response()->json([
                    'message' => 'Varian tidak valid untuk menu ini.',
                ], 422);
            }
        }

        // Cek apakah item (menu + variant) sudah ada di cart
        $cart = Cart::where('user_id', $request->user()->id)
                    ->where('menu_id', $request->menu_id)
                    ->where('variant_id', $request->variant_id ?? null)
                    ->first();

        if ($cart) {
            $cart->update([
                'quantity' => $cart->quantity + $request->quantity,
                'notes'    => $request->notes ?? $cart->notes,
            ]);
        } else {
            $cart = Cart::create([
                'user_id'    => $request->user()->id,
                'menu_id'    => $request->menu_id,
                'variant_id' => $request->variant_id ?? null,
                'quantity'   => $request->quantity,
                'notes'      => $request->notes,
            ]);
        }

        return response()->json($cart->load(['menu', 'variant']), 201);
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

<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Category;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Menu::with('category');

            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->has('is_available')) {
                $query->where('is_available', $request->boolean('is_available'));
            }

            return response()->json($query->orderBy('created_at', 'desc')->get());
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil data menu', 
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $menu = Menu::with('category')->where('id', $id)->first();
            
            if (!$menu && !is_numeric($id)) {
                $menu = Menu::with('category')->where('slug', $id)->first();
            }

            if (!$menu) {
                return response()->json(['message' => 'Menu tidak ditemukan'], 404);
            }

            return response()->json(['data' => $menu]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan sistem saat memuat menu', 
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'is_available' => 'boolean'
        ]);

        $data = $request->only(['category_id', 'name', 'price', 'description', 'image', 'is_available']);
        $data['slug'] = \Illuminate\Support\Str::slug($request->name) . '-' . time();

        $menu = Menu::create($data);
        return response()->json($menu, 201);
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'is_available' => 'boolean'
        ]);

        $data = $request->only(['category_id', 'name', 'price', 'description', 'image', 'is_available']);
        
        if ($request->name !== $menu->name) {
            $data['slug'] = \Illuminate\Support\Str::slug($request->name) . '-' . time();
        }

        $menu->update($data);
        return response()->json($menu);
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();
        return response()->json(['message' => 'Menu deleted successfully']);
    }

    public function toggleStatus($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->is_available = !$menu->is_available;
        $menu->save();
        
        return response()->json([
            'is_available' => $menu->is_available,
            'message' => 'Status updated successfully'
        ]);
    }
}

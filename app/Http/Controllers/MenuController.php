<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        try {
            // If filters are applied, skip cache
            if ($request->has('category_id') || $request->has('is_available')) {
                $query = Menu::with(['category', 'variants']);

                if ($request->has('category_id')) {
                    $query->where('category_id', $request->category_id);
                }

                if ($request->has('is_available')) {
                    $query->where('is_available', $request->boolean('is_available'));
                }

                return response()->json($query->orderBy('created_at', 'desc')->get());
            }

            // Cache all menus for 5 minutes
            $menus = Cache::remember('menus_all', 300, function () {
                return Menu::with(['category', 'variants'])->orderBy('created_at', 'desc')->get();
            });

            return response()->json($menus);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil data menu',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function featured()
    {
        try {
            $menus = Menu::with(['category', 'variants'])
                ->where('is_featured', 1)
                ->where('is_available', 1)
                ->whereNull('deleted_at')
                ->get();

            return response()->json($menus);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil menu populer',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $menu = Menu::with(['category', 'variants'])->where('id', $id)->first();

            if (!$menu && !is_numeric($id)) {
                $menu = Menu::with(['category', 'variants'])->where('slug', $id)->first();
            }

            if (!$menu) {
                return response()->json(['message' => 'Menu tidak ditemukan'], 404);
            }

            return response()->json(['data' => $menu]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan sistem saat memuat menu',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id'          => 'required|exists:categories,id',
            'name'                 => 'required|string|max:255',
            'price'                => 'required|numeric|min:0',
            'description'          => 'nullable|string',
            'image'                => 'nullable|string',
            'is_available'         => 'boolean',
            'is_featured'          => 'boolean',
            // variants bersifat opsional
            'variants'             => 'nullable|array',
            'variants.*.name'      => 'required_with:variants|string|max:255',
            'variants.*.price'     => 'required_with:variants|numeric|min:0',
            'variants.*.is_available' => 'boolean',
        ]);

        $hasVariants = $request->filled('variants') && count($request->variants) > 0;

        $data          = $request->only(['category_id', 'name', 'description', 'image', 'is_available', 'is_featured']);
        $data['slug']  = \Illuminate\Support\Str::slug($request->name) . '-' . time();
        // Jika punya varian, price menu = 0 (harga ditentukan per varian)
        $data['price'] = $hasVariants ? 0 : $request->price;

        $menu = Menu::create($data);

        if ($hasVariants) {
            foreach ($request->variants as $variant) {
                $menu->variants()->create([
                    'name'         => $variant['name'],
                    'price'        => $variant['price'],
                    'is_available' => $variant['is_available'] ?? 1,
                ]);
            }
        }

        Cache::forget('menus_all');
        return response()->json($menu->load('variants'), 201);
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $request->validate([
            'category_id'             => 'required|exists:categories,id',
            'name'                    => 'required|string|max:255',
            'price'                   => 'required|numeric|min:0',
            'description'             => 'nullable|string',
            'image'                   => 'nullable|string',
            'is_available'            => 'boolean',
            'is_featured'             => 'boolean',
            // variants bersifat opsional; jika dikirim, akan di-sync
            'variants'                => 'nullable|array',
            'variants.*.name'         => 'required_with:variants|string|max:255',
            'variants.*.price'        => 'required_with:variants|numeric|min:0',
            'variants.*.is_available' => 'boolean',
        ]);

        $hasVariants = $request->filled('variants') && count($request->variants) > 0;

        $data          = $request->only(['category_id', 'name', 'description', 'image', 'is_available', 'is_featured']);
        $data['price'] = $hasVariants ? 0 : $request->price;

        if ($request->name !== $menu->name) {
            $data['slug'] = \Illuminate\Support\Str::slug($request->name) . '-' . time();
        }

        $menu->update($data);

        // Sync variants jika dikirim dalam request
        if ($request->has('variants')) {
            // Hapus semua varian lama, ganti dengan yang baru
            $menu->variants()->delete();

            if ($hasVariants) {
                foreach ($request->variants as $variant) {
                    $menu->variants()->create([
                        'name'         => $variant['name'],
                        'price'        => $variant['price'],
                        'is_available' => $variant['is_available'] ?? 1,
                    ]);
                }
            }
        }

        Cache::forget('menus_all');
        return response()->json($menu->load('variants'));
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();
        Cache::forget('menus_all');
        return response()->json(['message' => 'Menu deleted successfully']);
    }

    public function toggleStatus($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->is_available = !$menu->is_available;
        $menu->save();
        Cache::forget('menus_all');

        return response()->json([
            'is_available' => $menu->is_available,
            'message'      => 'Status updated successfully'
        ]);
    }

    public function byCategory($slug)
    {
        try {
            $menus = Menu::with(['category', 'variants'])
                ->whereHas('category', function ($query) use ($slug) {
                    $query->where('name', $slug)
                          ->orWhere('slug', $slug);
                })
                ->where('is_available', true)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($menus);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memuat menu berdasarkan kategori',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}

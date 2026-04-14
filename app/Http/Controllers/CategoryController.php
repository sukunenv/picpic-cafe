<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::withCount('menus');
        
        if ($request->has('all')) {
            return response()->json($query->get());
        }
        
        $categories = Cache::remember('categories_active', 300, function () use ($query) {
            return $query->where('is_active', true)->get();
        });
        
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        $category = Category::create($validated);
        Cache::forget('categories_active');

        return response()->json($category, 201);
    }

    public function show(Category $category)
    {
        return response()->json($category->loadCount('menus'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'image' => 'nullable|string',
            'is_active' => 'sometimes|boolean'
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);
        Cache::forget('categories_active');

        return response()->json($category);
    }

    public function destroy(Category $category)
    {
        if (Str::contains($category->image, 'storage/categories')) {
            $oldPath = Str::after($category->image, 'storage/');
            Storage::disk('public')->delete($oldPath);
        }
        
        $category->delete();
        Cache::forget('categories_active');
        return response()->json(['message' => 'Category deleted']);
    }
}

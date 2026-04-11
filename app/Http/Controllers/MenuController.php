<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Category;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = Menu::where('is_available', true)->with('category');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        return response()->json($query->get());
    }

    public function show($slug)
    {
        $menu = Menu::where('slug', $slug)->with('category')->firstOrFail();
        return response()->json($menu);
    }

    public function byCategory($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $menus = Menu::where('category_id', $category->id)
                    ->where('is_available', true)
                    ->with('category')
                    ->get();
                    
        return response()->json($menus);
    }

    public function store(Request $request) {}
    public function update(Request $request, Menu $menu) {}
    public function destroy(Menu $menu) {}
}

<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        return response()->json(Banner::orderBy('created_at', 'desc')->get());
    }

    public function publicIndex()
    {
        return response()->json(Banner::where('is_active', true)->orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image_url' => 'required|url',
            'is_active' => 'boolean'
        ]);

        $banner = Banner::create($request->all());
        return response()->json($banner, 201);
    }

    public function show(Banner $banner)
    {
        return response()->json($banner);
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'image_url' => 'required|url',
            'is_active' => 'boolean'
        ]);

        $banner->update($request->all());
        return response()->json($banner);
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->delete();
        return response()->json(['message' => 'Banner deleted successfully']);
    }
}

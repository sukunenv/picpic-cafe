<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Cache::remember('banners_all', 300, function () {
            return Banner::orderBy('created_at', 'desc')->get();
        });
        return response()->json($banners);
    }

    public function publicIndex()
    {
        return response()->json(Banner::where('is_active', true)->orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'tag' => 'nullable|string|max:255',
            'image' => 'nullable|string',
            'gradient_start' => 'nullable|string|max:10',
            'gradient_end' => 'nullable|string|max:10',
            'type' => 'nullable|string|in:image,gradient',
            'is_active' => 'boolean'
        ]);

        $banner = Banner::create($request->all());
        Cache::forget('banners_all');
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
            'subtitle' => 'nullable|string|max:255',
            'tag' => 'nullable|string|max:255',
            'image' => 'nullable|string',
            'gradient_start' => 'nullable|string|max:10',
            'gradient_end' => 'nullable|string|max:10',
            'type' => 'nullable|string|in:image,gradient',
            'is_active' => 'boolean'
        ]);

        $banner->update($request->all());
        Cache::forget('banners_all');
        return response()->json($banner);
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->delete();
        Cache::forget('banners_all');
        return response()->json(['message' => 'Banner deleted successfully']);
    }
}

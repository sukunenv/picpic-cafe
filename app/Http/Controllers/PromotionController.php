<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    public function index()
    {
        return response()->json(Promotion::with('menus')->orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'type'       => 'required|in:percent,fixed',
            'value'      => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'is_active'  => 'boolean',
            'menu_ids'   => 'required|array',
            'menu_ids.*' => 'exists:menus,id'
        ]);

        return DB::transaction(function () use ($request) {
            $promotion = Promotion::create($request->only([
                'name', 'type', 'value', 'start_date', 'end_date', 'is_active'
            ]));

            $promotion->menus()->attach($request->menu_ids);

            return response()->json($promotion->load('menus'), 201);
        });
    }

    public function show($id)
    {
        return response()->json(Promotion::with('menus')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $promotion = Promotion::findOrFail($id);

        $request->validate([
            'name'       => 'required|string|max:255',
            'type'       => 'required|in:percent,fixed',
            'value'      => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'is_active'  => 'boolean',
            'menu_ids'   => 'required|array',
            'menu_ids.*' => 'exists:menus,id'
        ]);

        return DB::transaction(function () use ($request, $promotion) {
            $promotion->update($request->only([
                'name', 'type', 'value', 'start_date', 'end_date', 'is_active'
            ]));

            $promotion->menus()->sync($request->menu_ids);

            return response()->json($promotion->load('menus'));
        });
    }

    public function destroy($id)
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->delete();

        return response()->json(['message' => 'Promotion deleted successfully']);
    }
}

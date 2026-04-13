<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return response()->json(SystemSetting::all());
    }

    public function update(Request $request, $key)
    {
        $request->validate([
            'value' => 'required|string',
        ]);

        $setting = SystemSetting::where('key', $key)->firstOrFail();
        $setting->update(['value' => $request->value]);

        return response()->json($setting);
    }

    public function resetPoints()
    {
        User::query()->update(['points' => 0]);
        return response()->json(['message' => 'Semua poin pelanggan telah direset ke 0.']);
    }

    public function bankInfo()
    {
        $settings = SystemSetting::whereIn('key', ['bank_name', 'bank_account_number', 'bank_account_name'])->get();
        
        $info = [];
        foreach ($settings as $setting) {
            $info[$setting->key] = $setting->value;
        }

        return response()->json($info);
    }

    public function loyaltyInfo()
    {
        $settings = SystemSetting::where('group', 'loyalty')->get();
        
        $info = [];
        foreach ($settings as $setting) {
            $info[$setting->key] = $setting->value;
        }

        return response()->json($info);
    }
}

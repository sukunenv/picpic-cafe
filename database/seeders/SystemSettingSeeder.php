<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Loyalty Base Settings
            ['key' => 'point_value', 'value' => '5000', 'label' => 'Kelipatan Belanja (Rp)', 'group' => 'loyalty'],
            ['key' => 'point_multiplier', 'value' => '10', 'label' => 'Poin per Kelipatan', 'group' => 'loyalty'],
            ['key' => 'point_expiry_months', 'value' => '3', 'label' => 'Masa Berlaku Poin (Bulan)', 'group' => 'loyalty'],
            
            // Tier 1 - Regular
            ['key' => 'tier_regular_name', 'value' => 'Regular', 'label' => 'Nama Tier Regular', 'group' => 'loyalty'],
            ['key' => 'tier_regular_min', 'value' => '0', 'label' => 'Min Poin Regular', 'group' => 'loyalty'],
            ['key' => 'tier_regular_max', 'value' => '500', 'label' => 'Max Poin Regular', 'group' => 'loyalty'],
            ['key' => 'tier_regular_color', 'value' => '#9CA3AF', 'label' => 'Warna Tier Regular', 'group' => 'loyalty'],
            
            // Tier 2 - Silver
            ['key' => 'tier_silver_name', 'value' => 'Silver', 'label' => 'Nama Tier Silver', 'group' => 'loyalty'],
            ['key' => 'tier_silver_min', 'value' => '501', 'label' => 'Min Poin Silver', 'group' => 'loyalty'],
            ['key' => 'tier_silver_max', 'value' => '1500', 'label' => 'Max Poin Silver', 'group' => 'loyalty'],
            ['key' => 'tier_silver_color', 'value' => '#94A3B8', 'label' => 'Warna Tier Silver', 'group' => 'loyalty'],
            
            // Tier 3 - Gold
            ['key' => 'tier_gold_name', 'value' => 'Gold', 'label' => 'Nama Tier Gold', 'group' => 'loyalty'],
            ['key' => 'tier_gold_min', 'value' => '1501', 'label' => 'Min Poin Gold', 'group' => 'loyalty'],
            ['key' => 'tier_gold_max', 'value' => '999999', 'label' => 'Max Poin Gold', 'group' => 'loyalty'],
            ['key' => 'tier_gold_color', 'value' => '#F59E0B', 'label' => 'Warna Tier Gold', 'group' => 'loyalty'],
            
            // Current Bank Settings (preserving if exists)
            ['key' => 'bank_name', 'value' => 'BANK BCA', 'label' => 'Nama Bank', 'group' => 'payment'],
            ['key' => 'bank_account_number', 'value' => '1234567890', 'label' => 'Nomor Rekening', 'group' => 'payment'],
            ['key' => 'bank_account_name', 'value' => 'PICPIC CAFE', 'label' => 'Atas Nama', 'group' => 'payment'],
        ];

        foreach ($settings as $setting) {
            DB::table('system_settings')->updateOrInsert(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'label' => $setting['label'],
                    'group' => $setting['group'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}

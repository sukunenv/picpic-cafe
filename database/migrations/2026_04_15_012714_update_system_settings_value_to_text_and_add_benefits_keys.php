<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change value column to text to support longer benefit strings
        Schema::table('system_settings', function (Blueprint $table) {
            $table->text('value')->change();
        });

        // Add default benefits for each tier in 'loyalty' group
        $benefits = [
            [
                'key' => 'tier_regular_benefits',
                'value' => "5% cashback setiap transaksi\nBirthday special voucher\nEarly access promo",
                'label' => 'Keuntungan Tier Regular/Bronze',
                'group' => 'loyalty',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'tier_silver_benefits',
                'value' => "10% cashback setiap transaksi\nFree upsize 1x/bulan\nBirthday special voucher + free drink\nPriority queue",
                'label' => 'Keuntungan Tier Silver',
                'group' => 'loyalty',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'tier_gold_benefits',
                'value' => "15% cashback setiap transaksi\nFree upsize unlimited\nBirthday special voucher + free meal\nVIP lounge access\nExclusive limited menu access\nPersonal barista request",
                'label' => 'Keuntungan Tier Gold',
                'group' => 'loyalty',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($benefits as $benefit) {
            DB::table('system_settings')->updateOrInsert(
                ['key' => $benefit['key']],
                [
                    'value' => $benefit['value'],
                    'label' => $benefit['label'],
                    'group' => $benefit['group'],
                    'created_at' => $benefit['created_at'],
                    'updated_at' => $benefit['updated_at'],
                ]
            );
        }
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->string('value')->change();
        });

        DB::table('system_settings')->whereIn('key', [
            'tier_regular_benefits',
            'tier_silver_benefits',
            'tier_gold_benefits'
        ])->delete();
    }
};

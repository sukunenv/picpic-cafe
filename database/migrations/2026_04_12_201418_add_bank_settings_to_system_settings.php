<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('system_settings')->insertOrIgnore([
            [
                'key' => 'bank_name',
                'value' => 'BCA',
                'label' => 'Nama Bank',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'bank_account_number',
                'value' => '1234567890',
                'label' => 'Nomor Rekening',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'bank_account_name',
                'value' => 'PICPIC CAFE',
                'label' => 'Atas Nama',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('system_settings')->whereIn('key', ['bank_name', 'bank_account_number', 'bank_account_name'])->delete();
    }
};

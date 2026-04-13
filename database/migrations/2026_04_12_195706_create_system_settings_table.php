<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->string('label');
            $table->timestamps();
        });

        // Seed initial loyalty settings
        DB::table('system_settings')->insert([
            [
                'key' => 'point_value',
                'value' => '5000',
                'label' => 'Kelipatan Belanja (Rp)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'point_multiplier',
                'value' => '10',
                'label' => 'Poin per Kelipatan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};

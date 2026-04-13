<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->string('image_url')->nullable()->change();
            $table->string('color_start')->nullable()->default('#6367FF')->after('image_url');
            $table->string('color_end')->nullable()->default('#8B5CF6')->after('color_start');
            $table->string('subtitle')->nullable()->after('title');
            $table->string('label')->nullable()->after('subtitle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->string('image_url')->nullable(false)->change();
            $table->dropColumn(['color_start', 'color_end', 'subtitle', 'label']);
        });
    }
};

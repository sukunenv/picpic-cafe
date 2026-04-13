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
            $table->renameColumn('label', 'tag');
            $table->renameColumn('image_url', 'image');
            $table->renameColumn('color_start', 'gradient_start');
            $table->renameColumn('color_end', 'gradient_end');
            $table->string('type')->default('image')->after('gradient_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->renameColumn('tag', 'label');
            $table->renameColumn('image', 'image_url');
            $table->renameColumn('gradient_start', 'color_start');
            $table->renameColumn('gradient_end', 'color_end');
            $table->dropColumn('type');
        });
    }
};

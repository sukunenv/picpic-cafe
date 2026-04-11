<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Banner::create([
            'title' => 'Promo Coffee Morning',
            'image_url' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&q=80&w=1200',
            'is_active' => true
        ]);

        \App\Models\Banner::create([
            'title' => 'Menu Baru: Rice Bowl Series',
            'image_url' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=80&w=1200',
            'is_active' => true
        ]);

        \App\Models\Banner::create([
            'title' => 'Weekend Pastry Discount 20%',
            'image_url' => 'https://images.unsplash.com/photo-1550617931-e17a7b70dce2?auto=format&fit=crop&q=80&w=1200',
            'is_active' => true
        ]);

        \App\Models\Banner::create([
            'title' => 'Enjoy our atmosphere',
            'image_url' => 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&q=80&w=1200',
            'is_active' => false
        ]);
    }
}

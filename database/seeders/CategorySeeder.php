<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Coffee', 
                'slug' => 'coffee', 
                'image' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=500',
                'is_active' => true
            ],
            [
                'name' => 'Food', 
                'slug' => 'food', 
                'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500',
                'is_active' => true
            ],
            [
                'name' => 'Pastry', 
                'slug' => 'pastry', 
                'image' => 'https://images.unsplash.com/photo-1550617931-e17a7b70dce2?w=500',
                'is_active' => true
            ],
            [
                'name' => 'Dessert', 
                'slug' => 'dessert', 
                'image' => 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=500',
                'is_active' => true
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}

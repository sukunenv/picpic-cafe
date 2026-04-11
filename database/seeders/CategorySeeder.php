<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Coffee', 'slug' => 'coffee'],
            ['name' => 'Food', 'slug' => 'food'],
            ['name' => 'Pastry', 'slug' => 'pastry'],
            ['name' => 'Dessert', 'slug' => 'dessert'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['slug' => $category['slug']], $category);
        }
    }
}

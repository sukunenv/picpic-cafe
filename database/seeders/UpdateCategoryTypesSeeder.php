<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class UpdateCategoryTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mappings = [
            'Coffee' => 'drink',
            'Food' => 'food',
            'Dessert' => 'food',
            'Non Coffee' => 'drink',
            'Non-Coffee' => 'drink',
            'Minuman' => 'drink',
            'Makanan' => 'food',
        ];

        foreach ($mappings as $name => $type) {
            Category::where('name', $name)->update(['type' => $type]);
        }
        
        echo "Category types updated successfully.\n";
    }
}

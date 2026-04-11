<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Category;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $coffee = Category::where('slug', 'coffee')->first();
        $food = Category::where('slug', 'food')->first();
        $pastry = Category::where('slug', 'pastry')->first();
        $dessert = Category::where('slug', 'dessert')->first();

        // 2 Coffee
        Menu::firstOrCreate(['slug' => 'cappuccino'], [
            'category_id' => $coffee->id,
            'name' => 'Cappuccino',
            'description' => 'Classic Italian espresso with steamed milk foam.',
            'price' => 35000,
            'rating' => 4.8,
            'is_available' => true,
            'is_featured' => true,
        ]);
        Menu::firstOrCreate(['slug' => 'iced-latte'], [
            'category_id' => $coffee->id,
            'name' => 'Iced Latte',
            'description' => 'Smooth espresso mixed with cold milk and ice.',
            'price' => 32000,
            'rating' => 4.5,
            'is_available' => true,
            'is_featured' => false,
        ]);

        // 2 Food
        Menu::firstOrCreate(['slug' => 'nasi-goreng'], [
            'category_id' => $food->id,
            'name' => 'Nasi Goreng',
            'description' => 'Indonesian fried rice with sunny side up egg.',
            'price' => 45000,
            'rating' => 4.9,
            'is_available' => true,
            'is_featured' => true,
        ]);
        Menu::firstOrCreate(['slug' => 'spaghetti-carbonara'], [
            'category_id' => $food->id,
            'name' => 'Spaghetti Carbonara',
            'description' => 'Creamy pasta with smoked beef and mushroom.',
            'price' => 55000,
            'rating' => 4.6,
            'is_available' => true,
            'is_featured' => false,
        ]);

        // 2 Pastry
        Menu::firstOrCreate(['slug' => 'butter-croissant'], [
            'category_id' => $pastry->id,
            'name' => 'Butter Croissant',
            'description' => 'Flaky and buttery French pastry.',
            'price' => 25000,
            'rating' => 4.7,
            'is_available' => true,
            'is_featured' => true,
        ]);
        Menu::firstOrCreate(['slug' => 'pain-au-chocolat'], [
            'category_id' => $pastry->id,
            'name' => 'Pain au Chocolat',
            'description' => 'Sweet pastry filled with dark chocolate chunks.',
            'price' => 28000,
            'rating' => 4.8,
            'is_available' => true,
            'is_featured' => false,
        ]);

        // 2 Dessert
        Menu::firstOrCreate(['slug' => 'tiramisu'], [
            'category_id' => $dessert->id,
            'name' => 'Tiramisu',
            'description' => 'Classic Italian coffee flavored dessert.',
            'price' => 40000,
            'rating' => 4.9,
            'is_available' => true,
            'is_featured' => true,
        ]);
        Menu::firstOrCreate(['slug' => 'cheese-cake'], [
            'category_id' => $dessert->id,
            'name' => 'New York Cheese Cake',
            'description' => 'Rich and creamy baked cheese cake.',
            'price' => 45000,
            'rating' => 4.7,
            'is_available' => true,
            'is_featured' => false,
        ]);
    }
}

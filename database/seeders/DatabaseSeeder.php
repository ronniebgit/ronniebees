<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Topping;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = env('RONNIEBEES_ADMIN_EMAIL', 'admin@ronniebees.com');
        $adminPassword = env('RONNIEBEES_ADMIN_PASSWORD', 'ronniebee123');

        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => "Ronnie Bee's Admin",
                'password' => Hash::make($adminPassword),
                'is_admin' => true,
            ]
        );

        $categories = [
            [
                'name' => 'Build Your Own',
                'slug' => 'byo-pizzas',
                'description' => 'Start with a classic cheese pie and build it with the toppings you love most.',
                'active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Classic Pizzas',
                'slug' => 'classic-pizzas',
                'description' => 'Our favorite everyday favorites with bold, familiar flavor.',
                'active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Signature Pizzas',
                'slug' => 'signature-pizzas',
                'description' => 'Chef-crafted pies built for big flavor and fresh ingredients.',
                'active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Veggie Lovers',
                'slug' => 'veggie-lovers',
                'description' => 'Loaded with greens, herbs, and bright toppings.',
                'active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Available Add-Ons',
                'slug' => 'add-ons',
                'description' => 'Perfect extras to pair with your pie.',
                'active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Drinks',
                'slug' => 'drinks',
                'description' => 'Cold and hot beverages paired with your order.',
                'active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }

        $byo = Category::where('slug', 'byo-pizzas')->first();
        $classic = Category::where('slug', 'classic-pizzas')->first();
        $signature = Category::where('slug', 'signature-pizzas')->first();
        $veggie = Category::where('slug', 'veggie-lovers')->first();
        $addOns = Category::where('slug', 'add-ons')->first();

        $popularToppings = [
            'Pepperoni', 'Mushrooms', 'Green Peppers', 'Onions', 'Sausage', 'Bacon', 'Ham', 'Black Olives',
            'Extra Cheese', 'Jalapeños', 'Pineapple', 'Spinach', 'Roasted Red Peppers', 'Tomatoes', 'Garlic',
            'Banana Peppers', 'Chicken', 'Red Onions', 'Basil', 'Artichokes',
        ];

        foreach ($popularToppings as $index => $toppingName) {
            $slug = strtolower(str_replace([' ', '&', '/'], '-', $toppingName));
            Topping::firstOrCreate(['slug' => $slug], [
                'name' => $toppingName,
                'slug' => $slug,
                'active' => true,
                'sort_order' => $index + 1,
                'small_price' => 1.25,
                'medium_price' => 1.75,
                'large_price' => 2.50,
            ]);
        }

        $products = [
            [
                'category_id' => $byo->id,
                'name' => 'Build Your Own',
                'slug' => 'build-your-own-pizza',
                'description' => 'Start with a classic cheese pie and build it with the toppings you love most.',
                'base_price' => 9.95,
                'small_price' => 9.95,
                'medium_price' => 16.45,
                'large_price' => 21.95,
                'image' => null,
                'featured' => true,
                'active' => true,
            ],
            [
                'category_id' => $classic->id,
                'name' => 'Margherita Pie',
                'slug' => 'margherita-pie',
                'description' => 'Fresh mozzarella, basil, tomato sauce, and a drizzle of olive oil.',
                'base_price' => 9.95,
                'small_price' => 9.95,
                'medium_price' => 21.99,
                'large_price' => 21.99,
                'image' => null,
                'featured' => true,
                'active' => true,
            ],
            [
                'category_id' => $classic->id,
                'name' => 'Pepperoni Feast',
                'slug' => 'pepperoni-feast',
                'description' => 'Classic pepperoni, mozzarella, and a rich tomato base.',
                'base_price' => 9.95,
                'small_price' => 9.95,
                'medium_price' => 24.99,
                'large_price' => 24.99,
                'image' => null,
                'featured' => true,
                'active' => true,
            ],
            [
                'category_id' => $signature->id,
                'name' => 'BBQ Chicken Pizza',
                'slug' => 'bbq-chicken-pizza',
                'description' => 'Grilled chicken, red onion, BBQ sauce, and smoked gouda.',
                'base_price' => 9.95,
                'small_price' => 9.95,
                'medium_price' => 26.99,
                'large_price' => 26.99,
                'image' => null,
                'featured' => true,
                'active' => true,
            ],
            [
                'category_id' => $signature->id,
                'name' => 'Buffalo Ranch',
                'slug' => 'buffalo-ranch',
                'description' => 'Buffalo chicken, ranch drizzle, red onion, and mozzarella.',
                'base_price' => 9.95,
                'small_price' => 9.95,
                'medium_price' => 27.99,
                'large_price' => 27.99,
                'image' => null,
                'featured' => true,
                'active' => true,
            ],
            [
                'category_id' => $veggie->id,
                'name' => 'Garden Glow',
                'slug' => 'garden-glow',
                'description' => 'Roasted peppers, olives, spinach, onion, and mozzarella.',
                'base_price' => 9.95,
                'small_price' => 9.95,
                'medium_price' => 16.45,
                'large_price' => 21.95,
                'image' => null,
                'featured' => false,
                'active' => true,
            ],
            [
                'category_id' => $addOns->id,
                'name' => 'Garlic Knots',
                'slug' => 'garlic-knots',
                'description' => 'Warm buttery knots brushed with garlic and herbs.',
                'base_price' => 6.99,
                'small_price' => 6.99,
                'medium_price' => 6.99,
                'large_price' => 6.99,
                'image' => null,
                'featured' => false,
                'active' => true,
            ],
            [
                'category_id' => $addOns->id,
                'name' => 'House Salad',
                'slug' => 'house-salad',
                'description' => 'Crisp greens, tomatoes, cucumber, and house dressing.',
                'base_price' => 7.99,
                'small_price' => 7.99,
                'medium_price' => 7.99,
                'large_price' => 7.99,
                'image' => null,
                'featured' => false,
                'active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(['slug' => $product['slug']], $product);
        }
    }
}

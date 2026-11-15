<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductVisibilityAndOrderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_visible_products_are_ordered_and_filtered(): void
    {
        $category = Category::create([
            'name' => 'Pizza',
            'slug' => 'pizza',
            'description' => 'featured pizza',
            'active' => true,
            'sort_order' => 1,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Hidden Pizza',
            'slug' => 'hidden-pizza',
            'description' => 'should not show',
            'base_price' => 10,
            'small_price' => 10,
            'medium_price' => 15,
            'large_price' => 20,
            'featured' => false,
            'active' => false,
            'sort_order' => 99,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Classic Pie',
            'slug' => 'classic-pie',
            'description' => 'visible',
            'base_price' => 12,
            'small_price' => 12,
            'medium_price' => 18,
            'large_price' => 24,
            'featured' => true,
            'active' => true,
            'sort_order' => 3,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Appetizer',
            'slug' => 'appetizer',
            'description' => 'visible',
            'base_price' => 8,
            'small_price' => 8,
            'medium_price' => 8,
            'large_price' => 8,
            'featured' => false,
            'active' => true,
            'sort_order' => 2,
        ]);

        $products = Product::visible()->ordered()->pluck('slug')->all();

        $this->assertSame(['appetizer', 'classic-pie'], $products);
    }
}

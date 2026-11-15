<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductManagementWebAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_can_manage_products_from_the_browser(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $category = Category::create([
            'name' => 'Pizza',
            'slug' => 'pizza',
            'active' => true,
            'sort_order' => 1,
        ]);

        $this->actingAs($admin)
            ->get('/admin/products')
            ->assertOk()
            ->assertSee('Products');

        $this->actingAs($admin)
            ->post('/admin/products', [
                'category_id' => $category->id,
                'name' => 'Margherita Pie',
                'slug' => 'margherita-pie',
                'description' => 'Classic cheese pie',
                'base_price' => 19.99,
                'small_price' => 15.99,
                'medium_price' => 19.99,
                'large_price' => 24.99,
                'active' => true,
                'featured' => true,
                'sort_order' => 1,
            ])
            ->assertRedirect('/admin/products');

        $this->assertDatabaseHas('products', [
            'name' => 'Margherita Pie',
            'category_id' => $category->id,
            'active' => 1,
        ]);

        $product = Product::first();

        $this->actingAs($admin)
            ->put('/admin/products/' . $product->id, [
                'name' => 'Updated Margherita Pie',
                'active' => false,
            ])
            ->assertRedirect('/admin/products');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Margherita Pie',
            'active' => 0,
        ]);
    }
}

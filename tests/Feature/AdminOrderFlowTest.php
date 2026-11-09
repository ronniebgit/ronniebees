<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_order_status(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@ronniebees.com',
            'name' => 'Admin',
        ]);

        $order = Order::create([
            'customer_name' => 'Test Customer',
            'customer_phone' => '5551234567',
            'delivery_type' => 'pickup',
            'total' => 34.99,
            'status' => 'pending',
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($admin)
            ->from('/admin')
            ->post(route('admin.orders.status.update', $order), [
                'status' => 'in-progress',
            ]);

        $response->assertRedirect(route('admin.orders.index'));
        $this->assertSame('in-progress', $order->fresh()->status);
    }

    public function test_customer_checkout_records_selected_payment_method(): void
    {
        $category = Category::create([
            'name' => 'Classic Pizzas',
            'slug' => 'classic-pizzas',
            'description' => 'Test category',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Margherita Pie',
            'slug' => 'margherita-pie',
            'description' => 'Fresh pizza',
            'base_price' => 21.99,
            'small_price' => 18.99,
            'medium_price' => 21.99,
            'large_price' => 27.99,
            'featured' => true,
            'active' => true,
        ]);

        session()->put('cart', [
            '1-medium-whole' => [
                'id' => '1-medium-whole',
                'product_id' => $product->id,
                'name' => 'Margherita Pie',
                'size' => 'medium',
                'split_type' => 'whole',
                'quantity' => 1,
                'unit_price' => 21.99,
                'image' => null,
                'special_instructions' => '',
            ],
        ]);

        $response = $this->post(route('checkout.store'), [
            'customer_name' => 'Jane Customer',
            'customer_phone' => '5559990000',
            'customer_email' => 'jane@example.com',
            'delivery_type' => 'pickup',
            'payment_method' => 'card',
            'payment_status' => 'paid',
            'delivery_address' => '',
            'notes' => 'Extra basil',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Jane Customer',
            'payment_method' => 'card',
            'payment_status' => 'paid',
        ]);
    }
}

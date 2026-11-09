<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_lookup_order_by_phone_and_id(): void
    {
        $order = Order::create([
            'customer_name' => 'Track Customer',
            'customer_phone' => '5551239876',
            'customer_email' => 'track@example.com',
            'delivery_type' => 'pickup',
            'payment_method' => 'card',
            'delivery_address' => null,
            'total' => 32.50,
            'status' => 'ready',
            'payment_status' => 'paid',
            'notes' => 'Extra basil',
        ]);

        $response = $this->post('/track', [
            'order_id' => $order->id,
            'phone' => '5551239876',
        ]);

        $response->assertOk();
        $response->assertSee('Track Customer');
        $response->assertSee('Order #'.$order->id);
    }
}

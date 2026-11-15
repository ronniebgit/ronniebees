<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminApiAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_and_access_dashboard_via_api(): void
    {
        $user = User::factory()->create([
            'name' => 'ronbrown',
            'email' => 'ronbrown@example.com',
            'password' => bcrypt('12345678'),
            'is_admin' => true,
        ]);

        $loginResponse = $this->postJson('/api/admin/login', [
            'email' => 'ronbrown@example.com',
            'password' => '12345678',
        ]);

        $loginResponse->assertOk()
            ->assertJsonStructure([
                'message',
                'token',
                'user' => ['id', 'name', 'email'],
            ]);

        $token = $loginResponse->json('token');

        Order::create([
            'customer_name' => 'API Customer',
            'customer_phone' => '5550001234',
            'delivery_type' => 'pickup',
            'total' => 34.50,
            'status' => 'pending',
            'payment_status' => 'paid',
        ]);

        $dashboardResponse = $this->withToken($token)
            ->getJson('/api/admin/dashboard');

        $dashboardResponse->assertOk()
            ->assertJsonPath('user.email', 'ronbrown@example.com')
            ->assertJsonPath('stats.total_orders', 1);
    }
}

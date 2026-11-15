<?php

namespace Tests\Feature;

use App\Models\Topping;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminToolsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_topping_prices_and_sort_order(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@ronniebees.com',
            'password' => bcrypt('secret123'),
            'is_admin' => true,
        ]);

        $topping = Topping::create([
            'name' => 'Pepperoni',
            'slug' => 'pepperoni',
            'active' => true,
            'sort_order' => 1,
            'small_price' => 1.25,
            'medium_price' => 1.75,
            'large_price' => 2.50,
        ]);

        $response = $this->actingAs($admin)
            ->put(route('admin.toppings.update', $topping), [
                'small_price' => 1.50,
                'medium_price' => 2.25,
                'large_price' => 3.00,
                'sort_order' => 4,
                'active' => true,
            ]);

        $response->assertRedirect(route('admin.toppings.index'));

        $topping->refresh();
        $this->assertSame(1.5, round((float) $topping->small_price, 2));
        $this->assertSame(2.25, round((float) $topping->medium_price, 2));
        $this->assertSame(3.0, round((float) $topping->large_price, 2));
        $this->assertSame(4, $topping->sort_order);
    }
}

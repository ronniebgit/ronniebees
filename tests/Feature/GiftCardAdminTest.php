<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GiftCardAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_and_create_gift_cards(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->get('/admin/gift-cards')
            ->assertOk();

        $this->actingAs($admin)
            ->post('/admin/gift-cards', [
                'code' => 'RONNIE25',
                'balance' => '25.00',
                'is_active' => true,
            ])
            ->assertRedirect('/admin/gift-cards');

        $this->assertDatabaseHas('gift_cards', [
            'code' => 'RONNIE25',
            'balance' => 25.00,
            'is_active' => true,
        ]);
    }
}

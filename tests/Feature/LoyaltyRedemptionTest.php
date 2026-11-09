<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltyRedemptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_redeem_loyalty_points_against_a_checkout_total(): void
    {
        $user = User::factory()->create([
            'loyalty_points' => 250,
        ]);

        $discount = $user->redeemPoints(200);

        $this->assertSame(2.00, round($discount, 2));
        $this->assertSame(50, $user->fresh()->loyalty_points);
    }
}

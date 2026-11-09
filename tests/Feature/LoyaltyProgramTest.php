<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltyProgramTest extends TestCase
{
    use RefreshDatabase;

    public function test_loyalty_points_and_redeem_value_are_calculated_correctly(): void
    {
        $this->assertSame(42, User::pointsForOrder(42.50));
        $this->assertSame(2.00, round(User::redeemValueFromPoints(200), 2));
    }
}

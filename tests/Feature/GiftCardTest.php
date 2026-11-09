<?php

namespace Tests\Feature;

use App\Models\GiftCard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GiftCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_gift_card_balance_is_applied_to_the_order_total(): void
    {
        $result = GiftCard::applyBalance(40.00, 25.00);

        $this->assertSame(25.00, round((float) $result['applied'], 2));
        $this->assertSame(15.00, round((float) $result['remaining'], 2));
    }
}

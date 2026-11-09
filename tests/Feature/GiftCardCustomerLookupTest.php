<?php

namespace Tests\Feature;

use App\Models\GiftCard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GiftCardCustomerLookupTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_lookup_a_gift_card_balance(): void
    {
        GiftCard::create([
            'code' => 'RONNIE25',
            'balance' => 25.00,
            'is_active' => true,
        ]);

        $this->get('/gift-card?code=RONNIE25')
            ->assertOk()
            ->assertSee('$25.00');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Topping;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToppingCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_byo_category_and_toppings_are_seeded_with_pricing(): void
    {
        $this->seed();

        $byo = Category::where('slug', 'byo-cheese')->first();
        $this->assertNotNull($byo);

        $buildYourOwn = Product::where('slug', 'build-your-own-cheese')->first();
        $this->assertNotNull($buildYourOwn);
        $this->assertSame('9.95', number_format($buildYourOwn->small_price, 2, '.', ''));
        $this->assertSame('16.45', number_format($buildYourOwn->medium_price, 2, '.', ''));
        $this->assertSame('21.95', number_format($buildYourOwn->large_price, 2, '.', ''));

        $this->assertGreaterThanOrEqual(20, Topping::count());
        $this->assertSame(1.25, Topping::first()->priceForSize('small', 'whole'));
        $this->assertSame(1.75, Topping::first()->priceForSize('medium', 'whole'));
        $this->assertSame(1.00, Topping::first()->priceForSize('medium', 'half'));
    }
}

<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Topping;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontPizzaFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_storefront_pizza_form_includes_required_topping_and_split_fields(): void
    {
        $category = Category::create([
            'name' => 'Build Your Own',
            'slug' => 'build-your-own',
            'description' => 'Classic build your own pies.',
            'active' => true,
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Build Your Own Pizza',
            'slug' => 'build-your-own-pizza',
            'description' => 'Create your perfect pie.',
            'base_price' => 9.95,
            'small_price' => 9.95,
            'medium_price' => 16.45,
            'large_price' => 21.95,
            'featured' => true,
            'active' => true,
        ]);

        Topping::create([
            'name' => 'Pepperoni',
            'slug' => 'pepperoni',
            'active' => true,
            'sort_order' => 1,
            'small_price' => 1.25,
            'medium_price' => 1.75,
            'large_price' => 2.50,
        ]);

        $this->get('/product/' . $product->slug)
            ->assertOk()
            ->assertSee('name="split_type"', false)
            ->assertSee('Whole pie', false)
            ->assertSee('First half', false)
            ->assertSee('Second half', false)
            ->assertSee('topping-split-group', false)
            ->assertSee('name="topping_portions[1][]"', false)
            ->assertSee('id="pizza-subtotal"', false);
    }

    public function test_byo_has_three_size_options_and_specialty_has_two(): void
    {
        $byoCategory = Category::create([
            'name' => 'Build Your Own',
            'slug' => 'byo-pizzas',
            'description' => 'Classic build your own pies.',
            'active' => true,
            'sort_order' => 1,
        ]);

        $byoProduct = Product::create([
            'category_id' => $byoCategory->id,
            'name' => 'Build Your Own Pizza',
            'slug' => 'build-your-own-pizza',
            'description' => 'Create your perfect pie.',
            'base_price' => 9.95,
            'small_price' => 9.95,
            'medium_price' => 16.45,
            'large_price' => 21.95,
            'featured' => true,
            'active' => true,
        ]);

        $specialtyCategory = Category::create([
            'name' => 'Signature Pizzas',
            'slug' => 'signature-pizzas',
            'description' => 'Chef-crafted pies.',
            'active' => true,
            'sort_order' => 2,
        ]);

        $specialtyProduct = Product::create([
            'category_id' => $specialtyCategory->id,
            'name' => 'Pepperoni Feast',
            'slug' => 'pepperoni-feast',
            'description' => 'Pepperoni and cheese.',
            'base_price' => 9.95,
            'small_price' => 9.95,
            'medium_price' => 24.99,
            'large_price' => 24.99,
            'featured' => true,
            'active' => true,
        ]);

        $this->get('/product/' . $byoProduct->slug)
            ->assertOk()
            ->assertSee('value="small"', false)
            ->assertSee('value="medium"', false)
            ->assertSee('value="large"', false);

        $this->get('/product/' . $specialtyProduct->slug)
            ->assertOk()
            ->assertDontSee('value="small"', false)
            ->assertSee('value="medium"', false)
            ->assertSee('value="large"', false);
    }

    public function test_menu_page_shows_only_drinks(): void
    {
        $pizzaCategory = Category::create([
            'name' => 'Pizza',
            'slug' => 'pizza',
            'description' => 'Pizza menu.',
            'active' => true,
            'sort_order' => 1,
        ]);

        $drinksCategory = Category::create([
            'name' => 'Drinks',
            'slug' => 'drinks',
            'description' => 'Cold and hot beverages.',
            'active' => true,
            'sort_order' => 2,
        ]);

        Product::create([
            'category_id' => $pizzaCategory->id,
            'name' => 'Build Your Own Pizza',
            'slug' => 'build-your-own-pizza',
            'description' => 'Build your own.',
            'base_price' => 9.95,
            'small_price' => 9.95,
            'medium_price' => 16.45,
            'large_price' => 21.95,
            'featured' => true,
            'active' => true,
        ]);

        Product::create([
            'category_id' => $drinksCategory->id,
            'name' => 'Coke',
            'slug' => 'coke',
            'description' => 'Classic soda.',
            'base_price' => 1.50,
            'small_price' => 1.50,
            'medium_price' => 1.50,
            'large_price' => 1.50,
            'featured' => false,
            'active' => true,
        ]);

        $this->get('/menu')
            ->assertOk()
            ->assertSee('Drinks', false)
            ->assertSee('Coke', false)
            ->assertDontSee('Build Your Own Pizza', false);
    }

    public function test_drinks_use_single_price_and_admin_can_filter_by_category(): void
    {
        $pizzaCategory = Category::create([
            'name' => 'Pizza',
            'slug' => 'pizza',
            'description' => 'Pizza menu.',
            'active' => true,
            'sort_order' => 1,
        ]);

        $drinksCategory = Category::create([
            'name' => 'Drinks',
            'slug' => 'drinks',
            'description' => 'Cold and hot beverages.',
            'active' => true,
            'sort_order' => 2,
        ]);

        $pizza = Product::create([
            'category_id' => $pizzaCategory->id,
            'name' => 'Build Your Own Pizza',
            'slug' => 'build-your-own-pizza',
            'description' => 'Build your own.',
            'base_price' => 9.95,
            'small_price' => 9.95,
            'medium_price' => 16.45,
            'large_price' => 21.95,
            'featured' => true,
            'active' => true,
        ]);

        $drink = Product::create([
            'category_id' => $drinksCategory->id,
            'name' => 'Coke',
            'slug' => 'coke',
            'description' => 'Classic soda.',
            'base_price' => 1.50,
            'small_price' => 5.00,
            'medium_price' => 7.50,
            'large_price' => 10.00,
            'featured' => false,
            'active' => true,
        ]);

        $this->assertSame(1.5, $drink->priceForSize('medium'));

        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin/products?category=' . $drinksCategory->id)
            ->assertOk()
            ->assertSee('Coke')
            ->assertDontSee($pizza->name);
    }

    public function test_drink_product_form_hides_pizza_options(): void
    {
        $category = Category::create([
            'name' => 'Drinks',
            'slug' => 'drinks',
            'description' => 'Cold and hot beverages.',
            'active' => true,
            'sort_order' => 2,
        ]);

        $drink = Product::create([
            'category_id' => $category->id,
            'name' => 'Coke',
            'slug' => 'coke',
            'description' => 'Classic soda.',
            'base_price' => 1.50,
            'small_price' => 1.50,
            'medium_price' => 1.50,
            'large_price' => 1.50,
            'featured' => false,
            'active' => true,
        ]);

        $this->get('/product/' . $drink->slug)
            ->assertOk()
            ->assertSee('Price', false)
            ->assertDontSee('Choose pie split', false)
            ->assertDontSee('Choose size', false)
            ->assertDontSee('Extra toppings', false);
    }
}

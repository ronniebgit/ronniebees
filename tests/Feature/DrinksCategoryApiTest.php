<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DrinksCategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_update_drinks_category_via_api(): void
    {
        $user = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('secret123'),
            'is_admin' => true,
        ]);

        $token = $user->createToken('admin-api-token')->plainTextToken;

        $createResponse = $this->withToken($token)
            ->postJson('/api/categories', [
                'name' => 'Drinks',
                'description' => 'Cold and hot beverages.',
            ]);

        $createResponse->assertCreated()
            ->assertJsonPath('name', 'Drinks')
            ->assertJsonPath('slug', 'drinks');

        $category = Category::where('slug', 'drinks')->firstOrFail();

        $updateResponse = $this->withToken($token)
            ->putJson('/api/categories/' . $category->id, [
                'name' => 'Cold Drinks',
                'description' => 'Refreshments for every order.',
            ]);

        $updateResponse->assertOk()
            ->assertJsonPath('name', 'Cold Drinks')
            ->assertJsonPath('slug', 'drinks');
    }
}

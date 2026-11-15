<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryVisibilityAndOrderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_visible_categories_are_ordered_and_filtered(): void
    {
        Category::create([
            'name' => 'Drinks',
            'slug' => 'drinks',
            'description' => 'cold drinks',
            'active' => true,
            'sort_order' => 10,
        ]);

        Category::create([
            'name' => 'Catering',
            'slug' => 'catering',
            'description' => 'party trays',
            'active' => false,
            'sort_order' => 1,
        ]);

        Category::create([
            'name' => 'Appetizers',
            'slug' => 'appetizers',
            'description' => 'shareables',
            'active' => true,
            'sort_order' => 1,
        ]);

        $categories = Category::visible()->ordered()->pluck('slug')->all();

        $this->assertSame(['appetizers', 'drinks'], $categories);
    }
}

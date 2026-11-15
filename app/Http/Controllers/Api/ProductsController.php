<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductsController extends Controller
{
    public function index()
    {
        return response()->json(Product::with('category')->ordered()->get());
    }

    public function show(Product $product)
    {
        return response()->json($product->load('category'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'description' => ['nullable', 'string'],
            'base_price' => ['nullable', 'numeric', 'min:0'],
            'small_price' => ['nullable', 'numeric', 'min:0'],
            'medium_price' => ['nullable', 'numeric', 'min:0'],
            'large_price' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'string', 'max:255'],
            'featured' => ['sometimes', 'boolean'],
            'active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $product = Product::create([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'slug' => $data['slug'] ?? Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'base_price' => (float) ($data['base_price'] ?? 0),
            'small_price' => (float) ($data['small_price'] ?? 0),
            'medium_price' => (float) ($data['medium_price'] ?? 0),
            'large_price' => (float) ($data['large_price'] ?? 0),
            'image' => $data['image'] ?? null,
            'featured' => (bool) ($data['featured'] ?? false),
            'active' => (bool) ($data['active'] ?? true),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);

        return response()->json($product->load('category'), 201);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id' => ['sometimes', 'exists:categories,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', 'unique:products,slug,' . $product->id],
            'description' => ['nullable', 'string'],
            'base_price' => ['sometimes', 'numeric', 'min:0'],
            'small_price' => ['sometimes', 'numeric', 'min:0'],
            'medium_price' => ['sometimes', 'numeric', 'min:0'],
            'large_price' => ['sometimes', 'numeric', 'min:0'],
            'image' => ['nullable', 'string', 'max:255'],
            'featured' => ['sometimes', 'boolean'],
            'active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if (isset($data['name']) && ! isset($data['slug'])) {
            $data['slug'] = $product->slug;
        }

        foreach (['featured', 'active', 'base_price', 'small_price', 'medium_price', 'large_price', 'sort_order'] as $field) {
            if (array_key_exists($field, $data)) {
                $data[$field] = $field === 'featured' || $field === 'active'
                    ? (bool) $data[$field]
                    : ($field === 'sort_order' ? (int) $data[$field] : (float) $data[$field]);
            }
        }

        $product->update($data);

        return response()->json($product->fresh()->load('category'));
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully.']);
    }
}

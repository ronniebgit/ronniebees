<?php

namespace App\Http\Controllers;

use App\Models\Topping;
use Illuminate\Http\Request;

class ToppingsController extends Controller
{
    public function index()
    {
        return response()->json(Topping::active()->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
            'small_price' => ['nullable', 'numeric', 'min:0'],
            'medium_price' => ['nullable', 'numeric', 'min:0'],
            'large_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $topping = Topping::create([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? strtolower(str_replace(' ', '-', $data['name'])),
            'active' => $data['active'] ?? true,
            'sort_order' => $data['sort_order'] ?? Topping::count() + 1,
            'small_price' => $data['small_price'] ?? 1.25,
            'medium_price' => $data['medium_price'] ?? 1.75,
            'large_price' => $data['large_price'] ?? 2.50,
        ]);

        return response()->json($topping, 201);
    }

    public function update(Request $request, Topping $topping)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255'],
            'active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer'],
            'small_price' => ['sometimes', 'numeric', 'min:0'],
            'medium_price' => ['sometimes', 'numeric', 'min:0'],
            'large_price' => ['sometimes', 'numeric', 'min:0'],
        ]);

        $topping->update($data);

        return response()->json($topping);
    }

    public function destroy(Topping $topping)
    {
        $topping->delete();

        return response()->json(['message' => 'Topping deleted successfully.']);
    }
}

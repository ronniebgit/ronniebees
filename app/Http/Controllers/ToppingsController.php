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
        ]);

        $topping = Topping::create([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? strtolower(str_replace(' ', '-', $data['name'])),
            'active' => $data['active'] ?? true,
            'sort_order' => $data['sort_order'] ?? Topping::count() + 1,
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

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Topping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        $cart = Session::get('cart', []);
        $items = collect($cart)->map(function ($item) {
            $product = Product::find($item['product_id']);

            return [
                'id' => $item['id'],
                'product_id' => $item['product_id'],
                'name' => $item['name'],
                'size' => $item['size'],
                'split_type' => $item['split_type'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'image' => $item['image'] ?? null,
                'special_instructions' => $item['special_instructions'] ?? '',
                'toppings' => $item['toppings'] ?? [],
                'line_total' => $item['quantity'] * $item['unit_price'],
                'product' => $product,
            ];
        })->values();

        $subtotal = $items->sum('line_total');

        return view('storefront.cart', compact('items', 'subtotal'));
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'size' => ['required', 'in:small,medium,large'],
            'split_type' => ['required', 'in:whole,first-half,second-half,half-and-half'],
            'quantity' => ['required', 'integer', 'min:1'],
            'special_instructions' => ['nullable', 'string'],
            'toppings' => ['nullable', 'array'],
            'toppings.*' => ['required_with:toppings', 'integer', 'exists:toppings,id'],
            'topping_portions' => ['nullable', 'array'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $selectedToppings = $this->normalizeToppings($request, $data['size']);
        $basePrice = $product->priceForSize($data['size']);
        $toppingCost = collect($selectedToppings)->sum(fn ($topping) => Topping::find($topping['id'])->priceForSize($data['size'], $topping['portion']));
        $unitPrice = (float) $basePrice + (float) $toppingCost;

        $key = $product->id . '-' . $data['size'] . '-' . $data['split_type'];
        $cart = Session::get('cart', []);

        $item = [
            'id' => $key,
            'product_id' => $product->id,
            'name' => $product->name,
            'size' => $data['size'],
            'split_type' => $data['split_type'],
            'quantity' => (int) $data['quantity'],
            'unit_price' => $unitPrice,
            'image' => $product->image,
            'special_instructions' => $data['special_instructions'] ?? '',
            'toppings' => $selectedToppings,
        ];

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += (int) $data['quantity'];
            $cart[$key]['unit_price'] = $unitPrice;
            $cart[$key]['toppings'] = $selectedToppings;
        } else {
            $cart[$key] = $item;
        }

        Session::put('cart', $cart);

        return redirect()->route('cart')->with('success', $product->name . ' was added to your cart.');
    }

    public function update(Request $request, string $id)
    {
        $cart = Session::get('cart', []);

        if (! isset($cart[$id])) {
            return back()->with('error', 'That item is no longer in your cart.');
        }

        $data = $request->validate([
            'size' => ['required', 'in:small,medium,large'],
            'split_type' => ['required', 'in:whole,first-half,second-half,half-and-half'],
            'quantity' => ['required', 'integer', 'min:1'],
            'special_instructions' => ['nullable', 'string'],
            'toppings' => ['nullable', 'array'],
            'toppings.*' => ['required_with:toppings', 'integer', 'exists:toppings,id'],
            'topping_portions' => ['nullable', 'array'],
        ]);

        $product = Product::find($cart[$id]['product_id']);

        if (! $product) {
            unset($cart[$id]);
            Session::put('cart', $cart);

            return back()->with('error', 'The selected product could not be updated.');
        }

        $selectedToppings = $this->normalizeToppings($request, $data['size']);
        $newKey = $product->id . '-' . $data['size'] . '-' . $data['split_type'];

        $cart[$id]['size'] = $data['size'];
        $cart[$id]['split_type'] = $data['split_type'];
        $cart[$id]['quantity'] = (int) $data['quantity'];
        $cart[$id]['special_instructions'] = $data['special_instructions'] ?? '';
        $cart[$id]['toppings'] = $selectedToppings;

        $basePrice = $product->priceForSize($data['size']);
        $toppingCost = collect($selectedToppings)->sum(fn ($topping) => Topping::find($topping['id'])->priceForSize($data['size'], $topping['portion']));
        $cart[$id]['unit_price'] = (float) $basePrice + (float) $toppingCost;

        if ($newKey !== $id) {
            $cart[$newKey] = $cart[$id];
            unset($cart[$id]);
        }

        Session::put('cart', $cart);

        return redirect()->route('cart')->with('success', 'Your item was updated.');
    }

    protected function normalizeToppings(Request $request, string $size): array
    {
        $selected = $request->input('toppings', []);
        $portions = $request->input('topping_portions', []);

        if (empty($selected)) {
            return [];
        }

        $normalized = [];

        foreach ($selected as $toppingId) {
            $portion = $portions[$toppingId] ?? 'whole';

            if ($size === 'small' && $portion === 'half') {
                continue;
            }

            $topping = Topping::find($toppingId);

            if (! $topping) {
                continue;
            }

            $normalized[] = [
                'id' => $topping->id,
                'name' => $topping->name,
                'portion' => $portion,
            ];
        }

        return $normalized;
    }

    public function remove(string $id)
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            Session::put('cart', $cart);
        }

        return back()->with('success', 'Item removed from cart.');
    }
}

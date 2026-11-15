<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Topping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

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
                'cut_options' => $item['cut_options'] ?? [],
                'line_total' => $item['quantity'] * $item['unit_price'],
                'product' => $product,
            ];
        })->values();

        $subtotal = $items->sum('line_total');

        return view('storefront.cart', compact('items', 'subtotal'));
    }

    public function add(Request $request)
    {
        $product = Product::find($request->input('product_id'));
        $isDrinkProduct = strtolower((string) ($product?->category?->slug ?? '')) === 'drinks';

        $rules = [
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'special_instructions' => ['nullable', 'string'],
            'toppings' => ['nullable', 'array'],
            'toppings.*' => ['required_with:toppings', 'integer', 'exists:toppings,id'],
            'topping_portions' => ['nullable', 'array'],
            'topping_portions.*' => ['nullable', 'array'],
            'topping_portions.*.*' => ['string', 'in:whole,first-half,second-half,half-and-half'],
            'cut_options' => ['nullable', 'array'],
            'cut_options.*' => ['required_with:cut_options', 'string', 'in:Well Done,XWell Done,No Cut'],
        ];

        if (! $isDrinkProduct) {
            $rules['size'] = ['required', Rule::in($this->allowedSizesForProduct($product))];
            $rules['split_type'] = ['required', 'in:whole,first-half,second-half,half-and-half'];
        }

        $data = $request->validate($rules);

        $product = Product::findOrFail($data['product_id']);
        $size = $isDrinkProduct ? 'single' : $data['size'];
        $splitType = $isDrinkProduct ? 'whole' : $data['split_type'];
        $selectedToppings = $isDrinkProduct ? [] : $this->normalizeToppings($request, $size);
        $cutOptions = $this->normalizeCutOptions($request);
        $basePrice = $product->priceForSize($size);
        $toppingCost = collect($selectedToppings)->sum(fn ($topping) => Topping::find($topping['id'])->priceForSize($size, $topping['portion']));
        $unitPrice = (float) $basePrice + (float) $toppingCost;

        $key = $product->id . '-' . $size . '-' . $splitType;
        $cart = Session::get('cart', []);

        $item = [
            'id' => $key,
            'product_id' => $product->id,
            'name' => $product->name,
            'size' => $size,
            'split_type' => $splitType,
            'quantity' => (int) $data['quantity'],
            'unit_price' => $unitPrice,
            'image' => $product->image,
            'special_instructions' => $data['special_instructions'] ?? '',
            'toppings' => $selectedToppings,
            'cut_options' => $cutOptions,
        ];

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += (int) $data['quantity'];
            $cart[$key]['unit_price'] = $unitPrice;
            $cart[$key]['toppings'] = $selectedToppings;
            $cart[$key]['cut_options'] = $cutOptions;
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

        $product = Product::find($cart[$id]['product_id']);
        $isDrinkProduct = strtolower((string) ($product?->category?->slug ?? '')) === 'drinks';

        $rules = [
            'quantity' => ['required', 'integer', 'min:1'],
            'special_instructions' => ['nullable', 'string'],
            'toppings' => ['nullable', 'array'],
            'toppings.*' => ['required_with:toppings', 'integer', 'exists:toppings,id'],
            'topping_portions' => ['nullable', 'array'],
            'topping_portions.*' => ['nullable', 'array'],
            'topping_portions.*.*' => ['string', 'in:whole,first-half,second-half,half-and-half'],
            'cut_options' => ['nullable', 'array'],
            'cut_options.*' => ['required_with:cut_options', 'string', 'in:Well Done,XWell Done,No Cut'],
        ];

        if (! $isDrinkProduct) {
            $rules['size'] = ['required', Rule::in($this->allowedSizesForProduct($product))];
            $rules['split_type'] = ['required', 'in:whole,first-half,second-half,half-and-half'];
        }

        $data = $request->validate($rules);

        $product = Product::find($cart[$id]['product_id']);

        if (! $product) {
            unset($cart[$id]);
            Session::put('cart', $cart);

            return back()->with('error', 'The selected product could not be updated.');
        }

        $size = $isDrinkProduct ? 'single' : $data['size'];
        $splitType = $isDrinkProduct ? 'whole' : $data['split_type'];
        $selectedToppings = $isDrinkProduct ? [] : $this->normalizeToppings($request, $size);
        $cutOptions = $this->normalizeCutOptions($request);
        $newKey = $product->id . '-' . $size . '-' . $splitType;

        $cart[$id]['size'] = $size;
        $cart[$id]['split_type'] = $splitType;
        $cart[$id]['quantity'] = (int) $data['quantity'];
        $cart[$id]['special_instructions'] = $data['special_instructions'] ?? '';
        $cart[$id]['toppings'] = $selectedToppings;
        $cart[$id]['cut_options'] = $cutOptions;

        $basePrice = $product->priceForSize($size);
        $toppingCost = collect($selectedToppings)->sum(fn ($topping) => Topping::find($topping['id'])->priceForSize($size, $topping['portion']));
        $cart[$id]['unit_price'] = (float) $basePrice + (float) $toppingCost;

        if ($newKey !== $id) {
            $cart[$newKey] = $cart[$id];
            unset($cart[$id]);
        }

        Session::put('cart', $cart);

        return redirect()->route('cart')->with('success', 'Your item was updated.');
    }

    protected function allowedSizesForProduct(?Product $product): array
    {
        if (! $product) {
            return ['small', 'medium', 'large'];
        }

        $categorySlug = strtolower((string) ($product->category?->slug ?? ''));
        if ($categorySlug === 'drinks') {
            return ['single'];
        }

        $slug = strtolower(($product->category?->slug ?? '') . ' ' . ($product->slug ?? ''));

        if (str_contains($slug, 'byo') || str_contains($slug, 'build-your-own')) {
            return ['small', 'medium', 'large'];
        }

        return ['medium', 'large'];
    }

    protected function normalizeToppings(Request $request, string $size): array
    {
        $portionsByTopping = $request->input('topping_portions', []);

        if ($size === 'single') {
            return [];
        }

        if (empty($portionsByTopping)) {
            return [];
        }

        $normalized = [];

        foreach ($portionsByTopping as $toppingId => $selectedPortions) {
            $selectedPortions = (array) $selectedPortions;

            foreach ($selectedPortions as $portion) {
                $portion = strtolower((string) $portion);

                if ($portion === '') {
                    continue;
                }

                if ($size === 'small' && in_array($portion, ['half', 'first-half', 'second-half'], true)) {
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
        }

        return $normalized;
    }

    protected function normalizeCutOptions(Request $request): array
    {
        $selected = $request->input('cut_options', []);

        if (empty($selected)) {
            return [];
        }

        $allowed = ['Well Done', 'XWell Done', 'No Cut'];

        return array_values(array_unique(array_filter($selected, function ($option) use ($allowed) {
            return in_array($option, $allowed, true);
        })));
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

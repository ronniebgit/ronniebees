<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\GiftCard;
use App\Models\Order;
use App\Models\Product;
use App\Models\Topping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function login()
    {
        return view('admin.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function dashboard()
    {
        $orders = Order::with('items')->latest()->take(20)->get();

        $stats = [
            'revenue' => Order::sum('total'),
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'pending' => Order::where('status', 'pending')->count(),
            'completed' => Order::where('status', 'confirmed')->count(),
        ];

        return view('admin.dashboard', compact('orders', 'stats'));
    }

    public function ordersIndex()
    {
        $orders = Order::with('items')->latest()->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function showOrder(Order $order)
    {
        $order->load('items');

        return view('admin.orders.show', compact('order'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,in-progress,confirmed,ready,completed,cancelled'],
        ]);

        $order->update([
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.orders.index')->with('success', 'Order status updated.');
    }

    public function categoriesIndex()
    {
        $categories = Category::orderBy('sort_order')->orderBy('name')->get();
        $editingCategory = request()->has('edit') ? Category::find(request('edit')) : null;

        return view('admin.categories.index', compact('categories', 'editingCategory'));
    }

    public function categoryStore(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('categories', 'slug')],
            'description' => ['nullable', 'string'],
            'active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        Category::create([
            'name' => trim($validated['name']),
            'slug' => $validated['slug'] ?? Str::slug(trim($validated['name'])),
            'description' => $validated['description'] ?? null,
            'active' => (bool) ($validated['active'] ?? true),
            'sort_order' => (int) ($validated['sort_order'] ?? Category::count() + 1),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function categoryUpdate(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', Rule::unique('categories', 'slug')->ignore($category->id)],
            'description' => ['nullable', 'string'],
            'active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $payload = [
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'active' => $category->active,
            'sort_order' => $category->sort_order,
        ];

        if (isset($validated['name'])) {
            $payload['name'] = trim($validated['name']);
        }

        if (isset($validated['slug'])) {
            $payload['slug'] = Str::slug(trim($validated['slug']));
        }

        if (array_key_exists('description', $validated)) {
            $payload['description'] = $validated['description'];
        }

        if (array_key_exists('active', $validated)) {
            $payload['active'] = (bool) $validated['active'];
        }

        if (array_key_exists('sort_order', $validated) && $validated['sort_order'] !== null) {
            $payload['sort_order'] = (int) $validated['sort_order'];
        }

        if (isset($validated['name']) && ! isset($validated['slug'])) {
            $payload['slug'] = Str::slug(trim($validated['name']));
        }

        $category->update($payload);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function categoryDestroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category removed.');
    }

    public function productsIndex()
    {
        $selectedCategoryId = request()->has('category') ? (int) request('category') : null;

        $products = Product::with('category')
            ->when($selectedCategoryId, fn ($query) => $query->where('category_id', $selectedCategoryId))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        $categories = Category::orderBy('sort_order')->orderBy('name')->get();
        $editingProduct = request()->has('edit') ? Product::find(request('edit')) : null;

        return view('admin.products.index', compact('products', 'categories', 'editingProduct', 'selectedCategoryId'));
    }

    public function productStore(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')],
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

        $category = Category::find($validated['category_id']);
        $basePrice = (float) ($validated['base_price'] ?? 0);
        $smallPrice = (float) ($validated['small_price'] ?? $basePrice);
        $mediumPrice = (float) ($validated['medium_price'] ?? $basePrice);
        $largePrice = (float) ($validated['large_price'] ?? $basePrice);

        if ($category && strtolower((string) $category->slug) === 'drinks') {
            $smallPrice = $basePrice;
            $mediumPrice = $basePrice;
            $largePrice = $basePrice;
        }

        Product::create([
            'category_id' => $validated['category_id'],
            'name' => trim($validated['name']),
            'slug' => $validated['slug'] ?? Str::slug(trim($validated['name'])),
            'description' => $validated['description'] ?? null,
            'base_price' => $basePrice,
            'small_price' => $smallPrice,
            'medium_price' => $mediumPrice,
            'large_price' => $largePrice,
            'image' => $validated['image'] ?? null,
            'featured' => (bool) ($validated['featured'] ?? false),
            'active' => (bool) ($validated['active'] ?? true),
            'sort_order' => (int) ($validated['sort_order'] ?? Product::count() + 1),
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function productUpdate(Request $request, $productIdentifier)
    {
        $product = Product::whereKey($productIdentifier)
            ->orWhere('slug', $productIdentifier)
            ->firstOrFail();

        $validated = $request->validate([
            'category_id' => ['sometimes', 'exists:categories,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($product->id)],
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

        $payload = $product->toArray();

        if (isset($validated['name'])) {
            $payload['name'] = trim($validated['name']);
        }

        if (isset($validated['slug'])) {
            $payload['slug'] = Str::slug(trim($validated['slug']));
        }

        $categoryId = $validated['category_id'] ?? $product->category_id;
        $category = Category::find($categoryId);

        if (isset($validated['category_id'])) {
            $payload['category_id'] = $validated['category_id'];
        }

        $basePrice = (float) ($validated['base_price'] ?? $product->base_price ?? 0);
        $smallPrice = (float) ($validated['small_price'] ?? $product->small_price ?? $basePrice);
        $mediumPrice = (float) ($validated['medium_price'] ?? $product->medium_price ?? $basePrice);
        $largePrice = (float) ($validated['large_price'] ?? $product->large_price ?? $basePrice);

        if ($category && strtolower((string) $category->slug) === 'drinks') {
            $smallPrice = $basePrice;
            $mediumPrice = $basePrice;
            $largePrice = $basePrice;
        }

        foreach (['description', 'image'] as $field) {
            if (array_key_exists($field, $validated)) {
                $payload[$field] = $validated[$field] ?? null;
            }
        }

        $payload['base_price'] = $basePrice;
        $payload['small_price'] = $smallPrice;
        $payload['medium_price'] = $mediumPrice;
        $payload['large_price'] = $largePrice;

        foreach (['featured', 'active'] as $field) {
            if (array_key_exists($field, $validated)) {
                $payload[$field] = (bool) $validated[$field];
            }
        }

        if (array_key_exists('sort_order', $validated) && $validated['sort_order'] !== null) {
            $payload['sort_order'] = (int) $validated['sort_order'];
        }

        if (isset($validated['name']) && ! isset($validated['slug'])) {
            $payload['slug'] = Str::slug(trim($validated['name']));
        }

        $product->update($payload);

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function productDestroy($productIdentifier)
    {
        $product = Product::whereKey($productIdentifier)
            ->orWhere('slug', $productIdentifier)
            ->firstOrFail();

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product removed.');
    }

    public function toppingsIndex()
    {
        $toppings = Topping::orderBy('sort_order')->orderBy('name')->get();
        $editingTopping = request()->has('edit') ? Topping::find(request('edit')) : null;

        return view('admin.toppings.index', compact('toppings', 'editingTopping'));
    }

    public function toppingStore(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('toppings', 'slug')],
            'active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'small_price' => ['required', 'numeric', 'min:0'],
            'medium_price' => ['required', 'numeric', 'min:0'],
            'large_price' => ['required', 'numeric', 'min:0'],
        ]);

        Topping::create([
            'name' => trim($validated['name']),
            'slug' => strtolower(str_replace(' ', '-', trim($validated['slug']))),
            'active' => (bool) ($validated['active'] ?? true),
            'sort_order' => (int) ($validated['sort_order'] ?? Topping::count() + 1),
            'small_price' => (float) $validated['small_price'],
            'medium_price' => (float) $validated['medium_price'],
            'large_price' => (float) $validated['large_price'],
        ]);

        return redirect()->route('admin.toppings.index')->with('success', 'Topping created.');
    }

    public function toppingUpdate(Request $request, Topping $topping)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', Rule::unique('toppings', 'slug')->ignore($topping->id)],
            'active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'small_price' => ['sometimes', 'numeric', 'min:0'],
            'medium_price' => ['sometimes', 'numeric', 'min:0'],
            'large_price' => ['sometimes', 'numeric', 'min:0'],
        ]);

        $payload = [
            'name' => $topping->name,
            'slug' => $topping->slug,
            'active' => $topping->active,
            'sort_order' => $topping->sort_order,
            'small_price' => $topping->small_price,
            'medium_price' => $topping->medium_price,
            'large_price' => $topping->large_price,
        ];

        if (isset($validated['name'])) {
            $payload['name'] = trim($validated['name']);
        }

        if (isset($validated['slug'])) {
            $payload['slug'] = strtolower(str_replace(' ', '-', trim($validated['slug'])));
        }

        if (array_key_exists('active', $validated)) {
            $payload['active'] = (bool) $validated['active'];
        }

        if (array_key_exists('sort_order', $validated) && $validated['sort_order'] !== null) {
            $payload['sort_order'] = (int) $validated['sort_order'];
        }

        foreach (['small_price', 'medium_price', 'large_price'] as $field) {
            if (array_key_exists($field, $validated)) {
                $payload[$field] = (float) $validated[$field];
            }
        }

        $topping->update($payload);

        return redirect()->route('admin.toppings.index')->with('success', 'Topping updated.');
    }

    public function toppingDestroy(Topping $topping)
    {
        $topping->delete();

        return redirect()->route('admin.toppings.index')->with('success', 'Topping removed.');
    }

    public function reorderOrderItems(Request $request, Order $order)
    {
        $validated = $request->validate([
            'order_items' => ['required', 'array'],
            'order_items.*' => ['required', 'integer', 'min:1'],
        ]);

        foreach ($validated['order_items'] as $itemId => $position) {
            $order->items()->whereKey($itemId)->update(['sort_order' => (int) $position]);
        }

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order item order updated.');
    }

    public function giftCardsIndex()
    {
        $cards = GiftCard::latest()->get();
        $editingCard = request()->has('edit') ? GiftCard::find(request('edit')) : null;

        return view('admin.gift-cards.index', compact('cards', 'editingCard'));
    }

    public function giftCardStore(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:gift_cards,code'],
            'balance' => ['required', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        GiftCard::create([
            'code' => strtoupper(trim($validated['code'])),
            'balance' => (float) $validated['balance'],
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        return redirect()->route('admin.gift-cards.index')->with('success', 'Gift card created.');
    }

    public function giftCardUpdate(Request $request, GiftCard $giftCard)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:gift_cards,code,' . $giftCard->id],
            'balance' => ['required', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $giftCard->update([
            'code' => strtoupper(trim($validated['code'])),
            'balance' => (float) $validated['balance'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()->route('admin.gift-cards.index')->with('success', 'Gift card updated.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}

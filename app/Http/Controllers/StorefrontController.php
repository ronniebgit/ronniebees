<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\GiftCard;
use App\Models\Order;
use App\Models\Product;
use App\Models\Topping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class StorefrontController extends Controller
{
    public function index()
    {
        if (! Schema::hasTable('categories') || ! Schema::hasTable('products')) {
            return view('storefront.index', ['categories' => collect(), 'featuredProducts' => collect()]);
        }

        $categories = Category::with('products')
            ->visible()
            ->ordered()
            ->get();
        $featuredProducts = Product::with('category')
            ->visible()
            ->featured()
            ->ordered()
            ->get()
            ->sortBy(function ($product) {
                $priority = [
                    'build-your-own-pizza' => 1,
                    'margherita-pie' => 2,
                    'pepperoni-feast' => 3,
                    'bbq-chicken-pizza' => 4,
                    'buffalo-ranch' => 5,
                ];

                return $priority[strtolower((string) ($product->slug ?? ''))] ?? 99;
            })->values();

        return view('storefront.index', compact('categories', 'featuredProducts'));
    }

    public function menu()
    {
        if (! Schema::hasTable('products') || ! Schema::hasTable('categories')) {
            return view('storefront.menu', ['products' => collect(), 'categories' => collect()]);
        }

        $products = Product::with('category')
            ->visible()
            ->whereHas('category', function ($query) {
                $query->where('slug', 'drinks');
            })
            ->ordered()
            ->get();
        $categories = Category::withCount('products')
            ->visible()
            ->where('slug', 'drinks')
            ->ordered()
            ->get();

        return view('storefront.menu', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        if (! Schema::hasTable('products') || ! Schema::hasTable('toppings')) {
            abort(404);
        }

        $product->load('category');
        $featuredProducts = Product::with('category')
            ->visible()
            ->featured()
            ->whereKeyNot($product->id)
            ->ordered()
            ->limit(5)
            ->get();
        $toppings = Topping::active()->get();

        return view('storefront.show', compact('product', 'featuredProducts', 'toppings'));
    }

    public function giftCard(Request $request)
    {
        $code = trim((string) $request->query('code', ''));
        $giftCard = $code !== '' ? GiftCard::lookup($code) : null;

        return view('storefront.gift-card', compact('giftCard', 'code'));
    }

    public function giftCardLookup(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:255'],
        ]);

        $giftCard = GiftCard::lookup($data['code']);

        if (! $giftCard) {
            return redirect()->route('gift-card')->with('error', 'That gift card code could not be found or is inactive.')->withInput();
        }

        return redirect()->route('gift-card', ['code' => $giftCard->code])
            ->with('success', 'Gift card balance: $' . number_format($giftCard->balance, 2));
    }

    public function track()
    {
        return view('storefront.track');
    }

    public function trackLookup(Request $request)
    {
        $data = $request->validate([
            'order_id' => ['required', 'integer'],
            'phone' => ['required', 'string', 'max:255'],
        ]);

        $order = Order::where('id', $data['order_id'])
            ->whereRaw('LOWER(customer_phone) = ?', [strtolower(preg_replace('/\D+/', '', $data['phone']))])
            ->first();

        if (! $order) {
            return back()->with('error', 'We could not find an order matching that number and phone.')->withInput();
        }

        $order->load('items');

        return view('storefront.track', compact('order'))->with('success', 'Order found.');
    }
}

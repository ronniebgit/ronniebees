<?php

namespace App\Http\Controllers;

use App\Models\GiftCard;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

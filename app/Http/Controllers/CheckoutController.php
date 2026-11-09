<?php

namespace App\Http\Controllers;

use App\Models\GiftCard;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('menu')->with('error', 'Your cart is empty.');
        }

        $subtotal = collect($cart)->sum(function ($item) {
            return (float) $item['quantity'] * (float) $item['unit_price'];
        });

        return view('storefront.checkout', compact('cart', 'subtotal'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email'],
            'delivery_type' => ['required', 'in:pickup,delivery'],
            'payment_method' => ['required', 'in:card,cash,apple_pay,paypal,stripe,gift_card'],
            'payment_status' => ['sometimes', 'in:pending,paid,failed'],
            'delivery_address' => ['nullable', 'string'],
            'gift_card_code' => ['nullable', 'string', 'max:255'],
            'loyalty_points_to_use' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('menu')->with('error', 'Your cart is empty.');
        }

        $subtotal = collect($cart)->sum(function ($item) {
            return (float) $item['quantity'] * (float) $item['unit_price'];
        });

        $giftCardAmountUsed = 0.0;
        $giftCardCode = trim((string) ($data['gift_card_code'] ?? ''));
        $loyaltyDiscount = 0.0;

        if (auth()->check()) {
            $pointsToUse = (int) ($data['loyalty_points_to_use'] ?? 0);
            if ($pointsToUse > 0) {
                $loyaltyDiscount = auth()->user()->redeemPoints($pointsToUse);
            }
        }

        if ($giftCardCode !== '') {
            $giftCard = GiftCard::lookup($giftCardCode);

            if (! $giftCard) {
                return back()->withInput()->with('error', 'The gift card code you entered could not be found.');
            }

            $giftCardResult = GiftCard::applyBalance($subtotal, (float) $giftCard->balance);
            $giftCardAmountUsed = (float) $giftCardResult['applied'];

            $giftCard->balance = max(0.00, (float) $giftCard->balance - $giftCardAmountUsed);
            $giftCard->save();
        }

        $paymentStatus = $data['payment_status'] ?? (($data['payment_method'] === 'cash') ? 'pending' : 'paid');
        $orderTotal = max(0.0, $subtotal - $loyaltyDiscount - $giftCardAmountUsed);

        $order = Order::create([
            'customer_name' => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'customer_email' => $data['customer_email'] ?? null,
            'delivery_type' => $data['delivery_type'],
            'payment_method' => $data['payment_method'],
            'gift_card_code' => $giftCardCode !== '' ? strtoupper($giftCardCode) : null,
            'gift_card_amount_used' => $giftCardAmountUsed,
            'delivery_address' => $data['delivery_address'] ?? null,
            'total' => $orderTotal,
            'status' => 'confirmed',
            'payment_status' => $paymentStatus,
            'notes' => $data['notes'] ?? null,
        ]);

        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'product_name' => $item['name'],
                'size' => $item['size'],
                'split_type' => $item['split_type'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['quantity'] * $item['unit_price'],
                'special_instructions' => $item['special_instructions'] ?? null,
                'toppings' => $item['toppings'] ?? [],
            ]);
        }

        if (auth()->check()) {
            auth()->user()->awardLoyaltyPoints((float) $subtotal);
        }

        Session::forget('cart');

        return redirect()->route('checkout.confirmation', $order)->with('success', 'Your order has been placed successfully.');
    }

    public function confirmation(Order $order)
    {
        $order->load('items');

        return view('storefront.confirmation', compact('order'));
    }
}

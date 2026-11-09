@extends('layouts.storefront')

@section('content')
    <div class="mb-8">
        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600">Checkout</p>
        <h1 class="mt-3 text-4xl font-black text-stone-900">Finish your order</h1>
    </div>

    <div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
        <form action="{{ route('checkout.store') }}" method="POST" class="space-y-6 rounded-[2rem] border border-orange-100 bg-white p-6 shadow-sm md:p-8">
            @csrf

            <div>
                <h2 class="text-xl font-black text-stone-900">Contact details</h2>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="customer_name" class="mb-2 block text-sm font-bold uppercase tracking-[0.2em] text-stone-600">Name</label>
                        <input id="customer_name" name="customer_name" type="text" required class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-stone-700" placeholder="John Doe">
                    </div>
                    <div>
                        <label for="customer_phone" class="mb-2 block text-sm font-bold uppercase tracking-[0.2em] text-stone-600">Phone</label>
                        <input id="customer_phone" name="customer_phone" type="text" required class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-stone-700" placeholder="(555) 123-4567">
                    </div>
                    <div class="md:col-span-2">
                        <label for="customer_email" class="mb-2 block text-sm font-bold uppercase tracking-[0.2em] text-stone-600">Email</label>
                        <input id="customer_email" name="customer_email" type="email" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-stone-700" placeholder="you@example.com">
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-xl font-black text-stone-900">Order preference</h2>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <label class="rounded-2xl border border-stone-200 p-4 text-center cursor-pointer has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                        <input type="radio" name="delivery_type" value="pickup" checked class="sr-only">
                        <div class="text-sm font-bold text-stone-900">Pickup</div>
                    </label>
                    <label class="rounded-2xl border border-stone-200 p-4 text-center cursor-pointer has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                        <input type="radio" name="delivery_type" value="delivery" class="sr-only">
                        <div class="text-sm font-bold text-stone-900">Delivery</div>
                    </label>
                </div>
            </div>

            <div>
                <h2 class="text-xl font-black text-stone-900">Payment method</h2>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <label class="rounded-2xl border border-stone-200 p-4 text-center cursor-pointer has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                        <input type="radio" name="payment_method" value="card" checked class="sr-only">
                        <div class="text-sm font-bold text-stone-900">Credit / Debit</div>
                    </label>
                    <label class="rounded-2xl border border-stone-200 p-4 text-center cursor-pointer has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                        <input type="radio" name="payment_method" value="apple_pay" class="sr-only">
                        <div class="text-sm font-bold text-stone-900">Apple Pay</div>
                    </label>
                    <label class="rounded-2xl border border-stone-200 p-4 text-center cursor-pointer has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                        <input type="radio" name="payment_method" value="paypal" class="sr-only">
                        <div class="text-sm font-bold text-stone-900">PayPal</div>
                    </label>
                    <label class="rounded-2xl border border-stone-200 p-4 text-center cursor-pointer has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                        <input type="radio" name="payment_method" value="cash" class="sr-only">
                        <div class="text-sm font-bold text-stone-900">Cash at pickup</div>
                    </label>
                </div>
            </div>

            <div>
                <label for="gift_card_code" class="mb-2 block text-sm font-bold uppercase tracking-[0.2em] text-stone-600">Gift card code</label>
                <input id="gift_card_code" name="gift_card_code" type="text" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-stone-700" placeholder="Enter gift card code" value="{{ old('gift_card_code') }}">
            </div>

            @auth
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                    <div class="text-sm font-black uppercase tracking-[0.2em] text-amber-700">Loyalty redemption</div>
                    <div class="mt-2 text-sm text-stone-700">You have {{ auth()->user()->loyalty_points }} points available. 100 points = $1.00.</div>
                    <div class="mt-3">
                        <label for="loyalty_points_to_use" class="mb-2 block text-xs font-bold uppercase tracking-[0.2em] text-stone-600">Use points</label>
                        <input id="loyalty_points_to_use" name="loyalty_points_to_use" type="number" min="0" max="{{ auth()->user()->loyalty_points }}" value="{{ old('loyalty_points_to_use', 0) }}" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-stone-700">
                    </div>
                </div>
            @endauth

            <div>
                <label for="delivery_address" class="mb-2 block text-sm font-bold uppercase tracking-[0.2em] text-stone-600">Delivery address</label>
                <textarea id="delivery_address" name="delivery_address" rows="4" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-stone-700" placeholder="Street address, apartment, city, ZIP"></textarea>
            </div>

            <div>
                <label for="notes" class="mb-2 block text-sm font-bold uppercase tracking-[0.2em] text-stone-600">Special requests</label>
                <textarea id="notes" name="notes" rows="3" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-stone-700" placeholder="Extra sauce, no onions, gluten free crust..."></textarea>
            </div>

            <button type="submit" class="pizza-button w-full rounded-full px-6 py-3 text-base font-black shadow-lg">Place order</button>
        </form>

        <aside class="order-shell rounded-[2rem] p-6">
            <h2 class="text-2xl font-black text-stone-900">Order summary</h2>
            <div class="mt-6 space-y-4">
                @foreach ($cart as $item)
                    <div class="rounded-2xl border border-stone-200 bg-white p-4">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <div class="font-black text-stone-900">{{ $item['name'] }}</div>
                                <div class="mt-1 text-xs uppercase tracking-[0.2em] text-orange-600">{{ ucfirst($item['size']) }} · {{ str_replace('-', ' ', $item['split_type']) }}</div>
                            </div>
                            <div class="text-sm font-black text-stone-900">${{ number_format($item['quantity'] * $item['unit_price'], 2) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-stone-700">
                <div class="font-black uppercase tracking-[0.2em] text-amber-700">Loyalty rewards</div>
                <div class="mt-2">Earn <span class="font-bold text-stone-900">{{ (int) floor($subtotal) }}</span> points on this order.</div>
                <div class="mt-1 text-xs text-stone-500">100 points = $1 reward.</div>
            </div>

            <div class="mt-6 border-t border-stone-200 pt-4">
                <div class="flex items-center justify-between text-sm text-stone-600">
                    <span>Subtotal</span>
                    <span class="font-bold text-stone-900">${{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="mt-3 flex items-center justify-between text-sm text-stone-600">
                    <span>Gift card applied</span>
                    <span class="font-bold text-stone-900">-${{ number_format(0, 2) }}</span>
                </div>
                <div class="mt-3 flex items-center justify-between text-sm text-stone-600">
                    <span>Loyalty discount</span>
                    <span class="font-bold text-stone-900">-${{ number_format(0, 2) }}</span>
                </div>
                <div class="mt-3 flex items-center justify-between text-lg font-black text-stone-900">
                    <span>Total</span>
                    <span>${{ number_format($subtotal, 2) }}</span>
                </div>
            </div>
        </aside>
    </div>
@endsection

@extends('layouts.storefront')

@section('content')
    <div class="mb-8 flex items-end justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600">Your cart</p>
            <h1 class="mt-3 text-4xl font-black text-stone-900">Ready for checkout</h1>
        </div>
        <a href="{{ route('menu') }}" class="rounded-full border border-stone-300 bg-white px-4 py-2 text-sm font-semibold text-stone-700 hover:border-orange-300 hover:text-orange-700">Continue shopping</a>
    </div>

    @if ($items->isEmpty())
        <div class="order-shell rounded-3xl p-10 text-center">
            <div class="text-6xl">🛒</div>
            <h2 class="mt-5 text-2xl font-black text-stone-900">Your cart is empty</h2>
            <p class="mt-2 text-stone-600">Pick a pizza and build your perfect order.</p>
            <a href="{{ route('menu') }}" class="pizza-button mt-6 inline-flex rounded-full px-6 py-3 font-bold">Browse menu</a>
        </div>
    @else
        <div class="grid gap-8 lg:grid-cols-[1.4fr_0.6fr]">
            <div class="space-y-5">
                @foreach ($items as $item)
                    <div class="rounded-3xl border border-stone-200 bg-white p-5 shadow-sm">
                        <div class="flex flex-col gap-5 sm:flex-row">
                            <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-[radial-gradient(circle,_#fef3c7,_#fbbf24_35%,_#c2410c_100%)] text-3xl">
                                🍕
                            </div>

                            <div class="flex-1">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <h2 class="text-xl font-black text-stone-900">{{ $item['name'] }}</h2>
                                        <p class="mt-1 text-sm uppercase tracking-[0.2em] text-orange-600">{{ ucfirst($item['size']) }} · {{ str_replace('-', ' ', $item['split_type']) }}</p>
                                        @if (!empty($item['toppings']))
                                            <p class="mt-2 text-sm text-stone-600">
                                                Toppings: {{ collect($item['toppings'])->map(fn ($topping) => $topping['name'] . ' (' . $topping['portion'] . ')')->implode(', ') }}
                                            </p>
                                        @endif
                                        @if (!empty($item['special_instructions']))
                                            <p class="mt-2 text-sm text-stone-600">{{ $item['special_instructions'] }}</p>
                                        @endif
                                    </div>
                                    <div class="text-xl font-black text-stone-900">${{ number_format($item['line_total'], 2) }}</div>
                                </div>

                                <form action="{{ route('cart.update', $item['id']) }}" method="POST" class="mt-5 grid gap-4 md:grid-cols-[1fr_1fr_1fr_auto]">
                                    @csrf
                                    <div>
                                        <label class="mb-2 block text-xs font-bold uppercase tracking-[0.2em] text-stone-500">Size</label>
                                        <select name="size" class="w-full rounded-xl border border-stone-300 bg-white px-3 py-2 text-sm font-medium text-stone-700">
                                            @foreach (['small' => 'Small', 'medium' => 'Medium', 'large' => 'Large'] as $size => $label)
                                                <option value="{{ $size }}" {{ $item['size'] === $size ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-xs font-bold uppercase tracking-[0.2em] text-stone-500">Split</label>
                                        <select name="split_type" class="w-full rounded-xl border border-stone-300 bg-white px-3 py-2 text-sm font-medium text-stone-700">
                                            @foreach (['whole' => 'Whole pie', 'half-and-half' => 'Half and half', 'first-half' => 'First half', 'second-half' => 'Second half'] as $split => $label)
                                                <option value="{{ $split }}" {{ $item['split_type'] === $split ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-xs font-bold uppercase tracking-[0.2em] text-stone-500">Qty</label>
                                        <input type="number" name="quantity" min="1" value="{{ $item['quantity'] }}" class="w-full rounded-xl border border-stone-300 bg-white px-3 py-2 text-sm font-medium text-stone-700">
                                    </div>
                                    <div class="flex items-end">
                                        <button type="submit" class="w-full rounded-xl border border-orange-300 bg-orange-50 px-4 py-2 text-sm font-bold text-orange-700 hover:bg-orange-100">Update</button>
                                    </div>
                                </form>

                                <div class="mt-4 flex justify-end">
                                    <form action="{{ route('cart.remove', $item['id']) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-700">Remove</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="order-shell rounded-3xl p-6">
                <h2 class="text-2xl font-black text-stone-900">Order totals</h2>
                <div class="mt-6 space-y-4 text-sm text-stone-600">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span class="font-bold text-stone-900">${{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Delivery</span>
                        <span class="font-bold text-stone-900">$0.00</span>
                    </div>
                    <div class="border-t border-stone-200 pt-4 flex justify-between text-lg font-black text-stone-900">
                        <span>Total</span>
                        <span>${{ number_format($subtotal, 2) }}</span>
                    </div>
                </div>

                <a href="{{ route('checkout') }}" class="pizza-button mt-6 inline-flex w-full justify-center rounded-full px-6 py-3 text-base font-black">Proceed to checkout</a>
            </div>
        </div>
    @endif
@endsection

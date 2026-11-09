@extends('layouts.storefront')

@section('content')
    <section class="bg-stone-950 py-20 text-white">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center">
                <p class="text-sm uppercase tracking-[0.3em] text-amber-400">Ronnie Bee's</p>
                <h1 class="mt-3 text-4xl font-black md:text-5xl">Track your order</h1>
                <p class="mt-4 text-stone-300">Use your order number and phone number to check the status of your pizza.</p>
            </div>

            @if(session('error'))
                <div class="mb-6 rounded-xl border border-red-500/40 bg-red-950/40 px-4 py-3 text-sm text-red-100">
                    {{ session('error') }}
                </div>
            @endif

            @if(isset($order))
                <div class="rounded-3xl border border-amber-500/30 bg-stone-900 p-8 shadow-2xl shadow-amber-500/10">
                    <div class="flex flex-col gap-4 border-b border-stone-700 pb-6 md:flex-row md:items-end md:justify-between">
                        <div>
                            <p class="text-sm uppercase tracking-[0.25em] text-stone-400">Order lookup</p>
                            <h2 class="mt-2 text-3xl font-bold">Order #{{ $order->id }}</h2>
                        </div>
                        <span class="inline-flex rounded-full bg-emerald-500/15 px-4 py-2 text-sm font-semibold text-emerald-300">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>

                    <div class="mt-6 grid gap-6 md:grid-cols-2">
                        <div>
                            <p class="text-sm uppercase tracking-[0.2em] text-stone-400">Customer</p>
                            <p class="mt-2 text-xl font-semibold text-white">{{ $order->customer_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm uppercase tracking-[0.2em] text-stone-400">Total</p>
                            <p class="mt-2 text-xl font-semibold text-amber-300">${{ number_format($order->total, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm uppercase tracking-[0.2em] text-stone-400">Pickup / Delivery</p>
                            <p class="mt-2 text-lg capitalize text-white">{{ $order->delivery_type }}</p>
                        </div>
                        <div>
                            <p class="text-sm uppercase tracking-[0.2em] text-stone-400">Payment</p>
                            <p class="mt-2 text-lg capitalize text-white">{{ str_replace('_', ' ', $order->payment_method) }}</p>
                        </div>
                    </div>

                    @if($order->items && $order->items->count())
                        <div class="mt-8">
                            <h3 class="text-xl font-bold text-white">Order items</h3>
                            <div class="mt-4 space-y-3">
                                @foreach($order->items as $item)
                                    <div class="flex items-center justify-between rounded-2xl border border-stone-700 bg-stone-950/60 px-4 py-3">
                                        <div>
                                            <p class="font-semibold text-white">{{ $item->product_name ?? 'Pizza' }}</p>
                                            <p class="text-sm text-stone-400">{{ ucfirst($item->size) }} • {{ ucfirst(str_replace('_', ' ', $item->split_type ?? 'whole')) }} • Qty {{ $item->quantity }}</p>
                                        </div>
                                        <p class="text-sm font-semibold text-amber-300">${{ number_format($item->total_price, 2) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <form method="POST" action="{{ route('track.lookup') }}" class="mx-auto max-w-xl rounded-3xl border border-stone-700 bg-stone-900 p-8 shadow-xl shadow-black/40">
                    @csrf
                    <div class="space-y-5">
                        <div>
                            <label for="order_id" class="mb-2 block text-sm font-medium text-stone-200">Order number</label>
                            <input id="order_id" name="order_id" type="number" value="{{ old('order_id') }}" class="w-full rounded-xl border border-stone-600 bg-stone-950 px-4 py-3 text-white outline-none ring-0 transition focus:border-amber-400" placeholder="e.g. 104" required>
                        </div>
                        <div>
                            <label for="phone" class="mb-2 block text-sm font-medium text-stone-200">Phone number</label>
                            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" class="w-full rounded-xl border border-stone-600 bg-stone-950 px-4 py-3 text-white outline-none ring-0 transition focus:border-amber-400" placeholder="(555) 123-4567" required>
                        </div>
                        <button type="submit" class="w-full rounded-xl bg-amber-400 px-5 py-3 text-base font-bold text-stone-950 transition hover:bg-amber-300">
                            Track my order
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </section>
@endsection

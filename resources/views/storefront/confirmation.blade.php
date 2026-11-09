@extends('layouts.storefront')

@section('content')
    <div class="mx-auto max-w-3xl rounded-[2rem] border border-emerald-200 bg-white p-8 shadow-sm">
        <div class="flex items-center justify-center text-5xl">✅</div>
        <h1 class="mt-6 text-center text-4xl font-black text-stone-900">Order confirmed</h1>
        <p class="mt-4 text-center text-stone-600">
            Thanks, {{ $order->customer_name }}. Your order #{{ $order->id }} is in progress.
        </p>

        <div class="mt-8 rounded-2xl bg-emerald-50 p-5 text-sm text-emerald-800">
            <div class="flex items-center justify-between">
                <span>Status</span>
                <span class="font-bold uppercase tracking-[0.18em]">{{ ucfirst($order->status) }}</span>
            </div>
            <div class="mt-3 flex items-center justify-between">
                <span>Delivery type</span>
                <span class="font-bold uppercase tracking-[0.18em]">{{ ucfirst($order->delivery_type) }}</span>
            </div>
            <div class="mt-3 flex items-center justify-between">
                <span>Payment</span>
                <span class="font-bold uppercase tracking-[0.12em]">{{ str_replace('_', ' ', ucfirst($order->payment_method ?? 'card')) }}</span>
            </div>
            <div class="mt-3 flex items-center justify-between">
                <span>Total</span>
                <span class="font-bold">${{ number_format($order->total, 2) }}</span>
            </div>
        </div>

        <div class="mt-8 space-y-4">
            @foreach ($order->items as $item)
                <div class="flex items-center justify-between rounded-2xl border border-stone-200 p-4">
                    <div>
                        <div class="font-black text-stone-900">{{ $item->product_name }}</div>
                        <div class="text-xs uppercase tracking-[0.2em] text-orange-600">{{ ucfirst($item->size) }} · {{ str_replace('-', ' ', $item->split_type) }}</div>
                    </div>
                    <div class="text-sm font-bold text-stone-900">Qty: {{ $item->quantity }}</div>
                </div>
            @endforeach
        </div>

        <div class="mt-8 flex justify-center">
            <a href="{{ route('home') }}" class="pizza-button rounded-full px-6 py-3 text-sm font-black">Back to home</a>
        </div>
    </div>
@endsection

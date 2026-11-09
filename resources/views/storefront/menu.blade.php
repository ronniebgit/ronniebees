@extends('layouts.storefront')

@section('content')
    <div class="mb-8">
        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600">Our menu</p>
        <h1 class="mt-3 text-4xl font-black text-stone-900">Build your perfect pie</h1>
    </div>

    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($products as $product)
            <div class="pizza-card overflow-hidden rounded-3xl">
                <div class="flex h-40 items-center justify-center bg-[radial-gradient(circle_at_top,_#fcd34d,_#fb923c_50%,_#9a4d0f_100%)] text-5xl">
                    🍕
                </div>
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-[0.2em] text-orange-600">{{ $product->category->name }}</span>
                        <span class="rounded-full bg-orange-100 px-2 py-1 text-[10px] font-bold uppercase tracking-[0.2em] text-orange-700">{{ $product->active ? 'Active' : 'Paused' }}</span>
                    </div>
                    <h3 class="mt-4 text-2xl font-black text-stone-900">{{ $product->name }}</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-600">{{ $product->description }}</p>

                    <div class="mt-5 flex items-center justify-between">
                        <div>
                            <div class="text-xs uppercase tracking-[0.2em] text-stone-500">From</div>
                            <div class="text-2xl font-black text-stone-900">${{ number_format($product->medium_price, 2) }}</div>
                        </div>
                        <a href="{{ route('product.show', $product) }}" class="pizza-button rounded-full px-4 py-2 text-sm font-bold">Customize</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-12">
        <div class="mb-6">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600">Refreshments</p>
            <h2 class="mt-3 text-3xl font-black text-stone-900">Drinks</h2>
        </div>

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @php
                $drinks = [
                    'Coke',
                    'Diet Coke',
                    'Sprite',
                    'Dr Pepper',
                    'Water',
                    'Arizona Green Tea',
                ];
            @endphp

            @foreach ($drinks as $drink)
                <div class="rounded-3xl border border-orange-100 bg-orange-50 p-5 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-orange-600">Drink</p>
                            <h3 class="mt-2 text-xl font-black text-stone-900">{{ $drink }}</h3>
                        </div>
                        <div class="rounded-full bg-white px-3 py-1 text-sm font-black text-stone-900">$1.50</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

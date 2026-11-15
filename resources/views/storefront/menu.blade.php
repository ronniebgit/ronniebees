@extends('layouts.storefront')

@section('content')
    <div class="mb-8 rounded-[2rem] border border-[#3a1d1b] bg-[#1a1412] p-6 shadow-2xl shadow-black/20 md:p-8">
        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#ffd54f]">Refreshments</p>
        <h1 class="mt-3 text-4xl font-black text-white">Drinks</h1>
    </div>

    @if ($products->isEmpty())
        <div class="rounded-[2rem] border border-[#3a1d1b] bg-[#17110f] p-8 text-center shadow-xl shadow-black/20">
            <p class="text-lg font-semibold text-[#f7efe4]">No drinks are available right now.</p>
        </div>
    @else
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($products as $product)
                <div class="overflow-hidden rounded-3xl border border-[#3a1d1b] bg-[#17110f] shadow-xl shadow-black/20">
                    <div class="flex h-40 items-center justify-center bg-[radial-gradient(circle_at_top,_#fcd34d,_#fb923c_50%,_#9a4d0f_100%)] text-5xl">
                        🥤
                    </div>
                    <div class="p-5">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-xs font-bold uppercase tracking-[0.2em] text-[#ffd54f]">{{ $product->category->name ?? 'Drinks' }}</span>
                            <span class="rounded-full bg-[#2d1c1a] px-2 py-1 text-[10px] font-bold uppercase tracking-[0.2em] text-[#f7efe4]">{{ $product->active ? 'Active' : 'Paused' }}</span>
                        </div>
                        <h3 class="mt-4 text-2xl font-black text-white">{{ $product->name }}</h3>
                        <p class="mt-2 text-sm leading-6 text-[#f7efe4]">{{ $product->description }}</p>

                        <div class="mt-5 flex items-center justify-between">
                            <div>
                                <div class="text-xs uppercase tracking-[0.2em] text-[#f0d8a7]">Price</div>
                                <div class="text-2xl font-black text-[#ffd54f]">${{ number_format((float) ($product->base_price ?? $product->medium_price ?? 0), 2) }}</div>
                            </div>
                            <a href="{{ route('product.show', $product) }}" class="rounded-full bg-[#b7181f] px-4 py-2 text-sm font-bold text-white shadow-lg shadow-red-950/50 hover:bg-[#8c1117]">Order</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

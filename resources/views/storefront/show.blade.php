@extends('layouts.storefront')

@section('content')
    @php
        $categorySlug = strtolower((string) ($product->category->slug ?? ''));
        $isDrinkProduct = $categorySlug === 'drinks';
        $isByo = ! $isDrinkProduct && (str_contains($categorySlug, 'byo') || str_contains(strtolower((string) ($product->slug ?? '')), 'build-your-own'));
        $availableSizes = $isDrinkProduct
            ? [
                'single' => ['label' => 'Single', 'price' => $product->base_price],
            ]
            : ($isByo
                ? [
                    'small' => ['label' => '10"', 'price' => $product->small_price],
                    'medium' => ['label' => '14"', 'price' => $product->medium_price],
                    'large' => ['label' => '18"', 'price' => $product->large_price],
                ]
                : [
                    'medium' => ['label' => '14"', 'price' => $product->medium_price],
                    'large' => ['label' => '18"', 'price' => $product->large_price],
                ]);
    @endphp

    <div class="grid gap-10 lg:grid-cols-[0.9fr_1.1fr]">
        <div class="flex items-center justify-center rounded-[2rem] border border-[#3a1d1b] bg-[radial-gradient(circle_at_top,_#fef3c7,_#fbbf24_35%,_#c2410c_100%)] p-10 shadow-2xl shadow-black/30">
            <div class="flex h-72 w-72 items-center justify-center rounded-full border-[12px] border-orange-200/60 bg-[radial-gradient(circle,_#fde68a,_#f59e0b_35%,_#7c2d12_100%)] shadow-2xl">
                <span class="text-7xl">🍕</span>
            </div>
        </div>

        <div class="rounded-[2rem] border border-[#3a1d1b] bg-[#17110f] p-6 shadow-2xl shadow-black/25 md:p-8">
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#ffd54f]">{{ $product->category->name }}</p>
            <h1 class="mt-3 text-4xl font-black text-white">{{ $product->name }}</h1>
            <p class="mt-4 text-base leading-7 text-[#f7efe4]">{{ $product->description }}</p>

            @if ($isDrinkProduct)
                <div class="mt-6 rounded-2xl border border-[#3a1d1b] bg-[#221b18] p-4">
                    <div class="text-xs font-semibold uppercase tracking-[0.22em] text-[#ffd54f]">Price</div>
                    <div class="mt-3 grid gap-3 sm:grid-cols-1">
                        <div class="rounded-xl border border-[#3a1d1b] bg-[#120d0c] p-3">
                            <div class="text-[10px] uppercase tracking-[0.2em] text-[#f0d8a7]">Single</div>
                            <div class="mt-2 text-xl font-black text-white">${{ number_format((float) ($product->base_price ?? 0), 2) }}</div>
                        </div>
                    </div>
                </div>
            @else
                <div class="mt-6 rounded-2xl border border-[#3a1d1b] bg-[#221b18] p-4">
                    <div class="text-xs font-semibold uppercase tracking-[0.22em] text-[#ffd54f]">Price guide</div>
                    <div class="mt-3 grid gap-3 {{ $isByo ? 'sm:grid-cols-3' : 'sm:grid-cols-2' }}">
                        @foreach ($availableSizes as $sizeKey => $sizeMeta)
                            <div class="rounded-xl border border-[#3a1d1b] bg-[#120d0c] p-3">
                                <div class="text-[10px] uppercase tracking-[0.2em] text-[#f0d8a7]">{{ $sizeMeta['label'] }}</div>
                                <div class="mt-2 text-xl font-black text-white">${{ number_format((float) $sizeMeta['price'], 2) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="{{ route('cart.add') }}" method="POST" class="mt-8 space-y-6" id="pizza-order-form">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                @if (! $isDrinkProduct)
                    <div>
                        <label class="mb-3 block text-sm font-bold uppercase tracking-[0.2em] text-[#f7efe4]">Choose pie split</label>
                        <div class="grid gap-3 sm:grid-cols-3">
                            @foreach (['whole' => 'Whole pie', 'first-half' => 'First half', 'second-half' => 'Second half'] as $split => $label)
                                <label class="cursor-pointer rounded-2xl border border-[#3a1d1b] bg-[#120d0c] p-3 text-center has-[:checked]:border-[#ffd54f] has-[:checked]:bg-[#221b18]">
                                    <input type="radio" name="split_type" value="{{ $split }}" {{ $split === 'whole' ? 'checked' : '' }} class="sr-only split-radio" required>
                                    <div class="text-sm font-bold text-white">{{ $label }}</div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="mb-3 block text-sm font-bold uppercase tracking-[0.2em] text-[#f7efe4]">Choose size</label>
                        <div class="grid gap-3 {{ $isByo ? 'sm:grid-cols-3' : 'sm:grid-cols-2' }}">
                            @foreach ($availableSizes as $size => $meta)
                                <label class="cursor-pointer rounded-2xl border border-[#3a1d1b] bg-[#120d0c] p-3 text-center has-[:checked]:border-[#ffd54f] has-[:checked]:bg-[#221b18]">
                                    <input type="radio" name="size" value="{{ $size }}" {{ $size === 'large' ? 'checked' : '' }} class="sr-only size-radio" required>
                                    <div class="text-sm font-bold text-white">{{ $meta['label'] }}</div>
                                    <div class="mt-1 text-xs text-[#f0d8a7]">${{ number_format((float) $product->priceForSize($size), 2) }}</div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="mb-3 block text-sm font-bold uppercase tracking-[0.2em] text-[#f7efe4]">Extra toppings</label>

                        @foreach (['whole' => 'Whole', 'first-half' => '1st Half', 'second-half' => '2nd Half'] as $portion => $label)
                            <div class="topping-split-group {{ $portion === 'whole' ? '' : 'hidden' }} mb-4 rounded-2xl border border-[#3a1d1b] bg-[#120d0c] p-3" data-split-group="{{ $portion }}">
                                <div class="mb-3 text-[10px] font-bold uppercase tracking-[0.2em] text-[#f0d8a7]">{{ $label }}</div>
                                <div class="space-y-2">
                                    @foreach($toppings as $topping)
                                        <label class="flex items-center gap-2 rounded-xl border border-[#3a1d1b] bg-[#120d0c] p-2 text-sm font-semibold text-[#f7efe4]">
                                            <input type="checkbox"
                                                name="topping_portions[{{ $topping->id }}][]"
                                                value="{{ $portion }}"
                                                class="topping-checkbox h-4 w-4 rounded border-[#f0d8a7] text-[#ffd54f] focus:ring-[#ffd54f]"
                                                data-portion="{{ $portion }}">
                                            <span>{{ $topping->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <p id="topping-price-note" class="mt-3 text-[10px] font-bold uppercase tracking-[0.2em] text-[#f0d8a7]">Toppings are priced by size and portion.</p>
                    </div>
                @endif

                <div class="mb-4 rounded-2xl border border-[#3a1d1b] bg-[#120d0c] p-4">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-xs font-bold uppercase tracking-[0.2em] text-[#f0d8a7]">Subtotal</span>
                        <span id="pizza-subtotal" class="text-2xl font-black text-[#ffd54f]">${{ number_format((float) ($product->base_price ?? 0), 2) }}</span>
                    </div>
                </div>

                @if ($isDrinkProduct)
                    <input type="hidden" name="size" value="single">
                    <input type="hidden" name="split_type" value="whole">
                @endif

                <div>
                    <label for="quantity" class="mb-3 block text-sm font-bold uppercase tracking-[0.2em] text-[#f7efe4]">Quantity</label>
                    <input id="quantity" type="number" name="quantity" min="1" value="1" class="w-28 rounded-xl border border-[#3a1d1b] bg-[#120d0c] px-3 py-3 text-lg font-semibold text-white">
                </div>

                <div>
                    <label for="special_instructions" class="mb-3 block text-sm font-bold uppercase tracking-[0.2em] text-[#f7efe4]">Special instructions</label>
                    <textarea id="special_instructions" name="special_instructions" rows="3" class="w-full rounded-2xl border border-[#3a1d1b] bg-[#120d0c] px-4 py-3 text-sm text-[#f7efe4] placeholder:text-[#d6c3a7]" placeholder="Extra sauce, no onions..."></textarea>
                </div>

                <button type="submit" class="w-full rounded-full bg-[#b7181f] px-6 py-3 text-base font-black text-white shadow-lg shadow-red-950/50 hover:bg-[#8c1117]">
                    Add to cart
                </button>
            </form>

            @if (! $isDrinkProduct)
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const sizeInputs = document.querySelectorAll('.size-radio');
                        const splitInputs = document.querySelectorAll('.split-radio');
                        const toppingGroups = document.querySelectorAll('.topping-split-group');
                        const toppingCheckboxes = document.querySelectorAll('.topping-checkbox');
                        const toppingPriceNote = document.getElementById('topping-price-note');
                        const subtotalEl = document.getElementById('pizza-subtotal');

                        const productPrices = {
                            @if ($isByo)
                                small: Number({{ (float) ($product->small_price ?? $product->base_price ?? 0) }}),
                            @endif
                            medium: Number({{ (float) ($product->medium_price ?? $product->base_price ?? 0) }}),
                            large: Number({{ (float) ($product->large_price ?? $product->base_price ?? 0) }})
                        };

                        const toppingPricing = {
                            @if ($isByo)
                                small: 1.25,
                            @endif
                            medium: 1.75,
                            large: 2.50
                        };

                        function formatCurrency(value) {
                            return new Intl.NumberFormat('en-US', {
                                style: 'currency',
                                currency: 'USD'
                            }).format(value);
                        }

                        function updateVisibleSplit() {
                            const selectedSplit = document.querySelector('input[name="split_type"]:checked')?.value || 'whole';

                            toppingGroups.forEach((group) => {
                                const shouldShow = group.dataset.splitGroup === selectedSplit;
                                group.classList.toggle('hidden', !shouldShow);
                            });
                        }

                        function updateSubtotal() {
                            const selectedSize = document.querySelector('input[name="size"]:checked')?.value || 'large';
                            const basePrice = productPrices[selectedSize] || productPrices.large;
                            const toppingRate = toppingPricing[selectedSize] || toppingPricing.large;

                            let subtotal = basePrice;

                            toppingCheckboxes.forEach((checkbox) => {
                                if (!checkbox.checked) {
                                    return;
                                }

                                const factor = ['first-half', 'second-half'].includes(checkbox.dataset.portion) ? 0.5 : 1;
                                subtotal += toppingRate * factor;
                            });

                            subtotalEl.textContent = formatCurrency(subtotal);
                        }

                        function updateToppingPricing() {
                            const selectedSize = document.querySelector('input[name="size"]:checked')?.value || 'large';
                            const selectedLabel = {
                                small: '10"',
                                medium: '14"',
                                large: '18"'
                            }[selectedSize] || '18"';
                            const currentPrice = toppingPricing[selectedSize] || toppingPricing.large;
                            toppingPriceNote.textContent = `Toppings are priced at ${selectedLabel} for $${currentPrice.toFixed(2)} each.`;
                        }

                        sizeInputs.forEach((input) => {
                            input.addEventListener('change', function () {
                                updateToppingPricing();
                                updateSubtotal();
                            });
                        });

                        splitInputs.forEach((input) => {
                            input.addEventListener('change', function () {
                                updateVisibleSplit();
                                updateSubtotal();
                            });
                        });

                        toppingCheckboxes.forEach((checkbox) => {
                            checkbox.addEventListener('change', updateSubtotal);
                        });

                        updateVisibleSplit();
                        updateToppingPricing();
                        updateSubtotal();
                    });
                </script>
            @endif
        </div>
    </div>
@endsection

@extends('layouts.storefront')

@section('content')
    <div class="grid gap-10 lg:grid-cols-[0.9fr_1.1fr]">
        <div class="pizza-card flex items-center justify-center rounded-[2rem] bg-[radial-gradient(circle_at_top,_#fef3c7,_#fbbf24_35%,_#c2410c_100%)] p-10">
            <div class="flex h-72 w-72 items-center justify-center rounded-full border-[12px] border-orange-200/60 bg-[radial-gradient(circle,_#fde68a,_#f59e0b_35%,_#7c2d12_100%)] shadow-2xl">
                <span class="text-7xl">🍕</span>
            </div>
        </div>

        <div class="rounded-[2rem] border border-orange-100 bg-white p-6 shadow-sm md:p-8">
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-orange-600">{{ $product->category->name }}</p>
            <h1 class="mt-3 text-4xl font-black text-stone-900">{{ $product->name }}</h1>
            <p class="mt-4 text-base leading-7 text-stone-600">{{ $product->description }}</p>

            <div class="mt-6 rounded-2xl bg-orange-50 p-4">
                <div class="text-xs font-semibold uppercase tracking-[0.22em] text-orange-600">Price guide</div>
                <div class="mt-3 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-xl bg-white p-3">
                        <div class="text-[10px] uppercase tracking-[0.2em] text-stone-500">10"</div>
                        <div class="mt-2 text-xl font-black text-stone-900">${{ number_format($product->small_price, 2) }}</div>
                    </div>
                    <div class="rounded-xl bg-white p-3">
                        <div class="text-[10px] uppercase tracking-[0.2em] text-stone-500">14"</div>
                        <div class="mt-2 text-xl font-black text-stone-900">${{ number_format($product->medium_price, 2) }}</div>
                    </div>
                    <div class="rounded-xl bg-white p-3">
                        <div class="text-[10px] uppercase tracking-[0.2em] text-stone-500">18"</div>
                        <div class="mt-2 text-xl font-black text-stone-900">${{ number_format($product->large_price, 2) }}</div>
                    </div>
                </div>
            </div>

            <form action="{{ route('cart.add') }}" method="POST" class="mt-8 space-y-6" id="pizza-order-form">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <div>
                    <label class="mb-3 block text-sm font-bold uppercase tracking-[0.2em] text-stone-700">Choose your pizza</label>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <button type="button" class="pizza-choice-button active" data-choice="build-your-own" data-label="Build Your Own Pizza">
                            <span>Build Your Own Pizza</span>
                        </button>
                        @foreach ($featuredProducts as $specialtyProduct)
                            <button type="button" class="pizza-choice-button" data-choice="specialty" data-label="{{ $specialtyProduct->name }}" data-price-medium="{{ number_format((float) ($specialtyProduct->medium_price ?? $specialtyProduct->base_price ?? 0), 2) }}" data-price-large="{{ number_format((float) ($specialtyProduct->large_price ?? $specialtyProduct->base_price ?? 0), 2) }}">
                                <span>{{ $specialtyProduct->name }}</span>
                                <small>14" ${{ number_format((float) ($specialtyProduct->medium_price ?? $specialtyProduct->base_price ?? 0), 2) }} / 18" ${{ number_format((float) ($specialtyProduct->large_price ?? $specialtyProduct->base_price ?? 0), 2) }}</small>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="mb-3 block text-sm font-bold uppercase tracking-[0.2em] text-stone-700">Choose size</label>
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach (['medium' => '14"', 'large' => '18"'] as $size => $label)
                            <label class="rounded-2xl border border-stone-200 p-3 text-center cursor-pointer has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                                <input type="radio" name="size" value="{{ $size }}" {{ $size === 'medium' ? 'checked' : '' }} class="sr-only size-radio" required>
                                <div class="text-sm font-bold text-stone-900">{{ $label }}</div>
                                <div class="mt-1 text-xs text-stone-500">${{ number_format($product->priceForSize($size), 2) }}</div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="mb-3 block text-sm font-bold uppercase tracking-[0.2em] text-stone-700">Toppings</label>
                    <div class="grid gap-3 sm:grid-cols-3">
                        <label class="split-option-tile">
                            <input type="radio" name="split_type" value="whole" class="sr-only split-option">
                            <div class="split-option-label">Whole</div>
                        </label>
                        <label class="split-option-tile">
                            <input type="radio" name="split_type" value="first-half" class="sr-only split-option">
                            <div class="split-option-label">First half</div>
                        </label>
                        <label class="split-option-tile">
                            <input type="radio" name="split_type" value="second-half" class="sr-only split-option">
                            <div class="split-option-label">Second half</div>
                        </label>
                    </div>
                    <p class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-stone-500">Toppings appear after you choose a pizza style and Whole, First half, or Second half.</p>
                </div>

                <div id="topping-options" class="hidden">
                    <label class="mb-3 block text-sm font-bold uppercase tracking-[0.2em] text-stone-900">Toppings</label>
                    <p id="topping-price-note" class="mb-3 text-xs uppercase tracking-[0.2em] text-stone-800">Choose a split option to reveal topping pricing.</p>
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach($toppings as $topping)
                            <div class="rounded-2xl border border-stone-200 bg-stone-50 p-3">
                                <label class="flex items-center gap-2 text-sm font-semibold text-stone-900">
                                    <input type="checkbox" name="toppings[]" value="{{ $topping->id }}" class="h-4 w-4 rounded border-stone-300 text-orange-600 focus:ring-orange-500 topping-checkbox">
                                    {{ $topping->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-4 rounded-2xl bg-orange-50 p-4">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-xs font-bold uppercase tracking-[0.2em] text-stone-600">Subtotal</span>
                        <span id="pizza-subtotal" class="text-2xl font-black text-stone-900">$0.00</span>
                    </div>
                </div>

                <div>
                    <label for="quantity" class="mb-3 block text-sm font-bold uppercase tracking-[0.2em] text-stone-700">Quantity</label>
                    <input id="quantity" type="number" name="quantity" min="1" value="1" class="w-28 rounded-xl border border-stone-300 bg-white px-3 py-3 text-lg font-semibold text-stone-900">
                </div>

                <div>
                    <label for="special_instructions" class="mb-3 block text-sm font-bold uppercase tracking-[0.2em] text-stone-700">Special instructions</label>
                    <textarea id="special_instructions" name="special_instructions" rows="3" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm text-stone-700" placeholder="Extra sauce, no onions, split toppings..."></textarea>
                </div>

                <button type="submit" class="pizza-button w-full rounded-full px-6 py-3 text-base font-black shadow-lg">
                    Add to cart
                </button>
            </form>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const sizeInputs = document.querySelectorAll('.size-radio');
                    const splitInputs = document.querySelectorAll('.split-option');
                    const toppingOptions = document.getElementById('topping-options');
                    const toppingPriceNote = document.getElementById('topping-price-note');
                    const pizzaChoiceButtons = document.querySelectorAll('.pizza-choice-button');
                    const subtotalEl = document.getElementById('pizza-subtotal');
                    const toppingCheckboxes = document.querySelectorAll('.topping-checkbox');

                    const productPrices = {
                        medium: Number({{ (float) ($product->medium_price ?? $product->base_price ?? 0) }}),
                        large: Number({{ (float) ($product->large_price ?? $product->base_price ?? 0) }})
                    };

                    const toppingPricing = {
                        medium: 1.75,
                        large: 2.50
                    };

                    function formatCurrency(value) {
                        return new Intl.NumberFormat('en-US', {
                            style: 'currency',
                            currency: 'USD'
                        }).format(value);
                    }

                    function updatePizzaChoiceSelection(selectedButton) {
                        pizzaChoiceButtons.forEach((button) => {
                            const isSelected = button === selectedButton;
                            button.classList.toggle('active', isSelected);
                            button.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
                        });
                    }

                    function updateSubtotal() {
                        const selectedSize = document.querySelector('input[name="size"]:checked')?.value || 'medium';
                        const selectedSplit = document.querySelector('input[name="split_type"]:checked');
                        const basePrice = productPrices[selectedSize] || productPrices.medium;
                        const toppingRate = toppingPricing[selectedSize] || toppingPricing.medium;
                        const toppingCount = Array.from(toppingCheckboxes).filter((checkbox) => checkbox.checked).length;
                        const splitMultiplier = selectedSplit && selectedSplit.value !== 'whole' ? 0.5 : 1;
                        const subtotal = (basePrice + (toppingCount * toppingRate)) * splitMultiplier;
                        subtotalEl.textContent = formatCurrency(subtotal);
                    }

                    function updateToppingPricing() {
                        const selectedSize = document.querySelector('input[name="size"]:checked')?.value || 'medium';
                        const currentPrice = toppingPricing[selectedSize] || toppingPricing.medium;
                        toppingPriceNote.textContent = `Selected size pricing: ${selectedSize === 'medium' ? '14"' : '18"'} $${currentPrice.toFixed(2)}`;
                    }

                    function updateSplitAvailability() {
                        const sizeSelected = document.querySelector('input[name="size"]:checked');
                        const selectedSplit = document.querySelector('input[name="split_type"]:checked');
                        const selectedPizzaChoice = document.querySelector('.pizza-choice-button.active');
                        const showToppings = !!sizeSelected && !!selectedSplit && !!selectedPizzaChoice;

                        splitInputs.forEach((input) => {
                            input.disabled = !sizeSelected || !selectedPizzaChoice;
                        });

                        if (!selectedPizzaChoice) {
                            toppingOptions.classList.add('hidden');
                            toppingPriceNote.textContent = 'Choose a pizza style to continue.';
                            return;
                        }

                        if (!sizeSelected) {
                            toppingOptions.classList.add('hidden');
                            toppingPriceNote.textContent = 'Choose a size first to continue.';
                            return;
                        }

                        toppingOptions.classList.toggle('hidden', !showToppings);

                        if (!selectedSplit) {
                            toppingPriceNote.textContent = 'Choose Whole, First half, or Second half to reveal toppings.';
                            return;
                        }

                        const selectedSize = sizeSelected.value;
                        const sizeLabel = selectedSize === 'medium' ? '14"' : '18"';
                        const sizePrice = toppingPricing[selectedSize] || toppingPricing.medium;

                        if (selectedSplit.value === 'whole') {
                            toppingPriceNote.textContent = `${selectedPizzaChoice.dataset.label}: Whole pie toppings ${sizeLabel} $${sizePrice.toFixed(2)}`;
                        } else {
                            toppingPriceNote.textContent = `${selectedPizzaChoice.dataset.label}: Half pie toppings ${sizeLabel} $${sizePrice.toFixed(2)} per half`;
                        }
                    }

                    pizzaChoiceButtons.forEach((button) => {
                        button.addEventListener('click', function () {
                            updatePizzaChoiceSelection(button);
                            updateSplitAvailability();
                            updateSubtotal();
                        });
                    });

                    sizeInputs.forEach((input) => {
                        input.addEventListener('change', function () {
                            updateToppingPricing();
                            updateSplitAvailability();
                            updateSubtotal();
                        });
                    });

                    splitInputs.forEach((input) => {
                        input.addEventListener('change', function () {
                            updateSplitAvailability();
                            updateSubtotal();
                        });
                    });

                    toppingCheckboxes.forEach((checkbox) => {
                        checkbox.addEventListener('change', updateSubtotal);
                    });

                    updatePizzaChoiceSelection(document.querySelector('.pizza-choice-button.active'));
                    updateToppingPricing();
                    updateSplitAvailability();
                    updateSubtotal();
                });
            </script>
        </div>
    </div>
@endsection

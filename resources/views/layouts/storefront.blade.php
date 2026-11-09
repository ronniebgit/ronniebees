<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#120d0c] text-white antialiased">
        <header class="border-b border-[#3a1d1b] bg-[#0d0908] text-white shadow-lg shadow-[#000]/30">
            <div class="border-b border-[#31201b] bg-[#140f0e]">
                <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-[#f6d9a0] sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                    <div class="flex flex-wrap items-center gap-4">
                        <span>Ronnie Bee's NY Pizza</span>
                        <span class="text-[#d1b785]">•</span>
                        <span>Fresh slices • Pickup & delivery</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-4 text-[#f4e2c5]">
                        <a href="tel:5551234567" class="hover:text-white">(555) 123-4567</a>
                        <span class="text-[#d1b785]">•</span>
                        <span>11 W. Main St, New York, NY</span>
                    </div>
                </div>
            </div>

            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-full bg-[#b7181f] text-lg font-black text-white shadow-lg shadow-red-950/60">R</span>
                    <div>
                        <div class="brand-word text-2xl leading-none text-[#ffd54f]">RONNIE</div>
                        <div class="text-[10px] uppercase tracking-[0.3em] text-[#f4e2c5]">Bee's</div>
                    </div>
                </a>

                <nav class="hidden items-center gap-8 text-sm font-semibold md:flex">
                    <a href="{{ route('home') }}" class="text-[#f4e2c5] hover:text-white">Home</a>
                    <a href="{{ route('menu') }}" class="text-[#f4e2c5] hover:text-white">Menu</a>
                    <a href="{{ route('gift-card') }}" class="text-[#f4e2c5] hover:text-white">Gift card</a>
                    <a href="{{ route('checkout') }}" class="text-[#f4e2c5] hover:text-white">Checkout</a>
                </nav>

                <a href="{{ route('cart') }}" class="inline-flex items-center gap-2 rounded-full border border-[#ffd54f] bg-[#b7181f] px-4 py-2 text-sm font-bold text-white hover:bg-[#8c1117]">
                    Cart
                    <span class="rounded-full bg-white px-2 py-0.5 text-xs font-bold text-[#b7181f]">
                        {{ collect(session('cart', []))->sum('quantity') }}
                    </span>
                </a>
            </div>
        </header>

        @if (session('success'))
            <div class="mx-auto mt-6 max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mx-auto mt-6 max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            @yield('content')
        </main>

        <footer class="border-t border-[#3a1d1b] bg-[#0d0908] text-[#f4e2c5]">
            <div class="mx-auto flex max-w-7xl flex-col gap-5 px-4 py-8 text-sm text-[#f4e2c5] sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                <div>
                    <div class="brand-word text-3xl text-[#ffd54f]">Ronnie Bee's</div>
                    <p class="mt-2 text-[#f0d8a7]">Fresh pizza. Fast pickup. Easy ordering.</p>
                </div>
                <div class="flex flex-wrap items-center gap-4">
                    <a href="{{ route('menu') }}" class="text-[#f4e2c5] hover:text-white">Menu</a>
                    <a href="{{ route('cart') }}" class="text-[#f4e2c5] hover:text-white">Cart</a>
                    <a href="{{ route('checkout') }}" class="text-[#f4e2c5] hover:text-white">Checkout</a>
                </div>
            </div>
        </footer>
    </body>
</html>

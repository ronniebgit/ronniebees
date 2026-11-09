<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ronnie Bee's Gift Cards</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-stone-100 min-h-screen text-stone-900">
        <div class="mx-auto max-w-6xl px-4 py-8">
            <header class="mb-8 flex flex-col gap-4 rounded-3xl bg-stone-900 px-6 py-5 text-white md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.2em] text-orange-300">Ronnie Bee's</p>
                    <h1 class="mt-1 text-2xl font-black">Gift cards</h1>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="rounded-full border border-white/20 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">Dashboard</a>
                    <a href="{{ route('admin.orders.index') }}" class="rounded-full border border-white/20 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">Orders</a>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="rounded-full bg-orange-500 px-4 py-2 text-sm font-bold text-white hover:bg-orange-600">Log out</button>
                    </form>
                </div>
            </header>

            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-8 grid gap-6 lg:grid-cols-[1fr_1.2fr]">
                <section class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-black text-stone-900">{{ $editingCard ? 'Edit gift card' : 'Create a gift card' }}</h2>

                    <form method="POST" action="{{ $editingCard ? route('admin.gift-cards.update', $editingCard) : route('admin.gift-cards.store') }}" class="mt-6 space-y-5">
                        @csrf
                        @if ($editingCard)
                            @method('PUT')
                        @endif

                        <div>
                            <label for="code" class="mb-2 block text-xs font-bold uppercase tracking-[0.2em] text-stone-600">Code</label>
                            <input id="code" name="code" type="text" value="{{ $editingCard ? $editingCard->code : old('code') }}" required class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-stone-700" placeholder="RONNIE25">
                        </div>

                        <div>
                            <label for="balance" class="mb-2 block text-xs font-bold uppercase tracking-[0.2em] text-stone-600">Balance</label>
                            <input id="balance" name="balance" type="number" step="0.01" min="0" value="{{ $editingCard ? $editingCard->balance : old('balance') }}" required class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-stone-700" placeholder="25.00">
                        </div>

                        <label class="flex items-center gap-3 rounded-2xl border border-stone-200 bg-stone-50 p-3">
                            <input type="checkbox" name="is_active" value="1" {{ ($editingCard ? $editingCard->is_active : old('is_active', true)) ? 'checked' : '' }} class="h-4 w-4 rounded border-stone-300 text-orange-600 focus:ring-orange-500">
                            <span class="text-sm font-semibold text-stone-700">Active</span>
                        </label>

                        <div class="flex gap-3">
                            <button type="submit" class="rounded-full bg-orange-500 px-5 py-3 text-sm font-black text-white hover:bg-orange-600">
                                {{ $editingCard ? 'Update card' : 'Create card' }}
                            </button>
                            @if ($editingCard)
                                <a href="{{ route('admin.gift-cards.index') }}" class="rounded-full border border-stone-300 bg-white px-5 py-3 text-sm font-bold text-stone-700">Cancel</a>
                            @endif
                        </div>
                    </form>
                </section>

                <section class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-black text-stone-900">Current cards</h2>

                    <div class="mt-5 space-y-3">
                        @forelse ($cards as $card)
                            <div class="flex items-center justify-between gap-4 rounded-2xl border border-stone-200 bg-stone-50 p-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg font-black text-stone-900">{{ $card->code }}</span>
                                        @if ($card->is_active)
                                            <span class="rounded-full bg-green-100 px-2 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-green-700">Active</span>
                                        @else
                                            <span class="rounded-full bg-stone-200 px-2 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-stone-600">Inactive</span>
                                        @endif
                                    </div>
                                    <div class="mt-1 text-sm text-stone-600">Balance: ${{ number_format($card->balance, 2) }}</div>
                                </div>

                                <a href="{{ route('admin.gift-cards.index', ['edit' => $card->id]) }}" class="rounded-full bg-stone-900 px-4 py-2 text-xs font-bold text-white">Edit</a>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-stone-300 bg-stone-50 p-6 text-center text-sm text-stone-500">
                                No gift cards have been created yet.
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </body>
</html>

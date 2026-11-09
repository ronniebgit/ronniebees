<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ronnie Bee's Admin</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-stone-100 min-h-screen text-stone-900">
        <div class="mx-auto max-w-7xl px-4 py-8">
            <header class="mb-8 flex flex-col gap-4 rounded-3xl bg-stone-900 px-6 py-5 text-white md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.2em] text-orange-300">Ronnie Bee's</p>
                    <h1 class="mt-1 text-2xl font-black">Admin dashboard</h1>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="rounded-full border border-white/20 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">View storefront</a>
                    <a href="{{ route('admin.orders.index') }}" class="rounded-full bg-orange-500 px-4 py-2 text-sm font-bold text-white hover:bg-orange-600">Orders</a>
                    <a href="{{ route('admin.gift-cards.index') }}" class="rounded-full border border-white/20 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">Gift cards</a>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="rounded-full bg-orange-500 px-4 py-2 text-sm font-bold text-white hover:bg-orange-600">Log out</button>
                    </form>
                </div>
            </header>

            <section class="grid gap-4 md:grid-cols-4">
                <div class="rounded-2xl bg-white p-5 shadow-sm border border-stone-200">
                    <p class="text-sm text-stone-500">Revenue</p>
                    <p class="mt-3 text-3xl font-black">${{ number_format($stats['revenue'], 2) }}</p>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-sm border border-stone-200">
                    <p class="text-sm text-stone-500">Today's orders</p>
                    <p class="mt-3 text-3xl font-black">{{ $stats['today_orders'] }}</p>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-sm border border-stone-200">
                    <p class="text-sm text-stone-500">Pending</p>
                    <p class="mt-3 text-3xl font-black">{{ $stats['pending'] }}</p>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-sm border border-stone-200">
                    <p class="text-sm text-stone-500">Confirmed</p>
                    <p class="mt-3 text-3xl font-black">{{ $stats['completed'] }}</p>
                </div>
            </section>

            <section class="mt-8 rounded-3xl bg-white p-6 shadow-sm border border-stone-200">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-xl font-black">Recent orders</h2>
                    <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-bold uppercase tracking-[0.12em] text-orange-700">Live</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b border-stone-200 text-stone-600">
                            <tr>
                                <th class="pb-3 pr-4 font-semibold">Customer</th>
                                <th class="pb-3 pr-4 font-semibold">Type</th>
                                <th class="pb-3 pr-4 font-semibold">Total</th>
                                <th class="pb-3 pr-4 font-semibold">Status</th>
                                <th class="pb-3 font-semibold">Items</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr class="border-b border-stone-100 align-top">
                                    <td class="py-3 pr-4 font-semibold">{{ $order->customer_name }}</td>
                                    <td class="py-3 pr-4 capitalize">{{ $order->delivery_type }}</td>
                                    <td class="py-3 pr-4 font-bold">${{ number_format($order->total, 2) }}</td>
                                    <td class="py-3 pr-4">
                                        <span class="inline-block rounded-full bg-stone-100 px-2 py-1 text-xs font-bold uppercase tracking-[0.1em] text-stone-700">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <ul class="space-y-1">
                                            @foreach ($order->items as $item)
                                                <li>{{ $item->product_name }} x{{ $item->quantity }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-stone-500">No orders yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </body>
</html>

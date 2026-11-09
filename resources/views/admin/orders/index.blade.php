<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ronnie Bee's Orders</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-stone-100 min-h-screen text-stone-900">
        <div class="mx-auto max-w-7xl px-4 py-8">
            <header class="mb-8 flex flex-col gap-4 rounded-3xl bg-stone-900 px-6 py-5 text-white md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.2em] text-orange-300">Ronnie Bee's</p>
                    <h1 class="mt-1 text-2xl font-black">Order queue</h1>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="rounded-full border border-white/20 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">Dashboard</a>
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

            <div class="overflow-hidden rounded-3xl border border-stone-200 bg-white shadow-sm">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-stone-50 text-stone-700">
                        <tr>
                            <th class="px-5 py-4 font-semibold">Customer</th>
                            <th class="px-5 py-4 font-semibold">Type</th>
                            <th class="px-5 py-4 font-semibold">Total</th>
                            <th class="px-5 py-4 font-semibold">Status</th>
                            <th class="px-5 py-4 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr class="border-t border-stone-200">
                                <td class="px-5 py-4">
                                    <div class="font-bold">{{ $order->customer_name }}</div>
                                    <div class="text-xs text-stone-500">{{ $order->customer_phone }}</div>
                                </td>
                                <td class="px-5 py-4 capitalize">{{ $order->delivery_type }}</td>
                                <td class="px-5 py-4 font-bold">${{ number_format($order->total, 2) }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full bg-stone-100 px-3 py-1 text-xs font-bold uppercase tracking-[0.12em] text-stone-700">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="rounded-full bg-stone-900 px-3 py-2 text-xs font-bold text-white">View</a>
                                        <form method="POST" action="{{ route('admin.orders.status.update', $order) }}">
                                            @csrf
                                            <select name="status" onchange="this.form.submit()" class="rounded-full border border-stone-300 bg-white px-3 py-2 text-xs font-bold text-stone-700">
                                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="in-progress" {{ $order->status === 'in-progress' ? 'selected' : '' }}>In progress</option>
                                                <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                                <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Ready</option>
                                                <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-stone-500">No orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        </div>
    </body>
</html>

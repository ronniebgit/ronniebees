<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Order #{{ $order->id }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-stone-100 min-h-screen text-stone-900">
        <div class="mx-auto max-w-5xl px-4 py-8">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.2em] text-orange-600">Ronnie Bee's</p>
                    <h1 class="mt-2 text-3xl font-black">Order #{{ $order->id }}</h1>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="rounded-full bg-stone-900 px-4 py-2 text-sm font-bold text-white">Back to orders</a>
            </div>

            <div class="grid gap-6 md:grid-cols-[1.1fr_0.9fr]">
                <div class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-black">Customer</h2>
                    <div class="mt-4 space-y-2 text-sm text-stone-700">
                        <p><span class="font-bold">Name:</span> {{ $order->customer_name }}</p>
                        <p><span class="font-bold">Phone:</span> {{ $order->customer_phone }}</p>
                        <p><span class="font-bold">Email:</span> {{ $order->customer_email ?? 'Not provided' }}</p>
                        <p><span class="font-bold">Type:</span> {{ ucfirst($order->delivery_type) }}</p>
                        <p><span class="font-bold">Address:</span> {{ $order->delivery_address ?? 'Pickup order' }}</p>
                    </div>

                    <div class="mt-8">
                        <h2 class="text-xl font-black">Items</h2>
                        <div class="mt-4 space-y-4">
                            @foreach ($order->items as $item)
                                <div class="rounded-2xl border border-stone-200 p-4">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <div class="font-black text-stone-900">{{ $item->product_name }}</div>
                                            <div class="mt-1 text-xs uppercase tracking-[0.18em] text-orange-600">{{ ucfirst($item->size) }} · {{ str_replace('-', ' ', $item->split_type) }}</div>
                                            @if ($item->special_instructions)
                                                <div class="mt-2 text-xs text-stone-500">Note: {{ $item->special_instructions }}</div>
                                            @endif
                                        </div>
                                        <div class="text-sm font-bold text-stone-900">${{ number_format($item->total_price, 2) }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-black">Status</h2>

                    <form method="POST" action="{{ route('admin.orders.status.update', $order) }}" class="mt-5 space-y-4">
                        @csrf
                        <select name="status" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm font-bold text-stone-700">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in-progress" {{ $order->status === 'in-progress' ? 'selected' : '' }}>In progress</option>
                            <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Ready</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        <button type="submit" class="w-full rounded-full bg-orange-500 px-4 py-3 font-bold text-white hover:bg-orange-600">Update status</button>
                    </form>

                    <div class="mt-8 border-t border-stone-200 pt-5">
                        <div class="flex items-center justify-between text-sm text-stone-600">
                            <span>Order total</span>
                            <span class="font-bold text-stone-900">${{ number_format($order->total, 2) }}</span>
                        </div>
                        <div class="mt-3 flex items-center justify-between text-sm text-stone-600">
                            <span>Payment method</span>
                            <span class="font-bold text-stone-900 uppercase tracking-[0.12em]">{{ str_replace('_', ' ', ucfirst($order->payment_method ?? 'card')) }}</span>
                        </div>
                        <div class="mt-3 flex items-center justify-between text-sm text-stone-600">
                            <span>Payment status</span>
                            <span class="font-bold text-stone-900 capitalize">{{ $order->payment_status }}</span>
                        </div>
                        <div class="mt-3 flex items-center justify-between text-sm text-stone-600">
                            <span>Placed</span>
                            <span class="font-bold text-stone-900">{{ $order->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

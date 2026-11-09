@extends('layouts.storefront')

@section('content')
    <section class="py-12">
        <div class="mx-auto max-w-3xl rounded-[2rem] border border-orange-200 bg-white p-8 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600">Ronnie Bee's</p>
            <h1 class="mt-3 text-4xl font-black text-stone-900">Check your gift card balance</h1>
            <p class="mt-3 text-stone-600">Enter a gift card code to see the available balance and use it during checkout.</p>

            <form method="POST" action="{{ route('gift-card.lookup') }}" class="mt-8 space-y-5">
                @csrf
                <div>
                    <label for="code" class="mb-2 block text-xs font-bold uppercase tracking-[0.2em] text-stone-600">Gift card code</label>
                    <input id="code" name="code" type="text" value="{{ old('code', $code ?? '') }}" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-stone-700" placeholder="RONNIE25" required>
                </div>
                <button type="submit" class="w-full rounded-full bg-orange-500 px-6 py-3 text-base font-black text-white shadow-lg hover:bg-orange-600">
                    Check balance
                </button>
            </form>

            @if ($giftCard)
                <div class="mt-8 rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                    <div class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-700">Balance available</div>
                    <div class="mt-3 text-3xl font-black text-emerald-900">${{ number_format($giftCard->balance, 2) }}</div>
                    <div class="mt-2 text-sm text-emerald-700">Code: <span class="font-bold">{{ $giftCard->code }}</span></div>
                </div>
            @elseif ($code)
                <div class="mt-8 rounded-2xl border border-red-200 bg-red-50 p-5 text-red-700">
                    We could not find that gift card code or it is inactive.
                </div>
            @endif
        </div>
    </section>
@endsection

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ronnie Bee's Admin Login</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-stone-100 min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-md rounded-3xl bg-white shadow-xl border border-stone-200 p-8">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-orange-500 text-white text-2xl font-black">RB</div>
                <h1 class="mt-4 text-2xl font-black text-stone-900">Ronnie Bee's</h1>
                <p class="mt-2 text-sm text-stone-500">Admin portal</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.authenticate') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-stone-700 mb-2">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="w-full rounded-xl border border-stone-300 bg-stone-50 px-4 py-3 text-stone-900 focus:border-orange-500 focus:outline-none">
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-stone-700 mb-2">Password</label>
                    <input id="password" name="password" type="password" required class="w-full rounded-xl border border-stone-300 bg-stone-50 px-4 py-3 text-stone-900 focus:border-orange-500 focus:outline-none">
                </div>

                <button type="submit" class="w-full rounded-xl bg-orange-500 px-4 py-3 font-bold text-white hover:bg-orange-600 transition">
                    Sign in
                </button>
            </form>

            <div class="mt-5 text-center text-xs text-stone-500">
                Default admin: admin@ronniebees.com / ronniebee123
            </div>
        </div>
    </body>
</html>

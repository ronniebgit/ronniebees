<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ronnie Bee's Categories</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-stone-100 min-h-screen text-stone-900">
        <div class="mx-auto max-w-7xl px-4 py-8">
            <header class="mb-8 flex flex-col gap-4 rounded-3xl bg-stone-900 px-6 py-5 text-white md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.2em] text-orange-300">Ronnie Bee's</p>
                    <h1 class="mt-1 text-2xl font-black">Category manager</h1>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="rounded-full border border-white/20 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">Dashboard</a>
                    <a href="{{ route('admin.products.index') }}" class="rounded-full border border-white/20 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">Products</a>
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

            <div class="grid gap-6 lg:grid-cols-[1.1fr_1.9fr]">
                <section class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-black">{{ $editingCategory ? 'Edit category' : 'Add category' }}</h2>

                    <form method="POST" action="{{ $editingCategory ? route('admin.categories.update', $editingCategory) : route('admin.categories.store') }}" class="mt-5 space-y-4">
                        @csrf
                        @if ($editingCategory)
                            @method('PUT')
                        @endif

                        <div>
                            <label class="mb-1 block text-sm font-semibold text-stone-700">Name</label>
                            <input name="name" value="{{ old('name', $editingCategory->name ?? '') }}" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm text-stone-700" required>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-semibold text-stone-700">Slug</label>
                            <input name="slug" value="{{ old('slug', $editingCategory->slug ?? '') }}" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm text-stone-700">
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-semibold text-stone-700">Description</label>
                            <textarea name="description" rows="4" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm text-stone-700">{{ old('description', $editingCategory->description ?? '') }}</textarea>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-stone-700">Display order</label>
                                <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $editingCategory->sort_order ?? $categories->count() + 1) }}" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm text-stone-700">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-stone-700">Visible</label>
                                <select name="active" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm font-bold text-stone-700">
                                    <option value="1" {{ old('active', $editingCategory->active ?? true) ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ ! old('active', $editingCategory->active ?? true) ? 'selected' : '' }}>Hidden</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <button type="submit" class="rounded-full bg-orange-500 px-5 py-3 text-sm font-bold text-white hover:bg-orange-600">{{ $editingCategory ? 'Save changes' : 'Create category' }}</button>
                            @if ($editingCategory)
                                <a href="{{ route('admin.categories.index') }}" class="rounded-full border border-stone-300 bg-white px-5 py-3 text-sm font-bold text-stone-700">Cancel</a>
                            @endif
                        </div>
                    </form>
                </section>

                <section class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                    <div class="mb-5 flex items-center justify-between">
                        <h2 class="text-xl font-black">Categories</h2>
                        <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-bold uppercase tracking-[0.12em] text-orange-700">{{ $categories->count() }} live</span>
                    </div>

                    <div class="space-y-4">
                        @forelse ($categories as $category)
                            <div class="rounded-2xl border border-stone-200 p-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="font-black text-stone-900">{{ $category->name }}</div>
                                        <div class="mt-1 text-xs uppercase tracking-[0.15em] text-stone-500">Order #{{ $category->sort_order }}</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.categories.index', ['edit' => $category->id]) }}" class="rounded-full bg-stone-900 px-3 py-2 text-xs font-bold text-white">Edit</a>
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Remove this category?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-full border border-red-200 bg-red-50 px-3 py-2 text-xs font-bold text-red-700">Delete</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="mt-3 rounded-xl bg-stone-50 p-2 text-xs text-stone-600">
                                    Slug: {{ $category->slug }}
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-stone-300 bg-stone-50 p-8 text-center text-stone-500">No categories yet.</div>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </body>
</html>

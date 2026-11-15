<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ronnie Bee's Products</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-stone-100 min-h-screen text-stone-900">
        <div class="mx-auto max-w-7xl px-4 py-8">
            <header class="mb-8 flex flex-col gap-4 rounded-3xl bg-stone-900 px-6 py-5 text-white md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.2em] text-orange-300">Ronnie Bee's</p>
                    <h1 class="mt-1 text-2xl font-black">Product manager</h1>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="rounded-full border border-white/20 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">Dashboard</a>
                    <a href="{{ route('admin.categories.index') }}" class="rounded-full border border-white/20 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">Categories</a>
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

            <div class="grid gap-6 lg:grid-cols-[1.2fr_1.8fr]">
                <section class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-black">{{ $editingProduct ? 'Edit product' : 'Add product' }}</h2>

                    <form method="POST" action="{{ $editingProduct ? route('admin.products.update', $editingProduct) : route('admin.products.store') }}" class="mt-5 space-y-4">
                        @csrf
                        @if ($editingProduct)
                            @method('PUT')
                        @endif

                        <div>
                            <label class="mb-1 block text-sm font-semibold text-stone-700">Category</label>
                            <select name="category_id" id="product-category" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm text-stone-700" required>
                                <option value="">Select category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" data-slug="{{ strtolower($category->slug) }}" {{ old('category_id', $editingProduct->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-semibold text-stone-700">Name</label>
                            <input name="name" value="{{ old('name', $editingProduct->name ?? '') }}" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm text-stone-700" required>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-semibold text-stone-700">Slug</label>
                            <input name="slug" value="{{ old('slug', $editingProduct->slug ?? '') }}" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm text-stone-700">
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-semibold text-stone-700">Description</label>
                            <textarea name="description" rows="4" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm text-stone-700">{{ old('description', $editingProduct->description ?? '') }}</textarea>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-stone-700" id="base-price-label">Base price</label>
                                <input type="number" step="0.01" min="0" name="base_price" value="{{ old('base_price', $editingProduct->base_price ?? 0) }}" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm text-stone-700">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-stone-700">Display order</label>
                                <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $editingProduct->sort_order ?? $products->count() + 1) }}" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm text-stone-700">
                            </div>
                        </div>

                        <div class="size-price-group grid gap-4 sm:grid-cols-3">
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-stone-700">Small</label>
                                <input type="number" step="0.01" min="0" name="small_price" value="{{ old('small_price', $editingProduct->small_price ?? 0) }}" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm text-stone-700">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-stone-700">Medium</label>
                                <input type="number" step="0.01" min="0" name="medium_price" value="{{ old('medium_price', $editingProduct->medium_price ?? 0) }}" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm text-stone-700">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-stone-700">Large</label>
                                <input type="number" step="0.01" min="0" name="large_price" value="{{ old('large_price', $editingProduct->large_price ?? 0) }}" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm text-stone-700">
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-stone-700">Visible</label>
                                <select name="active" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm font-bold text-stone-700">
                                    <option value="1" {{ old('active', $editingProduct->active ?? true) ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ ! old('active', $editingProduct->active ?? true) ? 'selected' : '' }}>Hidden</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-stone-700">Featured</label>
                                <select name="featured" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm font-bold text-stone-700">
                                    <option value="1" {{ old('featured', $editingProduct->featured ?? false) ? 'selected' : '' }}>Featured</option>
                                    <option value="0" {{ ! old('featured', $editingProduct->featured ?? false) ? 'selected' : '' }}>Not featured</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-semibold text-stone-700">Image URL</label>
                            <input name="image" value="{{ old('image', $editingProduct->image ?? '') }}" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm text-stone-700" placeholder="/images/product.jpg">
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <button type="submit" class="rounded-full bg-orange-500 px-5 py-3 text-sm font-bold text-white hover:bg-orange-600">{{ $editingProduct ? 'Save changes' : 'Create product' }}</button>
                            @if ($editingProduct)
                                <a href="{{ route('admin.products.index') }}" class="rounded-full border border-stone-300 bg-white px-5 py-3 text-sm font-bold text-stone-700">Cancel</a>
                            @endif
                        </div>
                    </form>
                </section>

                <section class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <h2 class="text-xl font-black">Products</h2>
                        <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-bold uppercase tracking-[0.12em] text-orange-700">{{ $products->count() }} live</span>
                    </div>

                    <form method="GET" action="{{ route('admin.products.index') }}" class="mb-5 flex flex-col gap-3 rounded-2xl border border-stone-200 bg-stone-50 p-3 sm:flex-row sm:items-end">
                        <div class="flex-1">
                            <label for="category-filter" class="mb-1 block text-xs font-bold uppercase tracking-[0.15em] text-stone-500">Filter by category</label>
                            <select id="category-filter" name="category" class="w-full rounded-xl border border-stone-300 bg-white px-3 py-2 text-sm text-stone-700">
                                <option value="">All categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ (string) ($selectedCategoryId ?? '') === (string) $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="rounded-full bg-stone-900 px-4 py-2 text-sm font-bold text-white">Apply</button>
                        @if ($selectedCategoryId)
                            <a href="{{ route('admin.products.index') }}" class="rounded-full border border-stone-300 bg-white px-4 py-2 text-sm font-bold text-stone-700">Clear</a>
                        @endif
                    </form>

                    <div class="space-y-4">
                        @forelse ($products as $product)
                            <div class="rounded-2xl border border-stone-200 p-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="font-black text-stone-900">{{ $product->name }}</div>
                                        <div class="mt-1 text-xs uppercase tracking-[0.15em] text-stone-500">{{ $product->category?->name ?? 'Unassigned' }} · Order #{{ $product->sort_order }}</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.products.index', ['edit' => $product->id]) }}" class="rounded-full bg-stone-900 px-3 py-2 text-xs font-bold text-white">Edit</a>
                                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Remove this product?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-full border border-red-200 bg-red-50 px-3 py-2 text-xs font-bold text-red-700">Delete</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="mt-3 grid gap-2 text-xs text-stone-600 sm:grid-cols-3">
                                    <div class="rounded-xl bg-stone-50 p-2"><span class="font-bold">Base:</span> ${{ number_format($product->base_price, 2) }}</div>
                                    <div class="rounded-xl bg-stone-50 p-2"><span class="font-bold">Small:</span> ${{ number_format($product->small_price, 2) }}</div>
                                    <div class="rounded-xl bg-stone-50 p-2"><span class="font-bold">Large:</span> ${{ number_format($product->large_price, 2) }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-stone-300 bg-stone-50 p-8 text-center text-stone-500">No products yet.</div>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const categorySelect = document.getElementById('product-category');
                const sizeGroup = document.querySelector('.size-price-group');
                const basePriceLabel = document.getElementById('base-price-label');

                if (!categorySelect || !sizeGroup || !basePriceLabel) {
                    return;
                }

                const syncPriceFields = function () {
                    const selected = categorySelect.options[categorySelect.selectedIndex];
                    const slug = (selected && selected.dataset.slug) ? selected.dataset.slug.toLowerCase() : '';
                    const isDrinks = slug === 'drinks';

                    sizeGroup.style.display = isDrinks ? 'none' : 'grid';
                    basePriceLabel.textContent = isDrinks ? 'Price' : 'Base price';
                };

                categorySelect.addEventListener('change', syncPriceFields);
                syncPriceFields();
            });
        </script>
    </body>
</html>

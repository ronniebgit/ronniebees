<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'base_price',
        'small_price',
        'medium_price',
        'large_price',
        'image',
        'featured',
        'active',
        'sort_order',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeVisible($query)
    {
        return $query->where('active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function priceForSize(?string $size = 'medium'): float
    {
        $categorySlug = strtolower((string) ($this->category?->slug ?? ''));

        if ($categorySlug === 'drinks') {
            return (float) ($this->base_price ?? 0);
        }

        $size = strtolower((string) $size);

        return match ($size) {
            'small' => (float) ($this->small_price ?? $this->base_price ?? 0),
            'large' => (float) ($this->large_price ?? $this->base_price ?? 0),
            default => (float) ($this->medium_price ?? $this->base_price ?? 0),
        };
    }
}

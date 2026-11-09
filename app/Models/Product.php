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

    public function priceForSize(?string $size = 'medium'): float
    {
        $size = strtolower((string) $size);

        return match ($size) {
            'small' => (float) ($this->small_price ?? $this->base_price ?? 0),
            'large' => (float) ($this->large_price ?? $this->base_price ?? 0),
            default => (float) ($this->medium_price ?? $this->base_price ?? 0),
        };
    }
}

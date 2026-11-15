<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Topping extends Model
{
    protected $fillable = ['name', 'slug', 'active', 'sort_order', 'small_price', 'medium_price', 'large_price'];

    protected $casts = [
        'active' => 'boolean',
        'small_price' => 'decimal:2',
        'medium_price' => 'decimal:2',
        'large_price' => 'decimal:2',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true)->orderBy('sort_order')->orderBy('name');
    }

    public function priceForSize(?string $size = 'medium', ?string $portion = 'whole'): float
    {
        $size = strtolower((string) $size);
        $portion = strtolower((string) $portion);

        $wholePrice = match ($size) {
            'small' => (float) ($this->small_price ?? 1.25),
            'medium' => (float) ($this->medium_price ?? 1.75),
            'large' => (float) ($this->large_price ?? 2.50),
            default => 0.00,
        };

        if (in_array($portion, ['half', 'first-half', 'second-half', 'half-and-half'], true)) {
            return round($wholePrice / 2, 2);
        }

        return round($wholePrice, 2);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Topping extends Model
{
    protected $fillable = ['name', 'slug', 'active', 'sort_order'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true)->orderBy('sort_order');
    }

    public function priceForSize(?string $size = 'medium', ?string $portion = 'whole'): float
    {
        $size = strtolower((string) $size);
        $portion = strtolower((string) $portion);

        if ($portion === 'half') {
            return match ($size) {
                'medium' => 1.00,
                'large' => 1.50,
                default => 0.00,
            };
        }

        return match ($size) {
            'small' => 1.25,
            'medium' => 1.75,
            'large' => 2.50,
            default => 0.00,
        };
    }
}

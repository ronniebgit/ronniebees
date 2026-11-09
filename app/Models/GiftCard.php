<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiftCard extends Model
{
    protected $fillable = [
        'code',
        'balance',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'float',
        'is_active' => 'boolean',
    ];

    public static function lookup(string $code): ?self
    {
        return static::query()
            ->whereRaw('UPPER(code) = ?', [strtoupper(trim($code))])
            ->where('is_active', true)
            ->first();
    }

    public static function applyBalance(float $orderTotal, float $cardBalance): array
    {
        $orderTotal = max(0.0, (float) $orderTotal);
        $cardBalance = max(0.0, (float) $cardBalance);

        $applied = min($orderTotal, $cardBalance);

        return [
            'applied' => round($applied, 2),
            'remaining' => round(max(0.0, $orderTotal - $applied), 2),
            'balance_left' => round(max(0.0, $cardBalance - $applied), 2),
        ];
    }
}

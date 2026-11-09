<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'loyalty_points',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'loyalty_points' => 'integer',
        ];
    }

    public static function pointsForOrder(float $amount): int
    {
        return max(0, (int) floor($amount));
    }

    public static function redeemValueFromPoints(int $points): float
    {
        return round(max(0, $points) / 100, 2);
    }

    public function awardLoyaltyPoints(float $orderTotal): int
    {
        $points = self::pointsForOrder($orderTotal);
        $this->loyalty_points = (int) $this->loyalty_points + $points;
        $this->save();

        return $this->loyalty_points;
    }

    public function redeemPoints(int $pointsToRedeem): float
    {
        $pointsToRedeem = max(0, $pointsToRedeem);
        $maxRedeemable = (int) $this->loyalty_points;
        $redeemable = min($pointsToRedeem, $maxRedeemable);
        $discount = self::redeemValueFromPoints($redeemable);

        $this->loyalty_points = max(0, $maxRedeemable - $redeemable);
        $this->save();

        return $discount;
    }
}

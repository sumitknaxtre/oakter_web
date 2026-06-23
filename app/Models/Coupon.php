<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'discount_amount_paise',
        'is_active',
        'starts_at',
        'ends_at',
        'usage_limit',
        'used_count',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function formattedDiscount(): string
    {
        return '₹'.number_format($this->discount_amount_paise / 100, 2);
    }

    public function toSnapshot(): array
    {
        return [
            'coupon_id' => $this->id,
            'code' => $this->code,
            'discount_amount_paise' => $this->discount_amount_paise,
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Coupon $coupon): void {
            $coupon->code = strtoupper(trim($coupon->code));
        });
    }
}

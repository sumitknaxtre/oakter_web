<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'product_snapshot',
        'shipping_address_id',
        'shipping_snapshot',
        'billing_address_id',
        'billing_snapshot',
        'billing_same_as_shipping',
        'subtotal_paise',
        'discount_paise',
        'amount_paise',
        'coupon_id',
        'coupon_snapshot',
        'currency',
        'status',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'product_snapshot' => 'array',
            'shipping_snapshot' => 'array',
            'billing_snapshot' => 'array',
            'coupon_snapshot' => 'array',
            'billing_same_as_shipping' => 'boolean',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class, 'shipping_address_id');
    }

    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class, 'billing_address_id');
    }

    public function getProductNameAttribute(): string
    {
        return $this->product_snapshot['name'] ?? $this->product?->name ?? '';
    }

    public function getProductKeyAttribute(): string
    {
        return $this->product_snapshot['config_key'] ?? $this->product?->config_key ?? '';
    }

    public function getCustomerNameAttribute(): string
    {
        $shipping = $this->shipping_snapshot ?? [];

        return trim(($shipping['first_name'] ?? '').' '.($shipping['last_name'] ?? ''));
    }

    public function getEmailAttribute(): ?string
    {
        return $this->user?->email;
    }

    public function getPhoneAttribute(): ?string
    {
        return $this->shipping_snapshot['phone'] ?? $this->user?->phone;
    }

    public function getCouponCodeAttribute(): ?string
    {
        return $this->coupon_snapshot['code'] ?? null;
    }

    public function amountInRupees(): float
    {
        return $this->amount_paise / 100;
    }

    public function formattedAmount(): string
    {
        return $this->formatPaise($this->amount_paise);
    }

    public function formattedSubtotal(): string
    {
        return $this->formatPaise($this->subtotal_paise);
    }

    public function formattedDiscount(): string
    {
        return $this->formatPaise($this->discount_paise);
    }

    public function includedTaxAmount(): float
    {
        return round($this->amountInRupees() - ($this->amountInRupees() / 1.18), 2);
    }

    private function formatPaise(int $paise): string
    {
        return '₹'.number_format($paise / 100, 2);
    }
}

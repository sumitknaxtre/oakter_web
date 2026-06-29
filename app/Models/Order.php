<?php

namespace App\Models;

use App\Support\OrderAttribution;
use App\Support\OrderFulfillmentStatus;
use App\Support\OrderPaymentStatus;
use App\Support\UnicommerceSyncStatus;
use Illuminate\Database\Eloquent\Builder;
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
        'shipping_charges',
        'tax_amount',
        'coupon_id',
        'coupon_snapshot',
        'currency',
        'status',
        'payment_status',
        'payment_method',
        'fulfillment_status',
        'unicommerce_sale_order_code',
        'unicommerce_sync_status',
        'unicommerce_synced_at',
        'unicommerce_last_error',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'paid_at',
        'meta_event_id',
        'meta_purchase_sent_at',
        'attribution',
    ];

    protected function casts(): array
    {
        return [
            'product_snapshot' => 'array',
            'shipping_snapshot' => 'array',
            'billing_snapshot' => 'array',
            'coupon_snapshot' => 'array',
            'attribution' => 'array',
            'billing_same_as_shipping' => 'boolean',
            'paid_at' => 'datetime',
            'meta_purchase_sent_at' => 'datetime',
            'unicommerce_synced_at' => 'datetime',
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

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('payment_status', OrderPaymentStatus::Paid);
    }

    public function scopePendingPayment(Builder $query): Builder
    {
        return $query->where('payment_status', OrderPaymentStatus::Pending);
    }

    public function isPaid(): bool
    {
        return (int) $this->payment_status === OrderPaymentStatus::Paid;
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
        if ($this->tax_amount > 0) {
            return $this->tax_amount / 100;
        }

        return round($this->amountInRupees() - ($this->amountInRupees() / 1.18), 2);
    }

    public static function calculateInclusiveTaxPaise(int $amountPaise): int
    {
        return (int) round($amountPaise - ($amountPaise / 1.18));
    }

    public function formattedShippingCharges(): string
    {
        return $this->formatPaise($this->shipping_charges);
    }

    public function formattedTaxAmount(): string
    {
        return $this->formatPaise($this->tax_amount);
    }

    public function paymentStatusLabel(): string
    {
        return OrderPaymentStatus::label((int) $this->payment_status);
    }

    public function fulfillmentStatusLabel(): string
    {
        return OrderFulfillmentStatus::label((string) $this->fulfillment_status);
    }

    public function unicommerceSyncStatusLabel(): string
    {
        return UnicommerceSyncStatus::label((string) $this->unicommerce_sync_status);
    }

    public function attributionLabel(): string
    {
        return OrderAttribution::label($this->attribution);
    }

    /**
     * @return list<array{label: string, value: string}>
     */
    public function attributionDetails(): array
    {
        return OrderAttribution::detailRows($this->attribution);
    }

    private function formatPaise(int $paise): string
    {
        return '₹'.number_format($paise / 100, 2);
    }
}

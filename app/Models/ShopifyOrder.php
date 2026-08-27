<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopifyOrder extends Model
{
    protected $fillable = [
        'shopify_id',
        'order_number',
        'shopify_customer_id',
        'email',
        'financial_status',
        'paid_at',
        'fulfillment_status',
        'subtotal',
        'taxes',
        'total',
        'discount_code',
        'discount_amount',
        'shopify_created_at',
        'lineitem_name',
        'lineitem_qty',
        'lineitem_price',
        'lineitem_compare_at_price',
        'shipping_address',
        'billing_address',
        'note_attributes',
        'cancelled_at',
        'payment_method',
        'payment_reference',
        'refunded_amount',
        'tax_value',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
            'shopify_created_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'subtotal' => 'decimal:2',
            'taxes' => 'decimal:2',
            'total' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'lineitem_price' => 'decimal:2',
            'lineitem_compare_at_price' => 'decimal:2',
            'refunded_amount' => 'decimal:2',
            'tax_value' => 'decimal:2',
            'lineitem_qty' => 'integer',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(ShopifyCustomer::class, 'shopify_customer_id');
    }

    public function formattedMoney(?string $amount): string
    {
        if ($amount === null || $amount === '') {
            return '—';
        }

        return '₹'.number_format((float) $amount, 2);
    }
}

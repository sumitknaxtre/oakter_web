<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopifyCustomer extends Model
{
    protected $fillable = [
        'shopify_id',
        'name',
        'email',
        'address',
        'phones',
        'total_spent',
        'total_orders',
    ];

    protected function casts(): array
    {
        return [
            'total_spent' => 'decimal:2',
            'total_orders' => 'integer',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(ShopifyOrder::class);
    }

    public function formattedTotalSpent(): string
    {
        return '₹'.number_format((float) $this->total_spent, 2);
    }
}

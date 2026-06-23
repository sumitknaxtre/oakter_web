<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class Product extends Model
{
    protected $fillable = [
        'slug',
        'config_key',
        'name',
        'amount_paise',
        'currency',
        'catalog',
        'is_active',
        'is_in_stock',
    ];

    protected function casts(): array
    {
        return [
            'catalog' => 'array',
            'is_active' => 'boolean',
            'is_in_stock' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('is_in_stock', true);
    }

    public function isInStock(): bool
    {
        return $this->is_in_stock;
    }

    public function ensureInStock(): void
    {
        if (! $this->is_in_stock) {
            throw ValidationException::withMessages([
                'product' => ['This product is currently out of stock.'],
            ]);
        }
    }

    public function priceInRupees(): float
    {
        return $this->amount_paise / 100;
    }

    public function mrpInRupees(): float
    {
        $mrp = $this->listingMrp();

        if ($mrp === null) {
            return $this->priceInRupees();
        }

        return (float) preg_replace('/[^\d.]/', '', $mrp);
    }

    public function updatePrices(float $priceInRupees, float $mrpInRupees): void
    {
        $catalog = $this->catalog ?? [];
        $currentListingPrice = $catalog['listing']['price'] ?? null;

        $priceLabel = $this->formatPriceLabel($priceInRupees, $currentListingPrice);
        $mrpLabel = '₹'.number_format($mrpInRupees, 0);

        $catalog['listing']['price'] = $priceLabel;
        $catalog['listing']['mrp'] = $mrpLabel;

        foreach ($catalog['summary'] ?? [] as $index => $row) {
            if (($row['label'] ?? '') === 'Price') {
                $catalog['summary'][$index]['value'] = $priceLabel;
            }

            if (($row['label'] ?? '') === 'MRP') {
                $catalog['summary'][$index]['value'] = $mrpLabel;
            }
        }

        $this->update([
            'amount_paise' => (int) round($priceInRupees * 100),
            'catalog' => $catalog,
        ]);
    }

    public function toCatalogArray(): array
    {
        return array_merge($this->catalog ?? [], [
            'order_name' => $this->name,
            'amount_paise' => $this->amount_paise,
            'is_in_stock' => $this->is_in_stock,
        ]);
    }

    private function formatPriceLabel(float $rupees, ?string $currentLabel): string
    {
        $formatted = '₹'.number_format($rupees, 0);

        if ($currentLabel !== null && str_starts_with(strtolower($currentLabel), 'from ')) {
            return 'from '.$formatted;
        }

        return $formatted;
    }

    public function formattedAmount(): string
    {
        return '₹'.number_format($this->amount_paise / 100, 2);
    }

    public function listingMrp(): ?string
    {
        return $this->catalog['listing']['mrp'] ?? null;
    }

    public function listingPrice(): ?string
    {
        return $this->catalog['listing']['price'] ?? null;
    }

    public function thumbPath(): ?string
    {
        return $this->catalog['images']['thumb'] ?? null;
    }

    public function toSnapshot(): array
    {
        return [
            'product_id' => $this->id,
            'slug' => $this->slug,
            'config_key' => $this->config_key,
            'name' => $this->name,
            'amount_paise' => $this->amount_paise,
            'currency' => $this->currency,
            'catalog' => $this->catalog,
        ];
    }

    public function toCheckoutArray(): array
    {
        return array_merge($this->catalog ?? [], [
            'id' => $this->id,
            'key' => $this->config_key,
            'slug' => $this->slug,
            'order_name' => $this->name,
            'amount_paise' => $this->amount_paise,
            'is_in_stock' => $this->is_in_stock,
        ]);
    }
}

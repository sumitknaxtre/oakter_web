<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

class CouponService
{
    /**
     * @return array{subtotal_paise: int, discount_paise: int, amount_paise: int}
     */
    public function calculatePricing(Product $product, ?Coupon $coupon = null): array
    {
        $subtotalPaise = $product->amount_paise;
        $discountPaise = 0;

        if ($coupon !== null) {
            $discountPaise = min($coupon->discount_amount_paise, max(0, $subtotalPaise - 100));
        }

        return [
            'subtotal_paise' => $subtotalPaise,
            'discount_paise' => $discountPaise,
            'amount_paise' => $subtotalPaise - $discountPaise,
        ];
    }

    public function resolveForProduct(string $code, Product $product): Coupon
    {
        $coupon = Coupon::query()
            ->with('products')
            ->where('code', strtoupper(trim($code)))
            ->first();

        if ($coupon === null) {
            throw ValidationException::withMessages([
                'coupon_code' => 'This discount code is not valid.',
            ]);
        }

        $this->assertApplicable($coupon, $product);

        return $coupon;
    }

    public function assertApplicable(Coupon $coupon, Product $product): void
    {
        if (! $coupon->is_active) {
            throw ValidationException::withMessages([
                'coupon_code' => 'This discount code is not active.',
            ]);
        }

        if ($coupon->starts_at !== null && now()->lt($coupon->starts_at)) {
            throw ValidationException::withMessages([
                'coupon_code' => 'This discount code is not valid yet.',
            ]);
        }

        if ($coupon->ends_at !== null && now()->gt($coupon->ends_at)) {
            throw ValidationException::withMessages([
                'coupon_code' => 'This discount code has expired.',
            ]);
        }

        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            throw ValidationException::withMessages([
                'coupon_code' => 'This discount code has reached its usage limit.',
            ]);
        }

        if (! $coupon->products->contains('id', $product->id)) {
            throw ValidationException::withMessages([
                'coupon_code' => 'This discount code does not apply to this product.',
            ]);
        }

        $pricing = $this->calculatePricing($product, $coupon);

        if ($pricing['discount_paise'] <= 0) {
            throw ValidationException::withMessages([
                'coupon_code' => 'This discount code cannot be applied to this order.',
            ]);
        }
    }

    public function markUsed(Coupon $coupon): void
    {
        $coupon->increment('used_count');
    }
}

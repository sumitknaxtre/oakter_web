<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidatorInstance;

class AdminAbandonedOrderFilters
{
    /**
     * @return array{q: string}
     */
    public static function normalize(array $validated): array
    {
        return [
            'q' => trim((string) ($validated['q'] ?? '')),
        ];
    }

    public static function makeValidator(Request $request): ValidatorInstance
    {
        return Validator::make($request->all(), [
            'q' => ['nullable', 'string', 'max:100'],
        ]);
    }

    /**
     * @param  array{q: string}  $filters
     */
    public static function hasActiveFilters(array $filters): bool
    {
        return $filters['q'] !== '';
    }

    /**
     * @param  Builder<\App\Models\Order>  $query
     * @param  array{q: string}  $filters
     */
    public static function apply(Builder $query, array $filters): void
    {
        if ($filters['q'] === '') {
            return;
        }

        $term = '%'.$filters['q'].'%';

        $query->where(function (Builder $inner) use ($term) {
            $inner->where('razorpay_order_id', 'like', $term)
                ->orWhere('product_snapshot->name', 'like', $term)
                ->orWhere('shipping_snapshot->phone', 'like', $term)
                ->orWhereHas('user', function (Builder $userQuery) use ($term) {
                    $userQuery->where('email', 'like', $term)
                        ->orWhere('phone', 'like', $term)
                        ->orWhere('first_name', 'like', $term)
                        ->orWhere('last_name', 'like', $term)
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$term]);
                });
        });
    }
}

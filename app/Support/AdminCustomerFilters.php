<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidatorInstance;

class AdminCustomerFilters
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
     * @param  array{q: string}  $filters
     * @return array<string, string>
     */
    public static function queryParameters(array $filters): array
    {
        return array_filter([
            'q' => $filters['q'],
        ]);
    }

    /**
     * @param  Builder<\App\Models\User>  $query
     * @param  array{q: string}  $filters
     */
    public static function apply(Builder $query, array $filters): void
    {
        if ($filters['q'] === '') {
            return;
        }

        $term = '%'.$filters['q'].'%';

        $query->where(function (Builder $inner) use ($term) {
            $inner->where('email', 'like', $term)
                ->orWhere('phone', 'like', $term)
                ->orWhere('first_name', 'like', $term)
                ->orWhere('last_name', 'like', $term)
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$term]);
        });
    }
}

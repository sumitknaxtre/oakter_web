<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidatorInstance;

class AdminCustomerFilters
{
    /**
     * @return array{q: string, sort: string}
     */
    public static function normalize(array $validated): array
    {
        $sort = (string) ($validated['sort'] ?? 'newest');

        if (! in_array($sort, self::sortOptions(), true)) {
            $sort = 'newest';
        }

        return [
            'q' => trim((string) ($validated['q'] ?? '')),
            'sort' => $sort,
        ];
    }

    /**
     * @return list<string>
     */
    public static function sortOptions(): array
    {
        return [
            'newest',
            'oldest',
            'spent_desc',
            'spent_asc',
        ];
    }

    public static function sortLabel(string $sort): string
    {
        return match ($sort) {
            'oldest' => 'Oldest joined',
            'spent_desc' => 'Highest spent',
            'spent_asc' => 'Lowest spent',
            default => 'Newest joined',
        };
    }

    public static function makeValidator(Request $request): ValidatorInstance
    {
        return Validator::make($request->all(), [
            'q' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'string', 'in:'.implode(',', self::sortOptions())],
        ]);
    }

    /**
     * @param  array{q: string, sort: string}  $filters
     */
    public static function hasActiveFilters(array $filters): bool
    {
        return $filters['q'] !== '' || $filters['sort'] !== 'newest';
    }

    /**
     * @param  array{q: string, sort: string}  $filters
     * @return array<string, string>
     */
    public static function queryParameters(array $filters): array
    {
        return array_filter([
            'q' => $filters['q'],
            'sort' => $filters['sort'] !== 'newest' ? $filters['sort'] : null,
        ]);
    }

    /**
     * @param  Builder<\App\Models\User>  $query
     * @param  array{q: string, sort: string}  $filters
     */
    public static function applySort(Builder $query, array $filters): void
    {
        match ($filters['sort']) {
            'oldest' => $query->oldest(),
            'spent_desc' => $query->orderByDesc('total_spent')->orderByDesc('id'),
            'spent_asc' => $query->orderBy('total_spent')->orderBy('id'),
            default => $query->latest(),
        };
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

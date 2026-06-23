<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidatorInstance;

class AdminOrderFilters
{
    /**
     * @return array{q: string, from: string, to: string}
     */
    public static function validated(Request $request): array
    {
        $validator = self::makeValidator($request);
        $validator->validate();

        return self::normalize($validator->validated());
    }

    public static function makeValidator(Request $request): ValidatorInstance
    {
        $today = self::maxFilterDate();

        $validator = Validator::make(
            $request->all(),
            [
                'q' => ['nullable', 'string', 'max:100'],
                'from' => ['nullable', 'date', 'before_or_equal:'.$today],
                'to' => ['nullable', 'date', 'before_or_equal:'.$today],
            ],
            [
                'from.before_or_equal' => 'The from date cannot be in the future.',
                'to.before_or_equal' => 'The to date cannot be in the future.',
            ],
        );

        $validator->after(function (ValidatorInstance $validator) use ($request) {
            $from = $request->input('from');
            $to = $request->input('to');

            if ($from === null || $from === '' || $to === null || $to === '') {
                return;
            }

            if (Carbon::parse($from)->gt(Carbon::parse($to))) {
                $validator->errors()->add('from', 'The from date must be on or before the to date.');
                $validator->errors()->add('to', 'The to date must be on or after the from date.');
            }
        });

        return $validator;
    }

    /**
     * @return array{q: string, from: string, to: string}
     */
    public static function normalize(array $validated): array
    {
        return [
            'q' => trim($validated['q'] ?? ''),
            'from' => $validated['from'] ?? '',
            'to' => $validated['to'] ?? '',
        ];
    }

    public static function apply(Builder $query, array $filters): void
    {
        if ($filters['q'] !== '') {
            $search = $filters['q'];

            $query->where(function (Builder $builder) use ($search) {
                $builder
                    ->where('product_snapshot->name', 'like', "%{$search}%")
                    ->orWhere('razorpay_payment_id', 'like', "%{$search}%")
                    ->orWhere('shipping_snapshot->phone', 'like', "%{$search}%")
                    ->orWhere('shipping_snapshot->city', 'like', "%{$search}%")
                    ->orWhere('shipping_snapshot->pincode', 'like', "%{$search}%")
                    ->orWhereHas('user', function (Builder $userQuery) use ($search) {
                        $userQuery
                            ->where('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($filters['from'] !== '') {
            $query->where(
                'created_at',
                '>=',
                Carbon::parse($filters['from'])->startOfDay(),
            );
        }

        if ($filters['to'] !== '') {
            $query->where(
                'created_at',
                '<=',
                Carbon::parse($filters['to'])->endOfDay(),
            );
        }
    }

    public static function hasActiveFilters(array $filters): bool
    {
        return $filters['q'] !== ''
            || $filters['from'] !== ''
            || $filters['to'] !== '';
    }

    /**
     * @return array<string, string>
     */
    public static function queryParameters(array $filters): array
    {
        return array_filter($filters, fn (string $value) => $value !== '');
    }

    public static function maxFilterDate(): string
    {
        return now()->format('Y-m-d');
    }
}

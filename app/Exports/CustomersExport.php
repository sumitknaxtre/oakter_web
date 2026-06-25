<?php

namespace App\Exports;

use App\Models\User;

class CustomersExport
{
    /**
     * @return list<string>
     */
    public static function headers(): array
    {
        return [
            'Customer ID',
            'Name',
            'Email',
            'Phone',
            'Successful orders',
            'Abandoned orders',
            'Total spent (INR)',
            'Joined date',
        ];
    }

    /**
     * @return list<int|float|string|null>
     */
    public static function row(User $customer): array
    {
        return [
            $customer->id,
            $customer->name,
            $customer->email,
            $customer->phone,
            $customer->total_orders,
            $customer->abandoned_orders_count ?? 0,
            number_format($customer->total_spent / 100, 2, '.', ''),
            $customer->created_at?->format('d M Y'),
        ];
    }
}

<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;

class CheckoutLookupService
{
    /**
     * @return array{found: bool, user?: array<string, mixed>, shipping_address?: array<string, mixed>}
     */
    public function lookupByEmail(string $email): array
    {
        $email = strtolower(trim($email));

        $user = User::query()
            ->where('email', $email)
            ->whereHas('role', fn ($query) => $query->where('name', Role::CUSTOMER))
            ->first();

        if ($user === null) {
            return ['found' => false];
        }

        $address = $user->addresses()
            ->orderByDesc('last_used_at')
            ->orderByDesc('is_default')
            ->orderByDesc('updated_at')
            ->first();

        return [
            'found' => true,
            'user' => [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone' => $user->phone,
            ],
            'shipping_address' => $address?->toFormArray(),
        ];
    }
}

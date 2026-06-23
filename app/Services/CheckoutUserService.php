<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;

class CheckoutUserService
{
    public function resolveFromCheckout(array $validated): User
    {
        $customerRole = Role::query()->where('name', Role::CUSTOMER)->firstOrFail();
        $email = strtolower($validated['email']);

        $user = User::query()->where('email', $email)->first();

        if ($user === null) {
            return User::query()->create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $email,
                'phone' => $validated['phone'],
                'password' => Str::password(40),
                'role_id' => $customerRole->id,
            ]);
        }

        $user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'],
        ]);

        return $user;
    }
}

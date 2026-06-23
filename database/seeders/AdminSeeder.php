<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::query()->where('name', Role::ADMIN)->firstOrFail();
        $adminName = env('ADMIN_NAME', 'Oakter Admin');
        [$firstName, $lastName] = array_pad(explode(' ', $adminName, 2), 2, '');

        User::query()->updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@oakter.com')],
            [
                'first_name' => $firstName !== '' ? $firstName : 'Oakter',
                'last_name' => $lastName,
                'password' => env('ADMIN_PASSWORD', 'Oakter@123'),
                'role_id' => $adminRole->id,
            ],
        );
    }
}

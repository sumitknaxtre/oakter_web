<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::query()->upsert([
            ['id' => 1, 'name' => Role::ADMIN, 'label' => 'Admin'],
            ['id' => 2, 'name' => Role::CUSTOMER, 'label' => 'Customer'],
        ], ['id'], ['name', 'label']);
    }
}

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
            ['id' => 3, 'name' => Role::SUB_ADMIN, 'label' => 'Sub Admin'],
        ], ['id'], ['name', 'label']);
    }
}

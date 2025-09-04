<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class TenantRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['role' => 'Proprietário'],
            ['role' => 'Funcionário'],
            ['role' => 'Cliente'],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate($roleData);
        }
    }
}

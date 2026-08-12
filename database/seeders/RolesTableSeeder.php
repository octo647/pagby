<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    // Verifica se a tabela de roles está vazia antes de inserir os dados
    if (Role::count() === 0) {
    // Insere os papéis padrão
        $roles = ['Proprietário', 'Funcionário', 'Cliente'];
    foreach ($roles as $role) {
        \App\Models\Role::firstOrCreate(['role' => $role]);
    }
    }
}
}

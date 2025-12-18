<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;
use App\Models\Role;
use App\Models\User;

class RoleUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       // Pegue o role_id de Cliente e Funcionário
        $clienteRoleId = Role::where('role', 'Cliente')->first()->id;      
        $funcionarioRoleId = Role::where('role', 'Funcionário')->first()->id;
       //Designa um usuário com o papel de proprietário
       $proprietarioRoleId = Role::where('role', 'Proprietário')->first()->id;
       $usuarioProprietario = User::find(1);
       if ($usuarioProprietario) {
           // Remove todos os papéis e atribui apenas o de proprietário
           $usuarioProprietario->roles()->sync([$proprietarioRoleId]);
       }
       
       
       // Para cada filial
        foreach (Branch::all() as $branch) {
            // Pegue 3 usuários aleatórios que são clientes e ainda não são funcionários
            $clientes = User::whereHas('roles', function($q) use ($clienteRoleId) {
                    $q->where('role_id', $clienteRoleId);
                })
                ->whereDoesntHave('roles', function($q) use ($funcionarioRoleId) {
                    $q->where('role_id', $funcionarioRoleId);
                })
                ->inRandomOrder()
                ->limit(3)
                ->get();
                
                
                

            foreach ($clientes as $cliente) {
                // Dá o papel de funcionário
                $cliente->roles()->attach($funcionarioRoleId);

                // Relaciona o funcionário à filial
                DB::table('branch_user')->insert([
                    'user_id' => $cliente->id,
                    'branch_id' => $branch->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}

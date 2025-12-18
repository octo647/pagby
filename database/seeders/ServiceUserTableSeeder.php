<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\Service;
use Illuminate\Support\Facades\DB;


class ServiceUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        // Pega os IDs de todos os serviços
        // Isso é necessário para garantir que os serviços existam antes de tentar associá-los
        // e para evitar erros de chave estrangeira ao associar serviços a usuários
        // Isso também garante que os serviços sejam criados antes de tentar associá-los aos usuários
        $services_ids = Service::all()->pluck('id')->toArray();
        // Pegue o role_id do papel "Funcionário"
        $funcionarioId = \App\Models\Role::where('role', 'Funcionário')->first() ?->id; 
     
        // Pegue os ids dos usuários que têm o papel de Funcionário na tabela role_user
        $employeeIds = DB::table('role_user')
        ->where('role_id', $funcionarioId)

        ->pluck('user_id')
        ->toArray();
    
        // Associa os usuários funcionários aos serviços        
        foreach ($employeeIds as $employeeId) {
            $random_services = array_rand(array_flip($services_ids), rand(3, 6));
         
            foreach ($random_services as $service_id) {
                // Verifica se o usuário já está associado ao serviço
                $exists = DB::table('service_user')
                    ->where('user_id', $employeeId)
                    ->where('service_id', $service_id)
                    ->exists();
                   
                
                // Se não estiver associado, faz a associação
                if (!$exists) {
                    DB::table('service_user')->insert([
                        'user_id' => $employeeId,
                        'service_id' => $service_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
        
        
    }
}

<?php

namespace Database\Seeders;

use App\Livewire\RoleUser;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\AppointmentsTableSeeder;
use Database\Seeders\RolesTableSeeder;
use Database\Seeders\ServiceTableSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Cria os papéis
        $this->call([
        RolesTableSeeder::class, // Cria os papéis primeiro
        ]);
        // 2. Cria os usuários (agora já pode associar roles)
        \App\Models\User::factory(1)->create();
        // 3. Popula as demais tabelas que dependem dos usuários
       // $this->call([            
       // ServiceTableSeeder::class,  // Depois os serviços
       // BranchesTableSeeder::class, // Depois 3 filiais,
       // RoleUserTableSeeder::class, // Depois 3 funcionários por filial (que dependem dos usuários e filiais criados anteriormente)
       // ServiceUserTableSeeder::class, // Depois associa os serviços aos funcionários (que dependem dos serviços e usuários criados anteriormente)
       // SchedulesTableSeeder::class, // Depois cria os horários dos funcionários (que dependem dos usuários e filiais criados anteriormente)     
       // AppointmentsTableSeeder::class, // Por último, os agendamentos (que dependem dos anteriores)
        //AvaliacoesTableSeeder::class, // Por último, as avaliações (que dependem dos usuários e filiais criados anteriormente)
        //]);
    

    }  
       
}

<?php

namespace Database\Seeders;

use App\Livewire\RoleUser;
use App\Models\Caixa;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\User;
use Database\Factories\EstoqueFactory;
use Database\Seeders\AppointmentsTableSeeder;
use Database\Seeders\RolesTableSeeder;
use Database\Seeders\ServiceTableSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Factories\BranchFactory;

class SeederMaster extends Seeder
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
    \App\Models\User::factory(1500)->create();
    // Cria 3 filiais usando a factory
    \App\Models\Branch::factory()->count(3)->create();
        // 3. Popula as demais tabelas que dependem dos usuários
    $this->call([
     ServiceTableSeeder::class,  // Depois os serviços
     RoleUserTableSeeder::class, // Depois 3 funcionários por filial (que dependem dos usuários e filiais criados anteriormente)
     ServiceUserTableSeeder::class, // Depois associa os serviços aos funcionários (que dependem dos serviços e usuários criados anteriormente)
     SchedulesTableSeeder::class, // Depois cria os horários dos funcionários (que dependem dos usuários e filiais criados anteriormente)
     EstoqueSeeder::class, // Depois cria o estoque
     DefaultPlansSeeder::class, // Cria os planos padrão
     PlanServiceSeeder::class, // Associa os serviços aos planos         
     SubscriptionsSeeder::class, // Cria assinaturas para os tenants
     AppointmentsTableSeeder::class, // Por último, os agendamentos (que dependem dos anteriores)
     AvaliacoesTableSeeder::class, // Por último, as avaliações (que dependem dos usuários e filiais criados anteriormente)
     ComandasSeeder::class, // Por último, as comandas (que dependem dos agendamentos)
     CaixaSeeder::class, // Por último, os caixas (que dependem dos agendamentos)        
    ]);

    
    

    }  
       
}

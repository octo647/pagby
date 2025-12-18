<?php

namespace Database\Seeders;

use App\Livewire\RoleUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\User;

class DefaultPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = \App\Models\Tenant::all();

        foreach ($tenants as $tenant) {
            switch ($tenant->type) {
                case 'Barbearia':
                    $plans = [
                        ['name' => 'Corte', 'price' => 30, 'duration_days' => 30, 'allowed_days'=>['segunda','terça','quarta','quinta','sexta']],
                        ['name' => 'Barba', 'price' => 20, 'duration_days' => 30, 'allowed_days'=>['segunda','terça','quarta','quinta','sexta']],
                        ['name' => 'Corte e Barba', 'price' => 45, 'duration_days' => 30, 'allowed_days'=>['segunda','terça','quarta','quinta','sexta']],
                    ];
                    break;
                case 'Salão de Beleza':
                    $plans = [
                        ['name' => 'Tingimento', 'price' => 80, 'duration_days' => 30,'allowed_days'=>[1,2,3,4,5]],
                        ['name' => 'Unhas', 'price' => 40, 'duration_days' => 30, 'allowed_days'=>[1,2,3,4,5]],
                        ['name' => 'Depilação', 'price' => 60, 'duration_days' => 30, 'allowed_days'=>[1,2,3,4,5]],
                        ['name' => 'Alisamento', 'price' => 120, 'duration_days' => 30, 'allowed_days'=>[1,2,3,4,5]],
                        ['name' => 'Corte', 'price' => 35, 'duration_days' => 30, 'allowed_days'=>[1,2,3,4,5]],
                    ];
                    break;
                case 'Petshop':
                    $plans = [
                        ['name' => 'Banho', 'price' => 50, 'duration_days' => 30, 'allowed_days'=>[1,2,3,4,5]],
                        ['name' => 'Tosa', 'price' => 60, 'duration_days' => 30, 'allowed_days'=>[1,2,3,4,5]],
                        ['name' => 'Banho e Tosa', 'price' => 100, 'duration_days' => 30, 'allowed_days'=>[1,2,3,4,5]],
                    ];
                    break;
                default:
                    $plans = [];
            }

            foreach ($plans as $plan) {
                \App\Models\Plan::updateOrCreate(
                    [
                        'name' => $plan['name'],
                    ],
                    [
                        'price' => $plan['price'],
                        'duration_days' => $plan['duration_days'],
                        'allowed_days' => $plan['allowed_days'],
                        'active' => true,
                        'features' => [
                            'Serviços ilimitados dentro do plano',
                            'Desconto de 10% em serviços adicionais'
                        ],
                        'created_by' => User::whereHas('roles', function($q) {
                            $q->where('role', 'Proprietário');
                        })->first()?->id,
                        // Adicione outros campos necessários aqui
                    ]
                );
            }
        }
    }
}

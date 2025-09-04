<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DefaultPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Básico',
                'price' => 29.90,
                'duration_days' => 30,
                'features' => [
                    'Até 1 profissional',
                    'Agendamento online',
                    'Controle financeiro básico',
                    'Relatórios simples',
                    'Suporte via email'
                ],
                'allowed_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'active' => true,
                'created_by' => 1
            ],
            [
                'name' => 'Intermediário',
                'price' => 59.90,
                'duration_days' => 30,
                'features' => [
                    'Até 3 profissionais',
                    'Agendamento online',
                    'Controle financeiro avançado',
                    'Relatórios detalhados',
                    'Gestão de estoque básica',
                    'Suporte via chat'
                ],
                'allowed_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'active' => true,
                'created_by' => 1
            ],
            [
                'name' => 'Avançado',
                'price' => 99.90,
                'duration_days' => 30,
                'features' => [
                    'Profissionais ilimitados',
                    'Agendamento online',
                    'Controle financeiro completo',
                    'Relatórios avançados',
                    'Gestão de estoque completa',
                    'Sistema de fidelidade',
                    'Múltiplas filiais',
                    'Suporte prioritário'
                ],
                'allowed_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
                'active' => true,
                'created_by' => 1
            ]
        ];

        foreach ($plans as $planData) {
            \App\Models\Plan::updateOrCreate(
                ['name' => $planData['name']],
                $planData
            );
        }
    }
}

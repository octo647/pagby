<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Plan;
use App\Models\Service;


class PlanServiceSeeder extends Seeder
{
    public function run(): void
    {
        // Definição dos serviços principais de cada plano
        $planServices = [
            'Corte' => ['Corte'],
            'Barba' => ['Barba'],
            'Corte e Barba' => ['Corte', 'Barba'],
            'Tingimento' => ['Tingimento'],
            'Unhas' => ['Unhas'],
            'Depilação' => ['Depilação'],
            'Alisamento' => ['Alisamento'],
            'Banho' => ['Banho'],
            'Tosa' => ['Tosa'],
            'Banho e Tosa' => ['Banho', 'Tosa'],
        ];

        // Definição dos serviços adicionais de cada plano
        $planAdditionalServices = [
            'Corte' => ['Barba'],
            'Tingimento' => ['Unhas', 'Depilação'],
            'Banho' => ['Tosa'],
            'Tosa' => ['Banho'],
            'Corte e Barba' => [],
            'Banho e Tosa' => [],
        ];

        foreach (Plan::all() as $plan) {
            // Serviços principais
            $nomesPrincipaisDoPlano = $planServices[$plan->name] ?? [];
            
            $mainServices = Service::whereIn('service', $nomesPrincipaisDoPlano)
                ->get();
            $discount = 10;
            $allowed_days = Plan::where('id', $plan->id)->value('allowed_days'); // Exemplo de dias permitidos
               

            foreach ($mainServices as $service) {
                DB::table('plan_service')->updateOrInsert([
                    'plan_id' => $plan->id,
                    'service_id' => $service->id,
                ], [
                    'created_at' => now(),
                    'updated_at' => now(),
                    'discount' => $discount ?? 0,
                    'allowed_days' => is_array($allowed_days) ? json_encode($allowed_days) : $allowed_days,
                ]);
            }

            // Serviços adicionais
            $nomesAdicionaisDoPlano = $planAdditionalServices[$plan->name] ?? [];
            $additionalServices = Service::whereIn('service', $nomesAdicionaisDoPlano)
                ->get();
            foreach ($additionalServices as $service) {
                DB::table('plan_additional_service')->insert([
                    'plan_id' => $plan->id,
                    'service_id' => $service->id,
                ]);
            }
        }
    }
}

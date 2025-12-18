<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanMonthlyRevenuesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //determina os planos ativos
        $plans = \App\Models\Plan::where('active', true)->get();
        //determina os branchs ativos
        $branches = \App\Models\Branch::all();
        foreach ($plans as $plan) {
            foreach ($branches as $branch) {
                foreach (range(1, 12) as $month) {
                    $monthDate = now()->setMonth($month)->endOfMonth()->format('Y-m-d');
                    \App\Models\PlanMonthlyRevenue::updateOrCreate(
                        [
                            'plan_id' => $plan->id,
                            'branch_id' => $branch->id,
                            'month' => $monthDate,
                        ],
                        [
                            'revenue' => rand(1000, 10000),
                        ]
                    );
                }
            }
        }

    }
}

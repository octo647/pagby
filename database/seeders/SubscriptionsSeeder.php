<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //adiciona 10% dos usuários aos planos de assinatura
        $users = \App\Models\User::all();
        $plans = \App\Models\Plan::all();

        $usersToSubscribe = $users->random(intval($users->count() * 0.1));

        foreach ($usersToSubscribe as $user) {
            $plan = $plans->random();
            \App\Models\Subscription::updateOrCreate([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'start_date' => now(),
                'end_date' => now()->addDays($plan->duration_days),
                'status' => 'Ativo',
                'created_by' => $user->id,
            ]);
        }
    }
}   
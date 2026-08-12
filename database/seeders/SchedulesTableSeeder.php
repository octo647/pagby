<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchedulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Pega o ID de todos os funcionários
        $funcionarioIds = \App\Models\User::whereHas('roles', function ($query) {
            $query->where('role', 'Funcionário');
        })->pluck('id')->toArray();
        foreach ($funcionarioIds as $funcionarioId) {
    // Gera 5 dias únicos para o funcionário
    $diasSemana = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    shuffle($diasSemana);
    $diasAleatorios = array_slice($diasSemana, 0, 5);

    $filialIds = \App\Models\BranchUser::where('user_id', $funcionarioId)
        ->pluck('branch_id')
        ->toArray();

    foreach ($filialIds as $filialId) {
        foreach ($diasAleatorios as $dia) {
            // Verifica se já existe um agendamento para o funcionário nesse dia e filial
            $exists = \App\Models\Schedule::where('user_id', $funcionarioId)
                ->where('branch_id', $filialId)
                ->where('day_of_week', $dia)
                ->exists();

            if ($exists) continue;

            // Gera horários conforme sua lógica...
            $startHour = rand(9, 13);
            $start_time = sprintf('%02d:00', $startHour);
            $lunch_start = sprintf('%02d:00', $startHour + 4);
            $lunch_end = sprintf('%02d:00', $startHour + 5);
            $endHour = min($startHour + 9, 22);
            $end_time = sprintf('%02d:00', $endHour);

            \App\Models\Schedule::create([
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => $funcionarioId,
                'branch_id' => $filialId,
                'day_of_week' => $dia,
                'start_time' => $start_time,
                'lunch_start' => $lunch_start,
                'lunch_end' => $lunch_end,
                'end_time' => $end_time,
                'status' => 'active',
            ]);
        }
    }
}       
        }
    }


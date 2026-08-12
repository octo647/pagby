<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Branch;
use App\Models\BranchUser;
use App\Models\Service;

use Illuminate\Support\Facades\DB;


class AppointmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pegue alguns funcionários, clientes e filiais existentes
        $employees = User::whereHas('roles', function($query) {
            $query->where('role', 'Funcionário');
        })->get();       
        $customers = User::whereHas('roles', function($query) {
            $query->where('role', 'Cliente');
        })->get();  
        
        
        $branches = Branch::all();


        // Serviços fictícios
        $servicosPossiveis = Service::pluck('service')->toArray();

        foreach (range(1, 18000) as $i) {
            $employee = $employees->random();
            $customer = $customers->random();
            $branch = $branches->random();

            // Gera 1 a 3 serviços separados por barra
            $servicosSelecionados = collect($servicosPossiveis)->random(rand(1, 3));
            $servicos = $servicosSelecionados->implode('/');

            // Gera data variada
            $data = now()->addDays(14)->subDays(rand(0, 360));
            $dataStr = $data->format('Y-m-d');

            // Define status conforme passado/futuro
            if ($data->isPast()) {
                $status = (mt_rand(1, 100) <= 5) ? 'Cancelado' : 'Realizado';
            } else {
                $status = 'Pendente';
            }

            // Calcular start_time e end_time realistas
            $startHour = rand(8, 18);
            $startMinute = [0, 15, 30, 45][array_rand([0, 15, 30, 45])];
            $start = now()->setTime($startHour, $startMinute, 0);

            // Buscar duração de cada serviço
            $totalDuration = 0;
            foreach ($servicosSelecionados as $nomeServico) {
                $service = \App\Models\Service::where('service', $nomeServico)->first();
                $totalDuration += $service ? ($service->time ?? 30) : 30;
            }
            $end = (clone $start)->addMinutes($totalDuration);

            Appointment::create([
                'employee_id' => $employee->id,
                'branch_id' => $branch->id,
                'customer_id' => $customer->id,
                'services' => $servicos,
                'appointment_date' => $dataStr,
                'start_time' => $start->format('H:i:s'),
                'end_time' => $end->format('H:i:s'),
                'status' => $status,
                'total' => rand(30, 200),
            ]);
        }
    }
}

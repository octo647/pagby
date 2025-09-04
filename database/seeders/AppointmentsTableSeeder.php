<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Branch;
use App\Models\BranchUser;

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
        $servicosPossiveis = ['Corte', 'Barba', 'Coloração', 'Escova', 'Manicure', 'Pedicure', 'Massagem', 'Limpeza de Pele', 'Design de Sobrancelhas', 'Depilação', 'Maquiagem', 'Tratamento Capilar', 'Hidratação', 'Relaxamento', 'Penteado', 'Corte de Cabelo Infantil'];

        foreach (range(1, 2000) as $i) {
            $employee = $employees->random();
            $customer = $customers->random();
            $branch = $branches->random();

            // Gera 1 a 3 serviços separados por barra
            $servicos = collect($servicosPossiveis)->random(rand(1, 3))->implode('/');

            Appointment::create([
                'employee_id' => $employee->id,
                'branch_id' => $branch->id,
                'customer_id' => $customer->id,
                'services' => $servicos,
                'appointment_date' => now()->subDays(rand(0, 360))->format('Y-m-d'),
                'start_time' => now()->setTime(rand(8, 18), [0, 15, 30, 45][array_rand([0, 15, 30, 45])])->format('H:i:s'),
                'end_time' => now()->setTime(rand(8, 18), [0, 15, 30, 45][array_rand([0, 15, 30, 45])])->addMinutes(rand(30, 120))->format('H:i:s'),
                'status' => ['Pendente', 'Confirmado', 'Realizado', 'Cancelado'][array_rand(['Pendente', 'Confirmado', 'Realizado','Cancelado'])],
                'total' => rand(30, 200),
            ]);
        }
    }
}

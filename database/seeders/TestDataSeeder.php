<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\Comanda;
use App\Models\ComandaProduto;
use App\Models\ComandaServico;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Appointments para mês anterior e atual
        Appointment::factory()->count(30)->create([
            'appointment_date' => now()->subMonth()->startOfMonth()->addDays(rand(0,29)),
        ]);
        Appointment::factory()->count(30)->create([
            'appointment_date' => now()->startOfMonth()->addDays(rand(0, now()->day-1)),
        ]);

        // Comandas para mês anterior e atual
        Comanda::factory()->count(30)->create([
            'data_abertura' => now()->subMonth()->startOfMonth()->addDays(rand(0,29)),
            'data_fechamento' => now()->subMonth()->startOfMonth()->addDays(rand(0,29)),
        ]);
        Comanda::factory()->count(30)->create([
            'data_abertura' => now()->startOfMonth()->addDays(rand(0, now()->day-1)),
            'data_fechamento' => now()->startOfMonth()->addDays(rand(0, now()->day-1)),
        ]);

        // ComandaProdutos e ComandaServicos para comandas criadas
        ComandaProduto::factory()->count(60)->create();
        ComandaServico::factory()->count(60)->create();
    }
}

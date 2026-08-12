<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Defina o ID do tenant desejado
        $type = tenant()->type; // Define o tipo de tenant atual
        $tenantId = tenant()->id;
        if ($type === 'Barbearia') {
        $services = [
            ['service'=> 'Corte', 'price'=> 40, 'time'=> 30],
            ['service'=> 'Barba', 'price'=> 20, 'time'=> 15],
            ['service'=> 'Corte e Barba', 'price'=> 50, 'time'=> 45],
            ['service'=> 'Sobrancelha', 'price'=> 15, 'time'=> 10],
            ['service'=> 'Corte Infantil', 'price'=> 45, 'time'=> 20],
            ['service'=> 'Barba Designer', 'price'=> 30, 'time'=> 20],
            ['service'=> 'Tratamento Capilar', 'price'=> 70, 'time'=> 40],
            ['service'=> 'Hidratação', 'price'=> 60, 'time'=> 30],
            ['service'=> 'Escova', 'price'=> 50, 'time'=> 25],
            ['service'=> 'Relaxamento', 'price'=> 80, 'time'=> 50],
            ['service'=> 'Coloração', 'price'=> 90, 'time'=> 60],
            ['service'=> 'Luzes', 'price'=> 100, 'time'=> 70],
        ];
        } elseif ($type === 'SalaoBeleza') {
        $services = [
            ['service'=> 'Corte', 'price'=> 40, 'time'=> 30],
            ['service'=> 'Alisamento', 'price'=> 20, 'time'=> 15],
            ['service'=> 'Corte e Alisamento', 'price'=> 50, 'time'=> 45],
            ['service'=> 'Sobrancelha', 'price'=> 15, 'time'=> 10],
            ['service'=> 'Manicure', 'price'=> 45, 'time'=> 20],
            ['service'=> 'Tintura', 'price'=> 30, 'time'=> 20],
            ['service'=> 'Penteado', 'price'=> 60, 'time'=> 40],
            ['service'=> 'Maquiagem', 'price'=> 70, 'time'=> 50],
            ['service'=> 'Corte Infantil', 'price'=> 35, 'time'=> 25],
            ['service'=> 'Hidratação', 'price'=> 80, 'time'=> 60],
            ['service'=> 'Escova', 'price'=> 50, 'time'=> 30],
            ['service'=> 'Depilação', 'price'=> 40, 'time'=> 20],
        ];
        } elseif ($type === 'PetShop') {
        $services = [        
            ['service'=> 'Banho', 'price'=> 60, 'time'=> 40],
            ['service'=> 'Tosa', 'price'=> 80, 'time'=> 60],
            ['service'=> 'Banho e Tosa', 'price'=> 130, 'time'=> 90],
            ['service'=> 'Hidratação de Pelos', 'price'=> 50, 'time'=> 30],
            ['service'=> 'Corte de Unhas', 'price'=> 20, 'time'=> 15],
            ['service'=> 'Limpeza de Ouvidos', 'price'=> 25, 'time'=> 15],
            ['service'=> 'Escovação de Dentes', 'price'=> 30, 'time'=> 20],
        ];
        } elseif ($type === 'Clínica Estética') {
        $services = [
            ['service'=> 'Limpeza de Pele', 'price'=> 100, 'time'=> 60],
            ['service'=> 'Peeling', 'price'=> 150, 'time'=> 75],
            ['service'=> 'Microdermoabrasão', 'price'=> 200, 'time'=> 90],
            ['service'=> 'Tratamento Anti-idade', 'price'=> 250, 'time'=> 120],
            ['service'=> 'Tratamento para Acne', 'price'=> 180, 'time'=> 80],
            ['service'=> 'Massagem Facial', 'price'=> 120, 'time'=> 50],
        ];
        } elseif ($type === 'Clínica Veterinária') {
        $services = [
            ['service'=> 'Consulta Veterinária', 'price'=> 80, 'time'=> 30],
            ['service'=> 'Vacinação', 'price'=> 60, 'time'=> 20],
            ['service'=> 'Exame de Sangue', 'price'=> 100, 'time'=> 40],
            ['service'=> 'Cirurgia Simples', 'price'=> 300, 'time'=> 120],
            ['service'=> 'Desparasitação', 'price'=> 70, 'time'=> 25],
            ['service'=> 'Check-up Completo', 'price'=> 150, 'time'=> 60],
        ];
        } else {
            // Se o tipo de tenant não corresponder a nenhum dos acima, não faça nada
            return;
        }

        foreach ($services as $service) {
            \App\Models\Service::firstOrCreate(
                [
                    'service' => $service['service'],
                    
                ],
                array_merge($service, [
                    
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }
    }
}

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
        // Verifica se a tabela de services está vazia antes de inserir os dados
    if (Service::count() === 0) {
    // Insere serviços padrão
        $services = [['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Corte', 'price'=> 40.00, 'time'=> 30],
                     ['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Barba', 'price'=> 20.00, 'time'=> 15],
                     ['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Cabelo e Barba', 'price'=> 50.00, 'time'=> 45],
                     ['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Sobrancelha', 'price'=> 15.00, 'time'=> 10],
                     ['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Lavagem de Cabelo', 'price'=> 10.00, 'time'=> 5],
                     ['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Penteado', 'price'=> 30.00, 'time'=> 20],
                     ['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Depilação', 'price'=> 25.00, 'time'=> 15],
                     ['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Manicure', 'price'=> 20.00, 'time'=> 30],
                     ['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Pedicure', 'price'=> 25.00, 'time'=> 30],
                     ['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Massagem', 'price'=> 60.00, 'time'=> 60],
                     ['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Tratamento Capilar', 'price'=> 70.00, 'time'=> 45],
                     ['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Coloração', 'price'=> 80.00, 'time'=> 90],
                     ['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Alisamento', 'price'=> 100.00, 'time'=> 120],
                     ['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Escova', 'price'=> 50.00, 'time'=> 60],
                     ['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Corte Infantil', 'price'=> 30.00, 'time'=> 20],
                     ['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Corte de Barba', 'price'=> 20.00, 'time'=> 15],
                     ['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Design de Sobrancelha', 'price'=> 25.00, 'time'=> 20],
                     ['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Hidratação Capilar', 'price'=> 40.00, 'time'=> 30],
                     ['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Escova Progressiva', 'price'=> 150.00, 'time'=> 120],
                     ['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Pintura de Unhas', 'price'=> 15.00, 'time'=> 10],
                     ['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Alongamento de Unhas', 'price'=> 50.00, 'time'=> 60],
                     ['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Design de Unhas', 'price'=> 40.00, 'time'=> 30],
                     ['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Limpeza de Pele', 'price'=> 70.00, 'time'=> 60],
                     ['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Maquiagem', 'price'=> 100.00, 'time'=> 90],
                     ['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Barba Designer', 'price'=> 30.00, 'time'=> 20],
                     ['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Corte de Cabelo Feminino', 'price'=> 50.00, 'time'=> 45],
                     ['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Corte de Cabelo Masculino', 'price'=> 40.00, 'time'=> 30],
                     ['created_at'=>now(),  'updated_at'=> now(), 'service'=> 'Corte de Cabelo Unissex', 'price'=> 45.00, 'time'=> 35]];
    foreach ($services as $service) {
        \App\Models\Service::firstOrCreate(
            ['service' => $service['service']],
            $service
        );
    }
    }
    }
}

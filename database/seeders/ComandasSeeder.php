<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Appointment;
use App\Models\Comanda;
use App\Models\Service;
use App\Models\Estoque;
use App\Models\User;

class ComandasSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Appointment::all() as $appointment) {
            // Cria a comanda relacionada ao appointment
            $serviceNames = array_map('trim', explode('/', $appointment->services));
           
            $subtotal_services = Service::whereIn('service', $serviceNames)->sum('price');
             if($appointment->appointment_date<now()){ 
                $status = 'Finalizada';
                } else {
                $status = 'Aberta';
                }

            $comanda = Comanda::create([
                'appointment_id' => $appointment->id,
                'branch_id' => $appointment->branch_id,
                'numero_comanda' => $appointment->id.'-'. $appointment->created_at->format('Ymd').'-'.random_int(1000,9999),
                'cliente_nome' => User::where('id', $appointment->customer_id)->value('name'),
                'cliente_telefone' => User::where('id', $appointment->customer_id)->value('phone'),
                'funcionario_id' => $appointment->employee_id,
                'data_abertura' => $appointment->appointment_date,
                'status' => $status,               
                'data_fechamento' => $appointment->appointment_date,
                'subtotal_servicos' => $subtotal_services,
                'subtotal_produtos' => 0,
                'desconto_servicos' => 0,
                'desconto_produtos' => 0,
                'total_geral' => $subtotal_services,
            ]);

            // Adiciona serviços à comanda extraídos do appointment
            // Supondo que o campo 'services' armazene IDs separados por vírgula
            
            $serviceIds = Service::whereIn('service', $serviceNames)->pluck('id')->toArray();
            foreach ($serviceIds as $serviceId) {
                DB::table('comanda_servicos')->insert([
                    'comanda_id' => $comanda->id,
                    'service_id' => $serviceId,
                    'funcionario_id' => $appointment->employee_id,
                    'quantidade' => 1,
                    'preco_unitario' => Service::find($serviceId)->price ?? 0,
                    'subtotal' => Service::find($serviceId)->price ?? 0,
                    'status_servico' => 'Concluído',
                    // outros campos...
                ]);
            }

            // Adiciona produtos à comanda
            $produtos = Estoque::where('branch_id', $appointment->branch_id ?? null)
                ->inRandomOrder()->take(2)->get();
            $total_produtos = 0;
            foreach ($produtos as $produto) {
                DB::table('comanda_produtos')->insert([
                    'funcionario_id' => $appointment->employee_id,
                    'quantidade' => 1,
                    'preco_unitario' => $produto->preco_unitario,
                    'subtotal' => $produto->preco_unitario,
                    'comanda_id' => $comanda->id,
                    'estoque_id' => $produto->id,
                    'created_at' => $appointment->created_at,
               
                    // outros campos...
                ]);
                $total_produtos += $produto->preco_unitario;
            }
            // Atualiza os totais da comanda após adicionar produtos        
            $comanda->subtotal_produtos = $total_produtos;
            $comanda->total_geral = $comanda->subtotal_servicos + $comanda->subtotal_produtos;
            $comanda->save();
        }
    }
}

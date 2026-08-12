<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Appointment;
use App\Models\Comanda;
use App\Models\Service;
use App\Models\Estoque;
use App\Models\User;
use App\Models\Branch;
use Carbon\Carbon;

class ComandasSeeder extends Seeder
{
    public function run(): void
    {
        // Primeiro, cria comandas baseadas nos appointments existentes
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

        // Agora cria comandas adicionais com datas recentes para testes dos períodos
        $this->criarComandasRecentes();
    }

    /**
     * Cria comandas com datas de fechamento recentes para testes
     * Distribui comandas nos últimos 30 dias para cobrir períodos semanal, quinzenal e mensal
     */
    private function criarComandasRecentes(): void
    {
        $branches = Branch::all();
        if ($branches->isEmpty()) {
            $this->command->warn('Nenhuma filial encontrada. Pulando criação de comandas recentes.');
            return;
        }

        // Pega funcionários com a role 'Funcionário'
        $funcionarios = User::whereHas('roles', function($q) {
            $q->where('role', 'Funcionário');
        })->get();

        if ($funcionarios->isEmpty()) {
            $this->command->warn('Nenhum funcionário encontrado. Pulando criação de comandas recentes.');
            return;
        }

        // Pega clientes
        $clientes = User::whereHas('roles', function($q) {
            $q->where('role', 'Cliente');
        })->get();

        if ($clientes->isEmpty()) {
            $this->command->warn('Nenhum cliente encontrado. Pulando criação de comandas recentes.');
            return;
        }

        $hoje = Carbon::today();
        
        // Define os períodos de distribuição
        $periodos = [
            'semanal' => [
                'inicio' => $hoje->copy()->subDays(6), // Últimos 7 dias
                'fim' => $hoje->copy(),
                'quantidade' => 15, // 15 comandas na última semana
            ],
            'quinzenal' => [
                'inicio' => $hoje->copy()->subDays(14), // Dias 8 a 14 atrás
                'fim' => $hoje->copy()->subDays(7),
                'quantidade' => 10, // 10 comandas na segunda semana
            ],
            'mensal' => [
                'inicio' => $hoje->copy()->subDays(30), // Dias 15 a 30 atrás
                'fim' => $hoje->copy()->subDays(15),
                'quantidade' => 8, // 8 comandas no resto do mês
            ],
        ];

        $comandaCounter = Comanda::max('id') ?? 0;

        foreach ($periodos as $nomePeriodo => $config) {
            $this->command->info("Criando comandas para período {$nomePeriodo}...");
            
            for ($i = 0; $i < $config['quantidade']; $i++) {
                $comandaCounter++;
                
                // Escolhe uma data aleatória dentro do período
                $diasDiferenca = $config['inicio']->diffInDays($config['fim']);
                $dataAbertura = $config['inicio']->copy()->addDays(rand(0, $diasDiferenca));
                $dataFechamento = $dataAbertura->copy()->addHours(rand(1, 4));
                
                // Escolhe filial, funcionário e cliente aleatórios
                $branch = $branches->random();
                $funcionario = $funcionarios->random();
                $cliente = $clientes->random();
                
                // Pega serviços disponíveis
                $services = Service::where('branch_id', $branch->id)->inRandomOrder()->take(rand(1, 3))->get();
                if ($services->isEmpty()) {
                    $services = Service::inRandomOrder()->take(rand(1, 2))->get();
                }
                
                $subtotal_servicos = $services->sum('price');
                
                // Cria a comanda
                $comanda = Comanda::create([
                    'appointment_id' => null,
                    'branch_id' => $branch->id,
                    'numero_comanda' => 'CMD-' . $dataAbertura->format('Ymd') . '-' . str_pad($comandaCounter, 4, '0', STR_PAD_LEFT),
                    'cliente_nome' => $cliente->name,
                    'cliente_telefone' => $cliente->phone ?? '(00) 00000-0000',
                    'funcionario_id' => $funcionario->id,
                    'data_abertura' => $dataAbertura,
                    'status' => 'Finalizada',
                    'data_fechamento' => $dataFechamento,
                    'subtotal_servicos' => $subtotal_servicos,
                    'subtotal_produtos' => 0,
                    'desconto_servicos' => 0,
                    'desconto_produtos' => 0,
                    'total_geral' => $subtotal_servicos,
                    'created_at' => $dataAbertura,
                    'updated_at' => $dataFechamento,
                ]);
                
                // Adiciona os serviços à comanda
                foreach ($services as $service) {
                    DB::table('comanda_servicos')->insert([
                        'comanda_id' => $comanda->id,
                        'service_id' => $service->id,
                        'funcionario_id' => $funcionario->id,
                        'quantidade' => 1,
                        'preco_unitario' => $service->price,
                        'subtotal' => $service->price,
                        'status_servico' => 'Concluído',
                        'created_at' => $dataAbertura,
                        'updated_at' => $dataFechamento,
                    ]);
                }
                
                // Adiciona produtos aleatórios (50% de chance)
                if (rand(0, 1) === 1) {
                    $produtos = Estoque::where('branch_id', $branch->id)
                        ->inRandomOrder()
                        ->take(rand(1, 2))
                        ->get();
                    
                    $total_produtos = 0;
                    foreach ($produtos as $produto) {
                        $quantidade = rand(1, 3);
                        $subtotal = $produto->preco_unitario * $quantidade;
                        
                        DB::table('comanda_produtos')->insert([
                            'comanda_id' => $comanda->id,
                            'estoque_id' => $produto->id,
                            'funcionario_id' => $funcionario->id,
                            'quantidade' => $quantidade,
                            'preco_unitario' => $produto->preco_unitario,
                            'subtotal' => $subtotal,
                            'percentual_produtos' => $produto->percentual_produtos ?? 10,
                            'created_at' => $dataAbertura,
                            'updated_at' => $dataFechamento,
                        ]);
                        
                        $total_produtos += $subtotal;
                    }
                    
                    // Atualiza totais da comanda
                    $comanda->subtotal_produtos = $total_produtos;
                    $comanda->total_geral = $comanda->subtotal_servicos + $total_produtos;
                    $comanda->save();
                }
            }
        }

        $this->command->info('Comandas recentes criadas com sucesso!');
        $this->command->info('Total de comandas adicionais: ' . array_sum(array_column($periodos, 'quantidade')));
    }
}

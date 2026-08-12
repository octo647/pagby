<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Comanda;
use App\Models\Service;
use Illuminate\Support\Facades\Log;

class CorrigirComandasAgendamentos extends Command
{
    protected $signature = 'comandas:corrigir-agendamentos';
    protected $description = 'Corrige comandas criadas de agendamentos que não tiveram os serviços adicionados corretamente';

    public function handle()
    {
        $this->info('Iniciando correção das comandas de agendamentos...');

        // Inicializar tenant
        $tenant = \App\Models\Tenant::where('id', 'foo')->first();
        if (!$tenant) {
            $this->error('Tenant não encontrado!');
            return Command::FAILURE;
        }
        tenancy()->initialize($tenant);
        $this->info("Tenant '{$tenant->id}' inicializado.");

        // Buscar comandas que vieram de agendamentos e têm total zero
        $comandas = Comanda::whereNotNull('appointment_id')
            ->where('total_geral', 0)
            ->with(['appointment', 'comandaServicos'])
            ->get();

        $this->info("Encontradas {$comandas->count()} comandas para corrigir");

        $corrigidas = 0;

        foreach ($comandas as $comanda) {
            if (!$comanda->appointment || !$comanda->appointment->services) {
                continue;
            }

            $this->info("Corrigindo comanda {$comanda->numero_comanda}...");
            
            $appointment = $comanda->appointment;
            $services = $appointment->services;
            
            $this->line("  Serviços do agendamento: '{$services}'");

            $servicosAdicionados = 0;

            // Primeiro, tentar como IDs separados por vírgula
            if (strpos($services, ',') !== false) {
                $this->line("  Processando como IDs separados por vírgula...");
                $servicosIds = explode(',', $services);
                foreach ($servicosIds as $serviceId) {
                    $serviceId = trim($serviceId);
                    if ($serviceId && is_numeric($serviceId)) {
                        $service = Service::find($serviceId);
                        if ($service) {
                            try {
                                $comanda->adicionarServico(
                                    $serviceId, 
                                    $appointment->employee_id, 
                                    1,
                                    null,
                                    "Serviço do agendamento #{$appointment->id} (corrigido)"
                                );
                                $servicosAdicionados++;
                                $this->line("    ✓ Adicionado: {$service->service}");
                            } catch (\Exception $e) {
                                $this->error("    ✗ Erro ao adicionar serviço {$serviceId}: {$e->getMessage()}");
                            }
                        }
                    }
                }
            }
            // Senão, tentar como nomes separados por separadores
            else {
                $separadores = ['/', ',', ';'];
                $servicosNomes = [];
                
                foreach ($separadores as $sep) {
                    if (strpos($services, $sep) !== false) {
                        $servicosNomes = explode($sep, $services);
                        $this->line("  Processando como nomes separados por '{$sep}'...");
                        break;
                    }
                }
                
                // Se não encontrou separador, trata como um serviço único
                if (empty($servicosNomes)) {
                    $servicosNomes = [$services];
                    $this->line("  Processando como nome único...");
                }
                
                foreach ($servicosNomes as $servicoNome) {
                    $servicoNome = trim($servicoNome);
                    if ($servicoNome) {
                        // Buscar serviço pelo nome
                        $service = Service::where('service', 'like', "%{$servicoNome}%")->first();
                        if ($service) {
                            try {
                                $comanda->adicionarServico(
                                    $service->id, 
                                    $appointment->employee_id, 
                                    1,
                                    null,
                                    "Serviço do agendamento #{$appointment->id}: {$servicoNome} (corrigido)"
                                );
                                $servicosAdicionados++;
                                $this->line("    ✓ Adicionado: {$service->service}");
                            } catch (\Exception $e) {
                                $this->error("    ✗ Erro ao adicionar serviço '{$servicoNome}': {$e->getMessage()}");
                            }
                        } else {
                            $this->error("    ✗ Serviço não encontrado: '{$servicoNome}'");
                        }
                    }
                }
            }

            if ($servicosAdicionados > 0) {
                $comanda->recalcularTotais();
                $comanda->refresh();
                $this->info("  ✓ Comanda corrigida! Novo total: R$ " . number_format($comanda->total_geral, 2, ',', '.'));
                $corrigidas++;
            } else {
                $this->error("  ✗ Nenhum serviço foi adicionado à comanda");
            }

            $this->line("");
        }

        $this->info("Correção concluída! {$corrigidas} comandas foram corrigidas.");
        
        return Command::SUCCESS;
    }
}
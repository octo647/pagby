<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Models\Comanda;

class TestarComandaAutomatica extends Command
{
    protected $signature = 'comandas:testar-automatica';
    protected $description = 'Testa a criação automática de comanda ao confirmar agendamento';

    public function handle()
    {
        // Inicializar tenant
        $tenant = \App\Models\Tenant::where('id', 'foo')->first();
        if (!$tenant) {
            $this->error('Tenant não encontrado!');
            return Command::FAILURE;
        }
        tenancy()->initialize($tenant);

        $this->info('Testando criação automática de comanda...');

        // Buscar um agendamento não realizado
        $appointment = Appointment::where('status', '!=', 'Realizado')
                                ->whereNotNull('services')
                                ->first();

        if (!$appointment) {
            $this->error('Nenhum agendamento não realizado encontrado para testar');
            return Command::FAILURE;
        }

        $this->info("Agendamento ID: {$appointment->id}");
        $this->info("Serviços: '{$appointment->services}'");
        $this->info("Status atual: {$appointment->status}");

        // Remover comanda existente se houver
        $comandaExistente = Comanda::where('appointment_id', $appointment->id)->first();
        if ($comandaExistente) {
            $this->info('Removendo comanda existente...');
            $comandaExistente->comandaServicos()->delete();
            $comandaExistente->comandaProdutos()->delete();
            $comandaExistente->delete();
        }

        // Confirmar agendamento (deve disparar o observer)
        $this->info('Confirmando agendamento...');
        $appointment->status = 'Realizado';
        $appointment->save();

        // Verificar se comanda foi criada
        $comanda = Comanda::where('appointment_id', $appointment->id)->first();

        if ($comanda) {
            $this->info("✅ Comanda criada: {$comanda->numero_comanda}");
            $this->info("Total: R$ " . number_format($comanda->total_geral, 2, ',', '.'));
            
            $servicos = $comanda->comandaServicos()->with('service')->get();
            $this->info("Serviços adicionados: {$servicos->count()}");
            
            foreach ($servicos as $servico) {
                $this->line("  • {$servico->service->service}: R$ " . number_format($servico->subtotal, 2, ',', '.'));
            }

            if ($comanda->total_geral > 0) {
                $this->info('✅ Teste PASSOU - Comanda criada com total correto!');
            } else {
                $this->error('❌ Teste FALHOU - Comanda criada mas com total zero');
            }
        } else {
            $this->error('❌ Teste FALHOU - Comanda não foi criada');
        }

        return Command::SUCCESS;
    }
}
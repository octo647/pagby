<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Models\Comanda;
use Stancl\Tenancy\Facades\Tenancy;

class TestarGeracaoAutomatica extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comandas:testar-automatica {tenant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa a geração automática de comandas simulando confirmação de agendamento';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant');
        
        // Inicializar contexto do tenant
        $tenant = \App\Models\Tenant::find($tenantId);
        if (!$tenant) {
            $this->error("Tenant '{$tenantId}' não encontrado!");
            return;
        }

        Tenancy::initialize($tenant);
        
        $this->info("Testando geração automática no tenant: {$tenantId}");

        // Buscar agendamentos pendentes
        $agendamentos = Appointment::where('status', 'Pendente')
            ->with(['customer', 'employee', 'branch'])
            ->take(5)
            ->get();

        if ($agendamentos->count() === 0) {
            $this->warn('Nenhum agendamento pendente encontrado para testar.');
            return;
        }

        foreach ($agendamentos as $agendamento) {
            $this->line("Testando agendamento ID: {$agendamento->id}");
            $this->line("  Cliente: " . ($agendamento->customer->name ?? 'N/A'));
            $this->line("  Funcionário: " . ($agendamento->employee->name ?? 'N/A'));
            $this->line("  Filial: " . ($agendamento->branch->branch_name ?? 'N/A'));
            $this->line("  Data: {$agendamento->appointment_date} {$agendamento->start_time}");
            
            // Verificar se já tem comanda
            $comandaExistente = Comanda::where('appointment_id', $agendamento->id)->first();
            if ($comandaExistente) {
                $this->warn("  ⚠️  Já existe comanda: {$comandaExistente->numero_comanda}");
                continue;
            }

            // Simular confirmação do agendamento
            $this->line("  🔄 Confirmando agendamento...");
            $agendamento->update(['status' => 'Confirmado']);
            
            // Verificar se a comanda foi criada
            $comandaCriada = Comanda::where('appointment_id', $agendamento->id)->first();
            if ($comandaCriada) {
                $this->info("  ✅ Comanda criada automaticamente: {$comandaCriada->numero_comanda}");
                $this->line("      Total: R$ " . number_format($comandaCriada->total_geral, 2, ',', '.'));
                $this->line("      Serviços: {$comandaCriada->comandaServicos->count()}");
            } else {
                $this->error("  ❌ Falha ao criar comanda automaticamente");
            }
            
            $this->line('');
        }

        $this->info('Teste concluído!');
    }
}

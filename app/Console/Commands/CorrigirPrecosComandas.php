<?php

namespace App\Console\Commands;

use App\Models\Comanda;
use App\Models\ComandaServico;
use App\Models\Service;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Stancl\Tenancy\Facades\Tenancy;

class CorrigirPrecosComandas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comandas:corrigir-precos {tenant?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corrige discrepâncias de preços entre agendamentos e comandas existentes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant') ?? 'foo';
        
        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            $this->error("Tenant {$tenantId} não encontrado!");
            return 1;
        }

        Tenancy::initialize($tenant);
        
        $this->info("Corrigindo preços das comandas no tenant: {$tenantId}");
        
        // Buscar comandas com agendamentos relacionados
        $comandas = Comanda::whereNotNull('appointment_id')
            ->with(['appointment', 'comandaServicos'])
            ->get();
            
        $corrigidas = 0;
        $verificadas = 0;
        
        foreach ($comandas as $comanda) {
            $verificadas++;
            
            if (!$comanda->appointment) {
                continue;
            }
            
            $appointment = $comanda->appointment;
            $precoAgendamento = (float) $appointment->total;
            $precoComanda = (float) $comanda->total_geral;
            
            // Verificar se há discrepância significativa (mais de R$ 0.01)
            if (abs($precoAgendamento - $precoComanda) > 0.01) {
                $this->line("Discrepância encontrada:");
                $this->line("  Comanda: #{$comanda->numero_comanda}");
                $this->line("  Agendamento: #{$appointment->id} - R$ {$precoAgendamento}");
                $this->line("  Comanda: R$ {$precoComanda}");
                
                // Corrigir os preços dos serviços na comanda
                $servicosComanda = $comanda->comandaServicos;
                $totalServicos = $servicosComanda->count();
                
                if ($totalServicos > 0) {
                    $precoMedioCorreto = $precoAgendamento / $totalServicos;
                    
                    foreach ($servicosComanda as $comandaServico) {
                        $comandaServico->update([
                            'preco_unitario' => $precoMedioCorreto,
                            'subtotal' => $precoMedioCorreto * $comandaServico->quantidade,
                            'observacoes' => ($comandaServico->observacoes ?? '') . " | Preço corrigido em " . date('d/m/Y H:i')
                        ]);
                    }
                    
                    // Recalcular totais da comanda
                    $comanda->recalcularTotais();
                    
                    $this->info("  ✅ Corrigido para: R$ {$comanda->fresh()->total_geral}");
                    $corrigidas++;
                } else {
                    $this->warn("  ⚠️  Comanda sem serviços - não foi possível corrigir");
                }
            }
        }
        
        $this->info("\n📊 Resumo:");
        $this->info("Comandas verificadas: {$verificadas}");
        $this->info("Comandas corrigidas: {$corrigidas}");
        
        if ($corrigidas > 0) {
            $this->info("✅ Correção concluída com sucesso!");
        } else {
            $this->info("ℹ️  Nenhuma discrepância encontrada.");
        }
        
        return 0;
    }
}

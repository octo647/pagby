<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:check-expired-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica e atualiza o status de assinaturas e períodos de teste expirados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Verificando assinaturas expiradas...');
        
        $tenants = \App\Models\Tenant::all();
        $expiredCount = 0;
        $blockedCount = 0;
        
        foreach ($tenants as $tenant) {
            $wasUpdated = false;
            
            // Verifica período de teste expirado
            if ($tenant->isTrialExpired() && $tenant->subscription_status === 'trial') {
                $tenant->subscription_status = 'expired';
                $tenant->is_blocked = true;
                $wasUpdated = true;
                $expiredCount++;
                $blockedCount++;
                $this->warn("Tenant {$tenant->id}: Período de teste expirado - bloqueado");
            }
            
            // Verifica assinatura expirada
            if ($tenant->isSubscriptionExpired() && $tenant->subscription_status === 'active') {
                $tenant->subscription_status = 'expired';
                $tenant->is_blocked = true;
                $wasUpdated = true;
                $expiredCount++;
                $blockedCount++;
                $this->warn("Tenant {$tenant->id}: Assinatura do plano {$tenant->current_plan} expirada - bloqueado");
            }
            
            if ($wasUpdated) {
                $tenant->save();
            }
        }
        
        $this->info("Verificação concluída:");
        $this->info("- Total de tenants verificados: {$tenants->count()}");
        $this->info("- Assinaturas expiradas: {$expiredCount}");
        $this->info("- Tenants bloqueados: {$blockedCount}");
        
        return 0;
    }
}

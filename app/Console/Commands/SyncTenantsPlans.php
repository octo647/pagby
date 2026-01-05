<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\TenantPlan;
use App\Models\Plan;

class SyncTenantsPlans extends Command
{
    protected $signature = 'tenants:sync-plans';
    protected $description = 'Sincroniza planos dos tenants na tabela central tenants_plans';

    public function handle()
    {
        $this->info('🔄 Sincronizando planos dos tenants...');
        
        $tenants = Tenant::all();
        $totalSynced = 0;
        
        foreach ($tenants as $tenant) {
            $this->info("📌 Processando tenant: {$tenant->id}");
            
            // Inicializar contexto do tenant
            tenancy()->initialize($tenant);
            
            // Buscar todos os planos do tenant
            $plans = Plan::all();
            
            foreach ($plans as $plan) {
                // Verificar se já existe na tabela central
                $existingTenantPlan = TenantPlan::on('mysql')
                    ->where('tenant_id', $tenant->id)
                    ->where('plan_id', $plan->id)
                    ->first();
                
                if ($existingTenantPlan) {
                    // Atualizar dados existentes
                    $existingTenantPlan->update([
                        'name' => $plan->name,
                        'price' => $plan->price,
                        'duration_days' => $plan->duration_days,
                    ]);
                    $this->line("  ✓ Atualizado: {$plan->name} (ID: {$plan->id})");
                } else {
                    // Criar novo registro
                    TenantPlan::on('mysql')->create([
                        'tenant_id' => $tenant->id,
                        'plan_id' => $plan->id,
                        'name' => $plan->name,
                        'price' => $plan->price,
                        'duration_days' => $plan->duration_days,
                        'active' => true,
                    ]);
                    $this->line("  ✓ Criado: {$plan->name} (ID: {$plan->id})");
                }
                
                $totalSynced++;
            }
            
            // Finalizar contexto do tenant
            tenancy()->end();
        }
        
        $this->info("✅ Sincronização concluída! Total: {$totalSynced} planos sincronizados.");
        
        return 0;
    }
}

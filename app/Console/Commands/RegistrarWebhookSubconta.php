<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Services\AsaasService;
use Illuminate\Support\Facades\Log;

class RegistrarWebhookSubconta extends Command
{
    protected $signature = 'asaas:registrar-webhook 
                            {--tenant= : ID do tenant específico}
                            {--all : Registrar webhook para todos os tenants}
                            {--force : Forçar registro mesmo se já existir}';

    protected $description = 'Registra webhook da subconta Asaas para receber notificações de pagamento';

    public function handle()
    {
        $tenantId = $this->option('tenant');
        $all = $this->option('all');
        $force = $this->option('force');

        if (!$tenantId && !$all) {
            $this->error('❌ Especifique --tenant=ID ou --all');
            return 1;
        }

        $asaasMaster = new AsaasService();
        $tenants = $all 
            ? Tenant::on('mysql')->whereNotNull('asaas_account_id')->get()
            : Tenant::on('mysql')->where('id', $tenantId)->get();

        if ($tenants->isEmpty()) {
            $this->error('❌ Nenhum tenant encontrado');
            return 1;
        }

        $this->info("🔧 Registrando webhooks para {$tenants->count()} tenant(s)...");
        $this->newLine();

        $success = 0;
        $failed = 0;

        foreach ($tenants as $tenant) {
            if (!$tenant->asaas_account_id) {
                $this->warn("⚠️  {$tenant->id}: Sem subconta Asaas");
                continue;
            }

            $this->line("📋 Processando: {$tenant->name} (ID: {$tenant->id})");
            $this->line("   Account ID: {$tenant->asaas_account_id}");

            $result = $asaasMaster->registrarWebhookSubconta($tenant->asaas_account_id);

            if ($result['success']) {
                $this->info("   ✅ Webhook registrado!");
                $this->line("   Webhook ID: {$result['data']['id']}");
                $success++;
            } else {
                $this->error("   ❌ Falha: {$result['message']}");
                $failed++;
            }

            $this->newLine();
        }

        $this->line(str_repeat('═', 60));
        $this->info("✅ Sucesso: {$success}");
        if ($failed > 0) {
            $this->error("❌ Falhas: {$failed}");
        }
        $this->line(str_repeat('═', 60));

        return 0;
    }
}

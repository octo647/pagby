<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Services\AsaasService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class DesabilitarWebhooksSubcontas extends Command
{
    protected $signature = 'asaas:desabilitar-webhooks 
                            {--tenant= : ID do tenant específico}
                            {--all : Desabilitar webhooks de todos os tenants}
                            {--delete : Deletar webhooks em vez de apenas desabilitar}';

    protected $description = 'Desabilita ou deleta webhooks das subcontas Asaas para parar penalizações';

    public function handle()
    {
        $tenantId = $this->option('tenant');
        $all = $this->option('all');
        $delete = $this->option('delete');

        if (!$tenantId && !$all) {
            $this->error('❌ Especifique --tenant=ID ou --all');
            return 1;
        }

        $this->warn('🚨 ATENÇÃO: Este comando vai ' . ($delete ? 'DELETAR' : 'DESABILITAR') . ' webhooks das subcontas Asaas');
        $this->warn('    Isso vai parar as penalizações, mas também vai parar as notificações automáticas.');
        $this->newLine();

        if (!$this->confirm('Deseja continuar?')) {
            $this->info('❌ Operação cancelada');
            return 0;
        }

        $asaasMaster = new AsaasService();
        $tenants = $all 
            ? Tenant::on('mysql')->whereNotNull('asaas_account_id')->get()
            : Tenant::on('mysql')->where('id', $tenantId)->get();

        if ($tenants->isEmpty()) {
            $this->error('❌ Nenhum tenant encontrado');
            return 1;
        }

        $this->info("🔧 Processando {$tenants->count()} tenant(s)...");
        $this->newLine();

        $success = 0;
        $failed = 0;
        $noWebhooks = 0;

        foreach ($tenants as $tenant) {
            if (!$tenant->asaas_account_id) {
                $this->warn("⚠️  {$tenant->id}: Sem subconta Asaas");
                continue;
            }

            $this->line("📋 Processando: {$tenant->name} (ID: {$tenant->id})");
            $this->line("   Account ID: {$tenant->asaas_account_id}");

            try {
                // Listar webhooks da subconta
                $response = Http::timeout(30)->withHeaders([
                    'access_token' => config('services.asaas.api_key'),
                    'Content-Type' => 'application/json',
                    'asaas-account' => $tenant->asaas_account_id,
                ])->get(config('services.asaas.api_url') . '/webhook');

                if (!$response->successful()) {
                    $this->error("   ❌ Erro ao listar webhooks: HTTP {$response->status()}");
                    $this->line("      Resposta: {$response->body()}");
                    $failed++;
                    continue;
                }

                $webhooks = $response->json();
                
                if (empty($webhooks['data'])) {
                    $this->info("   ℹ️  Nenhum webhook encontrado");
                    $noWebhooks++;
                    continue;
                }

                $webhookCount = count($webhooks['data']);
                $this->line("   🔍 Encontrados {$webhookCount} webhook(s)");

                foreach ($webhooks['data'] as $webhook) {
                    $webhookId = $webhook['id'];
                    $webhookName = $webhook['name'] ?? 'Sem nome';
                    $webhookUrl = $webhook['url'] ?? 'N/A';
                    $isEnabled = $webhook['enabled'] ?? false;

                    $this->line("      • Webhook ID: {$webhookId}");
                    $this->line("        Nome: {$webhookName}");
                    $this->line("        URL: {$webhookUrl}");
                    $this->line("        Status: " . ($isEnabled ? 'Ativado' : 'Desativado'));

                    if ($delete) {
                        // DELETAR webhook
                        $deleteResponse = Http::timeout(30)->withHeaders([
                            'access_token' => config('services.asaas.api_key'),
                            'Content-Type' => 'application/json',
                            'asaas-account' => $tenant->asaas_account_id,
                        ])->delete(config('services.asaas.api_url') . '/webhook/' . $webhookId);

                        if ($deleteResponse->successful()) {
                            $this->info("        ✅ Webhook deletado");
                        } else {
                            $this->error("        ❌ Erro ao deletar: {$deleteResponse->body()}");
                        }
                    } else {
                        // DESABILITAR webhook
                        if (!$isEnabled) {
                            $this->line("        ℹ️  Já está desabilitado");
                            continue;
                        }

                        $updateResponse = Http::timeout(30)->withHeaders([
                            'access_token' => config('services.asaas.api_key'),
                            'Content-Type' => 'application/json',
                            'asaas-account' => $tenant->asaas_account_id,
                        ])->put(config('services.asaas.api_url') . '/webhook/' . $webhookId, [
                            'enabled' => false,
                            'interrupted' => true,
                        ]);

                        if ($updateResponse->successful()) {
                            $this->info("        ✅ Webhook desabilitado");
                        } else {
                            $this->error("        ❌ Erro ao desabilitar: {$updateResponse->body()}");
                        }
                    }
                }

                $success++;
                $this->newLine();

            } catch (\Exception $e) {
                $this->error("   ❌ Exceção: {$e->getMessage()}");
                Log::error('[DesabilitarWebhooks] Exceção', [
                    'tenant_id' => $tenant->id,
                    'account_id' => $tenant->asaas_account_id,
                    'error' => $e->getMessage()
                ]);
                $failed++;
            }
        }

        $this->newLine();
        $this->info('📊 RESUMO:');
        $this->line("   ✅ Processados com sucesso: {$success}");
        $this->line("   ℹ️  Sem webhooks: {$noWebhooks}");
        $this->line("   ❌ Falhas: {$failed}");
        $this->newLine();

        if ($delete) {
            $this->warn('⚠️  Webhooks deletados. Para reativar, use: php artisan asaas:registrar-webhook --all');
        } else {
            $this->warn('⚠️  Webhooks desabilitados. Para reativar, use: php artisan asaas:registrar-webhook --all');
        }

        return 0;
    }
}

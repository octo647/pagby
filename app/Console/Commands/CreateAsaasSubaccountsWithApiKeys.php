<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Services\AsaasService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class CreateAsaasSubaccountsWithApiKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asaas:create-subaccounts-with-keys 
                            {--tenant= : ID de um tenant específico}
                            {--force : Recriar subconta mesmo se já existir}
                            {--only-keys : Apenas gerar API keys para subcontas existentes}
                            {--check-status : Verificar e atualizar status de subcontas pendentes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria subcontas Asaas COMPLETAS com API keys para modelo sem split (pagamentos diretos 100%)';

    protected $asaasService;

    /**
     * Execute the console command.
     */
    public function handle(AsaasService $asaasService)
    {
        $this->asaasService = $asaasService;
        
        $tenantId = $this->option('tenant');
        $force = $this->option('force');
        $onlyKeys = $this->option('only-keys');
        $checkStatus = $this->option('check-status');

        $this->info("🚀 Criando subcontas Asaas com API keys (Modelo SEM split)\n");

        // Modo: Verificar status
        if ($checkStatus) {
            return $this->checkAndUpdateStatus($tenantId);
        }

        // Modo: Apenas gerar API keys
        if ($onlyKeys) {
            $this->info("📋 Modo: APENAS gerar API keys para subcontas existentes\n");
        }

        if ($tenantId) {
            $tenant = Tenant::on('mysql')->find($tenantId);
            
            if (!$tenant) {
                $this->error("❌ Tenant {$tenantId} não encontrado.");
                return 1;
            }
            
            $this->processarTenant($tenant, $force, $onlyKeys);
        } else {
            $tenants = Tenant::on('mysql')->get();
            $this->info("📋 Processando " . $tenants->count() . " tenants...\n");
            
            $progressBar = $this->output->createProgressBar($tenants->count());
            
            foreach ($tenants as $tenant) {
                $this->processarTenant($tenant, $force, $onlyKeys);
                $progressBar->advance();
            }
            
            $progressBar->finish();
            $this->newLine(2);
        }

        $this->info('✅ Processamento concluído!');
        return 0;
    }

    /**
     * Processa criação de subconta para um tenant
     */
    private function processarTenant(Tenant $tenant, bool $force, bool $onlyKeys)
    {
        $this->newLine();
        $this->info("🔄 Tenant: {$tenant->id} - {$tenant->name}");

        // Se for apenas gerar keys
        if ($onlyKeys) {
            if (!$tenant->asaas_account_id) {
                $this->warn("⏭️  Tenant não tem asaas_account_id. Pulando...");
                return;
            }

            return $this->gerarApiKey($tenant);
        }

        // Verificar se já tem subconta
        if ($tenant->asaas_account_id && !$force) {
            $this->line("⏭️  Já possui subconta: {$tenant->asaas_account_id}");
            
            // Verificar se tem API key
            if (!$tenant->asaas_api_key) {
                $this->info("🔑 Gerando API key...");
                $this->gerarApiKey($tenant);
            } else {
                $this->line("   Status: {$tenant->getSubaccountStatusDisplay()}");
            }
            
            return;
        }

        // Validar dados necessários
        $email = $tenant->email;
        if (!$email) {
            $this->warn("⚠️  Sem email. Pulando...");
            return;
        }

        // Coletar dados (interativo se necessário)
        $cpfCnpj = $tenant->cnpj;
        if (!$cpfCnpj) {
            $cpfCnpj = $this->ask("CNPJ/CPF para {$tenant->name}:");
        }
        
        $phone = $tenant->phone ?? $tenant->whatsapp;
        if (!$phone) {
            $phone = $this->ask("Telefone para {$tenant->name}:");
        }
        
        $cpfCnpjClean = preg_replace('/\D/', '', $cpfCnpj);
        
        if (strlen($cpfCnpjClean) !== 11 && strlen($cpfCnpjClean) !== 14) {
            $this->error("❌ CPF/CNPJ inválido: {$cpfCnpj}");
            return;
        }

        // Preparar dados da subconta
        $accountData = [
            'name' => $tenant->name,
            'email' => $email,
            'cpfCnpj' => $cpfCnpjClean,
            'mobilePhone' => preg_replace('/\D/', '', $phone),
        ];

        // Dados específicos para CNPJ
        if (strlen($cpfCnpjClean) === 14) {
            $accountData['companyType'] = 'LIMITED'; // Pode ajustar: MEI, LIMITED, INDIVIDUAL
            $accountData['incomeValue'] = 5000.00; // Valor padrão, pode parametrizar
            
            $this->line("   Tipo: Empresa (CNPJ)");
        } 
        // Dados específicos para CPF
        else {
            $birthDate = $this->ask("Data nascimento (YYYY-MM-DD) para {$tenant->name}:", '1990-01-01');
            $accountData['birthDate'] = $birthDate;
            $accountData['incomeValue'] = 3000.00; // Valor padrão para CPF
            
            $this->line("   Tipo: Pessoa Física (CPF)");
        }

        // Criar subconta completa
        $this->line("📤 Criando subconta no Asaas...");
        
        try {
            $result = $this->asaasService->criarSubcontaCompleta($accountData);

            if ($result['success']) {
                $accountId = $result['data']['account_id'];
                $apiKey = $result['data']['api_key'] ?? null;
                $walletId = $result['data']['wallet_id'] ?? null;

                // Salvar dados no tenant
                $tenant->asaas_account_id = $accountId;
                $tenant->asaas_wallet_id = $walletId;
                
                if ($apiKey) {
                    $tenant->asaas_api_key = Crypt::encryptString($apiKey);
                }
                
                $tenant->asaas_account_status = 'pending'; // Aguardando aprovação Asaas
                $tenant->asaas_account_data = json_encode($result['data']['account']);
                $tenant->save();

                $this->info("✅ Subconta criada com sucesso!");
                $this->line("   Account ID: {$accountId}");
                if ($walletId) {
                    $this->line("   Wallet ID: {$walletId}");
                }
                if ($apiKey) {
                    $this->line("   API Key: " . substr($apiKey, 0, 20) . "...");
                    $this->comment("   Status: Aguardando aprovação Asaas (até 48h)");
                }

                Log::info('[CreateAsaasSubaccounts] Subconta criada', [
                    'tenant_id' => $tenant->id,
                    'account_id' => $accountId
                ]);

            } else {
                $this->error("❌ Erro: {$result['message']}");
                
                if (isset($result['errors'])) {
                    $this->error(json_encode($result['errors'], JSON_PRETTY_PRINT));
                }
                
                Log::error('[CreateAsaasSubaccounts] Erro ao criar subconta', [
                    'tenant_id' => $tenant->id,
                    'error' => $result
                ]);
            }

        } catch (\Exception $e) {
            $this->error("❌ Exceção: {$e->getMessage()}");
            Log::error('[CreateAsaasSubaccounts] Exceção', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Gera ou recupera API key para uma subconta existente
     */
    private function gerarApiKey(Tenant $tenant)
    {
        $this->line("🔑 Gerando/recuperando API key para subconta...");

        try {
            $result = $this->asaasService->obterApiKeySubconta($tenant->asaas_account_id);

            if ($result['success'] && $result['api_key']) {
                $apiKey = $result['api_key'];
                $tenant->asaas_api_key = Crypt::encryptString($apiKey);
                $tenant->save();

                $this->info("✅ API key gerada/recuperada!");
                $this->line("   API Key: " . substr($apiKey, 0, 20) . "...");

                Log::info('[CreateAsaasSubaccounts] API key gerada', [
                    'tenant_id' => $tenant->id,
                    'account_id' => $tenant->asaas_account_id
                ]);

            } else {
                $this->error("❌ Erro ao gerar API key: {$result['message']}");
            }

        } catch (\Exception $e) {
            $this->error("❌ Exceção: {$e->getMessage()}");
        }
    }

    /**
     * Verifica e atualiza status de subcontas pendentes
     */
    private function checkAndUpdateStatus($tenantId = null)
    {
        $this->info("🔍 Verificando status de subcontas...\n");

        if ($tenantId) {
            $tenants = Tenant::on('mysql')->where('id', $tenantId)->get();
        } else {
            // Apenas tenants com subconta criada
            $tenants = Tenant::on('mysql')
                ->whereNotNull('asaas_account_id')
                ->get();
        }

        if ($tenants->isEmpty()) {
            $this->warn("Nenhum tenant com subconta encontrado.");
            return 0;
        }

        $this->info("Verificando " . $tenants->count() . " tenant(s)...\n");

        $updated = 0;
        $pending = 0;
        $active = 0;

        foreach ($tenants as $tenant) {
            $this->line("🔄 {$tenant->name} ({$tenant->id})");
            $this->line("   Account ID: {$tenant->asaas_account_id}");
            $this->line("   Status atual: {$tenant->asaas_account_status}");

            try {
                $result = $this->asaasService->consultarStatusSubconta($tenant->asaas_account_id);

                if ($result['success']) {
                    $newStatus = strtolower($result['status']);
                    $oldStatus = $tenant->asaas_account_status;

                    $this->line("   Status Asaas: {$result['status']}");

                    // Atualizar se mudou
                    if ($newStatus !== $oldStatus) {
                        $tenant->asaas_account_status = $newStatus;
                        
                        if ($newStatus === 'active' && !$tenant->asaas_account_activated_at) {
                            $tenant->asaas_account_activated_at = now();
                        }
                        
                        $tenant->save();

                        $this->info("   ✅ Status atualizado: {$oldStatus} → {$newStatus}");
                        $updated++;

                        Log::info('[CreateAsaasSubaccounts] Status atualizado', [
                            'tenant_id' => $tenant->id,
                            'old_status' => $oldStatus,
                            'new_status' => $newStatus
                        ]);
                    } else {
                        $this->line("   ⏭️  Status inalterado");
                    }

                    if ($newStatus === 'pending') $pending++;
                    if ($newStatus === 'active') $active++;

                } else {
                    $this->warn("   ⚠️  Erro ao consultar: {$result['message']}");
                }

            } catch (\Exception $e) {
                $this->error("   ❌ Exceção: {$e->getMessage()}");
            }

            $this->newLine();
        }

        $this->info("\n📊 Resumo:");
        $this->line("   Total verificado: {$tenants->count()}");
        $this->line("   Atualizados: {$updated}");
        $this->line("   Pendentes: {$pending}");
        $this->line("   Ativos: {$active}");

        return 0;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Services\AsaasService;
use Illuminate\Support\Facades\Log;

class CreateAsaasAccountsForTenants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:create-asaas-accounts 
                            {--tenant= : ID de um tenant específico}
                            {--force : Recriar conta mesmo se já existir}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria subcontas Asaas para tenants (necessário para split de pagamentos)';

    protected $asaasService;

    /**
     * Execute the console command.
     */
    public function handle(AsaasService $asaasService)
    {
        $this->asaasService = $asaasService;
        
        $tenantId = $this->option('tenant');
        $force = $this->option('force');

        if ($tenantId) {
            // Processar apenas um tenant específico
            $tenant = Tenant::on('mysql')->find($tenantId);
            
            if (!$tenant) {
                $this->error("Tenant {$tenantId} não encontrado.");
                return 1;
            }
            
            $this->createAccountForTenant($tenant, $force);
        } else {
            // Processar todos os tenants
            $tenants = Tenant::on('mysql')->get();
            $this->info("Processando " . $tenants->count() . " tenants...\n");
            
            $progressBar = $this->output->createProgressBar($tenants->count());
            
            foreach ($tenants as $tenant) {
                $this->createAccountForTenant($tenant, $force);
                $progressBar->advance();
            }
            
            $progressBar->finish();
            $this->newLine(2);
        }

        $this->info('✅ Processamento concluído!');
        return 0;
    }

    /**
     * Cria subconta Asaas para um tenant específico
     */
    private function createAccountForTenant(Tenant $tenant, bool $force = false)
    {
        // Verificar se já tem wallet_id e não é force
        if ($tenant->asaas_wallet_id && !$force) {
            $this->line("⏭️  Tenant {$tenant->id} já possui wallet_id: {$tenant->asaas_wallet_id}");
            return;
        }

        $this->info("\n🔄 Processando tenant: {$tenant->id} - {$tenant->name}");

        // Verificar se temos os dados necessários (usar email ou owner_email)
        $email = $tenant->owner_email ?? $tenant->email;
        
        if (!$email) {
            $this->warn("⚠️  Tenant {$tenant->id} não possui email. Pulando...");
            return;
        }

        // Preparar dados da conta (adaptar aos campos disponíveis)
        $cpfCnpj = $tenant->owner_cpf_cnpj ?? $tenant->cnpj ?? $this->ask("CPF/CNPJ para {$tenant->name}:");
        $phone = $tenant->owner_phone ?? $tenant->phone ?? $tenant->whatsapp ?? $this->ask("Telefone para {$tenant->name}:");
        
        // Limpar CPF/CNPJ
        $cpfCnpjClean = preg_replace('/\D/', '', $cpfCnpj);
        
        $accountData = [
            'name' => $tenant->name,
            'email' => $email,
            'cpfCnpj' => $cpfCnpjClean,
            'mobilePhone' => $phone,
        ];

        // Se for CNPJ (14 dígitos), adicionar dados da empresa
        if (strlen($cpfCnpjClean) === 14) {
            $accountData['companyType'] = 'LIMITED'; // Pode ser MEI, LIMITED, INDIVIDUAL, ASSOCIATION
            $incomeValue = $this->ask("Faturamento mensal estimado (ex: 5000.00) para {$tenant->name}:");
            $accountData['incomeValue'] = (float) $incomeValue;
        } 
        // Se for CPF (11 dígitos), adicionar data de nascimento e renda
        elseif (strlen($cpfCnpjClean) === 11) {
            $birthDate = $tenant->owner_birthdate ?? $this->ask("Data de nascimento (YYYY-MM-DD) para {$tenant->name}:");
            $accountData['birthDate'] = $birthDate;
            
            $incomeValue = $this->ask("Renda mensal estimada (ex: 3000.00) para {$tenant->name}:");
            $accountData['incomeValue'] = (float) $incomeValue;
        }

        try {
            $this->line("📤 Enviando dados para Asaas...");
            
            $result = $this->asaasService->criarSubconta($accountData);

            if ($result['success']) {
                $walletId = $result['data']['walletId'] ?? $result['data']['id'];
                
                $tenant->asaas_wallet_id = $walletId;
                $tenant->asaas_account_data = json_encode($result['data']);
                $tenant->save();

                $this->info("✅ Subconta criada com sucesso! Wallet ID: {$walletId}");
                
                Log::info('Subconta Asaas criada para tenant', [
                    'tenant_id' => $tenant->id,
                    'wallet_id' => $walletId
                ]);
            } else {
                $this->error("❌ Erro ao criar subconta para tenant {$tenant->id}:");
                $this->error($result['message']);
                
                if (isset($result['errors'])) {
                    $this->error(json_encode($result['errors'], JSON_PRETTY_PRINT));
                }
                
                Log::error('Erro ao criar subconta Asaas', [
                    'tenant_id' => $tenant->id,
                    'error' => $result
                ]);
            }
        } catch (\Exception $e) {
            $this->error("❌ Exceção ao criar subconta para tenant {$tenant->id}:");
            $this->error($e->getMessage());
            
            Log::error('Exceção ao criar subconta Asaas', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}

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

        // Buscar proprietário no banco do tenant
        try {
            tenancy()->initialize($tenant);
            
            // Buscar usuário proprietário no banco tenant
            $proprietario = \DB::connection('tenant')
                ->table('users')
                ->join('role_user', 'users.id', '=', 'role_user.user_id')
                ->join('roles', 'role_user.role_id', '=', 'roles.id')
                ->where('roles.role', 'Proprietário')
                ->select('users.*')
                ->first();
            
            if (!$proprietario) {
                $this->warn("⚠️  Tenant {$tenant->id} não possui usuário proprietário. Pulando...");
                tenancy()->end();
                return;
            }
            
            $this->line("   Proprietário encontrado: {$proprietario->name} ({$proprietario->email})");
            
            // Limpar CPF/CNPJ
            $cpfCnpj = $tenant->cnpj ?? $proprietario->cpf ?? null;
            
            if (!$cpfCnpj) {
                $this->warn("⚠️  Tenant {$tenant->id} não possui CPF/CNPJ. Pulando...");
                tenancy()->end();
                return;
            }
            
            $cpfCnpjClean = preg_replace('/\D/', '', $cpfCnpj);
            
            // Email
            $email = $tenant->email ?? $proprietario->email;
            
            // Telefone
            $phone = $tenant->phone ?? $proprietario->phone ?? null;
            if ($phone) {
                $phone = preg_replace('/\D/', '', $phone);
            }
            
            tenancy()->end();
            
        } catch (\Exception $e) {
            $this->error("❌ Erro ao buscar proprietário: " . $e->getMessage());
            tenancy()->end();
            return;
        }

        // Verificar se já existe customer com esse CPF/CNPJ no Asaas
        $customerExistenteId = $this->asaasService->buscarCustomerPorCpf($cpfCnpjClean);
        
        // REGRA: Se CPF (11 dígitos) já é customer no Asaas, NÃO pode criar subconta
        // Asaas não permite mesmo CPF ser customer E subconta simultaneamente
        if (strlen($cpfCnpjClean) === 11 && $customerExistenteId) {
            $this->warn("⚠️ CPF já é customer Asaas (ID: {$customerExistenteId})");
            $this->warn("   Modelo SEM split não disponível - tenant usará modelo COM split");
            $this->line("   (Asaas não permite mesmo CPF ser customer E subconta)");
            return; // Não cria subconta
        }
        
        // Preparar dados da conta
        $accountData = [
            'name' => $tenant->fantasy_name ?? $tenant->name,
            'email' => $email,
            'cpfCnpj' => $cpfCnpjClean,
        ];
        
        $this->line("   CPF/CNPJ: " . substr($cpfCnpjClean, 0, 3) . "...***");
        
        // Adicionar telefone se disponível E VÁLIDO
        if ($phone && strlen($phone) >= 10 && strlen($phone) <= 11) {
            // Validar se começa com DDD válido (apenas DDDs reais do Brasil)
            $ddd = (int)substr($phone, 0, 2);
            $dddsValidos = [11, 12, 13, 14, 15, 16, 17, 18, 19, // SP
                            21, 22, 24, 27, 28, // RJ/ES
                            31, 32, 33, 34, 35, 37, 38, // MG
                            41, 42, 43, 44, 45, 46, // PR
                            47, 48, 49, // SC
                            51, 53, 54, 55, // RS
                            61, // DF
                            62, 64, // GO
                            63, // TO
                            65, 66, // MT
                            67, // MS
                            68, 69, // AC/RO
                            71, 73, 74, 75, 77, // BA
                            79, // SE
                            81, 87, // PE
                            82, // AL
                            83, // PB
                            84, // RN
                            85, 88, // CE
                            86, 89, // PI
                            91, 93, 94, // PA
                            92, 97, // AM
                            95, // RR
                            96, // AP
                            98, 99]; // MA
            
            if (in_array($ddd, $dddsValidos)) {
                $number = substr($phone, 2);
                $accountData['mobilePhone'] = $ddd . $number;
                $this->line("   Telefone: {$ddd} {$number}");
            } else {
                $this->warn("   ⚠️ DDD {$ddd} inválido, criando subconta SEM telefone");
            }
        } else {
            $this->warn("   ⚠️ Telefone ausente ou inválido, criando subconta SEM telefone");
        }

        // Se for CNPJ (14 dígitos)
        if (strlen($cpfCnpjClean) === 14) {
            $accountData['companyType'] = 'MEI';
            $accountData['incomeValue'] = 5000;
            $this->line("   Tipo: CNPJ - Empresa");
        } 
        // Se for CPF (11 dígitos)
        elseif (strlen($cpfCnpjClean) === 11) {
            // Usar birthdate do proprietário
            $birthDate = $proprietario->birthdate ?? '1990-01-01';
            if ($birthDate instanceof \DateTime || $birthDate instanceof \Carbon\Carbon) {
                $birthDate = $birthDate->format('Y-m-d');
            }
            $accountData['birthDate'] = $birthDate;
            $accountData['incomeValue'] = 3000;
            $this->line("   Tipo: CPF - Pessoa Física");
            $this->line("   Data nascimento: {$birthDate}");
        }

        try {
            $this->line("📤 Enviando dados para Asaas...");
            
            $result = $this->asaasService->criarSubcontaCompleta($accountData);

            if ($result['success']) {
                $accountId = $result['data']['account_id'];
                $walletId = $result['data']['wallet_id'] ?? null;
                $apiKey = $result['data']['api_key'] ?? null;
                
                $tenant->asaas_account_id = $accountId;
                $tenant->asaas_wallet_id = $walletId;
                
                if ($apiKey) {
                    $tenant->asaas_api_key = \Illuminate\Support\Facades\Crypt::encryptString($apiKey);
                }
                
                $tenant->save();

                $this->info("✅ Subconta criada com sucesso!");
                $this->line("   Account ID: {$accountId}");
                $this->line("   Wallet ID: {$walletId}");
                
                Log::info('Subconta Asaas criada para tenant', [
                    'tenant_id' => $tenant->id,
                    'account_id' => $accountId,
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

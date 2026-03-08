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

        // Preparar dados da conta
        $accountData = [
            'name' => $tenant->fantasy_name ?? $tenant->name,
            'email' => $email,
            'cpfCnpj' => $cpfCnpjClean,
        ];
        
        // Adicionar telefone se disponível E VÁLIDO
        if ($phone && strlen($phone) >= 10 && strlen($phone) <= 11) {
            // Validar se começa com DDD válido (11-99)
            $ddd = substr($phone, 0, 2);
            if ($ddd >= 11 && $ddd <= 99) {
                $number = substr($phone, 2);
                $accountData['mobilePhone'] = $ddd . $number;
                $this->line("   Telefone: {$ddd} {$number}");
            } else {
                $this->warn("   ⚠️ Telefone com DDD inválido, não enviando para Asaas");
            }
        } else {
            $this->warn("   ⚠️ Telefone inválido ou ausente, não enviando para Asaas");
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

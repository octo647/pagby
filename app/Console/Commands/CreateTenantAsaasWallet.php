<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Services\AsaasService;

class CreateTenantAsaasWallet extends Command
{
    protected $signature = 'tenant:create-wallet {tenant_id} {--cnpj=} {--cpf=}';
    protected $description = 'Cria subconta (wallet) Asaas para um tenant';

    public function handle()
    {
        $tenantId = $this->argument('tenant_id');
        $cnpj = $this->option('cnpj');
        $cpf = $this->option('cpf');

        if (!$cnpj && !$cpf) {
            $this->error('É necessário fornecer --cnpj ou --cpf');
            return 1;
        }

        $tenant = Tenant::on('mysql')->find($tenantId);
        
        if (!$tenant) {
            $this->error("Tenant '{$tenantId}' não encontrado");
            return 1;
        }

        if ($tenant->asaas_wallet_id) {
            $this->warn("Tenant já possui wallet_id: {$tenant->asaas_wallet_id}");
            if (!$this->confirm('Deseja criar uma nova subconta?')) {
                return 0;
            }
        }

        $this->info("Criando subconta Asaas para tenant: {$tenantId}");
        
        $asaasService = new AsaasService();
        
        // Preparar dados da subconta
        $accountData = [
            'name' => $tenant->fantasy_name ?? $tenant->name ?? $tenantId,
            'email' => $tenant->email,
            'cpfCnpj' => $cnpj ?? $cpf,
        ];

        // Adicionar telefone se disponível
        if ($tenant->phone) {
            // Extrair apenas números do telefone
            $phone = preg_replace('/[^0-9]/', '', $tenant->phone);
            if (strlen($phone) >= 10) {
                $ddd = substr($phone, 0, 2);
                $number = substr($phone, 2);
                $accountData['mobilePhone'] = "({$ddd}) {$number}";
            }
        }

        // Adicionar endereço se disponível
        if ($tenant->address && $tenant->city && $tenant->state) {
            $accountData['address'] = $tenant->address;
            $accountData['addressNumber'] = $tenant->number ?? 'S/N';
            $accountData['province'] = $tenant->neighborhood ?? '';
            $accountData['postalCode'] = preg_replace('/[^0-9]/', '', $tenant->cep ?? '');
        }

        // Para CNPJ, adicionar companyType
        if ($cnpj) {
            $accountData['companyType'] = 'MEI'; // ou LIMITED, INDIVIDUAL, ASSOCIATION
            $accountData['incomeValue'] = 5000; // Faturamento mensal estimado
        } else {
            // Para CPF, adicionar data de nascimento (obrigatório)
            $accountData['birthDate'] = '1990-01-01'; // Data padrão
            $accountData['incomeValue'] = 3000; // Renda mensal estimada
        }

        $this->info('Dados da subconta:');
        $this->table(
            ['Campo', 'Valor'],
            collect($accountData)->map(fn($v, $k) => [$k, $v])->values()
        );

        if (!$this->confirm('Confirma criação da subconta?')) {
            $this->info('Operação cancelada');
            return 0;
        }

        try {
            $result = $asaasService->criarSubconta($accountData);

            if ($result['success']) {
                $walletId = $result['data']['walletId'];
                
                // Atualizar tenant com wallet_id
                $tenant->asaas_wallet_id = $walletId;
                $tenant->asaas_account_data = json_encode($result['data']);
                $tenant->save();

                $this->info("✅ Subconta criada com sucesso!");
                $this->info("Wallet ID: {$walletId}");
                $this->info("Account ID: {$result['data']['id']}");
                
                return 0;
            } else {
                $this->error("❌ Erro ao criar subconta:");
                $this->error($result['message'] ?? 'Erro desconhecido');
                if (isset($result['errors'])) {
                    $this->error(json_encode($result['errors'], JSON_PRETTY_PRINT));
                }
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("❌ Exceção: " . $e->getMessage());
            return 1;
        }
    }
}

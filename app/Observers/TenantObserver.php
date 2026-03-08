<?php

namespace App\Observers;

use App\Models\Tenant;
use App\Services\AsaasService;
use Illuminate\Support\Facades\Log;

/**
 * Observer para Tenant
 * Automatiza criação de subconta Asaas após registro
 */
class TenantObserver
{
    /**
     * Handle the Tenant "created" event.
     * Cria subconta Asaas automaticamente após criar tenant
     */
    public function created(Tenant $tenant): void
    {
        // Executar em background para não travar o registro
        dispatch(function () use ($tenant) {
            $this->criarSubcontaAsaas($tenant);
        })->afterResponse();
    }

    /**
     * Cria subconta Asaas para o tenant
     */
    private function criarSubcontaAsaas(Tenant $tenant): void
    {
        try {
            // Verificar se já tem subconta
            if ($tenant->asaas_account_id || $tenant->asaas_wallet_id) {
                Log::info('[TenantObserver] Tenant já possui subconta Asaas', [
                    'tenant_id' => $tenant->id,
                    'account_id' => $tenant->asaas_account_id,
                    'wallet_id' => $tenant->asaas_wallet_id
                ]);
                return;
            }

            Log::info('[TenantObserver] Criando subconta Asaas automática', [
                'tenant_id' => $tenant->id,
                'name' => $tenant->name
            ]);

            // Buscar proprietário no banco do tenant
            try {
                tenancy()->initialize($tenant);
                
                $proprietario = \DB::connection('tenant')
                    ->table('users')
                    ->join('role_user', 'users.id', '=', 'role_user.user_id')
                    ->join('roles', 'role_user.role_id', '=', 'roles.id')
                    ->where('roles.role', 'Proprietário')
                    ->select('users.*')
                    ->first();
                
                tenancy()->end();
                
                if (!$proprietario) {
                    Log::warning('[TenantObserver] Proprietário não encontrado no banco tenant', [
                        'tenant_id' => $tenant->id
                    ]);
                    return;
                }
                
                Log::info('[TenantObserver] Proprietário encontrado', [
                    'tenant_id' => $tenant->id,
                    'proprietario_name' => $proprietario->name,
                    'proprietario_email' => $proprietario->email
                ]);
                
            } catch (\Exception $e) {
                Log::error('[TenantObserver] Erro ao buscar proprietário', [
                    'tenant_id' => $tenant->id,
                    'error' => $e->getMessage()
                ]);
                tenancy()->end();
                return;
            }

            $asaasService = new AsaasService();
            
            // Preparar dados da subconta
            $cpfCnpj = $tenant->cnpj ?? $proprietario->cpf ?? null;
            
            if (empty($cpfCnpj)) {
                Log::warning('[TenantObserver] Tenant sem CPF/CNPJ, não pode criar subconta', [
                    'tenant_id' => $tenant->id
                ]);
                return;
            }

            // Remover formatação
            $cpfCnpj = preg_replace('/[^0-9]/', '', $cpfCnpj);
            
            // Validar tamanho
            if (!in_array(strlen($cpfCnpj), [11, 14])) {
                Log::error('[TenantObserver] CPF/CNPJ inválido', [
                    'tenant_id' => $tenant->id,
                    'cpf_cnpj_length' => strlen($cpfCnpj)
                ]);
                return;
            }

            $accountData = [
                'name' => $tenant->fantasy_name ?? $tenant->name ?? $tenant->id,
                'email' => $tenant->email ?? $proprietario->email,
                'cpfCnpj' => $cpfCnpj,
            ];

            // Validar email
            if (empty($accountData['email'])) {
                Log::warning('[TenantObserver] Tenant sem email, não pode criar subconta', [
                    'tenant_id' => $tenant->id
                ]);
                return;
            }

            // Adicionar telefone se disponível E VÁLIDO
            $phone = $tenant->phone ?? $proprietario->phone ?? null;
            if ($phone) {
                $phone = preg_replace('/[^0-9]/', '', $phone);
                if (strlen($phone) >= 10 && strlen($phone) <= 11) {
                    // Validar DDD (11-99)
                    $ddd = substr($phone, 0, 2);
                    if ($ddd >= 11 && $ddd <= 99) {
                        $number = substr($phone, 2);
                        $accountData['mobilePhone'] = $ddd . $number;
                        Log::info('[TenantObserver] Telefone adicionado', [
                            'tenant_id' => $tenant->id,
                            'phone' => $ddd . ' ' . $number
                        ]);
                    } else {
                        Log::warning('[TenantObserver] Telefone com DDD inválido', [
                            'tenant_id' => $tenant->id,
                            'phone' => $phone
                        ]);
                    }
                } else {
                    Log::warning('[TenantObserver] Telefone com tamanho inválido', [
                        'tenant_id' => $tenant->id,
                        'phone_length' => strlen($phone)
                    ]);
                }
            }

            // Adicionar endereço se disponível
            if ($tenant->address && $tenant->city && $tenant->state) {
                $accountData['address'] = $tenant->address;
                $accountData['addressNumber'] = $tenant->number ?? 'S/N';
                $accountData['province'] = $tenant->neighborhood ?? '';
                $accountData['postalCode'] = preg_replace('/[^0-9]/', '', $tenant->cep ?? '');
            }

            // Para CNPJ (14 dígitos)
            if (strlen($cpfCnpj) === 14) {
                $accountData['companyType'] = 'MEI'; // Padrão
                $accountData['incomeValue'] = 5000;
            } 
            // Para CPF (11 dígitos)
            else {
                // Data de nascimento do proprietário
                $birthDate = $proprietario->birthdate ?? '1990-01-01';
                if ($birthDate instanceof \DateTime || $birthDate instanceof \Carbon\Carbon) {
                    $birthDate = $birthDate->format('Y-m-d');
                }
                $accountData['birthDate'] = $birthDate;
                $accountData['incomeValue'] = 3000;
            }

            Log::info('[TenantObserver] Enviando dados para Asaas', [
                'tenant_id' => $tenant->id,
                'accountData' => array_merge($accountData, ['cpfCnpj' => '***']) // Ocultar CPF no log
            ]);

            // Criar subconta completa (account + wallet + api_key + webhook)
            $result = $asaasService->criarSubcontaCompleta($accountData);

            if ($result['success']) {
                $accountId = $result['data']['account_id'];
                $walletId = $result['data']['wallet_id'] ?? null;
                $apiKey = $result['data']['api_key'] ?? null;
                
                // Atualizar tenant com dados Asaas
                $tenant->asaas_account_id = $accountId;
                $tenant->asaas_wallet_id = $walletId;
                
                if ($apiKey) {
                    $tenant->asaas_api_key = \Illuminate\Support\Facades\Crypt::encryptString($apiKey);
                }
                
                $tenant->asaas_account_status = 'pending'; // Aguardando aprovação
                $tenant->asaas_account_data = json_encode($result['data']['account']);
                $tenant->save();

                Log::info('[TenantObserver] ✅ Subconta Asaas criada com sucesso!', [
                    'tenant_id' => $tenant->id,
                    'account_id' => $accountId,
                    'wallet_id' => $walletId,
                    'api_key_created' => !empty($apiKey)
                ]);

            } else {
                Log::error('[TenantObserver] ❌ Erro ao criar subconta Asaas', [
                    'tenant_id' => $tenant->id,
                    'error' => $result['message'] ?? 'Erro desconhecido',
                    'errors' => $result['errors'] ?? null
                ]);
            }

        } catch (\Exception $e) {
            Log::error('[TenantObserver] ❌ Exceção ao criar subconta Asaas', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}

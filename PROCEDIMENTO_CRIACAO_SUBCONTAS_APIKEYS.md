# 🔑 Procedimento: Criação de Subcontas e API Keys para Tenants

**Data:** 05/03/2026  
**Objetivo:** Documentar processo completo para criar subcontas Asaas e obter API keys dos tenants

---

## 📊 Situação Atual vs Situação Desejada

### ❌ Modelo Atual (COM SPLIT - Problemático)

```
Cliente paga R$ 100
↓
API Master PagBy (única API key)
├─ Split: 95% → Subconta Salão (wallet_id)
└─ Split: 5% → Conta Master PagBy
↓
NF emitida em nome do PAGBY (ERRADO!)
```

**Problemas:**
- ❌ NF emitida em nome do PagBy (mas serviço é do salão)
- ❌ Usa API key master para tudo
- ❌ Split financeiro, mas NF centralizada
- ❌ Fiscalmente incorreto

---

### ✅ Modelo Proposto (SEM SPLIT - Correto)

```
Cliente paga R$ 100
↓
API Key da SUBCONTA do Salão
├─ 100% → Conta do Salão
└─ NF emitida em nome do SALÃO (CORRETO!)

(Separadamente)

Salão paga R$ 80/mês
↓
API Master PagBy
├─ 100% → Conta Master PagBy
└─ NF emitida em nome do PAGBY (CORRETO!)
```

**Vantagens:**
- ✅ NF emitida pelo prestador real do serviço (salão)
- ✅ Salão usa sua própria API key (autonomia)
- ✅ Sem split (100% para quem recebe)
- ✅ Fiscalmente correto

---

## 🛠️ O QUE PRECISA SER FEITO

### Problema Identificado

**O código atual NÃO armazena a API key da subconta!**

Campos existentes na tabela `tenants`:
- ✅ `asaas_wallet_id` - ID da wallet (para split)
- ✅ `asaas_customer_id` - ID do customer (para cobranças B2B)
- ✅ `asaas_account_data` - JSON com dados completos da subconta
- ❌ **FALTA:** Campo para armazenar API key da subconta

---

## 📋 PROCEDIMENTO COMPLETO

### Fase 1: Preparação do Banco de Dados

#### 1.1 Criar Migration para API Keys

**Arquivo:** `database/migrations/2026_03_05_000001_add_asaas_api_key_to_tenants.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // API key da subconta Asaas (para receber pagamentos diretos)
            $table->string('asaas_api_key', 512)->nullable()
                ->after('asaas_account_data')
                ->comment('API key da subconta Asaas para receber pagamentos diretos');
            
            // ID da subconta (diferente de wallet_id)
            $table->string('asaas_account_id')->nullable()
                ->after('asaas_wallet_id')
                ->comment('ID da subconta Asaas (accountId)');
            
            // Status da subconta
            $table->enum('asaas_account_status', [
                'pending',      // Aguardando aprovação Asaas
                'active',       // Ativa e pode receber pagamentos
                'rejected',     // Rejeitada pelo Asaas
                'disabled'      // Desabilitada
            ])->nullable()->after('asaas_api_key');
            
            // Data de ativação
            $table->timestamp('asaas_account_activated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'asaas_api_key',
                'asaas_account_id',
                'asaas_account_status',
                'asaas_account_activated_at'
            ]);
        });
    }
};
```

**Executar:**
```bash
php artisan migrate
```

---

### Fase 2: Atualizar AsaasService

#### 2.1 Adicionar Método para Criar Subconta Completa

**Arquivo:** `app/Services/AsaasService.php`

```php
/**
 * Cria uma subconta Asaas (account) e obtém sua API key.
 * 
 * IMPORTANTE: Diferente de criarSubconta() que cria apenas wallet para split,
 * este método cria uma subconta COMPLETA que pode operar independentemente.
 * 
 * Documentação: https://docs.asaas.com/reference/criar-conta-filha
 * 
 * @param array $accountData Dados da subconta
 * @return array ['success' => bool, 'data' => array, 'message' => string]
 */
public function criarSubcontaCompleta(array $accountData)
{
    try {
        // 1. Criar a subconta
        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl . '/accounts', $accountData);

        if (!$response->successful()) {
            return [
                'success' => false,
                'message' => 'Erro ao criar subconta',
                'errors' => $response->json()
            ];
        }

        $accountCreated = $response->json();
        $accountId = $accountCreated['id'];

        // 2. Gerar API key para a subconta
        // NOTA: A API key é gerada automaticamente na criação, mas precisamos recuperá-la
        $apiKeyResponse = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl . '/accounts/' . $accountId . '/apiKeys');

        if (!$apiKeyResponse->successful()) {
            return [
                'success' => true, // Conta foi criada
                'data' => $accountCreated,
                'message' => 'Subconta criada, mas erro ao gerar API key',
                'api_key_error' => $apiKeyResponse->json()
            ];
        }

        $apiKeyData = $apiKeyResponse->json();

        return [
            'success' => true,
            'data' => [
                'account' => $accountCreated,
                'api_key' => $apiKeyData['apiKey'] ?? null,
                'account_id' => $accountId,
                'wallet_id' => $accountCreated['walletId'] ?? null
            ],
            'message' => 'Subconta criada com sucesso'
        ];

    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => 'Exceção ao criar subconta: ' . $e->getMessage(),
            'errors' => []
        ];
    }
}

/**
 * Recupera ou gera nova API key para uma subconta.
 * 
 * @param string $accountId ID da subconta
 * @return array
 */
public function obterApiKeySubconta(string $accountId)
{
    try {
        // Tentar recuperar API key existente
        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->get($this->apiUrl . '/accounts/' . $accountId . '/apiKeys');

        if ($response->successful()) {
            $apiKeys = $response->json()['data'] ?? [];
            
            if (!empty($apiKeys)) {
                // Retornar primeira API key ativa
                foreach ($apiKeys as $key) {
                    if ($key['status'] === 'ACTIVE') {
                        return [
                            'success' => true,
                            'api_key' => $key['apiKey']
                        ];
                    }
                }
            }
        }

        // Se não encontrou, gerar nova
        $createResponse = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl . '/accounts/' . $accountId . '/apiKeys');

        if ($createResponse->successful()) {
            $data = $createResponse->json();
            return [
                'success' => true,
                'api_key' => $data['apiKey'] ?? null
            ];
        }

        return [
            'success' => false,
            'message' => 'Erro ao obter/gerar API key',
            'errors' => $createResponse->json()
        ];

    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => 'Exceção: ' . $e->getMessage()
        ];
    }
}

/**
 * Consulta status de uma subconta.
 * 
 * @param string $accountId
 * @return array
 */
public function consultarStatusSubconta(string $accountId)
{
    try {
        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->get($this->apiUrl . '/accounts/' . $accountId);

        if ($response->successful()) {
            $data = $response->json();
            return [
                'success' => true,
                'status' => $data['status'] ?? 'UNKNOWN',
                'data' => $data
            ];
        }

        return [
            'success' => false,
            'message' => 'Erro ao consultar subconta'
        ];

    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => 'Exceção: ' . $e->getMessage()
        ];
    }
}
```

---

### Fase 3: Criar Command Atualizado

#### 3.1 Novo Command para Criar Subcontas com API Keys

**Arquivo:** `app/Console/Commands/CreateAsaasSubaccountsWithApiKeys.php`

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Services\AsaasService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class CreateAsaasSubaccountsWithApiKeys extends Command
{
    protected $signature = 'asaas:create-subaccounts-with-keys 
                            {--tenant= : ID de um tenant específico}
                            {--force : Recriar subconta mesmo se já existir}
                            {--only-keys : Apenas gerar API keys para subcontas existentes}';

    protected $description = 'Cria subcontas Asaas COMPLETAS com API keys para recebimento direto (modelo sem split)';

    protected $asaasService;

    public function handle(AsaasService $asaasService)
    {
        $this->asaasService = $asaasService;
        
        $tenantId = $this->option('tenant');
        $force = $this->option('force');
        $onlyKeys = $this->option('only-keys');

        $this->info("🚀 Criando subcontas Asaas com API keys (Modelo SEM split)\n");

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
            }
            
            return;
        }

        // Validar dados necessários
        $email = $tenant->email;
        if (!$email) {
            $this->warn("⚠️  Sem email. Pulando...");
            return;
        }

        // Coletar dados
        $cpfCnpj = $tenant->cnpj ?? $this->ask("CNPJ para {$tenant->name}:");
        $phone = $tenant->phone ?? $tenant->whatsapp ?? $this->ask("Telefone para {$tenant->name}:");
        
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
            'mobilePhone' => $phone,
        ];

        // Dados específicos para CNPJ
        if (strlen($cpfCnpjClean) === 14) {
            $accountData['companyType'] = 'LIMITED';
            $accountData['incomeValue'] = 5000.00; // Pode ser parametrizado
        } 
        // Dados específicos para CPF
        else {
            $birthDate = $this->ask("Data nascimento (YYYY-MM-DD) para {$tenant->name}:");
            $accountData['birthDate'] = $birthDate;
            $accountData['incomeValue'] = 3000.00;
        }

        // Criar subconta completa
        $this->line("📤 Criando subconta no Asaas...");
        
        try {
            $result = $this->asaasService->criarSubcontaCompleta($accountData);

            if ($result['success']) {
                $accountId = $result['data']['account_id'];
                $apiKey = $result['data']['api_key'];
                $walletId = $result['data']['wallet_id'];

                // Salvar dados
                $tenant->asaas_account_id = $accountId;
                $tenant->asaas_wallet_id = $walletId;
                $tenant->asaas_api_key = Crypt::encryptString($apiKey); // Criptografar!
                $tenant->asaas_account_status = 'pending'; // Aguardando aprovação
                $tenant->asaas_account_data = json_encode($result['data']['account']);
                $tenant->save();

                $this->info("✅ Subconta criada!");
                $this->line("   Account ID: {$accountId}");
                $this->line("   Wallet ID: {$walletId}");
                $this->line("   API Key: " . substr($apiKey, 0, 20) . "...");

                Log::info('Subconta Asaas criada com API key', [
                    'tenant_id' => $tenant->id,
                    'account_id' => $accountId
                ]);

            } else {
                $this->error("❌ Erro: {$result['message']}");
                
                if (isset($result['errors'])) {
                    $this->error(json_encode($result['errors'], JSON_PRETTY_PRINT));
                }
                
                Log::error('Erro ao criar subconta Asaas', [
                    'tenant_id' => $tenant->id,
                    'error' => $result
                ]);
            }

        } catch (\Exception $e) {
            $this->error("❌ Exceção: {$e->getMessage()}");
            Log::error('Exceção ao criar subconta', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function gerarApiKey(Tenant $tenant)
    {
        $this->line("🔑 Gerando API key para subconta...");

        try {
            $result = $this->asaasService->obterApiKeySubconta($tenant->asaas_account_id);

            if ($result['success']) {
                $apiKey = $result['api_key'];
                $tenant->asaas_api_key = Crypt::encryptString($apiKey);
                $tenant->save();

                $this->info("✅ API key gerada e salva!");
                $this->line("   API Key: " . substr($apiKey, 0, 20) . "...");

                Log::info('API key gerada para tenant', [
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
}
```

**Registrar no Kernel:**

`app/Console/Kernel.php`:
```php
protected $commands = [
    Commands\CreateAsaasSubaccountsWithApiKeys::class,
    // ... outros commands
];
```

---

### Fase 4: Atualizar Model Tenant

#### 4.1 Adicionar Campos e Métodos

```php
// app/Models/Tenant.php

protected $fillable = [
    // ... campos existentes
    'asaas_wallet_id',
    'asaas_account_id',
    'asaas_account_data',
    'asaas_api_key',
    'asaas_account_status',
    'asaas_account_activated_at',
];

protected $casts = [
    // ... casts existentes
    'asaas_account_data' => 'array',
    'asaas_account_activated_at' => 'datetime',
];

protected $hidden = [
    // ... outros campos
    'asaas_api_key', // Nunca expor em APIs!
];

/**
 * Obtém API key descriptografada da subconta.
 * 
 * @return string|null
 */
public function getAsaasApiKeyDecryptedAttribute()
{
    if (!$this->asaas_api_key) {
        return null;
    }

    try {
        return Crypt::decryptString($this->asaas_api_key);
    } catch (\Exception $e) {
        Log::error('Erro ao descriptografar API key', [
            'tenant_id' => $this->id,
            'error' => $e->getMessage()
        ]);
        return null;
    }
}

/**
 * Verifica se a subconta está ativa e pode receber pagamentos.
 * 
 * @return bool
 */
public function canReceivePayments()
{
    return $this->asaas_account_status === 'active' 
        && !empty($this->asaas_api_key);
}

/**
 * Verifica se precisa criar subconta.
 * 
 * @return bool
 */
public function needsAsaasSubaccount()
{
    return empty($this->asaas_account_id) 
        || empty($this->asaas_api_key);
}
```

---

### Fase 5: Processo de Pagamento com Subconta

#### 5.1 Criar Assinatura Usando API Key da Subconta

**Exemplo de uso:**

```php
// Controller ou Livewire
use App\Services\AsaasService;

public function criarAssinaturaCliente($clienteData, $planoData)
{
    $tenant = tenant(); // Tenant atual
    
    // Verificar se subconta está ativa
    if (!$tenant->canReceivePayments()) {
        throw new \Exception('Salão não está configurado para receber pagamentos');
    }

    // IMPORTANTE: Usar API key da SUBCONTA, não da master!
    $asaasService = new AsaasService(
        $tenant->asaas_api_key_decrypted // API key do tenant!
    );

    // Criar customer (cliente final)
    $customer = $asaasService->criarOuAtualizarCliente([
        'name' => $clienteData['name'],
        'email' => $clienteData['email'],
        'cpfCnpj' => $clienteData['cpf'],
        'mobilePhone' => $clienteData['phone'],
    ]);

    // Criar assinatura (SEM SPLIT!)
    $subscription = $asaasService->criarAssinatura(
        ['customer' => $customer['id']],
        [
            'billingType' => 'CREDIT_CARD',
            'value' => $planoData['value'],
            'cycle' => 'MONTHLY',
            'description' => $planoData['description'],
        ]
        // NÃO passa $splitData - 100% fica com a subconta
    );

    // Nota fiscal será emitida em nome do SALÃO (subconta)
    // não em nome do PagBy!

    return $subscription;
}
```

#### 5.2 Webhook Handler Específico

```php
// Controller
public function webhookAsaasSubconta(Request $request)
{
    $event = $request->get('event');
    $payment = $request->get('payment');
    
    // Identificar a qual tenant pertence este pagamento
    // Pode ser pelo customer_id ou payment_id
    
    switch ($event) {
        case 'PAYMENT_CONFIRMED':
            // Pagamento confirmado
            // NF será emitida pela SUBCONTA (em nome do salão)
            break;
            
        case 'PAYMENT_RECEIVED':
            // Valor já disponível na conta do salão
            break;
    }
}
```

---

## 🏃 PASSOS PARA EXECUÇÃO

### 1. Preparar Ambiente

```bash
# 1. Criar migration
php artisan make:migration add_asaas_api_key_to_tenants

# 2. Copiar código da migration (Fase 1.1)
# Editar: database/migrations/2026_03_05_000001_add_asaas_api_key_to_tenants.php

# 3. Executar migration
php artisan migrate

# 4. Verificar
php artisan migrate:status
```

---

### 2. Atualizar AsaasService

```bash
# Editar: app/Services/AsaasService.php
# Adicionar métodos da Fase 2.1:
# - criarSubcontaCompleta()
# - obterApiKeySubconta()
# - consultarStatusSubconta()
```

---

### 3. Criar Command

```bash
# Criar arquivo
php artisan make:command CreateAsaasSubaccountsWithApiKeys

# Copiar código da Fase 3.1
# Editar: app/Console/Commands/CreateAsaasSubaccountsWithApiKeys.php

# Registrar no Kernel (se necessário)
```

---

### 4. Atualizar Model Tenant

```bash
# Editar: app/Models/Tenant.php
# Adicionar código da Fase 4.1
```

---

### 5. Testar em Sandbox

#### 5.1 Configurar Sandbox

```env
# .env
ASAAS_API_URL=https://sandbox.asaas.com/api/v3
ASAAS_API_KEY=sua_api_key_sandbox_master
```

#### 5.2 Criar Subconta Teste

```bash
# Criar para tenant específico
php artisan asaas:create-subaccounts-with-keys --tenant=tenantbar

# Ver logs
tail -f storage/logs/laravel.log
```

#### 5.3 Verificar no Painel Asaas

1. Login: https://sandbox.asaas.com
2. Menu: **Configurações** → **Contas Filhas**
3. Verificar se subconta foi criada
4. Verificar status (Pendente → Aprovada)

⚠️ **IMPORTANTE:** No sandbox, aprovação pode ser automática. Em produção, pode levar até 48h.

---

### 6. Gerar API Keys para Subcontas Existentes

Se você já tem subcontas criadas (pelo comando antigo), mas sem API keys:

```bash
# Apenas gerar API keys
php artisan asaas:create-subaccounts-with-keys --only-keys

# Para tenant específico
php artisan asaas:create-subaccounts-with-keys --only-keys --tenant=tenantbar
```

---

### 7. Verificar Status

#### 7.1 Command de Verificação (criar)

```php
// app/Console/Commands/CheckAsaasSubaccounts.php
php artisan make:command CheckAsaasSubaccounts

// Listar todos os tenants com status das subcontas
php artisan asaas:check-subaccounts
```

Exemplo de output:
```
Tenant ID       | Nome          | Account ID      | API Key | Status   
----------------|---------------|-----------------|---------|----------
tenantbar       | Bar Elegante  | acc_123456      | ✅      | active   
tenantbelle     | Belle Salon   | acc_789012      | ✅      | pending  
tenantdudu      | Dudu Barber   | -               | ❌      | -        
```

---

## 🔒 SEGURANÇA DA API KEY

### Criptografia

**NUNCA armazene API keys em texto plano!**

```php
use Illuminate\Support\Facades\Crypt;

// Ao salvar
$tenant->asaas_api_key = Crypt::encryptString($apiKey);

// Ao usar
$apiKey = Crypt::decryptString($tenant->asaas_api_key);
```

### Proteção no Model

```php
// app/Models/Tenant.php

protected $hidden = [
    'asaas_api_key', // Nunca expor em JSON/API
];
```

### Rotação de Keys

```php
// Regenerar API key periodicamente
php artisan asaas:rotate-api-keys --tenant=tenantbar
```

---

## ⚠️ CUIDADOS E LIMITAÇÕES

### 1. Aprovação de Subcontas

- ⏱️ **Sandbox:** Aprovação pode ser automática
- ⏱️ **Produção:** Pode levar até **48 horas** para aprovação manual Asaas
- ⚠️ Status inicial: `PENDING` (não pode receber pagamentos)
- ✅ Após aprovação: `ACTIVE` (pode receber pagamentos)

**Solução:** Implementar verificação periódica de status:

```bash
# Criar command que roda diariamente
php artisan asaas:update-subaccount-status

# Cron
0 9 * * * cd /path/to/pagby && php artisan asaas:update-subaccount-status
```

---

### 2. Dados Obrigatórios Asaas

**Para CPF:**
- Nome completo
- Email válido
- CPF (11 dígitos)
- Data de nascimento
- Telefone celular
- Renda mensal estimada

**Para CNPJ:**
- Razão social
- Email válido
- CNPJ (14 dígitos)
- Telefone
- Tipo de empresa (MEI, LTDA, etc.)
- Faturamento mensal estimado

⚠️ **Se faltar algum dado, a criação da subconta FALHARÁ!**

---

### 3. Limites por Tipo de Conta

**MEI:**
- Faturamento: Até R$ 81.000/ano
- Sem emissão de NF-e própria (nota manual)

**LTDA/ME:**
- Sem limite (conforme atividade)
- Emissão de NF-e eletrônica

**Pessoa Física:**
- Limite menor para recebimentos
- Pode não emitir NF (depende da atividade)

---

### 4. Taxas Asaas

**Por Transação:**
- Boleto: R$ 2,99
- PIX: 0,99%
- Cartão de crédito: 3,69% + R$ 0,39

**Taxa da Subconta:**
- Asaas pode cobrar taxa mensal da subconta (verificar contrato)
- Ou a master (PagBy) assume todas as taxas

---

## 📊 MONITORAMENTO

### Métricas Importantes

```sql
-- Quantos tenants têm subconta?
SELECT COUNT(*) FROM tenants WHERE asaas_account_id IS NOT NULL;

-- Quantos têm API key?
SELECT COUNT(*) FROM tenants WHERE asaas_api_key IS NOT NULL;

-- Status das subcontas
SELECT asaas_account_status, COUNT(*) 
FROM tenants 
WHERE asaas_account_id IS NOT NULL
GROUP BY asaas_account_status;
```

### Dashboard

Criar painel admin com:
- Total de subcontas criadas
- Total ativas / pendentes / rejeitadas
- Última sincronização de status
- Alertas para subcontas pendentes há mais de 48h

---

## ✅ CHECKLIST DE IMPLEMENTAÇÃO

### Preparação
- [ ] Criar migration para campos de API key
- [ ] Executar `php artisan migrate`
- [ ] Atualizar AsaasService com novos métodos
- [ ] Criar command CreateAsaasSubaccountsWithApiKeys
- [ ] Atualizar Model Tenant

### Testes em Sandbox
- [ ] Configurar .env com credenciais sandbox
- [ ] Criar subconta teste: `php artisan asaas:create-subaccounts-with-keys --tenant=X`
- [ ] Verificar no painel Asaas se subconta foi criada
- [ ] Verificar se API key foi salva (criptografada) no banco
- [ ] Testar criação de pagamento usando API key da subconta
- [ ] Verificar se NF é emitida em nome da SUBCONTA (não do PagBy)

### Migration Produção
- [ ] Backup completo do banco de dados
- [ ] Executar migration em produção
- [ ] Configurar .env com credenciais produção
- [ ] Criar subcontas para tenants piloto (2-3 tenants)
- [ ] Validar funcionamento por 1 semana
- [ ] Criar subcontas para todos os tenants: `php artisan asaas:create-subaccounts-with-keys`
- [ ] Implementar monitoramento de status
- [ ] Configurar cron para atualização automática de status

### Validações Finais
- [ ] **CRÍTICO:** Confirmar que NF sai em nome do SALÃO (não do PagBy)
- [ ] Validar com contador que modelo está correto
- [ ] Confirmar com jurídico sobre contratos
- [ ] Documentar procedimento de onboarding de novos tenants

---

## 🎯 PRÓXIMOS PASSOS APÓS IMPLEMENTAÇÃO

1. **Migrar fluxo de pagamento B2C** (cliente → salão) para usar API keys das subcontas
2. **Manter fluxo B2B** (salão → PagBy) usando API master
3. **Implementar webhook por subconta** (cada tenant tem seu webhook)
4. **Criar painel de configuração fiscal** para cada salão
5. **Implementar onboarding** guiando salão a configurar dados fiscais no Asaas
6. **Monitorar emissão de NFs** e alertar salões em caso de falha

---

## 📚 DOCUMENTAÇÃO ASAAS

**API Reference:**
- Criar subconta: https://docs.asaas.com/reference/criar-conta-filha
- Gerar API key: https://docs.asaas.com/reference/gerar-api-key
- Status da conta: https://docs.asaas.com/reference/consultar-conta
- Webhooks: https://docs.asaas.com/docs/webhooks

**Contas Filhas (Marketplace):**
- https://docs.asaas.com/docs/contas-de-marketplace

---

**Última atualização:** 05/03/2026  
**Responsável:** Helder (PagBy)  
**Status:** 📋 Documentação completa - pronto para implementação após validações legais/contábeis

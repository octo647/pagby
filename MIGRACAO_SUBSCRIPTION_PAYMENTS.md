# Migração: Centralizar Pagamentos de Assinaturas nos Tenants

## 📋 Contexto

**Problema Identificado:**
O sistema abandonou o modelo de split de pagamentos e cada tenant possui sua própria subconta Asaas, mas o código ainda registra pagamentos de planos de assinatura na **tabela central** `pagby.tenants_plans_payments` ao invés da **tabela do tenant** `tenant{id}.subscriptions_payments`.

**Arquitetura Atual (Incorreta):**
```
AsaasSubscriptionController
    ↓
TenantsPlansPayment (pagby.tenants_plans_payments)  ❌ Base central
    ↓
Dados isolados da base do tenant
```

**Arquitetura Correta (Já existe, não sendo usada):**
```
AsaasSubscriptionController
    ↓
Subscription (tenant{id}.subscriptions)  ✅ Base do tenant
    ↓
SubscriptionPayment (tenant{id}.subscriptions_payments)  ✅ Base do tenant
    ↓
Dados integrados com User, Plan, etc.
```

---

## 🗂️ Modelos Envolvidos

### ❌ **Modelo Antigo (DEPRECAR)**

**TenantsPlansPayment** (`pagby.tenants_plans_payments`)
- Criado: 2025-10-09
- Localização: `app/Models/TenantsPlansPayment.php`
- Conexão: `on('mysql')` (base central)
- Campos principais:
  - `external_id`, `tenant_id`, `plan_id`, `amount`, `status`
  - `asaas_subscription_id`, `asaas_payment_id`, `asaas_data`
- **Problema:** Isolado do contexto do tenant (não vê User, Plan local)

### ✅ **Modelos Corretos (USAR)**

**1. Subscription** (`tenant{id}.subscriptions`)
- Criado: 2025-05-29
- Localização: `app/Models/Subscription.php`
- Relacionamentos: `belongsTo(User)`, `belongsTo(Plan)`
- Campos principais:
  - `user_id`, `plan_id`, `start_date`, `end_date`, `status`
  - `created_by`, `updated_by` (auditoria)

**2. SubscriptionPayment** (`tenant{id}.subscriptions_payments`)
- Criado: 2026-03-08 ✅ (Migration recente!)
- Localização: `app/Models/SubscriptionPayment.php`
- Relacionamento: `belongsTo(Subscription)`
- Campos principais:
  - `subscription_id`, `asaas_payment_id`, `asaas_invoice_url`
  - `amount`, `net_value`, `billing_type`, `status`
  - `due_date`, `payment_date`, `confirmed_at`, `received_at`
- **Comentário na migration:** "modelo SEM split - subconta do salão"

---

## 🔍 Pontos de Uso do Modelo Antigo

### Controllers que usam TenantsPlansPayment:

1. **app/Livewire/AsaasSubscriptionController.php** (10+ ocorrências)
   - `assinarPlano()` - linha 160: cria registro na base central
   - `cancelarAssinatura()` - linha 44, 46, 75
   - `webhook()` - linha 368, 405
   - `consultarStatusPagamento()` - linha 565, 605, 654

2. **app/Http/Controllers/AsaasSubscriptionController.php** (10+ ocorrências)
   - Mesma lógica duplicada do Livewire

3. **app/Http/Controllers/PagBySubscriptionController.php** (2 ocorrências)
   - `webhook()` - linha 746, 748: busca fallback em tenants_plans_payments

4. **scripts/check-payment-status.sh**
   - Query direta: `TenantsPlansPayment::on('mysql')`

---

## 📝 Plano de Migração

### **Fase 1: Preparação (1-2 horas)**

#### 1.1. Adicionar campos faltantes no modelo Subscription
```php
// app/Models/Subscription.php - verificar se existem:
'asaas_subscription_id',  // ID da assinatura recorrente no Asaas
'asaas_customer_id',      // ID do cliente no Asaas
'external_reference',     // Referência única
'next_due_date',          // Próxima cobrança
```

#### 1.2. Criar migration para adicionar campos (se necessário)
```bash
php artisan make:migration add_asaas_subscription_fields_to_subscriptions_table --path=database/migrations/tenant
```

---

### **Fase 2: Criar Adapter/Service (2-3 horas)**

#### 2.1. Criar `TenantSubscriptionService`
```php
// app/Services/TenantSubscriptionService.php

namespace App\Services;

use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\Plan;
use App\Services\AsaasService;

class TenantSubscriptionService
{
    private AsaasService $asaas;

    public function __construct(AsaasService $asaas)
    {
        $this->asaas = $asaas;
    }

    /**
     * Criar assinatura de plano para cliente do tenant
     */
    public function createSubscription(
        int $userId, 
        int $planId, 
        array $customerData
    ): Subscription {
        $plan = Plan::findOrFail($planId);
        
        // Criar assinatura no Asaas (subconta do tenant)
        $asaasSubscription = $this->asaas->criarAssinatura(
            $customerData,
            [
                'cycle' => 'MONTHLY',
                'value' => $plan->price,
                'description' => "Assinatura {$plan->name}",
                'nextDueDate' => now()->format('Y-m-d'),
                'externalReference' => 'sub-' . uniqid(),
            ]
        );

        // Salvar na base do tenant
        $subscription = Subscription::create([
            'user_id' => $userId,
            'plan_id' => $planId,
            'asaas_subscription_id' => $asaasSubscription['id'],
            'asaas_customer_id' => $customerData['id'],
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'status' => 'Ativo',
            'created_by' => auth()->id(),
        ]);

        return $subscription;
    }

    /**
     * Registrar pagamento da assinatura (chamado pelo webhook)
     */
    public function recordPayment(
        string $asaasPaymentId,
        array $webhookData
    ): ?SubscriptionPayment {
        // Buscar assinatura pelo subscription_id do Asaas
        $subscription = Subscription::where(
            'asaas_subscription_id', 
            $webhookData['payment']['subscription'] ?? null
        )->first();

        if (!$subscription) {
            \Log::warning('Subscription not found for payment', [
                'asaas_payment_id' => $asaasPaymentId,
            ]);
            return null;
        }

        // Verificar se pagamento já existe
        $payment = SubscriptionPayment::where(
            'asaas_payment_id', 
            $asaasPaymentId
        )->first();

        if ($payment) {
            // Atualizar status existente
            $payment->update([
                'status' => $this->mapAsaasStatus($webhookData['payment']['status']),
                'payment_date' => $webhookData['payment']['clientPaymentDate'] ?? null,
                'asaas_data' => $webhookData,
            ]);
        } else {
            // Criar novo registro de pagamento
            $payment = SubscriptionPayment::create([
                'subscription_id' => $subscription->id,
                'asaas_payment_id' => $asaasPaymentId,
                'asaas_invoice_url' => $webhookData['payment']['invoiceUrl'] ?? null,
                'amount' => $webhookData['payment']['value'],
                'net_value' => $webhookData['payment']['netValue'] ?? null,
                'billing_type' => $webhookData['payment']['billingType'],
                'due_date' => $webhookData['payment']['dueDate'],
                'payment_date' => $webhookData['payment']['clientPaymentDate'] ?? null,
                'status' => $this->mapAsaasStatus($webhookData['payment']['status']),
                'asaas_data' => $webhookData,
            ]);
        }

        // Atualizar end_date da assinatura
        if ($payment->isReceived()) {
            $subscription->update([
                'end_date' => $subscription->end_date->addMonth(),
                'status' => 'Ativo',
            ]);
        }

        return $payment;
    }

    /**
     * Mapear status Asaas → SubscriptionPayment
     */
    private function mapAsaasStatus(string $asaasStatus): string
    {
        return match($asaasStatus) {
            'PENDING' => 'pending',
            'CONFIRMED' => 'confirmed',
            'RECEIVED', 'RECEIVED_IN_CASH' => 'received',
            'OVERDUE' => 'overdue',
            'REFUNDED' => 'refunded',
            'CANCELLED' => 'cancelled',
            default => 'pending',
        };
    }
}
```

---

### **Fase 3: Refatorar Controllers (3-4 horas)**

#### 3.1. Atualizar AsaasSubscriptionController

**ANTES (linha 160):**
```php
$payment = TenantsPlansPayment::on('mysql')->create([
    'external_id' => 'asaas-subscription-' . uniqid(),
    'tenant_id' => $tenantId,
    'plan_id' => $planId,
    'plan' => $plan->name,
    'amount' => $plan->price,
    'status' => 'PENDING',
]);
```

**DEPOIS:**
```php
$subscriptionService = app(TenantSubscriptionService::class);

$subscription = $subscriptionService->createSubscription(
    userId: $userId,
    planId: $planId,
    customerData: [
        'name' => $userName,
        'email' => $userEmail,
        'cpfCnpj' => $cpfCnpj,
    ]
);
```

#### 3.2. Atualizar webhook() para usar SubscriptionPayment

**ANTES (linha 368):**
```php
$payment = TenantsPlansPayment::on('mysql')
    ->where('asaas_subscription_id', $subscriptionId)
    ->first();

$payment->status = 'ACTIVE';
$payment->save();
```

**DEPOIS:**
```php
$subscriptionService = app(TenantSubscriptionService::class);

$payment = $subscriptionService->recordPayment(
    asaasPaymentId: $paymentData['id'],
    webhookData: $request->all()
);
```

---

### **Fase 4: Migração de Dados (2-3 horas)**

#### 4.1. Script de migração de dados existentes

```php
// database/migrations/2026_03_11_migrate_tenants_plans_to_subscriptions.php

use App\Models\TenantsPlansPayment;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use Stancl\Tenancy\Database\Models\Tenant;

public function up(): void
{
    $oldPayments = TenantsPlansPayment::on('mysql')
        ->whereNotNull('asaas_subscription_id')
        ->get();

    foreach ($oldPayments as $oldPayment) {
        try {
            $tenant = Tenant::find($oldPayment->tenant_id);
            
            if (!$tenant) continue;

            // Inicializar contexto do tenant
            tenancy()->initialize($tenant);

            // Buscar ou criar assinatura
            $subscription = Subscription::firstOrCreate(
                ['asaas_subscription_id' => $oldPayment->asaas_subscription_id],
                [
                    'user_id' => 1, // Atribuir ao admin do tenant ou primeiro user
                    'plan_id' => $oldPayment->plan_id,
                    'start_date' => $oldPayment->created_at,
                    'end_date' => $oldPayment->expires_at ?? now()->addMonth(),
                    'status' => $this->mapOldStatus($oldPayment->status),
                    'created_by' => 1,
                ]
            );

            // Criar registro de pagamento se houver asaas_payment_id
            if ($oldPayment->asaas_payment_id) {
                SubscriptionPayment::firstOrCreate(
                    ['asaas_payment_id' => $oldPayment->asaas_payment_id],
                    [
                        'subscription_id' => $subscription->id,
                        'amount' => $oldPayment->amount,
                        'billing_type' => $oldPayment->payment_method ?? 'UNDEFINED',
                        'due_date' => $oldPayment->created_at,
                        'status' => $this->mapOldStatus($oldPayment->status),
                        'asaas_data' => json_decode($oldPayment->asaas_data, true),
                    ]
                );
            }

            // Marcar como migrado
            $oldPayment->update(['migrated' => true]);

            tenancy()->end();

        } catch (\Exception $e) {
            \Log::error('Migration failed for payment', [
                'payment_id' => $oldPayment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

private function mapOldStatus(string $oldStatus): string
{
    return match($oldStatus) {
        'PENDING' => 'pending',
        'ACTIVE', 'APPROVED' => 'received',
        'CANCELLED' => 'cancelled',
        'OVERDUE' => 'overdue',
        default => 'pending',
    };
}
```

---

### **Fase 5: Testes (2-3 horas)**

#### 5.1. Testes unitários
```php
// tests/Feature/TenantSubscriptionServiceTest.php

public function test_can_create_subscription()
{
    $user = User::factory()->create();
    $plan = Plan::factory()->create(['price' => 100]);
    
    $service = app(TenantSubscriptionService::class);
    
    $subscription = $service->createSubscription(
        userId: $user->id,
        planId: $plan->id,
        customerData: ['name' => 'Test', 'email' => 'test@test.com']
    );
    
    $this->assertDatabaseHas('subscriptions', [
        'user_id' => $user->id,
        'plan_id' => $plan->id,
        'status' => 'Ativo',
    ]);
}
```

#### 5.2. Teste manual de fluxo completo
1. Cliente seleciona plano
2. Sistema cria Subscription + gera cobrança Asaas
3. Cliente paga
4. Webhook chega e cria SubscriptionPayment
5. Assinatura renovada (end_date += 1 mês)

---

### **Fase 6: Deploy e Monitoramento (1-2 horas)**

#### 6.1. Checklist de deploy
- [ ] Backup da tabela `tenants_plans_payments`
- [ ] Rodar migrations nos tenants
- [ ] Deploy do código refatorado
- [ ] Executar script de migração de dados
- [ ] Monitorar logs por 24h

#### 6.2. Rollback plan
Se houver problema:
```bash
git revert <commit>
php artisan migrate:rollback --path=database/migrations/tenant
```

---

## 🗑️ Fase 7: Limpeza (após 30 dias)

Após validar que o novo sistema está estável:

1. **Deprecar modelo antigo:**
```php
// app/Models/TenantsPlansPayment.php
/**
 * @deprecated Usar Subscription + SubscriptionPayment do tenant
 */
class TenantsPlansPayment extends Model
{
    // ...
}
```

2. **Remover referências:**
- AsaasSubscriptionController (código antigo comentado)
- scripts/check-payment-status.sh

3. **Criar migration de cleanup (OPCIONAL):**
```php
// Após 30 dias sem incidentes
Schema::dropIfExists('tenants_plans_payments');
```

---

## 📊 Vantagens da Nova Arquitetura

### ✅ **Antes (Modelo Central)**
❌ Dados isolados da base central  
❌ Sem integração com User/Plan do tenant  
❌ Queries `on('mysql')` em todo código  
❌ Auditoria limitada  

### ✅ **Depois (Modelo Tenant)**
✅ Dados no contexto correto (tenant)  
✅ Relacionamentos com User/Plan funcionam  
✅ Queries normais (contexto automático)  
✅ Auditoria completa (created_by, updated_by)  
✅ Histórico de pagamentos por assinatura  
✅ Relatórios por tenant simplificados  

---

## 🚀 Execução Recomendada

### **Abordagem Incremental:**

1. **Dia 1-2:** Criar TenantSubscriptionService + testes
2. **Dia 3:** Refatorar AsaasSubscriptionController (novas assinaturas)
3. **Dia 4:** Migrar dados existentes
4. **Dia 5-7:** Monitoramento e ajustes
5. **Dia 30+:** Deprecar modelo antigo

### **Prioridade Alta:**
- Webhook handler (afeta pagamentos atuais)
- Criação de novas assinaturas

### **Prioridade Média:**
- Migração de dados históricos
- Testes de integração

### **Prioridade Baixa:**
- Limpeza de código antigo
- Remoção da tabela central

---

## 📞 Notas Finais

- **Compatibilidade:** Manter TenantsPlansPayment temporariamente para consultas read-only de dados antigos
- **Webhook:** Prioridade máxima, afeta todos os pagamentos futuros
- **Documentação:** Atualizar SUBSCRIPTION_SYSTEM.md após migração
- **Comunicação:** Avisar equipe sobre mudanças no modelo de dados

---

**Data do documento:** 11/03/2026  
**Status:** Planejamento  
**Responsável:** Time de Desenvolvimento

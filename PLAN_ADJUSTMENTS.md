# Sistema de Ajustes de Plano - PagBy

## 📋 Visão Geral

Sistema completo para gerenciar ajustes de planos (upgrade/downgrade) de forma proporcional, garantindo que o cliente **não perca o que já pagou** e seja cobrado/creditado apenas pela diferença proporcional dos dias restantes.

---

## 🗄️ Estrutura de Dados

### Tabela: `plan_adjustments`

Armazena todos os ajustes de plano realizados pelos tenants.

```sql
- id: bigint (PK)
- tenant_id: string (FK → tenants)
- type: enum('credit', 'debit')
- amount: decimal(10,2)
- employee_count_before: int
- employee_count_after: int
- plan_period: string (mensal, trimestral, etc)
- days_remaining: int
- percentage_remaining: decimal(5,2)
- status: enum('pending', 'applied', 'paid', 'cancelled')
- asaas_payment_id: string (nullable)
- asaas_invoice_url: string (nullable)
- applied_at: timestamp (nullable)
- paid_at: timestamp (nullable)
- notes: text (nullable)
- created_at: timestamp
- updated_at: timestamp
```

---

## 🔄 Fluxo de Funcionamento

### 1️⃣ Cliente Solicita Ajuste

**Localização**: [Livewire/Proprietario/MeuPagby.php](../app/Livewire/Proprietario/MeuPagby.php)

1. Cliente acessa "Meu PagBy"
2. Clica em "Ajustar" ao lado do número de funcionários
3. Informa o novo número desejado
4. Sistema calcula automaticamente:
   - Valor proporcional do plano atual
   - Valor proporcional do novo plano
   - Diferença (crédito ou débito)
   - Dias restantes do período

**Método**: `calcularAjuste()`

---

### 2️⃣ Cálculo Proporcional

**Localização**: [Services/PagbyService.php](../app/Services/PagbyService.php)

**Método**: `calcularAjusteProporcional()`

```php
// Exemplo de cálculo
Plano atual: 2 funcionários, trimestral (90 dias), R$ 90,00
Já decorrido: 30 dias
Dias restantes: 60 dias (66,67% do período)

Upgrade para 3 funcionários:
- Valor novo plano completo: R$ 117,00
- Valor proporcional novo (60 dias): R$ 78,00
- Valor proporcional atual (60 dias): R$ 60,00
- DÉBITO a pagar: R$ 18,00 ✅

Downgrade para 1 funcionário:
- Valor novo plano completo: R$ 60,00
- Valor proporcional novo (60 dias): R$ 40,00
- Valor proporcional atual (60 dias): R$ 60,00
- CRÉDITO a receber: R$ 20,00 ✅
```

**Retorno**:
```php
[
    'ajuste' => 18.00,
    'tipo' => 'debito', // ou 'credito'
    'dias_restantes' => 60,
    'percentual_restante' => 66.67,
    'valor_plano_atual' => 90.00,
    'valor_novo_plano' => 117.00,
]
```

---

### 3️⃣ Confirmação do Ajuste

#### A) **DÉBITO (Upgrade - Adicionar Funcionários)**

1. **Cria registro** em `plan_adjustments` com `status='pending'`
2. **Gera cobrança no Asaas** via `AsaasService::criarCobranca()`
3. **Salva** `asaas_payment_id` e `asaas_invoice_url`
4. **Atualiza** `tenant.employee_count` imediatamente
5. **Aguarda pagamento** via webhook do Asaas
6. Quando pago → `status='paid'`, `paid_at=now()`

#### B) **CRÉDITO (Downgrade - Remover Funcionários)**

1. **Cria registro** em `plan_adjustments` com `status='pending'`
2. **Atualiza** `tenant.employee_count` imediatamente
3. **Crédito fica pendente** até próxima renovação
4. Na renovação → crédito é **aplicado automaticamente** ao valor
5. Após aplicação → `status='applied'`, `applied_at=now()`

---

### 4️⃣ Webhook Asaas (Confirmação de Pagamento)

**Localização**: [PagBySubscriptionController.php](../app/Http/Controllers/PagBySubscriptionController.php)

**Endpoint**: `POST /pagby-subscription/webhook`

```php
// Webhook recebe:
{
    "event": "PAYMENT_RECEIVED",
    "payment": {
        "id": "pay_xxx",
        "status": "RECEIVED"
    }
}

// Sistema verifica:
1. É um PlanAdjustment? → Marca como pago
2. É um PagByPayment? → Processa normalmente
```

**Tratamento**:
```php
$adjustment = PlanAdjustment::where('asaas_payment_id', $asaasPaymentId)->first();
if ($adjustment && in_array($asaasStatus, ['RECEIVED', 'CONFIRMED'])) {
    $adjustment->markAsPaid(); // status='paid', paid_at=now()
}
```

---

### 5️⃣ Aplicação de Créditos na Renovação

**Localização**: [PagBySubscriptionController@renewSubscription](../app/Http/Controllers/PagBySubscriptionController.php)

```php
// 1. Buscar créditos pendentes
$pendingCredits = PlanAdjustment::getPendingCredits($tenantId);
// Ex: R$ 20,00

// 2. Calcular valor base
$baseAmount = $pagbyService->calcularValorPlano($numFuncionarios, $plan);
// Ex: R$ 90,00

// 3. Aplicar créditos
$finalAmount = max(0, $baseAmount - $pendingCredits);
// Ex: R$ 90,00 - R$ 20,00 = R$ 70,00

// 4. Criar pagamento com valor reduzido
PagByPayment::create(['amount' => $finalAmount]);

// 5. Marcar créditos como aplicados
PlanAdjustment::applyPendingCredits($tenantId);
// status='applied', applied_at=now()
```

---

## 🎯 Estados do Ajuste

| Status | Descrição | Quando |
|--------|-----------|--------|
| `pending` | Aguardando pagamento (débito) ou aplicação (crédito) | Após confirmação do ajuste |
| `paid` | Débito pago pelo cliente | Webhook Asaas confirma pagamento |
| `applied` | Crédito aplicado na renovação | Cliente renova plano |
| `cancelled` | Ajuste cancelado (não usado atualmente) | - |

---

## 📊 Métodos Principais

### Model: `PlanAdjustment`

```php
// Buscar soma de créditos pendentes
PlanAdjustment::getPendingCredits($tenantId)

// Aplicar todos os créditos pendentes
PlanAdjustment::applyPendingCredits($tenantId)

// Marcar débito como pago
$adjustment->markAsPaid()
```

### Service: `PagbyService`

```php
// Calcular valor do plano
$pagbyService->calcularValorPlano($numFuncionarios, $periodicidade)

// Calcular ajuste proporcional
$pagbyService->calcularAjusteProporcional(
    $funcionariosAtuais,
    $novoNumeroFuncionarios,
    $periodicidade,
    $dataInicio,
    $dataFim
)
```

### Service: `AsaasService`

```php
// Criar cobrança única
$asaasService->criarCobranca($customerData, $paymentData)

// Consultar status
$asaasService->consultarCobranca($asaasPaymentId)
```

---

## 🔐 Segurança

- ✅ Validações de valor mínimo (não permite negativo)
- ✅ Logs detalhados de todas as operações
- ✅ Transações atômicas no banco
- ✅ Webhook autenticado pelo Asaas
- ✅ Status controlados por enum

---

## 🎨 Interface do Usuário

### Modal de Ajuste

**View**: [meu-pagby.blade.php](../resources/views/livewire/proprietario/meu-pagby.blade.php)

**Elementos**:
- Campo para novo número de funcionários
- Botão "Calcular Ajuste"
- Painel de detalhes com:
  - Valores dos planos (atual vs novo)
  - Dias restantes e percentual
  - Valores proporcionais
  - Resultado final (débito/crédito)
- Alertas visuais:
  - 🔴 Vermelho para débito
  - 🟢 Verde para crédito

---

## 📝 Exemplo Prático

### Cenário: Upgrade de 2 para 3 funcionários

```
Cliente: Salão Exemplo
Plano atual: Trimestral (3 meses)
Funcionários: 2
Valor pago: R$ 90,00
Data início: 01/01/2025
Data fim: 01/04/2025
Data atual: 01/02/2025 (1 mês decorrido)

Nova solicitação: 3 funcionários

Cálculo:
- Dias totais: 90
- Dias decorridos: 30
- Dias restantes: 60 (66.67%)

Plano atual (2 func):
- Valor total: R$ 90,00
- Valor proporcional (60 dias): R$ 60,00

Novo plano (3 func):
- Valor total: R$ 117,00
- Valor proporcional (60 dias): R$ 78,00

Resultado:
- Tipo: DÉBITO
- Valor: R$ 18,00
- Ação: Cobrar via Asaas

Fluxo:
1. ✅ Cliente confirma ajuste
2. ✅ Sistema cria registro em plan_adjustments
3. ✅ Sistema gera cobrança no Asaas (R$ 18,00)
4. ✅ Cliente recebe link de pagamento por email
5. ✅ Cliente paga via PIX/Boleto/Cartão
6. ✅ Webhook Asaas confirma pagamento
7. ✅ Sistema marca ajuste como 'paid'
8. ✅ tenant.employee_count = 3 (já estava atualizado)
```

---

## 🚀 Deploy

### Migração

```bash
php artisan migrate
```

### Configuração Webhook Asaas

1. Acessar painel Asaas
2. Configurações → Webhooks
3. URL: `https://seu-dominio.com/pagby-subscription/webhook`
4. Eventos: `PAYMENT_RECEIVED`, `PAYMENT_CONFIRMED`

### Testes

```bash
# Testar cálculo proporcional
$pagbyService = new \App\Services\PagbyService();
$result = $pagbyService->calcularAjusteProporcional(2, 3, 'trimestral', 
    Carbon::parse('2025-01-01'), Carbon::parse('2025-04-01'));
dd($result);
```

---

## 📞 Suporte

Em caso de dúvidas sobre ajustes de plano:
- Verificar logs: `storage/logs/laravel.log`
- Consultar ajustes: `select * from plan_adjustments where tenant_id = 'xxx'`
- Verificar créditos: `PlanAdjustment::getPendingCredits($tenantId)`

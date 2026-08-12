# Migração do Sistema de Assinaturas: MercadoPago → Asaas

## Visão Geral

Este documento descreve a migração do sistema de assinaturas do **MercadoPago** para o **Asaas**, motivada pela necessidade de implementar **split de pagamentos** (divisão de receita) entre a plataforma PagBy e os tenants.

### Problema Identificado

- O MercadoPago não suporta split de pagamentos em planos de assinatura
- Fazer o split manualmente pelo PagBy causaria problemas fiscais e legais:
  - Bitributação (pagamento duplicado de impostos)
  - Possível caracterização ilegal como intermediário de pagamentos

### Solução: Asaas

O Asaas oferece suporte nativo a **split de pagamentos mesmo em assinaturas recorrentes**, através do sistema de subcontas (wallets).

---

## Arquitetura do Sistema

### Fluxo de Pagamento com Split

```
Cliente → Assinatura PagBy → Asaas
                                ↓
                          Split Automático
                                ↓
                    ┌───────────┴───────────┐
                    ↓                       ↓
            Conta PagBy (10%)      Subconta Tenant (90%)
```

### Componentes Criados

1. **AsaasService** (estendido): `/app/Services/AsaasService.php`
   - `criarAssinatura()`: Cria assinatura recorrente com split
   - `consultarAssinatura()`: Consulta status da assinatura
   - `cancelarAssinatura()`: Cancela assinatura
   - `criarSubconta()`: Cria subconta (wallet) para tenants
   - `listarCobrancasAssinatura()`: Lista cobranças de uma assinatura

2. **AsaasSubscriptionController**: `/app/Http/Controllers/AsaasSubscriptionController.php`
   - Substitui o SubscriptionController original
   - Mesma estrutura de rotas e views para facilitar migração
   - Webhook handler específico para eventos do Asaas

3. **Migration**: `2026_01_01_000001_add_asaas_fields_to_tenants_and_payments.php`
   - Adiciona campos `asaas_wallet_id` e `asaas_account_data` na tabela `tenants`
   - Adiciona campos `asaas_subscription_id` e `asaas_data` na tabela `tenants_plans_payments`

4. **Command**: `CreateAsaasAccountsForTenants`
   - Comando artisan para criar subcontas Asaas para tenants existentes
   - Uso: `php artisan tenants:create-asaas-accounts`

5. **Routes**: `/routes/asaas.php`
   - Rotas espelhadas das rotas do MercadoPago
   - Prefixo: `/asaas-assinatura/`

---

## Processo de Migração

### 1. Pré-requisitos

#### a) Configurar Credenciais Asaas

Adicionar no `.env`:

```env
ASAAS_API_KEY=sua_api_key_aqui
ASAAS_API_URL=https://www.asaas.com/api/v3
```

Para ambiente de testes/sandbox:
```env
ASAAS_API_URL=https://sandbox.asaas.com/api/v3
```

#### b) Verificar Configuração

Arquivo `/config/services.php` já está configurado:

```php
'asaas' => [
    'api_url' => env('ASAAS_API_URL', 'https://www.asaas.com/api/v3'),
    'api_key' => env('ASAAS_API_KEY'),
],
```

### 2. Executar Migration

```bash
php artisan migrate
```

Isso adicionará os campos necessários nas tabelas `tenants` e `tenants_plans_payments`.

### 3. Criar Subcontas para Tenants Existentes

**IMPORTANTE**: Cada tenant precisa ter uma subconta (wallet) no Asaas para receber sua parte do split.

#### Opção A: Criar para todos os tenants

```bash
php artisan tenants:create-asaas-accounts
```

#### Opção B: Criar para um tenant específico

```bash
php artisan tenants:create-asaas-accounts --tenant=tenant123
```

#### Opção C: Recriar contas (forçar)

```bash
php artisan tenants:create-asaas-accounts --force
```

**Nota**: O comando solicitará CPF/CNPJ e telefone se não estiverem disponíveis no banco de dados.

### 4. Atualizar Model Tenant

Se ainda não existir, adicionar os campos ao model `/app/Models/Tenant.php`:

```php
protected $fillable = [
    // ... campos existentes
    'asaas_wallet_id',
    'asaas_account_data',
];

protected $casts = [
    // ... casts existentes
    'asaas_account_data' => 'array',
];
```

### 5. Atualizar Rotas

Duas opções:

#### Opção A: Substituir rotas existentes (migração completa)

Editar `/routes/web.php` ou `/routes/tenant.php`:

```php
// Comentar rotas antigas do MercadoPago
// require __DIR__.'/mercadopago-subscription.php';

// Incluir rotas do Asaas
require __DIR__.'/asaas.php';
```

#### Opção B: Manter ambos (período de transição)

```php
// Rotas antigas (MercadoPago) - manter para assinaturas existentes
require __DIR__.'/mercadopago-subscription.php';

// Rotas novas (Asaas) - para novas assinaturas
require __DIR__.'/asaas.php';
```

### 6. Atualizar Views e Formulários

Alterar os formulários de seleção de planos para apontar para a nova rota:

**Antes:**
```html
<form action="{{ route('tenant-assinatura.store') }}" method="POST">
```

**Depois:**
```html
<form action="{{ route('asaas-assinatura.store') }}" method="POST">
```

### 7. Configurar Webhook no Painel Asaas

1. Acesse o painel do Asaas
2. Vá em **Configurações → Webhooks**
3. Adicione a URL do webhook:
   ```
   https://pagby.com.br/asaas-assinatura/webhook
   ```
4. Selecione os eventos:
   - `PAYMENT_CREATED`
   - `PAYMENT_RECEIVED`
   - `PAYMENT_CONFIRMED`
   - `PAYMENT_OVERDUE`
   - `PAYMENT_DELETED`

### 8. Testar em Ambiente de Sandbox

Antes de ir para produção, teste com a API de sandbox:

```env
ASAAS_API_URL=https://sandbox.asaas.com/api/v3
ASAAS_API_KEY=sua_sandbox_key
```

---

## Diferenças Importantes

### Status de Pagamento

| MercadoPago | Asaas | Descrição |
|------------|-------|-----------|
| `pending` | `PENDING` | Aguardando pagamento |
| `authorized` / `approved` | `ACTIVE` / `CONFIRMED` | Pagamento aprovado |
| `cancelled` | `CANCELLED` | Assinatura cancelada |
| `paused` | - | Não existe no Asaas |
| - | `OVERDUE` | Pagamento vencido |
| - | `EXPIRED` | Assinatura expirada |

### Ciclos de Cobrança

O Asaas suporta os seguintes ciclos:
- `WEEKLY` - Semanal
- `BIWEEKLY` - Quinzenal
- `MONTHLY` - Mensal (padrão)
- `QUARTERLY` - Trimestral
- `SEMIANNUALLY` - Semestral
- `YEARLY` - Anual

### Métodos de Pagamento

O campo `billingType` aceita:
- `BOLETO` - Apenas boleto
- `CREDIT_CARD` - Apenas cartão de crédito
- `DEBIT_CARD` - Cartão de débito
- `PIX` - PIX
- `UNDEFINED` - Cliente escolhe (recomendado)

---

## Configuração do Split

### Percentual (Recomendado)

No `AsaasSubscriptionController`, linha ~193:

```php
$splitData = [
    'walletId' => $tenant->asaas_wallet_id,
    'percentualValue' => 90, // 90% para o tenant, 10% para PagBy
];
```

### Valor Fixo (Alternativa)

```php
$splitData = [
    'walletId' => $tenant->asaas_wallet_id,
    'fixedValue' => 10.00, // R$ 10 para PagBy, resto para tenant
];
```

**Importante**: Use apenas `percentualValue` OU `fixedValue`, não ambos.

---

## Tratamento de Assinaturas Existentes

### Cenário 1: Manter MercadoPago para Assinaturas Ativas

Recomendado para evitar interrupções.

**Estratégia:**
1. Manter ambos os controllers ativos
2. Novas assinaturas usam Asaas
3. Assinaturas existentes continuam no MercadoPago
4. Migrar gradualmente quando renovarem

### Cenário 2: Migração Forçada

**Passos:**
1. Cancelar assinatura no MercadoPago
2. Criar nova assinatura no Asaas
3. Aplicar desconto/cortesia para compensar

**Script exemplo:**

```php
// Em um Command ou Controller
$oldSubscriptions = TenantsPlansPayment::where('mp_payment_id', '!=', null)
    ->where('status', 'active')
    ->get();

foreach ($oldSubscriptions as $oldSub) {
    // 1. Cancelar no MercadoPago
    // (usar código existente)
    
    // 2. Criar no Asaas
    $asaasController->store(/* dados do tenant */);
    
    // 3. Notificar tenant
}
```

---

## Monitoramento e Logs

### Logs Importantes

Todos os eventos são logados em `/storage/logs/laravel.log`:

```
🔄 Processando tenant: tenant123 - Salão Exemplo
💰 Split configurado: 90% para tenant, 10% para PagBy
✅ Assinatura Asaas criada: sub_abc123
```

### Verificar Status de Assinatura

```bash
curl https://pagby.com.br/asaas-assinatura/check-status/123
```

### Debug de Pagamento

```bash
curl https://pagby.com.br/asaas-assinatura/debug/123
```

**IMPORTANTE**: Remover ou proteger rota de debug em produção!

---

## Reversão (Rollback)

Se necessário reverter para MercadoPago:

1. Restaurar rotas antigas:
   ```php
   require __DIR__.'/mercadopago-subscription.php';
   // require __DIR__.'/asaas.php'; // Comentar
   ```

2. Reverter migration (opcional):
   ```bash
   php artisan migrate:rollback
   ```

3. Atualizar formulários para apontar para rotas antigas

---

## Próximos Passos

### Funcionalidades Adicionais

1. **Dashboard de Split**: Exibir quanto o tenant recebeu/receberá
2. **Relatórios Financeiros**: Detalhar splits por período
3. **Configuração de Split Personalizável**: Permitir diferentes percentuais por tenant
4. **Notificações**: Alertar tenant quando receber transferência
5. **API de Consulta**: Endpoint para tenants consultarem seus recebimentos

### Melhorias de Segurança

1. **Validação de Webhook**: Implementar validação de assinatura Asaas
2. **Rate Limiting**: Limitar chamadas ao webhook
3. **Auditoria**: Log detalhado de todas as operações financeiras

---

## Suporte e Documentação

### Documentação Oficial Asaas

- **API Reference**: https://docs.asaas.com/reference
- **Split de Pagamentos**: https://docs.asaas.com/docs/split-de-pagamentos
- **Assinaturas**: https://docs.asaas.com/docs/assinaturas-recorrentes
- **Subcontas**: https://docs.asaas.com/docs/contas-de-marketplace

### Contato Asaas

- **Suporte**: suporte@asaas.com
- **Telefone**: (11) 4950-2209
- **Chat**: Disponível no painel

---

## Checklist de Migração

- [ ] Configurar credenciais Asaas no `.env`
- [ ] Executar migration `add_asaas_fields_to_tenants_and_payments`
- [ ] Criar subcontas para tenants (`php artisan tenants:create-asaas-accounts`)
- [ ] Atualizar rotas (incluir `/routes/asaas.php`)
- [ ] Atualizar formulários de assinatura
- [ ] Configurar webhook no painel Asaas
- [ ] Testar em sandbox
- [ ] Testar assinatura completa (criação → pagamento → ativação)
- [ ] Verificar split de pagamento na dashboard Asaas
- [ ] Monitorar logs por 24h após deploy
- [ ] Documentar para equipe
- [ ] Planejar migração de assinaturas existentes (se necessário)

---

## Troubleshooting

### Erro: "Erro ao criar cliente no Asaas"

**Causa**: Dados incompletos ou inválidos (CPF/CNPJ, email)

**Solução**: Verificar que o tenant tem `owner_email` e `owner_cpf_cnpj` válidos

### Erro: "Tenant sem wallet_id"

**Causa**: Subconta não foi criada

**Solução**: Executar `php artisan tenants:create-asaas-accounts --tenant=X`

### Webhook não está sendo chamado

**Causa**: URL não configurada ou firewall bloqueando

**Solução**: 
1. Verificar URL no painel Asaas
2. Testar manualmente: `curl -X POST https://pagby.com.br/asaas-assinatura/webhook`
3. Verificar logs do servidor web

### Split não está acontecendo

**Causa**: `wallet_id` inválido ou não configurado

**Solução**:
1. Verificar `$tenant->asaas_wallet_id` no banco
2. Confirmar subconta no painel Asaas
3. Verificar logs: "Split configurado" deve aparecer

---

## Perguntas Frequentes

**P: Posso usar Asaas e MercadoPago simultaneamente?**  
R: Sim, mantenha ambas as rotas ativas durante a transição.

**P: O split é automático?**  
R: Sim, o Asaas transfere automaticamente para a subconta do tenant conforme configurado.

**P: Quando o tenant recebe o dinheiro?**  
R: Conforme configuração de repasse no Asaas (pode ser configurado no painel).

**P: E se o tenant não tiver CPF/CNPJ cadastrado?**  
R: O comando solicitará via prompt, ou você pode preencher manualmente antes.

**P: Posso alterar o percentual do split depois?**  
R: Não na assinatura criada, mas pode alterar para novas assinaturas modificando o código.

**P: O Asaas cobra taxas adicionais?**  
R: Sim, consulte a tabela de preços do Asaas. As taxas são descontadas antes do split.

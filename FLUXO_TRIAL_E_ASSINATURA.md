# Fluxo de Trial e Assinatura - PagBy

## Novo Modelo de Negócio (Implementado em 23/03/2026)

### 🎯 Estratégia: Teste Grátis Primeiro, Pagamento Depois

O PagBy agora adota um modelo **trial-first** para reduzir a barreira de entrada e aumentar conversões.

---

## 📋 Fluxo Completo

### 1. Cadastro Inicial (GRATUITO)
**Rota:** `/register-tenant`  
**Controller:** `TenantRegistrationController@register`

**O que acontece:**
- ✅ Cliente preenche formulário com dados do negócio
- ✅ Sistema salva contato na tabela `contacts` (banco central)
- ✅ **CRIA TENANT AUTOMATICAMENTE** com status `trial`
- ✅ Tenant recebe 30 dias grátis (`trial_ends_at = now() + 30 dias`)
- ✅ Banco de dados do tenant é criado e inicializado
- ✅ Usuário owner é criado no banco do tenant
- ✅ Cliente recebe acesso imediato ao sistema
- ❌ **NÃO integra com Asaas** (sem cobrança)
- ❌ **NÃO cria pagamento** 

**Resultado:**
- Cliente pode usar TODAS as funcionalidades por 30 dias
- Sistema totalmente funcional sem compromisso
- Sem necessidade de cartão de crédito

---

### 2. Durante o Trial (Dias 1-30)
**Status do Tenant:** `subscription_status = 'trial'`

**O que o cliente pode fazer:**
- ✅ Acesso completo a todas as funcionalidades
- ✅ Cadastrar serviços, funcionários, clientes
- ✅ Fazer agendamentos
- ✅ Gerenciar pagamentos de clientes
- ✅ Gerar relatórios

**Lembretes automáticos:**
- Dia 23: "Faltam 7 dias para seu trial expirar"
- Dia 28: "Faltam 2 dias - escolha seu plano"
- Dia 30: Sistema bloqueia e exibe página de escolha de planos

---

### 3. Fim do Trial (Dia 30)
**Comando cron:** `php artisan tenants:check-expired-subscriptions` (diário)

**O que acontece:**
- Sistema verifica `trial_ends_at < now()`
- Tenant marcado como `subscription_status = 'expired'`
- Flag `is_blocked = true`
- Middleware `CheckTenantSubscription` bloqueia acesso
- Redireciona para `/subscription/plans`

---

### 4. Escolha de Plano e Pagamento
**Rotas:** 
- `/subscription/plans` - Escolha do plano
- `/subscription/select/{plan}` - Seleção de plano específico
- `/pagby-subscription/payment` - Checkout Asaas

**Controller:** `PagBySubscriptionController`

**O que acontece:**
- ✅ Tenant escolhe plano (mensal, trimestral, semestral, anual)
- ✅ AGORA SIM: Integração com Asaas é feita
  - Cria customer no Asaas via `AsaasService::getOrCreateCustomer()`
  - Gera cobrança no Asaas
  - Salva `asaas_customer_id` no tenant
- ✅ Cria registro em `pag_by_payments` (banco central)
- ✅ Redireciona para checkout Asaas

---

### 5. Confirmação de Pagamento
**Webhook:** `/api/subconta-webhook` (Asaas notifica)

**O que acontece após pagamento confirmado:**
- ✅ Webhook Asaas notifica status `CONFIRMED`
- ✅ Tenant atualizado:
  - `subscription_status = 'active'`
  - `subscription_started_at = now()`
  - `subscription_ends_at = now() + período do plano`
  - `is_blocked = false`
  - `trial_ends_at = null` (trial finalizado)
- ✅ Cliente recebe email de confirmação
- ✅ Sistema desbloqueado

---

## 🔧 Implementação Técnica

### Quando NÃO integrar com Asaas:
- ❌ Durante cadastro inicial (`TenantRegistrationController@register`)
- ❌ Durante o período de trial
- ❌ Ao criar o tenant pela primeira vez

### Quando SIM integrar com Asaas:
- ✅ Quando tenant escolhe um plano após trial
- ✅ Na renovação de assinatura
- ✅ Ao trocar de plano
- ✅ Ao adicionar mais funcionários (cobrança proporcional)

### Arquivos Importantes:
1. **TenantRegistrationController.php**
   - Método `register()`: Cria tenant trial SEM Asaas
   - Método `createTrialTenant()`: Cria tenant com 30 dias grátis
   - Método `initializeTenantDatabase()`: Inicializa banco e cria owner

2. **PagBySubscriptionController.php**
   - Método `choosePlan()`: Tenant escolhe plano
   - Método `showPaymentForm()`: Exibe checkout
   - Método `processPayment()`: INTEGRA COM ASAAS aqui
   - Método `webhook()`: Recebe confirmação do Asaas

3. **CheckTenantSubscription.php** (Middleware)
   - Bloqueia tenant quando `is_blocked = true`
   - Permite acesso durante trial se `trial_ends_at > now()`

---

## 📊 Estados Possíveis do Tenant

| Status | is_blocked | trial_ends_at | subscription_ends_at | Pode acessar? |
|--------|-----------|---------------|---------------------|---------------|
| `trial` | false | futuro | null | ✅ SIM |
| `trial` | true | passado | null | ❌ NÃO (trial expirado) |
| `expired` | true | passado/null | passado/null | ❌ NÃO |
| `active` | false | null | futuro | ✅ SIM |
| `active` | true | null | passado | ❌ NÃO (assinatura vencida) |
| `suspended` | true | qualquer | qualquer | ❌ NÃO (suspenso manualmente) |

---

## 🎯 Vantagens do Novo Modelo

### Para o Cliente:
- ✅ Zero risco - teste sem compromisso
- ✅ Sem necessidade de cartão de crédito
- ✅ Experiência completa do produto
- ✅ Tempo para avaliar o valor antes de pagar

### Para o PagBy:
- ✅ Menor barreira de entrada = mais cadastros
- ✅ Cliente experimenta valor antes de pagar = maior conversão
- ✅ Reduz abandono no checkout
- ✅ Constrói confiança e credibilidade
- ✅ Cliente já está usando quando chega a hora de pagar

---

## 🔄 Migração de Código Antigo

**ANTES (modelo com pagamento imediato):**
```php
// register-tenant integrava com Asaas imediatamente
$asaas = new AsaasService();
$customerId = $asaas->getOrCreateCustomer($customerData);
// Redirecionava para pagamento antes de criar tenant
```

**AGORA (modelo trial-first):**
```php
// register-tenant cria tenant trial SEM integração Asaas
$tenant = $this->createTrialTenant($contact);
// Cliente recebe acesso imediato
// Asaas só é integrado DEPOIS do trial, ao escolher plano
```

---

## 📝 Próximos Passos Recomendados

1. **Email Marketing:**
   - Email de boas-vindas após cadastro
   - Email no dia 7: "Como está sua experiência?"
   - Email no dia 23: "Faltam 7 dias de trial"
   - Email no dia 28: "Últimos dias - escolha seu plano"

2. **Notificações no Sistema:**
   - Banner no dashboard mostrando dias restantes
   - Lightbox no dia 28 incentivando escolha de plano

3. **Analytics:**
   - Rastrear taxa de conversão trial → paid
   - Identificar quando no trial o cliente tem mais engajamento
   - A/B test diferentes durações de trial

4. **Suporte Proativo:**
   - Chatbot/WhatsApp perguntando se precisa de ajuda
   - Vídeos tutoriais para primeiros dias
   - Webinar semanal para novos usuários

---

**Última atualização:** 23/03/2026  
**Versão:** 2.0 - Trial-First Model

# Sistema de Assinatura Multi-Tenant

## Visão Geral

Sistema implementado para controlar assinaturas de barbearias e salões de beleza com período de teste gratuito de 30 dias e bloqueio automático após expiração.

## Recursos Implementados

### 1. Campos de Assinatura no Tenant
- `trial_started_at`: Data de início do período de teste
- `trial_ends_at`: Data de fim do período de teste (30 dias)
- `subscription_status`: Status da assinatura (trial, active, expired, suspended)
- `current_plan`: Plano atual ativo (Básico, Intermediário, Avançado)
- `subscription_started_at`: Data de início da assinatura paga
- `subscription_ends_at`: Data de fim da assinatura paga
- `is_blocked`: Flag para bloqueio manual

### 2. Planos Disponíveis

#### Plano Básico - R$ 29,90/mês
- Até 1 profissional
- Agendamento online
- Controle financeiro básico
- Relatórios simples
- Suporte via email

#### Plano Intermediário - R$ 59,90/mês
- Até 3 profissionais
- Agendamento online
- Controle financeiro avançado
- Relatórios detalhados
- Gestão de estoque básica
- Suporte via chat

#### Plano Avançado - R$ 99,90/mês
- Profissionais ilimitados
- Agendamento online
- Controle financeiro completo
- Relatórios avançados
- Gestão de estoque completa
- Sistema de fidelidade
- Múltiplas filiais
- Suporte prioritário

### 3. Fluxo de Funcionamento

1. **Criação do Tenant**: Automaticamente inicia período de teste de 30 dias
2. **Período de Teste**: Tenant tem acesso completo por 30 dias
3. **Aviso de Expiração**: Alertas nos últimos 7 dias do período de teste
4. **Expiração**: Após 30 dias, tenant é bloqueado automaticamente
5. **Seleção de Plano**: Tenant deve escolher um plano pago para reativar
6. **Renovação**: Sistema monitora e bloqueia assinaturas expiradas

### 4. Componentes Implementados

#### Middleware: `CheckTenantSubscription`
- Verifica status da assinatura em todas as requisições
- Bloqueia acesso se assinatura expirada
- Permite acesso às páginas de pagamento e seleção de planos

#### Controller: `TenantSubscriptionController`
- `showPlans()`: Exibe planos disponíveis
- `selectPlan()`: Ativa plano selecionado
- `blocked()`: Página mostrada quando tenant está bloqueado
- `startTrial()`: Inicia período de teste manual

#### Livewire: `SubscriptionStatus`
- Componente para exibir status da assinatura no dashboard
- Mostra dias restantes e alertas de expiração
- Links para gerenciar assinatura

#### Command: `CheckExpiredSubscriptions`
- Comando para verificar e atualizar assinaturas expiradas
- Deve ser executado via cron job diariamente
- Comando: `php artisan tenants:check-expired-subscriptions`

### 5. Métodos no Model Tenant

- `isInTrial()`: Verifica se está no período de teste
- `isTrialExpired()`: Verifica se período de teste expirou
- `hasActiveSubscription()`: Verifica se tem assinatura ativa
- `isSubscriptionExpired()`: Verifica se assinatura expirou
- `shouldBeBlocked()`: Verifica se deve ser bloqueado
- `startTrial()`: Inicia período de teste
- `activateSubscription()`: Ativa assinatura paga
- `block()` / `unblock()`: Bloqueia/desbloqueia tenant

### 6. Rotas Implementadas

```php
// Rotas sempre disponíveis (mesmo bloqueado)
/subscription/plans - Visualizar planos
/subscription/select - Selecionar plano
/subscription/success - Página de sucesso
/blocked - Página de bloqueio
/trial/start - Iniciar período de teste

// Rotas protegidas (verificam assinatura)
/ - Home do tenant
/dashboard - Dashboard principal
/plans - Gerenciar planos
// ... outras rotas do sistema
```

### 7. Views Criadas

- `tenant/subscription/plans.blade.php`: Página de seleção de planos
- `tenant/subscription/success.blade.php`: Página de sucesso após ativação
- `tenant/subscription/blocked.blade.php`: Página mostrada quando bloqueado
- `livewire/subscription-status.blade.php`: Componente de status da assinatura

### 8. Automação e Monitoramento

#### Cron Job Recomendado
```bash
# Adicionar ao crontab para verificar diariamente às 00:00
0 0 * * * php /path/to/artisan tenants:check-expired-subscriptions
```

#### Integração com Dashboard
```blade
<!-- Adicionar ao dashboard principal -->
<livewire:subscription-status />
```

### 9. Próximos Passos (Opcional)

1. **Integração com Gateway de Pagamento**
   - PIX, cartão de crédito, boleto
   - Webhooks para confirmação automática

2. **Notificações por Email**
   - Alertas de expiração
   - Confirmação de pagamento

3. **Relatórios Administrativos**
   - Dashboard admin com métricas de assinatura
   - Controle de receita

4. **Funcionalidades Adicionais**
   - Desconto para pagamento anual
   - Período de carência pós-expiração
   - Upgrade/downgrade de planos

## Uso

### Para Administradores
```php
// Verificar status de todos os tenants
php artisan tenants:check-expired-subscriptions

// Bloquear tenant manualmente
$tenant = Tenant::find('tenant-id');
$tenant->block();

// Ativar assinatura manualmente
$tenant->activateSubscription('Básico', 30);
```

### Para Tenants
1. Acessar página de planos: `/subscription/plans`
2. Selecionar plano desejado
3. Sistema ativa automaticamente (integração com pagamento pendente)

## Segurança

- Middleware verifica status em todas as rotas protegidas
- Campos de assinatura protegidos contra edição manual
- Logs de alterações via command automático
- Validação de planos disponíveis

## Status do Projeto

✅ **Implementado:**
- Sistema base de assinatura
- Período de teste automático
- Bloqueio por expiração
- Interface de seleção de planos
- Middleware de proteção
- Command de verificação
- Componente de status

🔄 **Pendente:**
- Integração com gateway de pagamento
- Sistema de notificações
- Relatórios administrativos

---

**Última atualização:** 24 de agosto de 2025
**Versão:** 1.0.0

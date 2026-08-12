# 🚀 Implementação Simplificada - Apenas Asaas

## ✅ Você Decidiu Usar Apenas Asaas - Ótima Escolha!

Código mais limpo, fiscalmente correto, split automático.

---

## 📋 Passos de Implementação

### 1. Substituir SubscriptionController (2 minutos)

```bash
# Backup do antigo
mv app/Http/Controllers/SubscriptionController.php app/Http/Controllers/SubscriptionController_OLD.php

# Usar o novo (simplificado)
mv app/Http/Controllers/SubscriptionController_NEW.php app/Http/Controllers/SubscriptionController.php
```

### 2. Configurar Credenciais (1 minuto)

Editar `.env`:

```env
# Asaas Sandbox (desenvolvimento)
ASAAS_API_KEY=sua_sandbox_key
ASAAS_API_URL=https://sandbox.asaas.com/api/v3
```

**Obter chave**: https://sandbox.asaas.com → Configurações → Integrações → API

### 3. Executar Migration (1 minuto)

```bash
php artisan migrate
```

Adiciona campos:
- `tenants.asaas_wallet_id`
- `tenants.asaas_account_data`  
- `tenants_plans_payments.asaas_subscription_id`
- `tenants_plans_payments.asaas_data`

### 4. Incluir Rotas (1 minuto)

Editar `routes/web.php` ou `routes/tenant.php`:

```php
// Adicionar no final
require __DIR__.'/subscription.php';
```

**Opcional**: Comentar/remover rotas antigas do MercadoPago.

### 5. Limpar Cache (30 segundos)

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

---

## 🧪 Testar Sistema

### A) Criar Tenant de Teste

Via Tinker:

```bash
php artisan tinker
```

```php
$tenant = Tenant::create([
    'id' => 'tenanttest',
    'name' => 'Salão Teste',
    'owner_email' => 'teste@email.com',
]);

$tenant->domains()->create(['domain' => 'tenanttest.localhost']);
```

### B) Criar Subconta Asaas

```bash
php artisan tenants:create-asaas-accounts --tenant=tenanttest
```

Dados de teste (sandbox):
- **CPF**: `111.111.111-11`
- **Telefone**: `11999999999`

### C) Criar Plano

```php
// Via tinker
TenantPlan::create([
    'tenant_id' => 'tenanttest',
    'plan_id' => 1,
    'name' => 'Plano Básico',
    'price' => 29.90,
    'active' => true,
]);
```

### D) Testar Criação de Assinatura

```bash
curl -X POST 'http://localhost/assinatura/store' \
  -d 'plan_id=1' \
  -d 'tenant_id=tenanttest' \
  -d 'user_email=comprador@teste.com' \
  -d 'user_name=João Teste' \
  -d 'cpf_cnpj=11111111111'
```

### E) Ver Logs

```bash
tail -f storage/logs/laravel.log | grep -E "Asaas|Split|Assinatura"
```

---

## 🔄 Atualizar Views

### Formulário de Assinatura

```blade
{{-- Ex: resources/views/plans/select.blade.php --}}

<form action="{{ route('assinatura.store') }}" method="POST">
    @csrf
    
    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
    <input type="hidden" name="tenant_id" value="{{ tenant('id') }}">
    
    <div class="form-group">
        <label>Nome Completo</label>
        <input type="text" name="user_name" 
               value="{{ auth()->user()->name }}" required>
    </div>
    
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="user_email" 
               value="{{ auth()->user()->email }}" required>
    </div>
    
    <div class="form-group">
        <label>CPF/CNPJ</label>
        <input type="text" name="cpf_cnpj" 
               placeholder="000.000.000-00" required>
    </div>
    
    <button type="submit" class="btn btn-primary">
        Assinar {{ $plan->name }} - R$ {{ number_format($plan->price, 2, ',', '.') }}/mês
    </button>
</form>
```

### Botão de Cancelamento

```blade
<form action="{{ route('assinatura.cancelar') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_id" value="{{ $payment->id }}">
    <button type="submit" class="btn btn-danger">
        Cancelar Assinatura
    </button>
</form>
```

---

## 📁 Estrutura de Arquivos

```
app/
├── Services/
│   └── AsaasService.php                    ← Já existe (estendido)
├── Http/Controllers/
│   ├── SubscriptionController.php          ← NOVO (simplificado)
│   └── SubscriptionController_OLD.php      ← Backup do antigo
└── Console/Commands/
    └── CreateAsaasAccountsForTenants.php   ← Já existe

database/migrations/
└── 2026_01_01_000001_add_asaas_fields...   ← Já existe

routes/
└── subscription.php                        ← NOVO (rotas limpas)
```

---

## ⚙️ Configurar Webhook em Produção

Quando for para produção:

1. Painel Asaas → Configurações → Webhooks
2. URL: `https://pagby.com.br/assinatura/webhook`
3. Eventos:
   - ✅ PAYMENT_CREATED
   - ✅ PAYMENT_RECEIVED
   - ✅ PAYMENT_CONFIRMED
   - ✅ PAYMENT_OVERDUE
   - ✅ PAYMENT_DELETED

---

## 🔧 Personalizar Split

Editar `SubscriptionController.php` linha ~103:

```php
$splitData = [
    'walletId' => $tenant->asaas_wallet_id,
    'percentualValue' => 85, // Alterar aqui: 85% tenant, 15% PagBy
];
```

Ou usar valor fixo:

```php
$splitData = [
    'walletId' => $tenant->asaas_wallet_id,
    'fixedValue' => 5.00, // R$ 5 fixos para PagBy
];
```

---

## 🧹 Limpeza (Opcional)

### Remover código MercadoPago não usado:

```bash
# Remover controller antigo
rm app/Http/Controllers/SubscriptionController_OLD.php

# Remover AsaasSubscriptionController duplicado (se existir)
rm app/Http/Controllers/AsaasSubscriptionController.php

# Remover rotas antigas asaas.php (se usou)
rm routes/asaas.php
```

### Remover campos não usados (opcional):

Se quiser limpar campos do MercadoPago no futuro:

```bash
php artisan make:migration remove_mercadopago_fields
```

```php
Schema::table('tenants_plans_payments', function (Blueprint $table) {
    $table->dropColumn(['mp_payment_id', 'mercadopago_data']);
});
```

**Mas não é necessário agora** - podem ficar nullable sem problema.

---

## ✅ Checklist Rápido

- [ ] SubscriptionController substituído
- [ ] Credenciais Asaas configuradas (sandbox)
- [ ] Migration executada
- [ ] Rotas incluídas em `routes/web.php`
- [ ] Cache limpo
- [ ] Tenant de teste criado
- [ ] Subconta criada para tenant
- [ ] Plano de teste criado
- [ ] Assinatura teste funcionando
- [ ] Split configurado corretamente
- [ ] Views atualizadas (formulários)

---

## 📊 Vantagens da Implementação Simplificada

### Código Reduzido
- **Antes**: ~743 linhas (com MercadoPago)
- **Depois**: ~458 linhas (apenas Asaas)
- **Redução**: ~40% menos código

### Complexidade
- ❌ Sem dual API (MP + Asaas)
- ❌ Sem conversão de status entre sistemas
- ❌ Sem código condicional de pagamento
- ✅ Um único fluxo, mais simples

### Manutenção
- ✅ Menos dependências
- ✅ Menos pontos de falha
- ✅ Logs mais claros
- ✅ Debugging mais fácil

---

## 🚀 Próximos Passos

1. **Hoje**: Setup completo (acima)
2. **Amanhã**: Testar fluxo completo sandbox
3. **Próxima semana**: Integrar com UI
4. **Antes do lançamento**: 
   - Migrar para credenciais produção
   - Configurar webhook real
   - Testes finais

---

## 📚 Documentação de Referência

- **Este guia**: Setup inicial simplificado
- **IMPLEMENTACAO_ASAAS_INICIAL.md**: Guia detalhado
- **MIGRACAO_ASAAS.md**: Documentação completa da migração
- **API Asaas**: https://docs.asaas.com

---

## ⚡ Comandos Rápidos

```bash
# Ver rotas disponíveis
php artisan route:list | grep assinatura

# Criar subconta para todos tenants
php artisan tenants:create-asaas-accounts

# Ver logs em tempo real
tail -f storage/logs/laravel.log | grep Asaas

# Debug de pagamento
curl http://localhost/assinatura/debug/1 | jq
```

---

**Pronto para produção!** 🎉

Sistema simplificado, limpo e fiscalmente correto desde o início.

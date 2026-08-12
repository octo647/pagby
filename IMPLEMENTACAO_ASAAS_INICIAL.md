# Implementação Inicial do Sistema Asaas (Ambiente de Desenvolvimento)

## 🎯 Cenário: Sistema em Desenvolvimento

Como você ainda **não tem tenants reais**, podemos implementar o Asaas diretamente sem necessidade de migração complexa.

---

## ✅ Passos Simplificados

### 1. Configurar Credenciais (2 minutos)

Adicionar no `.env`:

```env
# Asaas Sandbox (desenvolvimento)
ASAAS_API_KEY=sua_sandbox_key_aqui
ASAAS_API_URL=https://sandbox.asaas.com/api/v3
```

**Onde obter a chave:**
1. Criar conta em: https://sandbox.asaas.com
2. Acesse: Configurações → Integrações → API Key

### 2. Executar Migration (1 minuto)

```bash
php artisan migrate
```

Isso adiciona os campos:
- `tenants.asaas_wallet_id`
- `tenants.asaas_account_data`
- `tenants_plans_payments.asaas_subscription_id`
- `tenants_plans_payments.asaas_data`

### 3. Incluir Rotas (1 minuto)

Editar `routes/web.php` ou `routes/tenant.php`, adicionar:

```php
// Sistema de assinaturas via Asaas (com split de pagamentos)
require __DIR__.'/asaas.php';
```

**Opcional**: Comentar rotas antigas do MercadoPago se não for mais usar:

```php
// Comentar se não for usar MercadoPago
// require __DIR__.'/mercadopago-routes.php';
```

### 4. Limpar Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

---

## 🧪 Testar Sistema (Sandbox)

### A) Criar Tenant de Teste

```sql
-- Via tinker ou direto no banco
INSERT INTO tenants (id, name, owner_email, created_at, updated_at)
VALUES ('tenanttest', 'Salão Teste', 'teste@email.com', NOW(), NOW());

INSERT INTO domains (domain, tenant_id)
VALUES ('tenanttest.localhost', 'tenanttest');
```

Ou via Tinker:

```bash
php artisan tinker
```

```php
$tenant = Tenant::create([
    'id' => 'tenanttest',
    'name' => 'Salão Teste',
    'owner_email' => 'teste@email.com',
]);

$tenant->domains()->create([
    'domain' => 'tenanttest.localhost'
]);
```

### B) Criar Subconta Asaas para o Tenant

```bash
php artisan tenants:create-asaas-accounts --tenant=tenanttest
```

O comando solicitará:
- **CPF/CNPJ**: Use `111.111.111-11` (CPF de teste no sandbox)
- **Telefone**: Use `11999999999`

### C) Criar Plano de Teste

```php
// Via tinker
$plan = TenantPlan::create([
    'tenant_id' => 'tenanttest',
    'plan_id' => 1,
    'name' => 'Plano Básico',
    'price' => 29.90,
    'active' => true,
]);
```

### D) Testar Criação de Assinatura

```bash
curl -X POST 'http://localhost/asaas-assinatura/store' \
  -H 'Content-Type: application/x-www-form-urlencoded' \
  -d 'plan_id=1' \
  -d 'tenant_id=tenanttest' \
  -d 'user_email=comprador@teste.com' \
  -d 'user_name=João Teste' \
  -d 'cpf_cnpj=11111111111'
```

**Resultado esperado:**
- Deve redirecionar para página `/wait`
- Verificar log: `tail -f storage/logs/laravel.log | grep -i asaas`

### E) Verificar no Sandbox Asaas

1. Login: https://sandbox.asaas.com
2. Menu: **Cobranças** → Ver assinatura criada
3. Menu: **Subcontas** → Ver subconta do tenant
4. **Simular pagamento** na própria interface do Asaas

---

## 🔄 Fluxo Completo de Desenvolvimento

```
1. Tenant criado no sistema
   ↓
2. Subconta Asaas criada automaticamente (via command ou on-the-fly)
   ↓
3. Cliente seleciona plano
   ↓
4. Assinatura criada no Asaas com split configurado
   ↓
5. Cliente paga (simulado no sandbox)
   ↓
6. Webhook notifica PagBy
   ↓
7. Assinatura ativada automaticamente
   ↓
8. Split realizado: 90% tenant / 10% PagBy
```

---

## 🎨 Ajustar Views (Opcional)

Se já tiver views de seleção de planos, atualizar o formulário:

```blade
{{-- resources/views/plans/select.blade.php --}}

<form action="{{ route('asaas-assinatura.store') }}" method="POST">
    @csrf
    
    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
    <input type="hidden" name="tenant_id" value="{{ tenant('id') }}">
    
    <div class="mb-3">
        <label>Nome Completo</label>
        <input type="text" name="user_name" value="{{ auth()->user()->name }}" required>
    </div>
    
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="user_email" value="{{ auth()->user()->email }}" required>
    </div>
    
    <div class="mb-3">
        <label>CPF/CNPJ</label>
        <input type="text" name="cpf_cnpj" placeholder="000.000.000-00" required>
    </div>
    
    <button type="submit" class="btn btn-primary">
        Assinar {{ $plan->name }} - R$ {{ number_format($plan->price, 2, ',', '.') }}/mês
    </button>
</form>
```

---

## 🚀 Quando For para Produção

### 1. Trocar Credenciais

No `.env` de produção:

```env
# Asaas Produção
ASAAS_API_KEY=sua_production_key_aqui
ASAAS_API_URL=https://www.asaas.com/api/v3
```

### 2. Configurar Webhook Real

No painel Asaas (produção):
- URL: `https://pagby.com.br/asaas-assinatura/webhook`
- Eventos: `PAYMENT_CREATED`, `PAYMENT_RECEIVED`, `PAYMENT_CONFIRMED`, `PAYMENT_OVERDUE`

### 3. Criar Subcontas para Tenants Reais

Quando tenants se registrarem, automatizar criação da subconta:

```php
// Em algum evento após criação do tenant
event(new TenantCreated($tenant));

// No listener
public function handle(TenantCreated $event)
{
    $asaasService = app(AsaasService::class);
    
    $result = $asaasService->criarSubconta([
        'name' => $event->tenant->name,
        'email' => $event->tenant->owner_email,
        'cpfCnpj' => $event->tenant->owner_cpf_cnpj,
        'mobilePhone' => $event->tenant->owner_phone,
    ]);
    
    if ($result['success']) {
        $event->tenant->asaas_wallet_id = $result['data']['walletId'];
        $event->tenant->save();
    }
}
```

---

## 📊 Monitoramento no Desenvolvimento

### Ver Logs em Tempo Real

```bash
tail -f storage/logs/laravel.log | grep -E "Asaas|Split|Subconta|Assinatura"
```

### Verificar Subcontas Criadas

```bash
php artisan tinker
```

```php
Tenant::whereNotNull('asaas_wallet_id')->get(['id', 'name', 'asaas_wallet_id']);
```

### Ver Últimas Assinaturas

```bash
curl http://localhost/asaas-assinatura/debug/1 | jq
```

---

## ⚙️ Personalizar Split

Padrão atual: **90% tenant / 10% PagBy**

Para alterar, editar `AsaasSubscriptionController.php` linha ~193:

```php
$splitData = [
    'walletId' => $tenant->asaas_wallet_id,
    'percentualValue' => 85, // 85% tenant, 15% PagBy
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

## 🐛 Troubleshooting Comum

### Erro: "wallet_id não encontrado"

**Causa**: Subconta não foi criada para o tenant

**Solução**:
```bash
php artisan tenants:create-asaas-accounts --tenant=ID_DO_TENANT
```

### Erro: "Erro ao criar cliente no Asaas"

**Causa**: CPF/CNPJ ou email inválidos

**Solução**: Verificar dados do tenant no banco

### Webhook não funciona localmente

**Normal!** Asaas precisa de URL pública. Opções:

1. **ngrok** (recomendado para dev):
   ```bash
   ngrok http 80
   # Copiar URL gerada e configurar no Asaas
   ```

2. **Simular manualmente**:
   ```bash
   curl -X POST 'http://localhost/asaas-assinatura/webhook' \
     -H 'Content-Type: application/json' \
     -d '{"event":"PAYMENT_RECEIVED","payment":{"id":"pay_123","subscription":"sub_123","value":29.90}}'
   ```

---

## 📚 Próximos Passos

1. ✅ **Hoje**: Setup inicial (feito acima)
2. ✅ **Amanhã**: Testar fluxo completo no sandbox
3. ⏭️ **Próxima semana**: Integrar com UI de seleção de planos
4. ⏭️ **Antes do lançamento**: Migrar para credenciais de produção

---

## 🎯 Checklist Rápido

- [ ] Credenciais Asaas configuradas (sandbox)
- [ ] Migration executada
- [ ] Rotas incluídas
- [ ] Cache limpo
- [ ] Tenant de teste criado
- [ ] Subconta de teste criada
- [ ] Assinatura teste criada com sucesso
- [ ] Split funcionando no sandbox Asaas
- [ ] Webhook testado (ngrok ou simulado)
- [ ] Views atualizadas para usar novas rotas

---

## 💡 Dica: Ignorar MercadoPago Completamente

Como você não tem tenants reais, pode **não implementar o MercadoPago** e usar apenas Asaas desde o início:

1. Não incluir rotas do MercadoPago
2. Remover referências a `mp_payment_id` (ou deixar nullable)
3. Focar 100% no Asaas

Isso simplifica muito o código e evita confusão!

---

## 📞 Precisa de Ajuda?

- **Documentação completa**: Ver `MIGRACAO_ASAAS.md`
- **API Asaas**: https://docs.asaas.com
- **Suporte Asaas**: suporte@asaas.com

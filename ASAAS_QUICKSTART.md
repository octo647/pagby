# Guia Rápido: Primeiros Passos com Asaas

## Setup Inicial (5 minutos)

### 1. Adicionar Credenciais

```bash
# Editar .env
nano .env
```

Adicionar:
```env
ASAAS_API_KEY=sua_chave_aqui
ASAAS_API_URL=https://sandbox.asaas.com/api/v3
```

### 2. Executar Migration

```bash
php artisan migrate
```

### 3. Criar Subcontas

```bash
php artisan tenants:create-asaas-accounts
```

### 4. Incluir Rotas

Editar `routes/web.php`:

```php
// Adicionar no final
require __DIR__.'/asaas.php';
```

### 5. Configurar Webhook

URL para webhook: `https://seu-dominio.com.br/asaas-assinatura/webhook`

---

## Teste Completo

### 1. Criar Assinatura de Teste

```bash
curl -X POST 'http://localhost/asaas-assinatura/store' \
  -H 'Content-Type: application/x-www-form-urlencoded' \
  -d 'plan_id=1' \
  -d 'tenant_id=tenantdudu' \
  -d 'user_email=teste@exemplo.com' \
  -d 'user_name=João Silva' \
  -d 'cpf_cnpj=12345678901'
```

### 2. Verificar Status

```bash
curl 'http://localhost/asaas-assinatura/check-status/1'
```

### 3. Simular Webhook (Pagamento Aprovado)

```bash
curl -X POST 'http://localhost/asaas-assinatura/webhook' \
  -H 'Content-Type: application/json' \
  -d '{
    "event": "PAYMENT_RECEIVED",
    "payment": {
      "id": "pay_123456",
      "subscription": "sub_123456",
      "value": 29.90,
      "status": "RECEIVED"
    }
  }'
```

---

## Verificações

### Confirmar Subconta Criada

```sql
SELECT id, name, asaas_wallet_id FROM tenants;
```

Deve mostrar `wallet_id` preenchido.

### Confirmar Assinatura Criada

```sql
SELECT id, tenant_id, asaas_subscription_id, status, amount 
FROM tenants_plans_payments 
ORDER BY created_at DESC 
LIMIT 5;
```

### Ver Logs

```bash
tail -f storage/logs/laravel.log | grep -i asaas
```

---

## Exemplo de Integração na View

```blade
{{-- resources/views/plans/select.blade.php --}}

<form action="{{ route('asaas-assinatura.store') }}" method="POST">
    @csrf
    
    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
    <input type="hidden" name="tenant_id" value="{{ tenant('id') }}">
    
    <div class="form-group">
        <label>Nome Completo</label>
        <input type="text" name="user_name" value="{{ auth()->user()->name }}" required>
    </div>
    
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="user_email" value="{{ auth()->user()->email }}" required>
    </div>
    
    <div class="form-group">
        <label>CPF/CNPJ</label>
        <input type="text" name="cpf_cnpj" required>
    </div>
    
    <button type="submit" class="btn btn-primary">
        Assinar Plano {{ $plan->name }} - R$ {{ number_format($plan->price, 2, ',', '.') }}
    </button>
</form>
```

---

## Configuração Personalizada do Split

Para alterar o percentual de split (padrão é 90% tenant, 10% PagBy):

Editar `app/Http/Controllers/AsaasSubscriptionController.php`, linha ~193:

```php
$splitData = [
    'walletId' => $tenant->asaas_wallet_id,
    'percentualValue' => 85, // Alterar aqui: 85% tenant, 15% PagBy
];
```

Ou usar valor fixo em vez de percentual:

```php
$splitData = [
    'walletId' => $tenant->asaas_wallet_id,
    'fixedValue' => 5.00, // R$ 5 para PagBy, resto para tenant
];
```

---

## Comandos Úteis

```bash
# Criar subconta para tenant específico
php artisan tenants:create-asaas-accounts --tenant=tenantdudu

# Recriar todas as subcontas (força)
php artisan tenants:create-asaas-accounts --force

# Ver detalhes de pagamento
curl http://localhost/asaas-assinatura/debug/1 | jq

# Limpar cache depois de mudanças
php artisan config:clear && php artisan cache:clear
```

---

## Próximo Passo

Leia a documentação completa em [MIGRACAO_ASAAS.md](MIGRACAO_ASAAS.md)

# Guia de Webhooks Asaas - PagBy

## 📋 Visão Geral

Este guia explica como funciona o fluxo de pagamento com Asaas e como testar o webhook que cria automaticamente o tenant após aprovação do pagamento.

## 🔄 Fluxo Completo

### 1. Registro do Cliente
```
Usuário preenche formulário → Contact criado → Redireciona para checkout Asaas
```

### 2. Pagamento no Asaas
```
Usuário preenche dados pessoais → Escolhe forma de pagamento → Efetua pagamento
```

### 3. Webhook Automático
```
Asaas envia webhook → Sistema atualiza status → Cria tenant automaticamente
```

### 4. Tenant Ativo
```
Tenant criado → Domínio configurado → Email de boas-vindas (opcional)
```

## 🧪 Como Testar em Desenvolvimento

### Opção 1: Simular Webhook (Recomendado)

Após criar um checkout no sandbox:

1. Anote o `payment_id` do registro criado na tabela `pag_by_payments`
2. Acesse no navegador:
```
http://localhost/pagby-subscription/simulate-webhook/{payment_id}
```

Exemplo:
```
http://localhost/pagby-subscription/simulate-webhook/123
```

3. Verifique os logs em `storage/logs/laravel.log`:
```bash
tail -f storage/logs/laravel.log
```

4. Confirme que:
   - Status do pagamento mudou para `RECEIVED`
   - Tenant foi criado na tabela `tenants`
   - Domínio foi criado na tabela `domains`
   - `tenant_id` no pagamento mudou de `temp_XXX` para o ID real

### Opção 2: Usar Sandbox do Asaas

1. **Configure o webhook no painel Asaas:**
   - Acesse: https://sandbox.asaas.com/config/webhooks
   - URL do Webhook: `https://seu-dominio.com.br/pagby-subscription/webhook`
   - Eventos: Marque `PAYMENT_RECEIVED` e `PAYMENT_CONFIRMED`

2. **Use ngrok para expor localhost:**
```bash
ngrok http 80
```

3. **Configure o webhook com URL do ngrok:**
```
https://abc123.ngrok.io/pagby-subscription/webhook
```

4. **Faça um pagamento de teste:**
   - Use cartões de teste do Asaas
   - Cartão aprovado: `5162306219378829`
   - CVV qualquer: `123`
   - Validade futura: `12/2030`

## 📡 Estrutura do Webhook Asaas

O Asaas envia um POST com este formato:

```json
{
  "event": "PAYMENT_RECEIVED",
  "payment": {
    "id": "pay_abc123xyz",
    "status": "RECEIVED",
    "customer": "cus_000005213605",
    "value": 59.90,
    "netValue": 56.90,
    "billingType": "CREDIT_CARD",
    "confirmedDate": "2025-12-26T15:30:00.000Z"
  }
}
```

## 🎯 Status do Pagamento Asaas

| Status | Descrição | Ação do Sistema |
|--------|-----------|-----------------|
| `PENDING` | Aguardando pagamento | Apenas atualiza status |
| `RECEIVED` | Pagamento confirmado | **Cria o tenant** |
| `CONFIRMED` | Pagamento compensado | **Cria o tenant** (se não criou) |
| `OVERDUE` | Pagamento vencido | Apenas atualiza status |
| `REFUNDED` | Pagamento estornado | Pode bloquear tenant |

## 🔧 Configuração em Produção

### 1. Configure Webhook no Painel Asaas

Acesse: https://www.asaas.com/config/webhooks

- **URL**: `https://pagby.com.br/pagby-subscription/webhook`
- **Eventos**:
  - ✅ PAYMENT_RECEIVED
  - ✅ PAYMENT_CONFIRMED
  - ✅ PAYMENT_OVERDUE (opcional)
  - ✅ PAYMENT_REFUNDED (opcional)

### 2. Verifique Segurança

O webhook deve estar acessível publicamente, mas você pode adicionar:

```php
// No método webhook()
$asaasToken = config('services.asaas.webhook_token');
if ($request->header('asaas-access-token') !== $asaasToken) {
    abort(401);
}
```

### 3. Monitore os Logs

```bash
# Em produção
tail -f /var/www/pagby/storage/logs/laravel.log | grep "Webhook Asaas"
```

## 🐛 Troubleshooting

### Webhook não está sendo chamado

1. **Verifique URL no painel Asaas:**
   - Deve ser HTTPS em produção
   - Deve estar acessível publicamente

2. **Teste manualmente:**
```bash
curl -X POST https://pagby.com.br/pagby-subscription/webhook \
  -H "Content-Type: application/json" \
  -d '{"event":"PAYMENT_RECEIVED","payment":{"id":"test123","status":"RECEIVED"}}'
```

3. **Verifique logs do Asaas:**
   - Painel → Webhooks → Histórico de envios

### Tenant não está sendo criado

1. **Verifique se pagamento existe:**
```sql
SELECT * FROM pag_by_payments WHERE id = 123;
```

2. **Verifique se contact existe:**
```sql
SELECT * FROM contacts WHERE id = (SELECT contact_id FROM pag_by_payments WHERE id = 123);
```

3. **Verifique logs:**
```bash
grep "Criando tenant" storage/logs/laravel.log
grep "Erro ao criar tenant" storage/logs/laravel.log
```

4. **Verifique status do pagamento:**
   - Deve ser `RECEIVED` ou `CONFIRMED`
   - Não deve já ter criado tenant (tenant_id não começa com `temp_`)

### Email de boas-vindas não está sendo enviado

Descomente a linha no método `criarTenantAposAprovacao()`:

```php
Mail::to($contact->email)->send(new TenantCreated($tenant, $contact));
```

E crie a Mailable:
```bash
php artisan make:mail TenantCreated
```

## 📊 Monitoramento

### Verificar últimos pagamentos:

```sql
SELECT 
    id, 
    tenant_id, 
    status, 
    amount, 
    plan,
    created_at,
    updated_at
FROM pag_by_payments 
ORDER BY created_at DESC 
LIMIT 10;
```

### Verificar últimos tenants criados:

```sql
SELECT 
    id, 
    name, 
    subscription_status, 
    subscription_plan,
    created_at
FROM tenants 
ORDER BY created_at DESC 
LIMIT 10;
```

### Verificar tenants sem pagamento aprovado:

```sql
SELECT 
    p.id as payment_id,
    p.tenant_id,
    p.status,
    p.amount,
    c.tenant_name,
    c.email
FROM pag_by_payments p
INNER JOIN contacts c ON p.contact_id = c.id
WHERE p.tenant_id LIKE 'temp_%'
ORDER BY p.created_at DESC;
```

## 🚀 Próximos Passos

1. ✅ Webhook cria tenant automaticamente
2. 📧 Implementar email de boas-vindas
3. 👤 Criar usuário proprietário automaticamente
4. 🏢 Criar filial padrão
5. 🎨 Seedar dados iniciais do tenant
6. 📱 Notificar cliente via WhatsApp/SMS

## 📞 Suporte

- Documentação Asaas: https://docs.asaas.com/reference/introducao
- Logs do sistema: `storage/logs/laravel.log`
- Contato: suporte@pagby.com.br

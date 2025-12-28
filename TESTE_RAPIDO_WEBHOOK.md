# 🚀 TESTE RÁPIDO - Webhook Asaas

## Como Testar Agora

### Passo 1: Identificar o Payment ID

Acesse o banco de dados e pegue o ID de um pagamento criado:

```sql
SELECT id, tenant_id, status, amount, contact_id, created_at 
FROM pag_by_payments 
ORDER BY created_at DESC 
LIMIT 5;
```

### Passo 2: Simular o Webhook

#### Opção A: Via Comando Artisan (Recomendado)

```bash
php artisan asaas:test-webhook {PAYMENT_ID}
```

Exemplo:
```bash
php artisan asaas:test-webhook 123
```

#### Opção B: Via Navegador

```
http://localhost/pagby-subscription/simulate-webhook/{PAYMENT_ID}
```

Exemplo:
```
http://localhost/pagby-subscription/simulate-webhook/123
```

### Passo 3: Verificar Resultado

1. **Status do pagamento mudou:**
```sql
SELECT id, status, tenant_id FROM pag_by_payments WHERE id = 123;
```

2. **Tenant foi criado:**
```sql
SELECT id, name, email, subscription_status FROM tenants ORDER BY created_at DESC LIMIT 1;
```

3. **Domínio foi configurado:**
```sql
SELECT * FROM domains ORDER BY created_at DESC LIMIT 1;
```

## 📋 O que Acontece

1. ✅ Webhook recebe payload simulado
2. ✅ Status do pagamento muda para `RECEIVED`
3. ✅ Sistema busca o Contact associado
4. ✅ Cria novo Tenant com slug único
5. ✅ Configura domínio (slug.localhost)
6. ✅ Atualiza tenant_id no pagamento
7. ✅ Loga tudo em `storage/logs/laravel.log`

## 🐛 Se Algo Der Errado

### Ver logs em tempo real:
```bash
tail -f storage/logs/laravel.log
```

### Buscar erros específicos:
```bash
grep "Erro ao criar tenant" storage/logs/laravel.log
grep "Tenant criado" storage/logs/laravel.log
```

### Resetar para testar novamente:
```sql
UPDATE pag_by_payments 
SET status = 'pending', tenant_id = CONCAT('temp_', contact_id) 
WHERE id = 123;

-- Deletar o tenant criado se necessário
DELETE FROM domains WHERE tenant_id = 'slug-do-tenant';
DELETE FROM tenants WHERE id = 'slug-do-tenant';
```

## ✅ Checklist de Verificação

- [ ] Pagamento existe na tabela `pag_by_payments`
- [ ] Contact existe e está linkado ao pagamento
- [ ] Status mudou de `pending` para `RECEIVED`
- [ ] Tenant foi criado na tabela `tenants`
- [ ] Domínio foi criado na tabela `domains`
- [ ] tenant_id mudou de `temp_XXX` para slug real
- [ ] Logs mostram "✅ Tenant criado com sucesso"

## 🎯 Próximo Passo

Depois que funcionar localmente:
1. Configure webhook no painel Asaas (sandbox ou produção)
2. Use ngrok para testar com webhook real
3. Faça um pagamento de teste
4. Asaas enviará webhook automaticamente

Ver guia completo: [ASAAS_WEBHOOK_GUIDE.md](ASAAS_WEBHOOK_GUIDE.md)

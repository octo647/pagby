# 🚀 Deploy e Teste no VPS

**Data:** 05/03/2026  
**Branch:** `sem_split`  
**Objetivo:** Fazer deploy e executar teste de validação no VPS com webhooks

---

## 📋 Pré-requisitos

- ✅ Código commitado na branch `sem_split`
- ✅ `.env_production` configurado com chave de homologação
- ✅ Acesso SSH ao VPS (porta 22022)
- ✅ Script de deploy: `scripts/deploy.sh`

---

## 🚀 Processo de Deploy

### 1. Fazer Deploy

```bash
# Certifique-se de estar na branch correta
git branch

# Se não estiver em sem_split, mude:
git checkout sem_split

# Execute o deploy
./scripts/deploy.sh
```

**O que o script faz:**
- ✅ Compila assets (npm run build)
- ✅ Sincroniza código via rsync (exclui .env, vendor, storage)
- ✅ Instala dependências no servidor
- ✅ Limpa e recria caches
- ✅ Limpa OPCache

### 2. Conectar ao VPS

```bash
ssh -p 22022 helder@69.6.222.77
```

### 3. Verificar Environment

```bash
cd /var/www/pagby

# Verificar se .env está correto
grep ASAAS_API .env

# Deve mostrar:
# ASAAS_API_URL=https://api.asaas.com/v3 (ou /api/v3)
# ASAAS_API_KEY=$aact_hmlg_... (chave de homologação)
```

### 4. Verificar Migration

```bash
# Ver se migration de subcontas foi executada
php artisan migrate:status | grep asaas_subaccount

# Se não foi executada, executar:
php artisan migrate --force
```

---

## 🧪 Executar Teste de Validação

### Teste Completo (Cria Tenant Automaticamente)

```bash
cd /var/www/pagby

php artisan asaas:test-subaccount-invoice --save-evidence
```

**O teste vai:**
1. ✅ Criar tenant de teste
2. ✅ Criar subconta no Asaas (homologação)
3. ✅ Gerar API key da subconta
4. ✅ Criar cliente de teste
5. ✅ Criar cobrança usando API da subconta
6. ✅ **VALIDAR:** campo 'account' da cobrança
7. ✅ Salvar evidências em arquivo .md

### Teste com Tenant Existente

```bash
# Listar tenants disponíveis
php artisan tinker
> Tenant::on('mysql')->pluck('name', 'id');
> exit

# Executar teste com tenant específico
php artisan asaas:test-subaccount-invoice --tenant=tenantbar --save-evidence
```

---

## 📊 Interpretar Resultado

### ✅ SUCESSO (Modelo Validado)

```
✅✅✅ SUCESSO! ✅✅✅
   Cobrança pertence à SUBCONTA!
   Isso significa que a NF será emitida em nome do SALÃO!

   🎉 MODELO VALIDADO TECNICAMENTE!
```

**Próximos passos:**
- ✅ Modelo SEM SPLIT validado tecnicamente
- ✅ NF será emitida em nome do SALÃO (não do PagBy)
- ✅ Implementar fluxo completo de pagamentos
- ✅ Consultar contador COM EVIDÊNCIA do teste

### ❌ FALHA (Modelo Precisa Ajustes)

```
❌❌❌ PROBLEMA! ❌❌❌
   Cobrança NÃO pertence à subconta!
   NF será emitida em nome do PAGBY (master)!
```

**Próximos passos:**
- ❌ Contatar suporte Asaas
- ❌ Verificar configuração de subcontas
- ❌ Avaliar modelo alternativo

---

## 📸 Baixar Evidências

### Verificar Arquivo Gerado

```bash
# Listar arquivos de evidência
ls -lh EVIDENCIA_*.md

# Exemplo de saída:
# EVIDENCIA_TESTE_SUBACCOUNT_2026-03-05_182345.md
```

### Baixar para Local

```bash
# No seu computador LOCAL (não no VPS):
scp -P 22022 helder@69.6.222.77:/var/www/pagby/EVIDENCIA_*.md ~/projetos/pagby/
```

---

## 🔍 Validar no Painel Asaas

### 1. Login no Asaas

- URL: https://www.asaas.com
- Conta: hecosoftwares@gmail.com

### 2. Verificar Subconta Criada

1. Menu: **Configurações** → **Contas Filhas**
2. Procurar: "Salão Teste Validação NF"
3. Verificar status: ATIVA ou PENDENTE

### 3. Verificar Cobrança

1. Menu: **Cobranças** → **Todas as cobranças**
2. Filtrar por valor: R$ 100,00
3. Abrir detalhes da cobrança
4. **CRÍTICO:** Verificar campo "Conta" ou "Account"
   - ✅ Se mostrar a subconta → Correto!
   - ❌ Se mostrar conta master → Incorreto!

---

## 🔧 Webhooks (Configuração Futura)

Se o teste passar, próxima etapa será configurar webhooks:

### URLs de Webhook

```
# Webhook principal (receberá eventos de todas subcontas)
https://pagby.com.br/webhook/asaas

# Webhook por subconta (se necessário)
https://{tenant}.pagby.com.br/webhook/asaas
```

### Eventos a Monitorar

- `PAYMENT_CONFIRMED` - Pagamento confirmado
- `PAYMENT_RECEIVED` - Pagamento recebido
- `PAYMENT_OVERDUE` - Pagamento vencido
- `PAYMENT_DELETED` - Pagamento excluído

### Configurar no Asaas

1. **Menu:** Configurações → Webhooks
2. **URL:** https://pagby.com.br/webhook/asaas
3. **Eventos:** Selecionar todos relacionados a pagamentos
4. **Versão:** v3
5. **Salvar** e testar

---

## 🐛 Troubleshooting

### Erro: "Tenant não encontrado"

```bash
# Listar tenants
php artisan tinker
> Tenant::on('mysql')->get(['id', 'name']);
```

### Erro: "Migration não encontrada"

```bash
# Ver status de migrations
php artisan migrate:status

# Executar migrations pendentes
php artisan migrate --force
```

### Erro: "Erro ao criar subconta"

**Possíveis causas:**

1. **API Key incorreta**
   ```bash
   grep ASAAS_API_KEY .env
   # Deve conter _hmlg_ (homologação) ou _prod_ (produção)
   ```

2. **Dados inválidos**
   - CPF/CNPJ: Use `24971563000198` para testes
   - Email: Deve ser único no Asaas
   - Telefone: Formato (99) 99999-9999

3. **Subconta já existe**
   - Verificar no painel Asaas se já foi criada
   - Usar `--force` para recriar

### Erro 401 Unauthorized

```bash
# Limpar cache de configuração
php artisan config:clear
php artisan cache:clear

# Testar API manualmente com curl
curl -H "access_token: $ASAAS_API_KEY" \
     https://api.asaas.com/api/v3/customers?limit=1
```

### Logs para Debug

```bash
# Ver últimas 50 linhas do log
tail -50 storage/logs/laravel.log

# Seguir log em tempo real
tail -f storage/logs/laravel.log

# Filtrar por "Asaas"
grep -i asaas storage/logs/laravel.log | tail -20
```

---

## ✅ Checklist Completo

### Antes do Deploy
- [ ] Branch `sem_split` ativa e commitada
- [ ] `.env_production` com chave de homologação
- [ ] Script `deploy.sh` testado

### Durante Deploy
- [ ] Deploy executado sem erros
- [ ] SSH no VPS funcionando
- [ ] `.env` no servidor correto

### Teste de Validação
- [ ] Comando executado: `php artisan asaas:test-subaccount-invoice`
- [ ] Tenant de teste criado
- [ ] Subconta criada no Asaas
- [ ] API key gerada
- [ ] Cobrança criada
- [ ] **Campo 'account' verificado**
- [ ] Arquivo de evidência gerado

### Validação Manual
- [ ] Login no painel Asaas
- [ ] Subconta localizada
- [ ] Cobrança visualizada
- [ ] Campo "Conta" verificado manualmente

### Decisão
- [ ] Resultado interpretado (passou ou falhou)
- [ ] Evidências baixadas para local
- [ ] Próximos passos definidos

---

## 🎯 Métrica de Sucesso

**Teste passa se:**
```json
{
  "payment": {
    "id": "pay_123...",
    "account": "acc_subconta_123...",  // ← DEVE SER ID DA SUBCONTA!
    "value": 100.00,
    "customer": "cus_test_123...",
    ...
  }
}
```

**Validação:** `payment.account === subconta.id` ✅

---

## 📞 Suporte

### Asaas Suporte
- **Email:** suporte@asaas.com
- **Chat:** No painel (canto inferior direito)
- **WhatsApp:** (48) 3027-5009

### Pergunta Específica para Asaas
> "Criamos uma subconta via API e geramos a API key dela. Quando criamos uma cobrança usando a API key DA SUBCONTA, o campo 'account' da cobrança está retornando o ID da conta master. Isso está correto? Como fazer para a nota fiscal ser emitida em nome da subconta?"

---

## 📚 Arquivos de Referência

- [TestAsaasSubaccountInvoice.php](app/Console/Commands/TestAsaasSubaccountInvoice.php)
- [AsaasService.php](app/Services/AsaasService.php)
- [Tenant.php](app/Models/Tenant.php)
- [Migration de Subcontas](database/migrations/2026_03_05_180021_add_asaas_subaccount_fields_to_tenants.php)
- [VALIDACAO_LEGAL_CONTABIL_SUBCONTAS.md](VALIDACAO_LEGAL_CONTABIL_SUBCONTAS.md)

---

**Última atualização:** 05/03/2026, 18:40  
**Status:** ✅ Pronto para deploy e teste  
**Branch:** `sem_split`  
**Próximo passo:** Deploy no VPS! 🚀

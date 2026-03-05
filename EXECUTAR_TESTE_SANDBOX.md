# 🧪 Como Executar o Teste de Validação (Sandbox)

**Data:** 05/03/2026  
**Branch:** `sem_split`  
**Objetivo:** Validar tecnicamente se NF é emitida em nome CORRETO

---

## 🎯 O Que Este Teste Faz

Valida se a Nota Fiscal será emitida em nome do **SALÃO (subconta)** e NÃO em nome do **PagBy (master)**.

**Fluxo:**
1. Cria subconta de teste no Asaas
2. Obtém API key da subconta
3. Cria cobrança usando **API da subconta** (não da master)
4. **Verifica campo `account`** da cobrança
5. **Resultado:**
   - ✅ Se `account` = `subconta_id` → NF em nome do salão (CORRETO!)
   - ❌ Se `account` = `master_id` → NF em nome do PagBy (ERRADO!)

---

## 📋 Pré-requisitos

### 1. Criar Conta Sandbox Asaas

1. Acesse: https://sandbox.asaas.com
2. Clique em **"Criar conta"**
3. Preencha dados (pode ser fictício)
4. Confirme email (verifique spam)
5. Faça login

### 2. Obter API Key

1. No painel Asaas, vá em: **Integrações** → **API Key**
2. Copie a API key (começa com `$aact_...`)

### 3. Configurar .env

Edite o arquivo `.env` (ou crie `.env.testing`):

```env
# API Asaas - SANDBOX
ASAAS_API_URL=https://sandbox.asaas.com/api/v3
ASAAS_API_KEY=sua_api_key_sandbox_aqui
```

**⚠️ IMPORTANTE:** Certifique-se de usar URL do **SANDBOX**, não produção!

---

## 🚀 Executar o Teste

### Método 1: Criação Automática de Tenant Teste

```bash
# Executa teste completo
php artisan asaas:test-subaccount-invoice
```

O comando vai:
- Criar tenant de teste automaticamente
- Criar subconta no Asaas
- Gerar API key
- Criar cobrança
- Validar emissor da NF
- Perguntar se quer salvar evidências

### Método 2: Usar Tenant Existente

```bash
# Usar tenant específico
php artisan asaas:test-subaccount-invoice --tenant=tenantbar
```

### Método 3: Salvar Evidências Automaticamente

```bash
# Salva resultado em arquivo markdown
php artisan asaas:test-subaccount-invoice --save-evidence
```

Gera arquivo: `EVIDENCIA_TESTE_SUBACCOUNT_2026-03-05_143022.md`

---

## 📊 Interpretando o Resultado

### ✅ Resultado POSITIVO (Teste Passou)

Output no terminal:
```
✅✅✅ SUCESSO! ✅✅✅
   Cobrança pertence à SUBCONTA!
   Isso significa que a NF será emitida em nome do SALÃO!

   🎉 MODELO VALIDADO TECNICAMENTE!
   🎉 Pagamentos diretos (100%, sem split) FUNCIONA!
   🎉 NF será emitida em nome CORRETO!
```

**Próximos passos:**
1. ✅ Modelo tecnicamente validado
2. Implementar código completo em produção
3. Contratar contador **COM EVIDÊNCIA** (mostrar print do teste)
4. Perguntar: "A NF sai em nome correto. Está OK fiscalmente?"
5. Consulta vira **confirmatória** (mais barata, R$ 2-3k vs R$ 5-8k)

---

### ❌ Resultado NEGATIVO (Teste Falhou)

Output no terminal:
```
❌❌❌ PROBLEMA! ❌❌❌
   Cobrança NÃO pertence à subconta!
   NF será emitida em nome do PAGBY (master)!

   ⚠️  MODELO PRECISA AJUSTES!
```

**Próximos passos:**
1. Contatar suporte Asaas
2. Perguntar: "Como fazer cobrança criada pela subconta pertencer a ela?"
3. Verificar se precisa configuração especial
4. Se não for possível → avaliar modelo alternativo
5. **NÃO implementar em produção** até resolver

---

## 🔍 Validação Manual no Painel Asaas

Após executar o teste, você pode validar manualmente:

### 1. Login no Sandbox

1. Acesse: https://sandbox.asaas.com
2. Login com sua conta

### 2. Verificar Subcontas

1. Vá em: **Configurações** → **Contas Filhas**
2. Veja se a subconta "Salão Teste Validação NF" aparece
3. Anote o Account ID

### 3. Verificar Cobrança

1. Vá em: **Cobranças** → **Todas as cobranças**
2. Procure a cobrança de R$ 100,00
3. Clique para ver detalhes
4. **Verifique o campo "Conta"** ou "Account"
   - Se mostrar a subconta → ✅ Correto
   - Se mostrar sua conta master → ❌ Incorreto

### 4. Testar Emissão de NF (Opcional)

Se o sandbox permitir:
1. Na cobrança, procure botão "Emitir Nota Fiscal"
2. Configure dados fiscais da subconta
3. Emita NF teste
4. **Verifique nome do emissor na NF**
   - Deve ser: "Salão Teste Validação NF"
   - NÃO deve ser: Seu nome/empresa master

---

## 📸 Documentar Evidências

### Screenshots Importantes

Tire prints de:
1. **Tela de subcontas** mostrando subconta criada
2. **Detalhes da cobrança** mostrando campo "account"
3. **Campo account = subconta_id** (se passou)
4. **Nota fiscal emitida** (se disponível) mostrando emissor

### Salvar Prints

Salve em: `public/evidencias/teste-sandbox-YYYY-MM-DD/`

Estrutura:
```
public/evidencias/teste-sandbox-2026-03-05/
├── 01-subcontas-criadas.png
├── 02-detalhes-cobranca.png
├── 03-campo-account-validado.png
└── 04-nota-fiscal-emissor.png (opcional)
```

---

## 🐛 Troubleshooting

### Erro: "Tenant não encontrado"

```bash
# Verificar tenants disponíveis
php artisan tinker
> Tenant::on('mysql')->pluck('name', 'id');
```

### Erro: "Erro ao criar subconta"

Possíveis causas:
- CPF/CNPJ inválido (use: `24971563000198`)
- Email duplicado (Asaas não aceita)
- Dados obrigatórios faltando

### Erro: "Erro ao criar cobrança"

- Verifique se API key da subconta foi gerada
- Aguarde alguns segundos após criar subconta
- Verifique logs: `storage/logs/laravel.log`

### API retorna 401 Unauthorized

- Verifique se ASAAS_API_KEY está correta no .env
- Confirme que está usando API key do SANDBOX
- Tente gerar nova API key no painel Asaas

---

## 📞 Suporte Asaas

Se teste falhar ou tiver dúvidas:

**Canais de suporte:**
- Email: suporte@asaas.com
- Chat: No painel (canto inferior direito)
- WhatsApp: (48) 3027-5009

**Pergunta específica:**
> "Criamos uma subconta via API. Quando criamos uma cobrança usando a API KEY da SUBCONTA, o campo 'account' da cobrança aponta para a conta master. Como fazer para a cobrança pertencer à subconta? Precisamos que a nota fiscal seja emitida em nome da subconta, não da master."

---

## ✅ Checklist Completo

Antes de executar:
- [ ] Conta criada no sandbox Asaas
- [ ] API key obtida
- [ ] .env configurado com URL sandbox
- [ ] Banco de dados migrado (`php artisan migrate`)

Durante execução:
- [ ] Comando executado sem erros
- [ ] Subconta criada (Account ID mostrado)
- [ ] API key gerada
- [ ] Cobrança criada (Payment ID mostrado)
- [ ] Campo 'account' verificado

Após teste:
- [ ] Resultado interpretado (passou ou falhou)
- [ ] Evidências salvas (arquivo .md)
- [ ] Screenshots tirados do painel Asaas
- [ ] Decisão tomada (prosseguir ou ajustar)

---

## 🎯 Métricas de Sucesso

**Teste PASSA se:**
- ✅ Subconta criada sem erros
- ✅ API key gerada com sucesso
- ✅ Cobrança criada usando API da subconta
- ✅ Campo `account` da cobrança = `subconta_id`
- ✅ **NF será emitida em nome do SALÃO**

**Próximo passo:** Contratar contador com evidência do teste!

---

## 📚 Referências

- [TESTE_CRITICO_SANDBOX_NOTA_FISCAL.md](TESTE_CRITICO_SANDBOX_NOTA_FISCAL.md) - Explicação detalhada
- [PROCEDIMENTO_CRIACAO_SUBCONTAS_APIKEYS.md](PROCEDIMENTO_CRIACAO_SUBCONTAS_APIKEYS.md) - Procedimentos completos
- [VALIDACAO_LEGAL_CONTABIL_SUBCONTAS.md](VALIDACAO_LEGAL_CONTABIL_SUBCONTAS.md) - Análise legal/contábil

---

**Última atualização:** 05/03/2026, 18:00  
**Status:** ✅ Pronto para execução  
**Branch:** `sem_split`  
**Próximo passo:** Executar teste agora! 🚀

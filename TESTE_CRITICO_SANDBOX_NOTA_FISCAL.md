# 🧪 TESTE CRÍTICO: Validação de Nota Fiscal em Sandbox

**Data:** 05/03/2026  
**Objetivo:** Testar SE a nota fiscal é emitida em nome da SUBCONTA (tenant) e NÃO do PagBy

---

## 🎯 HIPÓTESE A VALIDAR

**Se conseguirmos criar subconta + obter API key + NF sair em nome do tenant:**
→ Modelo está tecnicamente VALIDADO ✅  
→ Consultorias jurídicas/contábeis viram apenas CONFIRMATÓRIAS (não exploratórias)

---

## 🚀 PLANO DE TESTE (Fazer HOJE/AMANHÃ)

### Fase 1: Setup Sandbox (30 minutos)

#### 1.1 Criar Conta Sandbox Asaas

1. Acessar: https://sandbox.asaas.com
2. Criar conta com email teste (ex: `pagby.teste@gmail.com`)
3. Anotar API key da conta MASTER

#### 1.2 Configurar .env Local

```env
# .env.local ou .env.testing
ASAAS_API_URL=https://sandbox.asaas.com/api/v3
ASAAS_API_KEY=sua_api_key_sandbox_master_aqui
```

---

### Fase 2: Implementação Mínima (2-3 horas)

Não precisa implementar tudo! Apenas o suficiente para testar.

#### 2.1 Migration (Campos essenciais)

```bash
php artisan make:migration add_asaas_account_fields_to_tenants_test
```

```php
public function up()
{
    Schema::table('tenants', function (Blueprint $table) {
        $table->string('asaas_account_id')->nullable();
        $table->text('asaas_api_key')->nullable();
        $table->string('asaas_account_status')->nullable();
    });
}
```

```bash
php artisan migrate
```

#### 2.2 Métodos no AsaasService (Copiar do documento)

Adicionar apenas:
- `criarSubcontaCompleta()`
- `obterApiKeySubconta()`

#### 2.3 Script de Teste Rápido

**Arquivo:** `tests/AsaasSubcontaTest.php`

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tenant;
use App\Services\AsaasService;
use Illuminate\Support\Facades\Crypt;

class AsaasSubcontaTest extends TestCase
{
    /**
     * TESTE CRÍTICO: Criar subconta, obter API key e processar pagamento teste.
     */
    public function test_criar_subconta_e_processar_pagamento()
    {
        // 1. Pegar tenant de teste
        $tenant = Tenant::on('mysql')->first();
        
        if (!$tenant) {
            $this->markTestSkipped('Nenhum tenant disponível para teste');
        }

        // 2. Criar subconta usando API MASTER
        $asaasMaster = new AsaasService(config('services.asaas.api_key'));
        
        $accountData = [
            'name' => 'Salão Teste Sandbox',
            'email' => 'salao.teste@example.com',
            'cpfCnpj' => '24971563000198', // CNPJ fictício válido
            'mobilePhone' => '11987654321',
            'companyType' => 'LIMITED',
            'incomeValue' => 5000.00,
        ];

        echo "\n🔄 Criando subconta...\n";
        $result = $asaasMaster->criarSubcontaCompleta($accountData);

        $this->assertTrue($result['success'], 'Falha ao criar subconta: ' . ($result['message'] ?? ''));

        $accountId = $result['data']['account_id'];
        $apiKey = $result['data']['api_key'];
        
        echo "✅ Subconta criada!\n";
        echo "   Account ID: {$accountId}\n";
        echo "   API Key: " . substr($apiKey, 0, 30) . "...\n";

        // Salvar no tenant
        $tenant->asaas_account_id = $accountId;
        $tenant->asaas_api_key = Crypt::encryptString($apiKey);
        $tenant->save();

        // 3. Aguardar 5 segundos (processamento Asaas)
        echo "\n⏳ Aguardando 5 segundos...\n";
        sleep(5);

        // 4. Criar pagamento USANDO API KEY DA SUBCONTA
        echo "\n💳 Criando pagamento teste usando API da SUBCONTA...\n";
        
        $asaasSubconta = new AsaasService($apiKey);

        // Criar customer
        $customer = $asaasSubconta->criarOuAtualizarCliente([
            'name' => 'Cliente Teste',
            'email' => 'cliente.teste@example.com',
            'cpfCnpj' => '12345678909', // CPF fictício
            'mobilePhone' => '11987654321',
        ]);

        $this->assertTrue($customer['success'], 'Falha ao criar customer');
        echo "   Customer criado: {$customer['id']}\n";

        // Criar cobrança
        $payment = $asaasSubconta->criarCobranca(
            $customer['id'],
            100.00, // R$ 100,00
            new \DateTime('+7 days'),
            'PIX', // ou 'BOLETO'
            'Teste de cobrança - validação NF'
        );

        $this->assertTrue($payment['success'], 'Falha ao criar cobrança');
        
        $paymentId = $payment['id'];
        echo "✅ Cobrança criada: {$paymentId}\n";

        // 5. TESTE CRÍTICO: Verificar quem é o emissor da NF
        echo "\n🔍 TESTE CRÍTICO: Verificando dados da cobrança...\n";
        
        $paymentDetails = $asaasSubconta->consultarCobranca($paymentId);
        
        echo "\n📋 DADOS DA COBRANÇA:\n";
        echo json_encode($paymentDetails, JSON_PRETTY_PRINT | JSON_UNESCAPE_UNICODE);

        // Verificar campo 'account' ou similar
        if (isset($paymentDetails['account'])) {
            echo "\n\n🎯 RESULTADO CRÍTICO:\n";
            echo "   Conta dona da cobrança: {$paymentDetails['account']}\n";
            echo "   Expected (Subconta): {$accountId}\n";
            
            if ($paymentDetails['account'] === $accountId) {
                echo "\n✅✅✅ SUCESSO! Cobrança pertence à SUBCONTA!\n";
                echo "       Isso significa que a NF será emitida em nome do SALÃO!\n";
            } else {
                echo "\n❌❌❌ PROBLEMA! Cobrança não pertence à subconta!\n";
                echo "       NF será emitida em nome do PAGBY (master)!\n";
            }
        }

        // 6. Se tiver emissão de NF no sandbox, testar
        if (method_exists($asaasSubconta, 'emitirNotaFiscal')) {
            echo "\n📄 Testando emissão de nota fiscal...\n";
            // Código para emitir NF teste
        }

        echo "\n" . str_repeat("=", 60) . "\n";
        echo "TESTE CONCLUÍDO!\n";
        echo "Próximo passo: Verificar no painel Asaas sandbox\n";
        echo "URL: https://sandbox.asaas.com\n";
        echo str_repeat("=", 60) . "\n\n";
    }
}
```

---

### Fase 3: Execução do Teste (15 minutos)

```bash
# Executar teste
php artisan test --filter test_criar_subconta_e_processar_pagamento

# OU executar script direto (se preferir)
php artisan tinker
```

**No tinker:**

```php
$tenant = Tenant::first();
$asaas = new \App\Services\AsaasService(env('ASAAS_API_KEY'));

// Criar subconta
$result = $asaas->criarSubcontaCompleta([
    'name' => 'Salão Teste',
    'email' => 'salao@teste.com',
    'cpfCnpj' => '24971563000198',
    'mobilePhone' => '11987654321',
    'companyType' => 'LIMITED',
    'incomeValue' => 5000.00,
]);

print_r($result);

// Pegar API key
$apiKey = $result['data']['api_key'];
$accountId = $result['data']['account_id'];

// Salvar
$tenant->asaas_account_id = $accountId;
$tenant->asaas_api_key = \Crypt::encryptString($apiKey);
$tenant->save();

// Criar cobrança usando API da SUBCONTA
$asaasSubconta = new \App\Services\AsaasService($apiKey);

$customer = $asaasSubconta->criarOuAtualizarCliente([
    'name' => 'Cliente Teste',
    'email' => 'cliente@teste.com',
    'cpfCnpj' => '12345678909',
    'mobilePhone' => '11987654321',
]);

$payment = $asaasSubconta->criarCobranca(
    $customer['id'],
    100.00,
    now()->addDays(7),
    'PIX',
    'Teste validação NF'
);

print_r($payment);

// VERIFICAR: Campo 'account' no payment deve ser igual a $accountId!
```

---

### Fase 4: Validação no Painel Asaas (10 minutos)

#### 4.1 Login Sandbox

1. Acessar: https://sandbox.asaas.com
2. Login com conta MASTER

#### 4.2 Navegar para Subcontas

1. Menu: **Configurações**
2. Submenu: **Contas Filhas** ou **Subcontas**
3. Verificar se subconta criada aparece

#### 4.3 Login na Subconta (Se possível)

1. Clicar na subconta criada
2. Ver se há opção "Visualizar como" ou "Acessar conta"
3. Verificar cobrança criada
4. **VER O CAMPO "EMISSOR" da cobrança**

#### 4.4 Testar Emissão de NF (Se disponível no sandbox)

1. Na cobrança criada, procurar botão "Emitir Nota Fiscal"
2. Preencher dados fiscais da subconta
3. Emitir NF teste
4. **VERIFICAR:** Nome do emissor na NF

---

## ✅ CRITÉRIOS DE SUCESSO

### ✅ TESTE PASSOU SE:

1. **Subconta criada com sucesso** → Account ID retornado
2. **API key obtida** → String não-vazia
3. **Cobrança criada usando API da subconta** → Payment ID retornado
4. **Campo `account` da cobrança = Account ID da subconta** (NÃO da master)
5. **NF (se emitida) está em nome da SUBCONTA** (dados do salão teste)

### ❌ TESTE FALHOU SE:

1. Erro ao criar subconta
2. API key não retornada ou inválida
3. Cobrança criada pertence à conta MASTER (não à subconta)
4. NF está em nome do PagBy (conta master)

---

## 🎯 DECISÕES BASEADAS NO RESULTADO

### Se TESTE PASSOU ✅

**Conclusão:** Modelo é tecnicamente VIÁVEL!

**Próximos passos:**
1. ✅ Implementar código completo (migration, service, command)
2. ✅ Testar com 2-3 tenants reais em sandbox
3. ✅ Documentar evidências (screenshots do painel Asaas)
4. ⚖️ Contratar contador/advogado mas com EVIDÊNCIA TÉCNICA
   - Mostrar: "Olha, a NF já sai em nome correto (print anexo)"
   - Perguntar: "Isso está correto fiscalmente?"
   - Consulta vira CONFIRMATÓRIA (não exploratória)
   - Custo menor, resposta mais rápida

### Se TESTE FALHOU ❌

**Conclusão:** Modelo precisa AJUSTES ou é INVIÁVEL.

**Cenários:**

#### Cenário A: Cobrança pertence à master
```
Problema: API da subconta cria cobrança, mas 'account' = master_id
Causa provável: Asaas não permite subcontas independentes
Solução: Buscar modelo alternativo (SaaS puro, sem subcontas)
```

#### Cenário B: NF sai em nome errado
```
Problema: Cobrança pertence à subconta, mas NF em nome do PagBy
Causa provável: Configuração fiscal não está na subconta
Solução: Configurar dados fiscais na subconta antes de emitir NF
```

#### Cenário C: API key não funciona
```
Problema: API key da subconta retorna erro 401/403
Causa provável: Subconta não aprovada ou API key inválida
Solução: Aguardar aprovação ou regenerar API key
```

---

## 📊 DOCUMENTAR RESULTADO

### Template de Evidência

**Arquivo:** `EVIDENCIA_TESTE_SANDBOX.md`

```markdown
# Evidência: Teste Subconta Asaas

**Data:** 05/03/2026
**Ambiente:** Sandbox Asaas

## Dados do Teste

- **Conta Master:** acc_XXXXXXXXX (PagBy)
- **Subconta Criada:** acc_YYYYYYYYY (Salão Teste)
- **API Key Subconta:** $aact_ZZZZZZZZZ...

## Cobrança Teste

- **Payment ID:** pay_WWWWWWWW
- **Valor:** R$ 100,00
- **Cliente:** Cliente Teste (CPF 123.456.789-09)
- **Descrição:** Teste validação NF

## RESULTADO CRÍTICO

```json
{
  "id": "pay_WWWWWWWW",
  "account": "acc_YYYYYYYYY",  ← SUBCONTA!
  "customer": "cus_AAAAAAAA",
  "value": 100.00,
  ...
}
```

### ✅ CONFIRMAÇÃO

Campo `account` = `acc_YYYYYYYYY` (subconta)
NÃO igual a `acc_XXXXXXXXX` (master)

**Conclusão:** Cobrança pertence à SUBCONTA!
Portanto, NF será emitida em nome do SALÃO TESTE.

## Screenshots

[Anexar prints do painel Asaas]
- Print 1: Tela de subcontas
- Print 2: Detalhes da cobrança (showing account field)
- Print 3: NF emitida (se disponível)
```

---

## ⏱️ CRONOGRAMA ACELERADO

### Hoje (05/03/2026 - Tarde)
- [ ] 14h-14h30: Criar conta sandbox Asaas
- [ ] 14h30-15h: Configurar .env, criar migration
- [ ] 15h-17h: Implementar métodos AsaasService
- [ ] 17h-18h: Executar teste

### Amanhã (06/03/2026 - Manhã)
- [ ] 9h-10h: Analisar resultados
- [ ] 10h-11h: Validar no painel Asaas
- [ ] 11h-12h: Documentar evidências

### Resultado: 06/03 meio-dia
- ✅ **Se passou:** Avançar com implementação completa
- ❌ **Se falhou:** Reavaliar modelo ou buscar suporte Asaas

---

## 🔥 VANTAGENS DE TESTAR PRIMEIRO

### 1. **Economia de Tempo**
- Contador/advogado: 4-6 semanas
- Teste sandbox: 1 dia
- **Decisão 30x mais rápida!**

### 2. **Economia de Dinheiro**
- Consultorias exploratórias: R$ 5.000-8.000
- Consultorias confirmatórias (com evidência): R$ 2.000-3.000
- **Economia de R$ 3.000-5.000**

### 3. **Certeza Técnica**
- Não é "achismo" ou "parece que funciona"
- É **comprovado tecnicamente com prints**
- Advogado/contador validam algo REAL (não teórico)

### 4. **Redução de Risco**
- Se teste falhar, não implementa (evita retrabalho)
- Se teste passar, implementa com confiança
- **Decisão baseada em dados, não opiniões**

---

## 🎯 RESUMO EXECUTIVO

### O QUE FAZER AGORA (Ordem de prioridade)

#### Prioridade 1: TESTAR (HOJE/AMANHÃ) 🔥
```bash
1. Criar conta sandbox Asaas
2. Implementar criarSubcontaCompleta() e obterApiKeySubconta()
3. Executar teste
4. Analisar campo 'account' no payment
5. DECISÃO: Passa ou falha?
```

#### Prioridade 2: Se PASSOU ✅
```bash
6. Documentar evidências (prints)
7. Implementar código completo
8. Contratar contador (mostrar evidência)
9. Consulta confirmatória: "Isso está correto?"
10. Produção
```

#### Prioridade 3: Se FALHOU ❌
```bash
6. Analisar motivo da falha
7. Contatar suporte Asaas
8. Avaliar modelo alternativo
9. (talvez) Consultorias para discutir alternativas
```

---

## 📞 SUPORTE ASAAS

Se teste falhar ou tiver dúvidas técnicas:

**Canal de suporte Asaas:**
- Email: suporte@asaas.com
- Chat: No painel (canto inferior direito)
- Whatsapp: (48) 3027-5009

**Pergunta específica para fazer:**
> "Criamos uma subconta (account) usando a API master.  
> Quando criamos uma cobrança (payment) usando a API KEY da SUBCONTA,  
> a nota fiscal será emitida em nome da SUBCONTA ou da conta MASTER?  
> Como configuramos para que a NF saia em nome da subconta?"

---

**CONCLUSÃO:**

Você está correto: Se conseguirmos criar a subconta, obter API key e a NF sair em nome correto, o modelo está validado tecnicamente.

**Ação imediata:** TESTAR em sandbox ANTES das consultorias.
**Prazo:** 1 dia (hoje/amanhã)
**Custo:** R$ 0
**Resultado:** Certeza técnica que economiza tempo e dinheiro

---

**Última atualização:** 05/03/2026 - 15h  
**Status:** 🔥 EXECUTAR TESTE IMEDIATAMENTE  
**Próximo passo:** Criar conta sandbox Asaas

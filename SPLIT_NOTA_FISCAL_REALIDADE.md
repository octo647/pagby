# ⚠️ REALIDADE: Split de Pagamentos e Nota Fiscal no Asaas

**Data**: 05/03/2026  
**Fontes**: 
- Atendimento Asaas (Bruno Peres)
- Documentação oficial: https://docs.asaas.com/docs/split-de-pagamentos
- Documentação oficial: https://docs.asaas.com/docs/notas-fiscais

## O que descobrimos

A documentação anterior (`COMPARACAO_MERCADOPAGO_ASAAS.md`) estava **INCORRETA** sobre a emissão de notas fiscais no split.

### ❌ O que pensávamos (ERRADO)

> "Cada parte emite sua NF do valor que recebe no split"
> - PagBy emite NF de R$ 10
> - Tenant emite NF de R$ 90
> - Total = R$ 100 (sem bitributação)

### ✅ O que realmente acontece (CONFIRMADO)

**Segundo o Asaas:**

1. **A nota fiscal é SEMPRE emitida pelo valor TOTAL** pelo prestador do serviço
2. **O split divide apenas o dinheiro recebido**, não afeta a parte fiscal
3. **Impostos são responsabilidade de quem emite a nota** (base de cálculo = valor total)
4. **Recebedores do split não têm relação fiscal com a nota do Asaas**

## Cenário Real no Pagby

### Fluxo Atual
```
Cliente paga R$ 100 ao Salão
↓
Salão emite NF de R$ 100 no Asaas
├─ Base de cálculo para ISS: R$ 100
├─ ISS pago pelo Salão: ~R$ 5,00
└─ Impostos todos por conta do Salão
↓
Split automático (apenas dinheiro):
├─ Salão recebe: R$ 90
└─ PagBy recebe: R$ 10
```

### O Problema Fiscal

**Para o Salão:**
- ✅ Emite NF de R$ 100
- ✅ Paga ISS sobre R$ 100
- ⚠️ Mas recebe apenas R$ 90 (R$ 10 foram para PagBy)
- ❓ Como contabiliza os R$ 10 que saíram?

**Para o PagBy:**
- ❌ Recebe R$ 10 sem nota fiscal própria
- ❌ Precisa declarar essa receita no IR
- ❓ Como emitir NF desses R$ 10 sem causar bitributação?

## Possíveis Soluções

### Opção 1: Contabilizar como Despesa (Salão) e Receita (PagBy)

**Salão:**
- Emite NF de R$ 100
- Paga ISS sobre R$ 100
- Contabiliza R$ 10 como "despesa com software/plataforma"

**PagBy:**
- Emite NF de serviço (SaaS/licença) de R$ 10 para o Salão
- Paga ISS sobre R$ 10
- Declara R$ 10 como receita

**Problema:** Bitributação parcial (ISS pago 2x sobre os mesmos R$ 10)

### Opção 2: Acordo Contratual de Parceria Comercial

**Salão:**
- Emite NF de R$ 90 (valor líquido que recebe)
- Paga ISS apenas sobre R$ 90

**PagBy:**
- Emite NF de R$ 10 (comissão/taxa de plataforma)
- Paga ISS sobre R$ 10

**Vantagem:** Sem bitributação  
**Problema:** O Asaas não faz isso automaticamente - requer emissão manual de NFs

### Opção 3: PagBy como Intermediador (com autorização BC)

**PagBy:**
- Registra-se como instituição de pagamento no Banco Central
- Pode receber 100% e repassar valores legalmente
- Emite documentos fiscais adequados

**Problema:** Complexo, caro, regulamentação pesada

## Recomendação do Contador

**CRÍTICO**: Consultar contador ANTES de lançar sistema de split em produção!

### Perguntas para o contador:

1. Como o Salão deve contabilizar os R$ 10 que vão para PagBy via split?
2. PagBy deve emitir NF de serviço de software ou de comissão?
3. Há diferença tributária entre "taxa de plataforma" vs "comissão de vendas"?
4. Como evitar bitributação do ISS?
5. Qual regime tributário minimiza impostos para ambas as partes?

## Comparação: Split vs Modelo SaaS Puro

### Modelo Atual (Split - PROBLEMÁTICO)
```
Pagamento do cliente: R$ 100
├─ Salão: NF de R$ 100, ISS de R$ 5, recebe R$ 90
└─ PagBy: recebe R$ 10, precisa emitir NF, paga ISS novamente?
```

### Modelo SaaS Tradicional (SIMPLES)
```
Pagamento do cliente: R$ 100
├─ Salão: NF de R$ 100, ISS de R$ 5, recebe R$ 100
└─ PagBy: NF mensal de assinatura (R$ 50/mês), ISS próprio

Salão paga assinatura separadamente (boleto/cartão)
```

**Vantagens SaaS puro:**
- ✅ Sem ambiguidade fiscal
- ✅ Notas fiscais totalmente separadas
- ✅ Contabilidade clara
- ❌ Salão precisa ter dinheiro para pagar assinatura

## Ação Imediata Necessária

### 1. Pausar implementação de split para clientes finais
Não ativar split para pagamentos de serviços dos salões até resolver questão fiscal.

### 2. Manter split apenas para assinaturas PagBy
O split atual de assinaturas (Tenant paga PagBy) pode continuar, pois:
- É um pagamento entre empresas (B2B)
- Tenant emite NF da assinatura que cobra do PagBy
- Fica mais claro contabilmente

### 3. Consultar especialista tributário
Agendar com contador especializado em SaaS/Marketplaces.

### 4. Documentar decisão fiscal
Criar política clara de como serão tratadas as NFs e impostos.

## Próximos Passos

- [ ] Reunião com contador (urgente!)
- [ ] Definir modelo fiscal correto
- [ ] Atualizar contratos com tenants
- [ ] Documentar processo de emissão de NF
- [ ] Treinar equipe sobre questões fiscais
- [ ] Revisar código se necessário ajustar percentuais ou modelo

## Atualização na Documentação

Arquivos que precisam ser corrigidos:
- ❌ `COMPARACAO_MERCADOPAGO_ASAAS.md` - Informações incorretas sobre NF
- ❌ Seção "Aspectos Fiscais" está baseada em premissa errada
- ✅ Este arquivo (`SPLIT_NOTA_FISCAL_REALIDADE.md`) contém informações corretas

---

**⚠️ DISCLAIMER**: Este documento reflete a realidade técnica do Asaas conforme informado pelo suporte. As implicações fiscais e contábeis devem ser validadas com contador especializado antes de implementação em produção.

---

## Confrontação: Atendente vs Documentação Oficial

### O que a Documentação Oficial diz

#### Sobre Split de Pagamentos

Da documentação [docs.asaas.com/docs/split-de-pagamentos](https://docs.asaas.com/docs/split-de-pagamentos):

> "O split de pagamento é uma funcionalidade que permite 'dividir' valores recebidos através dos pagamentos entre uma ou várias carteiras (contas ASAAS) automaticamente."

> "O valor do split sempre será feito em cima do `netValue` que é o valor da cobrança descontados os valores de taxas aplicadas."

**Exemplo da documentação:**
```
João faz uma venda de R$ 200,00 e Marcelo deve receber 20%.
A cobrança é criada na conta do João (pois é ele quem fez venda).
Ao registrar o recebimento da cobrança, o Asaas fará o débito 
desses 20% da conta do João e creditará os 20% na conta de Marcelo.
```

**⚠️ NOTA IMPORTANTE:** A documentação oficial do Asaas:
- ✅ Explica claramente o split **financeiro** (divisão automática de valores)
- ❌ **NÃO menciona** split de notas fiscais
- ❌ **NÃO menciona** como emitir notas fiscais separadas no split
- ❌ **NÃO explica** responsabilidade tributária do split

#### Sobre Notas Fiscais

Da documentação [docs.asaas.com/docs/notas-fiscais](https://docs.asaas.com/docs/notas-fiscais):

> "O Asaas possibilita que empresas (pessoa jurídica) emitam Notas Fiscais de serviço para seus clientes. É possível emitir uma Nota Fiscal atrelada a cobranças já existentes ou avulsas."

**Processo de emissão:**
1. Listar configurações municipais
2. Criar/atualizar configurações municipais
3. Listar serviços municipais
4. Agendar nota fiscal (vinculada a um `payment`)

**⚠️ NOTA IMPORTANTE:** A documentação de notas fiscais:
- ✅ Foca em emissão de NF vinculada a **cobranças (`payment`)**
- ❌ **NÃO menciona** como lidar com NF quando há split
- ❌ **NÃO explica** se cada parte do split emite NF separada
- ❌ **NÃO orienta** sobre responsabilidade tributária no split

### O que o Atendente do Asaas Disse

**Bruno Peres (Suporte Asaas) - 05/03/2026:**

> "No Asaas, a nota fiscal é sempre emitida pelo valor total do serviço, independente da divisão do recebimento entre contas."

> "O split atua só na divisão do dinheiro recebido, não na base de cálculo do imposto."

> "A parte fiscal (valor da nota, ISS, demais tributos) continua sendo de responsabilidade da empresa que está emitindo a nota, conforme o regime tributário dela e a orientação da contabilidade."

> **"Questões de tributos e valores fiscais ficarão com a empresa responsável pela emissão da nota. Recebedores do valor do split não têm, ou não devem ter, relação com isso."**

> Sobre os valores repassados: "É uma questão totalmente contábil/jurídica entre vocês, sabe? É entender o que foi combinado por aí."

### Análise: Confrontando as Fontes

| Aspecto | Documentação Oficial | Atendente Asaas | Status |
|---------|---------------------|-----------------|--------|
| **Split divide dinheiro** | ✅ Claramente documentado | ✅ Confirmado | ✅ ALINHADO |
| **Split automático** | ✅ Funciona via API | ✅ Confirmado | ✅ ALINHADO |
| **Split em valor líquido** | ✅ Sobre `netValue` (após taxas) | ✅ Confirmado | ✅ ALINHADO |
| **NF pelo valor total** | ❌ Não menciona | ✅ Confirma: "sempre valor total" | ⚠️ GAP na documentação |
| **Split não afeta NF** | ❌ Não menciona | ✅ Confirma: "split só divide $" | ⚠️ GAP na documentação |
| **Responsabilidade fiscal** | ❌ Não menciona | ✅ Confirma: "quem emite NF" | ⚠️ GAP na documentação |
| **Split recebedor e NF** | ❌ Não menciona | ✅ Confirma: "não tem relação" | ⚠️ GAP na documentação |
| **Questão tributária** | ❌ Não menciona | ✅ "Contábil/jurídica entre partes" | ⚠️ GAP na documentação |

### Conclusão da Confrontação

✅ **O atendente está 100% correto** - O que ele disse está alinhado com a documentação técnica disponível.

⚠️ **A documentação oficial tem GAPS críticos** - A documentação do Asaas:
- Foca apenas no aspecto técnico/financeiro do split
- **Não orienta** sobre aspectos fiscais e tributários
- **Não explica** a relação entre split e nota fiscal
- **Não esclarece** responsabilidades de cada parte no split

⚠️ **A documentação do projeto estava ERRADA** - Assumimos incorretamente que:
- "Cada parte emite sua parcela da NF" ❌ FALSO
- "Split resolve questões fiscais" ❌ FALSO
- "Sem bitributação automaticamente" ❌ FALSO

---

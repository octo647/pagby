# 🏪 Modelo de Negócio Real do Pagby - Marketplace de Assinaturas

**Data**: 05/03/2026  
**Atualização**: Esclarecimento do modelo de negócio correto

---

## O Modelo Real do Pagby

### ❌ O que NÃO é:
- Sistema de agendamento simples onde cliente paga serviço avulso
- SaaS puro onde salão paga assinatura fixa

### ✅ O que REALMENTE é:
**Marketplace de Planos de Assinatura para Salões de Beleza**

O PagBy permite que salões criem e gerenciem **seus próprios planos de assinatura recorrente** para clientes.

---

## Fluxo Completo

### Camada 1: Salão → PagBy (B2B)
```
Salão Bella Vista assina o PagBy
├─ Plano: Premium
├─ Valor: R$ 179,70/mês
├─ Benefício: Acesso à plataforma de gestão de assinaturas
└─ Split: NÃO (valor integral para PagBy)
```

### Camada 2: Cliente → Salão (B2C via PagBy)
```
Cliente João Silva assina plano criado pelo salão
├─ Plano: "Corte e Barba Premium"
├─ Valor: R$ 100,00/mês
├─ Prestador: Salão Bella Vista
├─ Plataforma: PagBy (processamento)
└─ Split: SIM (95% salão / 5% PagBy)
```

**Pagamento de R$ 100:**
```
Cliente João → R$ 100
↓
Asaas processa (taxa ~5%): R$ 100 - R$ 5 = R$ 95
↓
Split automático:
├─ Salão Bella Vista: R$ 90,25 (95% de R$ 95)
└─ PagBy: R$ 4,75 (5% de R$ 95 = comissão da plataforma)
```

---

## Comparação com Outros Marketplaces

| Aspecto | iFood | Uber | PagBy |
|---------|-------|------|-------|
| **Quem oferece serviço?** | Restaurante | Motorista | Salão |
| **O que o cliente compra?** | Comida | Corrida | Plano de assinatura |
| **Quem emite NF ao cliente?** | Restaurante | Motorista* | **Salão (provável)** |
| **Comissão da plataforma** | ~30% | ~25% | 5% |
| **Como plataforma recebe?** | NF ao restaurante | NF ao motorista | **Split automático (?)** |
| **Bitributação ISS?** | Sim (aceito) | Sim (aceito) | **A DEFINIR** |

*Motorista Uber: dependendo do município, pode ser isento de ISS

---

## O Problema Fiscal no PagBy

### Cenário Atual (sem orientação contador)

**1. Cliente paga R$ 100 por "Plano Corte e Barba"**
- Cliente é pessoa física (CPF)
- Assinou plano oferecido pelo Salão Bella Vista
- Pagamento processado via PagBy/Asaas

**2. Salão Bella Vista precisa emitir NF**
- É o prestador de serviço de beleza
- Deve emitir NF de serviço
- **Valor da NF: R$ 100? R$ 95? R$ 90,25?** ❓

**3. Salão paga ISS**
- Sobre qual valor? R$ 100 (o que cliente pagou)? ✅ Mais provável
- Alíquota: ~5% = R$ 5,00

**4. PagBy recebe R$ 4,75 via split**
- É receita do PagBy? Sim ✅
- Precisa emitir NF? **Provavelmente SIM** ⚠️
- Para quem? **Para o salão** (mais provável)
- Como? NF de "comissão de intermediação" ou "taxa de plataforma"?
- Paga ISS? **Provavelmente SIM** = ~R$ 0,24

**5. Resultado final:**
```
Cliente pagou: R$ 100,00
Salão emitiu NF: R$ 100,00
Salão pagou ISS: R$ 5,00 (5%)
Salão pagou Asaas: R$ 5,00 (5%)
Salão pagou PagBy: R$ 4,75 (comissão via split)
Salão recebeu líquido: R$ 85,25

PagBy recebeu: R$ 4,75
PagBy deve emitir NF: R$ 4,75 (ao salão)
PagBy deve pagar ISS: ~R$ 0,24 (5%)
PagBy líquido: R$ 4,51

IMPOSTOS TOTAIS:
- ISS Salão: R$ 5,00
- ISS PagBy: R$ 0,24
- TOTAL: R$ 5,24 sobre serviço de R$ 100 (5,24% efetivo) ← Bitributação!
```

---

## Comparação Fiscal: iFood vs PagBy

### iFood (modelo estabelecido)
```
Cliente paga: R$ 100 (pedido de comida)
↓
Restaurante emite NF: R$ 100 ao cliente
Restaurante paga ISS: R$ 5 (5%)
↓
iFood cobra: R$ 30 de comissão
iFood emite NF: R$ 30 ao restaurante (serviço de intermediação)
iFood paga ISS: R$ 1,50 (5%)
↓
Restaurante líquido: R$ 70
Impostos totais: R$ 6,50 (6,5% sobre R$ 100)
```
**✅ Bitributação aceita pelo mercado e regulamentada**

### PagBy (modelo similar, MAS...)
```
Cliente paga: R$ 100 (plano de assinatura)
↓
Salão emite NF: R$ 100 ao cliente
Salão paga ISS: R$ 5 (5%)
↓
PagBy recebe via split: R$ 4,75
PagBy emite NF: R$ 4,75 ao salão (?)
PagBy paga ISS: R$ 0,24 (5%)
↓
Salão líquido: R$ 85,25
Impostos totais: R$ 5,24 (5,24% sobre R$ 100)
```
**⚠️ Bitributação precisa ser validada com contador**

---

## Perguntas URGENTES para o Contador

### 1. Emissão de NF pelo Salão
**Pergunta:** Qual valor o salão deve colocar na NF emitida ao cliente?

- [ ] **R$ 100** (valor total que cliente pagou)
  - Vantagem: Transparente, cliente pagou isso
  - Desvantagem: Salão paga ISS sobre R$ 100 mas recebe R$ 85,25
  
- [ ] **R$ 95,25** (valor líquido que salão recebe)
  - Vantagem: Alinhado com o que recebe
  - Desvantagem: Cliente pagou R$ 100, não R$ 95,25 (pode gerar confusão)

- [ ] **R$ 95** (valor antes do split PagBy)
  - Vantagem: Dedica apenas taxas de processamento
  - Desvantagem: Ainda desalinhado com recebimento

**Recomendação esperada:** R$ 100 (mais transparente e comum em marketplaces)

---

### 2. Comissão do PagBy
**Pergunta:** Como PagBy deve contabilizar/tributar os R$ 4,75?

- [ ] **Emitir NF de comissão ao salão**
  - Natureza: "Comissão por intermediação de pagamento recorrente"
  - ISS: Sim (~R$ 0,24)
  - Para: Salão Bella Vista (CNPJ)
  
- [ ] **Emitir NF de taxa de plataforma ao salão**
  - Natureza: "Taxa de uso da plataforma SaaS"
  - ISS: Sim (~R$ 0,24)
  - Para: Salão Bella Vista (CNPJ)

- [ ] **Não emitir NF (só se for split interno entre contas PagBy)**
  - Risco: Receita sem documentação fiscal

**Recomendação esperada:** Emitir NF de comissão ou taxa ao salão

---

### 3. Dedução da Base de ISS pelo Salão
**Pergunta:** Salão pode deduzir a comissão PagBy da base de cálculo do ISS?

**Exemplo:**
```
Cliente pagou: R$ 100
Comissão PagBy: R$ 4,75
Base de ISS do salão: R$ 100 - R$ 4,75 = R$ 95,25?
ISS reduzido: R$ 95,25 × 5% = R$ 4,76 (em vez de R$ 5)
```

**Isso é legal/permitido?**
- Depende da legislação municipal (ISS é municipal)
- Alguns municípios permitem dedução de comissões
- Outros exigem ISS sobre valor bruto

---

### 4. Contrato Cliente-Salão-PagBy
**Pergunta:** Como deve ser a relação contratual?

**Opção A: Cliente contrata com Salão (PagBy é processador)**
```
Termos de Uso (Cliente aceita):
"Você está assinando o plano 'Corte e Barba' do Salão Bella Vista.
O pagamento é processado via PagBy/Asaas.
Uma taxa de processamento de 5% do valor é cobrada pela plataforma."
```

**Opção B: Cliente contrata com PagBy (Salão é prestador)**
```
Termos de Uso (Cliente aceita):
"Você está assinando um plano na plataforma PagBy.
O serviço será prestado pelo Salão Bella Vista.
O valor será dividido entre o salão e a plataforma."
```

**Qual é mais adequado fiscal e legalmente?**
- Opção A: Cliente ↔ Salão (PagBy é intermediário) ← Mais comum em marketplaces
- Opção B: Cliente ↔ PagBy (Salão é fornecedor) ← Pode requerer autorização BC

---

### 5. Comparação com Concorrentes
**Pergunta:** Como outros marketplaces de beleza/wellness fazem?

**Pesquisar:**
- Gympass (academia)
- TotalPass (studios de fitness)
- Urban Sports Club (Europa)
- Wellhub (antigo Gympass)

**Modelo deles:**
- Cliente assina com a plataforma OU com o estabelecimento?
- Como funciona a parte fiscal?
- Bitributação é aceita?

---

## Modelos Alternativos (Caso Atual Seja Problemático)

### Alternativa 1: PagBy como Recebedor Principal (Estilo Gympass)

**Mudança:** Cliente assina com PagBy, não com o salão

```
Cliente João assina "Plano Corte e Barba" na plataforma PagBy
↓
PagBy emite NF de R$ 100 ao cliente João
PagBy paga ISS sobre R$ 100
↓
PagBy repassa R$ 95 ao Salão Bella Vista
↓
Salão Bella Vista emite NF de R$ 95 ao PagBy (prestação de serviço B2B)
Salão paga ISS sobre R$ 95
```

**Vantagens:**
- ✅ Fiscal claro: Cada um emite NF e paga ISS sobre o que recebe
- ✅ Cliente tem contrato claro com plataforma

**Desvantagens:**
- ❌ PagBy pode precisar autorização Banco Central (instituição de pagamento)
- ❌ Maior complexidade operacional
- ❌ PagBy assume responsabilidade perante o cliente

---

### Alternativa 2: Taxa Fixa + Taxa Variável Híbrida

**Mudança:** Combinar assinatura fixa com comissão baixa

```
Salão paga ao PagBy:
├─ Assinatura fixa: R$ 100/mês (acesso à plataforma)
└─ Comissão: 2% sobre pagamentos processados (em vez de 5%)

Cliente paga R$ 100 ao salão:
├─ Salão recebe: R$ 98 (após 2% de comissão)
└─ PagBy recebe: R$ 2 (comissão reduzida)
```

**Vantagens:**
- ✅ Receita recorrente garantida para PagBy (assinatura)
- ✅ Comissão menor = menos impacto fiscal
- ✅ Emissão de NF mais clara (assinatura B2B + comissão separadas)

**Desvantagens:**
- ❌ Salão pode preferir só pagar comissão (sem fixo)
- ❌ Menor receita total para PagBy

---

### Alternativa 3: Apenas Assinatura (SaaS Puro) - SEM Split

**Mudança:** Eliminar split completamente

```
Salão paga ao PagBy: R$ 200/mês (fixo)
Cliente paga ao salão: R$ 100 (salão recebe 100%, sem split)
```

**Vantagens:**
- ✅ SEM complicação fiscal do split
- ✅ Modelo SaaS tradicional, claro
- ✅ Fácil de contabilizar

**Desvantagens:**
- ❌ PagBy não escala com volume do salão
- ❌ Salão pode achar caro se tiver poucos clientes
- ❌ PagBy perde receita potencial de salões grandes

---

## Ação Imediata Recomendada

### 1. 🛑 PAUSAR ativação de split em produção
Não ativar split de pagamentos para novos salões até ter:
- Parecer do contador
- Contratos revisados
- Modelo fiscal definido

### 2. 📞 Agendar consultoria tributária URGENTE
Usar o documento [ORIENTACOES_CONTADOR_SPLIT.md](ORIENTACOES_CONTADOR_SPLIT.md) como base, atualizando para refletir:
- PagBy é marketplace de assinaturas
- Salão cria planos para clientes
- Split de 95%/5% em pagamentos B2C

### 3. 📋 Pesquisar concorrentes
Descobrir como outros marketplaces de wellness/beleza fazem:
- Estrutura contratual
- Emissão de NFs
- Tratamento fiscal

### 4. ⚖️ Consultar advogado especializado em marketplace
Questões:
- PagBy precisa autorização BC?
- Contratos precisam de cláusulas específicas?
- Responsabilidade solidária em caso de problemas?

### 5. 🧮 Simular cenários fiscais
Calcular carga tributária total em diferentes modelos:
- Modelo atual (split 5%)
- Híbrido (fixo + 2% comissão)
- SaaS puro (sem split)

---

## Cronograma Sugerido

| Prazo | Ação |
|-------|------|
| **Semana 1** | Agendar contador + advogado especializado |
| **Semana 2** | Reuniões de consultoria (2-3h cada) |
| **Semana 3** | Análise de pareceres e definição de modelo |
| **Semana 4** | Ajustes contratuais e documentação |
| **Mês 2** | Implementação técnica de ajustes |
| **Mês 2** | Testes com salões parceiros (piloto) |
| **Mês 3** | Roll-out gradual em produção |

**Investimento:** R$ 5.000 - R$ 15.000 (consultoria tributária + jurídica)  
**Retorno:** Segurança jurídica e fiscal, evita multas futuras

---

## Conclusão

O modelo de negócio do PagBy (marketplace de assinaturas) é **válido e tem precedentes** (iFood, Uber, Gympass), **MAS** a implementação fiscal precisa ser:

1. ✅ **Validada por contador especializado**
2. ✅ **Estruturada contratualmente de forma clara**
3. ✅ **Documentada para evitar problemas futuros**

**Não subestime a complexidade fiscal de um marketplace.** Empresas grandes gastam milhões em adequação tributária.

---

**Elaborado:** 05/03/2026  
**Próximos passos:** Consultorias especializadas (contador + advogado)

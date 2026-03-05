# 📋 Orientações para Consultar Contador - Split de Pagamentos

**Data**: 05/03/2026  
**Contexto**: Implementação de split de pagamentos no Pagby (plataforma SaaS para salões de beleza)

## Contexto do Negócio

**PagBy** é uma plataforma SaaS que conecta:
- **Salões de beleza** (tenants) que prestam serviços aos clientes finais
- **PagBy** que fornece a plataforma/tecnologia

### Modelo de Receita Proposto

**Split de pagamentos:**
- Cliente paga R$ 100 ao salão por serviço de beleza
- Asaas cobra taxa de ~5% = R$ 5
- Valor líquido: R$ 95
- **Split automático:**
  - Salão recebe: R$ 85,50 (90%)
  - PagBy recebe: R$ 9,50 (10%)

## O Problema Fiscal Descoberto

Segundo o **suporte do Asaas**:
> "A nota fiscal é sempre emitida pelo valor total do serviço, independente do split."  
> "O split divide só o dinheiro, não os impostos."  
> "Questões tributárias são contábeis/jurídicas entre as partes."

### Implicações:
1. Salão emite NF de R$ 100 (serviço prestado ao cliente)
2. Salão paga ISS sobre R$ 100
3. Salão recebe apenas R$ 85,50 (R$ 9,50 foram para PagBy via split)
4. PagBy recebe R$ 9,50, mas **não tem NF automática** desse valor
5. **Risco de bitributação** ou receita sem documento fiscal

---

## Perguntas Críticas para o Contador

### 1. Natureza da Receita do PagBy

**Pergunta:** Como deve ser classificada a receita de R$ 9,50 que PagBy recebe via split?

**Opções:**
- [ ] A) **Taxa de licença de software** (SaaS)
- [ ] B) **Comissão de intermediação** (marketplace)
- [ ] C) **Taxa de administração de plataforma**
- [ ] D) **Outra classificação:**_____________________

**Implicação tributária:** Cada classificação tem tratamento fiscal diferente (ISS, ISSQN, regime de tributação específico)

---

### 2. Emissão de Nota Fiscal pelo Salão

**Pergunta:** Como o salão deve emitir a Nota Fiscal?

**Cenário atual:**
- Cliente paga R$ 100 por serviço de beleza
- Salão recebe R$ 85,50 (após split de R$ 9,50 + taxa Asaas de R$ 5)

**Opções:**
- [ ] A) **NF de R$ 100** (valor total) → Salão paga ISS sobre R$ 100, contabiliza R$ 9,50 como despesa
- [ ] B) **NF de R$ 85,50** (valor líquido) → Salão paga ISS sobre R$ 85,50, PagBy emite NF separada
- [ ] C) **NF de R$ 95** (valor antes do split) → Contabiliza split como "dedução de plataforma"
- [ ] D) **Outra solução:**_____________________

**Qual é a solução correta e legal?**

---

### 3. Emissão de Nota Fiscal pelo PagBy

**Pergunta:** PagBy deve emitir Nota Fiscal dos R$ 9,50?

**Opções:**
- [ ] A) **Sim, sempre** → NF de serviço de SaaS/plataforma
- [ ] B) **Sim, se salão emitir NF do total** → Para permitir dedução contábil do salão
- [ ] C) **Não precisa** → Split é apenas repasse, não é receita tributável
- [ ] D) **Depende do regime tributário** (Simples Nacional, Lucro Presumido, etc.)

**Se SIM, qual o tomador da NF?**
- [ ] A) O salão (PagBy presta serviço ao salão)
- [ ] B) O cliente final (PagBy co-presta serviço junto com salão)
- [ ] C) Nota avulsa sem tomador específico

---

### 4. Bitributação do ISS

**Pergunta:** Como evitar bitribuição do ISS?

**Problema identificado:**
- Salão paga ISS sobre R$ 100
- PagBy precisa pagar ISS sobre R$ 9,50
- **Total de ISS:** ~R$ 5,45 (sobre R$ 109,50!)
- Mas o serviço foi de apenas R$ 100

**Soluções possíveis:**
- [ ] A) Salão **deduz** os R$ 9,50 da base de cálculo → NF de R$ 90,50, ISS sobre R$ 90,50
- [ ] B) PagBy **não paga ISS** → Classifica receita de forma que não incida ISS
- [ ] C) **Acordo contratual** específico → Documentação que justifica o split
- [ ] D) **É inevitável** → Custo do modelo de negócio

**Qual a solução correta segundo a legislação?**

---

### 5. Documentação Contábil do Split

**Pergunta:** Como contabilizar o split?

**Para o Salão:**
- Receita de R$ 100 (serviço prestado)?
- Despesa de R$ 9,50 (taxa de plataforma)?
- Ou receita líquida de R$ 85,50 direto?

**Para o PagBy:**
- Receita de R$ 9,50 (taxa de plataforma)?
- Origem: múltiplos salões (milhares de transações/mês)

**Documentação necessária:**
- [ ] Contrato de adesão entre PagBy e Salão (especificando split)
- [ ] Termos de uso da plataforma
- [ ] Relatórios detalhados de split para contabilidade
- [ ] Nota fiscal do PagBy para cada transação
- [ ] Demonstrativo mensal consolidado

**O que é obrigatório e o que é recomendado?**

---

### 6. Retenção de Impostos

**Pergunta:** Há retenção de impostos na fonte no split?

**Cenário:**
- PagBy é PJ (CNPJ, regime Simples Nacional ou Lucro Presumido)
- Salão é PJ (CNPJ, maioria no Simples Nacional)

**Questões:**
- [ ] PagBy deve reter IRRF, CSRF, PIS, COFINS sobre os R$ 9,50?
- [ ] Salão deve reter algo ao "pagar" indiretamente pelo split?
- [ ] Como isso funciona em split automático (sem transferência manual)?

---

### 7. Conformidade com Lei de Intermediação

**Pergunta:** PagBy está caracterizado como instituição de pagamento?

Segundo **Lei nº 12.865/2013** e **Circular BCB nº 3.682/2013**:
> "Intermediação de Pagamentos: caracteriza-se quando uma parte recebe recursos de terceiros e repassa posteriormente."

**No modelo de split:**
- PagBy **NÃO recebe** o valor total e repassa ✅
- Asaas faz split automático direto ✅
- PagBy apenas recebe sua parte separadamente ✅

**Confirmação:** Esse modelo está **em conformidade** com a legislação Banco Central?

**Documentação necessária:**
- [ ] Parecer jurídico sobre não-caracterização como instituição de pagamento
- [ ] Comprovantes de que PagBy não "toca" no dinheiro do salão
- [ ] Contratos explicitando que Asaas faz o split

---

### 8. Regime Tributário Recomendado

**Pergunta:** Qual o melhor regime tributário para PagBy?

**Cenários:**

#### Simples Nacional
- Vantagens: Tributação unificada, alíquota reduzida
- Desvantagens: Limite de faturamento (R$ 4,8mi/ano)
- ISS: Incluído no DAS (2% a 5% dependendo da faixa)

#### Lucro Presumido
- Vantagens: Sem limite de faturamento
- Desvantagens: Tributação mais alta (~11,33% + ISS de 2-5%)
- Base de cálculo: 32% da receita bruta

#### Lucro Real
- Vantagens: Tributação sobre lucro real
- Desvantagens: Complexidade contábil alta
- Indicado para margens baixas

**Considerações específicas:**
- Volume projetado: ~1000 salões × R$ 200/mês = R$ 200k/mês (R$ 2,4mi/ano)
- Margem: 10% via split + assinatura mensal fixa
- Despesas operacionais: infraestrutura, equipe, marketing

**Qual regime é mais vantajoso para esse modelo de negócio?**

---

## Alternativas ao Split - Para Comparação

### Modelo 1: SaaS Puro (Sem Split)

**Funcionamento:**
- Cliente paga R$ 100 ao salão
- Salão emite NF de R$ 100, recebe R$ 95 (após taxas Asaas)
- PagBy **não participa** do pagamento do cliente
- Salão paga **assinatura mensal** ao PagBy (ex: R$ 50/mês)
- PagBy emite NF de R$ 50 mensalmente

**Vantagens fiscais:**
- ✅ Notas fiscais totalmente separadas
- ✅ Sem ambiguidade tributária
- ✅ Contabilidade clara

**Desvantagens:**
- ❌ Salão precisa ter dinheiro para pagar assinatura
- ❌ PagBy tem receita fixa (não escala com volume)
- ❌ Risco de inadimplência

---

### Modelo 2: Marketplace Puro

**Funcionamento:**
- Cliente paga R$ 100 ao **PagBy**
- PagBy emite NF de R$ 100 ao cliente
- PagBy **repassa** R$ 90 ao salão
- Salão emite NF de R$ 90 ao PagBy (prestação de serviço)

**Vantagens:**
- ✅ Modelo claro: PagBy é marketplace
- ✅ PagBy controla toda a experiência de pagamento

**Desvantagens:**
- ❌ PagBy precisa autorização do Banco Central (instituição de pagamento)
- ❌ Complexidade regulatória alta
- ❌ Bitributação: ISS sobre R$ 100 (PagBy) + ISS sobre R$ 90 (Salão)

---

### Modelo 3: Comissionamento Inverso

**Funcionamento:**
- Cliente paga R$ 100 ao salão
- Salão emite NF de R$ 100, recebe R$ 95
- Salão **emite NF de comissão** de R$ 9,50 ao PagBy
- Salão paga R$ 9,50 ao PagBy via transferência/boleto

**Vantagens:**
- ✅ Notas fiscais claras para ambos
- ✅ Sem bitributação

**Desvantagens:**
- ❌ Salão precisa fazer pagamento manual
- ❌ Risco de inadimplência
- ❌ Operacionalmente complexo

---

## Perguntas sobre Alternativas

**Qual modelo o contador recomenda para minimizar:**
1. Carga tributária total? ____________
2. Complexidade operacional? ____________
3. Risco fiscal/legal? ____________

**É possível combinar modelos?** (ex: split + assinatura mínima mensal)

---

## Documentação a Preparar

**Antes da consulta, reunir:**
- [ ] Contratos entre PagBy e Salões (modelo)
- [ ] Termos de uso da plataforma
- [ ] Exemplos de extratos de split do Asaas
- [ ] Projeções de faturamento (1 ano, 3 anos)
- [ ] Estrutura societária do PagBy (CNPJ, sócios, capital social)
- [ ] Regime tributário atual do PagBy
- [ ] Quantidade estimada de transações/mês

**Documentos que o contador pode solicitar:**
- [ ] Documentação técnica do Asaas sobre split
- [ ] Parecer jurídico sobre modelo de negócio
- [ ] Análise de concorrentes (como eles fazem?)

---

## Resultado Esperado da Consultoria

**Deliverables do contador:**
1. **Parecer formal** sobre o modelo fiscal mais adequado
2. **Manual de procedimentos** para emissão de NFs
3. **Contrato padrão** revisado juridicamente (entre PagBy e Salões)
4. **Plano de ação** com prazos para adequações
5. **Treinamento** para equipe contábil da PagBy

**Custo estimado:** R$ 3.000 a R$ 10.000 (consultoria especializada em SaaS/Marketplaces)

**Prazo:** 2-4 semanas para análise completa

---

## ⚠️ AÇÃO IMEDIATA

**Até ter parecer do contador:**
1. **NÃO ativar** split para pagamentos de clientes em produção
2. **Manter apenas** split para assinaturas PagBy (entre empresas, mais claro)
3. **Usar modelo SaaS puro** temporariamente (assinatura mensal fixa)
4. **Documentar tudo** para apresentar ao contador

---

**Elaborado para:** Consulta com contador especializado em SaaS/Marketplace  
**Próximo passo:** Agendar reunião de 2-3h com contador

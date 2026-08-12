# ✅ Checklist Executivo: Validação Modelo Subcontas

**Status do Modelo:** 🟡 AGUARDANDO VALIDAÇÃO PROFISSIONAL  
**Implementação em Produção:** ❌ NÃO AUTORIZADA até conclusão de todas validações críticas

---

## 🎯 Resumo Executivo

**Modelo a Validar:**
- Cliente paga 100% direto na subconta do salão (sem split)
- Salão paga R$ 30 ou R$ 80/mês de assinatura ao PagBy
- Cada um emite sua própria NF e paga seus próprios impostos

**Status Atual:** Modelo parece viável, MAS precisa validação formal antes de produção.

**Investimento em Validações:** R$ 5.000 - R$ 8.000  
**Prazo Estimado:** 4-6 semanas

---

## 📋 VALIDAÇÕES OBRIGATÓRIAS

### ⚖️ 1. Jurídico

#### 1.1 Parecer sobre Instituição de Pagamento
- [ ] Contratado: Advogado especializado em fintech/direito bancário
- [ ] Enviado: Documento com perguntas (PERGUNTAS_CONTADOR_ADVOGADO.md)
- [ ] Recebido: Parecer formal em PDF
- [ ] Conclusão: [ ] APROVADO / [ ] REPROVADO / [ ] AJUSTES NECESSÁRIOS
- **Custo:** R$ 3.000 - R$ 5.000
- **Prazo:** 2-3 semanas
- **Criticidade:** 🔴 CRÍTICA (bloqueante)

**O que validar:**
- PagBy NÃO é caracterizado como instituição de pagamento?
- Subordinação da subconta NÃO gera responsabilidade solidária?
- Modelo é similar ao Shopify (precedente)?

---

#### 1.2 Revisão de Contratos (Termos de Uso)
- [ ] Minuta elaborada: Termos de Uso PagBy ↔ Salão
- [ ] Revisado por advogado
- [ ] Cláusulas de limitação de responsabilidade incluídas
- [ ] Definição clara: cliente contrata COM O SALÃO (não com PagBy)
- **Custo:** R$ 1.000 - R$ 2.000 (parte da consultoria acima)
- **Prazo:** 1 semana
- **Criticidade:** 🔴 CRÍTICA

**Cláusulas essenciais:**
- PagBy não é responsável por tributos do salão
- PagBy não é responsável por qualidade do serviço prestado
- Salão é único responsável por NF e impostos
- Limitação de responsabilidade em caso de inadimplência do salão

---

#### 1.3 DPA - LGPD (Data Processing Agreement)
- [ ] Modelo DPA elaborado ou obtido
- [ ] Revisado por advogado
- [ ] Definido: Salão = CONTROLADOR, PagBy = OPERADOR
- [ ] Aceite eletrônico validado como suficiente
- **Custo:** R$ 1.000 - R$ 2.000
- **Prazo:** 1 semana
- **Criticidade:** 🟡 IMPORTANTE (não bloqueante imediato, mas obrigatório por lei)

---

### 💰 2. Contábil/Fiscal

#### 2.1 Validação de Emissão de NF
- [ ] Contratado: Contador especializado em SaaS
- [ ] Enviado: Documento com perguntas (PERGUNTAS_CONTADOR_ADVOGADO.md)
- [ ] Recebido: Orientações formais
- [ ] Conclusão: [ ] APROVADO / [ ] REPROVADO / [ ] AJUSTES NECESSÁRIOS
- **Custo:** R$ 2.000 - R$ 3.000
- **Prazo:** 1-2 semanas
- **Criticidade:** 🔴 CRÍTICA (bloqueante)

**O que validar:**
- Salão emite NF de R$ 100 ao cliente? ✅
- PagBy emite NF de R$ 80 ao salão? ✅
- NÃO há bitributação? ✅
- Código de serviço está correto? (1.05 - Licenciamento de software)

---

#### 2.2 Simulação Tributária PagBy
- [ ] Simulado: Simples Nacional
- [ ] Simulado: Lucro Presumido
- [ ] Comparados: Cenários de R$ 300k, R$ 1mi, R$ 3mi/ano
- [ ] Decidido: Regime tributário inicial
- **Custo:** Incluído na consultoria contábil acima
- **Prazo:** 1 semana
- **Criticidade:** 🟡 IMPORTANTE (afeta rentabilidade, mas não impede go-live)

---

#### 2.3 Orientações de Contabilização
- [ ] Documentado: Como contabilizar subconta (para os salões)
- [ ] Documentado: Como contabilizar assinatura (para o PagBy)
- [ ] Validado: Despesa de assinatura é 100% dedutível para salões
- **Custo:** Incluído na consultoria contábil
- **Prazo:** 1 semana
- **Criticidade:** 🟢 DESEJÁVEL

---

### 🧪 3. Técnico

#### 3.1 Testes em Sandbox Asaas
- [ ] Criada: Subconta teste via API
- [ ] Testado: Processamento de pagamento na subconta
- [ ] Verificado: NF é emitida em nome da SUBCONTA (não da master)
- [ ] Verificado: Subconta pode sacar valores independentemente
- [ ] Testado: Webhooks funcionam corretamente
- **Custo:** R$ 0 (desenvolvimento interno)
- **Prazo:** 1 semana
- **Criticidade:** 🔴 CRÍTICA (bloqueante técnico)

**Checklist de Testes:**
```bash
[ ] Criar subconta teste via POST /api/v3/wallets
[ ] Obter API key da subconta
[ ] Criar cobrança usando API key da SUBCONTA (não da master)
[ ] Processar pagamento teste (R$ 10,00)
[ ] Verificar: Dinheiro cai na subconta, não na master
[ ] Emitir NF teste (verificar nome do emissor)
[ ] Sacar valor da subconta para conta bancária teste
```

---

#### 3.2 Validação Termos Asaas sobre Subcontas
- [ ] Lido: Termos de Serviço Asaas sobre contas de marketplace
- [ ] Verificado: O que acontece se master encerrar conta?
- [ ] Verificado: Salão pode migrar subconta para conta independente?
- [ ] Verificado: Limites de responsabilidade da master
- **Custo:** R$ 0
- **Prazo:** 2 dias
- **Criticidade:** 🟡 IMPORTANTE

**Link:** https://www.asaas.com/termos-de-uso  
**Seção:** Contas de Marketplace / Subcontas

---

## 📊 PAINEL DE PROGRESSO

| Validação | Status | Risco | Criticidade |
|-----------|--------|-------|-------------|
| Parecer jurídico | ⏳ PENDENTE | 🟡 Médio | 🔴 CRÍTICA |
| Contratos/Termos | ⏳ PENDENTE | 🟢 Baixo | 🔴 CRÍTICA |
| DPA (LGPD) | ⏳ PENDENTE | 🟢 Baixo | 🟡 IMPORTANTE |
| Validação fiscal | ⏳ PENDENTE | 🟡 Médio | 🔴 CRÍTICA |
| Simulação tributária | ⏳ PENDENTE | 🟢 Baixo | 🟡 IMPORTANTE |
| Testes sandbox | ⏳ PENDENTE | 🟡 Médio | 🔴 CRÍTICA |
| Termos Asaas | ⏳ PENDENTE | 🟢 Baixo | 🟡 IMPORTANTE |

**Legenda de Status:**
- ⏳ PENDENTE: Ainda não iniciado
- 🔄 EM ANDAMENTO: Contratado/iniciado, aguardando conclusão
- ✅ CONCLUÍDO: Validado e aprovado
- ⚠️ AJUSTES: Requer modificações no modelo
- ❌ REPROVADO: Validação negativa (buscar alternativa)

**Legenda de Criticidade:**
- 🔴 CRÍTICA: Bloqueante - não pode ir para produção sem essa validação
- 🟡 IMPORTANTE: Não bloqueia go-live, mas é obrigatória legalmente
- 🟢 DESEJÁVEL: Melhora segurança jurídica, mas não é mandatória

---

## 🚦 DECISÃO GO / NO-GO

### Critérios para GO (Implementar em Produção)

✅ **Todas as validações CRÍTICAS (🔴) devem estar CONCLUÍDAS ✅**

1. [ ] Parecer jurídico APROVA modelo (não caracteriza instituição de pagamento)
2. [ ] Contador APROVA emissão de NF (sem bitributação)
3. [ ] Testes sandbox COMPROVAM que NF sai em nome da subconta
4. [ ] Contratos/Termos REVISADOS e com cláusulas de limitação

**SE TODAS ✅:** Modelo está validado, pode implementar.

---

### Critérios para NO-GO (Buscar Alternativa)

❌ **Se QUALQUER validação crítica for REPROVADA:**

1. [ ] Parecer jurídico caracteriza PagBy como instituição de pagamento
2. [ ] Contador identifica bitributação ou emissão incorreta de NF
3. [ ] Testes comprovam que NF sai em nome do PagBy (não da subconta)
4. [ ] Termos Asaas não permitem subordinação ou migração de subcontas

**SE QUALQUER ❌:** Modelo inviável, buscar alternativa (ex: modelo PURE SaaS sem subcontas).

---

## 📅 CRONOGRAMA SUGERIDO

### Semana 1-2: Contratações e Envio de Documentação
- [ ] Dia 1: Identificar contador especializado em SaaS
- [ ] Dia 2: Identificar advogado especializado em fintech
- [ ] Dia 3: Enviar PERGUNTAS_CONTADOR_ADVOGADO.md aos profissionais
- [ ] Dia 5: Agendar reuniões com cada um (remoto ou presencial)

### Semana 2-3: Consultorias e Testes Técnicos
- [ ] Reunião com advogado (2-3 horas)
- [ ] Reunião com contador (2 horas)
- [ ] Iniciar testes técnicos em sandbox Asaas (paralelo)
- [ ] Ler termos de serviço Asaas sobre subcontas

### Semana 4: Recebimento de Pareceres
- [ ] Receber parecer jurídico formal
- [ ] Receber orientações contábeis formais
- [ ] Concluir testes técnicos
- [ ] Consolidar resultados

### Semana 5: Ajustes (se necessário)
- [ ] Revisar contratos conforme recomendações
- [ ] Ajustar modelo se necessário
- [ ] Elaborar DPA (LGPD)
- [ ] Atualizar documentação técnica

### Semana 6: Decisão GO / NO-GO
- [ ] Reunião interna: analisar todos os pareceres
- [ ] Decisão final: implementar ou buscar alternativa?
- [ ] Se GO: elaborar plano de implementação técnica
- [ ] Se NO-GO: avaliar modelo alternativo (PURE SaaS)

---

## 💰 ORÇAMENTO

| Item | Custo | Status | Observação |
|------|-------|--------|------------|
| Advogado (parecer) | R$ 3.000 - R$ 5.000 | ⏳ Pendente | Fintech/bancário |
| Contador (validação) | R$ 2.000 - R$ 3.000 | ⏳ Pendente | Especializado SaaS |
| DPA (LGPD) | Incluído acima | ⏳ Pendente | Parte consultoria |
| Testes técnicos | R$ 0 | ⏳ Pendente | Interno |
| **TOTAL** | **R$ 5.000 - R$ 8.000** | - | 4-6 semanas |

**Alternativa Econômica:**
- Contador + advogado generalista: R$ 3.000 - R$ 5.000
- ⚠️ Risco: Menos especialização, análise superficial

---

## 📞 PRÓXIMOS PASSOS IMEDIATOS

### HOJE (Prioridade 1)
1. [ ] Buscar 3 indicações de advogados especializados em fintech/direito bancário
2. [ ] Buscar 3 indicações de contadores especializados em SaaS
3. [ ] Solicitar orçamentos de cada um

### ESTA SEMANA (Prioridade 2)
4. [ ] Contratar os profissionais escolhidos
5. [ ] Enviar PERGUNTAS_CONTADOR_ADVOGADO.md antecipadamente
6. [ ] Agendar reuniões (preferencialmente presenciais)

### PRÓXIMA SEMANA (Prioridade 3)
7. [ ] Iniciar testes técnicos em sandbox Asaas
8. [ ] Ler e anotar termos de serviço Asaas (foco em subcontas)
9. [ ] Preparar apresentação do modelo para os profissionais (se necessário)

---

## 🎯 RESULTADO ESPERADO

**Ao final das 6 semanas, ter:**

✅ **Decisão GO ou NO-GO fundamentada em pareceres profissionais**

**Se GO:**
- Parecer jurídico aprovando modelo
- Orientações contábeis formais
- Contratos e termos revisados
- DPA (LGPD) elaborado
- Testes técnicos aprovados
- **→ Pode iniciar implementação em produção**

**Se NO-GO:**
- Identificação clara dos impedimentos
- Recomendações de ajustes ou alternativas
- **→ Avaliar modelo PURE SaaS (sem subcontas) ou outro modelo**

---

## ⚠️ AVISOS IMPORTANTES

### 🚫 NÃO IMPLEMENTAR EM PRODUÇÃO antes de:
- ❌ Receber parecer jurídico favorável
- ❌ Receber validação contábil
- ❌ Comprovar via testes que NF sai em nome da subconta

### 💡 Modelo atual (com split) tem PROBLEMA:
- NF está saindo em nome do PagBy (ERRADO)
- Salão presta serviço, mas NF não está em nome dele
- ⚠️ Risco fiscal: PagBy emitindo NF de serviço que não prestou

### ✅ Modelo proposto (subconta 100%) PARECE resolver:
- Cliente paga direto na subconta do salão
- Salão emite NF ao cliente (correto)
- PagBy não recebe valores dos clientes (não é intermediário)
- **MAS precisa validação formal para ter certeza!**

---

**Última atualização:** 05/03/2026  
**Responsável:** Helder (PagBy)  
**Status:** 🟡 Aguardando início das validações

---

## 📚 Documentos de Apoio

Criados nesta análise:
- [VALIDACAO_LEGAL_CONTABIL_SUBCONTAS.md](VALIDACAO_LEGAL_CONTABIL_SUBCONTAS.md) - Análise completa
- [PERGUNTAS_CONTADOR_ADVOGADO.md](PERGUNTAS_CONTADOR_ADVOGADO.md) - Roteiro de consulta
- Este checklist executivo

Documentos anteriores relevantes:
- [SPLIT_NOTA_FISCAL_REALIDADE.md](SPLIT_NOTA_FISCAL_REALIDADE.md) - Problema do modelo com split
- [ORIENTACOES_CONTADOR_SPLIT.md](ORIENTACOES_CONTADOR_SPLIT.md) - Perguntas originais (modelo split)
- [MODELO_MARKETPLACE_REALIDADE.md](MODELO_MARKETPLACE_REALIDADE.md) - Clarificação do modelo de negócio
- [SUBSCRIPTION_SYSTEM.md](SUBSCRIPTION_SYSTEM.md) - Sistema de assinaturas atual

---

**🎯 Foco agora: VALIDAR antes de IMPLEMENTAR. Investir R$ 5-8k em consultorias é MUITO menos que potenciais multas fiscais ou problemas legais futuros.**

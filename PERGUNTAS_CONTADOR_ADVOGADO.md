# 📋 Perguntas para Contador e Advogado - Validação Modelo PagBy

**Data**: 05/03/2026  
**Objetivo**: Roteiro de consulta para validar modelo de negócio SaaS com subcontas

---

## 👨‍⚖️ PERGUNTAS PARA O ADVOGADO

### Parte 1: Caracterização de Instituição de Pagamento

**Contexto:** PagBy é uma plataforma SaaS para gestão de salões de beleza. O modelo proposto é:

1. Cliente final paga DIRETAMENTE em subconta Asaas do salão (não passa pela conta do PagBy)
2. 100% do valor fica com o salão (sem split)
3. Salão paga assinatura mensal à PagBy pelo uso da plataforma (R$ 30 ou R$ 80)
4. PagBy é "master" da subconta no Asaas (pode criar, monitorar, mas não movimentar valores)

**Perguntas:**

#### 1.1 Intermediação Financeira
> **"Este modelo caracteriza PagBy como Instituição de Pagamento conforme Lei 12.865/2013?"**

Detalhamento:
- PagBy NUNCA recebe valores dos clientes finais
- PagBy não faz repasses ou transferências
- PagBy apenas fornece software de gestão
- Asaas é quem processa os pagamentos (Asaas SIM é instituição autorizada)

**Esperamos:** NÃO caracteriza, pois não há recebimento/repasse. Confirmar?

---

#### 1.2 Subordinação da Subconta
> **"A subordinação técnica da subconta à conta master do PagBy gera alguma responsabilidade legal ou fiscal?"**

Detalhamento:
- Subconta está em nome do salão (CNPJ dele)
- Mas subconta é tecnicamente subordinada à master (PagBy)
- PagBy pode bloquear subconta se salão não pagar assinatura?
- PagBy pode ser responsabilizado por inadimplência fiscal do salão?

**Preocupação:** Se salão não pagar ISS, Receita pode cobrar PagBy?

---

#### 1.3 Relação Contratual Cliente-Salão
> **"O contrato de prestação de serviço deve ser firmado entre Cliente e Salão? PagBy pode constar como 'fornecedor de tecnologia' apenas?"**

Detalhamento:
- Cliente aceita termos de uso do SALÃO (não do PagBy)
- Cliente contrata PLANO criado pelo SALÃO
- PagBy fornece a plataforma tecnológica para o salão
- Em caso de problema (serviço não prestado), cliente processa SALÃO, não PagBy?

**Objetivo:** Delimitar claramente responsabilidades.

---

#### 1.4 Encerramento de Relacionamento
> **"Se salão cancelar assinatura PagBy, o que pode/deve acontecer com a subconta Asaas?"**

Cenários possíveis:
a) Subconta permanece ativa (salão migra para conta Asaas direta)
b) Subconta é encerrada imediatamente (ruptura do serviço)
c) Subconta fica inativa por período de transição (ex: 30 dias)

**Perguntas específicas:**
- Qual opção é legalmente mais segura para PagBy?
- PagBy pode bloquear subconta por inadimplência de assinatura?
- Precisa dar prazo para salão migrar clientes?

---

### Parte 2: LGPD (Lei Geral de Proteção de Dados)

#### 2.1 Papéis de Controlador e Operador
> **"Na estrutura proposta, quem é Controlador e quem é Operador dos dados pessoais dos clientes finais?"**

Nossa interpretação:
- **Controlador:** Salão (decide usar dados para fidelização, marketing)
- **Operador:** PagBy (processa dados tecnicamente, mas não decide finalidade)

**Confirmar?** Quais obrigações específicas para cada papel?

---

#### 2.2 DPA (Data Processing Agreement)
> **"É obrigatório ter DPA entre PagBy e cada Salão?"**

- Se sim, há modelo padrão recomendado?
- Quais cláusulas são essenciais?
- Precisa ser assinado fisicamente ou aceite eletrônico é válido?

---

### Parte 3: Contratos e Termos de Uso

#### 3.1 Limitação de Responsabilidade
> **"Quais cláusulas são essenciais no contrato PagBy ↔ Salão para limitar responsabilidade?"**

Sugestões para validar:
- PagBy não se responsabiliza por tributos do salão
- PagBy não se responsabiliza por qualidade do serviço prestado pelo salão
- PagBy não garante volume de clientes ou faturamento
- Salão é responsável exclusivo por emissão de NF e pagamento de impostos

**São válidas e suficientes?**

---

#### 3.2 Fiscalização e Compliance
> **"PagBy pode/deve exigir comprovação de regularidade fiscal do Salão?"**

Exemplos:
- Exigir certidão negativa de débitos municipais
- Bloquear subconta se salão não emitir NFs por 30 dias
- Notificar salão sobre obrigações fiscais

**Questão:** Isso protege PagBy de responsabilidade solidária?

---

### Parte 4: Modelos de Referência

#### 4.1 Comparação com Shopify
> **"O modelo PagBy é juridicamente similar ao Shopify?"**

- Shopify: Loja recebe pagamento em subconta, paga assinatura ao Shopify
- PagBy: Salão recebe pagamento em subconta, paga assinatura ao PagBy

**Se sim:** Há jurisprudência favorável ao modelo Shopify no Brasil?

---

#### 4.2 Análise de Risco
> **"Em uma escala de 1 a 10, qual o risco legal deste modelo para o PagBy?"**

- 1-3: Baixo risco (similar a SaaS tradicional)
- 4-6: Médio risco (requer mitigações)
- 7-10: Alto risco (considerar modelo alternativo)

**Se risco médio/alto:** Quais seguros ou garantias são recomendados?

---

## 👨‍💼 PERGUNTAS PARA O CONTADOR

### Parte 1: Emissão de Notas Fiscais

#### 1.1 Nota Fiscal do Cliente Final
> **"Quando cliente paga R$ 100 diretamente na subconta do Salão, quem deve emitir NF?"**

Interpretação:
- ✅ **SALÃO** emite NF de R$ 100 ao cliente
- Descrição: "Plano de assinatura mensal - serviços de beleza"
- Tomador: Cliente (CPF)
- Prestador: Salão (CNPJ)

**Está correto?** Alguma particularidade?

---

#### 1.2 Nota Fiscal da Assinatura SaaS
> **"PagBy deve emitir NF de R$ 80 (assinatura) ao Salão?"**

Interpretação:
- ✅ **PAGBY** emite NF de R$ 80 ao salão
- Descrição: "Licença de software SaaS - gestão e automação de salão"
- Tomador: Salão (CNPJ)
- Prestador: PagBy (CNPJ)
- Código de serviço: 1.05 (LC 116/2003) - Licenciamento ou cessão de direito de uso de programas de computação

**Está correto?** Código de serviço está adequado?

---

#### 1.3 Bitributação
> **"Há risco de bitributação (duplo pagamento de ISS) neste modelo?"**

Análise:
- Salão paga ISS sobre R$ 100 (serviço de beleza prestado)
- PagBy paga ISS sobre R$ 80 (serviço de software prestado)
- São SERVIÇOS DIFERENTES com BASES DIFERENTES

**Confirmar:** Não há bitributação, correto?

---

### Parte 2: Tributação

#### 2.1 ISS do Salão
> **"Salão deve recolher ISS sobre os R$ 100 recebidos na subconta?"**

- Alíquota: 5% (serviços de beleza) = R$ 5,00
- Município: Local do estabelecimento do salão
- Se salão é Simples Nacional: ISS incluído no DAS?

**Confirmar:** Procedimento está correto?

---

#### 2.2 ISS do PagBy
> **"PagBy deve recolher ISS sobre os R$ 80 de assinatura?"**

- Código de serviço: 1.05 (licenciamento de software)
- Alíquota: 2% a 5% (depende do município)
- Município: Local do estabelecimento do PagBy

**Pergunta:** Se PagBy é no Rio de Janeiro, qual alíquota ISS para serviço 1.05?

---

#### 2.3 Regime Tributário do PagBy
> **"Qual regime tributário é mais vantajoso para PagBy: Simples Nacional ou Lucro Presumido?"**

**Simples Nacional:**
- Anexo V (serviços)
- Alíquota efetiva: 15,5% a 19,25%
- Limite: R$ 4,8 milhões/ano

**Lucro Presumido:**
- Base: 32% da receita
- IR + CSLL: ~11,33%
- ISS: 2-5% (separado)
- PIS/COFINS: 3,65%
- Total: ~17-20%
- Sem limite de faturamento

**Simular:** Qual mais vantajoso para faturamento anual estimado de:
- Cenário 1: R$ 500 mil/ano
- Cenário 2: R$ 2 milhões/ano
- Cenário 3: R$ 5 milhões/ano (acima do limite Simples)

---

### Parte 3: Contabilização

#### 3.1 Contabilização da Subconta (lado do Salão)
> **"Como salão deve contabilizar valores recebidos na subconta Asaas?"**

Proposta:
```
ATIVO: Contas a Receber - Subconta Asaas
├─ Crédito: R$ 100 (pagamento recebido)
└─ Débito: R$ 100 (saque para conta bancária)

RECEITA: Prestação de Serviços
└─ Crédito: R$ 100 (reconhecimento de receita)
```

**Está correto?** Como justificar subconta em nome do salão, mas subordinada ao PagBy?

---

#### 3.2 Contabilização da Assinatura (lado do Salão)
> **"Assinatura PagBy de R$ 80 é despesa dedutível para o Salão?"**

Proposta:
```
DESPESA: Tecnologia / Software
├─ Débito: R$ 80 (despesa com software)
└─ Comprovante: NF emitida por PagBy
```

**Confirmar:** 100% dedutível? Categoria correta?

---

#### 3.3 Receita do PagBy
> **"Como PagBy deve contabilizar receita de assinaturas?"**

Proposta:
```
RECEITA: Prestação de Serviços - Licença de Software
└─ Crédito: R$ 80/mês por salão assinante

Exemplo: 100 salões × R$ 80 = R$ 8.000/mês
```

**Perguntas:**
- Reconhecer receita mensalmente (regime de competência)?
- Como tratar quando salão paga anualmente (ex: R$ 960 de uma vez)?
- Receita diferida ou reconhecimento imediato?

---

### Parte 4: Obrigações Acessórias

#### 4.1 Configuração Fiscal no Asaas
> **"Cada subconta (salão) precisa configurar dados fiscais no Asaas para emitir NF?"**

Dados necessários:
- CNPJ do salão
- Inscrição Municipal
- Inscrição Estadual (se aplicável)
- Senha de acesso ao sistema da prefeitura

**Pergunta:** PagBy pode automatizar essa configuração ou salão deve fazer manualmente?

---

#### 4.2 Limite MEI
> **"Se salão é MEI, pode usar subconta Asaas e emitir NF normalmente?"**

Limites MEI:
- Faturamento: R$ 81 mil/ano (R$ 6.750/mês)
- Não pode ter sócios
- Máximo 1 funcionário

**Cenário problema:** Salão MEI fatura R$ 10 mil/mês com assinaturas.
- Ultrapassa limite MEI → precisa migrar para ME
- PagBy deve alertar salão sobre isso?

---

#### 4.3 Retenção de Tributos
> **"Quando PagBy emite NF de R$ 80 ao salão, há retenção na fonte?"**

Possíveis retenções:
- IRRF (Imposto de Renda Retido na Fonte)
- CSRF (Contribuição Social Retida na Fonte)
- PIS/COFINS/CSLL retidos

**Depende do regime tributário do salão?**
- Salão no Simples: Sem retenção?
- Salão no Lucro Presumido/Real: Com retenção?

**Como proceder em cada caso?**

---

### Parte 5: Planejamento Tributário

#### 5.1 Expectativa de Crescimento
> **"PagBy projeta crescimento de 50 salões hoje para 500 salões em 2 anos. Como planejar regime tributário?"**

Projeção:
- Ano 1: 100 salões × R$ 80 × 12 = **R$ 96 mil**
- Ano 2: 300 salões × R$ 80 × 12 = **R$ 288 mil**
- Ano 3: 500 salões × R$ 80 × 12 = **R$ 480 mil**

**Pergunta:** Começar no Simples Nacional e migrar depois, ou já abrir no Lucro Presumido?

---

#### 5.2 Preço Baseado em Volume
> **"Se PagBy cobrar R$ 30 (básico) ou R$ 80 (premium), como isso afeta tributação?"**

Simulação:
- 300 salões × R$ 30 = R$ 9 mil/mês
- 200 salões × R$ 80 = R$ 16 mil/mês
- Total: R$ 25 mil/mês = **R$ 300 mil/ano**

**Melhor tributação para mix de planos?**

---

#### 5.3 Despesas Operacionais
> **"Quais despesas SaaS típicas são 100% dedutíveis?"**

Exemplos:
- Servidores AWS/Azure
- Taxas do Asaas (gateway de pagamento)
- Salários desenvolvedores
- Marketing digital (Google Ads, Meta Ads)
- Contador e advogado

**Confirmar:** Todas dedutíveis? Alguma limitação?

---

### Parte 6: Comparação com Concorrentes

#### 6.1 Modelo Shopify
> **"Contabilmente, o modelo PagBy é similar ao Shopify?"**

- Shopify: Loja recebe em subconta, paga assinatura Shopify
- PagBy: Salão recebe em subconta, paga assinatura PagBy

**Mesmo tratamento fiscal?** Shopify tem alguma particularidade?

---

#### 6.2 Modelo iFood
> **"Por que o modelo PagBy é DIFERENTE do iFood tributariamente?"**

- iFood: RECEBE R$ 100 do cliente → REPASSA R$ 70 ao restaurante
- PagBy: Cliente paga DIRETO na subconta do salão (PagBy não recebe/repassa)

**Confirmar:** Modelos completamente distintos fiscalmente?

---

## 📝 INFORMAÇÕES A FORNECER AOS PROFISSIONAIS

### Dados do PagBy
- Razão Social: [A DEFINIR]
- CNPJ: [A DEFINIR]
- Endereço: [A DEFINIR]
- Atividade: Licenciamento de software (CNAE 6209-1/00)
- Faturamento atual: [A DEFINIR]
- Projeção 12 meses: R$ 300 mil
- Regime tributário atual: [A DEFINIR]

### Modelo de Negócio
- **Plano Básico:** R$ 30/mês (gestão simples, sem assinaturas)
- **Plano Premium:** R$ 80/mês (gestão completa, com assinaturas)
- **Gateway:** Asaas (instituição autorizada pelo Banco Central)
- **Subcontas:** Criadas via API Asaas, em nome do salão (CNPJ dele)
- **Subordinação:** Subconta é tecnicamente subordinada à master (PagBy), mas valores pertencem ao salão

### Fluxo Financeiro
```
Cliente Final
↓ paga R$ 100
Subconta Asaas do Salão (CNPJ do salão)
├─ Salão saca 100% do valor
└─ Salão emite NF de R$ 100 ao cliente

(Separadamente)

Salão
↓ paga R$ 80/mês
Conta Master PagBy
├─ PagBy emite NF de R$ 80 ao salão
└─ PagBy paga ISS sobre R$ 80
```

---

## ⏰ CRONOGRAMA DE CONSULTAS

| Profissional | Duração | Documentos Necessários | Custo Estimado |
|--------------|---------|------------------------|----------------|
| **Advogado** | 2-3 horas | Este documento + Termos de Uso Asaas | R$ 3.000 - R$ 5.000 |
| **Contador** | 2 horas | Este documento + Projeções financeiras | R$ 2.000 - R$ 3.000 |
| **Total** | - | - | **R$ 5.000 - R$ 8.000** |

---

## ✅ RESULTADO ESPERADO

Ao final das consultas, ter:

### Do Advogado:
- [ ] **Parecer formal** sobre caracterização ou não como instituição de pagamento
- [ ] **Minuta de DPA (LGPD)** entre PagBy e Salões
- [ ] **Cláusulas recomendadas** para Termos de Uso
- [ ] **Análise de riscos** e recomendações de mitigação

### Do Contador:
- [ ] **Confirmação fiscal** sobre emissão de NF (quem emite o quê)
- [ ] **Simulação tributária** comparando Simples Nacional vs Lucro Presumido
- [ ] **Orientações de contabilização** da subconta
- [ ] **Checklist de obrigações acessórias**

### Decisão Final:
- [ ] **GO** (implementar modelo) ou **NO-GO** (buscar alternativa)
- [ ] **Cronograma de implementação** (se GO)
- [ ] **Ajustes necessários** em contratos e processos

---

**Elaborado:** 05/03/2026  
**Uso:** Consulta com profissionais especializados  
**Próximo passo:** Agendar reuniões e enviar este documento antecipadamente

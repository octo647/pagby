# Comparação: MercadoPago vs Asaas - Sistema de Assinaturas PagBy

---

## ⚠️ AVISO IMPORTANTE - ATUALIZAÇÃO 05/03/2026

**As informações sobre emissão de Nota Fiscal neste documento estão INCORRETAS.**

Após confirmação com o suporte do Asaas, descobrimos que:
- **O split do Asaas divide apenas o dinheiro, não a responsabilidade fiscal**
- **Apenas UMA nota fiscal é emitida** (pelo prestador principal, sobre o valor total)
- **A questão tributária dos valores repassados é contábil/jurídica entre as partes**

📄 **Leia a documentação correta em**: [`SPLIT_NOTA_FISCAL_REALIDADE.md`](SPLIT_NOTA_FISCAL_REALIDADE.md)

**⚠️ Consulte um contador antes de implementar split em produção!**

---

## Resumo Executivo

| Critério | MercadoPago | Asaas |
|----------|-------------|-------|
| **Split de Pagamentos** | ❌ Não suporta em assinaturas | ✅ Suporta nativamente |
| **Complexidade Fiscal** | ⚠️ Alta (bitributação) | ⚠️ Média (requer contador) |
| **Status Legal** | ⚠️ Risco de caracterização irregular | ✅ Conforme legislação |
| **Integração** | ✅ Implementada | 🆕 Nova (compatível) |
| **Métodos de Pagamento** | Cartão, Boleto, PIX | Cartão, Boleto, PIX, Débito |
| **Taxas** | ~4.99% + R$0.40 | ~4.99% (varia por método) |
| **Webhook** | Eventos complexos | Eventos simples |
| **Dashboard** | Completa | Completa |

---

## Análise Detalhada

### 1. Split de Pagamentos

#### MercadoPago
```
❌ NÃO SUPORTA em assinaturas recorrentes

Fluxo atual (PROBLEMÁTICO):
Cliente → MercadoPago → Conta PagBy
                           ↓
                   PagBy transfere manualmente
                           ↓
                     Conta do Tenant

Problemas:
• PagBy recebe 100% e precisa repassar
• Caracteriza intermediação de pagamentos
• Bitributação: impostos pagos 2x
• Responsabilidade legal sobre valores de terceiros
```

#### Asaas
```
✅ SUPORTA nativamente

Fluxo novo (CORRETO):
Cliente → Asaas → Split Automático
                       ↓
              ┌────────┴────────┐
              ↓                 ↓
     Conta PagBy (10%)   Subconta Tenant (90%)

Vantagens:
• Split automático na origem
• Cada parte recebe direto
• Sem bitributação
• Sem caracterização irregular
• PagBy não manipula $ do tenant
```

### 2. Aspectos Fiscais

| Aspecto | MercadoPago (Manual) | Asaas (Split) |
|---------|---------------------|---------------|
| **Nota Fiscal** | PagBy emite 100%, depois repassa | ⚠️ Prestador emite 100% (split só divide $) |
| **ISS** | Pago 2x (PagBy + Tenant) | ⚠️ Precisa definir com contador |
| **IRPF/IRPJ** | Base de cálculo inflada | ⚠️ Questão contábil entre partes |
| **Contabilidade** | Complexa (entrada + saída) | ⚠️ Requer definição jurídica |
| **Risco Fiscal** | ⚠️ Alto | ⚠️ Médio (requer consultoria) |

### 3. Aspectos Legais

#### Problema com MercadoPago (Split Manual)

Segundo a Lei nº 12.865/2013 e Circular BCB nº 3.682/2013:

> **Intermediação de Pagamentos**: Caracteriza-se quando uma parte recebe recursos de terceiros e repassa posteriormente.

**Consequências:**
- Necessidade de autorização do Banco Central
- Sujeito a regulamentação específica
- Possíveis multas e sanções
- Responsabilidade solidária

#### Solução com Asaas (Split Nativo)

> **Facilitador de Pagamentos**: PagBy atua apenas como tecnologia, não manipula valores de terceiros.

**Vantagens:**
- Não requer autorização especial
- Cada tenant recebe direto na subconta
- PagBy apenas provê a tecnologia/plataforma
- Sem responsabilidade sobre valores

---

## Migração Recomendada

### Fase 1: Preparação (Semana 1)
- [x] Criar conta no Asaas
- [x] Implementar AsaasService
- [x] Criar AsaasSubscriptionController
- [x] Criar migrations
- [x] Criar comando de subcontas
- [x] Documentar

### Fase 2: Testes (Semana 2)
- [ ] Testar em sandbox
- [ ] Criar subcontas para tenants piloto
- [ ] Testar fluxo completo
- [ ] Validar webhooks
- [ ] Confirmar split funcionando

### Fase 3: Deploy Gradual (Semana 3-4)
- [ ] Criar subcontas para todos tenants
- [ ] Ativar rotas Asaas em produção
- [ ] Manter MercadoPago para assinaturas existentes
- [ ] Novas assinaturas → Asaas
- [ ] Migrar assinaturas antigas gradualmente

### Fase 4: Consolidação (Mês 2)
- [ ] Migrar 100% para Asaas
- [ ] Cancelar integrações MercadoPago
- [ ] Remover código legado
- [ ] Documentar para equipe

---

## Exemplo Numérico

⚠️ **AVISO**: Este exemplo está DESATUALIZADO. Ver [`SPLIT_NOTA_FISCAL_REALIDADE.md`](SPLIT_NOTA_FISCAL_REALIDADE.md) para informações corretas.

### Cenário: Assinatura de R$ 100,00/mês

#### Fluxo MercadoPago (Problemático)

```
Cliente paga: R$ 100,00
↓
MercadoPago taxa (5%): -R$ 5,00
↓
PagBy recebe: R$ 95,00

PagBy precisa:
• Emitir NF de R$ 95,00
• Pagar ISS sobre R$ 95,00 (~5%): -R$ 4,75
• Repassar R$ 85,50 ao tenant
• Tenant emite NF de R$ 85,50
• Tenant paga ISS sobre R$ 85,50 (~5%): -R$ 4,28

TOTAL IMPOSTOS: R$ 9,03 (9.5%)
TRABALHO CONTÁBIL: Alto (2 NFs, 2 recolhimentos)
RISCO LEGAL: Alto
```

#### Fluxo Asaas (Correto)

```
Cliente paga: R$ 100,00
↓
Asaas taxa (5%): -R$ 5,00
↓
Split automático:
├─ PagBy recebe: R$ 9,50 (10% de R$ 95)
│  • Emite NF de R$ 9,50
│  • Paga ISS: R$ 0,48 (~5%)
│
└─ Tenant recebe: R$ 85,50 (90% de R$ 95)
   • Emite NF de R$ 85,50
   • Paga ISS: R$ 4,28 (~5%)

TOTAL IMPOSTOS: R$ 4,76 (5%)
TRABALHO CONTÁBIL: Normal (cada um cuida do seu)
RISCO LEGAL: Zero
```

**ECONOMIA: R$ 4,27 por assinatura/mês**

Com 100 assinaturas ativas: **R$ 427,00/mês de economia fiscal**

---

## Recomendação Final

### ✅ MIGRAR PARA ASAAS

**Motivos:**

1. **Legal**: Evita caracterização irregular de intermediação
2. **Fiscal**: Elimina bitributação
3. **Operacional**: Split automático, sem trabalho manual
4. **Escalável**: Funciona com 10 ou 10.000 tenants
5. **Transparente**: Cada tenant vê seu $ direto na conta

### 🔄 Cronograma Sugerido

- **Hoje**: Setup inicial (credenciais, migration)
- **Semana 1**: Criar subcontas, testar sandbox
- **Semana 2**: Deploy em produção, novas assinaturas via Asaas
- **Mês 1**: Migrar assinaturas antigas conforme renovam
- **Mês 2**: 100% Asaas, desativar MercadoPago

### 📊 Métricas de Sucesso

- [ ] 0 erros de split
- [ ] 100% subcontas criadas
- [ ] Tenants recebendo dentro do prazo
- [ ] Redução de custos fiscais confirmada
- [ ] Conformidade legal verificada

---

## Arquivos Criados

```
app/
├── Services/
│   └── AsaasService.php ← Estendido com métodos de assinatura
├── Http/Controllers/
│   └── AsaasSubscriptionController.php ← Novo controller
└── Console/Commands/
    └── CreateAsaasAccountsForTenants.php ← Comando CLI

database/
└── migrations/
    └── 2026_01_01_000001_add_asaas_fields_to_tenants_and_payments.php

routes/
└── asaas.php ← Rotas do sistema Asaas

MIGRACAO_ASAAS.md ← Documentação completa
ASAAS_QUICKSTART.md ← Guia rápido
```

---

## Contato

Dúvidas sobre a migração:
- **Documentação**: Ver MIGRACAO_ASAAS.md
- **Quick Start**: Ver ASAAS_QUICKSTART.md
- **Suporte Asaas**: suporte@asaas.com | (11) 4950-2209

---

**Data**: Janeiro 2026  
**Status**: ✅ Implementação Completa  
**Próximo Passo**: Executar migration e criar subcontas

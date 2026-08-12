# Dashboard de Fidelização - Guia do Proprietário

## 📍 Acesso

**URL**: `/proprietario/fidelidade`

**Permissão necessária**: Role `Proprietário`

---

## 🎨 Interface

A dashboard possui **4 abas principais**:

### 1️⃣ **Visão Geral**
Dashboard com estatísticas principais:
- 📊 **Cards com métricas:**
  - Recompensas ativas
  - Total de créditos ativos (em R$)
  - Vendas de produtos no período
  - Clientes com recompensas

- 📈 **Indicadores de engajamento:**
  - Recompensas usadas
  - Taxa de conversão

- ℹ️ **Informações do sistema:**
  - Como funciona o programa de fidelização
  - Dicas para maximizar resultados

### 2️⃣ **Produtos & Serviços**
Gerencie vínculos entre produtos e serviços:

- **Listar vínculos existentes:**
  - Produtos agrupados por serviço
  - Prioridade de exibição (1, 2, 3...)
  - Desconto especial associado
  - Status (ativo/inativo)
  - Total de vendas de cada produto

- **Criar novo vínculo:**
  - Botão "Novo Vínculo" abre modal
  - Selecione serviço e produto
  - Defina prioridade (ordem de sugestão)
  - Configure desconto opcional
  - Adicione observações

- **Gerenciar vínculos:**
  - Toggle para ativar/desativar
  - Remover vínculos desnecessários

**💡 Dica:** Produtos vinculados serão sugeridos automaticamente no agendamento!

### 3️⃣ **Recompensas Ativas**
Visualize todas as recompensas ativas dos clientes:

- **Cards de clientes:**
  - Avatar com inicial do nome
  - Nome e email
  - Tipo de recompensa (crédito/cupom/serviço grátis)
  - Valor ou código do cupom
  - Origem (compra_produto, promoção, etc.)
  - Data de validade
  - Alerta quando faltam <= 7 dias

- **Informações adicionais:**
  - Descrição formatada da recompensa
  - Data de criação
  - ID da venda que gerou a recompensa

### 4️⃣ **Produtos Mais Vendidos**
Ranking dos produtos que mais geram recompensas:

- **Tabela completa com:**
  - Posição (🥇🥈🥉 para top 3)
  - Nome do produto
  - Categoria
  - Preço unitário
  - Estoque atual (com alerta se baixo)
  - Total de vendas (com barra de progresso)
  - Faturamento estimado

- **Resumo inferior:**
  - Total de produtos vendidos
  - Faturamento total
  - Ticket médio

---

## 🔧 Funcionalidades

### Filtros Globais
- **Filial:** Selecione qual filial visualizar
- **Período:** 7, 30 ou 90 dias

### Ações Disponíveis

#### Vincular Produto a Serviço
1. Clique em "Novo Vínculo"
2. Selecione o serviço
3. Selecione o produto
4. Defina prioridade (1 = primeira sugestão)
5. Configure desconto opcional (ex: 10%)
6. Adicione observações se necessário
7. Clique em "Vincular"

#### Gerenciar Vínculos Existentes
- **Ativar/Desativar:** Clique no badge de status
- **Remover:** Clique no ícone de lixeira (confirmação obrigatória)

---

## 💰 Como Funcionam as Recompensas

### Geração Automática
Quando um cliente compra um produto:
1. Sistema cria automaticamente um crédito de fidelidade
2. Valor do crédito varia de acordo com a compra:
   - **≥ R$ 100:** 25% de crédito
   - **R$ 50-99:** 20% de crédito
   - **< R$ 50:** 15% de crédito
3. Crédito fica ativo por 90 dias
4. Cliente pode usar em seu próximo serviço

### Tipos de Recompensas
- **💰 Crédito:** Valor em R$ para usar em serviços
- **🎟️ Cupom:** Código com percentual de desconto
- **🎁 Serviço Grátis:** Serviço específico gratuito

### Origens
- **📦 Compra de produto:** Automático ao vender produtos
- **🎉 Promoção:** Criado manualmente para campanhas
- **👥 Indicação:** Quando cliente indica amigos
- **🎂 Aniversário:** Presente de aniversário

---

## 📊 Métricas Importantes

### Taxa de Conversão
```
(Recompensas Usadas) / (Recompensas Usadas + Ativas) * 100
```
**Meta ideal:** > 60%

### Ticket Médio
```
Faturamento Total / Total de Vendas
```

### Clientes Ativos
Clientes com pelo menos 1 recompensa ativa

---

## 🎯 Dicas para Aumentar Vendas

### 1. Vincule Produtos Estratégicos
- Produtos complementares ao serviço
- Shampoos com tratamentos capilares
- Cremes com manicure/pedicure

### 2. Priorize Produtos com Maior Margem
- Coloque prioridade 1 em produtos mais lucrativos
- Use descontos para incentivar teste de novos produtos

### 3. Mantenha Estoque Controlado
- Monitore produtos com estoque baixo
- Reponha produtos mais vendidos

### 4. Comunique Recompensas
- Avise clientes sobre créditos disponíveis
- Use WhatsApp para notificar recompensas expirando

---

## 🔔 Notificações Automáticas

### Emails Programados (em desenvolvimento)
- ✉️ Novo crédito disponível
- ⚠️ Recompensa expirando em 7 dias
- 🎉 Cupom especial de aniversário

### WhatsApp (futuro)
- Mensagens personalizadas sobre recompensas
- Lembretes de validade
- Ofertas especiais

---

## 📱 Responsividade

A interface é **totalmente responsiva**:
- Desktop: Layout em grid completo
- Tablet: Colunas adaptadas
- Mobile: Cards empilhados

---

## 🛠️ Solução de Problemas

### Recompensa não foi criada após venda
1. Verifique se a comanda tem `client_id` preenchido
2. Confirme que o Observer está registrado
3. Cheque logs em `storage/logs/laravel.log`

### Produtos não aparecem para vincular
1. Certifique-se que o produto tem estoque > 0
2. Verifique se está na filial correta

### Estatísticas zeradas
1. Confirme filtro de período
2. Verifique filtro de filial
3. Pode não haver vendas no período

---

## 🔐 Segurança

- ✅ Apenas usuários com role `Proprietário` podem acessar
- ✅ Dados isolados por tenant (multi-tenancy)
- ✅ Filtros por filial aplicados automaticamente
- ✅ Confirmação obrigatória para ações destrutivas

---

## 🚀 Próximos Passos

1. Acesse `/proprietario/fidelidade`
2. Explore a aba "Visão Geral"
3. Configure vínculos na aba "Produtos & Serviços"
4. Monitore recompensas ativas
5. Acompanhe ranking de produtos

**🎁 Parab éns! Seu sistema de fidelização está pronto para aumentar suas vendas!**

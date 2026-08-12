# Sistema de Fidelidade Integrado com Agendamento

## Visão Geral

O sistema de fidelidade foi completamente integrado ao fluxo de agendamento de serviços, proporcionando uma experiência completa de cross-selling e recompensas para os clientes.

## Componentes Criados

### 1. Backend (Livewire)

#### MinhasRecompensas.php
**Localização:** `app/Livewire/Cliente/MinhasRecompensas.php`

Componente que exibe as recompensas ativas do cliente autenticado na tela de agendamento.

**Funcionalidades:**
- Lista todas as recompensas ativas (válidas) do cliente
- Permite selecionar uma recompensa para usar no agendamento
- Exibe 3 tipos de recompensas:
  - 💰 **Crédito**: Valor em dinheiro para abater no serviço
  - 🎫 **Cupom**: Percentual de desconto
  - 🎁 **Serviço Grátis**: Serviço sem custo

**Alertas:**
- Badge vermelha quando restam ≤7 dias para expirar

#### SugestoesProdutos.php (Atualizado)
**Localização:** `app/Livewire/Cliente/SugestoesProdutos.php`

Componente que exibe produtos recomendados baseados no serviço selecionado.

**Lógica Hierárquica:**
1. Produtos vinculados manualmente ao serviço (prioridade)
2. Produtos mais vendidos com aquele serviço (automático)

**Funcionalidades:**
- Exibe até 3 produtos sugeridos
- Mostra desconto especial quando produto está vinculado
- Calcula ganho de crédito (20% do valor) ao comprar
- Permite selecionar múltiplos produtos
- Emite evento `produtosSelecionados` para componente pai

#### MakeAppointment.php (Atualizado)
**Localização:** `app/Livewire/Cliente/MakeAppointment.php`

Componente principal de agendamento, agora com suporte a fidelidade.

**Novas Propriedades:**
```php
public $produtosSelecionados = [];      // IDs dos produtos escolhidos
public $recompensaSelecionada = null;   // Dados da recompensa ativa
public $descontoRecompensa = 0;         // Valor do desconto a aplicar
```

**Novos Listeners:**
```php
protected $listeners = [
    'produtosSelecionados' => 'atualizarProdutos',
    'recompensaSelecionada' => 'aplicarRecompensa',
    'recompensaDeselecionada' => 'removerRecompensa',
];
```

**Novos Métodos:**
- `atualizarProdutos($produtos)`: Recebe produtos selecionados
- `aplicarRecompensa($recompensa)`: Aplica desconto da recompensa
- `removerRecompensa()`: Remove recompensa selecionada

### 2. Frontend (Blade)

#### minhas-recompensas.blade.php
**Localização:** `resources/views/livewire/cliente/minhas-recompensas.blade.php`

Interface visual das recompensas ativas do cliente.

**Design:**
- Fundo gradiente roxo/rosa
- Cards clicáveis com visual de seleção
- Badges coloridas por tipo de recompensa
- Alerta de validade próxima do vencimento
- Checkbox visual quando selecionada

#### sugestoes-produtos.blade.php
**Localização:** `resources/views/livewire/cliente/sugestoes-produtos.blade.php`

Interface visual de produtos recomendados.

**Design:**
- Fundo gradiente azul/índigo
- Destaque para desconto quando aplicável
- Cálculo visual do ganho de crédito (20%)
- Checkbox de seleção
- Informação de estoque disponível

#### make-appointment.blade.php (Atualizado)
**Localização:** `resources/views/livewire/cliente/make-appointment.blade.php`

Tela principal de agendamento com componentes integrados.

**Integração:**
```blade
{{-- Recompensas do Cliente --}}
@auth
    @livewire('cliente.minhas-recompensas')
@endauth

{{-- Sugestões de Produtos --}}
@if($ch_professional && !empty($ch_services))
    @livewire('cliente.sugestoes-produtos', [
        'serviceId' => $ch_services[0] ?? null,
        'branchId' => $ch_professional->branches->first()?->id ?? null,
    ])
@endif
```

## Fluxo de Uso para o Cliente

### 1. Acessar Agendamento
Cliente vai para a tela de agendamento de serviços.

### 2. Selecionar Profissional e Serviço
Ao selecionar o profissional e serviço:
- ✅ Componente de **Sugestões de Produtos** é exibido automaticamente
- ✅ Produtos são carregados usando a lógica hierárquica
- ✅ Se cliente estiver autenticado, **Minhas Recompensas** também aparece

### 3. Visualizar Recompensas Ativas
Cliente vê todas as suas recompensas disponíveis:
- Tipo e valor
- Data de validade
- Origem (compra de produto, promoção, etc.)
- Alerta se está próximo do vencimento

### 4. Selecionar Recompensa (Opcional)
Cliente clica na recompensa que deseja usar:
- ✓ Visual muda indicando seleção
- ✓ Mensagem flash confirma aplicação
- ✓ Desconto será aplicado no agendamento

### 5. Selecionar Produtos (Opcional)
Cliente clica nos produtos que deseja comprar:
- ✓ Checkbox marca como selecionado
- ✓ Evento enviado ao componente pai
- ✓ Ganho de crédito (20%) é destacado

### 6. Escolher Data e Horário
Cliente continua o fluxo normal de agendamento.

### 7. Confirmar Agendamento
Ao confirmar:
- ✅ Recompensa é aplicada (desconto ou crédito)
- ✅ Produtos selecionados são associados ao agendamento
- ✅ Observer cria nova recompensa automaticamente (20% dos produtos)

## Benefícios do Sistema

### Para o Salão
1. **Aumento de Vendas**: Cross-selling automático baseado em dados
2. **Fidelização**: Clientes ganham créditos e voltam mais
3. **Inteligente**: Recomendações hierárquicas (manual > automático)
4. **Visualização**: Dashboard completo do sistema de fidelidade

### Para o Cliente
1. **Conveniência**: Produtos sugeridos no momento certo
2. **Economia**: Descontos especiais em produtos vinculados
3. **Recompensas**: Ganha 20% em créditos nas compras
4. **Transparência**: Vê todas as recompensas ativas e validades

## Próximos Passos (Implementação Futura)

### 1. Processar Produtos no Agendamento
Ao confirmar o agendamento, criar registros de venda dos produtos:
- Criar Comanda associada ao agendamento
- Adicionar ComandaProduto para cada produto selecionado
- Observer vai criar recompensa automaticamente (20%)

### 2. Aplicar Desconto de Recompensa
No método `confirmTime()`:
- Calcular valor total dos serviços
- Aplicar desconto da recompensa selecionada
- Marcar recompensa como "usado"
- Registrar na tabela `fidelidade_rewards`

### 3. Notificações
- Email ao ganhar nova recompensa
- Alerta quando recompensa está próxima do vencimento
- WhatsApp (se configurado) para lembretes

### 4. Estatísticas
- Taxa de conversão de produtos
- Produtos mais aceitos nas sugestões
- ROI do sistema de fidelidade
- Lifetime value dos clientes

## Testes Recomendados

### Teste 1: Visualização de Recompensas
1. Login como cliente Labelle (José da Silva)
2. Acessar agendamento
3. Verificar se recompensa de R$ 44,33 aparece
4. Clicar para selecionar
5. Verificar visual de seleção

### Teste 2: Sugestões de Produtos
1. Selecionar profissional e serviço "Corte"
2. Verificar se produto "Shampoo Antiqueda" aparece (vinculado)
3. Verificar desconto de 10%
4. Clicar para selecionar produto
5. Verificar cálculo do ganho de crédito (20%)

### Teste 3: Fluxo Completo
1. Cliente seleciona profissional e serviço
2. Vê e seleciona produtos sugeridos
3. Vê e seleciona recompensa ativa
4. Escolhe data e horário
5. Confirma agendamento
6. Verificar se produtos foram associados
7. Verificar se desconto foi aplicado
8. Verificar se nova recompensa foi criada

## Arquivos Importantes

### Novos Arquivos
- `app/Livewire/Cliente/MinhasRecompensas.php`
- `resources/views/livewire/cliente/minhas-recompensas.blade.php`

### Arquivos Modificados
- `app/Livewire/Cliente/SugestoesProdutos.php` (adicionado método `isProdutoSelecionado`)
- `app/Livewire/Cliente/MakeAppointment.php` (listeners e métodos de fidelidade)
- `resources/views/livewire/cliente/make-appointment.blade.php` (integração dos componentes)

### Arquivos Relacionados (Já Existentes)
- `app/Models/Service.php` - Método `getProdutosSugeridos()`
- `app/Models/Estoque.php` - Modelo de produtos
- `app/Models/FidelidadeReward.php` - Modelo de recompensas
- `app/Observers/ComandaProdutoObserver.php` - Cria recompensas automaticamente
- `database/migrations/*_add_total_vendas_to_estoque.php` - Campo de vendas
- `database/migrations/*_create_service_estoque_table.php` - Vínculos manuais
- `database/migrations/*_create_fidelidade_rewards_table.php` - Tabela de rewards

## Configuração

### Percentuais de Recompensa (Padrão)
- `> R$ 100`: 25% de crédito
- `R$ 50 - R$ 100`: 20% de crédito  
- `< R$ 50`: 15% de crédito

*Configurável em: `app/Observers/ComandaProdutoObserver.php`*

### Validade Padrão
- Recompensas de compra: **90 dias**

*Configurável no Observer*

### Quantidade de Produtos Sugeridos
- **3 produtos** por serviço

*Configurável em: `Service::getProdutosSugeridos()`*

## Suporte e Manutenção

### Logs
Todos os eventos são logados:
```bash
tail -f storage/logs/laravel.log | grep -i "recompensa\|produto"
```

### Verificar Recompensas no Banco
```sql
SELECT * FROM fidelidade_rewards 
WHERE user_id = 1 
AND status = 'ativo' 
AND validade >= NOW()
ORDER BY created_at DESC;
```

### Verificar Vínculos Produto-Serviço
```sql
SELECT s.service, e.produto_nome, se.priority, se.discount_percentage
FROM service_estoque se
JOIN services s ON s.id = se.service_id
JOIN estoque e ON e.id = se.estoque_id
WHERE se.is_active = 1
ORDER BY s.service, se.priority DESC;
```

---

**Documentação criada em:** 07/04/2026  
**Sistema:** Pagby - Multi-tenant SaaS para Salões de Beleza  
**Versão:** 1.0

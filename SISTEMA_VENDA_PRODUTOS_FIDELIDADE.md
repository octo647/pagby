# Sistema de Venda de Produtos e Fidelização

## 📋 Visão Geral

Sistema que facilita a venda de produtos relacionados aos serviços da barbearia/salão, com recomendações inteligentes e programa de fidelidade que recompensa clientes com bônus ao comprar produtos.

---

## 🎯 Funcionalidades Principais

### 1. **Relacionamento Produtos ↔ Serviços**

**Hierarquia de Recomendação:**

```
┌─────────────────────────────────────────┐
│  PRIORIDADE 1: Produtos Manuais         │
│  ✓ Vinculados pelo proprietário         │
│  ✓ Ordenados por prioridade (1, 2, 3...) │
│  ✓ Com desconto opcional configurável    │
└─────────────────────────────────────────┘
                    ⬇️
┌─────────────────────────────────────────┐
│  PRIORIDADE 2: Mais Vendidos (Fallback) │
│  ✓ Automático quando não há manual      │
│  ✓ Baseado em total_vendas              │
│  ✓ Top 3 produtos da filial             │
└─────────────────────────────────────────┘
```

**Exemplo Prático:**
- Cliente agenda "Corte Masculino"
- Sistema verifica: Há produtos vinculados manualmente?
  - **SIM**: Oferece "Pomada Modeladora #1", "Shampoo Anticaspa #2", "Cera Fixadora #3"
  - **NÃO**: Oferece os 3 produtos mais vendidos da loja automaticamente

---

### 2. **Sistema de Recompensas (Fidelidade)**

Clientes ganham bônus ao comprar produtos que podem usar em serviços futuros.

**Tipos de Recompensas:**

#### **A) Créditos em Reais** ⭐ (Recomendado)
```php
// Cliente compra shampoo por R$ 50
// Ganha 20% em créditos = R$ 10
FidelidadeReward::criarCredito(
    userId: $clienteId,
    valor: 10.00,
    diasValidade: 90
);
```
✅ Simples de entender  
✅ Flexível para qualquer serviço  
✅ Validade de 90 dias

#### **B) Cupons de Desconto**
```php
// Gera cupom com código único
FidelidadeReward::criarCupom(
    userId: $clienteId,
    percentualDesconto: 15,
    diasValidade: 30
);
// Resultado: "PRODAB12C4 - 15% OFF"
```
✅ Marketing viral (cliente compartilha código)  
✅ Validade mais curta (30 dias) = urgência  

#### **C) Serviço Grátis**
```php
// Comprou produto premium? Ganha barba grátis
FidelidadeReward::criarServicoGratis(
    userId: $clienteId,
    serviceId: $servicoBarbear->id,
    diasValidade: 60
);
```
✅ Incentivo para produtos específicos  
✅ Aumenta frequência de visitas  

---

## 🗄️ Estrutura do Banco de Dados

### **Tabelas Criadas:**

#### `service_estoque` (Pivot - Relacionamento Manual)
```sql
id, service_id, estoque_id, 
priority (ordem de sugestão),
discount_percentage (desconto especial),
is_active (ativo para sugestão),
observacoes
```

#### `fidelidade_rewards` (Recompensas)
```sql
id, user_id, tipo (credito|cupom|servico_gratis),
valor_credito, codigo_cupom, percentual_desconto,
service_id, origem, validade, status,
usado_em, usado_em_comanda_id
```

### **Campos Adicionados:**

#### `estoque`
```sql
+ total_vendas INT (contador para ranking)
```

---

## 🛠️ Como Usar (Fluxo Completo)

### **Para Proprietários:**

#### **1. Vincular Produtos a Serviços**

**Interface Livewire:** `GerenciarProdutosServicos`

```blade
{{-- Em qualquer view do proprietário --}}
@livewire('proprietario.gerenciar-produtos-servicos', [
    'serviceId' => $service->id,
    'branchId' => $branchId
])
```

**Funcionalidades:**
- ✅ Adicionar produtos ao serviço
- ✅ Definir prioridade (1 = primeiro oferecido)
- ✅ Configurar desconto especial opcional
- ✅ Ativar/desativar produtos
- ✅ Ver produtos mais vendidos (fallback automático)

**Exemplo:**
```
Serviço: Corte Premium
├─ #1: Pomada Premium (15% OFF) ✓ Ativo
├─ #2: Shampoo Importado ✓ Ativo
└─ #3: Cera Modeladora ✗ Inativo
```

---

#### **2. Configurar Recompensas**

**No código de processamento de venda (Comanda):**

```php
use App\Models\FidelidadeReward;
use App\Models\ComandaProduto;

// Quando produto é vendido na comanda
$comandaProduto = ComandaProduto::create([...]);

// Gera recompensa (20% do valor em créditos)
$valorRecompensa = $comandaProduto->subtotal * 0.20;

FidelidadeReward::criarCredito(
    userId: $comanda->cliente_id,
    valor: $valorRecompensa,
    origem: 'compra_produto',
    comandaProdutoId: $comandaProduto->id,
    diasValidade: 90
);

// Incrementa contador de vendas do produto
$comandaProduto->estoque->incrementarVendas($comandaProduto->quantidade);
```

---

### **Para Clientes:**

#### **1. Ver Produtos Sugeridos ao Agendar**

```blade
{{-- No formulário de agendamento --}}
@php
    $produtosSugeridos = $service->getProdutosSugeridos($branchId, 3);
@endphp

@if($produtosSugeridos->isNotEmpty())
    <div class="mt-6 bg-blue-50 p-4 rounded">
        <h4 class="font-semibold mb-3">🛍️ Produtos Recomendados:</h4>
        
        @foreach($produtosSugeridos as $produto)
            <div class="flex justify-between items-center mb-2">
                <span>{{ $produto->produto_nome }}</span>
                
                @if($produto->pivot && $produto->pivot->discount_percentage > 0)
                    <span class="text-green-600 font-bold">
                        R$ {{ number_format($produto->preco_unitario, 2) }}
                        (-{{ $produto->pivot->discount_percentage }}% OFF!)
                    </span>
                @else
                    <span>R$ {{ number_format($produto->preco_unitario, 2) }}</span>
                @endif
                
                <button>Adicionar</button>
            </div>
        @endforeach
    </div>
@endif
```

#### **2. Ver Recompensas Disponíveis**

```php
// Saldo total de créditos do cliente
$saldoCreditos = FidelidadeReward::saldoCreditosCliente(auth()->id());
// Ex: R$ 35,50

// Todas recompensas ativas
$recompensas = FidelidadeReward::where('user_id', auth()->id())
    ->ativos()
    ->get();

foreach ($recompensas as $reward) {
    echo $reward->descricao_formatada;
    // "R$ 10,00"
    // "Cupom PRODAB12C4 - 15% OFF"
    // "Barba Express grátis"
}
```

#### **3. Usar Recompensa em Comanda**

```php
// Cliente aplica crédito na comanda
$reward = FidelidadeReward::find($rewardId);

if ($reward->isValido()) {
    // Aplica desconto na comanda
    $comanda->desconto_servicos += $reward->valor_credito;
    $comanda->save();
    
    // Marca recompensa como usada
    $reward->marcarComoUsado($comanda->id);
}
```

---

## 📊 Métodos Úteis nos Models

### **Service**
```php
// Obter produtos sugeridos (hierárquico)
$produtos = $service->getProdutosSugeridos($branchId, $limit = 3);

// Verifica se tem produtos manuais
$temManuais = $service->temProdutosRecomendadosManuais($branchId);

// Produtos relacionados manualmente
$manuais = $service->produtosRecomendados;
```

### **Estoque**
```php
// Incrementar vendas
$produto->incrementarVendas($quantidade);

// Registrar venda (atualiza estoque + contador)
$produto->registrarVenda($quantidade);

// Produtos mais vendidos
$topProdutos = Estoque::where('branch_id', $branchId)
    ->maisVendidos(10)
    ->get();

// Disponível para venda?
$produto->isDisponivelParaVenda(); // estoque > 0 && não vencido
```

### **FidelidadeReward**
```php
// Criar recompensas
FidelidadeReward::criarCredito($userId, $valor, $origem, $comandaProdutoId, $diasValidade);
FidelidadeReward::criarCupom($userId, $percentual, $origem, $comandaProdutoId, $diasValidade);
FidelidadeReward::criarServicoGratis($userId, $serviceId, $origem, $comandaProdutoId, $diasValidade);

// Consultar saldo do cliente
$saldo = FidelidadeReward::saldoCreditosCliente($userId);

// Usar recompensa
$reward->marcarComoUsado($comandaId);

// Verificar validade
$reward->isValido();
$reward->verificarExpiracao(); // atualiza status se expirou
```

---

## 🚀 Como Ativar o Sistema

### **1. Rodar Migrations**
```bash
php artisan migrate
```

### **2. Configurar Rotas** (adicionar em `routes/tenant.php`)
```php
Route::middleware(['auth', 'role:Proprietário'])->group(function () {
    Route::get('/servicos/{service}/produtos', function($serviceId) {
        return view('proprietario.produtos-servicos', [
            'serviceId' => $serviceId
        ]);
    })->name('proprietario.produtos-servicos');
});
```

### **3. Integrar no Fluxo de Vendas**

**No Observer de ComandaProduto** (criar se não existir):

```php
// app/Observers/ComandaProdutoObserver.php
namespace App\Observers;

use App\Models\ComandaProduto;
use App\Models\FidelidadeReward;

class ComandaProdutoObserver
{
    public function created(ComandaProduto $comandaProduto)
    {
        // Incrementa contador de vendas
        $comandaProduto->estoque->incrementarVendas($comandaProduto->quantidade);

        // Gera recompensa (configurar percentual desejado)
        $comanda = $comandaProduto->comanda;
        
        if ($comanda->cliente_id) {
            $valorRecompensa = $comandaProduto->subtotal * 0.20; // 20% em créditos
            
            FidelidadeReward::criarCredito(
                userId: $comanda->cliente_id,
                valor: $valorRecompensa,
                origem: 'compra_produto',
                comandaProdutoId: $comandaProduto->id,
                diasValidade: 90
            );
        }
    }
}
```

**Registrar Observer** em `app/Providers/AppServiceProvider.php`:
```php
use App\Models\ComandaProduto;
use App\Observers\ComandaProdutoObserver;

public function boot()
{
    ComandaProduto::observe(ComandaProdutoObserver::class);
}
```

---

## 📈 Estratégias de Negócio

### **Opção 1: Recompensas Generosas (20-30%)**
✅ Fideliza rapidamente  
✅ Aumenta ticket médio  
⚠️ Menor margem inicial  

### **Opção 2: Recompensas Moderadas (10-15%)**
✅ Equilíbrio lucro/fidelização  
✅ Sustentável longo prazo  

### **Opção 3: Recompensas por Categoria**
```php
// Produtos premium: 25% crédito
// Produtos comuns: 10% crédito
$percentual = $produto->categoria === 'Premium' ? 0.25 : 0.10;
```

### **Opção 4: Combo de Recompensas**
```php
// Valor alto (> R$ 100): Crédito
if ($comandaProduto->subtotal >= 100) {
    FidelidadeReward::criarCredito(...);
}
// Valor médio (R$ 50-100): Cupom
elseif ($comandaProduto->subtotal >= 50) {
    FidelidadeReward::criarCupom(...);
}
```

---

## 🎨 Sugestões de Melhorias Futuras

1. **WhatsApp:** Enviar produtos sugeridos após confirmação de agendamento
2. **Email:** Lembrete de recompensas prestes a expirar (7 dias antes)
3. **Notificações Push:** "Você tem R$ 15 em créditos! Use hoje"
4. **Dashboard Analytics:** Produtos mais convertidos por serviço
5. **Gamificação:** Níveis de fidelidade (Bronze, Prata, Ouro)
6. **Cross-sell Inteligente:** Análise de produtos comprados juntos

---

## 📞 Suporte

Para dúvidas ou problemas:
1. Verificar logs: `storage/logs/laravel.log`
2. Testar migrations: `php artisan migrate:fresh --seed`
3. Limpar cache: `php artisan cache:clear && php artisan config:clear`

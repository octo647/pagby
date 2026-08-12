# Guia de Ativação Completa - Sistema de Produtos e Fidelização

## 📋 Checklist de Implementação

### ✅ Passo 1: Rodar Migrations

```bash
# Certifique-se de estar conectado ao banco tenant
php artisan migrate

# Verificar se tabelas foram criadas:
# - service_estoque
# - fidelidade_rewards
# - estoque (agora com campo total_vendas)
```

---

### ✅ Passo 2: Registrar Observer

**Arquivo:** `app/Providers/AppServiceProvider.php`

```php
<?php

namespace App\Providers;

use App\Models\ComandaProduto;
use App\Observers\ComandaProdutoObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Registrar observer para automação de recompensas
        ComandaProduto::observe(ComandaProdutoObserver::class);
    }

    public function register(): void
    {
        //
    }
}
```

**Teste:**
```bash
# Criar uma venda de produto via tinker para testar
php artisan tinker

>>> $comanda = \App\Models\Comanda::first();
>>> $produto = \App\Models\Estoque::first();
>>> $venda = \App\Models\ComandaProduto::create([
    'comanda_id' => $comanda->id,
    'estoque_id' => $produto->id,
    'quantidade' => 1,
    'preco_unitario' => $produto->preco_unitario,
    'subtotal' => $produto->preco_unitario,
]);

>>> # Verificar se recompensa foi criada
>>> \App\Models\FidelidadeReward::where('user_id', $comanda->cliente_id)->count();
```

---

### ✅ Passo 3: Agendar Comandos (Cron)

**Arquivo:** `app/Console/Kernel.php`

```php
<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Expirar recompensas vencidas (diariamente às 2h)
        $schedule->command('fidelidade:expirar-recompensas')
            ->dailyAt('02:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/fidelidade.log'));

        // Notificar sobre recompensas expirando (diariamente às 9h)
        $schedule->command('fidelidade:notificar-expirando --dias=7')
            ->dailyAt('09:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/fidelidade.log'));
    }
}
```

**Configurar Cron no servidor:**
```bash
# Editar crontab
crontab -e

# Adicionar linha (ajustar caminho do artisan):
* * * * * cd /caminho/para/pagby && php artisan schedule:run >> /dev/null 2>&1
```

**Testar comandos manualmente:**
```bash
# Modo dry-run (sem alterar dados)
php artisan fidelidade:expirar-recompensas --dry-run
php artisan fidelidade:notificar-expirando --dry-run

# Execução real
php artisan fidelidade:expirar-recompensas
php artisan fidelidade:notificar-expirando --dias=7
```

---

### ✅ Passo 4: Adicionar Rotas

**Arquivo:** `routes/tenant.php`

```php
use App\Livewire\Proprietario\GerenciarProdutosServicos;
use App\Livewire\Cliente\SugestoesProdutos;

// Rotas para Proprietário
Route::middleware(['auth', 'role:Proprietário'])->prefix('proprietario')->group(function () {
    
    // Gerenciar produtos recomendados de um serviço
    Route::get('/servicos/{service}/produtos', function($serviceId) {
        return view('proprietario.produtos-servicos', [
            'serviceId' => $serviceId
        ]);
    })->name('proprietario.service.produtos');
});

// Rotas para Cliente
Route::middleware(['auth', 'role:Cliente'])->prefix('cliente')->group(function () {
    
    // Ver minhas recompensas
    Route::get('/recompensas', function() {
        $recompensas = \App\Models\FidelidadeReward::where('user_id', auth()->id())
            ->ativos()
            ->get();
        
        $saldoCreditos = \App\Models\FidelidadeReward::saldoCreditosCliente(auth()->id());
        
        return view('cliente.recompensas', compact('recompensas', 'saldoCreditos'));
    })->name('cliente.recompensas');
});
```

---

### ✅ Passo 5: Criar Views

#### **View Proprietário - Gerenciar Produtos**

**Arquivo:** `resources/views/proprietario/produtos-servicos.blade.php`

```blade
@extends('layouts.proprietario')

@section('content')
<div class="container mx-auto px-4 py-8">
    @livewire('proprietario.gerenciar-produtos-servicos', [
        'serviceId' => $serviceId,
        'branchId' => auth()->user()->branches->first()?->id
    ])
</div>
@endsection
```

#### **View Cliente - Minhas Recompensas**

**Arquivo:** `resources/views/cliente/recompensas.blade.php`

```blade
@extends('layouts.cliente')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">🎁 Minhas Recompensas</h1>

    {{-- Saldo de Créditos --}}
    @if($saldoCreditos > 0)
        <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl p-6 mb-6 shadow-lg">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm opacity-90">Saldo Total em Créditos</p>
                    <p class="text-4xl font-bold mt-1">R$ {{ number_format($saldoCreditos, 2, ',', '.') }}</p>
                    <p class="text-sm opacity-75 mt-2">Use em qualquer serviço!</p>
                </div>
                <div class="text-6xl">💰</div>
            </div>
        </div>
    @endif

    {{-- Lista de Recompensas --}}
    @if($recompensas->isEmpty())
        <div class="bg-gray-100 rounded-lg p-8 text-center">
            <p class="text-gray-600 mb-4">Você ainda não tem recompensas.</p>
            <p class="text-sm text-gray-500">Compre produtos e ganhe bônus para usar em serviços!</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($recompensas as $reward)
                <div class="bg-white rounded-lg shadow p-6 border-l-4 
                            {{ $reward->tipo === 'credito' ? 'border-green-500' : 
                               ($reward->tipo === 'cupom' ? 'border-blue-500' : 'border-purple-500') }}">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-2xl">
                                    {{ $reward->tipo === 'credito' ? '💰' : 
                                       ($reward->tipo === 'cupom' ? '🏷️' : '✂️') }}
                                </span>
                                <h3 class="text-xl font-bold text-gray-800">
                                    {{ $reward->descricao_formatada }}
                                </h3>
                            </div>
                            
                            <div class="text-sm text-gray-600 space-y-1">
                                <p>📅 Válido até: {{ $reward->validade->format('d/m/Y') }}</p>
                                <p>⏰ {{ $reward->validade->diffForHumans() }}</p>
                                @if($reward->origem)
                                    <p class="text-xs text-gray-500">
                                        Origem: {{ match($reward->origem) {
                                            'compra_produto' => 'Compra de produto',
                                            'promocao' => 'Promoção especial',
                                            'indicacao' => 'Indicação de amigo',
                                            'aniversario' => 'Presente de aniversário',
                                            default => ucfirst($reward->origem)
                                        } }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                            Usar Agora
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
```

---

### ✅ Passo 6: Integrar Sugestões no Agendamento

**Em qualquer formulário de agendamento existente:**

```blade
{{-- Após seleção de serviço --}}
<div>
    {{-- Campos existentes do agendamento --}}
    <input type="text" name="servico" ...>
    <input type="date" name="data" ...>
    
    {{-- NOVO: Sugestões de produtos --}}
    @livewire('cliente.sugestoes-produtos', [
        'serviceId' => $serviceId,
        'branchId' => $branchId
    ])
</div>

<script>
    // Quando serviço mudar, recarregar produtos
    document.querySelector('[name="servico"]').addEventListener('change', function(e) {
        Livewire.dispatch('servicoSelecionado', {
            serviceId: e.target.value,
            branchId: {{ $branchId }}
        });
    });

    // Receber produtos selecionados
    Livewire.on('produtosSelecionados', (produtosIds) => {
        console.log('Produtos selecionados:', produtosIds);
        // Adicionar ao formulário ou processar conforme necessário
    });
</script>
```

---

### ✅ Passo 7: Adicionar Links na Navegação

#### **Menu do Proprietário:**

```blade
{{-- resources/views/layouts/proprietario.blade.php --}}
<nav>
    <a href="{{ route('proprietario.services') }}">Serviços</a>
    <a href="{{ route('proprietario.estoque') }}">Estoque</a>
    {{-- NOVO --}}
    <a href="#" x-data @click.prevent="/* modal de escolher serviço */">
        🔗 Vincular Produtos a Serviços
    </a>
</nav>
```

#### **Menu do Cliente:**

```blade
{{-- resources/views/layouts/cliente.blade.php --}}
<nav>
    <a href="{{ route('cliente.agendamentos') }}">Meus Agendamentos</a>
    {{-- NOVO --}}
    <a href="{{ route('cliente.recompensas') }}">
        🎁 Minhas Recompensas
        @if($saldoCreditos = \App\Models\FidelidadeReward::saldoCreditosCliente(auth()->id()))
            <span class="badge">R$ {{ number_format($saldoCreditos, 2, ',', '.') }}</span>
        @endif
    </a>
</nav>
```

---

### ✅ Passo 8: Configurar Políticas de Recompensa

**Criar arquivo de configuração (opcional):**

**Arquivo:** `config/fidelidade.php`

```php
<?php

return [
    // Tipo padrão de recompensa
    'tipo_padrao' => env('FIDELIDADE_TIPO', 'credito'), // credito|cupom|servico_gratis

    // Percentual de crédito sobre venda de produtos
    'percentual_credito' => env('FIDELIDADE_PERCENTUAL', 20), // 20%

    // Validade padrão das recompensas (dias)
    'validade_dias' => [
        'credito' => 90,
        'cupom' => 30,
        'servico_gratis' => 60,
    ],

    // Regras por valor de compra
    'regras_por_valor' => [
        [
            'valor_minimo' => 100,
            'tipo' => 'credito',
            'percentual' => 25, // 25% para compras acima de R$ 100
        ],
        [
            'valor_minimo' => 50,
            'tipo' => 'credito',
            'percentual' => 20, // 20% para compras entre R$ 50-100
        ],
        [
            'valor_minimo' => 0,
            'tipo' => 'credito',
            'percentual' => 15, // 15% padrão
        ],
    ],

    // Notificações
    'notificar_expiracao' => true,
    'dias_antes_notificar' => 7,
];
```

**Usar no Observer:**

```php
// app/Observers/ComandaProdutoObserver.php
private function getRecompensaConfig(ComandaProduto $comandaProduto): array
{
    $valor = $comandaProduto->subtotal;
    $regras = config('fidelidade.regras_por_valor', []);

    foreach ($regras as $regra) {
        if ($valor >= $regra['valor_minimo']) {
            return [
                'tipo' => $regra['tipo'],
                'percentual' => $regra['percentual'] / 100,
                'dias_validade' => config("fidelidade.validade_dias.{$regra['tipo']}", 90),
            ];
        }
    }

    // Fallback
    return [
        'tipo' => 'credito',
        'percentual' => 0.15,
        'dias_validade' => 90,
    ];
}
```

---

## 🧪 Testes

### ⚠️ IMPORTANTE: Multi-Tenancy

**Antes de testar no Tinker, você DEVE inicializar o contexto do tenant!**

```bash
php artisan tinker
```

```php
// 1️⃣ SEMPRE inicialize o tenant primeiro!
$tenant = \App\Models\Tenant::first(); // ou ->find('id-especifico')
tenancy()->initialize($tenant);

// 2️⃣ Confirme que está no banco correto
echo "Banco: " . \DB::connection()->getDatabaseName(); 
// Deve mostrar: tenant12345-abcd-...

// 3️⃣ Agora pode testar normalmente
```

📚 **Ver guia completo:** [TINKER_MULTITENANCY.md](TINKER_MULTITENANCY.md)

---

### Teste 1: Vincular Produto a Serviço
1. Acesse `/proprietario/servicos/{service_id}/produtos`
2. Clique em "+ Adicionar Produto"
3. Selecione produto, prioridade e desconto
4. Salve e verifique na lista

### Teste 2: Venda Gera Recompensa
```bash
php artisan tinker
```

```php
// INICIALIZAR TENANT PRIMEIRO!
$tenant = \App\Models\Tenant::first();
tenancy()->initialize($tenant);
echo "✅ Conectado a: " . \DB::connection()->getDatabaseName() . "\n";

// Agora sim, testar venda
$comanda = \App\Models\Comanda::first();
$produto = \App\Models\Estoque::first();

\App\Models\ComandaProduto::create([
    'comanda_id' => $comanda->id,
    'estoque_id' => $produto->id,
    'quantidade' => 1,
    'preco_unitario' => 50,
    'subtotal' => 50,
]);

// Verificar recompensa
$recompensa = \App\Models\FidelidadeReward::latest()->first();
echo "🎁 Recompensa: R$ {$recompensa->valor_credito}\n";
```

### Teste 3: Sugestões Hierárquicas
```bash
php artisan tinker
```

```php
// INICIALIZAR TENANT
$tenant = \App\Models\Tenant::first();
tenancy()->initialize($tenant);

$service = \App\Models\Service::first();
$branch = \App\Models\Branch::first();

// Sem produtos manuais (deve retornar mais vendidos)
$sugeridos = $service->getProdutosSugeridos($branch->id, 3);
foreach($sugeridos as $p) {
    echo "- {$p->produto_nome} (vendas: {$p->total_vendas})\n";
}

// Com produtos manuais
// (vincular via interface primeiro)
$sugeridos = $service->getProdutosSugeridos($branch->id, 3);
```

---

## ✅ Checklist Final

- [ ] Migrations rodadas
- [ ] Observer registrado
- [ ] Comandos agendados no cron
- [ ] Rotas adicionadas
- [ ] Views criadas
- [ ] Links na navegação
- [ ] Testar venda → recompensa
- [ ] Testar sugestões de produtos
- [ ] Verificar logs: `storage/logs/laravel.log`

---

## 📞 Troubleshooting

### Recompensas não estão sendo criadas
```bash
# Verificar logs
tail -f storage/logs/laravel.log

# Verificar se observer está registrado
php artisan tinker
>>> app(\App\Providers\AppServiceProvider::class)->boot();
```

### Produtos não aparecem nas sugestões
```bash
php artisan tinker
>>> $service = \App\Models\Service::find(ID);
>>> $service->getProdutosSugeridos(BRANCH_ID, 3);
>>> # Verificar se tem produtos em estoque
>>> \App\Models\Estoque::where('branch_id', BRANCH_ID)->where('quantidade_atual', '>', 0)->count();
```

### Comandos não rodam automaticamente
```bash
# Verificar cron
crontab -l

# Testar schedule manualmente
php artisan schedule:run

# Ver próximos agendamentos
php artisan schedule:list
```

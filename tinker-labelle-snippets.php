# COPIE E COLE ISSO NO TINKER PARA CONECTAR AO LABELLE

# ============================================================================
# INICIALIZAR TENANT LABELLE
# ============================================================================

// Buscar tenant Labelle
$tenant = \App\Models\Tenant::where('id', 'like', '%labelle%')
    ->orWhereHas('domains', function($q) {
        $q->where('domain', 'like', '%labelle%');
    })
    ->orWhere('name', 'like', '%labelle%')
    ->first();

if (!$tenant) {
    echo "❌ Labelle não encontrado!\n\nTenants disponíveis:\n";
    foreach(\App\Models\Tenant::all() as $t) {
        echo "  - {$t->name} (ID: {$t->id})\n";
    }
} else {
    tenancy()->initialize($tenant);
    echo "✅ Conectado ao Labelle!\n";
    echo "🏢 Nome: {$tenant->name}\n";
    echo "🆔 ID: {$tenant->id}\n";
    echo "📊 DB: " . DB::connection()->getDatabaseName() . "\n\n";
}

# ============================================================================
# COMANDOS ÚTEIS - LABELLE
# ============================================================================

# Ver produtos em estoque
$produtos = \App\Models\Estoque::all();

# Ver serviços
$servicos = \App\Models\Service::all();

# Ver clientes
$clientes = \App\Models\User::whereHas('roles', function($q) {
    $q->where('role', 'Cliente');
})->get();

# Ver recompensas ativas
$recompensas = \App\Models\FidelidadeReward::where('status', 'ativo')->get();

# Produtos mais vendidos
$maisVendidos = \App\Models\Estoque::orderBy('total_vendas', 'desc')->limit(5)->get();

# ============================================================================
# TESTE RÁPIDO: VENDA + RECOMPENSA
# ============================================================================

// 1. Pegar dados necessários
$branch = \App\Models\Branch::first();
$funcionario = \App\Models\User::whereHas('roles', function($q) { $q->where('role', 'Funcionário'); })->first();
$cliente = \App\Models\User::whereHas('roles', function($q) { $q->where('role', 'Cliente'); })->first();
$produto = \App\Models\Estoque::where('quantidade_atual', '>', 0)->first();

// 2. Criar comanda
$comanda = \App\Models\Comanda::create([
    'numero_comanda' => \App\Models\Comanda::gerarNumeroComanda($branch->id),
    'branch_id' => $branch->id,
    'client_id' => $cliente->id,
    'cliente_nome' => $cliente->name,
    'funcionario_id' => $funcionario->id,
    'status' => 'Aberta',
    'data_abertura' => now(),
    'subtotal_produtos' => 0,
    'total_geral' => 0,
]);

// 3. Registrar venda (ISSO GERA RECOMPENSA AUTOMATICAMENTE!)
$venda = \App\Models\ComandaProduto::create([
    'comanda_id' => $comanda->id,
    'estoque_id' => $produto->id,
    'quantidade' => 1,
    'preco_unitario' => $produto->preco_unitario,
    'subtotal' => $produto->preco_unitario,
]);

// 4. Verificar recompensa criada
$recompensa = \App\Models\FidelidadeReward::latest()->first();
echo "🎁 Recompensa: " . $recompensa->descricao_formatada . "\n";

// 5. Verificar total_vendas
$produto->refresh();
echo "📊 Total vendas: {$produto->total_vendas}\n";

# ============================================================================
# VINCULAR PRODUTO A SERVIÇO
# ============================================================================

$service = \App\Models\Service::first();
$produto = \App\Models\Estoque::first();

DB::table('service_estoque')->insert([
    'service_id' => $service->id,
    'estoque_id' => $produto->id,
    'priority' => 1,
    'discount_percentage' => 10, // 10% OFF
    'is_active' => true,
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "✅ Produto vinculado ao serviço!\n";

# ============================================================================
# TESTAR SUGESTÕES HIERÁRQUICAS
# ============================================================================

$service = \App\Models\Service::first();
$branch = \App\Models\Branch::first();

// Ver produtos sugeridos
$sugeridos = $service->getProdutosSugeridos($branch->id, 3);

echo "💡 Produtos sugeridos para '{$service->service}':\n";
foreach($sugeridos as $p) {
    echo "  - {$p->produto_nome} (R$ {$p->preco_unitario})\n";
}

# ============================================================================
# VER RECOMPENSAS DE UM CLIENTE
# ============================================================================

$cliente = \App\Models\User::whereHas('roles', function($q) { 
    $q->where('role', 'Cliente'); 
})->first();

// Saldo de créditos
$saldo = \App\Models\FidelidadeReward::saldoCreditosCliente($cliente->id);
echo "💰 Saldo de {$cliente->name}: R$ " . number_format($saldo, 2, ',', '.') . "\n";

// Todas as recompensas ativas
$recompensas = \App\Models\FidelidadeReward::where('user_id', $cliente->id)
    ->ativos()
    ->get();

foreach($recompensas as $r) {
    echo "🎁 {$r->descricao_formatada} - Válido até {$r->validade->format('d/m/Y')}\n";
}

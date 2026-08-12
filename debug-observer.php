#!/usr/bin/env php
<?php
/**
 * Debug Observer - Verificar por que recompensa não está sendo criada
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n╔════════════════════════════════════════════════════════════╗\n";
echo "║  DEBUG: OBSERVER DE FIDELIZAÇÃO                           ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// Conectar ao Labelle
$tenant = \App\Models\Tenant::where('id', 'like', '%labelle%')
    ->orWhereHas('domains', function($q) {
        $q->where('domain', 'like', '%labelle%');
    })
    ->orWhere('name', 'like', '%labelle%')
    ->first();

if (!$tenant) {
    die("❌ Tenant Labelle não encontrado!\n\n");
}

tenancy()->initialize($tenant);
echo "✅ Conectado: {$tenant->name}\n\n";

// Verificação 1: Observer está registrado?
echo "1️⃣ VERIFICANDO REGISTRO DO OBSERVER\n";
echo "═══════════════════════════════════════════════════════════\n";

// Verificar se o AppServiceProvider registra o Observer
$appServiceProvider = file_get_contents(app_path('Providers/AppServiceProvider.php'));
if (strpos($appServiceProvider, 'ComandaProdutoObserver') !== false) {
    echo "✅ ComandaProdutoObserver está mencionado no AppServiceProvider\n";
} else {
    echo "❌ ComandaProdutoObserver NÃO encontrado no AppServiceProvider\n";
}

// Verificar se o arquivo do Observer existe
if (file_exists(app_path('Observers/ComandaProdutoObserver.php'))) {
    echo "✅ Arquivo ComandaProdutoObserver.php existe\n";
} else {
    echo "❌ Arquivo ComandaProdutoObserver.php NÃO existe\n";
}

// Verificar se o Model ComandaProduto existe
if (class_exists(\App\Models\ComandaProduto::class)) {
    echo "✅ Model ComandaProduto existe\n";
} else {
    echo "❌ Model ComandaProduto NÃO existe\n";
}

echo "\n";

// Verificação 2: Dados necessários
echo "2️⃣ VERIFICANDO DADOS PARA TESTE\n";
echo "═══════════════════════════════════════════════════════════\n";

$branch = \App\Models\Branch::first();
$funcionario = \App\Models\User::whereHas('roles', function($q) {
    $q->where('role', 'Funcionário');
})->first();
$cliente = \App\Models\User::whereHas('roles', function($q) {
    $q->where('role', 'Cliente');
})->first();
$produto = \App\Models\Estoque::where('quantidade_atual', '>', 0)->first();

echo "Filial: " . ($branch ? "✅ {$branch->name}" : "❌ Não encontrada") . "\n";
echo "Funcionário: " . ($funcionario ? "✅ {$funcionario->name}" : "❌ Não encontrado") . "\n";
echo "Cliente: " . ($cliente ? "✅ {$cliente->name} (ID: {$cliente->id})" : "❌ Não encontrado") . "\n";
echo "Produto: " . ($produto ? "✅ {$produto->produto_nome} (R$ {$produto->preco_unitario})" : "❌ Não encontrado") . "\n\n";

if (!$branch || !$funcionario || !$cliente || !$produto) {
    die("❌ Dados necessários não encontrados!\n\n");
}

// Verificação 3: Tabela fidelidade_rewards existe?
echo "3️⃣ VERIFICANDO TABELA FIDELIDADE_REWARDS\n";
echo "═══════════════════════════════════════════════════════════\n";

try {
    $count = DB::table('fidelidade_rewards')->count();
    echo "✅ Tabela existe! Total de recompensas: {$count}\n\n";
} catch (\Exception $e) {
    echo "❌ Tabela NÃO existe ou erro: {$e->getMessage()}\n\n";
    die();
}

// Verificação 4: Criar venda e observar o que acontece
echo "4️⃣ TESTE DE VENDA COM OBSERVADOR\n";
echo "═══════════════════════════════════════════════════════════\n\n";

try {
    echo "📝 Criando comanda...\n";
    $comanda = \App\Models\Comanda::create([
        'numero_comanda' => \App\Models\Comanda::gerarNumeroComanda($branch->id),
        'branch_id' => $branch->id,
        'client_id' => $cliente->id,  // IMPORTANTE: client_id presente
        'cliente_nome' => $cliente->name,
        'funcionario_id' => $funcionario->id,
        'status' => 'Aberta',
        'data_abertura' => now(),
        'subtotal_produtos' => 0,
        'total_geral' => 0,
    ]);
    
    echo "   ✅ Comanda criada: #{$comanda->numero_comanda}\n";
    echo "   📌 ID: {$comanda->id}\n";
    echo "   👤 Cliente ID: {$comanda->client_id}\n\n";
    
    // Verificar se comanda tem relação com cliente
    $comanda->refresh();
    echo "   Comanda recarregada:\n";
    echo "   - branch_id: {$comanda->branch_id}\n";
    echo "   - client_id: " . ($comanda->client_id ?? 'NULL') . "\n";
    echo "   - funcionario_id: {$comanda->funcionario_id}\n\n";
    
    echo "💳 Registrando venda (OBSERVER DEVE AGIR AQUI)...\n";
    
    $recompensasAntes = \App\Models\FidelidadeReward::count();
    echo "   📊 Recompensas antes da venda: {$recompensasAntes}\n\n";
    
    $venda = \App\Models\ComandaProduto::create([
        'comanda_id' => $comanda->id,
        'estoque_id' => $produto->id,
        'quantidade' => 1,
        'preco_unitario' => $produto->preco_unitario,
        'subtotal' => $produto->preco_unitario,
    ]);
    
    echo "   ✅ Venda registrada!\n";
    echo "   - ID Venda: {$venda->id}\n";
    echo "   - Valor: R$ " . number_format($venda->subtotal, 2, ',', '.') . "\n\n";
    
    // Aguardar um pouco para o Observer processar
    sleep(1);
    
    $recompensasDepois = \App\Models\FidelidadeReward::count();
    echo "   📊 Recompensas depois da venda: {$recompensasDepois}\n\n";
    
    if ($recompensasDepois > $recompensasAntes) {
        echo "✅ SUCESSO! Recompensa criada automaticamente!\n\n";
        
        $recompensa = \App\Models\FidelidadeReward::latest()->first();
        echo "╔═══════════════════════════════════════════════════╗\n";
        echo "║  DETALHES DA RECOMPENSA                          ║\n";
        echo "╚═══════════════════════════════════════════════════╝\n\n";
        echo "ID: {$recompensa->id}\n";
        echo "Cliente ID: {$recompensa->user_id}\n";
        echo "Tipo: {$recompensa->tipo}\n";
        echo "Status: {$recompensa->status}\n";
        
        if ($recompensa->tipo === 'credito') {
            echo "Valor Crédito: R$ " . number_format($recompensa->valor_credito, 2, ',', '.') . "\n";
        }
        
        echo "Origem: {$recompensa->origem}\n";
        echo "Comanda Produto ID: {$recompensa->comanda_produto_id}\n";
        echo "Validade: {$recompensa->validade->format('d/m/Y')}\n\n";
        
    } else {
        echo "❌ FALHA! Recompensa NÃO foi criada!\n\n";
        
        echo "🔍 DIAGNÓSTICO:\n";
        echo "═══════════════════════════════════════════════════════════\n";
        
        // Verificar relações
        $venda->load(['comanda', 'estoque']);
        
        echo "Venda carregada:\n";
        echo "  - comanda_id: {$venda->comanda_id}\n";
        echo "  - Comanda existe? " . ($venda->comanda ? "SIM" : "NÃO") . "\n";
        
        if ($venda->comanda) {
            echo "  - client_id na comanda: " . ($venda->comanda->client_id ?? 'NULL') . "\n";
        }
        
        echo "  - estoque_id: {$venda->estoque_id}\n";
        echo "  - Estoque existe? " . ($venda->estoque ? "SIM" : "NÃO") . "\n\n";
        
        echo "⚠️ POSSÍVEIS CAUSAS:\n";
        echo "  1. Observer não está sendo disparado\n";
        echo "  2. Erro silencioso no Observer (checar logs)\n";
        echo "  3. Condição no Observer está falhando\n";
        echo "  4. Problema na criação do FidelidadeReward\n\n";
        
        echo "📋 Verificar logs em storage/logs/laravel.log\n\n";
    }
    
    // Verificar total_vendas foi incrementado
    $produto->refresh();
    echo "📊 Total vendas do produto: {$produto->total_vendas}\n";
    echo "   " . ($produto->total_vendas > 0 ? "✅" : "❌") . " Contador foi incrementado\n\n";
    
} catch (\Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n\n";
}

// Verificação 5: Logs recentes
echo "5️⃣ LOGS RECENTES (últimas 20 linhas)\n";
echo "═══════════════════════════════════════════════════════════\n";

$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $lines = file($logFile);
    $lastLines = array_slice($lines, -20);
    
    foreach($lastLines as $line) {
        if (stripos($line, 'fidelidade') !== false || 
            stripos($line, 'observer') !== false || 
            stripos($line, 'recompensa') !== false) {
            echo $line;
        }
    }
} else {
    echo "Arquivo de log não encontrado.\n";
}

echo "\n";

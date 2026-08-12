#!/usr/bin/env php
<?php
/**
 * Teste Rápido - Sistema de Fidelização
 * 
 * Execute: php teste-rapido-fidelidade.php
 * 
 * Este script demonstra o fluxo completo:
 * 1. Conecta ao primeiro tenant
 * 2. Cria um produto
 * 3. Simula uma venda
 * 4. Verifica se a recompensa foi gerada automaticamente
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  TESTE RÁPIDO - SISTEMA DE FIDELIZAÇÃO E PRODUTOS         ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// Passo 1: Inicializar Tenant
echo "📍 Passo 1: Conectando ao tenant...\n";
try {
    $tenant = \App\Models\Tenant::first();
    
    if (!$tenant) {
        die("   ❌ ERRO: Nenhum tenant encontrado! Crie um tenant primeiro.\n\n");
    }
    
    tenancy()->initialize($tenant);
    
    $database = DB::connection()->getDatabaseName();
    echo "   ✅ Conectado ao tenant: {$tenant->name}\n";
    echo "   📊 Banco de dados: {$database}\n\n";
} catch (\Exception $e) {
    die("   ❌ ERRO: " . $e->getMessage() . "\n\n");
}

// Passo 2: Verificar ou criar dados necessários
echo "📦 Passo 2: Preparando dados de teste...\n";

try {
    $branch = \App\Models\Branch::first();
    if (!$branch) {
        die("   ❌ ERRO: Nenhuma filial encontrada! Crie uma filial primeiro.\n\n");
    }
    echo "   ✅ Filial: {$branch->name}\n";
    
    $funcionario = \App\Models\User::whereHas('roles', function($q) {
        $q->where('role', 'Funcionário');
    })->first();
    
    if (!$funcionario) {
        die("   ❌ ERRO: Nenhum funcionário encontrado!\n\n");
    }
    echo "   ✅ Funcionário: {$funcionario->name}\n";
    
    $cliente = \App\Models\User::whereHas('roles', function($q) {
        $q->where('role', 'Cliente');
    })->first();
    
    if (!$cliente) {
        die("   ❌ ERRO: Nenhum cliente encontrado!\n\n");
    }
    echo "   ✅ Cliente: {$cliente->name}\n\n";
    
} catch (\Exception $e) {
    die("   ❌ ERRO: " . $e->getMessage() . "\n\n");
}

// Passo 3: Criar produto de teste
echo "🛍️  Passo 3: Criando produto de teste...\n";
try {
    $produto = \App\Models\Estoque::create([
        'branch_id' => $branch->id,
        'produto_nome' => 'Produto Teste Fidelização ' . time(),
        'categoria' => 'Teste',
        'preco_unitario' => 50.00,
        'quantidade_atual' => 10,
        'quantidade_minima' => 2,
        'total_vendas' => 0,
    ]);
    
    echo "   ✅ Produto criado: {$produto->produto_nome}\n";
    echo "   💰 Preço: R$ " . number_format($produto->preco_unitario, 2, ',', '.') . "\n\n";
} catch (\Exception $e) {
    die("   ❌ ERRO ao criar produto: " . $e->getMessage() . "\n\n");
}

// Passo 4: Criar comanda
echo "📝 Passo 4: Criando comanda...\n";
try {
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
    
    echo "   ✅ Comanda #{$comanda->numero_comanda} criada\n\n";
} catch (\Exception $e) {
    die("   ❌ ERRO ao criar comanda: " . $e->getMessage() . "\n\n");
}

// Passo 5: Registrar venda (OBSERVER DEVE CRIAR RECOMPENSA!)
echo "💳 Passo 5: Registrando venda do produto...\n";
try {
    // Associar cliente à comanda antes da venda
    $comanda->update(['cliente_id' => $cliente->id]);
    
    $venda = \App\Models\ComandaProduto::create([
        'comanda_id' => $comanda->id,
        'estoque_id' => $produto->id,
        'quantidade' => 1,
        'preco_unitario' => $produto->preco_unitario,
        'subtotal' => $produto->preco_unitario,
    ]);
    
    echo "   ✅ Venda registrada!\n";
    echo "   💵 Valor: R$ " . number_format($venda->subtotal, 2, ',', '.') . "\n\n";
} catch (\Exception $e) {
    die("   ❌ ERRO ao registrar venda: " . $e->getMessage() . "\n\n");
}

// Passo 6: Verificar se recompensa foi criada automaticamente
echo "🎁 Passo 6: Verificando recompensa gerada...\n";
try {
    sleep(1); // Pequeno delay para garantir que observer executou
    
    $recompensa = \App\Models\FidelidadeReward::where('user_id', $cliente->id)
        ->where('comanda_produto_id', $venda->id)
        ->first();
    
    if ($recompensa) {
        echo "   ✅ SUCESSO! Recompensa criada automaticamente!\n";
        echo "   ╔═══════════════════════════════════════════════════╗\n";
        echo "   ║  DETALHES DA RECOMPENSA                           ║\n";
        echo "   ╠═══════════════════════════════════════════════════╣\n";
        echo "   ║  Cliente: " . str_pad($cliente->name, 40) . " ║\n";
        echo "   ║  Tipo: " . str_pad(ucfirst($recompensa->tipo), 43) . " ║\n";
        
        if ($recompensa->tipo === 'credito') {
            echo "   ║  Valor: R$ " . str_pad(number_format($recompensa->valor_credito, 2, ',', '.'), 38) . " ║\n";
        } elseif ($recompensa->tipo === 'cupom') {
            echo "   ║  Cupom: " . str_pad($recompensa->codigo_cupom, 40) . " ║\n";
            echo "   ║  Desconto: " . str_pad($recompensa->percentual_desconto . '%', 37) . " ║\n";
        }
        
        echo "   ║  Válido até: " . str_pad($recompensa->validade->format('d/m/Y'), 35) . " ║\n";
        echo "   ║  Origem: " . str_pad('Compra de produto', 40) . " ║\n";
        echo "   ╚═══════════════════════════════════════════════════╝\n\n";
        
        // Verificar saldo total
        $saldoTotal = \App\Models\FidelidadeReward::saldoCreditosCliente($cliente->id);
        echo "   💰 Saldo total de créditos do cliente: R$ " . number_format($saldoTotal, 2, ',', '.') . "\n\n";
        
    } else {
        echo "   ⚠️  ATENÇÃO: Nenhuma recompensa foi criada!\n";
        echo "   Possíveis causas:\n";
        echo "   - Observer não está registrado (verifique AppServiceProvider)\n";
        echo "   - Cliente não tem ID associado à comanda\n";
        echo "   - Erro silencioso no Observer (verifique logs)\n\n";
        
        echo "   🔍 Verificando logs...\n";
        $logPath = storage_path('logs/laravel.log');
        if (file_exists($logPath)) {
            $logs = file_get_contents($logPath);
            $recentLogs = substr($logs, -2000);
            if (strpos($recentLogs, 'Erro ao processar venda de produto') !== false) {
                echo "   ❌ Erro encontrado nos logs! Execute: tail -f storage/logs/laravel.log\n\n";
            }
        }
    }
} catch (\Exception $e) {
    echo "   ❌ ERRO ao verificar recompensa: " . $e->getMessage() . "\n\n";
}

// Passo 7: Verificar total_vendas incrementado
echo "📊 Passo 7: Verificando contador de vendas...\n";
try {
    $produto->refresh();
    echo "   ✅ Total de vendas do produto: {$produto->total_vendas}\n";
    
    if ($produto->total_vendas > 0) {
        echo "   ✅ Contador incrementado corretamente!\n\n";
    } else {
        echo "   ⚠️  Contador não foi incrementado (esperado: 1, obtido: 0)\n\n";
    }
} catch (\Exception $e) {
    echo "   ❌ ERRO: " . $e->getMessage() . "\n\n";
}

// Passo 8: Limpeza (opcional)
echo "🧹 Passo 8: Limpeza...\n";
echo "   Deseja remover os dados de teste? (s/N): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
fclose($handle);

if (trim(strtolower($line)) === 's') {
    try {
        if (isset($recompensa)) $recompensa->delete();
        if (isset($venda)) $venda->delete();
        if (isset($comanda)) $comanda->delete();
        if (isset($produto)) $produto->delete();
        echo "   ✅ Dados de teste removidos\n\n";
    } catch (\Exception $e) {
        echo "   ⚠️  Erro ao limpar: " . $e->getMessage() . "\n\n";
    }
} else {
    echo "   ℹ️  Dados de teste mantidos\n\n";
}

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  TESTE CONCLUÍDO!                                          ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

echo "📚 Próximos passos:\n";
echo "1. Acesse /proprietario/servicos/{id}/produtos para vincular produtos\n";
echo "2. Acesse /cliente/recompensas para ver as recompensas\n";
echo "3. Consulte SISTEMA_VENDA_PRODUTOS_FIDELIDADE.md para documentação completa\n\n";

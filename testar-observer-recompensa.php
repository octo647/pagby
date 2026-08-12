<?php

/**
 * Script para testar se o Observer está funcionando ao criar ComandaProduto
 * 
 * Uso: php testar-observer-recompensa.php
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Database\Models\Domain;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tenantDomain = 'labelle.localhost';

echo "\n🔍 TESTE DO OBSERVER DE RECOMPENSAS\n";
echo "====================================\n\n";

try {
    $domain = Domain::where('domain', $tenantDomain)->first();
    tenancy()->initialize($domain->tenant);
    
    echo "✅ Tenancy inicializado\n\n";
    
    // 1. Verificar se existem dados necessários
    echo "1️⃣ VERIFICANDO DADOS NECESSÁRIOS\n";
    
    $cliente = \App\Models\User::whereHas('roles', function($q) {
        $q->where('role', 'Cliente');
    })->first();
    
    if (!$cliente) {
        throw new Exception("Nenhum cliente encontrado");
    }
    echo "   ✅ Cliente: {$cliente->name} (ID: {$cliente->id})\n";
    
    $produto = \App\Models\Estoque::where('quantidade_atual', '>', 0)->first();
    if (!$produto) {
        throw new Exception("Nenhum produto em estoque encontrado");
    }
    echo "   ✅ Produto: {$produto->produto_nome} (R$ " . number_format($produto->preco_unitario, 2, ',', '.') . ")\n";
    
    $branch = \App\Models\Branch::first();
    if (!$branch) {
        throw new Exception("Nenhuma filial encontrada");
    }
    echo "   ✅ Filial: {$branch->branch_name}\n\n";
    
    // 2. Contar recompensas antes
    $recompensasAntes = \App\Models\FidelidadeReward::where('user_id', $cliente->id)->count();
    echo "2️⃣ RECOMPENSAS ANTES: {$recompensasAntes}\n\n";
    
    // 3. Criar uma comanda
    echo "3️⃣ CRIANDO COMANDA...\n";
    $comanda = \App\Models\Comanda::create([
        'branch_id' => $branch->id,
        'numero_comanda' => \App\Models\Comanda::gerarNumeroComanda($branch->id),
        'client_id' => $cliente->id,
        'cliente_nome' => $cliente->name,
        'cliente_telefone' => $cliente->phone,
        'funcionario_id' => \App\Models\User::whereHas('roles', function($q) {
            $q->where('role', 'Funcionário');
        })->first()?->id ?? 1,
        'status' => 'Aberta',
        'data_abertura' => now(),
    ]);
    
    echo "   Comanda criada: ID {$comanda->id}, Número: {$comanda->numero_comanda}\n\n";
    
    // 4. Adicionar produto à comanda (isso deve disparar o Observer)
    echo "4️⃣ ADICIONANDO PRODUTO À COMANDA...\n";
    echo "   Produto: {$produto->produto_nome}\n";
    echo "   Quantidade: 1\n";
    echo "   Preço unitário: R$ " . number_format($produto->preco_unitario, 2, ',', '.') . "\n";
    echo "   Subtotal: R$ " . number_format($produto->preco_unitario, 2, ',', '.') . "\n\n";
    
    $comandaProduto = \App\Models\ComandaProduto::create([
        'comanda_id' => $comanda->id,
        'estoque_id' => $produto->id,
        'quantidade' => 1,
        'preco_unitario' => $produto->preco_unitario,
        'subtotal' => $produto->preco_unitario,
    ]);
    
    echo "   ✅ ComandaProduto criado: ID {$comandaProduto->id}\n\n";
    
    // 5. Aguardar um pouco para garantir que o Observer foi executado
    sleep(1);
    
    // 6. Verificar se recompensa foi criada
    echo "5️⃣ VERIFICANDO SE RECOMPENSA FOI CRIADA...\n";
    
    $recompensasDepois = \App\Models\FidelidadeReward::where('user_id', $cliente->id)->get();
    echo "   Total de recompensas: {$recompensasDepois->count()}\n\n";
    
    if ($recompensasDepois->count() > $recompensasAntes) {
        $novaRecompensa = $recompensasDepois->last();
        
        echo "   ✅ OBSERVER FUNCIONOU!\n";
        echo "   Nova recompensa criada:\n";
        echo "      ID: {$novaRecompensa->id}\n";
        echo "      Tipo: {$novaRecompensa->tipo}\n";
        echo "      Valor: R$ " . number_format($novaRecompensa->valor_credito ?? 0, 2, ',', '.') . "\n";
        echo "      Origem: {$novaRecompensa->origem}\n";
        echo "      Validade: {$novaRecompensa->validade->format('d/m/Y')}\n";
        echo "      Status: {$novaRecompensa->status}\n";
        echo "      ComandaProduto ID: {$novaRecompensa->comanda_produto_id}\n\n";
        
        // Verificar percentual aplicado
        $percentual = ($novaRecompensa->valor_credito / $produto->preco_unitario) * 100;
        echo "      Percentual: " . number_format($percentual, 0) . "%\n";
        
        if ($produto->preco_unitario >= 100) {
            $esperado = 25;
        } elseif ($produto->preco_unitario >= 50) {
            $esperado = 20;
        } else {
            $esperado = 15;
        }
        
        if (round($percentual) == $esperado) {
            echo "      ✅ Percentual correto ({$esperado}%)!\n\n";
        } else {
            echo "      ⚠️ Percentual incorreto (esperado: {$esperado}%)\n\n";
        }
        
    } else {
        echo "   ❌ OBSERVER NÃO FUNCIONOU!\n";
        echo "   Recompensa não foi criada automaticamente\n\n";
        
        // Verificar se há erro no log
        echo "   📋 Verificando logs...\n";
        $logPath = storage_path('logs/laravel.log');
        if (file_exists($logPath)) {
            $logContent = file_get_contents($logPath);
            $lastLines = array_slice(explode("\n", $logContent), -50);
            $erros = array_filter($lastLines, function($line) {
                return stripos($line, 'ComandaProduto') !== false || 
                       stripos($line, 'Observer') !== false ||
                       stripos($line, 'FidelidadeReward') !== false;
            });
            
            if (!empty($erros)) {
                echo "   Últimas linhas relevantes do log:\n";
                foreach ($erros as $erro) {
                    echo "   " . trim($erro) . "\n";
                }
            } else {
                echo "   Nenhum erro encontrado nos logs recentes\n";
            }
        }
        echo "\n";
    }
    
    // 7. Limpar dados de teste
    echo "6️⃣ LIMPANDO DADOS DE TESTE...\n";
    $comandaProduto->delete();
    $comanda->delete();
    
    if ($recompensasDepois->count() > $recompensasAntes) {
        $novaRecompensa->delete();
        echo "   Recompensa de teste removida\n";
    }
    echo "   Comanda e produto removidos\n\n";
    
    echo "✅ TESTE CONCLUÍDO!\n\n";
    
} catch (Exception $e) {
    echo "\n❌ ERRO: {$e->getMessage()}\n";
    echo "Arquivo: {$e->getFile()}:{$e->getLine()}\n";
    echo "\nStack trace:\n{$e->getTraceAsString()}\n\n";
    exit(1);
}

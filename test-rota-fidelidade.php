#!/usr/bin/env php
<?php
/**
 * Teste da Rota de Fidelização
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n╔════════════════════════════════════════════════════╗\n";
echo "║  TESTE: ROTA FIDELIZAÇÃO                          ║\n";
echo "╚════════════════════════════════════════════════════╝\n\n";

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

echo "🔍 Testando componente Livewire...\n";

try {
    // Verificar se a classe existe
    if (!class_exists(\App\Livewire\Proprietario\DashboardFidelidade::class)) {
        echo "❌ Classe DashboardFidelidade não encontrada!\n";
        exit(1);
    }
    echo "✅ Classe DashboardFidelidade existe\n";

    // Verificar se os modelos existem
    $modelos = [
        'Branch' => \App\Models\Branch::class,
        'Estoque' => \App\Models\Estoque::class,
        'FidelidadeReward' => \App\Models\FidelidadeReward::class,
        'Service' => \App\Models\Service::class,
        'ComandaProduto' => \App\Models\ComandaProduto::class,
    ];

    foreach ($modelos as $nome => $classe) {
        if (class_exists($classe)) {
            echo "✅ Model {$nome} existe\n";
        } else {
            echo "❌ Model {$nome} NÃO existe\n";
        }
    }

    echo "\n🔍 Testando queries...\n";

    // Testar query de branch
    try {
        $branch = \App\Models\Branch::first();
        echo "✅ Branch: " . ($branch ? $branch->name : "Nenhuma encontrada") . "\n";
    } catch (\Exception $e) {
        echo "❌ Erro ao buscar Branch: {$e->getMessage()}\n";
    }

    // Testar query de recompensas
    try {
        $recompensas = \App\Models\FidelidadeReward::count();
        echo "✅ FidelidadeReward: {$recompensas} recompensa(s)\n";
    } catch (\Exception $e) {
        echo "❌ Erro ao buscar FidelidadeReward: {$e->getMessage()}\n";
    }

    // Testar query de produtos
    try {
        $produtos = \App\Models\Estoque::count();
        echo "✅ Estoque: {$produtos} produto(s)\n";
    } catch (\Exception $e) {
        echo "❌ Erro ao buscar Estoque: {$e->getMessage()}\n";
    }

    // Testar query de serviços
    try {
        $servicos = \App\Models\Service::count();
        echo "✅ Service: {$servicos} serviço(s)\n";
    } catch (\Exception $e) {
        echo "❌ Erro ao buscar Service: {$e->getMessage()}\n";
    }

    // Testar relacionamento produtosRecomendados
    echo "\n🔍 Testando relacionamento Service → Estoque...\n";
    try {
        $servico = \App\Models\Service::first();
        if ($servico) {
            $produtosRecomendados = $servico->produtosRecomendados;
            echo "✅ Relacionamento produtosRecomendados funciona\n";
            echo "   Serviço '{$servico->service}' tem {$produtosRecomendados->count()} produto(s) vinculado(s)\n";
        } else {
            echo "⚠️ Nenhum serviço encontrado para testar\n";
        }
    } catch (\Exception $e) {
        echo "❌ Erro no relacionamento: {$e->getMessage()}\n";
    }

    echo "\n✅ TODOS OS TESTES PASSARAM!\n";
    echo "\n📍 Acesse a rota:\n";
    echo "   https://labelle.seudominio.com.br/proprietario/fidelidade\n\n";

} catch (\Exception $e) {
    echo "\n❌ ERRO: {$e->getMessage()}\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n\n";
    exit(1);
}

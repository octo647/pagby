<?php

use Illuminate\Support\Facades\DB;

define('LARAVEL_START', microtime(true));

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tenant;
use App\Models\User;
use App\Models\Service;
use App\Models\Estoque;
use App\Models\FidelidadeReward;
use App\Models\ComandaProduto;

echo "\n" . str_repeat("=", 80) . "\n";
echo "TESTE DE INTEGRAÇÃO: FIDELIDADE + AGENDAMENTO\n";
echo str_repeat("=", 80) . "\n\n";

// Inicializar tenant Labelle
$tenant = Tenant::where('id', 'labelle')->first();

if (!$tenant) {
    echo "❌ Tenant Labelle não encontrado!\n";
    exit(1);
}

tenancy()->initialize($tenant);
echo "✅ Tenant inicializado: {$tenant->nome}\n\n";

// 1. Verificar Cliente com Recompensas
echo "📋 TESTE 1: RECOMPENSAS DO CLIENTE\n";
echo str_repeat("-", 80) . "\n";

$cliente = User::whereHas('roles', function($q) {
    $q->where('role', 'Cliente');
})->first();

if ($cliente) {
    echo "✅ Cliente encontrado: {$cliente->name}" . ($cliente->email ? " ({$cliente->email})" : "") . "\n";
    
    $recompensas = FidelidadeReward::where('user_id', $cliente->id)
        ->where('status', 'ativo')
        ->where('validade', '>=', now())
        ->get();
    
    echo "   Recompensas Ativas: " . $recompensas->count() . "\n";
    
    foreach ($recompensas as $reward) {
        echo "   - Tipo: {$reward->tipo}\n";
        if ($reward->tipo === 'credito') {
            echo "     Valor: R$ " . number_format($reward->valor_credito, 2, ',', '.') . "\n";
        } elseif ($reward->tipo === 'cupom') {
            echo "     Desconto: {$reward->percentual_desconto}%\n";
            echo "     Código: {$reward->codigo_cupom}\n";
        }
        echo "     Validade: " . $reward->validade->format('d/m/Y') . "\n";
        echo "     Origem: {$reward->origem}\n\n";
    }
} else {
    echo "⚠️  Nenhum cliente encontrado\n\n";
}

// 2. Verificar Serviços com Produtos Vinculados
echo "📋 TESTE 2: PRODUTOS VINCULADOS AOS SERVIÇOS\n";
echo str_repeat("-", 80) . "\n";

$servico = Service::with(['produtosRecomendados' => function($q) {
    $q->wherePivot('is_active', true)
      ->orderBy('priority', 'desc');
}])->first();

if ($servico) {
    echo "✅ Serviço: {$servico->service}\n";
    echo "   Produtos Vinculados Manualmente: " . $servico->produtosRecomendados->count() . "\n";
    
    foreach ($servico->produtosRecomendados as $produto) {
        echo "   - {$produto->produto_nome}\n";
        echo "     Preço: R$ " . number_format($produto->preco_unitario, 2, ',', '.') . "\n";
        echo "     Prioridade: {$produto->pivot->priority}\n";
        if ($produto->pivot->discount_percentage > 0) {
            echo "     Desconto Especial: {$produto->pivot->discount_percentage}%\n";
            $precoFinal = $produto->preco_unitario * (1 - $produto->pivot->discount_percentage / 100);
            echo "     Preço Final: R$ " . number_format($precoFinal, 2, ',', '.') . "\n";
        }
        echo "     Ganho de Crédito (20%): R$ " . number_format($produto->preco_unitario * 0.20, 2, ',', '.') . "\n";
        echo "\n";
    }
    
    // Testar método getProdutosSugeridos
    echo "   Testando getProdutosSugeridos():\n";
    $branchId = \App\Models\Branch::first()?->id ?? 1;
    $sugeridos = $servico->getProdutosSugeridos($branchId, 3);
    echo "   Produtos Retornados: " . $sugeridos->count() . "\n";
    
    foreach ($sugeridos as $prod) {
        $origem = isset($prod->pivot) && $prod->pivot->priority ? 'Manual' : 'Automático';
        echo "   - [{$origem}] {$prod->produto_nome}\n";
    }
    
} else {
    echo "⚠️  Nenhum serviço encontrado\n";
}

echo "\n";

// 3. Verificar Produtos Mais Vendidos
echo "📋 TESTE 3: PRODUTOS MAIS VENDIDOS\n";
echo str_repeat("-", 80) . "\n";

$produtosMaisVendidos = Estoque::where('total_vendas', '>', 0)
    ->orderBy('total_vendas', 'desc')
    ->limit(5)
    ->get();

echo "✅ Top 5 Produtos Mais Vendidos:\n";
foreach ($produtosMaisVendidos as $index => $produto) {
    $posicao = $index + 1;
    $medalha = match($posicao) {
        1 => '🥇',
        2 => '🥈',
        3 => '🥉',
        default => "#{$posicao}"
    };
    
    echo "   {$medalha} {$produto->produto_nome}\n";
    echo "      Vendas: {$produto->total_vendas}x\n";
    echo "      Preço: R$ " . number_format($produto->preco_unitario, 2, ',', '.') . "\n";
    echo "      Estoque: {$produto->quantidade_atual}\n";
    
    if ($produto->total_vendas > 0) {
        $receitaEstimada = $produto->total_vendas * $produto->preco_unitario;
        echo "      Receita Estimada: R$ " . number_format($receitaEstimada, 2, ',', '.') . "\n";
    }
    echo "\n";
}

// 4. Testar Cálculo de Recompensa
echo "📋 TESTE 4: SIMULAÇÃO DE COMPRA COM RECOMPENSA\n";
echo str_repeat("-", 80) . "\n";

$produtoTeste = Estoque::where('quantidade_atual', '>', 0)->first();

if ($produtoTeste && $cliente) {
    echo "✅ Simulando compra:\n";
    echo "   Cliente: {$cliente->name}\n";
    echo "   Produto: {$produtoTeste->produto_nome}\n";
    echo "   Valor: R$ " . number_format($produtoTeste->preco_unitario, 2, ',', '.') . "\n\n";
    
    // Calcular recompensa
    $valorCompra = $produtoTeste->preco_unitario;
    $percentual = $valorCompra > 100 ? 25 : ($valorCompra >= 50 ? 20 : 15);
    $valorRecompensa = $valorCompra * ($percentual / 100);
    
    echo "   💰 Recompensa que seria gerada:\n";
    echo "      Percentual: {$percentual}%\n";
    echo "      Valor em Crédito: R$ " . number_format($valorRecompensa, 2, ',', '.') . "\n";
    echo "      Validade: " . now()->addDays(90)->format('d/m/Y') . " (90 dias)\n";
    echo "      Status: Ativo\n\n";
    
    echo "   ✨ Impacto para o Salão:\n";
    echo "      Venda Imediata: R$ " . number_format($valorCompra, 2, ',', '.') . "\n";
    echo "      Crédito Concedido: R$ " . number_format($valorRecompensa, 2, ',', '.') . "\n";
    echo "      Incentivo para Retorno: Cliente tem crédito para usar\n";
    echo "      Fidelização: Cliente tende a voltar para não perder o crédito\n";
}

echo "\n";

// 5. Estatísticas Gerais
echo "📋 TESTE 5: ESTATÍSTICAS DO SISTEMA\n";
echo str_repeat("-", 80) . "\n";

$totalRecompensasAtivas = FidelidadeReward::where('status', 'ativo')
    ->where('validade', '>=', now())
    ->count();

$valorTotalCreditos = FidelidadeReward::where('status', 'ativo')
    ->where('tipo', 'credito')
    ->where('validade', '>=', now())
    ->sum('valor_credito');

$totalProdutosCadastrados = Estoque::count();
$totalProdutosComVendas = Estoque::where('total_vendas', '>', 0)->count();

$totalVinculosAtivos = \DB::table('service_estoque')
    ->where('is_active', true)
    ->count();

echo "✅ Resumo do Sistema de Fidelidade:\n";
echo "   📊 Recompensas Ativas: {$totalRecompensasAtivas}\n";
echo "   💰 Valor Total em Créditos: R$ " . number_format($valorTotalCreditos, 2, ',', '.') . "\n";
echo "   📦 Produtos Cadastrados: {$totalProdutosCadastrados}\n";
echo "   🛒 Produtos com Vendas: {$totalProdutosComVendas}\n";
echo "   🔗 Vínculos Produto-Serviço Ativos: {$totalVinculosAtivos}\n";

if ($totalProdutosCadastrados > 0) {
    $taxaConversao = ($totalProdutosComVendas / $totalProdutosCadastrados) * 100;
    echo "   📈 Taxa de Conversão de Produtos: " . number_format($taxaConversao, 1) . "%\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "✅ TODOS OS TESTES CONCLUÍDOS!\n";
echo str_repeat("=", 80) . "\n\n";

echo "📝 PRÓXIMOS PASSOS:\n";
echo "1. Acessar a tela de agendamento como cliente\n";
echo "2. Selecionar um profissional e um serviço\n";
echo "3. Verificar se aparecem:\n";
echo "   - Recompensas ativas do cliente (se autenticado)\n";
echo "   - Produtos recomendados para o serviço\n";
echo "4. Selecionar produtos e/ou recompensa\n";
echo "5. Completar o agendamento\n";
echo "6. Verificar se o Observer criou nova recompensa (20% dos produtos)\n\n";

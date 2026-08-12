<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use App\Models\Comanda;

// Usar tenant labelle.localhost
$tenant = Tenant::whereHas('domains', function($q) {
    $q->where('domain', 'labelle.localhost');
})->firstOrFail();

tenancy()->initialize($tenant);

echo str_repeat('=', 70) . "\n";
echo "🔍 PROCURANDO COMANDAS COM PRODUTOS MAS TOTAIS INCORRETOS\n";
echo str_repeat('=', 70) . "\n\n";

// Buscar comandas que têm produtos mas subtotal_produtos = 0
$comandas = Comanda::whereHas('comandaProdutos')
    ->where('subtotal_produtos', 0)
    ->latest()
    ->limit(10)
    ->get();

echo "📊 Comandas encontradas: " . $comandas->count() . "\n\n";

if ($comandas->count() > 0) {
    foreach ($comandas as $c) {
        echo "─────────────────────────────────────────\n";
        echo "📋 Comanda: #{$c->numero_comanda}\n";
        echo "👤 Cliente: {$c->cliente_nome}\n";
        echo "📅 Data: " . $c->data_abertura->format('d/m/Y H:i') . "\n\n";
        
        echo "VALORES ATUAIS (INCORRETOS):\n";
        echo "  Subtotal Serviços: R$ " . number_format($c->subtotal_servicos ?? 0, 2, ',', '.') . "\n";
        echo "  Subtotal Produtos: R$ " . number_format($c->subtotal_produtos ?? 0, 2, ',', '.') . "\n";
        echo "  Total Geral: R$ " . number_format($c->total_geral ?? 0, 2, ',', '.') . "\n\n";
        
        echo "PRODUTOS NA COMANDA:\n";
        $somaProdutos = 0;
        foreach ($c->comandaProdutos as $p) {
            $nomeProduto = $p->estoque->produto_nome ?? 'N/A';
            echo "  - {$nomeProduto}: R$ " . number_format($p->subtotal, 2, ',', '.') . "\n";
            $somaProdutos += $p->subtotal;
        }
        
        $totalCorreto = ($c->subtotal_servicos ?? 0) + $somaProdutos;
        
        echo "\nVALORES CORRETOS:\n";
        echo "  Subtotal Produtos: R$ " . number_format($somaProdutos, 2, ',', '.') . "\n";
        echo "  Total Geral: R$ " . number_format($totalCorreto, 2, ',', '.') . "\n";
        echo "  ⚠️ DIFERENÇA: R$ " . number_format($somaProdutos, 2, ',', '.') . " não contabilizado!\n\n";
    }
    
    echo str_repeat('=', 70) . "\n";
    echo "🔧 CORRIGINDO TODAS AS COMANDAS...\n";
    echo str_repeat('=', 70) . "\n\n";
    
    $corrigidas = 0;
    foreach ($comandas as $c) {
        echo "Recalculando comanda #{$c->numero_comanda}... ";
        $c->recalcularTotais();
        $corrigidas++;
        echo "✅\n";
    }
    
    echo "\n✅ {$corrigidas} comandas foram corrigidas!\n";
    echo "📌 A partir de agora, novos produtos serão incluídos automaticamente.\n";
} else {
    echo "✅ Nenhuma comanda com totais incorretos encontrada!\n";
    echo "Todas as comandas estão com os valores corretos.\n";
}

echo "\n" . str_repeat('=', 70) . "\n";

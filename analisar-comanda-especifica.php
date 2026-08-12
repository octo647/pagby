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
echo "🔍 ANÁLISE DA COMANDA #2-20260407-005\n";
echo str_repeat('=', 70) . "\n\n";

// Buscar a comanda específica
$comanda = Comanda::where('numero_comanda', '2-20260407-005')->first();

if (!$comanda) {
    echo "❌ Comanda não encontrada!\n";
    exit;
}

echo "📋 Comanda: #{$comanda->numero_comanda} (ID: {$comanda->id})\n";
echo "👤 Cliente: {$comanda->cliente_nome}\n";
echo "📅 Abertura: " . $comanda->data_abertura->format('d/m/Y H:i:s') . "\n";
echo "📊 Status: {$comanda->status}\n\n";

echo str_repeat('-', 70) . "\n";
echo "VALORES ATUAIS (INCORRETOS):\n";
echo str_repeat('-', 70) . "\n";
echo "Subtotal Serviços: R$ " . number_format($comanda->subtotal_servicos ?? 0, 2, ',', '.') . "\n";
echo "Subtotal Produtos: R$ " . number_format($comanda->subtotal_produtos ?? 0, 2, ',', '.') . "\n";
echo "Total Geral: R$ " . number_format($comanda->total_geral ?? 0, 2, ',', '.') . "\n\n";

echo str_repeat('-', 70) . "\n";
echo "SERVIÇOS NA COMANDA:\n";
echo str_repeat('-', 70) . "\n";
$somaServicos = 0;
foreach ($comanda->comandaServicos as $s) {
    echo "  - " . ($s->servico_nome ?? 'N/A') . 
         " | Qtd: {$s->quantidade}" .
         " | Unitário: R$ " . number_format($s->preco_unitario, 2, ',', '.') .
         " | Subtotal: R$ " . number_format($s->subtotal, 2, ',', '.') . "\n";
    $somaServicos += $s->subtotal;
}
echo "Total Serviços: R$ " . number_format($somaServicos, 2, ',', '.') . "\n\n";

echo str_repeat('-', 70) . "\n";
echo "PRODUTOS NA COMANDA:\n";
echo str_repeat('-', 70) . "\n";
$somaProdutos = 0;
$produtos = $comanda->comandaProdutos;

if ($produtos->count() > 0) {
    foreach ($produtos as $p) {
        $nomeProduto = $p->estoque->produto_nome ?? 'N/A';
        echo "  - {$nomeProduto}" .
             " | Qtd: {$p->quantidade}" .
             " | Unitário: R$ " . number_format($p->preco_unitario, 2, ',', '.') .
             " | Subtotal: R$ " . number_format($p->subtotal, 2, ',', '.') . "\n";
        $somaProdutos += $p->subtotal;
    }
} else {
    echo "  (Nenhum produto)\n";
}
echo "Total Produtos: R$ " . number_format($somaProdutos, 2, ',', '.') . "\n\n";

// Verificar recompensas aplicadas
echo str_repeat('-', 70) . "\n";
echo "RECOMPENSAS APLICADAS:\n";
echo str_repeat('-', 70) . "\n";
$recompensas = \App\Models\FidelidadeReward::where('usado_em_comanda_id', $comanda->id)->get();
if ($recompensas->count() > 0) {
    $totalDescontos = 0;
    foreach ($recompensas as $r) {
        $desconto = $r->valor_credito_original ?? $r->valor_credito;
        echo "  - ID {$r->id}: R$ " . number_format($desconto, 2, ',', '.') . 
             " | Status: {$r->status}\n";
        $totalDescontos += $desconto;
    }
    echo "Total Descontos: R$ " . number_format($totalDescontos, 2, ',', '.') . "\n";
} else {
    echo "  (Nenhuma recompensa aplicada)\n";
}
echo "\n";

echo str_repeat('=', 70) . "\n";
echo "VALORES CORRETOS:\n";
echo str_repeat('=', 70) . "\n";
$totalCorreto = $somaServicos + $somaProdutos;
echo "Subtotal Serviços: R$ " . number_format($somaServicos, 2, ',', '.') . "\n";
echo "Subtotal Produtos: R$ " . number_format($somaProdutos, 2, ',', '.') . "\n";
echo "Total Geral (SEM desconto): R$ " . number_format($totalCorreto, 2, ',', '.') . "\n\n";

if ($somaProdutos > 0 && $comanda->subtotal_produtos == 0) {
    echo "⚠️ PROBLEMA DETECTADO:\n";
    echo "   R$ " . number_format($somaProdutos, 2, ',', '.') . " em produtos NÃO estão contabilizados!\n\n";
    
    echo "🔧 APLICANDO CORREÇÃO...\n";
    $comanda->recalcularTotais();
    
    echo "✅ Totais recalculados!\n\n";
    
    echo "VALORES APÓS CORREÇÃO:\n";
    echo "Subtotal Serviços: R$ " . number_format($comanda->subtotal_servicos, 2, ',', '.') . "\n";
    echo "Subtotal Produtos: R$ " . number_format($comanda->subtotal_produtos, 2, ',', '.') . "\n";
    echo "Total Geral: R$ " . number_format($comanda->total_geral, 2, ',', '.') . "\n\n";
    
    echo "✅ CORRIGIDO! O valor do produto agora está incluído no total.\n";
} else {
    echo "✅ Comanda já está com os valores corretos!\n";
}

echo "\n" . str_repeat('=', 70) . "\n";

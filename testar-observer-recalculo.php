<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use App\Models\Comanda;
use App\Models\ComandaProduto;
use App\Models\Estoque;
use App\Models\User;

// Usar tenant labelle.localhost
$tenant = Tenant::whereHas('domains', function($q) {
    $q->where('domain', 'labelle.localhost');
})->firstOrFail();

tenancy()->initialize($tenant);

echo str_repeat('=', 70) . "\n";
echo "🧪 TESTE: OBSERVER RECALCULA AUTOMATICAMENTE\n";
echo str_repeat('=', 70) . "\n\n";

// Buscar dados (usar qualquer usuário, sem filtrar por role)
$cliente = User::first();
$funcionario = User::skip(1)->first() ?? $cliente; // Segundo usuário ou o mesmo

$produto = Estoque::where('quantidade_atual', '>', 0)
    ->where('preco_unitario', '>', 0)
    ->first();

if (!$cliente || !$funcionario || !$produto) {
    echo "❌ Dados insuficientes para teste\n";
    echo "Cliente: " . ($cliente ? "OK" : "FALTA") . "\n";
    echo "Funcionário: " . ($funcionario ? "OK" : "FALTA") . "\n";
    echo "Produto: " . ($produto ? "OK" : "FALTA") . "\n";
    exit;
}

echo "📝 SETUP DO TESTE\n";
echo "Cliente: {$cliente->name}\n";
echo "Funcionário: {$funcionario->name}\n";
echo "Produto: {$produto->produto_nome}\n";
echo "Preço do produto: R$ " . number_format($produto->preco_unitario, 2, ',', '.') . "\n\n";

// Criar comanda
echo "1️⃣ Criando comanda...\n";
$comanda = Comanda::create([
    'branch_id' => $funcionario->branches->first()->id ?? 1,
    'numero_comanda' => Comanda::gerarNumeroComanda($funcionario->branches->first()->id ?? 1),
    'client_id' => $cliente->id,
    'cliente_nome' => $cliente->name,
    'cliente_telefone' => $cliente->phone ?? null,
    'funcionario_id' => $funcionario->id,
    'status' => 'Aberta',
    'data_abertura' => now(),
    'subtotal_servicos' => 0,
    'subtotal_produtos' => 0,
    'total_geral' => 0,
]);

echo "✅ Comanda #{$comanda->numero_comanda} criada\n";
echo "   Subtotal Produtos: R$ 0,00\n";
echo "   Total Geral: R$ 0,00\n\n";

// Adicionar produto (Observer deve disparar)
echo "2️⃣ Adicionando produto (Observer deve disparar)...\n";

$comandaProduto = ComandaProduto::create([
    'comanda_id' => $comanda->id,
    'estoque_id' => $produto->id,
    'quantidade' => 1,
    'preco_unitario' => $produto->preco_unitario,
    'subtotal' => $produto->preco_unitario,
]);

echo "✅ Produto adicionado (ID: {$comandaProduto->id})\n\n";

// Verificar se Observer recalculou (SEM refresh forçado)
echo "3️⃣ Verificando se Observer recalculou automaticamente...\n";
$comanda->refresh(); // Apenas reload do banco

echo "\n--- RESULTADO ---\n";
echo "Subtotal Produtos: R$ " . number_format($comanda->subtotal_produtos ?? 0, 2, ',', '.') . "\n";
echo "Total Geral: R$ " . number_format($comanda->total_geral ?? 0, 2, ',', '.') . "\n";
echo "Valor Esperado: R$ " . number_format($produto->preco_unitario, 2, ',', '.') . "\n\n";

// Validação
$valorEsperado = $produto->preco_unitario;
$diferenca = abs($comanda->total_geral - $valorEsperado);

if ($diferenca < 0.01) {
    echo "✅ SUCESSO! Observer recalculou os totais automaticamente!\n";
    echo "✅ O produto foi incluído no total_geral da comanda.\n";
    echo "✅ Correção funcionando perfeitamente!\n\n";
    $sucesso = true;
} else {
    echo "❌ FALHA! Observer NÃO recalculou automaticamente.\n";
    echo "Diferença: R$ " . number_format($diferenca, 2, ',', '.') . "\n\n";
    $sucesso = false;
}

// Cleanup
echo "🧹 Limpando dados de teste...\n";

// Verificar se gerou recompensa
$recompensas = \App\Models\FidelidadeReward::where('user_id', $cliente->id)
    ->where('origem', 'compra_produto')
    ->whereNull('usado_em_comanda_id')
    ->latest()
    ->first();

if ($recompensas) {
    echo "   ℹ️ Recompensa gerada (ID: {$recompensas->id}) - será deletada\n";
    $recompensas->delete();
}

$comandaProduto->delete();
$comanda->delete();
echo "✅ Limpeza concluída\n\n";

echo str_repeat('=', 70) . "\n";
if ($sucesso) {
    echo "🎉 CORREÇÃO VALIDADA: Observer funciona corretamente!\n";
    echo "📌 PRÓXIMO PASSO: Criar novo agendamento e testar o fluxo completo\n";
} else {
    echo "⚠️ ATENÇÃO: Observer não está recalculando automaticamente\n";
    echo "Verifique se AppServiceProvider está registrando o Observer!\n";
}
echo str_repeat('=', 70) . "\n";

<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use App\Models\Appointment;
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
echo "🧪 TESTE: PRODUTOS NA COMANDA\n";
echo str_repeat('=', 70) . "\n\n";

// Verificar uma comanda recente com produtos
$comanda = Comanda::whereHas('comandaProdutos')
    ->latest()
    ->first();

if ($comanda) {
    echo "📋 COMANDA: #{$comanda->numero_comanda} (ID: {$comanda->id})\n";
    echo "📅 Abertura: " . $comanda->data_abertura->format('d/m/Y H:i') . "\n";
    echo "👤 Cliente: " . $comanda->cliente_nome . "\n\n";
    
    echo "--- VALORES ANTES DO TESTE ---\n";
    echo "Subtotal Serviços: R$ " . number_format($comanda->subtotal_servicos ?? 0, 2, ',', '.') . "\n";
    echo "Subtotal Produtos: R$ " . number_format($comanda->subtotal_produtos ?? 0, 2, ',', '.') . "\n";
    echo "Total Geral: R$ " . number_format($comanda->total_geral ?? 0, 2, ',', '.') . "\n\n";
    
    // Listar produtos
    $produtos = $comanda->comandaProdutos;
    echo "🛍️ Produtos na Comanda: " . $produtos->count() . "\n";
    foreach ($produtos as $prod) {
        echo "  - " . ($prod->estoque->produto_nome ?? 'N/A') . 
             " | Qtd: {$prod->quantidade}" .
             " | Unitário: R$ " . number_format($prod->preco_unitario, 2, ',', '.') .
             " | Subtotal: R$ " . number_format($prod->subtotal, 2, ',', '.') . "\n";
    }
    echo "\n";
    
    // Listar serviços
    $servicos = $comanda->comandaServicos;
    echo "✂️ Serviços na Comanda: " . $servicos->count() . "\n";
    foreach ($servicos as $serv) {
        echo "  - " . ($serv->servico_nome ?? 'N/A') .
             " | Qtd: {$serv->quantidade}" .
             " | Unitário: R$ " . number_format($serv->preco_unitario, 2, ',', '.') .
             " | Subtotal: R$ " . number_format($serv->subtotal, 2, ',', '.') . "\n";
    }
    echo "\n";
    
    // Forçar recálculo
    echo "🔄 Forçando recálculo dos totais...\n";
    $comanda->recalcularTotais();
    $comanda->refresh();
    
    echo "\n--- VALORES APÓS RECÁLCULO ---\n";
    echo "Subtotal Serviços: R$ " . number_format($comanda->subtotal_servicos ?? 0, 2, ',', '.') . "\n";
    echo "Subtotal Produtos: R$ " . number_format($comanda->subtotal_produtos ?? 0, 2, ',', '.') . "\n";
    echo "Total Geral: R$ " . number_format($comanda->total_geral ?? 0, 2, ',', '.') . "\n\n";
    
    // Validar cálculo manual
    $somaServicos = $servicos->sum('subtotal');
    $somaProdutos = $produtos->sum('subtotal');
    $totalManual = $somaServicos + $somaProdutos;
    
    echo "--- VALIDAÇÃO MANUAL ---\n";
    echo "Soma Serviços: R$ " . number_format($somaServicos, 2, ',', '.') . "\n";
    echo "Soma Produtos: R$ " . number_format($somaProdutos, 2, ',', '.') . "\n";
    echo "Total Esperado: R$ " . number_format($totalManual, 2, ',', '.') . "\n\n";
    
    if (abs($comanda->total_geral - $totalManual) < 0.01) {
        echo "✅ TOTAIS CORRETOS! Produtos estão incluídos no total_geral\n";
    } else {
        echo "❌ ERRO! Total diverge do esperado\n";
        echo "Divergência: R$ " . number_format(abs($comanda->total_geral - $totalManual), 2, ',', '.') . "\n";
    }
} else {
    echo "⚠️ Nenhuma comanda com produtos encontrada.\n";
    echo "Vou criar um caso de teste...\n\n";
    
    // Buscar dados para criar um teste
    $cliente = User::whereHas('roles', function($q) {
        $q->where('name', 'Cliente');
    })->first();
    
    $funcionario = User::whereHas('roles', function($q) {
        $q->where('name', 'Funcionário');
    })->first();
    
    $produto = Estoque::where('quantidade_atual', '>', 0)->first();
    
    if ($cliente && $funcionario && $produto) {
        echo "📝 Criando comanda de teste...\n";
        echo "Cliente: {$cliente->name}\n";
        echo "Funcionário: {$funcionario->name}\n";
        echo "Produto: {$produto->produto_nome} - R$ " . number_format($produto->preco_unitario, 2, ',', '.') . "\n\n";
        
        // Criar comanda manualmente
        $comanda = Comanda::create([
            'branch_id' => $funcionario->branches->first()->id ?? 1,
            'numero_comanda' => Comanda::gerarNumeroComanda($funcionario->branches->first()->id ?? 1),
            'client_id' => $cliente->id,
            'cliente_nome' => $cliente->name,
            'cliente_telefone' => $cliente->phone ?? null,
            'funcionario_id' => $funcionario->id,
            'status' => 'Aberta',
            'data_abertura' => now(),
        ]);
        
        echo "✅ Comanda criada: #{$comanda->numero_comanda}\n";
        echo "Total antes de adicionar produto: R$ " . number_format($comanda->total_geral ?? 0, 2, ',', '.') . "\n\n";
        
        // Adicionar produto (vai disparar Observer)
        echo "➕ Adicionando produto...\n";
        $comandaProduto = ComandaProduto::create([
            'comanda_id' => $comanda->id,
            'estoque_id' => $produto->id,
            'quantidade' => 1,
            'preco_unitario' => $produto->preco_unitario,
            'subtotal' => $produto->preco_unitario,
        ]);
        
        echo "✅ Produto adicionado (Observer deve ter recalculado)\n\n";
        
        // Verificar se totais foram atualizados
        $comanda->refresh();
        
        echo "--- VALORES APÓS ADICIONAR PRODUTO ---\n";
        echo "Subtotal Produtos: R$ " . number_format($comanda->subtotal_produtos ?? 0, 2, ',', '.') . "\n";
        echo "Total Geral: R$ " . number_format($comanda->total_geral ?? 0, 2, ',', '.') . "\n\n";
        
        if ($comanda->subtotal_produtos == $produto->preco_unitario) {
            echo "✅ SUCESSO! Observer recalculou os totais automaticamente!\n";
            echo "O produto foi incluído no total_geral da comanda.\n";
        } else {
            echo "❌ FALHA! Observer não recalculou os totais.\n";
            echo "Esperado: R$ " . number_format($produto->preco_unitario, 2, ',', '.') . "\n";
            echo "Obtido: R$ " . number_format($comanda->subtotal_produtos ?? 0, 2, ',', '.') . "\n";
        }
        
        // Cleanup
        echo "\n🧹 Limpando dados de teste...\n";
        $comandaProduto->delete();
        $comanda->delete();
        echo "✅ Limpeza concluída.\n";
    } else {
        echo "❌ Dados insuficientes para criar teste.\n";
    }
}

echo "\n" . str_repeat('=', 70) . "\n";
echo "🎯 PRÓXIMO PASSO: Testar com um agendamento real\n";
echo str_repeat('=', 70) . "\n";

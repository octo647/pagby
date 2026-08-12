#!/usr/bin/env php
<?php
/**
 * Teste LABELLE - Sistema de Fidelização
 * 
 * Execute: php teste-labelle-fidelidade.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  TESTE LABELLE - SISTEMA DE FIDELIZAÇÃO                   ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// Conectar ao tenant Labelle
echo "🔍 Buscando tenant Labelle...\n";
try {
    $tenant = \App\Models\Tenant::where('id', 'like', '%labelle%')
        ->orWhereHas('domains', function($q) {
            $q->where('domain', 'like', '%labelle%');
        })
        ->first();
    
    // Se não encontrou, buscar por nome
    if (!$tenant) {
        $tenant = \App\Models\Tenant::where('name', 'like', '%labelle%')->first();
    }
    
    if (!$tenant) {
        echo "\n❌ Tenant Labelle não encontrado!\n\n";
        echo "Tenants disponíveis:\n";
        $tenants = \App\Models\Tenant::all(['id', 'name']);
        foreach($tenants as $t) {
            echo "  - {$t->name} (ID: {$t->id})\n";
        }
        echo "\n";
        die();
    }
    
    tenancy()->initialize($tenant);
    
    $database = DB::connection()->getDatabaseName();
    echo "   ✅ Conectado ao Labelle!\n";
    echo "   🏢 Nome: {$tenant->name}\n";
    echo "   🆔 ID: {$tenant->id}\n";
    echo "   📊 Banco: {$database}\n\n";
} catch (\Exception $e) {
    die("   ❌ ERRO: " . $e->getMessage() . "\n\n");
}

// Menu de opções
echo "Escolha uma opção:\n";
echo "1. Testar venda com geração de recompensa\n";
echo "2. Listar produtos do estoque\n";
echo "3. Listar serviços\n";
echo "4. Vincular produto a serviço\n";
echo "5. Ver recompensas de clientes\n";
echo "6. Testar sugestões de produtos para serviço\n";
echo "7. Ver estatísticas gerais\n";
echo "0. Sair\n\n";

echo "Opção: ";
$handle = fopen("php://stdin", "r");
$opcao = trim(fgets($handle));

switch($opcao) {
    case '1':
        testarVenda();
        break;
    case '2':
        listarProdutos();
        break;
    case '3':
        listarServicos();
        break;
    case '4':
        vincularProdutoServico();
        break;
    case '5':
        verRecompensas();
        break;
    case '6':
        testarSugestoes();
        break;
    case '7':
        verEstatisticas();
        break;
    default:
        echo "Saindo...\n";
}

fclose($handle);

// ============================================================================
// FUNÇÕES
// ============================================================================

function testarVenda() {
    echo "\n╔══════════════════════════════════════════════════════╗\n";
    echo "║  TESTE: VENDA COM GERAÇÃO DE RECOMPENSA             ║\n";
    echo "╚══════════════════════════════════════════════════════╝\n\n";
    
    try {
        echo "🔍 Buscando dados necessários...\n\n";
        
        $branch = \App\Models\Branch::first();
        echo "   Filial: " . ($branch ? "✅ {$branch->name}" : "❌ Não encontrada") . "\n";
        
        $funcionario = \App\Models\User::whereHas('roles', function($q) {
            $q->where('role', 'Funcionário');
        })->first();
        echo "   Funcionário: " . ($funcionario ? "✅ {$funcionario->name}" : "❌ Não encontrado") . "\n";
        
        $cliente = \App\Models\User::whereHas('roles', function($q) {
            $q->where('role', 'Cliente');
        })->first();
        echo "   Cliente: " . ($cliente ? "✅ {$cliente->name}" : "❌ Não encontrado") . "\n\n";
        
        if (!$branch || !$funcionario || !$cliente) {
            echo "❌ Erro: Dados básicos não encontrados\n";
            echo "Execute primeiro: php diagnostico-labelle.php\n\n";
            return;
        }
        
        $produto = \App\Models\Estoque::where('quantidade_atual', '>', 0)->first();
        
        if (!$produto) {
            // Criar produto de teste
            echo "📦 Criando produto de teste...\n";
            $produto = \App\Models\Estoque::create([
                'branch_id' => $branch->id,
                'produto_nome' => 'Produto Teste Fidelização ' . date('H:i:s'),
                'categoria' => 'Teste',
                'preco_unitario' => 75.00,
                'quantidade_atual' => 10,
                'quantidade_minima' => 2,
                'total_vendas' => 0,
            ]);
            echo "   ✅ Produto criado: {$produto->produto_nome}\n\n";
        }
        
        echo "📝 Criando comanda...\n";
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
        
        echo "💳 Registrando venda...\n";
        $venda = \App\Models\ComandaProduto::create([
            'comanda_id' => $comanda->id,
            'estoque_id' => $produto->id,
            'quantidade' => 1,
            'preco_unitario' => $produto->preco_unitario,
            'subtotal' => $produto->preco_unitario,
        ]);
        echo "   ✅ Venda de R$ " . number_format($venda->subtotal, 2, ',', '.') . " registrada\n\n";
        
        sleep(1);
        
        echo "🎁 Verificando recompensa gerada...\n";
        $recompensa = \App\Models\FidelidadeReward::where('comanda_produto_id', $venda->id)->first();
        
        if ($recompensa) {
            echo "   ✅ SUCESSO! Recompensa criada automaticamente!\n\n";
            echo "   ╔═══════════════════════════════════════════════╗\n";
            echo "   ║  Cliente: " . str_pad($cliente->name, 32) . "║\n";
            echo "   ║  Tipo: " . str_pad(ucfirst($recompensa->tipo), 35) . "║\n";
            
            if ($recompensa->tipo === 'credito') {
                echo "   ║  Crédito: R$ " . str_pad(number_format($recompensa->valor_credito, 2, ',', '.'), 28) . "║\n";
            }
            
            echo "   ║  Válido até: " . str_pad($recompensa->validade->format('d/m/Y'), 28) . "║\n";
            echo "   ╚═══════════════════════════════════════════════╝\n\n";
        } else {
            echo "   ⚠️ ATENÇÃO: Recompensa não foi criada!\n";
            echo "   Verifique se o Observer está registrado.\n\n";
        }
        
        $produto->refresh();
        echo "📊 Total vendas do produto: {$produto->total_vendas}\n\n";
        
    } catch (\Exception $e) {
        echo "❌ ERRO: " . $e->getMessage() . "\n\n";
    }
}

function listarProdutos() {
    echo "\n📦 PRODUTOS EM ESTOQUE\n";
    echo "════════════════════════════════════════════════════\n";
    
    $produtos = \App\Models\Estoque::orderBy('total_vendas', 'desc')->get();
    
    if ($produtos->isEmpty()) {
        echo "Nenhum produto cadastrado.\n\n";
        return;
    }
    
    foreach($produtos as $p) {
        echo "\n{$p->produto_nome}\n";
        echo "  💰 R$ " . number_format($p->preco_unitario, 2, ',', '.') . "\n";
        echo "  📦 Estoque: {$p->quantidade_atual}\n";
        echo "  📊 Vendas: {$p->total_vendas}\n";
        echo "  🏷️ Categoria: " . ($p->categoria ?: 'N/A') . "\n";
    }
    echo "\n";
}

function listarServicos() {
    echo "\n✂️ SERVIÇOS CADASTRADOS\n";
    echo "════════════════════════════════════════════════════\n";
    
    $servicos = \App\Models\Service::all();
    
    if ($servicos->isEmpty()) {
        echo "Nenhum serviço cadastrado.\n\n";
        return;
    }
    
    foreach($servicos as $s) {
        echo "\n{$s->service} (ID: {$s->id})\n";
        echo "  💰 R$ " . number_format($s->price, 2, ',', '.') . "\n";
        echo "  ⏱️ {$s->time} minutos\n";
        
        $vinculados = DB::table('service_estoque')
            ->where('service_id', $s->id)
            ->count();
        echo "  🔗 {$vinculados} produtos vinculados\n";
    }
    echo "\n";
}

function vincularProdutoServico() {
    echo "\n🔗 VINCULAR PRODUTO A SERVIÇO\n";
    echo "════════════════════════════════════════════════════\n\n";
    
    $servicos = \App\Models\Service::all();
    $produtos = \App\Models\Estoque::all();
    
    if ($servicos->isEmpty() || $produtos->isEmpty()) {
        echo "❌ Cadastre serviços e produtos primeiro.\n\n";
        return;
    }
    
    echo "Serviços disponíveis:\n";
    foreach($servicos as $i => $s) {
        echo ($i+1) . ". {$s->service}\n";
    }
    
    echo "\nEscolha o serviço (número): ";
    global $handle;
    $escolhaServico = (int)trim(fgets($handle)) - 1;
    
    if (!isset($servicos[$escolhaServico])) {
        echo "❌ Opção inválida\n\n";
        return;
    }
    
    $servico = $servicos[$escolhaServico];
    
    echo "\nProdutos disponíveis:\n";
    foreach($produtos as $i => $p) {
        echo ($i+1) . ". {$p->produto_nome} - R$ " . number_format($p->preco_unitario, 2, ',', '.') . "\n";
    }
    
    echo "\nEscolha o produto (número): ";
    $escolhaProduto = (int)trim(fgets($handle)) - 1;
    
    if (!isset($produtos[$escolhaProduto])) {
        echo "❌ Opção inválida\n\n";
        return;
    }
    
    $produto = $produtos[$escolhaProduto];
    
    echo "Desconto especial (0-100%): ";
    $desconto = (float)trim(fgets($handle));
    
    try {
        // Verificar se já existe
        $existe = DB::table('service_estoque')
            ->where('service_id', $servico->id)
            ->where('estoque_id', $produto->id)
            ->exists();
        
        if ($existe) {
            echo "\n⚠️ Este produto já está vinculado a este serviço.\n\n";
            return;
        }
        
        DB::table('service_estoque')->insert([
            'service_id' => $servico->id,
            'estoque_id' => $produto->id,
            'priority' => 1,
            'discount_percentage' => $desconto,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        echo "\n✅ Produto '{$produto->produto_nome}' vinculado ao serviço '{$servico->service}'";
        if ($desconto > 0) {
            echo " com {$desconto}% de desconto";
        }
        echo "!\n\n";
        
    } catch (\Exception $e) {
        echo "\n❌ ERRO: " . $e->getMessage() . "\n\n";
    }
}

function verRecompensas() {
    echo "\n🎁 RECOMPENSAS DOS CLIENTES\n";
    echo "════════════════════════════════════════════════════\n";
    
    $clientes = \App\Models\User::whereHas('roles', function($q) {
        $q->where('role', 'Cliente');
    })->get();
    
    foreach($clientes as $cliente) {
        $recompensas = \App\Models\FidelidadeReward::where('user_id', $cliente->id)
            ->ativos()
            ->get();
        
        $saldo = \App\Models\FidelidadeReward::saldoCreditosCliente($cliente->id);
        
        echo "\n👤 {$cliente->name}\n";
        echo "   💰 Saldo: R$ " . number_format($saldo, 2, ',', '.') . "\n";
        echo "   🎁 Recompensas ativas: {$recompensas->count()}\n";
        
        if ($recompensas->isNotEmpty()) {
            foreach($recompensas as $r) {
                echo "      - {$r->descricao_formatada} (expira em {$r->validade->format('d/m/Y')})\n";
            }
        }
    }
    echo "\n";
}

function testarSugestoes() {
    echo "\n💡 TESTAR SUGESTÕES DE PRODUTOS\n";
    echo "════════════════════════════════════════════════════\n\n";
    
    $servicos = \App\Models\Service::all();
    
    if ($servicos->isEmpty()) {
        echo "❌ Nenhum serviço cadastrado.\n\n";
        return;
    }
    
    $branch = \App\Models\Branch::first();
    
    foreach($servicos as $s) {
        echo "✂️ {$s->service}\n";
        
        $sugeridos = $s->getProdutosSugeridos($branch->id, 3);
        
        if ($sugeridos->isEmpty()) {
            echo "   ⚠️ Nenhum produto sugerido\n";
        } else {
            $temManuais = $s->temProdutosRecomendadosManuais($branch->id);
            echo "   " . ($temManuais ? "🔗 Manual" : "📊 Automático") . "\n";
            
            foreach($sugeridos as $p) {
                $preco = $p->preco_unitario;
                $desconto = $p->pivot->discount_percentage ?? 0;
                
                echo "      {$p->produto_nome} - R$ " . number_format($preco, 2, ',', '.');
                if ($desconto > 0) {
                    echo " (-{$desconto}% OFF)";
                }
                echo " [{$p->total_vendas} vendas]\n";
            }
        }
        echo "\n";
    }
}

function verEstatisticas() {
    echo "\n📊 ESTATÍSTICAS GERAIS\n";
    echo "════════════════════════════════════════════════════\n\n";
    
    $totalProdutos = \App\Models\Estoque::count();
    $totalServicos = \App\Models\Service::count();
    $totalVinculos = DB::table('service_estoque')->count();
    $totalRecompensas = \App\Models\FidelidadeReward::count();
    $recompensasAtivas = \App\Models\FidelidadeReward::where('status', 'ativo')->count();
    $totalCreditos = \App\Models\FidelidadeReward::where('tipo', 'credito')
        ->where('status', 'ativo')
        ->sum('valor_credito');
    
    echo "📦 Produtos cadastrados: {$totalProdutos}\n";
    echo "✂️ Serviços cadastrados: {$totalServicos}\n";
    echo "🔗 Vínculos Produto-Serviço: {$totalVinculos}\n";
    echo "🎁 Total recompensas: {$totalRecompensas}\n";
    echo "✅ Recompensas ativas: {$recompensasAtivas}\n";
    echo "💰 Créditos disponíveis: R$ " . number_format($totalCreditos, 2, ',', '.') . "\n";
    
    echo "\n📈 Produtos mais vendidos:\n";
    $maisVendidos = \App\Models\Estoque::orderBy('total_vendas', 'desc')
        ->limit(5)
        ->get();
    
    foreach($maisVendidos as $i => $p) {
        echo "   " . ($i+1) . ". {$p->produto_nome} ({$p->total_vendas} vendas)\n";
    }
    
    echo "\n";
}

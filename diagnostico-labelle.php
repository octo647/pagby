#!/usr/bin/env php
<?php
/**
 * Diagnóstico Labelle - Verificar dados disponíveis
 * 
 * Execute: php diagnostico-labelle.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  DIAGNÓSTICO LABELLE                                       ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// Conectar ao Labelle
echo "🔍 Conectando ao Labelle...\n";
try {
    $tenant = \App\Models\Tenant::where('id', 'like', '%labelle%')
        ->orWhereHas('domains', function($q) {
            $q->where('domain', 'like', '%labelle%');
        })
        ->orWhere('name', 'like', '%labelle%')
        ->first();
    
    if (!$tenant) {
        echo "❌ Tenant Labelle não encontrado!\n\n";
        echo "Tenants disponíveis:\n";
        $tenants = \App\Models\Tenant::all(['id', 'name']);
        foreach($tenants as $t) {
            echo "  - {$t->name} (ID: {$t->id})\n";
        }
        die("\n");
    }
    
    tenancy()->initialize($tenant);
    
    echo "✅ Conectado: {$tenant->name}\n";
    echo "📊 DB: " . DB::connection()->getDatabaseName() . "\n\n";
} catch (\Exception $e) {
    die("❌ ERRO: " . $e->getMessage() . "\n\n");
}

// Verificações
echo "═══════════════════════════════════════════════════════════\n";
echo "VERIFICANDO DADOS ESSENCIAIS\n";
echo "═══════════════════════════════════════════════════════════\n\n";

// 1. Filiais
echo "1️⃣ FILIAIS (Branches)\n";
$branches = \App\Models\Branch::all();
$qtdBranches = $branches->count();

if ($qtdBranches > 0) {
    echo "   ✅ {$qtdBranches} filial(is) encontrada(s)\n";
    foreach($branches as $b) {
        echo "      - {$b->name} (ID: {$b->id})\n";
    }
} else {
    echo "   ❌ Nenhuma filial encontrada\n";
    echo "      → NECESSÁRIO criar uma filial\n";
}
echo "\n";

// 2. Roles
echo "2️⃣ ROLES (Perfis)\n";
$roles = \App\Models\Role::all();
echo "   " . ($roles->count() > 0 ? "✅" : "❌") . " {$roles->count()} role(s)\n";
foreach($roles as $r) {
    echo "      - {$r->name}\n";
}
echo "\n";

// 3. Usuários
echo "3️⃣ USUÁRIOS\n";
$totalUsers = \App\Models\User::count();
echo "   Total: {$totalUsers} usuário(s)\n\n";

// 3.1 Proprietários
$proprietarios = \App\Models\User::whereHas('roles', function($q) {
    $q->where('role', 'Proprietário');
})->get();
echo "   👔 Proprietários: {$proprietarios->count()}\n";
if ($proprietarios->count() > 0) {
    foreach($proprietarios as $u) {
        echo "      ✅ {$u->name} ({$u->email})\n";
    }
} else {
    echo "      ⚠️ Nenhum proprietário encontrado\n";
}
echo "\n";

// 3.2 Funcionários
$funcionarios = \App\Models\User::whereHas('roles', function($q) {
    $q->where('role', 'Funcionário');
})->get();
echo "   ✂️ Funcionários: {$funcionarios->count()}\n";
if ($funcionarios->count() > 0) {
    foreach($funcionarios as $u) {
        echo "      ✅ {$u->name} ({$u->email})\n";
    }
} else {
    echo "      ❌ NENHUM funcionário encontrado\n";
    echo "      → NECESSÁRIO para criar vendas\n";
}
echo "\n";

// 3.3 Clientes
$clientes = \App\Models\User::whereHas('roles', function($q) {
    $q->where('role', 'Cliente');
})->get();
echo "   👤 Clientes: {$clientes->count()}\n";
if ($clientes->count() > 0) {
    foreach($clientes->take(5) as $u) {
        echo "      ✅ {$u->name} ({$u->email})\n";
    }
    if ($clientes->count() > 5) {
        echo "      ... e mais " . ($clientes->count() - 5) . " cliente(s)\n";
    }
} else {
    echo "      ❌ NENHUM cliente encontrado\n";
    echo "      → NECESSÁRIO para receber recompensas\n";
}
echo "\n";

// 4. Serviços
echo "4️⃣ SERVIÇOS\n";
$servicos = \App\Models\Service::all();
if ($servicos->count() > 0) {
    echo "   ✅ {$servicos->count()} serviço(s)\n";
    foreach($servicos as $s) {
        echo "      - {$s->service} (R$ " . number_format($s->price, 2, ',', '.') . ")\n";
    }
} else {
    echo "   ⚠️ Nenhum serviço cadastrado\n";
}
echo "\n";

// 5. Produtos
echo "5️⃣ PRODUTOS (Estoque)\n";
$produtos = \App\Models\Estoque::all();
if ($produtos->count() > 0) {
    echo "   ✅ {$produtos->count()} produto(s)\n";
    foreach($produtos->take(5) as $p) {
        echo "      - {$p->produto_nome} (R$ " . number_format($p->preco_unitario, 2, ',', '.') . ") [Estoque: {$p->quantidade_atual}]\n";
    }
    if ($produtos->count() > 5) {
        echo "      ... e mais " . ($produtos->count() - 5) . " produto(s)\n";
    }
} else {
    echo "   ⚠️ Nenhum produto no estoque\n";
}
echo "\n";

// 6. Comandas
echo "6️⃣ COMANDAS\n";
$comandas = \App\Models\Comanda::count();
echo "   " . ($comandas > 0 ? "✅" : "ℹ️") . " {$comandas} comanda(s) no histórico\n\n";

// 7. Recompensas
echo "7️⃣ RECOMPENSAS\n";
$recompensas = \App\Models\FidelidadeReward::count();
$ativas = \App\Models\FidelidadeReward::where('status', 'ativo')->count();
echo "   " . ($recompensas > 0 ? "✅" : "ℹ️") . " {$recompensas} recompensa(s) criada(s)\n";
echo "   " . ($ativas > 0 ? "✅" : "ℹ️") . " {$ativas} ativa(s)\n\n";

// 8. Vínculos Produto-Serviço
echo "8️⃣ VÍNCULOS PRODUTO-SERVIÇO\n";
$vinculos = DB::table('service_estoque')->count();
echo "   " . ($vinculos > 0 ? "✅" : "ℹ️") . " {$vinculos} vínculo(s) configurado(s)\n\n";

// Resumo
echo "═══════════════════════════════════════════════════════════\n";
echo "RESUMO\n";
echo "═══════════════════════════════════════════════════════════\n\n";

$problemas = [];

if ($qtdBranches === 0) {
    $problemas[] = "Criar filial (Branch)";
}
if ($funcionarios->count() === 0) {
    $problemas[] = "Criar pelo menos 1 funcionário";
}
if ($clientes->count() === 0) {
    $problemas[] = "Criar pelo menos 1 cliente";
}

if (empty($problemas)) {
    echo "✅ TUDO OK! Você pode executar os testes.\n\n";
    echo "Execute: php teste-labelle-fidelidade.php\n\n";
} else {
    echo "⚠️ PROBLEMAS ENCONTRADOS:\n\n";
    foreach($problemas as $i => $p) {
        echo "   " . ($i + 1) . ". {$p}\n";
    }
    echo "\n";
    echo "💡 SOLUÇÃO:\n";
    echo "Execute: php preparar-labelle.php\n\n";
}

// Sugestões
if ($servicos->count() === 0) {
    echo "💡 Dica: Crie serviços antes de testar sugestões de produtos\n";
}
if ($produtos->count() === 0) {
    echo "💡 Dica: Adicione produtos ao estoque para testar vendas\n";
}
if ($vinculos === 0 && $servicos->count() > 0 && $produtos->count() > 0) {
    echo "💡 Dica: Vincule produtos aos serviços para sugestões personalizadas\n";
}

echo "\n";

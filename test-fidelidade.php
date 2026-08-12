<?php
/**
 * Script de Teste - Sistema de Produtos e Fidelização
 * 
 * Execute no Tinker:
 * 1. php artisan tinker
 * 2. Copie e cole este script OU
 * 3. Modifique $tenantId abaixo e execute: php artisan tinker < test-fidelidade.php
 */

echo "🧪 TESTANDO SISTEMA DE PRODUTOS E FIDELIZAÇÃO\n\n";

// ⚠️ IMPORTANTE: Defina o ID ou domínio do tenant que deseja testar
$tenantId = null; // Substitua por ID do tenant ou deixe null para pegar o primeiro

echo "0️⃣ Inicializando contexto do tenant...\n";
try {
    if ($tenantId) {
        $tenant = \App\Models\Tenant::find($tenantId);
    } else {
        $tenant = \App\Models\Tenant::first();
    }
    
    if (!$tenant) {
        die("   ❌ Nenhum tenant encontrado! Crie um tenant primeiro.\n");
    }
    
    tenancy()->initialize($tenant);
    
    $database = \DB::connection()->getDatabaseName();
    echo "   ✅ Conectado ao tenant: {$tenant->id}\n";
    echo "   📊 Banco de dados: {$database}\n";
    echo "   🏢 Nome: {$tenant->name}\n\n";
} catch (\Exception $e) {
    die("   ❌ Erro ao inicializar tenant: " . $e->getMessage() . "\n");
}

// Teste 1: Verificar se tabelas foram criadas
echo "1️⃣ Verificando tabelas...\n";
try {
    $estoqueCount = DB::table('estoque')->count();
    echo "   ✅ Tabela 'estoque' existe ({$estoqueCount} registros)\n";
} catch (\Exception $e) {
    echo "   ❌ Erro na tabela 'estoque': " . $e->getMessage() . "\n";
}

try {
    $serviceEstoqueCount = DB::table('service_estoque')->count();
    echo "   ✅ Tabela 'service_estoque' existe ({$serviceEstoqueCount} vínculos)\n";
} catch (\Exception $e) {
    echo "   ❌ Erro na tabela 'service_estoque': " . $e->getMessage() . "\n";
}

try {
    $rewardsCount = DB::table('fidelidade_rewards')->count();
    echo "   ✅ Tabela 'fidelidade_rewards' existe ({$rewardsCount} recompensas)\n";
} catch (\Exception $e) {
    echo "   ❌ Erro na tabela 'fidelidade_rewards': " . $e->getMessage() . "\n";
}

echo "\n2️⃣ Verificando campo 'total_vendas' no estoque...\n";
try {
    $produto = \App\Models\Estoque::first();
    if ($produto) {
        echo "   ✅ Campo 'total_vendas' existe: {$produto->total_vendas}\n";
    } else {
        echo "   ⚠️  Nenhum produto no estoque ainda\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n3️⃣ Testando método hierárquico de sugestões...\n";
try {
    $service = \App\Models\Service::first();
    $branch = \App\Models\Branch::first();
    
    if ($service && $branch) {
        $produtos = $service->getProdutosSugeridos($branch->id, 3);
        echo "   ✅ Método getProdutosSugeridos() funciona\n";
        echo "   📦 {$produtos->count()} produtos sugeridos para '{$service->service}'\n";
        
        if ($produtos->isEmpty()) {
            echo "   💡 Dica: Vincule produtos manualmente ou adicione produtos ao estoque\n";
        } else {
            foreach ($produtos as $p) {
                echo "      - {$p->produto_nome} (R$ {$p->preco_unitario})\n";
            }
        }
    } else {
        echo "   ⚠️  Crie serviços e filiais primeiro\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n4️⃣ Testando criação de recompensa (simulação)...\n";
try {
    $cliente = \App\Models\User::whereHas('roles', function($q) {
        $q->where('role', 'Cliente');
    })->first();
    
    if ($cliente) {
        echo "   Cliente teste: {$cliente->name}\n";
        
        // Simular criação de recompensa
        $reward = \App\Models\FidelidadeReward::criarCredito(
            userId: $cliente->id,
            valor: 10.00,
            origem: 'compra_produto',
            diasValidade: 90
        );
        
        echo "   ✅ Recompensa criada com sucesso!\n";
        echo "   💰 Valor: R$ {$reward->valor_credito}\n";
        echo "   📅 Válida até: {$reward->validade->format('d/m/Y')}\n";
        echo "   🔑 ID: {$reward->id}\n";
        
        // Verificar saldo
        $saldo = \App\Models\FidelidadeReward::saldoCreditosCliente($cliente->id);
        echo "   💳 Saldo total do cliente: R$ " . number_format($saldo, 2, ',', '.') . "\n";
        
        // Limpar teste
        $reward->delete();
        echo "   🧹 Recompensa de teste removida\n";
    } else {
        echo "   ⚠️  Nenhum cliente encontrado. Crie um cliente primeiro.\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n5️⃣ Verificando Observer registrado...\n";
try {
    $observers = \Illuminate\Support\Facades\Event::getListeners('eloquent.created: App\Models\ComandaProduto');
    if (!empty($observers)) {
        echo "   ✅ Observer ComandaProduto está registrado\n";
    } else {
        echo "   ⚠️  Observer pode não estar registrado. Verifique AppServiceProvider.\n";
    }
} catch (\Exception $e) {
    echo "   ℹ️  Não foi possível verificar observers automaticamente\n";
}

echo "\n6️⃣ Testando comandos...\n";
echo "   Execute manualmente:\n";
echo "   - php artisan fidelidade:expirar-recompensas --dry-run\n";
echo "   - php artisan fidelidade:notificar-expirando --dry-run\n";

echo "\n✅ TESTE CONCLUÍDO!\n\n";
echo "📚 Próximos passos:\n";
echo "1. Acesse /proprietario/servicos/{id}/produtos para vincular produtos\n";
echo "2. Acesse /cliente/recompensas para ver recompensas\n";
echo "3. Venda um produto e veja a recompensa sendo criada automaticamente\n\n";

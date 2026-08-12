<?php

/**
 * Script para testar validade de 30 dias nas recompensas
 * 
 * Uso: php testar-validade-30-dias.php
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Database\Models\Domain;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Tenant para teste
$tenantDomain = 'labelle.localhost';

echo "\n🕐 TESTE DE VALIDADE DE 30 DIAS NAS RECOMPENSAS\n";
echo "Tenant: {$tenantDomain}\n\n";

try {
    // Inicializar tenancy
    $domain = Domain::where('domain', $tenantDomain)->first();
    if (!$domain) {
        throw new Exception("Domínio '{$tenantDomain}' não encontrado");
    }
    
    tenancy()->initialize($domain->tenant);
    echo "✅ Tenancy inicializado\n\n";
    
    // 1. Criar recompensa de teste diretamente
    echo "1️⃣ TESTANDO CRIAÇÃO DIRETA DE CRÉDITO...\n";
    
    $cliente = \App\Models\User::whereHas('roles', function($q) {
        $q->where('role', 'Cliente');
    })->first();
    
    if (!$cliente) {
        echo "   ❌ Nenhum cliente encontrado\n\n";
    } else {
        echo "   Cliente encontrado: {$cliente->name} (ID: {$cliente->id})\n\n";
        
        // Criar recompensa com valores padrão
        echo "   📝 Criando recompensa com valores padrão (sem especificar diasValidade)...\n";
        $recompensa = \App\Models\FidelidadeReward::criarCredito(
            userId: $cliente->id,
            valor: 50.00,
            origem: 'promocao'
        );
        
        echo "      ✅ Recompensa criada:\n";
        echo "         ID: {$recompensa->id}\n";
        echo "         Valor: R$ " . number_format($recompensa->valor_credito, 2, ',', '.') . "\n";
        echo "         Criado em: {$recompensa->created_at->format('d/m/Y')}\n";
        echo "         Validade: {$recompensa->validade->format('d/m/Y')}\n";
        
        // Calcular dias de validade
        $diasValidade = now()->diffInDays($recompensa->validade);
        echo "         Dias de validade: {$diasValidade} dias\n";
        
        if ($diasValidade == 30) {
            echo "         ✅ CORRETO! 30 dias de validade\n\n";
        } else {
            echo "         ❌ INCORRETO! Deveria ser 30 dias\n\n";
        }
        
        // Limpar teste
        $recompensa->delete();
        echo "      🗑️ Recompensa de teste deletada\n\n";
    }
    
    // 2. Testar via Observer (simulação)
    echo "2️⃣ TESTANDO VIA OBSERVER (getRecompensaConfig)...\n";
    
    // Usar reflection para acessar método privado
    $observer = new \App\Observers\ComandaProdutoObserver();
    $reflection = new ReflectionClass($observer);
    $method = $reflection->getMethod('getRecompensaConfig');
    $method->setAccessible(true);
    
    // Criar ComandaProduto simulado
    $comandaProdutoSimulado = new \App\Models\ComandaProduto();
    $comandaProdutoSimulado->subtotal = 50.00;
    
    $config = $method->invoke($observer, $comandaProdutoSimulado);
    
    echo "   Configuração retornada para compra de R$ 50,00:\n";
    echo "      Tipo: {$config['tipo']}\n";
    echo "      Percentual: " . ($config['percentual'] * 100) . "%\n";
    echo "      Dias de validade: {$config['dias_validade']} dias\n";
    
    if ($config['dias_validade'] == 30) {
        echo "      ✅ CORRETO! 30 dias de validade\n\n";
    } else {
        echo "      ❌ INCORRETO! Deveria ser 30 dias\n\n";
    }
    
    // Testar para compra acima de R$ 100
    $comandaProdutoSimulado->subtotal = 150.00;
    $config = $method->invoke($observer, $comandaProdutoSimulado);
    
    echo "   Configuração retornada para compra de R$ 150,00:\n";
    echo "      Tipo: {$config['tipo']}\n";
    echo "      Percentual: " . ($config['percentual'] * 100) . "%\n";
    echo "      Dias de validade: {$config['dias_validade']} dias\n";
    
    if ($config['dias_validade'] == 30) {
        echo "      ✅ CORRETO! 30 dias de validade\n\n";
    } else {
        echo "      ❌ INCORRETO! Deveria ser 30 dias\n\n";
    }
    
    // 3. Verificar recompensas existentes
    echo "3️⃣ VERIFICANDO RECOMPENSAS EXISTENTES...\n";
    
    $recompensas = \App\Models\FidelidadeReward::where('status', 'ativo')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    if ($recompensas->count() == 0) {
        echo "   ℹ️ Nenhuma recompensa ativa encontrada\n\n";
    } else {
        echo "   Últimas {$recompensas->count()} recompensas ativas:\n\n";
        
        foreach ($recompensas as $r) {
            $diasRestantes = now()->diffInDays($r->validade, false);
            $diasDesdeCreation = $r->created_at->diffInDays($r->validade);
            
            echo "      ID: {$r->id}\n";
            echo "      Valor: R$ " . number_format($r->valor_credito ?? 0, 2, ',', '.') . "\n";
            echo "      Criado: {$r->created_at->format('d/m/Y')}\n";
            echo "      Validade: {$r->validade->format('d/m/Y')}\n";
            echo "      Dias desde criação até validade: {$diasDesdeCreation} dias\n";
            echo "      Dias restantes: " . max(0, $diasRestantes) . " dias\n";
            
            if ($diasDesdeCreation == 30) {
                echo "      ✅ Criada com 30 dias\n";
            } elseif ($diasDesdeCreation == 90) {
                echo "      ⚠️ Criada com 90 dias (antiga)\n";
            } else {
                echo "      ℹ️ Criada com {$diasDesdeCreation} dias\n";
            }
            echo "\n";
        }
    }
    
    echo "✅ TESTE CONCLUÍDO!\n\n";
    
} catch (Exception $e) {
    echo "\n❌ ERRO: {$e->getMessage()}\n";
    echo "Arquivo: {$e->getFile()}:{$e->getLine()}\n\n";
    exit(1);
}

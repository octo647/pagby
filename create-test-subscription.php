<?php

/**
 * Script para criar assinatura de teste manualmente
 * 
 * Este script cria:
 * 1. Registro de assinatura na tabela subscriptions do tenant
 * 2. Registro de pagamento na tabela subscriptions_payments
 * 
 * USO:
 * php create-test-subscription.php <tenant_id> <user_email> <plan_id>
 * 
 * EXEMPLO:
 * php create-test-subscription.php bar contato@bar.com.br 1
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;

// Validar argumentos
if ($argc < 4) {
    echo "❌ Erro: Argumentos insuficientes\n";
    echo "Uso: php create-test-subscription.php <tenant_id> <user_email> <plan_id>\n";
    echo "Exemplo: php create-test-subscription.php bar contato@bar.com.br 1\n";
    exit(1);
}

$tenantId = $argv[1];
$userEmail = $argv[2];
$planId = $argv[3];

echo "🔍 Buscando tenant: {$tenantId}\n";

// Buscar tenant na base central
$tenant = Tenant::on('mysql')->find($tenantId);

if (!$tenant) {
    echo "❌ Tenant não encontrado: {$tenantId}\n";
    exit(1);
}

echo "✅ Tenant encontrado: {$tenant->name}\n";

try {
    // Inicializar contexto do tenant
    tenancy()->initialize($tenant);
    
    echo "🔍 Buscando usuário: {$userEmail}\n";
    
    // Buscar usuário
    $user = User::where('email', $userEmail)->first();
    
    if (!$user) {
        echo "❌ Usuário não encontrado: {$userEmail}\n";
        exit(1);
    }
    
    echo "✅ Usuário encontrado: {$user->name} (ID: {$user->id})\n";
    
    echo "🔍 Buscando plano: {$planId}\n";
    
    // Buscar plano
    $plan = Plan::find($planId);
    
    if (!$plan) {
        echo "❌ Plano não encontrado: {$planId}\n";
        exit(1);
    }
    
    echo "✅ Plano encontrado: {$plan->name} (R$ {$plan->price})\n";
    
    // Verificar se já existe assinatura ativa
    $existing = Subscription::where('user_id', $user->id)
        ->where('plan_id', $plan->id)
        ->where('status', 'Ativo')
        ->first();
    
    if ($existing) {
        echo "⚠️  Já existe uma assinatura ativa para este usuário e plano:\n";
        echo "   - Subscription ID: {$existing->id}\n";
        echo "   - Status: {$existing->status}\n";
        echo "   - Período: {$existing->start_date} até {$existing->end_date}\n";
        echo "\n🔄 Deseja atualizar? (s/n): ";
        
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);
        
        if (trim(strtolower($line)) != 's') {
            echo "❌ Operação cancelada\n";
            exit(0);
        }
        
        // Atualizar assinatura existente
        $existing->start_date = now();
        $existing->end_date = now()->addMonth();
        $existing->updated_by = $user->id;
        $existing->save();
        
        $subscription = $existing;
        echo "✅ Assinatura atualizada: ID {$subscription->id}\n";
    } else {
        // Criar nova assinatura
        echo "\n💳 Criando assinatura...\n";
        
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'mp_payment_id' => 'TEST_' . uniqid(),
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'status' => 'Ativo',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        
        echo "✅ Assinatura criada: ID {$subscription->id}\n";
    }
    
    // Criar registro de pagamento
    echo "\n💰 Criando registro de pagamento...\n";
    
    $payment = SubscriptionPayment::create([
        'subscription_id' => $subscription->id,
        'asaas_payment_id' => 'TEST_PAY_' . uniqid(),
        'amount' => $plan->price,
        'billing_type' => 'CREDIT_CARD',
        'due_date' => now(),
        'payment_date' => now(),
        'status' => 'received',
    ]);
    
    echo "✅ Pagamento criado: ID {$payment->id}\n";
    
    // Resumo
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "✅ ASSINATURA CRIADA COM SUCESSO!\n";
    echo str_repeat("=", 60) . "\n";
    echo "Tenant: {$tenant->name} ({$tenant->id})\n";
    echo "Usuário: {$user->name} ({$user->email})\n";
    echo "Plano: {$plan->name} (R$ {$plan->price})\n";
    echo "Subscription ID: {$subscription->id}\n";
    echo "Payment ID: {$payment->id}\n";
    echo "Status: {$subscription->status}\n";
    echo "Período: " . $subscription->start_date->format('d/m/Y') . " até " . $subscription->end_date->format('d/m/Y') . "\n";
    echo str_repeat("=", 60) . "\n";
    echo "\n🎉 O usuário agora pode acessar o sistema!\n";
    
} catch (\Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
} finally {
    tenancy()->end();
}

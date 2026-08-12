<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use App\Models\FidelidadeReward;
use Carbon\Carbon;

// Usar tenant labelle.localhost
$tenant = Tenant::whereHas('domains', function($q) {
    $q->where('domain', 'labelle.localhost');
})->firstOrFail();

tenancy()->initialize($tenant);

echo "🏢 Tenant: {$tenant->id}\n";
echo "📅 Data atual: " . now()->format('d/m/Y H:i:s') . "\n\n";

// Buscar recompensas ativas
$rewards = FidelidadeReward::where('status', 'ativo')
    ->where('validade', '>=', now())
    ->orderBy('validade', 'asc')
    ->get();

echo "📊 Total de recompensas ativas: " . $rewards->count() . "\n\n";

if ($rewards->count() > 0) {
    echo "=== RECOMPENSAS ENCONTRADAS ===\n\n";
    
    foreach ($rewards as $reward) {
        echo "ID: {$reward->id}\n";
        echo "Cliente: " . ($reward->user->name ?? 'N/A') . "\n";
        echo "Valor: R$ " . number_format($reward->valor_credito, 2, ',', '.') . "\n";
        echo "Validade: " . $reward->validade->format('d/m/Y') . "\n";
        
        // Cálculo ANTIGO (problema)
        $dias_antigo = $reward->validade->diffInDays(now());
        echo "Dias restantes (cálculo antigo): {$dias_antigo}\n";
        
        // Cálculo NOVO (corrigido)
        $dias_novo = max(0, (int) now()->diffInDays($reward->validade->endOfDay(), false));
        echo "Dias restantes (cálculo novo): {$dias_novo}\n";
        
        // Mensagem formatada
        $texto_dias = $dias_novo == 1 ? 'dia' : 'dias';
        echo "Mensagem: ⏰ Você tem {$dias_novo} {$texto_dias} para aproveitar\n";
        
        echo "\n" . str_repeat('-', 50) . "\n\n";
    }
} else {
    echo "❌ Nenhuma recompensa ativa encontrada\n";
    echo "\nCriando uma recompensa de teste...\n\n";
    
    // Pegar primeiro usuário
    $user = \App\Models\User::first();
    
    if (!$user) {
        echo "❌ Nenhum usuário encontrado\n";
        exit(1);
    }
    
    // Criar recompensa que expira em 5 dias
    $reward = FidelidadeReward::create([
        'user_id' => $user->id,
        'tipo' => 'credito',
        'valor_credito' => 50.00,
        'origem' => 'Teste de sistema',
        'status' => 'ativo',
        'validade' => now()->addDays(5)->endOfDay(),
    ]);
    
    echo "✅ Recompensa criada!\n";
    echo "ID: {$reward->id}\n";
    echo "Valor: R$ " . number_format($reward->valor_credito, 2, ',', '.') . "\n";
    echo "Validade: " . $reward->validade->format('d/m/Y H:i:s') . "\n";
    
    $dias = max(0, (int) now()->diffInDays($reward->validade->endOfDay(), false));
    $texto_dias = $dias == 1 ? 'dia' : 'dias';
    echo "Mensagem: ⏰ Você tem {$dias} {$texto_dias} para aproveitar\n";
}

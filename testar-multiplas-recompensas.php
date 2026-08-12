<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use App\Models\User;
use App\Models\FidelidadeReward;
use Carbon\Carbon;

// Usar tenant labelle.localhost
$tenant = Tenant::whereHas('domains', function($q) {
    $q->where('domain', 'labelle.localhost');
})->firstOrFail();

tenancy()->initialize($tenant);

echo "🏢 Tenant: {$tenant->id}\n";
echo "📅 Data atual: " . now()->format('d/m/Y H:i:s') . "\n\n";

// Buscar um usuário (qualquer um)
$user = User::first();

if (!$user) {
    echo "❌ Nenhum usuário encontrado\n";
    exit(1);
}

echo "👤 Cliente: {$user->name} (ID: {$user->id})\n";
echo "📧 Email: {$user->email}\n\n";

// Verificar recompensas ativas existentes
$recompensasExistentes = FidelidadeReward::where('user_id', $user->id)
    ->where('status', 'ativo')
    ->where('validade', '>=', now())
    ->get();

echo "📊 Recompensas ativas existentes: " . $recompensasExistentes->count() . "\n\n";

if ($recompensasExistentes->count() >= 2) {
    echo "✅ Cliente já tem " . $recompensasExistentes->count() . " recompensas ativas!\n\n";
    
    echo "=== RECOMPENSAS DISPONÍVEIS ===\n";
    foreach ($recompensasExistentes as $index => $reward) {
        echo "\nRecompensa #" . ($index + 1) . ":\n";
        echo "  ID: {$reward->id}\n";
        echo "  Valor: R$ " . number_format($reward->valor_credito, 2, ',', '.') . "\n";
        echo "  Validade: " . $reward->validade->format('d/m/Y') . "\n";
        echo "  Status: {$reward->status}\n";
    }
    
    // Simular seleção de múltiplas recompensas
    $totalDesconto = $recompensasExistentes->sum('valor_credito');
    
    echo "\n=== SIMULAÇÃO DE USO ===\n";
    echo "Cliente seleciona TODAS as " . $recompensasExistentes->count() . " recompensas\n";
    echo "Total de desconto acumulado: R$ " . number_format($totalDesconto, 2, ',', '.') . "\n\n";
    
    // Simular aplicação em um serviço de R$ 150
    $valorServico = 150.00;
    echo "Valor do serviço: R$ " . number_format($valorServico, 2, ',', '.') . "\n";
    
    $totalRestante = $valorServico;
    $descontoAplicado = 0;
    
    echo "\nAplicando recompensas:\n";
    foreach ($recompensasExistentes as $index => $reward) {
        if ($totalRestante <= 0) {
            echo "  Recompensa #" . ($index + 1) . ": ⏭️ Não aplicada (valor já zerado)\n";
            continue;
        }
        
        $descontoAtual = min($reward->valor_credito, $totalRestante);
        $totalRestante -= $descontoAtual;
        $descontoAplicado += $descontoAtual;
        
        echo "  Recompensa #" . ($index + 1) . ": -R$ " . number_format($descontoAtual, 2, ',', '.') . 
             " (restante: R$ " . number_format($totalRestante, 2, ',', '.') . ")\n";
    }
    
    echo "\n✅ RESULTADO FINAL:\n";
    echo "Valor original: R$ " . number_format($valorServico, 2, ',', '.') . "\n";
    echo "Desconto aplicado: R$ " . number_format($descontoAplicado, 2, ',', '.') . "\n";
    echo "Valor final: R$ " . number_format($totalRestante, 2, ',', '.') . "\n";
    
} else {
    echo "⚠️ Cliente tem menos de 2 recompensas. Criando mais para teste...\n\n";
    
    // Criar 2 recompensas de teste
    $recompensa1 = FidelidadeReward::create([
        'user_id' => $user->id,
        'tipo' => 'credito',
        'valor_credito' => 44.33,
        'status' => 'ativo',
        'validade' => now()->addDays(30)->endOfDay(),
    ]);
    
    $recompensa2 = FidelidadeReward::create([
        'user_id' => $user->id,
        'tipo' => 'credito',
        'valor_credito' => 26.16,
        'status' => 'ativo',
        'validade' => now()->addDays(30)->endOfDay(),
    ]);
    
    echo "✅ Recompensas criadas:\n";
    echo "\nRecompensa #1:\n";
    echo "  ID: {$recompensa1->id}\n";
    echo "  Valor: R$ " . number_format($recompensa1->valor_credito, 2, ',', '.') . "\n";
    echo "  Validade: " . $recompensa1->validade->format('d/m/Y') . "\n";
    
    echo "\nRecompensa #2:\n";
    echo "  ID: {$recompensa2->id}\n";
    echo "  Valor: R$ " . number_format($recompensa2->valor_credito, 2, ',', '.') . "\n";
    echo "  Validade: " . $recompensa2->validade->format('d/m/Y') . "\n";
    
    $totalDesconto = $recompensa1->valor_credito + $recompensa2->valor_credito;
    echo "\n💰 Total de desconto disponível: R$ " . number_format($totalDesconto, 2, ',', '.') . "\n";
    
    echo "\n✅ Agora o cliente pode fazer um agendamento e selecionar ambas as recompensas!\n";
    echo "   Acesse: http://labelle.localhost:8000/agenda\n";
    echo "   Login: {$user->email}\n";
}

echo "\n" . str_repeat('=', 60) . "\n";
echo "🧪 TESTE: Faça um agendamento selecionando MÚLTIPLAS recompensas\n";
echo "    e verifique se TODAS são aplicadas corretamente!\n";
echo str_repeat('=', 60) . "\n";

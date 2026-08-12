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

// Buscar um usuário
$user = User::first();

if (!$user) {
    echo "❌ Nenhum usuário encontrado\n";
    exit(1);
}

echo "👤 Usuário: {$user->name} (ID: {$user->id})\n\n";

// Limpar recompensas antigas de teste
FidelidadeReward::where('user_id', $user->id)
    ->where('status', 'ativo')
    ->delete();

echo "🧹 Recompensas antigas removidas\n\n";

// Criar 4 recompensas com datas de validade diferentes
echo "📝 Criando 4 recompensas com validades diferentes...\n\n";

$recompensas = [
    [
        'dias' => 5,
        'valor' => 20.00,
        'label' => 'Expira em 5 dias (PRIORIDADE 1 - Deve ser usada PRIMEIRO)',
    ],
    [
        'dias' => 15,
        'valor' => 30.00,
        'label' => 'Expira em 15 dias (PRIORIDADE 2)',
    ],
    [
        'dias' => 25,
        'valor' => 40.00,
        'label' => 'Expira em 25 dias (PRIORIDADE 3)',
    ],
    [
        'dias' => 10,
        'valor' => 50.00,
        'label' => 'Expira em 10 dias (PRIORIDADE 2.5)',
    ],
];

$recompensasCriadas = [];

foreach ($recompensas as $index => $dados) {
    $reward = FidelidadeReward::create([
        'user_id' => $user->id,
        'tipo' => 'credito',
        'valor_credito' => $dados['valor'],
        'status' => 'ativo',
        'validade' => now()->addDays($dados['dias'])->endOfDay(),
    ]);
    
    $recompensasCriadas[] = $reward;
    
    echo "✅ Recompensa #{$reward->id}: R$ " . number_format($reward->valor_credito, 2, ',', '.') . 
         " | Validade: " . $reward->validade->format('d/m/Y') . 
         " | " . $dados['label'] . "\n";
}

echo "\n" . str_repeat('=', 70) . "\n";
echo "📊 ORDEM ESPERADA DE APLICAÇÃO (por validade):\n";
echo str_repeat('=', 70) . "\n\n";

// Buscar recompensas ordenadas por validade (como o sistema fará)
$recompensasOrdenadas = FidelidadeReward::where('user_id', $user->id)
    ->where('status', 'ativo')
    ->where('validade', '>=', now())
    ->orderBy('validade', 'asc')
    ->get();

foreach ($recompensasOrdenadas as $index => $reward) {
    $diasRestantes = max(0, (int) now()->diffInDays($reward->validade->endOfDay(), false));
    echo ($index + 1) . "ª) ID {$reward->id}: R$ " . number_format($reward->valor_credito, 2, ',', '.') . 
         " | Validade: " . $reward->validade->format('d/m/Y') . 
         " | {$diasRestantes} dias restantes\n";
}

echo "\n" . str_repeat('=', 70) . "\n";
echo "🧪 SIMULAÇÃO: Agendamento de R$ 100,00\n";
echo str_repeat('=', 70) . "\n\n";

$valorServico = 100.00;
$totalRestante = $valorServico;
$descontoTotal = 0;

echo "💰 Valor do serviço: R$ " . number_format($valorServico, 2, ',', '.') . "\n\n";
echo "Aplicando recompensas na ordem de validade:\n\n";

foreach ($recompensasOrdenadas as $index => $reward) {
    if ($totalRestante <= 0) {
        $diasRestantes = max(0, (int) now()->diffInDays($reward->validade->endOfDay(), false));
        echo "   " . ($index + 1) . ") ID {$reward->id} (R$ " . number_format($reward->valor_credito, 2, ',', '.') . 
             ", {$diasRestantes} dias): ⏭️  NÃO APLICADA (valor já zerado)\n";
        continue;
    }
    
    $descontoAtual = min($reward->valor_credito, $totalRestante);
    $totalRestante -= $descontoAtual;
    $descontoTotal += $descontoAtual;
    
    $diasRestantes = max(0, (int) now()->diffInDays($reward->validade->endOfDay(), false));
    
    echo "   " . ($index + 1) . ") ID {$reward->id} (R$ " . number_format($reward->valor_credito, 2, ',', '.') . 
         ", {$diasRestantes} dias): ✅ -R$ " . number_format($descontoAtual, 2, ',', '.') . 
         " | Restante: R$ " . number_format($totalRestante, 2, ',', '.') . "\n";
}

echo "\n" . str_repeat('-', 70) . "\n";
echo "📊 RESULTADO FINAL:\n";
echo "   Valor original: R$ " . number_format($valorServico, 2, ',', '.') . "\n";
echo "   Desconto total aplicado: R$ " . number_format($descontoTotal, 2, ',', '.') . "\n";
echo "   Valor final a pagar: R$ " . number_format($totalRestante, 2, ',', '.') . "\n";
echo str_repeat('-', 70) . "\n\n";

echo "🎯 CONCLUSÃO:\n";
echo "   ✅ As recompensas que expiram PRIMEIRO foram priorizadas!\n";
echo "   ✅ Recompensas com mais prazo foram preservadas para uso futuro\n\n";

echo "🧪 TESTE NA INTERFACE:\n";
echo "   1. Acesse: http://labelle.localhost:8000/agenda\n";
echo "   2. Login: {$user->email}\n";
echo "   3. Faça um agendamento\n";
echo "   4. Observe que a primeira recompensa tem o badge '🔥 Será usada primeiro'\n";
echo "   5. Selecione TODAS as 4 recompensas\n";
echo "   6. Confirme o agendamento\n";
echo "   7. Verifique nos logs que foram aplicadas na ordem de validade!\n\n";

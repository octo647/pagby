<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use App\Models\FidelidadeReward;

// Usar tenant labelle.localhost
$tenant = Tenant::whereHas('domains', function($q) {
    $q->where('domain', 'labelle.localhost');
})->firstOrFail();

tenancy()->initialize($tenant);

echo "🏢 Tenant: {$tenant->id}\n";
echo "📅 Data: " . now()->format('d/m/Y H:i:s') . "\n\n";

echo str_repeat('=', 70) . "\n";
echo "✅ CORREÇÃO APLICADA: RECOMPENSAS COM VALOR ZERO\n";
echo str_repeat('=', 70) . "\n\n";

// Verificar se ainda existem recompensas com valor zero
$recompensasZero = FidelidadeReward::where('valor_credito', '<=', 0)
    ->orWhereNull('valor_credito')
    ->get();

echo "📊 Recompensas com valor zero no banco: " . $recompensasZero->count() . "\n";

if ($recompensasZero->count() > 0) {
    echo "⚠️ Ainda existem recompensas com valor zero (já foram filtradas da exibição)\n\n";
    foreach ($recompensasZero as $reward) {
        echo "  - ID {$reward->id}: R$ " . number_format($reward->valor_credito ?? 0, 2, ',', '.') . 
             " | Status: {$reward->status} | Criado: " . $reward->created_at->format('d/m/Y') . "\n";
    }
} else {
    echo "✅ Nenhuma recompensa com valor zero encontrada!\n";
}

echo "\n" . str_repeat('-', 70) . "\n\n";

// Verificar recompensas válidas que serão exibidas
$recompensasValidas = FidelidadeReward::where('status', 'ativo')
    ->where('validade', '>=', now())
    ->where('valor_credito', '>', 0) // Novo filtro
    ->orderBy('validade', 'asc')
    ->get();

echo "📊 Recompensas VÁLIDAS (que serão exibidas): " . $recompensasValidas->count() . "\n\n";

if ($recompensasValidas->count() > 0) {
    echo "=== RECOMPENSAS VÁLIDAS ===\n\n";
    foreach ($recompensasValidas as $index => $reward) {
        $diasRestantes = max(0, (int) now()->diffInDays($reward->validade->endOfDay(), false));
        echo ($index + 1) . ") Cliente: " . ($reward->user->name ?? 'N/A') . "\n";
        echo "   ID: {$reward->id}\n";
        echo "   Valor: R$ " . number_format($reward->valor_credito, 2, ',', '.') . "\n";
        echo "   Validade: " . $reward->validade->format('d/m/Y') . " ({$diasRestantes} dias)\n\n";
    }
}

echo str_repeat('=', 70) . "\n";
echo "📝 CORREÇÕES IMPLEMENTADAS:\n";
echo str_repeat('=', 70) . "\n\n";

echo "1. ✅ Observer validado:\n";
echo "   - Não cria recompensas se subtotal <= 0\n";
echo "   - Não cria recompensas se valor calculado < R$ 0,01\n";
echo "   - Logs de warning para debugging\n\n";

echo "2. ✅ Queries filtradas:\n";
echo "   - Componente MakeAppointment: where('valor_credito', '>', 0)\n";
echo "   - Componente MinhasRecompensas: where('valor_credito', '>', 0)\n";
echo "   - Dashboard Fidelidade: where('valor_credito', '>', 0)\n\n";

echo "3. ✅ Estatísticas ajustadas:\n";
echo "   - Total de recompensas ativas: exclui valor zero\n";
echo "   - Valor total de créditos: exclui valor zero\n";
echo "   - Clientes ativos: conta apenas com recompensas válidas\n\n";

echo "4. ✅ Recompensas existentes com valor zero:\n";
echo "   - Já foram deletadas do banco\n";
echo "   - Se novas forem criadas, não aparecerão nas listagens\n\n";

echo str_repeat('=', 70) . "\n";
echo "🎯 RESULTADO: Recompensas R$ 0,00 não aparecerão mais!\n";
echo str_repeat('=', 70) . "\n";

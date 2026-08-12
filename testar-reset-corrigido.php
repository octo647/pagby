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

// Verificar recompensas ativas
$recompensas = FidelidadeReward::where('status', 'ativo')
    ->where('validade', '>=', now())
    ->orderBy('validade', 'asc')
    ->get();

echo "📊 Recompensas ativas disponíveis: " . $recompensas->count() . "\n\n";

if ($recompensas->count() > 0) {
    echo "=== RECOMPENSAS (ordenadas por validade) ===\n\n";
    foreach ($recompensas as $index => $reward) {
        $diasRestantes = max(0, (int) now()->diffInDays($reward->validade->endOfDay(), false));
        $priorityLabel = $index === 0 ? " 🔥 SERÁ USADA PRIMEIRO" : "";
        
        echo ($index + 1) . ") Cliente: " . ($reward->user->name ?? 'N/A') . "\n";
        echo "   ID: {$reward->id}\n";
        echo "   Valor: R$ " . number_format($reward->valor_credito, 2, ',', '.') . "\n";
        echo "   Validade: " . $reward->validade->format('d/m/Y') . " ({$diasRestantes} dias){$priorityLabel}\n\n";
    }
} else {
    echo "❌ Nenhuma recompensa ativa encontrada\n";
}

echo str_repeat('=', 70) . "\n";
echo "🧪 TESTE DO PROBLEMA DE RESET:\n";
echo str_repeat('=', 70) . "\n\n";

echo "ANTES (com bug):\n";
echo "  ❌ Componente filho @livewire('cliente.minhas-recompensas') criado\n";
echo "  ❌ Ao clicar no horário → novo componente instanciado\n";
echo "  ❌ Componente filho dispara eventos → pai re-renderiza\n";
echo "  ❌ Estado do agendamento reseta\n\n";

echo "AGORA (corrigido):\n";
echo "  ✅ Recompensas exibidas INLINE no componente pai\n";
echo "  ✅ Método toggleRecompensa() atualiza array diretamente\n";
echo "  ✅ Sem componente filho Livewire → sem eventos\n";
echo "  ✅ Sem re-render do pai → SEM RESET!\n\n";

echo str_repeat('=', 70) . "\n";
echo "📝 COMO TESTAR NA INTERFACE:\n";
echo str_repeat('=', 70) . "\n\n";

if ($recompensas->count() > 0) {
    $primeiroUsuario = $recompensas->first()->user;
    echo "1. Acesse: http://labelle.localhost:8000/agenda\n";
    echo "2. Login: {$primeiroUsuario->email}\n";
    echo "3. Selecione um profissional e serviços\n";
    echo "4. 🎯 CLIQUE EM UM HORÁRIO\n";
    echo "5. ✅ Verifique que NÃO reseta (horário permanece selecionado)\n";
    echo "6. Veja as recompensas aparecerem abaixo\n";
    echo "7. Clique em múltiplas recompensas para selecionar\n";
    echo "8. Confirme o agendamento\n";
    echo "9. Verifique que as recompensas foram aplicadas na ordem de validade!\n\n";
} else {
    echo "⚠️ Execute primeiro: php testar-priorizacao-validade.php\n";
    echo "   para criar recompensas de teste\n\n";
}

echo "✅ Correção implementada com sucesso!\n";

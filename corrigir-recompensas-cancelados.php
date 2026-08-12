<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use App\Models\FidelidadeReward;
use App\Models\Comanda;
use App\Models\Appointment;

// Usar tenant labelle.localhost
$tenant = Tenant::whereHas('domains', function($q) {
    $q->where('domain', 'labelle.localhost');
})->firstOrFail();

tenancy()->initialize($tenant);

echo str_repeat('=', 70) . "\n";
echo "🔍 DIAGNÓSTICO: RECOMPENSAS EM AGENDAMENTOS CANCELADOS\n";
echo str_repeat('=', 70) . "\n\n";

// Buscar recompensas "usadas" em agendamentos cancelados
$recompensasPreasas = FidelidadeReward::where('status', 'usado')
    ->whereHas('comandaOndeUsado', function($q) {
        $q->whereHas('appointment', function($q2) {
            $q2->where('status', 'Cancelado');
        });
    })
    ->get();

echo "📊 Recompensas 'usadas' em agendamentos cancelados: " . $recompensasPreasas->count() . "\n\n";

if ($recompensasPreasas->count() > 0) {
    echo "⚠️ PROBLEMA DETECTADO: Recompensas não foram restauradas!\n\n";
    
    foreach ($recompensasPreasas as $r) {
        $comanda = $r->comandaOndeUsado;
        $appointment = $comanda->appointment ?? null;
        
        echo "─────────────────────────────────────────\n";
        echo "Recompensa ID: {$r->id}\n";
        echo "Cliente: " . ($r->user->name ?? 'N/A') . "\n";
        echo "Valor: R$ " . number_format($r->valor_credito, 2, ',', '.') . "\n";
        echo "Status: {$r->status} (deveria ser 'ativo')\n";
        echo "Validade: " . $r->validade->format('d/m/Y') . "\n";
        
        if ($comanda) {
            echo "Comanda: #{$comanda->numero_comanda}\n";
        }
        
        if ($appointment) {
            echo "Agendamento: #{$appointment->id} - Status: {$appointment->status}\n";
        }
        
        // Verificar se ainda está válida
        if ($r->validade >= now()->toDate()) {
            echo "✅ Ainda válida - pode ser restaurada\n";
        } else {
            echo "❌ Já expirou - será marcada como expirada\n";
        }
        
        echo "\n";
    }
    
    echo str_repeat('=', 70) . "\n";
    echo "🔧 RESTAURANDO RECOMPENSAS...\n";
    echo str_repeat('=', 70) . "\n\n";
    
    $restauradas = 0;
    $expiradas = 0;
    
    foreach ($recompensasPreasas as $r) {
        echo "Processando recompensa ID {$r->id}... ";
        
        if ($r->restaurar()) {
            $restauradas++;
            echo "✅ Restaurada (ativo)\n";
        } else {
            $expiradas++;
            echo "⏰ Expirada (fora da validade)\n";
        }
    }
    
    echo "\n" . str_repeat('=', 70) . "\n";
    echo "📊 RESULTADO:\n";
    echo str_repeat('=', 70) . "\n";
    echo "✅ Recompensas restauradas: {$restauradas}\n";
    echo "⏰ Recompensas expiradas: {$expiradas}\n";
    echo "📌 Total processado: " . ($restauradas + $expiradas) . "\n\n";
    
} else {
    echo "✅ NENHUM PROBLEMA ENCONTRADO!\n";
    echo "Todas as recompensas de agendamentos cancelados já foram restauradas.\n\n";
}

// Verificar recompensas ativas disponíveis
echo str_repeat('=', 70) . "\n";
echo "📊 RESUMO DE RECOMPENSAS NO SISTEMA\n";
echo str_repeat('=', 70) . "\n\n";

$stats = [
    'ativo' => FidelidadeReward::where('status', 'ativo')->count(),
    'usado' => FidelidadeReward::where('status', 'usado')->count(),
    'expirado' => FidelidadeReward::where('status', 'expirado')->count(),
];

echo "Status:\n";
foreach ($stats as $status => $count) {
    $icon = $status === 'ativo' ? '✅' : ($status === 'usado' ? '💰' : '⏰');
    echo "  {$icon} " . ucfirst($status) . ": {$count}\n";
}

echo "\n";

// Recompensas ativas por cliente
$recompensasAtivas = FidelidadeReward::where('status', 'ativo')
    ->where('validade', '>=', now())
    ->with('user')
    ->get()
    ->groupBy('user_id');

echo "Clientes com recompensas ativas: " . $recompensasAtivas->count() . "\n";

if ($recompensasAtivas->count() > 0) {
    echo "\nTop 5 clientes com mais créditos:\n";
    $topClientes = $recompensasAtivas->map(function($rewards, $userId) {
        return [
            'user' => $rewards->first()->user,
            'total' => $rewards->sum('valor_credito'),
            'quantidade' => $rewards->count(),
        ];
    })->sortByDesc('total')->take(5);
    
    foreach ($topClientes as $item) {
        echo "  - " . ($item['user']->name ?? 'N/A') . ": ";
        echo "R$ " . number_format($item['total'], 2, ',', '.') . " ";
        echo "({$item['quantidade']} " . ($item['quantidade'] == 1 ? 'recompensa' : 'recompensas') . ")\n";
    }
}

echo "\n" . str_repeat('=', 70) . "\n";
echo "🎯 CORREÇÃO APLICADA NOS COMPONENTES:\n";
echo str_repeat('=', 70) . "\n";
echo "✅ ServicosRealizados.php - agora restaura recompensas\n";
echo "✅ ServicosFuncionarioRealizados.php - agora restaura recompensas\n";
echo "✅ Agenda.php - já estava restaurando\n";
echo "✅ Appointments.php - já estava restaurando\n\n";
echo "📌 A partir de agora, todos os cancelamentos restaurarão as recompensas!\n";
echo str_repeat('=', 70) . "\n";

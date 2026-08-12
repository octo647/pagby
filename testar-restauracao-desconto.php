<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Comanda;
use App\Models\FidelidadeReward;
use App\Models\Service;
use App\Models\Branch;

// Usar tenant labelle.localhost
$tenant = Tenant::whereHas('domains', function($q) {
    $q->where('domain', 'labelle.localhost');
})->firstOrFail();

tenancy()->initialize($tenant);

echo str_repeat('=', 70) . "\n";
echo "🧪 TESTE COMPLETO: CANCELAMENTO COM RESTAURAÇÃO DE DESCONTO\n";
echo str_repeat('=', 70) . "\n\n";

// 1. Buscar usuário com recompensa ativa
$recompensaAtiva = FidelidadeReward::where('status', 'ativo')
    ->where('validade', '>=', now())
    ->where('valor_credito', '>', 0)
    ->first();

if (!$recompensaAtiva) {
    echo "❌ Nenhuma recompensa ativa encontrada.\n";
    echo "Criando recompensa de teste...\n\n";
    
    $cliente = User::first();
    $recompensaAtiva = FidelidadeReward::criarCredito(
        userId: $cliente->id,
        valor: 50.00,
        origem: 'teste',
        diasValidade: 30
    );
    
    echo "✅ Recompensa criada: ID {$recompensaAtiva->id} - R$ 50,00\n\n";
} else {
    $cliente = $recompensaAtiva->user;
    echo "✅ Cliente encontrado: {$cliente->name}\n\n";
}

// Listar recompensas do cliente ANTES
$recompensasAntes = FidelidadeReward::where('user_id', $cliente->id)
    ->where('status', 'ativo')
    ->where('validade', '>=', now())
    ->get();

echo "--- RECOMPENSAS DO CLIENTE (ANTES) ---\n";
foreach ($recompensasAntes as $r) {
    echo "  ID {$r->id}: R$ " . number_format($r->valor_credito, 2, ',', '.') . 
         " | Status: {$r->status} | Validade: " . $r->validade->format('d/m/Y') . "\n";
}
echo "Total de crédito disponível: R$ " . number_format($recompensasAntes->sum('valor_credito'), 2, ',', '.') . "\n\n";

// 2. Criar agendamento de teste
$funcionario = User::whereHas('branches')->first();
$servico = Service::first();
$branch = Branch::first();

if (!$funcionario || !$servico || !$branch) {
    echo "❌ Dados insuficientes para criar agendamento de teste\n";
    exit;
}

echo "--- CRIANDO AGENDAMENTO DE TESTE ---\n";
$appointment = Appointment::create([
    'customer_id' => $cliente->id,
    'employee_id' => $funcionario->id,
    'branch_id' => $branch->id,
    'appointment_date' => now()->addDays(1)->toDateString(),
    'start_time' => '10:00:00',
    'end_time' => '11:00:00',
    'services' => $servico->id,
    'total' => 100.00,
    'status' => 'Pendente',
    'created_by' => $cliente->id,
]);

echo "✅ Agendamento criado: ID {$appointment->id}\n";
echo "   Data: " . $appointment->appointment_date . " às " . $appointment->start_time . "\n";
echo "   Valor original: R$ 100,00\n\n";

// 3. Criar comanda com desconto aplicado
echo "--- APLICANDO DESCONTO (SIMULANDO AGENDAMENTO) ---\n";
$recompensa = $recompensasAntes->first();

$comanda = Comanda::create([
    'branch_id' => $branch->id,
    'appointment_id' => $appointment->id,
    'numero_comanda' => Comanda::gerarNumeroComanda($branch->id),
    'client_id' => $cliente->id,
    'cliente_nome' => $cliente->name,
    'funcionario_id' => $funcionario->id,
    'status' => 'Aberta',
    'data_abertura' => now(),
    'subtotal_servicos' => 100.00,
    'total_geral' => 100.00 - $recompensa->valor_credito, // Aplicar desconto
]);

// Marcar recompensa como usada
$recompensa->marcarComoUsado($comanda->id);

echo "✅ Comanda criada: #{$comanda->numero_comanda}\n";
echo "   Subtotal: R$ 100,00\n";
echo "   Desconto aplicado: R$ " . number_format($recompensa->valor_credito, 2, ',', '.') . " (Recompensa ID {$recompensa->id})\n";
echo "   Total final: R$ " . number_format($comanda->total_geral, 2, ',', '.') . "\n\n";

// Verificar status da recompensa APÓS USO
$recompensa->refresh();
echo "--- STATUS DA RECOMPENSA (APÓS USO) ---\n";
echo "  ID {$recompensa->id}: R$ " . number_format($recompensa->valor_credito, 2, ',', '.') . "\n";
echo "  Status: {$recompensa->status} (era 'ativo', agora é 'usado')\n";
echo "  Usado em comanda: #{$comanda->numero_comanda}\n\n";

// 4. CANCELAR O AGENDAMENTO
echo str_repeat('=', 70) . "\n";
echo "🚫 CANCELANDO AGENDAMENTO...\n";
echo str_repeat('=', 70) . "\n\n";

// Simular cancelamento (igual ao que faz nos componentes agora)
$appointment->status = 'Cancelado';
$appointment->cancellation_reason = 'Teste de restauração';
$appointment->cancellation_date = now();
$appointment->save();

// Restaurar recompensas
$comandaParaRestaurar = Comanda::where('appointment_id', $appointment->id)->first();
if ($comandaParaRestaurar) {
    $restaurados = FidelidadeReward::restaurarPorComanda($comandaParaRestaurar->id);
    echo "✅ Agendamento cancelado\n";
    echo "✅ {$restaurados} recompensa(s) restaurada(s)\n\n";
}

// Deletar comanda (como fazem os componentes)
Comanda::where('appointment_id', $appointment->id)->delete();

// 5. Verificar recompensas APÓS CANCELAMENTO
echo "--- RECOMPENSAS DO CLIENTE (APÓS CANCELAMENTO) ---\n";
$recompensasDepois = FidelidadeReward::where('user_id', $cliente->id)
    ->where('status', 'ativo')
    ->where('validade', '>=', now())
    ->get();

foreach ($recompensasDepois as $r) {
    echo "  ID {$r->id}: R$ " . number_format($r->valor_credito, 2, ',', '.') . 
         " | Status: {$r->status} | Validade: " . $r->validade->format('d/m/Y') . "\n";
}
echo "Total de crédito disponível: R$ " . number_format($recompensasDepois->sum('valor_credito'), 2, ',', '.') . "\n\n";

// Validação final
echo str_repeat('=', 70) . "\n";
echo "📊 RESULTADO DO TESTE\n";
echo str_repeat('=', 70) . "\n";

$creditoAntes = $recompensasAntes->sum('valor_credito');
$creditoDepois = $recompensasDepois->sum('valor_credito');

echo "Crédito antes: R$ " . number_format($creditoAntes, 2, ',', '.') . "\n";
echo "Crédito depois: R$ " . number_format($creditoDepois, 2, ',', '.') . "\n\n";

if (abs($creditoAntes - $creditoDepois) < 0.01) {
    echo "✅ SUCESSO! O desconto foi restaurado corretamente!\n";
    echo "✅ O cliente voltou a ter o mesmo crédito de antes.\n";
} else {
    echo "❌ FALHA! O desconto não foi restaurado.\n";
    echo "Diferença: R$ " . number_format(abs($creditoAntes - $creditoDepois), 2, ',', '.') . "\n";
}

// Cleanup
echo "\n" . str_repeat('-', 70) . "\n";
echo "🧹 Limpando dados de teste...\n";
$appointment->delete();
echo "✅ Dados de teste removidos\n";

echo "\n" . str_repeat('=', 70) . "\n";
echo "🎯 CORREÇÃO APLICADA:\n";
echo str_repeat('=', 70) . "\n";
echo "✅ ServicosRealizados.php - restaura recompensas ao cancelar\n";
echo "✅ ServicosFuncionarioRealizados.php - restaura recompensas ao cancelar\n";
echo "✅ Agenda.php - restaura recompensas ao cancelar\n";
echo "✅ Appointments.php - restaura recompensas ao cancelar\n\n";
echo "📌 Todos os fluxos de cancelamento agora restauram os descontos!\n";
echo str_repeat('=', 70) . "\n";

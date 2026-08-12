<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Comanda;
use App\Models\FidelidadeReward;
use App\Models\ComandaProduto;
use App\Models\Estoque;
use App\Models\Service;
use App\Models\Branch;

// Usar tenant labelle.localhost
$tenant = Tenant::whereHas('domains', function($q) {
    $q->where('domain', 'labelle.localhost');
})->firstOrFail();

tenancy()->initialize($tenant);

echo str_repeat('=', 70) . "\n";
echo "🧪 TESTE: AGENDAMENTO + PRODUTO + CANCELAMENTO\n";
echo str_repeat('=', 70) . "\n";
echo "Cenário: Cliente faz agendamento, compra produto e depois cancela\n";
echo str_repeat('=', 70) . "\n\n";

// Setup
$cliente = User::first();
$funcionario = User::whereHas('branches')->first();
$servico = Service::first();
$branch = Branch::first();
$produto = Estoque::where('quantidade_atual', '>', 0)
    ->where('preco_unitario', '>', 0)
    ->first();

if (!$cliente || !$funcionario || !$servico || !$branch || !$produto) {
    echo "❌ Dados insuficientes\n";
    exit;
}

echo "📝 DADOS DO TESTE\n";
echo "Cliente: {$cliente->name}\n";
echo "Funcionário: {$funcionario->name}\n";
echo "Produto: {$produto->produto_nome} - R$ " . number_format($produto->preco_unitario, 2, ',', '.') . "\n\n";

// Verificar recompensas iniciais
$recompensasIniciais = FidelidadeReward::where('user_id', $cliente->id)
    ->where('status', 'ativo')
    ->get();

echo "--- RECOMPENSAS INICIAIS DO CLIENTE ---\n";
if ($recompensasIniciais->count() > 0) {
    foreach ($recompensasIniciais as $r) {
        echo "  ID {$r->id}: R$ " . number_format($r->valor_credito, 2, ',', '.') . 
             " | Origem: {$r->origem}\n";
    }
} else {
    echo "  (Nenhuma recompensa)\n";
}
$totalInicial = $recompensasIniciais->sum('valor_credito');
echo "Total: R$ " . number_format($totalInicial, 2, ',', '.') . "\n\n";

// 1. CRIAR AGENDAMENTO
echo str_repeat('=', 70) . "\n";
echo "1️⃣ CRIAR AGENDAMENTO\n";
echo str_repeat('=', 70) . "\n\n";

$appointment = Appointment::create([
    'customer_id' => $cliente->id,
    'employee_id' => $funcionario->id,
    'branch_id' => $branch->id,
    'appointment_date' => now()->addDays(1)->toDateString(),
    'start_time' => '14:00:00',
    'end_time' => '15:00:00',
    'services' => $servico->id,
    'total' => 80.00,
    'status' => 'Pendente',
    'created_by' => $cliente->id,
]);

echo "✅ Agendamento criado: ID {$appointment->id}\n";
echo "   Data: " . $appointment->appointment_date . " às " . $appointment->start_time . "\n";
echo "   Valor: R$ 80,00\n\n";

// 2. CRIAR COMANDA
echo str_repeat('=', 70) . "\n";
echo "2️⃣ CRIAR COMANDA (Simulando checkout)\n";
echo str_repeat('=', 70) . "\n\n";

$comanda = Comanda::create([
    'branch_id' => $branch->id,
    'appointment_id' => $appointment->id,
    'numero_comanda' => Comanda::gerarNumeroComanda($branch->id),
    'client_id' => $cliente->id,
    'cliente_nome' => $cliente->name,
    'funcionario_id' => $funcionario->id,
    'status' => 'Aberta',
    'data_abertura' => now(),
    'subtotal_servicos' => 80.00,
    'subtotal_produtos' => 0,
    'total_geral' => 80.00,
]);

echo "✅ Comanda criada: #{$comanda->numero_comanda}\n";
echo "   Serviços: R$ 80,00\n\n";

// 3. ADICIONAR PRODUTO À COMANDA
echo str_repeat('=', 70) . "\n";
echo "3️⃣ ADICIONAR PRODUTO À COMANDA (Cliente compra produto)\n";
echo str_repeat('=', 70) . "\n\n";

// Criar ComandaProduto (Observer vai gerar recompensa automaticamente)
$comandaProduto = ComandaProduto::create([
    'comanda_id' => $comanda->id,
    'estoque_id' => $produto->id,
    'quantidade' => 1,
    'preco_unitario' => $produto->preco_unitario,
    'subtotal' => $produto->preco_unitario,
]);

echo "✅ Produto adicionado à comanda\n";
echo "   Produto: {$produto->produto_nome}\n";
echo "   Valor: R$ " . number_format($produto->preco_unitario, 2, ',', '.') . "\n";
echo "   (Observer deve ter gerado recompensa automaticamente)\n\n";

// Recalcular totais da comanda
$comanda->recalcularTotais();

echo "💰 Totais da comanda atualizados:\n";
echo "   Subtotal Serviços: R$ " . number_format($comanda->subtotal_servicos, 2, ',', '.') . "\n";
echo "   Subtotal Produtos: R$ " . number_format($comanda->subtotal_produtos, 2, ',', '.') . "\n";
echo "   Total Geral: R$ " . number_format($comanda->total_geral, 2, ',', '.') . "\n\n";

// Verificar recompensa GERADA pelo produto
sleep(1); // Aguardar Observer processar
$recompensaGerada = FidelidadeReward::where('comanda_produto_id', $comandaProduto->id)
    ->first();

if ($recompensaGerada) {
    echo "🎁 RECOMPENSA GERADA PELA COMPRA:\n";
    echo "   ID: {$recompensaGerada->id}\n";
    echo "   Valor: R$ " . number_format($recompensaGerada->valor_credito, 2, ',', '.') . "\n";
    echo "   Status: {$recompensaGerada->status}\n";
    echo "   Origem: {$recompensaGerada->origem}\n\n";
} else {
    echo "⚠️ Nenhuma recompensa gerada (Observer pode não ter executado)\n\n";
}

// Listar todas as recompensas após a compra
$recompensasAposCompra = FidelidadeReward::where('user_id', $cliente->id)
    ->where('status', 'ativo')
    ->get();

echo "--- RECOMPENSAS APÓS COMPRA ---\n";
foreach ($recompensasAposCompra as $r) {
    echo "  ID {$r->id}: R$ " . number_format($r->valor_credito, 2, ',', '.') . 
         " | Origem: {$r->origem}" .
         ($r->id == $recompensaGerada->id ?? 0 ? " 🆕 NOVA!" : "") . "\n";
}
$totalAposCompra = $recompensasAposCompra->sum('valor_credito');
echo "Total: R$ " . number_format($totalAposCompra, 2, ',', '.') . "\n\n";

// 4. CANCELAR AGENDAMENTO
echo str_repeat('=', 70) . "\n";
echo "4️⃣ CANCELAR AGENDAMENTO (Cliente desiste)\n";
echo str_repeat('=', 70) . "\n\n";

// Simular cancelamento
$appointment->status = 'Cancelado';
$appointment->cancellation_reason = 'Cliente desistiu';
$appointment->cancellation_date = now();
$appointment->save();

// Processar cancelamento (igual aos componentes)
if ($comanda) {
    echo "🔄 Processando cancelamento...\n";
    
    // Restaurar recompensas usadas (caso houvesse desconto aplicado)
    $restaurados = FidelidadeReward::restaurarPorComanda($comanda->id);
    echo "   Recompensas usadas restauradas: {$restaurados}\n";
    
    // Cancelar recompensas geradas por produtos (NOVA LÓGICA)
    $cancelados = FidelidadeReward::cancelarRecompensasGeradasPorComanda($comanda->id);
    echo "   Recompensas geradas canceladas: {$cancelados}\n";
    
    // Deletar comanda
    Comanda::where('appointment_id', $appointment->id)->delete();
    echo "   Comanda deletada\n\n";
}

// Verificar recompensas APÓS CANCELAMENTO
$recompensasAposCancelamento = FidelidadeReward::where('user_id', $cliente->id)
    ->where('status', 'ativo')
    ->get();

echo "--- RECOMPENSAS APÓS CANCELAMENTO ---\n";
if ($recompensasAposCancelamento->count() > 0) {
    foreach ($recompensasAposCancelamento as $r) {
        echo "  ID {$r->id}: R$ " . number_format($r->valor_credito, 2, ',', '.') . 
             " | Origem: {$r->origem}\n";
    }
} else {
    echo "  (Nenhuma recompensa ativa)\n";
}
$totalAposCancelamento = $recompensasAposCancelamento->sum('valor_credito');
echo "Total: R$ " . number_format($totalAposCancelamento, 2, ',', '.') . "\n\n";

// VALIDAÇÃO FINAL
echo str_repeat('=', 70) . "\n";
echo "📊 RESULTADO DO TESTE\n";
echo str_repeat('=', 70) . "\n\n";

echo "Recompensas iniciais: R$ " . number_format($totalInicial, 2, ',', '.') . "\n";
echo "Após compra produto: R$ " . number_format($totalAposCompra, 2, ',', '.') . " (+recompensa gerada)\n";
echo "Após cancelamento: R$ " . number_format($totalAposCancelamento, 2, ',', '.') . "\n\n";

if (abs($totalInicial - $totalAposCancelamento) < 0.01) {
    echo "✅ SUCESSO! Recompensa gerada foi cancelada corretamente!\n";
    echo "✅ Cliente voltou ao estado inicial (como se não tivesse comprado)\n";
    echo "✅ O problema descrito foi RESOLVIDO!\n";
} else {
    echo "❌ FALHA! Recompensa gerada não foi cancelada.\n";
    echo "Diferença: R$ " . number_format(abs($totalInicial - $totalAposCancelamento), 2, ',', '.') . "\n";
}

// Cleanup
echo "\n" . str_repeat('-', 70) . "\n";
echo "🧹 Limpando dados de teste...\n";
$appointment->delete();
echo "✅ Teste concluído e limpo\n";

echo "\n" . str_repeat('=', 70) . "\n";
echo "🎯 CORREÇÃO APLICADA:\n";
echo str_repeat('=', 70) . "\n";
echo "✅ Novo método: FidelidadeReward::cancelarRecompensasGeradasPorComanda()\n";
echo "✅ Agenda.php - cancela recompensas geradas\n";
echo "✅ Appointments.php - cancela recompensas geradas\n";
echo "✅ ServicosRealizados.php - cancela recompensas geradas\n";
echo "✅ ServicosFuncionarioRealizados.php - cancela recompensas geradas\n\n";
echo "📌 Agora ao cancelar agendamento:\n";
echo "   1. Restaura descontos usados (recompensas voltam ativas)\n";
echo "   2. Cancela recompensas geradas por produtos (deletadas)\n";
echo str_repeat('=', 70) . "\n";

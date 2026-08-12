<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use App\Models\Appointment;
use App\Models\Comanda;
use App\Models\Estoque;
use App\Models\ComandaProduto;
use App\Models\FidelidadeReward;

// Usar tenant labelle.localhost
$tenant = Tenant::whereHas('domains', function($q) {
    $q->where('domain', 'labelle.localhost');
})->firstOrFail();

tenancy()->initialize($tenant);

echo "🏢 Tenant: {$tenant->id}\n";
echo "📊 Rewards antes: " . FidelidadeReward::count() . "\n\n";

// Pegar um appointment existente que tenha customer
$appointment = Appointment::with('customer')->whereNotNull('customer_id')->first();

if (!$appointment) {
    echo "❌ Nenhum appointment com customer encontrado\n";
    exit(1);
}

echo "📅 Appointment ID: {$appointment->id}\n";
echo "👤 Customer ID: {$appointment->customer_id}\n";
echo "👤 Customer Nome: {$appointment->customer->name}\n\n";

// Criar comanda usando o método que acabamos de corrigir
echo "📋 Criando comanda através de criarDeAgendamento...\n";
$comanda = Comanda::criarDeAgendamento($appointment);

echo "✅ Comanda criada! ID: {$comanda->id}\n";
echo "   - client_id: {$comanda->client_id}\n";
echo "   - cliente_nome: {$comanda->cliente_nome}\n\n";

// Pegar um produto em estoque
$produto = Estoque::where('quantidade_atual', '>', 0)->first();

if (!$produto) {
    echo "❌ Nenhum produto em estoque encontrado\n";
    exit(1);
}

echo "🛍️ Produto selecionado:\n";
echo "   - ID: {$produto->id}\n";
echo "   - Nome: {$produto->produto_nome}\n";
echo "   - Preço: R$ " . number_format($produto->preco_unitario, 2, ',', '.') . "\n\n";

// Adicionar produto à comanda (isso deve disparar o Observer)
echo "➕ Adicionando produto à comanda...\n";
$comandaProduto = ComandaProduto::create([
    'comanda_id' => $comanda->id,
    'estoque_id' => $produto->id,
    'quantidade' => 1,
    'preco_unitario' => $produto->preco_unitario,
    'subtotal' => $produto->preco_unitario,
    'percentual_produtos' => $produto->percentual_produtos ?? 0,
]);

echo "✅ ComandaProduto criado! ID: {$comandaProduto->id}\n\n";

// Verificar se a recompensa foi criada
sleep(1); // Dar tempo para o Observer processar

$rewards = FidelidadeReward::where('user_id', $appointment->customer_id)
    ->latest()
    ->get();

echo "📊 Rewards após: " . FidelidadeReward::count() . "\n";
echo "📊 Rewards do cliente: " . $rewards->count() . "\n\n";

if ($rewards->count() > 0) {
    echo "🎉 SUCESSO! Recompensa criada:\n";
    foreach ($rewards as $reward) {
        echo "   - ID: {$reward->id}\n";
        echo "   - Valor: R$ " . number_format($reward->valor_credito, 2, ',', '.') . "\n";
        echo "   - Status: {$reward->status}\n";
        echo "   - Validade: " . $reward->validade->format('d/m/Y') . "\n";
        echo "   - Origem: Comanda #{$reward->comanda_id}\n";
        
        // Calcular porcentagem
        $porcentagem = ($reward->valor_credito / $produto->preco_unitario) * 100;
        echo "   - Porcentagem: " . number_format($porcentagem, 0) . "%\n\n";
    }
} else {
    echo "❌ FALHOU! Nenhuma recompensa criada\n";
    echo "Verificando dados da comanda:\n";
    $comanda->refresh();
    echo "   - Comanda ID: {$comanda->id}\n";
    echo "   - client_id: {$comanda->client_id}\n";
    echo "   - Cliente existe? " . ($comanda->client_id ? 'SIM' : 'NÃO') . "\n";
}

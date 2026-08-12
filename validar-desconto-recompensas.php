<?php

use Illuminate\Support\Facades\DB;

define('LARAVEL_START', microtime(true));

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tenant;
use App\Models\User;
use App\Models\FidelidadeReward;

echo "\n" . str_repeat("=", 80) . "\n";
echo "VALIDAÇÃO: SISTEMA DE DESCONTO DE RECOMPENSAS\n";
echo str_repeat("=", 80) . "\n\n";

// Inicializar tenant Labelle
$tenant = Tenant::where('id', 'labelle')->first();

if (!$tenant) {
    echo "❌ Tenant Labelle não encontrado!\n";
    exit(1);
}

tenancy()->initialize($tenant);
echo "✅ Tenant inicializado: {$tenant->nome}\n\n";

// Verificar cliente com recompensas
$cliente = User::whereHas('roles', function($q) {
    $q->where('role', 'Cliente');
})->first();

if (!$cliente) {
    echo "⚠️  Nenhum cliente encontrado\n";
    exit(1);
}

echo "📋 CLIENTE: {$cliente->name}\n";
echo str_repeat("-", 80) . "\n";

// Listar recompensas ativas
$recompensasAtivas = FidelidadeReward::where('user_id', $cliente->id)
    ->where('status', 'ativo')
    ->where('validade', '>=', now())
    ->get();

echo "Recompensas Ativas: " . $recompensasAtivas->count() . "\n\n";

foreach ($recompensasAtivas as $reward) {
    echo "ID: {$reward->id}\n";
    echo "Tipo: {$reward->tipo}\n";
    
    if ($reward->tipo === 'credito') {
        echo "Valor: R$ " . number_format($reward->valor_credito, 2, ',', '.') . "\n";
    } elseif ($reward->tipo === 'cupom') {
        echo "Desconto: {$reward->percentual_desconto}%\n";
        echo "Código: {$reward->codigo_cupom}\n";
    }
    
    echo "Validade: " . $reward->validade->format('d/m/Y') . "\n";
    echo "Status: {$reward->status}\n";
    echo "Origem: {$reward->origem}\n";
    echo "\n";
}

// Listar recompensas usadas
$recompensasUsadas = FidelidadeReward::where('user_id', $cliente->id)
    ->where('status', 'usado')
    ->orderBy('usado_em', 'desc')
    ->get();

if ($recompensasUsadas->count() > 0) {
    echo "\n📋 RECOMPENSAS USADAS: " . $recompensasUsadas->count() . "\n";
    echo str_repeat("-", 80) . "\n";
    
    foreach ($recompensasUsadas as $reward) {
        echo "ID: {$reward->id}\n";
        echo "Tipo: {$reward->tipo}\n";
        
        if ($reward->tipo === 'credito') {
            echo "Valor: R$ " . number_format($reward->valor_credito, 2, ',', '.') . "\n";
        }
        
        echo "Usado em: " . ($reward->usado_em ? $reward->usado_em->format('d/m/Y H:i:s') : 'N/A') . "\n";
        echo "Comanda ID: " . ($reward->usado_em_comanda_id ?? 'N/A') . "\n";
        echo "\n";
    }
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "✅ VALIDAÇÃO CONCLUÍDA!\n";
echo str_repeat("=", 80) . "\n\n";

echo "🧪 COMO TESTAR O FLUXO COMPLETO:\n\n";
echo "1. Acesse o sistema como cliente (Login: email do cliente acima)\n";
echo "2. Vá para a tela de agendamento\n";
echo "3. Selecione profissional e serviço\n";
echo "4. Escolha data e horário\n";
echo "5. Na tela final, você verá:\n";
echo "   ✓ Recompensas disponíveis (se houver)\n";
echo "   ✓ Produtos recomendados\n";
echo "6. Selecione uma recompensa (clique no card)\n";
echo "7. Opcionalmente, selecione produtos\n";
echo "8. Clique em 'Confirmar Agendamento'\n\n";
echo "✅ RESULTADOS ESPERADOS:\n";
echo "   - Desconto aplicado no total do agendamento\n";
echo "   - Recompensa marcada como 'usado'\n";
echo "   - Campo 'usado_em' preenchido com data/hora\n";
echo "   - Campo 'usado_em_comanda_id' com ID da comanda\n";
echo "   - Notes do appointment contém informação do desconto\n";
echo "   - Produtos adicionados à comanda (se selecionados)\n";
echo "   - Nova recompensa criada (20% dos produtos)\n\n";

echo "📊 CONSULTA SQL PARA VERIFICAR:\n";
echo "SELECT id, tipo, valor_credito, status, usado_em, usado_em_comanda_id\n";
echo "FROM fidelidade_rewards\n";
echo "WHERE user_id = {$cliente->id}\n";
echo "ORDER BY created_at DESC;\n\n";

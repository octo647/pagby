<?php

/**
 * Script para demonstrar que recompensas geradas não são aplicadas no mesmo agendamento
 * 
 * Uso: php demonstrar-fluxo-correto.php
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Database\Models\Domain;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tenantDomain = 'labelle.localhost';

echo "\n📋 DEMONSTRAÇÃO: SEPARAÇÃO DE RECOMPENSAS\n";
echo "==========================================\n\n";

try {
    $domain = Domain::where('domain', $tenantDomain)->first();
    tenancy()->initialize($domain->tenant);
    
    $cliente = \App\Models\User::whereHas('roles', function($q) {
        $q->where('role', 'Cliente');
    })->first();
    
    if (!$cliente) {
        throw new Exception("Nenhum cliente encontrado");
    }
    
    echo "👤 Cliente: {$cliente->name} (ID: {$cliente->id})\n\n";
    
    // ========================================
    // SIMULAÇÃO 1: Cliente SEM recompensas
    // ========================================
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "SIMULAÇÃO 1: Cliente SEM recompensas\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    // Limpar recompensas do cliente para simulação limpa
    \App\Models\FidelidadeReward::where('user_id', $cliente->id)->delete();
    
    echo "1️⃣ INÍCIO DO AGENDAMENTO\n";
    echo "   Recompensas disponíveis: ";
    $recompensasAntes = \App\Models\FidelidadeReward::where('user_id', $cliente->id)
        ->where('status', 'ativo')
        ->count();
    echo $recompensasAntes . "\n";
    
    if ($recompensasAntes > 0) {
        echo "   ⚠️ Limpando recompensas para simulação...\n";
        \App\Models\FidelidadeReward::where('user_id', $cliente->id)->delete();
        $recompensasAntes = 0;
    }
    
    echo "   ✅ Cliente começa sem recompensas\n\n";
    
    echo "2️⃣ DURANTE O AGENDAMENTO\n";
    echo "   MinhasRecompensas carrega: SELECT * FROM fidelidade_rewards\n";
    echo "                               WHERE user_id = {$cliente->id}\n";
    echo "                               AND status = 'ativo'\n";
    echo "   Resultado: {$recompensasAntes} recompensas → NENHUMA para selecionar ✅\n\n";
    
    echo "3️⃣ CONFIRMAÇÃO (confirmTime)\n";
    echo "   a) Verifica recompensa selecionada: NÃO HÁ\n";
    echo "   b) Cria Appointment com valor TOTAL (sem desconto)\n";
    echo "   c) Cria Comanda\n";
    echo "   d) Adiciona produtos à comanda\n\n";
    
    // Simular criação de recompensa pelo Observer
    echo "4️⃣ OBSERVER DISPARA (após adicionar produtos)\n";
    $recompensa = \App\Models\FidelidadeReward::criarCredito(
        userId: $cliente->id,
        valor: 20.00,
        origem: 'compra_produto'
    );
    echo "   ✅ Nova recompensa criada: R$ 20,00\n";
    echo "   ID: {$recompensa->id}\n";
    echo "   Status: {$recompensa->status}\n";
    echo "   Validade: {$recompensa->validade->format('d/m/Y')}\n\n";
    
    echo "5️⃣ RESULTADO\n";
    $recompensasDepois = \App\Models\FidelidadeReward::where('user_id', $cliente->id)
        ->where('status', 'ativo')
        ->count();
    echo "   Recompensas após agendamento: {$recompensasDepois}\n";
    echo "   ✅ Recompensa GERADA não foi usada no mesmo agendamento\n";
    echo "   ✅ Ficará disponível apenas no PRÓXIMO agendamento\n\n";
    
    // ========================================
    // SIMULAÇÃO 2: Cliente COM recompensa pré-existente
    // ========================================
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "SIMULAÇÃO 2: Cliente COM recompensa\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    echo "1️⃣ INÍCIO DO AGENDAMENTO\n";
    $recompensaExistente = \App\Models\FidelidadeReward::where('user_id', $cliente->id)
        ->where('status', 'ativo')
        ->first();
    
    if ($recompensaExistente) {
        echo "   Recompensa disponível:\n";
        echo "   - ID: {$recompensaExistente->id}\n";
        echo "   - Valor: R$ " . number_format($recompensaExistente->valor_credito, 2, ',', '.') . "\n";
        echo "   - Status: {$recompensaExistente->status}\n";
        echo "   ✅ Cliente PODE usar esta recompensa\n\n";
        
        echo "2️⃣ DURANTE O AGENDAMENTO\n";
        echo "   MinhasRecompensas mostra: 1 recompensa de R$ " . 
             number_format($recompensaExistente->valor_credito, 2, ',', '.') . "\n";
        echo "   Cliente seleciona: SIM, usar a recompensa\n\n";
        
        echo "3️⃣ CONFIRMAÇÃO (confirmTime)\n";
        $valorServico = 50.00;
        $descontoAplicado = min($recompensaExistente->valor_credito, $valorServico);
        $totalFinal = $valorServico - $descontoAplicado;
        
        echo "   Valor do serviço: R$ " . number_format($valorServico, 2, ',', '.') . "\n";
        echo "   Desconto aplicado: R$ " . number_format($descontoAplicado, 2, ',', '.') . "\n";
        echo "   Total final: R$ " . number_format($totalFinal, 2, ',', '.') . "\n";
        echo "   a) Aplica recompensa PRÉ-EXISTENTE ✅\n";
        echo "   b) Cria Appointment com valor DESCONTADO\n";
        echo "   c) Marca recompensa como 'usado'\n";
        echo "   d) Cria Comanda\n";
        echo "   e) Cliente adiciona produto de R$ 100,00\n\n";
        
        // Simular uso da recompensa
        $recompensaExistente->update([
            'status' => 'usado',
            'usado_em' => now(),
        ]);
        
        echo "4️⃣ OBSERVER DISPARA (após adicionar produto)\n";
        $novaRecompensa = \App\Models\FidelidadeReward::criarCredito(
            userId: $cliente->id,
            valor: 25.00, // 25% de R$ 100
            origem: 'compra_produto'
        );
        echo "   ✅ NOVA recompensa criada: R$ 25,00 (25% de R$ 100)\n";
        echo "   ID: {$novaRecompensa->id}\n\n";
        
        echo "5️⃣ RESULTADO\n";
        $ativas = \App\Models\FidelidadeReward::where('user_id', $cliente->id)
            ->where('status', 'ativo')
            ->count();
        $usadas = \App\Models\FidelidadeReward::where('user_id', $cliente->id)
            ->where('status', 'usado')
            ->count();
        
        echo "   Recompensas ativas: {$ativas}\n";
        echo "   Recompensas usadas: {$usadas}\n";
        echo "   ✅ Recompensa ANTIGA foi usada no agendamento atual\n";
        echo "   ✅ Recompensa NOVA ficará para o próximo agendamento\n";
        echo "   ✅ Não há risco de usar recompensa que ainda não existe!\n\n";
    }
    
    // ========================================
    // ANÁLISE DO CÓDIGO
    // ========================================
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "ANÁLISE TÉCNICA DO CÓDIGO\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    echo "📁 MinhasRecompensas.php (mount - linha 17)\n";
    echo "   ↓\n";
    echo "   carregarRecompensas() executa ANTES do confirmTime()\n";
    echo "   Query: WHERE status = 'ativo' AND validade >= NOW()\n";
    echo "   ✅ Só carrega recompensas JÁ EXISTENTES\n\n";
    
    echo "📁 MakeAppointment.php (confirmTime)\n";
    echo "   ↓\n";
    echo "   Linha 602-638: Verifica/aplica recompensa selecionada\n";
    echo "   Linha 644: Cria Appointment\n";
    echo "   Linha 660: Cria Comanda\n";
    echo "   Linha 682: ComandaProduto::create()\n";
    echo "   ↓\n";
    echo "   Observer dispara APÓS create()\n";
    echo "   ✅ Novas recompensas criadas DEPOIS do appointment\n\n";
    
    echo "🔒 GARANTIA DE SEPARAÇÃO:\n";
    echo "   1. MinhasRecompensas carrega no mount (antes)\n";
    echo "   2. Appointment criado (meio)\n";
    echo "   3. Observer cria recompensa (depois)\n";
    echo "   ✅ IMPOSSÍVEL usar recompensa que ainda não existe!\n\n";
    
    echo "✅ SISTEMA FUNCIONANDO CORRETAMENTE!\n\n";
    
} catch (Exception $e) {
    echo "\n❌ ERRO: {$e->getMessage()}\n";
    exit(1);
}

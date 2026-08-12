<?php

/**
 * Script para testar restauração de recompensas quando agendamento é cancelado
 * 
 * Uso: php testar-restauracao-recompensas.php
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Database\Models\Domain;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Tenant para teste
$tenantDomain = 'labelle.localhost';

echo "\n🔍 TESTE DE RESTAURAÇÃO DE RECOMPENSAS\n";
echo "Tenant: {$tenantDomain}\n\n";

try {
    // Inicializar tenancy
    $domain = Domain::where('domain', $tenantDomain)->first();
    if (!$domain) {
        throw new Exception("Domínio '{$tenantDomain}' não encontrado");
    }
    
    tenancy()->initialize($domain->tenant);
    echo "✅ Tenancy inicializado\n\n";
    
    // 1. Buscar recompensa usada (se existir)
    echo "1️⃣ BUSCANDO RECOMPENSA USADA...\n";
    $recompensaUsada = \App\Models\FidelidadeReward::where('status', 'usado')->first();
    
    if ($recompensaUsada) {
        echo "   ✅ Encontrada recompensa usada:\n";
        echo "      ID: {$recompensaUsada->id}\n";
        echo "      Tipo: {$recompensaUsada->tipo}\n";
        echo "      Valor: R$ " . number_format($recompensaUsada->valor_credito ?? 0, 2, ',', '.') . "\n";
        echo "      Usado em: {$recompensaUsada->usado_em}\n";
        echo "      Comanda ID: {$recompensaUsada->usado_em_comanda_id}\n";
        echo "      Validade: {$recompensaUsada->validade}\n\n";
        
        // 2. Testar restauração
        echo "2️⃣ TESTANDO RESTAURAÇÃO...\n";
        $resultado = $recompensaUsada->restaurar();
        
        if ($resultado) {
            echo "   ✅ Recompensa restaurada com sucesso!\n";
            
            // Recarregar para ver os novos valores
            $recompensaUsada->refresh();
            echo "      Novo status: {$recompensaUsada->status}\n";
            echo "      Usado em: " . ($recompensaUsada->usado_em ?? 'null') . "\n";
            echo "      Comanda ID: " . ($recompensaUsada->usado_em_comanda_id ?? 'null') . "\n\n";
        } else {
            echo "   ⚠️ Restauração retornou false\n";
            $recompensaUsada->refresh();
            echo "      Status atual: {$recompensaUsada->status}\n";
            if ($recompensaUsada->validade < now()->toDate()) {
                echo "      Motivo: Recompensa expirada\n\n";
            }
        }
    } else {
        echo "   ℹ️ Nenhuma recompensa 'usada' encontrada para teste\n";
        echo "      Buscando recompensa ativa para simular uso...\n\n";
        
        $recompensaAtiva = \App\Models\FidelidadeReward::where('status', 'ativo')->first();
        
        if ($recompensaAtiva) {
            echo "   ✅ Encontrada recompensa ativa:\n";
            echo "      ID: {$recompensaAtiva->id}\n";
            echo "      Tipo: {$recompensaAtiva->tipo}\n";
            echo "      Valor: R$ " . number_format($recompensaAtiva->valor_credito ?? 0, 2, ',', '.') . "\n\n";
            
            // Simular uso
            echo "   📝 Simulando uso da recompensa...\n";
            $recompensaAtiva->update([
                'status' => 'usado',
                'usado_em' => now(),
                'usado_em_comanda_id' => 999, // ID fictício para teste
            ]);
            echo "      Status alterado para 'usado'\n\n";
            
            // Testar restauração
            echo "   🔄 Testando restauração...\n";
            $resultado = $recompensaAtiva->restaurar();
            
            if ($resultado) {
                echo "      ✅ Recompensa restaurada com sucesso!\n";
                $recompensaAtiva->refresh();
                echo "      Novo status: {$recompensaAtiva->status}\n\n";
            } else {
                echo "      ❌ Falha na restauração\n\n";
            }
        } else {
            echo "   ⚠️ Nenhuma recompensa disponível para teste\n\n";
        }
    }
    
    // 3. Testar método estático restaurarPorComanda
    echo "3️⃣ TESTANDO RESTAURAÇÃO POR COMANDA...\n";
    
    // Buscar comanda com appointment
    $comanda = \App\Models\Comanda::whereNotNull('appointment_id')
        ->where('status', '!=', 'Cancelada')
        ->first();
    
    if ($comanda) {
        echo "   ✅ Comanda encontrada:\n";
        echo "      ID: {$comanda->id}\n";
        echo "      Número: {$comanda->numero_comanda}\n";
        echo "      Status: {$comanda->status}\n";
        echo "      Appointment ID: {$comanda->appointment_id}\n\n";
        
        // Verificar se há recompensas usadas nesta comanda
        $recompensasNaComanda = \App\Models\FidelidadeReward::where('usado_em_comanda_id', $comanda->id)
            ->where('status', 'usado')
            ->count();
        
        echo "      Recompensas usadas nesta comanda: {$recompensasNaComanda}\n\n";
        
        if ($recompensasNaComanda > 0) {
            echo "   🔄 Testando restauração em massa...\n";
            $restaurados = \App\Models\FidelidadeReward::restaurarPorComanda($comanda->id);
            echo "      ✅ {$restaurados} recompensa(s) restaurada(s)!\n\n";
        } else {
            echo "      ℹ️ Nenhuma recompensa para restaurar nesta comanda\n\n";
        }
    } else {
        echo "   ℹ️ Nenhuma comanda disponível para teste\n\n";
    }
    
    // 4. Estatísticas gerais
    echo "4️⃣ ESTATÍSTICAS GERAIS\n";
    $totalRecompensas = \App\Models\FidelidadeReward::count();
    $ativas = \App\Models\FidelidadeReward::where('status', 'ativo')->count();
    $usadas = \App\Models\FidelidadeReward::where('status', 'usado')->count();
    $expiradas = \App\Models\FidelidadeReward::where('status', 'expirado')->count();
    
    echo "   Total de recompensas: {$totalRecompensas}\n";
    echo "   Ativas: {$ativas}\n";
    echo "   Usadas: {$usadas}\n";
    echo "   Expiradas: {$expiradas}\n\n";
    
    echo "✅ TESTE CONCLUÍDO!\n\n";
    
} catch (Exception $e) {
    echo "\n❌ ERRO: {$e->getMessage()}\n";
    echo "Arquivo: {$e->getFile()}:{$e->getLine()}\n\n";
    exit(1);
}

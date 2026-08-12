<?php
/**
 * Script rápido para listar cobranças da subconta via API
 */

require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

echo "════════════════════════════════════════════════════════\n";
echo "📊 LISTANDO COBRANÇAS DA SUBCONTA\n";
echo "════════════════════════════════════════════════════════\n\n";

// Buscar tenant de teste e sua API key
echo "🔍 Buscando tenant teste1772829838...\n";
$tenant = DB::connection('mysql')->table('tenants')
    ->where('id', 'teste1772829838')
    ->first();

if (!$tenant) {
    echo "❌ Tenant não encontrado!\n";
    exit(1);
}

if (!$tenant->asaas_api_key) {
    echo "❌ Tenant não tem API key da subconta!\n";
    exit(1);
}

// Descriptografar API key
$subcontaApiKey = Crypt::decryptString($tenant->asaas_api_key);
$accountId = $tenant->asaas_account_id;

echo "✅ Tenant encontrado: {$tenant->name}\n";
echo "   Account ID: {$accountId}\n";
echo "   API Key: " . substr($subcontaApiKey, 0, 30) . "...\n\n";

$apiUrl = 'https://sandbox.asaas.com/api/v3';

// Listar cobranças (todas)
echo "📋 Listando todas as cobranças...\n";
$response = Http::withHeaders([
    'access_token' => $subcontaApiKey,
    'Content-Type' => 'application/json',
])->get($apiUrl . '/payments', [
    'limit' => 50,
    'offset' => 0,
]);

if ($response->successful()) {
    $data = $response->json();
    $cobranças = $data['data'] ?? [];
    
    echo "✅ Total de cobranças: " . count($cobranças) . "\n\n";
    
    foreach ($cobranças as $cobranca) {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "ID: " . $cobranca['id'] . "\n";
        echo "Valor: R$ " . number_format($cobranca['value'], 2, ',', '.') . "\n";
        echo "Tipo: " . $cobranca['billingType'] . "\n";
        echo "Status: " . $cobranca['status'] . "\n";
        echo "Vencimento: " . $cobranca['dueDate'] . "\n";
        echo "Cliente: " . $cobranca['customer'] . "\n";
        echo "Descrição: " . ($cobranca['description'] ?? 'N/A') . "\n";
        
        // CAMPO CRÍTICO - não deve existir quando consultado pela própria subconta
        if (isset($cobranca['account'])) {
            echo "⚠️  Campo 'account': " . $cobranca['account'] . "\n";
        } else {
            echo "✅ Campo 'account': (ausente - esperado quando autenticado como subconta)\n";
        }
        
        echo "Invoice URL: " . ($cobranca['invoiceUrl'] ?? 'N/A') . "\n";
        echo "\n";
    }
    
    if (count($cobranças) === 0) {
        echo "⚠️  Nenhuma cobrança encontrada.\n\n";
        
        // Tentar listar customers para verificar se a conta está funcionando
        echo "🔍 Verificando se há customers cadastrados...\n";
        $customersResponse = Http::withHeaders([
            'access_token' => $subcontaApiKey,
            'Content-Type' => 'application/json',
        ])->get($apiUrl . '/customers', ['limit' => 10]);
        
        if ($customersResponse->successful()) {
            $customers = $customersResponse->json()['data'] ?? [];
            echo "   Customers encontrados: " . count($customers) . "\n";
            
            if (count($customers) > 0) {
                echo "\n   📋 Últimos customers:\n";
                foreach ($customers as $customer) {
                    echo "      • {$customer['name']} (ID: {$customer['id']})\n";
                }
                
                echo "\n   💡 Customers existem mas sem cobranças.\n";
                echo "      Possível causa: Cobranças podem ter sido criadas em outro momento/conta.\n";
            }
        }
        
        // Tentar listar cobranças criadas hoje
        echo "\n🔍 Verificando cobranças criadas hoje (2026-03-06)...\n";
        $todayPaymentsResponse = Http::withHeaders([
            'access_token' => $subcontaApiKey,
            'Content-Type' => 'application/json',
        ])->get($apiUrl . '/payments', [
            'dateCreated[ge]' => '2026-03-06',
            'dateCreated[le]' => '2026-03-06',
            'limit' => 50,
        ]);
        
        if ($todayPaymentsResponse->successful()) {
            $todayPayments = $todayPaymentsResponse->json()['data'] ?? [];
            echo "   Cobranças de hoje: " . count($todayPayments) . "\n";
            
            if (count($todayPayments) > 0) {
                echo "\n   ✅ ENCONTRADAS COBRANÇAS DE HOJE!\n";
                foreach ($todayPayments as $payment) {
                    echo "      • ID: {$payment['id']} | R$ {$payment['value']} | {$payment['status']}\n";
                }
            }
        }
    }
    
} else {
    echo "❌ Erro ao consultar cobranças\n";
    echo "Status: " . $response->status() . "\n";
    echo "Resposta: " . $response->body() . "\n";
}

echo "\n════════════════════════════════════════════════════════\n";
echo "💡 INTERPRETAÇÃO:\n";
echo "   - Campo 'account' ausente = Normal quando autenticado como subconta\n";
echo "   - Cobranças visíveis aqui = Pertencem à subconta\n";
echo "   - Master não vê (404) + Subconta vê = ISOLAMENTO CONFIRMADO ✅\n";
echo "════════════════════════════════════════════════════════\n";

<?php
/**
 * Script de teste para debugar a API de ativação WhatsApp
 * Uso: php test-whatsapp-api.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Facades\Log;

$tenantSlug = 'magic-club';
$phone = '32998448612'; // O número que você tentou ativar

echo "🔍 Testando ativação WhatsApp\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// 1. Encontra o tenant
$tenant = Tenant::where('subdomain', $tenantSlug)->first();
if (!$tenant) {
    echo "❌ Tenant '{$tenantSlug}' não encontrado!\n";
    exit(1);
}

echo "✅ Tenant encontrado: {$tenant->name}\n";
echo "   Subdomain: {$tenant->subdomain}\n\n";

// 2. Inicializa tenancy
tenancy()->initialize($tenant);
echo "✅ Tenancy inicializada\n\n";

// 3. Normaliza o telefone (simula o que a API faz)
$normalizedPhone = preg_replace('/\D/', '', $phone);
echo "📱 Número original: {$phone}\n";
echo "📱 Número normalizado: {$normalizedPhone}\n\n";

// 4. Gera variações (exatamente como a API)
$variations = [
    $normalizedPhone,
    '0' . $normalizedPhone,
];

if (strlen($normalizedPhone) == 10) {
    $with9 = substr($normalizedPhone, 0, 2) . '9' . substr($normalizedPhone, 2);
    $variations[] = $with9;
    $variations[] = '0' . $with9;
}

if (strlen($normalizedPhone) == 11) {
    $without9 = substr($normalizedPhone, 0, 2) . substr($normalizedPhone, 3);
    $variations[] = $without9;
    $variations[] = '0' . $without9;
}

$variations = array_unique($variations);

echo "🔄 Variações geradas:\n";
foreach ($variations as $v) {
    echo "   • {$v}\n";
}
echo "\n";

// 5. Lista TODOS os usuários para comparar
echo "📋 Usuários no banco:\n";
$users = \App\Models\User::select('id', 'name', 'phone', 'whatsapp', 'whatsapp_activated')->get();
foreach ($users as $user) {
    $userNormalized = preg_replace('/\D/', '', $user->phone);
    $match = in_array($userNormalized, $variations) ? '✅ MATCH' : '';
    echo "   • ID {$user->id}: {$user->name}\n";
    echo "     Tel: '{$user->phone}' (normalizado: '{$userNormalized}') {$match}\n";
    echo "     WhatsApp: " . ($user->whatsapp ? 'Sim' : 'Não') . "\n";
    echo "     Ativado: " . ($user->whatsapp_activated ? 'Sim' : 'Não') . "\n\n";
}

// 6. Testa a query EXATA da API
echo "🔍 Testando query da API:\n";
$found = \App\Models\User::where(function($query) use ($variations) {
    foreach ($variations as $variant) {
        $query->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '(', ''), ')', '') = ?", [$variant]);
    }
})->get();

if ($found->isEmpty()) {
    echo "❌ NENHUM usuário encontrado com a query da API!\n\n";
    
    // Debug: testa cada variação individualmente
    echo "🔬 Testando cada variação individualmente:\n";
    foreach ($variations as $variant) {
        $result = \App\Models\User::whereRaw(
            "REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '(', ''), ')', '') = ?",
            [$variant]
        )->first();
        
        if ($result) {
            echo "   ✅ '{$variant}' → Encontrou: {$result->name}\n";
        } else {
            echo "   ❌ '{$variant}' → Não encontrou\n";
        }
    }
} else {
    echo "✅ Usuários encontrados:\n";
    foreach ($found as $user) {
        echo "   • {$user->name} (ID: {$user->id}) - {$user->phone}\n";
    }
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Teste concluído\n";

tenancy()->end();

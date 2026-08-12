<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== DEBUG DETALHADO Asaas API ===\n";

$apiUrl = config('services.asaas.api_url');
$apiKey = config('services.asaas.api_key');

echo "URL: $apiUrl\n";
echo "Key: " . substr($apiKey, 0, 30) . "...\n\n";

// TESTE 1: Verificar AUTENTICAÇÃO
echo "1. TESTE DE AUTENTICAÇÃO:\n";
echo "   Tentando acessar endpoint que requer autenticação...\n";
$testAuth = Http::withHeaders([
    'access_token' => $apiKey,
])->get($apiUrl . '/customers?limit=1');

echo "   Status: " . $testAuth->status() . "\n";
echo "   Sucesso: " . ($testAuth->successful() ? 'SIM' : 'NÃO') . "\n";

if ($testAuth->status() === 401) {
    echo "   ⚠️ ERRO: API KEY INVÁLIDA!\n";
    exit;
}

// TESTE 2: Criar cliente com DEBUG máximo
echo "\n2. CRIAR CLIENTE COM DEBUG:\n";
$uniqueData = [
    'name' => 'DEBUG Cliente ' . date('H:i:s'),
    'cpfCnpj' => rand(10000000000, 99999999999),
    'email' => 'debug_' . microtime(true) . '@test.com',
    'phone' => '(11) ' . rand(10000, 99999) . '-' . rand(1000, 9999),
    'mobilePhone' => '(11) ' . rand(10000, 99999) . '-' . rand(1000, 9999),
    'address' => 'Rua Debug',
    'addressNumber' => '123',
    'complement' => 'Sala 1',
    'province' => 'Centro',
    'postalCode' => '01234-567',
];

echo "   Dados sendo enviados:\n";
foreach ($uniqueData as $key => $value) {
    echo "   - $key: $value\n";
}

// FAZER A REQUISIÇÃO MANUALMENTE PARA VER TUDO
$client = new \GuzzleHttp\Client();
try {
    $response = $client->request('POST', $apiUrl . '/customers', [
        'headers' => [
            'access_token' => $apiKey,
            'Content-Type' => 'application/json',
            'User-Agent' => 'Pagby-Debug/1.0',
        ],
        'json' => $uniqueData,
        'http_errors' => false,
        'debug' => true, // ATIVA DEBUG DO GUZZLE
    ]);
    
    echo "\n   ╔══════════════════════════════════════════╗\n";
    echo "   ║          RESPOSTA COMPLETA               ║\n";
    echo "   ╚══════════════════════════════════════════╝\n";
    
    echo "   Status: " . $response->getStatusCode() . "\n";
    echo "   Headers:\n";
    foreach ($response->getHeaders() as $name => $values) {
        echo "     $name: " . implode(', ', $values) . "\n";
    }
    
    $body = $response->getBody()->getContents();
    echo "\n   Body (raw):\n";
    echo "   " . str_replace("\n", "\n   ", $body) . "\n";
    
    $json = json_decode($body, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "\n   Body (JSON decoded):\n";
        echo "   " . json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        
        // Análise da resposta
        if (isset($json['object']) && $json['object'] === 'list') {
            echo "\n   🔍 ANÁLISE: Resposta é uma LISTA, não um objeto customer\n";
            echo "   TotalCount: " . ($json['totalCount'] ?? 0) . "\n";
            echo "   Data vazia? " . (empty($json['data']) ? 'SIM' : 'NÃO') . "\n";
            
            if (!empty($json['data'])) {
                echo "   Primeiro item object: " . ($json['data'][0]['object'] ?? 'N/A') . "\n";
            }
        } elseif (isset($json['object']) && $json['object'] === 'customer') {
            echo "\n   ✅ ANÁLISE: Resposta é um objeto CUSTOMER válido!\n";
            echo "   ID do cliente: " . ($json['id'] ?? 'N/A') . "\n";
        }
    }
    
} catch (\Exception $e) {
    echo "   ❌ EXCEÇÃO: " . $e->getMessage() . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n";
}

// TESTE 3: Verificar se endpoint está correto
echo "\n3. VERIFICAÇÃO DE ENDPOINT:\n";
echo "   Tentando endpoint alternativo...\n";

// Às vezes o Asaas tem endpoints diferentes
$endpoints = [
    '/customers',
    '/v3/customers',
    '/api/customers',
];

foreach ($endpoints as $endpoint) {
    $testUrl = 'https://asaas.com' . $endpoint;
    echo "   Testando: $testUrl\n";
    
    $test = Http::withHeaders(['access_token' => $apiKey])
                ->get($testUrl . '?limit=1');
    
    echo "     Status: " . $test->status() . "\n";
    if ($test->successful()) {
        $data = $test->json();
        echo "     Object: " . ($data['object'] ?? 'N/A') . "\n";
        echo "     Total: " . ($data['totalCount'] ?? 0) . "\n";
    }
    echo "\n";
}

// TESTE 4: Testar criação SIMPLES
echo "\n4. TESTE SIMPLES DE CRIAÇÃO:\n";
echo "   Dados mínimos...\n";

$simpleData = [
    'name' => 'Teste Simples',
    'cpfCnpj' => '11122233344',
];

$simpleResp = Http::withHeaders([
    'access_token' => $apiKey,
    'Content-Type' => 'application/json',
])->post($apiUrl . '/customers', $simpleData);

echo "   Status: " . $simpleResp->status() . "\n";
echo "   Body: " . $simpleResp->body() . "\n";

// TESTE 5: Verificar tipo de conta
echo "\n5. VERIFICANDO TIPO DE CONTA:\n";
echo "   Acessando informações da conta...\n";

$accountResp = Http::withHeaders([
    'access_token' => $apiKey,
])->get($apiUrl . '/accounts');

echo "   Status: " . $accountResp->status() . "\n";
if ($accountResp->successful()) {
    $accountData = $accountResp->json();
    echo "   Resposta: " . json_encode($accountData, JSON_PRETTY_PRINT) . "\n";
}

echo "\n=== FIM DO DEBUG ===\n";

<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== Teste DIRETO Asaas API ===\n";

$apiUrl = config('services.asaas.api_url');
$apiKey = config('services.asaas.api_key');

echo "URL: $apiUrl\n";
echo "Key: " . substr($apiKey, 0, 30) . "...\n";

// Teste 1: Listar TODOS os clientes
echo "\n1. Listando TODOS os clientes:\n";
$resp = Http::withHeaders([
    'access_token' => $apiKey,
])->get($apiUrl . '/customers?limit=10');

echo "Status: " . $resp->status() . "\n";
$data = $resp->json();
echo "Total: " . ($data['totalCount'] ?? 0) . " clientes\n";

if (!empty($data['data'])) {
    foreach ($data['data'] as $i => $customer) {
        echo "  $i. ID: " . $customer['id'] . 
             " | Nome: " . $customer['name'] . 
             " | CPF: " . ($customer['cpfCnpj'] ?? 'null') . 
             " | Email: " . ($customer['email'] ?? 'null') . "\n";
    }
}

// Teste 2: Criar com dados MUITO diferentes
echo "\n2. Criando cliente com dados únicos:\n";
$uniqueEmail = 'test-' . microtime(true) . '-' . rand(1000, 9999) . '@exemplo.com';
$uniqueCPF = rand(10000000000, 99999999999);

$customerData = [
    'name' => 'Cliente Único ' . date('His'),
    'cpfCnpj' => (string)$uniqueCPF,
    'email' => $uniqueEmail,
    'phone' => '(11) 9' . rand(1000, 9999) . '-' . rand(1000, 9999),
    'mobilePhone' => '(11) 9' . rand(1000, 9999) . '-' . rand(1000, 9999),
];

echo "Dados únicos:\n";
echo "  Email: $uniqueEmail\n";
echo "  CPF: $uniqueCPF\n";

$resp2 = Http::withHeaders([
    'access_token' => $apiKey,
    'Content-Type' => 'application/json',
])->post($apiUrl . '/customers', $customerData);

echo "Status: " . $resp2->status() . "\n";
$data2 = $resp2->json();
echo "Resposta: " . json_encode($data2, JSON_PRETTY_PRINT) . "\n";

// Teste 3: Verificar se realmente criou
echo "\n3. Buscando cliente recém-criado:\n";
$resp3 = Http::withHeaders([
    'access_token' => $apiKey,
])->get($apiUrl . '/customers', [
    'cpfCnpj' => $uniqueCPF,
    'email' => $uniqueEmail,
]);

echo "Status: " . $resp3->status() . "\n";
$data3 = $resp3->json();
echo "Total encontrado: " . ($data3['totalCount'] ?? 0) . "\n";

if (!empty($data3['data'])) {
    foreach ($data3['data'] as $customer) {
        echo "  Encontrado: " . $customer['id'] . " - " . $customer['name'] . "\n";
    }
}

// Teste 4: Health check da API
echo "\n4. Health check:\n";
try {
    $resp4 = Http::withHeaders([
        'access_token' => $apiKey,
    ])->get($apiUrl . '/health');
    
    echo "Status: " . $resp4->status() . "\n";
    echo "Body: " . $resp4->body() . "\n";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\n=== Fim do Teste ===\n";

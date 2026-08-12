<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== Teste Asaas Service Corrigido ===\n";

$apiUrl = config('services.asaas.api_url');
$apiKey = config('services.asaas.api_key');

// 1. Testar criação manual
echo "\n1. Criando cliente manualmente...\n";
$customerData = [
    'name' => 'Cliente Teste Final',
    'cpfCnpj' => '88877766655',
    'email' => 'final' . time() . '@teste.com',
    'phone' => '(11) 98888-7777',
    'mobilePhone' => '(11) 98888-7777',
];

$resp = Http::withHeaders([
    'access_token' => $apiKey,
    'Content-Type' => 'application/json',
])->post($apiUrl . '/customers', $customerData);

echo "Status: " . $resp->status() . "\n";
$data = $resp->json();
echo "Resposta: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";

// 2. Testar o serviço
echo "\n2. Testando getOrCreateCustomer...\n";

// Primeiro, carregar a classe corrigida
require_once app_path('Services/AsaasService.php');
$service = new App\Services\AsaasService();

$testCustomer = [
    'name' => 'Teste Serviço',
    'cpfCnpj' => '99988877766',
    'email' => 'servico' . time() . '@teste.com',
    'phone' => '(11) 97777-6666',
    'mobilePhone' => '(11) 97777-6666',
];

$customerId = $service->getOrCreateCustomer($testCustomer);
echo "Resultado: " . ($customerId ?: 'null') . "\n";

// 3. Testar criação de assinatura
echo "\n3. Testando criação de assinatura...\n";
if ($customerId) {
    $subscriptionData = [
        'cycle' => 'MONTHLY',
        'value' => 100.00,
        'description' => 'Assinatura Teste',
        'nextDueDate' => date('Y-m-d', strtotime('+1 day')),
        'externalReference' => 'test-' . uniqid(),
        'billingType' => 'UNDEFINED',
    ];
    
    $result = $service->criarAssinatura($testCustomer, $subscriptionData);
    
    echo "Sucesso: " . ($result['success'] ? 'SIM' : 'NÃO') . "\n";
    if ($result['success']) {
        echo "ID Assinatura: " . ($result['data']['id'] ?? 'N/A') . "\n";
        echo "Status: " . ($result['data']['status'] ?? 'N/A') . "\n";
        echo "Invoice URL: " . ($result['data']['invoiceUrl'] ?? 'N/A') . "\n";
    } else {
        echo "Erro: " . ($result['message'] ?? 'Erro desconhecido') . "\n";
    }
}

echo "\n=== Fim do Teste ===\n";

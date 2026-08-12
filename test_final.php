<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTE FINAL ASaaS SERVICE ===\n";

// Instanciar serviço
$service = new App\Services\AsaasService();

// Teste 1: Criar cliente
echo "\n1. Criando cliente...\n";
$customerData = [
    'name' => 'Helder Teste',
    'cpfCnpj' => '12345678901',
    'email' => 'helder@teste.com',
    'phone' => '(11) 99999-9999',
];

$customerId = $service->getOrCreateCustomer($customerData);
echo "   Customer ID: " . ($customerId ?: 'null') . "\n";

// Teste 2: Criar assinatura
echo "\n2. Criando assinatura...\n";
$subscriptionData = [
    'cycle' => 'MONTHLY',
    'value' => 150.00,
    'description' => 'Assinatura Teste',
    'nextDueDate' => date('Y-m-d', strtotime('+1 day')),
    'externalReference' => 'test-' . uniqid(),
    'billingType' => 'UNDEFINED',
];

$result = $service->criarAssinatura($customerData, $subscriptionData);
echo "   Sucesso: " . ($result['success'] ? 'SIM' : 'NÃO') . "\n";

if ($result['success']) {
    echo "   ID Assinatura: " . ($result['data']['id'] ?? 'N/A') . "\n";
    echo "   URL Pagamento: " . ($result['data']['invoiceUrl'] ?? 'N/A') . "\n";
    echo "   Status: " . ($result['data']['status'] ?? 'N/A') . "\n";
    
    // Se tiver URL, podemos simular acesso
    if (!empty($result['data']['invoiceUrl'])) {
        echo "\n3. ✅ PRONTO PARA TESTE NO NAVEGADOR!\n";
        echo "   Acesse esta URL no navegador:\n";
        echo "   " . $result['data']['invoiceUrl'] . "\n";
        echo "\n   Ou use este comando curl:\n";
        echo "   curl -I \"" . $result['data']['invoiceUrl'] . "\"\n";
    }
} else {
    echo "   Erro: " . ($result['message'] ?? 'Erro desconhecido') . "\n";
}

// Teste 3: Criar checkout (opcional)
echo "\n4. Criando checkout...\n";
$checkoutResult = $service->criarCheckout($customerData, [
    'name' => 'Checkout Teste',
    'value' => 99.90,
    'description' => 'Pagamento único',
]);

echo "   Sucesso: " . ($checkoutResult['success'] ? 'SIM' : 'NÃO') . "\n";
if ($checkoutResult['success']) {
    echo "   URL Checkout: " . ($checkoutResult['data']['url'] ?? 'N/A') . "\n";
}

echo "\n=== FIM DO TESTE ===\n";
echo "\n📝 NOTA: O sistema está usando MOCK porque a API do Asaas\n";
echo "       está retornando apenas listas vazias (conta demo?).\n";
echo "       Para usar a API real, ative sua conta Asaas ou\n";
echo "       use uma chave de SANDBOX válida.\n";

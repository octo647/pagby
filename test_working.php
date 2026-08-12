<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTE ASaaS WORKING (MOCK) ===\n";

// Usar o novo serviço
require_once app_path('Services/AsaasServiceWorking.php');
$service = new App\Services\AsaasServiceWorking();

// Teste 1: Criar cliente
echo "\n1. Criando cliente...\n";
$customerData = [
    'name' => 'Helder Teste',
    'cpfCnpj' => '12345678901',
    'email' => 'helder@teste.com',
    'phone' => '(11) 99999-9999',
];

$customerId = $service->getOrCreateCustomer($customerData);
echo "   ✅ Customer ID: $customerId\n";

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
echo "   Sucesso: " . ($result['success'] ? '✅ SIM' : '❌ NÃO') . "\n";

if ($result['success']) {
    echo "   📋 ID Assinatura: " . ($result['data']['id'] ?? 'N/A') . "\n";
    echo "   🔗 URL Pagamento: " . ($result['data']['invoiceUrl'] ?? 'N/A') . "\n";
    echo "   📊 Status: " . ($result['data']['status'] ?? 'N/A') . "\n";
    echo "   💰 Valor: R$ " . ($result['data']['value'] ?? '0') . "\n";
    
    echo "\n3. 🎉 PRONTO PARA USAR NO CONTROLLER!\n";
    echo "   Copie este código para seu controller:\n\n";
    
    echo <<<CODE
    // No PagBySubscriptionController.php, método showPaymentForm:
    // SUBSTITUA esta linha:
    // \$asaasService = new \\App\\Services\\AsaasService();
    
    // POR esta:
    \$asaasService = new \\App\\Services\\AsaasServiceWorking();
    
    CODE;
    
} else {
    echo "   ❌ Erro: " . ($result['message'] ?? 'Erro desconhecido') . "\n";
}

// Teste 3: Criar checkout
echo "\n4. Criando checkout...\n";
$checkoutResult = $service->criarCheckout($customerData, [
    'name' => 'Checkout Teste',
    'value' => 99.90,
    'description' => 'Pagamento único',
]);

if ($checkoutResult['success']) {
    echo "   ✅ URL Checkout: " . ($checkoutResult['data']['url'] ?? 'N/A') . "\n";
}

echo "\n=== FIM DO TESTE ===\n";
echo "\n🎯 AGORA VÁ PARA O CONTROLLER E USE AsaasServiceWorking!\n";

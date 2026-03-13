<?php
require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tenant = App\Models\Tenant::find('teste1772962022');
$apiKey = Crypt::decryptString($tenant->asaas_api_key);

echo "🧪 Criando pagamento de teste para disparar webhook...\n";
echo "URL Webhook: https://pagby.com.br/api/subconta-webhook\n\n";

$response = Http::withHeaders(['access_token' => $apiKey])
    ->post('https://sandbox.asaas.com/api/v3/payments', [
        'customer' => 'cus_000007645766',
        'billingType' => 'PIX',
        'value' => 25.00,
        'dueDate' => date('Y-m-d', strtotime('+7 days')),
        'description' => 'TESTE WEBHOOK - Aguardando disparo do webhook',
    ]);

if ($response->successful()) {
    $payment = $response->json();
    echo "✅ Pagamento criado!\n";
    echo "ID: {$payment['id']}\n";
    echo "Valor: R$ {$payment['value']}\n";
    echo "Status: {$payment['status']}\n";
    echo "\n⏳ Aguardando 5 segundos para webhook ser disparado...\n";
    sleep(5);
    echo "✅ Webhook deveria ter sido chamado!\n";
    echo "📋 Verificando logs...\n\n";
    
    // Verificar últimos logs
    $logFile = '/var/www/pagby/storage/logs/laravel.log';
    $logs = shell_exec("tail -50 $logFile | grep -E '(Webhook|subconta-webhook|SubcontaWebhookController)'");
    if ($logs) {
        echo "📝 Logs encontrados:\n";
        echo $logs . "\n";
    } else {
        echo "⚠️  Nenhum log de webhook encontrado\n";
        echo "Últimas linhas do log:\n";
        echo shell_exec("tail -10 $logFile") . "\n";
    }
} else {
    echo "❌ Erro ao criar pagamento: {$response->body()}\n";
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AsaasServiceWorking {
    protected $apiUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('services.asaas.api_url', 'https://www.asaas.com/api/v3');
        $this->apiKey = config('services.asaas.api_key');
    }

    /**
     * MOCK: Busca ou cria um cliente
     */
    public function getOrCreateCustomer(array $customerData)
    {
        // SEMPRE retornar ID mock para desenvolvimento
        $customerId = 'cus_' . Str::random(10);
        
        \Log::info('[Asaas MOCK] Cliente criado', [
            'id' => $customerId,
            'name' => $customerData['name'] ?? 'N/A',
            'email' => $customerData['email'] ?? 'N/A',
            'cpf' => $customerData['cpfCnpj'] ?? 'N/A'
        ]);
        
        return $customerId;
    }

    /**
     * MOCK: Cria uma assinatura recorrente
     */
    public function criarAssinatura(array $customerData, array $subscriptionData, ?array $splitData = null)
    {
        $customerId = $this->getOrCreateCustomer($customerData);
        $subscriptionId = 'sub_' . Str::random(10);
        
        // Usar a rota que acabamos de criar
        $paymentUrl = route('asaas.mock.payment', ['id' => $subscriptionId]);
        
        \Log::info('[Asaas MOCK] Assinatura criada', [
            'subscription_id' => $subscriptionId,
            'customer_id' => $customerId,
            'value' => $subscriptionData['value'],
            'payment_url' => $paymentUrl
        ]);
        
        return [
            'success' => true,
            'data' => [
                'id' => $subscriptionId,
                'customer' => $customerId,
                'invoiceUrl' => $paymentUrl,
                'status' => 'ACTIVE',
                'value' => $subscriptionData['value'],
                'cycle' => $subscriptionData['cycle'] ?? 'MONTHLY',
                'billingType' => $subscriptionData['billingType'] ?? 'UNDEFINED',
                'description' => $subscriptionData['description'] ?? 'Assinatura PagBy',
                'nextDueDate' => $subscriptionData['nextDueDate'] ?? now()->addDays(7)->format('Y-m-d'),
                'externalReference' => $subscriptionData['externalReference'] ?? null,
            ]
        ];
    }

    /**
     * MOCK: Cria checkout
     */
    public function criarCheckout(array $customerData, array $paymentData)
    {
        $checkoutId = 'checkout_' . Str::random(10);
        
        // Usar a rota de checkout
        $checkoutUrl = route('asaas.mock.checkout', ['id' => $checkoutId]);
        
        \Log::info('[Asaas MOCK] Checkout criado', [
            'id' => $checkoutId,
            'value' => $paymentData['value'] ?? 0,
            'url' => $checkoutUrl
        ]);
        
        return [
            'success' => true,
            'data' => [
                'id' => $checkoutId,
                'url' => $checkoutUrl,
                'paymentLink' => $checkoutId,
                'value' => $paymentData['value'] ?? 0,
                'name' => $paymentData['name'] ?? 'Pagamento PagBy',
                'description' => $paymentData['description'] ?? 'Checkout Mock',
            ]
        ];
    }

    /**
     * MOCK: Consulta cobrança
     */
    public function consultarCobranca($asaasPaymentId)
    {
        return [
            'id' => $asaasPaymentId,
            'status' => 'RECEIVED',
            'value' => 100.00,
            'billingType' => 'UNDEFINED',
            'paymentDate' => now()->format('Y-m-d'),
        ];
    }

    /**
     * MOCK: Cria cobrança
     */
    public function criarCobranca(array $customerData, array $paymentData)
    {
        $customerId = $this->getOrCreateCustomer($customerData);
        $paymentId = 'pay_' . Str::random(10);
        
        return [
            'success' => true,
            'data' => [
                'id' => $paymentId,
                'customer' => $customerId,
                'value' => $paymentData['value'],
                'status' => 'PENDING',
                'billingType' => $paymentData['billingType'] ?? 'UNDEFINED',
            ]
        ];
    }

    /**
     * MOCK: Outros métodos necessários
     */
    public function cancelarCobranca($asaasPaymentId) {
        return ['success' => true];
    }
    
    public function consultarAssinatura($subscriptionId) {
        return [
            'id' => $subscriptionId,
            'status' => 'ACTIVE', 
            'value' => 150.00,
            'customer' => 'cus_' . Str::random(10)
        ];
    }
    
    public function cancelarAssinatura($subscriptionId) {
        return ['success' => true];
    }
    
    public function atualizarAssinatura($subscriptionId, array $updateData) {
        return ['success' => true];
    }
    
    public function listarCobrancasAssinatura($subscriptionId) {
        return ['data' => []];
    }
    
    public function criarSubconta(array $accountData) {
        return [
            'success' => true, 
            'data' => ['id' => 'acc_' . Str::random(10)]
        ];
    }
    
    public function consultarSubconta($accountId) {
        return ['id' => $accountId, 'status' => 'ACTIVE'];
    }
}

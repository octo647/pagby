<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;



class AsaasService {
    public function criarCheckout(array $customerData, array $paymentData)
    {
        // Criar checkout sem vincular cliente - Asaas cria o cliente automaticamente no pagamento
        $checkoutData = [
            'name' => $paymentData['name'] ?? 'Pagamento PagBy',
            'description' => $paymentData['description'] ?? 'Assinatura PagBy',
            'billingType' => $paymentData['billingType'] ?? 'UNDEFINED',
            'chargeType' => 'DETACHED',
            'endDate' => now()->addDays(7)->format('Y-m-d'), // Link válido por 7 dias
            'dueDateLimitDays' => 3, // 3 dias após vencimento para pagar
            'value' => $paymentData['value'] ?? 0,
        ];

        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl . '/paymentLinks', $checkoutData);

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json()];
        }
        return ['success' => false, 'message' => $response->body()];
    }
    protected $apiUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('services.asaas.api_url', 'https://www.asaas.com/api/v3');
        $this->apiKey = config('services.asaas.api_key');
    }

    /**
     * Consulta o status de uma cobrança no Asaas.
     * @param string $asaasPaymentId
     * @return array|null
     */
    public function consultarCobranca($asaasPaymentId)
    {
        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->get($this->apiUrl . '/payments/' . $asaasPaymentId);

        if ($response->successful()) {
            return $response->json();
        }
        return null;
    }

    /**
     * Cria uma cobrança no Asaas para o cliente.
     * @param array $customerData
     * @param array $paymentData
     * @return array
     */
    public function criarCobranca(array $customerData, array $paymentData)
    {
        // 1. Criar/obter cliente no Asaas
        $customerId = $this->getOrCreateCustomer($customerData);
        if (!$customerId) {
            return ['success' => false, 'message' => 'Erro ao criar cliente no Asaas.'];
        }

        // 2. Criar cobrança
        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl . '/payments', array_merge($paymentData, [
            'customer' => $customerId
        ]));

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json()];
        }
        return ['success' => false, 'message' => $response->body()];
    }

    /**
     * Busca ou cria um cliente no Asaas.
     * @param array $customerData
     * @return string|null
     */
    public function getOrCreateCustomer(array $customerData)
    {
        // Buscar cliente por CPF/email
        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->get($this->apiUrl . '/customers', [
            'cpfCnpj' => $customerData['cpfCnpj'] ?? null,
            'email' => $customerData['email'] ?? null,
        ]);

        if ($response->successful() && !empty($response['data'])) {
            return $response['data'][0]['id'] ?? null;
        }

        // Criar cliente se não existir
        $create = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl . '/customers', $customerData);

        if ($create->successful()) {
            $responseData = $create->json();
            return $responseData['id'] ?? null;
        }
        // Retornar mensagem de erro detalhada do Asaas
        if ($create->status() >= 400) {
            $error = $create->json('errors') ?? $create->json('message') ?? $create->body();
            throw new \Exception('Erro ao criar cliente no Asaas: ' . json_encode($error));
        }
        return null;
    }
}

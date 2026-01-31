<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;    
 
class AsaasService {
    /**
     * Lista todas as cobranças em aberto para um cliente pelo CPF/CNPJ.
     * @param string $cpfCnpj
     * @return array|null
     */
    public function listarCobrancasAbertasPorCpf($cpfCnpj)
    {
        // Buscar cliente pelo CPF/CNPJ
        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->get($this->apiUrl . '/customers', [
            'cpfCnpj' => $cpfCnpj
        ]);
        if (!$response->successful() || empty($response['data'][0]['id'])) {
            return null;
        }
        $customerId = $response['data'][0]['id'];

        // Buscar cobranças em aberto para o cliente
        $payments = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->get($this->apiUrl . "/payments", [
            'customer' => $customerId,
            'status' => 'OPEN', // pega todas em aberto, inclusive vencidas
        ]);
        if ($payments->successful() && isset($payments['data'])) {
            return $payments['data'];
        }
        return null;
    }

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
        \Log::info('[AsaasService] apiKey carregada', [
            'apiKey' => $this->apiKey ? substr($this->apiKey, 0, 12) . '...' : null
        ]);
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

    /**
     * Cancela uma cobrança no Asaas.
     * @param string $asaasPaymentId
     * @return array
     */
    public function cancelarCobranca($asaasPaymentId)
    {
        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->delete($this->apiUrl . '/payments/' . $asaasPaymentId);

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json()];
        }
        return ['success' => false, 'message' => $response->body()];
    }

    /**
     * Cria uma assinatura recorrente no Asaas com split de pagamentos.
     * 
     * @param array $customerData Dados do cliente (name, cpfCnpj, email, etc)
     * @param array $subscriptionData Dados da assinatura
     *   - cycle: WEEKLY, BIWEEKLY, MONTHLY, QUARTERLY, SEMIANNUALLY, YEARLY
     *   - value: Valor da assinatura
     *   - description: Descrição da assinatura
     *   - nextDueDate: Primeira data de vencimento (Y-m-d)
     *   - externalReference: Referência externa (payment_id)
     * @param array|null $splitData Configuração do split (opcional)
     *   - walletId: ID da carteira do tenant (subconta Asaas)
     *   - percentualValue: Percentual para o tenant (ex: 90 = 90%)
     *   - fixedValue: Valor fixo para o tenant (alternativa ao percentual)
     * @return array
     */
    public function criarAssinatura(array $customerData, array $subscriptionData, ?array $splitData = null)
    {
        // 1. Criar/obter cliente no Asaas
        $customerId = $this->getOrCreateCustomer($customerData);
        if (!$customerId) {
            return ['success' => false, 'message' => 'Erro ao criar cliente no Asaas.'];
        }

        // 2. Preparar dados da assinatura
        $payload = [
            'customer' => $customerId,
            'billingType' => $subscriptionData['billingType'] ?? 'UNDEFINED', // BOLETO, CREDIT_CARD, UNDEFINED, etc
            'cycle' => $subscriptionData['cycle'] ?? 'MONTHLY',
            'value' => $subscriptionData['value'],
            'nextDueDate' => $subscriptionData['nextDueDate'] ?? now()->addDays(7)->format('Y-m-d'),
            'description' => $subscriptionData['description'] ?? 'Assinatura PagBy',
            'externalReference' => $subscriptionData['externalReference'] ?? null,
        ];

        // Adicionar configuração de split se fornecida
        if ($splitData && isset($splitData['walletId'])) {
            $payload['split'] = [
                [
                    'walletId' => $splitData['walletId'],
                    'percentualValue' => $splitData['percentualValue'] ?? null,
                    'fixedValue' => $splitData['fixedValue'] ?? null,
                ]
            ];
        }

        // 3. Criar assinatura
        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl . '/subscriptions', $payload);

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json()];
        }
        
        return ['success' => false, 'message' => $response->body(), 'errors' => $response->json()];
    }

    /**
     * Consulta o status de uma assinatura no Asaas.
     * @param string $subscriptionId
     * @return array|null
     */
    public function consultarAssinatura($subscriptionId)
    {
        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->get($this->apiUrl . '/subscriptions/' . $subscriptionId);

        if ($response->successful()) {
            return $response->json();
        }
        return null;
    }

    /**
     * Cancela uma assinatura no Asaas.
     * @param string $subscriptionId
     * @return array
     */
    public function cancelarAssinatura($subscriptionId)
    {
        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->delete($this->apiUrl . '/subscriptions/' . $subscriptionId);

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json()];
        }
        return ['success' => false, 'message' => $response->body()];
    }

    /**
     * Atualiza uma assinatura existente.
     * @param string $subscriptionId
     * @param array $updateData
     * @return array
     */
    public function atualizarAssinatura($subscriptionId, array $updateData)
    {
        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl . '/subscriptions/' . $subscriptionId, $updateData);

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json()];
        }
        return ['success' => false, 'message' => $response->body()];
    }

    /**
     * Lista as cobranças de uma assinatura.
     * @param string $subscriptionId
     * @return array|null
     */
    public function listarCobrancasAssinatura($subscriptionId)
    {
        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->get($this->apiUrl . '/subscriptions/' . $subscriptionId . '/payments');

        if ($response->successful()) {
            return $response->json();
        }
        return null;
    }

    /**
     * Cria uma subconta (carteira) para um tenant no Asaas.
     * Necessário para implementar o split de pagamentos.
     * 
     * @param array $accountData
     *   - name: Nome do titular
     *   - email: Email
     *   - cpfCnpj: CPF ou CNPJ
     *   - birthDate: Data de nascimento (Y-m-d) - para CPF
     *   - companyType: MEI, LIMITED, INDIVIDUAL, ASSOCIATION - para CNPJ
     *   - phone: Telefone
     *   - mobilePhone: Celular
     *   - address: Array com dados do endereço
     * @return array
     */
    public function criarSubconta(array $accountData)
    {
        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl . '/accounts', $accountData);

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json()];
        }
        return ['success' => false, 'message' => $response->body(), 'errors' => $response->json()];
    }

    /**
     * Consulta dados de uma subconta.
     * @param string $accountId
     * @return array|null
     */
    public function consultarSubconta($accountId)
    {
        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->get($this->apiUrl . '/accounts/' . $accountId);

        if ($response->successful()) {
            return $response->json();
        }
        return null;
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
 
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

    /**
     * Constructor do AsaasService.
     * 
     * @param string|null $apiKey API key customizada (para usar com subcontas)
     */
    public function __construct($apiKey = null)
    {
        $this->apiUrl = config('services.asaas.api_url', 'https://www.asaas.com/api/v3');
        $this->apiKey = $apiKey ?? config('services.asaas.api_key');
        \Log::info('[AsaasService] apiKey carregada', [
            'apiKey' => $this->apiKey ? substr($this->apiKey, 0, 12) . '...' : null,
            'custom' => $apiKey !== null
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
            'nextDueDate' => $subscriptionData['nextDueDate'] ?? now()->format('Y-m-d'), // Gera cobrança imediatamente
            'description' => $subscriptionData['description'] ?? 'Assinatura PagBy',
            'externalReference' => $subscriptionData['externalReference'] ?? null,
        ];

        // Adicionar configuração de split se fornecida (array de múltiplos beneficiários)
        if ($splitData && is_array($splitData) && count($splitData) > 0) {
            $payload['split'] = $splitData;
            Log::info('✅ Split adicionado ao payload', [
                'split' => $splitData
            ]);
        } else {
            Log::warning('❌ Split NÃO adicionado', [
                'splitData' => $splitData,
                'is_array' => is_array($splitData),
                'count' => $splitData ? count($splitData) : 0
            ]);
        }

        Log::info('📤 Payload completo para Asaas', [
            'payload' => $payload
        ]);

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
        Log::info('🔴 Cancelando assinatura Asaas', [
            'subscription_id' => $subscriptionId,
            'api_url' => $this->apiUrl,
            'api_key_prefix' => substr($this->apiKey, 0, 20) . '...'
        ]);

        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->delete($this->apiUrl . '/subscriptions/' . $subscriptionId);

        Log::info('📡 Resposta Asaas DELETE', [
            'status' => $response->status(),
            'successful' => $response->successful(),
            'body' => $response->body(),
            'json' => $response->json()
        ]);

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json()];
        }
        return [
            'success' => false, 
            'message' => $response->body(),
            'status' => $response->status()
        ];
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

    /**
     * Cria uma subconta Asaas COMPLETA e obtém sua API key.
     * 
     * IMPORTANTE: Diferente de criarSubconta() que cria apenas wallet para split,
     * este método cria uma subconta que pode operar independentemente.
     * Usado no modelo SEM SPLIT onde cada tenant recebe pagamentos diretos.
     * 
     * Documentação: https://docs.asaas.com/reference/criar-conta-filha
     * 
     * @param array $accountData Dados da subconta com campos obrigatórios:
     *   - name: Nome completo/razão social
     *   - email: Email válido
     *   - cpfCnpj: CPF (11 dígitos) ou CNPJ (14 dígitos)
     *   - mobilePhone: Telefone celular
     *   - birthDate: Data nascimento (Y-m-d) - obrigatório para CPF
     *   - companyType: Tipo empresa (MEI, LIMITED, etc) - obrigatório para CNPJ
     *   - incomeValue: Renda/faturamento mensal estimado
     * @return array ['success' => bool, 'data' => array, 'message' => string]
     */
    public function criarSubcontaCompleta(array $accountData)
    {
        try {
            Log::info('[AsaasService] Criando subconta completa', [
                'account_name' => $accountData['name'] ?? null,
                'email' => $accountData['email'] ?? null
            ]);

            // 1. Criar a subconta
            $response = Http::timeout(60)->withHeaders([
                'access_token' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/accounts', $accountData);

            if (!$response->successful()) {
                Log::error('[AsaasService] Erro ao criar subconta', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Erro ao criar subconta',
                    'errors' => $response->json()
                ];
            }

            $accountCreated = $response->json();
            $accountId = $accountCreated['id'];

            Log::info('[AsaasService] Subconta criada', [
                'account_id' => $accountId,
                'wallet_id' => $accountCreated['walletId'] ?? null
            ]);

            // 2. Gerar API key para a subconta
            $apiKeyResponse = Http::timeout(60)->withHeaders([
                'access_token' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/accounts/' . $accountId . '/apiKeys');

            if (!$apiKeyResponse->successful()) {
                Log::warning('[AsaasService] Subconta criada mas erro ao gerar API key', [
                    'account_id' => $accountId,
                    'error' => $apiKeyResponse->body()
                ]);
                
                return [
                    'success' => true, // Conta foi criada
                    'partial' => true,
                    'data' => $accountCreated,
                    'message' => 'Subconta criada, mas erro ao gerar API key',
                    'api_key_error' => $apiKeyResponse->json()
                ];
            }

            $apiKeyData = $apiKeyResponse->json();
            $apiKey = $apiKeyData['apiKey'] ?? null;

            Log::info('[AsaasService] API key gerada', [
                'account_id' => $accountId,
                'api_key_preview' => $apiKey ? substr($apiKey, 0, 20) . '...' : null
            ]);

            // 3. Registrar webhook para receber notificações de pagamentos
            $webhookResult = $this->registrarWebhookSubconta($accountId);
            
            if (!$webhookResult['success']) {
                Log::warning('[AsaasService] Subconta criada mas webhook não configurado', [
                    'account_id' => $accountId,
                    'webhook_error' => $webhookResult['message']
                ]);
                // Não falha a criação - webhook pode ser configurado depois
            }

            return [
                'success' => true,
                'data' => [
                    'account' => $accountCreated,
                    'api_key' => $apiKey,
                    'account_id' => $accountId,
                    'wallet_id' => $accountCreated['walletId'] ?? null,
                    'webhook' => $webhookResult['data'] ?? null,
                ],
                'message' => 'Subconta criada com sucesso'
            ];

        } catch (\Exception $e) {
            Log::error('[AsaasService] Exceção ao criar subconta completa', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Exceção ao criar subconta: ' . $e->getMessage(),
                'errors' => []
            ];
        }
    }

    /**
     * Recupera ou gera nova API key para uma subconta.
     * 
     * Tenta primeiro listar as API keys existentes e retornar uma ativa.
     * Se não encontrar, gera uma nova.
     * 
     * @param string $accountId ID da subconta
     * @return array ['success' => bool, 'api_key' => string|null, 'message' => string]
     */
    public function obterApiKeySubconta(string $accountId)
    {
        try {
            Log::info('[AsaasService] Obtendo API key da subconta', [
                'account_id' => $accountId
            ]);

            // Tentar recuperar API key existente
            $response = Http::timeout(30)->withHeaders([
                'access_token' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->get($this->apiUrl . '/accounts/' . $accountId . '/apiKeys');

            if ($response->successful()) {
                $apiKeys = $response->json()['data'] ?? [];
                
                // Procurar primeira API key ativa
                foreach ($apiKeys as $key) {
                    if (isset($key['status']) && $key['status'] === 'ACTIVE') {
                        Log::info('[AsaasService] API key existente encontrada', [
                            'account_id' => $accountId
                        ]);
                        
                        return [
                            'success' => true,
                            'api_key' => $key['apiKey'],
                            'message' => 'API key existente recuperada'
                        ];
                    }
                }
            }

            // Se não encontrou, gerar nova
            Log::info('[AsaasService] Gerando nova API key', [
                'account_id' => $accountId
            ]);
            
            $createResponse = Http::timeout(30)->withHeaders([
                'access_token' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/accounts/' . $accountId . '/apiKeys');

            if ($createResponse->successful()) {
                $data = $createResponse->json();
                $apiKey = $data['apiKey'] ?? null;
                
                Log::info('[AsaasService] Nova API key gerada', [
                    'account_id' => $accountId,
                    'api_key_preview' => $apiKey ? substr($apiKey, 0, 20) . '...' : null
                ]);
                
                return [
                    'success' => true,
                    'api_key' => $apiKey,
                    'message' => 'Nova API key gerada'
                ];
            }

            Log::error('[AsaasService] Erro ao gerar API key', [
                'account_id' => $accountId,
                'error' => $createResponse->body()
            ]);
            
            return [
                'success' => false,
                'api_key' => null,
                'message' => 'Erro ao obter/gerar API key',
                'errors' => $createResponse->json()
            ];

        } catch (\Exception $e) {
            Log::error('[AsaasService] Exceção ao obter API key', [
                'account_id' => $accountId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'api_key' => null,
                'message' => 'Exceção: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Consulta o status detalhado de uma subconta.
     * 
     * Retorna informações sobre aprovação e capacidade de receber pagamentos.
     * 
     * Status possíveis:
     * - PENDING: Aguardando aprovação Asaas
     * - ACTIVE: Ativa e pode receber pagamentos
     * - REJECTED: Rejeitada pelo Asaas
     * - DISABLED: Desabilitada
     * 
     * @param string $accountId ID da subconta
     * @return array ['success' => bool, 'status' => string, 'data' => array]
     */
    public function consultarStatusSubconta(string $accountId)
    {
        try {
            Log::info('[AsaasService] Consultando status da subconta', [
                'account_id' => $accountId
            ]);
            
            $response = Http::timeout(30)->withHeaders([
                'access_token' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->get($this->apiUrl . '/accounts/' . $accountId);

            if ($response->successful()) {
                $data = $response->json();
                $status = $data['status'] ?? 'UNKNOWN';
                
                Log::info('[AsaasService] Status da subconta obtido', [
                    'account_id' => $accountId,
                    'status' => $status
                ]);
                
                return [
                    'success' => true,
                    'status' => $status,
                    'data' => $data,
                    'can_receive_payments' => $status === 'ACTIVE'
                ];
            }

            Log::error('[AsaasService] Erro ao consultar status da subconta', [
                'account_id' => $accountId,
                'error' => $response->body()
            ]);
            
            return [
                'success' => false,
                'status' => 'ERROR',
                'message' => 'Erro ao consultar subconta'
            ];

        } catch (\Exception $e) {
            Log::error('[AsaasService] Exceção ao consultar status', [
                'account_id' => $accountId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'status' => 'ERROR',
                'message' => 'Exceção: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Registra webhook para uma subconta receber notificações de pagamento.
     * 
     * IMPORTANTE: Usa header 'Asaas-Account' para configurar webhook DA subconta
     * enquanto autentica com a API key MASTER.
     * 
     * @param string $accountId ID da subconta
     * @return array ['success' => bool, 'data' => array|null, 'message' => string]
     */
    public function registrarWebhookSubconta(string $accountId)
    {
        try {
            Log::info('[AsaasService] Registrando webhook para subconta', [
                'account_id' => $accountId,
                'webhook_url' => config('app.url') . '/api/subconta-webhook'
            ]);

            // Usar API MASTER com header especial para configurar webhook DA subconta
            $response = Http::timeout(60)->withHeaders([
                'access_token' => $this->apiKey, // Master key
                'Content-Type' => 'application/json',
                'Asaas-Account' => $accountId, // Configura webhook para esta subconta
            ])->post($this->apiUrl . '/webhook', [
                'name' => 'PagBy - Notificações de Pagamento',
                'url' => config('app.url') . '/api/subconta-webhook',
                'email' => config('mail.from.address', 'webhooks@pagby.com.br'),
                'apiVersion' => 3,
                'enabled' => true,
                'interrupted' => false,
                'events' => [
                    'PAYMENT_CREATED',
                    'PAYMENT_UPDATED',
                    'PAYMENT_CONFIRMED',
                    'PAYMENT_RECEIVED',
                    'PAYMENT_OVERDUE',
                    'PAYMENT_DELETED',
                    'PAYMENT_REFUNDED',
                    'PAYMENT_RECEIVED_IN_CASH',
                ]
            ]);

            if ($response->successful()) {
                $webhookData = $response->json();
                
                Log::info('[AsaasService] Webhook registrado com sucesso', [
                    'account_id' => $accountId,
                    'webhook_id' => $webhookData['id'] ?? null
                ]);

                return [
                    'success' => true,
                    'data' => $webhookData,
                    'message' => 'Webhook registrado com sucesso'
                ];
            }

            Log::warning('[AsaasService] Erro ao registrar webhook', [
                'account_id' => $accountId,
                'status' => $response->status(),
                'error' => $response->body()
            ]);

            return [
                'success' => false,
                'data' => null,
                'message' => 'Erro ao registrar webhook: ' . $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('[AsaasService] Exceção ao registrar webhook', [
                'account_id' => $accountId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'data' => null,
                'message' => 'Exceção ao registrar webhook: ' . $e->getMessage()
            ];
        }
    }
}

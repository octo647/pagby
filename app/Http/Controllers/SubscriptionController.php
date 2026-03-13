<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Services\AsaasService;

/**
 * SubscriptionController - Sistema de Assinaturas via Asaas
 * 
 * Gerencia assinaturas recorrentes com split de pagamentos automático
 * entre PagBy e tenants através do Asaas.
 */
class SubscriptionController extends Controller
{
    protected $asaasService;

    public function __construct(AsaasService $asaasService)
    {
        $this->asaasService = $asaasService;
    }

    /**
     * Cria nova assinatura via Asaas
     */
    public function store(Request $request)
    {
        $planId = $request->input('plan_id');
        $tenantId = $request->input('tenant_id');
        $userEmail = $request->input('user_email');
        $userName = $request->input('user_name');
        $cpfCnpj = $request->input('cpf_cnpj');

        Log::info('=== Criando Assinatura Asaas ===', [
            'tenant_id' => $tenantId,
            'plan_id' => $planId,
            'user_email' => $userEmail
        ]);

        // Buscar tenant
        $tenant = Tenant::on('mysql')->find($tenantId);
        
        if (!$tenant) {
            return redirect()->away("https://{$tenantId}.pagby.com.br/tenant-assinatura/failure?message=Salão não encontrado");
        }
        
        // Inicializar contexto do tenant
        tenancy()->initialize($tenant);
        
        // Buscar plano na base do TENANT
        $plan = \App\Models\Plan::find($planId);
        
        if (!$plan) {
            return redirect()->away("https://{$tenantId}.pagby.com.br/tenant-assinatura/failure?message=Plano não encontrado");
        }
        
        // Buscar usuário no tenant
        $user = User::where('email', $userEmail)->first();
        
        if (!$user) {
            Log::error('❌ Usuário não encontrado no tenant:', [
                'email' => $userEmail,
                'tenant_id' => $tenantId
            ]);
            return redirect()->away("https://{$tenantId}.pagby.com.br/tenant-assinatura/failure?message=Usuário não encontrado");
        }
        
        // Criar ou buscar assinatura existente no tenant
        $subscription = Subscription::where('user_id', $user->id)
            ->where('plan_id', $plan->id)
            ->first();
        
        if (!$subscription) {
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => 'Ativo',
                'start_date' => now(),
                'end_date' => now()->addMonth(),
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);
            Log::info('✨ Nova assinatura criada no tenant:', ['subscription_id' => $subscription->id]);
        } else {
            Log::info('📄 Assinatura existente encontrada:', ['subscription_id' => $subscription->id]);
        }
        
        // Criar registro de pagamento no TENANT
        $externalReference = 'asaas-sub-' . $tenantId . '-' . uniqid();
        $subscriptionPayment = SubscriptionPayment::create([
            'subscription_id' => $subscription->id,
            'asaas_payment_id' => $externalReference,
            'amount' => $plan->price,
            'billing_type' => 'CREDIT_CARD', // Padrão: cartão de crédito
            'due_date' => now()->addDays(7), // Vencimento em 7 dias
            'status' => 'pending',
            'asaas_data' => json_encode([
                'payer' => [
                    'email' => $userEmail,
                    'name' => $userName,
                    'cpfCnpj' => $cpfCnpj
                ]
            ]),
        ]);

        Log::info('💰 Pagamento criado na base do TENANT:', [
            'subscription_payment_id' => $subscriptionPayment->id,
            'subscription_id' => $subscription->id
        ]);

        // Preparar dados do cliente
        $customerData = [
            'name' => $userName,
            'email' => $userEmail,
            'cpfCnpj' => $cpfCnpj,
        ];

        // Preparar dados da assinatura
        $tenantName = $tenant->name ?? $tenant->fantasy_name ?? $tenantId;
        $subscriptionData = [
            'cycle' => 'MONTHLY',
            'value' => floatval($plan->price),
            'billingType' => 'UNDEFINED', // Cliente escolhe forma de pagamento
            'description' => "Assinatura {$plan->name} - {$tenantName}",
            'nextDueDate' => now()->format('Y-m-d'), // Gera cobrança imediatamente
            'externalReference' => $externalReference,
        ];

        // SubscriptionController gerencia APENAS planos DOS TENANTS (Corte, Barba, etc)
        // 
        // MODELO SEM SPLIT (preferencial):
        // - Tenant tem asaas_account_id + asaas_api_key → Usa API key da subconta (pagamento 100% na subconta)
        // 
        // MODELO COM SPLIT (fallback):
        // - Tenant tem apenas asaas_wallet_id → Usa split 95% tenant, 5% PagBy
        
        Log::info('🔍 Verificando modelo de pagamento', [
            'tenant_id' => $tenant?->id,
            'asaas_account_id' => $tenant?->asaas_account_id,
            'asaas_wallet_id' => $tenant?->asaas_wallet_id,
            'asaas_api_key' => $tenant?->asaas_api_key ? 'PRESENTE' : 'AUSENTE'
        ]);
        
        $splitData = null;
        $accountApiKey = null;
        
        // MODELO SEM SPLIT: Tenant tem API key própria
        if ($tenant && $tenant->asaas_account_id && $tenant->asaas_api_key) {
            try {
                $accountApiKey = \Illuminate\Support\Facades\Crypt::decryptString($tenant->asaas_api_key);
                Log::info('✅ Modelo SEM SPLIT: Usando API key da subconta', [
                    'account_id' => $tenant->asaas_account_id,
                    'plan_name' => $plan->name,
                    'api_key_preview' => substr($accountApiKey, 0, 20) . '...',
                    'message' => 'Pagamento vai 100% para subconta (sem split)'
                ]);
            } catch (\Exception $e) {
                Log::error('❌ Erro ao descriptografar API key', [
                    'tenant_id' => $tenant->id,
                    'error' => $e->getMessage()
                ]);
                $accountApiKey = null;
            }
        }
        
        // MODELO COM SPLIT: Se não tem API key, usar split com wallet_id
        if (!$accountApiKey && $tenant && $tenant->asaas_wallet_id) {
            $splitData = [
                [
                    'walletId' => $tenant->asaas_wallet_id,
                    'percentualValue' => 95
                ]
            ];
            Log::info('💰 Modelo COM SPLIT: 95% para subconta do tenant', [
                'tenant_wallet' => $tenant->asaas_wallet_id,
                'percentualValue' => 95,
                'plan_name' => $plan->name
            ]);
        }
        
        // Se não tem nem API key nem wallet_id
        if (!$accountApiKey && !$tenant->asaas_wallet_id) {
            Log::error('❌ Tenant sem configuração Asaas', [
                'tenant_id' => $tenant?->id,
                'plan_name' => $plan->name,
                'message' => 'Tenant precisa ter API key ou wallet_id configurado'
            ]);
            
            return redirect()->away(
                "https://{$tenantId}.pagby.com.br/tenant-assinatura/failure"
                . "?message=" . urlencode('Este salão ainda não está configurado para receber pagamentos. Entre em contato com o suporte.')
            );
        }

        try {
            // Criar AsaasService com API key customizada se disponível
            $asaasService = $accountApiKey 
                ? new AsaasService($accountApiKey)
                : $this->asaasService;
            
            // Criar assinatura no Asaas
            $result = $asaasService->criarAssinatura($customerData, $subscriptionData, $splitData, null);

            if (!$result['success']) {
                Log::error('❌ Erro ao criar assinatura:', $result);
                
                $subscriptionPayment->status = 'error';
                $subscriptionPayment->asaas_data = json_encode($result);
                $subscriptionPayment->save();
                
                tenancy()->end();
                
                return redirect()->away(
                    "https://{$tenantId}.pagby.com.br/tenant-assinatura/failure"
                    . "?message=" . urlencode($result['message'] ?? 'Erro ao criar assinatura')
                );
            }

            $asaasSubscription = $result['data'];

            // Buscar a primeira cobrança gerada pela assinatura
            $invoiceUrl = '#';
            $paymentId = null;
            try {
                $cobranças = $asaasService->listarCobrancasAssinatura($asaasSubscription['id']);
                
                Log::info('📋 Cobranças da assinatura:', [
                    'subscription_id' => $asaasSubscription['id'],
                    'cobranças' => $cobranças
                ]);
                
                if ($cobranças && isset($cobranças['data']) && count($cobranças['data']) > 0) {
                    $primeiraCobranca = $cobranças['data'][0];
                    $paymentId = $primeiraCobranca['id'] ?? null; // ✅ ID do PAYMENT
                    $invoiceUrl = $primeiraCobranca['invoiceUrl'] ?? '#';
                    
                    Log::info('✅ Invoice URL e Payment ID encontrados:', [
                        'payment_id' => $paymentId,
                        'invoice_url' => $invoiceUrl
                    ]);
                } else {
                    Log::warning('⚠️ Nenhuma cobrança encontrada para a assinatura');
                }
            } catch (\Exception $e) {
                Log::error('❌ Erro ao buscar cobranças da assinatura:', [
                    'error' => $e->getMessage()
                ]);
            }

            // ✅ FIX: Salvar o ID do PAYMENT (cobrança), não da assinatura
            $subscriptionPayment->asaas_payment_id = $paymentId ?? $asaasSubscription['id']; 
            // TODO: Criar campo asaas_subscription_id se necessário: $subscriptionPayment->asaas_subscription_id = $asaasSubscription['id'];
            $subscriptionPayment->asaas_invoice_url = $invoiceUrl;
            $subscriptionPayment->asaas_data = json_encode($asaasSubscription);
            $subscriptionPayment->status = 'pending';
            $subscriptionPayment->save();
            
            // Atualizar subscription com payment_id
            $subscription->mp_payment_id = $asaasSubscription['id'];
            $subscription->save();

            Log::info('✅ Assinatura Asaas criada com sucesso:', [
                'subscription_payment_id' => $subscriptionPayment->id,
                'subscription_id' => $subscription->id,
                'asaas_subscription_id' => $asaasSubscription['id'],
                'asaas_payment_id' => $paymentId,
                'invoice_url' => $invoiceUrl,
                'status' => 'PENDING - Aguardando confirmação de pagamento'
            ]);

            // NÃO ativar aqui - apenas quando webhook confirmar PAYMENT_RECEIVED ou PAYMENT_CONFIRMED
            
            tenancy()->end();
            
            // Redirecionar para página de aguarde no domínio CENTRAL (pagby.com.br)
            $centralDomains = config('tenancy.central_domains');
            $centralDomain = collect($centralDomains)
                ->filter(fn($domain) => !in_array($domain, ['localhost', '127.0.0.1']))
                ->first() ?? $centralDomains[0];
            
            $waitUrl = "https://{$centralDomain}/tenant-assinatura/wait?"
                . "paymentId={$subscriptionPayment->id}"
                . "&tenant_id={$tenantId}"
                . "&tenant_name=" . urlencode($tenantName)
                . "&checkoutUrl=" . urlencode($invoiceUrl);

            return redirect()->away($waitUrl);

        } catch (\Exception $e) {
            Log::error('❌ Exceção:', [
                'error' => $e->getMessage()
            ]);

            if (isset($subscriptionPayment)) {
                $subscriptionPayment->status = 'error';
                $subscriptionPayment->asaas_data = json_encode(['error' => $e->getMessage()]);
                $subscriptionPayment->save();
            }
            
            tenancy()->end();

            $centralDomains = config('tenancy.central_domains');
            $centralDomain = collect($centralDomains)
                ->filter(fn($domain) => !in_array($domain, ['localhost', '127.0.0.1']))
                ->first() ?? $centralDomains[0];

            return redirect()->away(
                "https://{$centralDomain}/tenant-assinatura/failure"
                . "?message=" . urlencode('Erro: ' . $e->getMessage())
                . "&tenant_id={$tenantId}"
            );
        }
    }

    /**
     * Webhook Asaas
     */
    public function webhook(Request $request)
    {
        Log::info('=== Webhook Asaas ===', $request->all());

        $event = $request->input('event');
        $paymentData = $request->input('payment');

        if (!$event || !$paymentData) {
            Log::warning('Webhook inválido');
            // Sempre responder 200 para evitar erro no Asaas
            return response('OK', 200);
        }

        switch ($event) {
            case 'PAYMENT_RECEIVED':
            case 'PAYMENT_CONFIRMED':
                $this->handlePaymentApproved($paymentData);
                break;
            
            case 'PAYMENT_OVERDUE':
            case 'PAYMENT_DELETED':
                $this->handlePaymentFailed($paymentData);
                break;
        }

        return response('OK', 200);
    }

    /**
     * Cancela assinatura
     */
    public function cancelarAssinatura(Request $request)
    {
        $paymentId = $request->input('payment_id');
        
        if (!tenant()) {
            return redirect()->back()->with('error', 'Contexto de tenant não identificado');
        }
        
        $subscriptionPayment = SubscriptionPayment::find($paymentId);

        if (!$subscriptionPayment || !$subscriptionPayment->asaas_payment_id) {
            return response()->json([
                'success' => false,
                'message' => 'Assinatura não encontrada'
            ]);
        }

        try {
            $result = $this->asaasService->cancelarAssinatura($subscriptionPayment->asaas_payment_id);

            if ($result['success']) {
                $subscriptionPayment->status = 'cancelled';
                $subscriptionPayment->save();
                
                // Cancelar subscription
                $subscription = $subscriptionPayment->subscription;
                $subscription->status = 'Cancelado';
                $subscription->save();
                
                return redirect()->away(
                    "https://" . tenant()->id . ".pagby.com.br/dashboard"
                    . "?tabelaAtiva=planos-de-assinatura"
                    . "&cancel=true"
                    . "&message=" . urlencode('Assinatura cancelada com sucesso!')
                );
            }
            
            return redirect()->back()->with('error', 'Erro ao cancelar');
            
        } catch (\Exception $e) {
            Log::error('Erro ao cancelar:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erro ao cancelar assinatura');
        }
    }

    /**
     * Verifica status da assinatura
     */
    public function checkStatus(Request $request, $paymentId)
    {
        Log::info('🔍 checkStatus iniciado', [
            'payment_id' => $paymentId,
            'tenant_id' => $request->query('tenant_id')
        ]);
        
        try {
            // Obter tenant_id da request
            $tenantId = $request->query('tenant_id');
            
            if (!$tenantId) {
                return response()->json(['error' => 'Tenant ID required'], 400);
            }
            
            // Inicializar tenant manualmente
            $tenant = Tenant::on('mysql')->find($tenantId);
            
            if (!$tenant) {
                return response()->json(['error' => 'Tenant not found'], 404);
            }
            
            // Inicializar o contexto do tenant
            tenancy()->initialize($tenant);
            
            // Buscar o pagamento SEM carregar relacionamentos (para evitar problemas de conexão)
            $subscriptionPayment = SubscriptionPayment::without(['subscription'])->find($paymentId);

            if (!$subscriptionPayment) {
                tenancy()->end();
                return response()->json(['error' => 'Payment not found'], 404);
            }

            Log::info('📋 Subscription Payment encontrado', [
                'payment_id' => $subscriptionPayment->id,
                'asaas_payment_id' => $subscriptionPayment->asaas_payment_id,
                'current_status' => $subscriptionPayment->status
            ]);
            
            // ✅ FIX: Usar API key da SUBCONTA para consultar pagamento
            $accountApiKey = null;
            if ($tenant && $tenant->asaas_account_id && $tenant->asaas_api_key) {
                try {
                    $accountApiKey = \Illuminate\Support\Facades\Crypt::decryptString($tenant->asaas_api_key);
                    Log::info('🔑 Usando API key da subconta para consulta', [
                        'account_id' => $tenant->asaas_account_id,
                        'api_key_preview' => substr($accountApiKey, 0, 20) . '...'
                    ]);
                } catch (\Exception $e) {
                    Log::error('❌ Erro ao descriptografar API key', [
                        'tenant_id' => $tenant->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // Criar AsaasService com API key da subconta (ou usar principal como fallback)
            $asaasService = $accountApiKey 
                ? new AsaasService($accountApiKey)
                : $this->asaasService;
            
            // ✅ FIX: Consultar PAYMENT (não subscription) na Asaas
            if ($subscriptionPayment->asaas_payment_id) {
                try {
                    Log::info('🔄 Consultando status do PAGAMENTO na Asaas', [
                        'asaas_payment_id' => $subscriptionPayment->asaas_payment_id,
                        'using_subaccount_key' => $accountApiKey ? 'SIM' : 'NÃO'
                    ]);
                    
                    // Usar consultarCobranca() ao invés de consultarAssinatura()
                    $asaasPayment = $asaasService->consultarCobranca($subscriptionPayment->asaas_payment_id);

                    if ($asaasPayment) {
                        Log::info('✅ Resposta do PAGAMENTO da Asaas recebida', [
                            'asaas_payment_status' => $asaasPayment['status'] ?? 'N/A',
                            'asaas_payment_id' => $asaasPayment['id'] ?? 'N/A',
                            'value' => $asaasPayment['value'] ?? 'N/A',
                            'payment_date' => $asaasPayment['paymentDate'] ?? 'N/A'
                        ]);
                        
                        // Atualizar asaas_data
                        $subscriptionPayment->asaas_data = json_encode($asaasPayment);
                        
                        // ✅ ATUALIZAR STATUS baseado na resposta da Asaas
                        $asaasStatus = strtoupper($asaasPayment['status'] ?? '');
                        $newStatus = null;
                        
                        switch ($asaasStatus) {
                            case 'PENDING':
                            case 'AWAITING_PAYMENT':
                                $newStatus = 'pending';
                                break;
                            case 'RECEIVED':
                            case 'CONFIRMED':
                                $newStatus = 'confirmed';
                                break;
                            case 'OVERDUE':
                                $newStatus = 'overdue';
                                break;
                            case 'REFUNDED':
                            case 'RECEIVED_IN_CASH':
                                $newStatus = 'refunded';
                                break;
                        }
                        
                        if ($newStatus && $newStatus !== $subscriptionPayment->status) {
                            $oldStatus = $subscriptionPayment->status;
                            $subscriptionPayment->status = $newStatus;
                            
                            Log::info('🔄 Status do pagamento atualizado', [
                                'payment_id' => $subscriptionPayment->id,
                                'old_status' => $oldStatus,
                                'new_status' => $newStatus,
                                'asaas_status' => $asaasStatus
                            ]);
                        }
                        
                        $subscriptionPayment->save();
                    } else {
                        Log::warning('⚠️ Asaas retornou null para o pagamento');
                    }
                } catch (\Exception $e) {
                    Log::error('❌ Erro ao consultar status na Asaas:', [
                        'error' => $e->getMessage(),
                        'payment_id' => $paymentId,
                        'tenant_id' => $tenantId
                    ]);
                }
            }

            // Mapeia os status para formato esperado pelo frontend
            $statusMap = [
                'pending' => 'pending',
                'received' => 'approved',
                'confirmed' => 'approved',
                'active' => 'approved',
                'approved' => 'approved',
                'overdue' => 'pending',
                'cancelled' => 'cancelled',
                'refunded' => 'refunded',
                'error' => 'error',
            ];
            
            $status = strtolower($subscriptionPayment->status);
            $mappedStatus = $statusMap[$status] ?? 'pending';

            // ✅ FIX: Extrair TODOS os dados necessários ANTES de finalizar o tenant context
            $responseData = [
                'status' => $mappedStatus,
                'payment_id' => $subscriptionPayment->id,
                'subscription_id' => $subscriptionPayment->asaas_payment_id,
                'raw_status' => $status,
                'updated_at' => $subscriptionPayment->updated_at // Acessa a data ANTES de tenancy()->end()
            ];

            Log::info('📤 Retornando resposta do checkStatus', $responseData);

            // Finalizar contexto do tenant
            tenancy()->end();

            return response()->json($responseData);
            
        } catch (\Exception $e) {
            // Garantir que tenancy seja finalizado mesmo em caso de erro
            if (tenancy()->initialized) {
                tenancy()->end();
            }
            
            Log::error('Erro no checkStatus:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payment_id' => $paymentId,
                'tenant_id' => $request->query('tenant_id')
            ]);
            
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Páginas de retorno
     */
    public function success(Request $request)
    {
        Log::info('=== Página de Sucesso Acessada ===');
        Log::info('Success Request:', $request->all());
        
        $payment_id = $request->query('payment_id');
        $plan_name = $request->query('plan_name');
        $price = $request->query('price');
        
        // Se veio payment_id e está no contexto tenant, buscar dados
        if ($payment_id && tenant()) {
            try {
                $subscriptionPayment = SubscriptionPayment::find($payment_id);
                if ($subscriptionPayment && $subscriptionPayment->subscription) {
                    $subscription = $subscriptionPayment->subscription;
                    $plan = $subscription->plan;
                    $plan_name = $plan->name ?? 'Plano';
                    $price = number_format($subscriptionPayment->amount, 2, ',', '.');
                }
            } catch (\Exception $e) {
                Log::error('Erro ao buscar subscription payment:', [
                    'payment_id' => $payment_id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return view('tenant-assinatura.success', [
            'plan_name' => $plan_name ?? 'Plano de Assinatura',
            'payment_id' => $payment_id,
            'price' => $price ?? '0,00',
        ]);
    }

    public function pending(Request $request)
    {
        return view('assinatura.pending');
    }

    public function failure(Request $request)
    {
        return view('assinatura.failure', [
            'message' => $request->query('message', 'Erro ao processar assinatura'),
        ]);
    }

    public function wait(Request $request)
    {
        // Aceita ambos os formatos de parâmetros (snake_case e camelCase)
        $payment_id = $request->query('paymentId') ?? $request->query('payment_id');
        $subscription_id = $request->query('subscription_id');
        $invoice_url = $request->query('checkoutUrl') ?? $request->query('invoice_url');
        $tenant_id = $request->query('tenant_id');
        $tenant_name = $request->query('tenant_name');
        
        // Buscar dados - precisa inicializar tenant
        $tenant = Tenant::on('mysql')->find($tenant_id);
        
        if (!$tenant) {
            return redirect()->route('tenant-assinatura.failure', [
                'message' => 'Tenant não encontrado'
            ]);
        }
        
        // Buscar domínio do tenant ANTES de inicializar tenancy
        $tenant_domain_record = \Stancl\Tenancy\Database\Models\Domain::on('mysql')
            ->where('tenant_id', $tenant_id)
            ->first();
        $tenant_domain = $tenant_domain_record ? $tenant_domain_record->domain : "{$tenant_id}.pagby.com.br";
        
        tenancy()->initialize($tenant);
        
        $subscriptionPayment = SubscriptionPayment::find($payment_id);
        
        if (!$subscriptionPayment) {
            tenancy()->end();
            return redirect()->route('tenant-assinatura.failure', [
                'message' => 'Pagamento não encontrado'
            ]);
        }
        
        $subscription = $subscriptionPayment->subscription;
        $plan = $subscription->plan;
        
        // Busca invoice_url do asaas_data se disponível
        if (!$invoice_url && $subscriptionPayment->asaas_data) {
            $asaasData = json_decode($subscriptionPayment->asaas_data, true);
            $invoice_url = $asaasData['invoiceUrl'] ?? null;
        }
        
        tenancy()->end();
        
        return view('tenant-assinatura.wait', [
            'payment' => $subscriptionPayment,
            'subscription_id' => $subscription_id,
            'invoice_url' => $invoice_url,
            'tenant_id' => $tenant_id,
            'tenant_domain' => $tenant_domain,
            'tenant_name' => $tenant_name ?? $tenant->name ?? 'Cliente',
            'plan_name' => $plan->name ?? 'Plano',
            'checkout_url' => $invoice_url ?? '#',
        ]);
    }

    // ============ MÉTODOS PRIVADOS ============

    public function handlePaymentApproved($paymentData)
    {
        // Webhook da Asaas envia dados do PAYMENT (cobrança), não da subscription
        // Precisamos extrair o subscription ID de dentro do payment
        $asaasSubscriptionId = $paymentData['subscription'] ?? null;
        $asaasPaymentId = $paymentData['id'] ?? null;
        
        Log::info('🔍 Webhook Payment Approved recebido', [
            'payment_id' => $asaasPaymentId,
            'subscription_id' => $asaasSubscriptionId,
            'status' => $paymentData['status'] ?? 'N/A',
            'value' => $paymentData['value'] ?? 'N/A'
        ]);
        
        if (!$asaasSubscriptionId && !$asaasPaymentId) {
            Log::warning('⚠️ Webhook sem subscription nem payment ID');
            return;
        }
        
        // Buscar em TODOS os tenants
        $tenants = Tenant::on('mysql')->get();
        
        foreach ($tenants as $tenant) {
            try {
                tenancy()->initialize($tenant);
                
                // Buscar SubscriptionPayment no TENANT pelo ID da subscription
                $subscriptionPayment = null;
                
                if ($asaasSubscriptionId) {
                    $subscriptionPayment = SubscriptionPayment::where('asaas_payment_id', $asaasSubscriptionId)->first();
                }
                
                // Se não encontrou pela subscription, tentar pelo payment ID (caso tenha sido salvo)
                if (!$subscriptionPayment && $asaasPaymentId) {
                    $subscriptionPayment = SubscriptionPayment::where('asaas_payment_id', $asaasPaymentId)->first();
                }
                
                if ($subscriptionPayment) {
                    Log::info('✅ Subscription Payment encontrado no tenant', [
                        'tenant_id' => $tenant->id,
                        'subscription_payment_id' => $subscriptionPayment->id,
                        'old_status' => $subscriptionPayment->status
                    ]);
                    
                    // Atualizar payment no tenant
                    $subscriptionPayment->status = 'received';
                    $subscriptionPayment->payment_date = now();
                    $subscriptionPayment->received_at = now();
                    $subscriptionPayment->confirmed_at = now();
                    $subscriptionPayment->asaas_data = json_encode($paymentData);
                    $subscriptionPayment->save();
                    
                    // Ativar subscription no tenant
                    $subscription = $subscriptionPayment->subscription;
                    $oldStatus = $subscription->status;
                    $subscription->status = 'Ativo';
                    $subscription->start_date = now();
                    $subscription->end_date = now()->addMonth();
                    $subscription->save();
                    
                    Log::info('✅ Pagamento aprovado e assinatura ativada', [
                        'tenant_id' => $tenant->id,
                        'subscription_payment_id' => $subscriptionPayment->id,
                        'subscription_id' => $subscription->id,
                        'payment_status' => 'pending → received',
                        'subscription_status' => "{$oldStatus} → Ativo",
                        'asaas_payment_id' => $asaasPaymentId,
                        'asaas_subscription_id' => $asaasSubscriptionId
                    ]);
                    
                    tenancy()->end();
                    return;
                }
                
                tenancy()->end();
            } catch (\Exception $e) {
                tenancy()->end();
                Log::error('❌ Erro ao processar tenant no webhook', [
                    'tenant_id' => $tenant->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        Log::warning('⚠️ Pagamento não encontrado em nenhum tenant', [
            'asaas_subscription_id' => $asaasSubscriptionId,
            'asaas_payment_id' => $asaasPaymentId
        ]);
    }

    private function handlePaymentFailed($paymentData)
    {
        $asaasSubscriptionId = $paymentData['subscription'] ?? $paymentData['id'];
        
        if (!$asaasSubscriptionId) {
            Log::warning('⚠️ Webhook sem subscription ID');
            return;
        }
        
        Log::info('🔍 Buscando pagamento vencido nos tenants', [
            'asaas_subscription_id' => $asaasSubscriptionId
        ]);

        // Buscar em TODOS os tenants
        $tenants = Tenant::on('mysql')->get();
        
        foreach ($tenants as $tenant) {
            try {
                tenancy()->initialize($tenant);
                
                // Buscar SubscriptionPayment no TENANT
                $subscriptionPayment = SubscriptionPayment::where('asaas_payment_id', $asaasSubscriptionId)->first();
                
                if ($subscriptionPayment) {
                    // Atualizar payment no tenant
                    $subscriptionPayment->status = 'overdue';
                    $subscriptionPayment->asaas_data = json_encode($paymentData);
                    $subscriptionPayment->save();
                    
                    // Cancelar subscription no tenant
                    $subscription = $subscriptionPayment->subscription;
                    $subscription->status = 'Cancelado';
                    $subscription->save();
                    
                    Log::info('⚠️ Pagamento vencido e assinatura cancelada', [
                        'tenant_id' => $tenant->id,
                        'subscription_payment_id' => $subscriptionPayment->id,
                        'subscription_id' => $subscription->id
                    ]);
                    
                    tenancy()->end();
                    return;
                }
                
                tenancy()->end();
            } catch (\Exception $e) {
                tenancy()->end();
                Log::error('Erro ao processar tenant', [
                    'tenant_id' => $tenant->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        Log::warning('⚠️ Pagamento não encontrado em nenhum tenant', [
            'asaas_subscription_id' => $asaasSubscriptionId
        ]);
    }
}

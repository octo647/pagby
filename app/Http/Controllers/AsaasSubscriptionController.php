<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use Illuminate\Support\Facades\Auth;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Services\AsaasService;

class AsaasSubscriptionController extends Controller
{
    protected $asaasService;

    public function __construct(AsaasService $asaasService)
    {
        $this->asaasService = $asaasService;
    }

    /**
     * Exibe página de sucesso
     */
    public function success(Request $request)
    {
        Log::info('=== Asaas Success Route Acessada ===');
        Log::info('Success Request:', $request->all());
        
        $payment_id = $request->query('payment_id');
        $plan_name = $request->query('plan_name');
        $price = $request->query('price');
        
        //  Se veio payment_id, buscar dados do SubscriptionPayment NO TENANT
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
            'plan_name' => $plan_name,
            'payment_id' => $payment_id,
            'price' => $price,
        ]);
    }

    /**
     * Exibe página de pendência
     */
    public function pending(Request $request)
    {
        return view('tenant-assinatura.pending');
    }

    /**
     * Exibe página de falha
     */
    public function failure(Request $request)
    {
        $message = $request->query('message', 'Não foi possível processar sua assinatura.');
        $tenantId = $request->query('tenant_id');    
        $planId = $request->query('plan_id');
        $plan = null;

        if ($planId && tenant()) {
            // Buscar plano na base do TENANT (não na central)
            $plan = \App\Models\Plan::find($planId);
        }

        return view('tenant-assinatura.failure', [
            'tenant_id' => $tenantId,            
            'message' => $message ?? null,
            'plan_id' => $plan->id ?? null,
            'name' => $plan->name ?? null,
        ]);
    }

    /**
     * Exibe página de aguarde
     */
    public function wait(Request $request)
    {
        return view('tenant-assinatura.wait');
    }

    /**
     * Cria assinatura recorrente via Asaas
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

        // Buscar tenant para obter wallet_id (subconta Asaas)
        $tenant = Tenant::on('mysql')->find($tenantId);
        
        if (!$tenant) {
            return redirect()->away("https://{$tenantId}.pagby.com.br/assinatura/failure?message=Salão não encontrado");
        }

        try {
            // Inicializar contexto do tenant
            tenancy()->initialize($tenant);
            
            // Buscar plano na base do TENANT
            $plan = \App\Models\Plan::find($planId);
            
            if (!$plan) {
                return redirect()->away("https://{$tenantId}.pagby.com.br/assinatura/failure?message=Plano não encontrado");
            }
            
            // Buscar usuário no tenant
            $user = User::where('email', $userEmail)->first();
            
            if (!$user) {
                Log::error('❌ Usuário não encontrado no tenant:', [
                    'email' => $userEmail,
                    'tenant_id' => $tenantId
                ]);
                return redirect()->away("https://{$tenantId}.pagby.com.br/assinatura/failure?message=Usuário não encontrado");
            }
            
            // Criar ou buscar assinatura existente no tenant
            $subscription = Subscription::where('user_id', $user->id)
                ->where('plan_id', $plan->id)
                ->first();
            
            if (!$subscription) {
                $subscription = Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'start_date' => now(),
                    'end_date' => now()->addMonth(),
                    'status' => 'Pendente',
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);
                
                Log::info('✨ Nova assinatura criada no tenant (aguardando pagamento):', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $user->id,
                    'plan_id' => $plan->id
                ]);
            }
            
            // Criar referência única para o pagamento
            $externalReference = 'asaas-sub-' . $tenantId . '-' . uniqid();
            
            // Criar registro de pagamento no tenant (PENDING até webhook confirmar)
            $subscriptionPayment = \App\Models\SubscriptionPayment::create([
                'subscription_id' => $subscription->id,
                'asaas_payment_id' => $externalReference, // Será atualizado pelo webhook
                'amount' => $plan->price,
                'billing_type' => 'UNDEFINED',
                'due_date' => now(),
                'status' => 'pending',
                'asaas_data' => json_encode([
                    'tenant_id' => $tenantId,
                    'user_email' => $userEmail,
                    'user_name' => $userName,
                    'cpf_cnpj' => $cpfCnpj,
                    'external_reference' => $externalReference
                ]),
            ]);

            Log::info('💰 Pagamento criado na base do TENANT:', [
                'subscription_payment_id' => $subscriptionPayment->id,
                'subscription_id' => $subscription->id,
                'tenant_id' => $tenantId,
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
                'billingType' => 'UNDEFINED', // Cliente escolhe na hora de pagar
                'description' => "Assinatura {$plan->name} - {$tenantName}",
                'nextDueDate' => now()->format('Y-m-d'), // Gera cobrança imediatamente
                'externalReference' => $externalReference,
            ];

            // SPLIT: Planos de tenants SEMPRE têm split 95% salão / 5% PagBy
            $splitData = null;
            if ($tenant && $tenant->asaas_wallet_id) {
                // Tenant tem subconta configurada: aplicar split
                $splitData = [
                    [
                        'walletId' => $tenant->asaas_wallet_id, // 95% para o salão
                        'percentualValue' => 95
                    ],
                    [
                        'walletId' => '2dd7ca51-c51d-410e-b0f5-6fee73aed5c7', // 5% para PagBy
                        'percentualValue' => 5
                    ]
                ];
                Log::info('💰 Assinatura com split: 95% salão, 5% PagBy', [
                    'tenant_wallet' => $tenant->asaas_wallet_id,
                    'pagby_wallet' => '2dd7ca51-c51d-410e-b0f5-6fee73aed5c7',
                    'tenant_id' => $tenantId,
                    'plan' => $plan->name
                ]);
            } else {
                Log::warning('⚠️ Tenant sem subconta Asaas - split NÃO aplicado!', [
                    'tenant_id' => $tenantId,
                    'plan' => $plan->name,
                    'message' => 'É necessário criar subconta para este tenant'
                ]);
            }

            // Criar assinatura no Asaas
            $result = $this->asaasService->criarAssinatura($customerData, $subscriptionData, $splitData);

            if (!$result['success']) {
                Log::error('❌ Erro ao criar assinatura Asaas:', $result);
                
                // Atualizar status do pagamento no tenant
                $subscriptionPayment->status = 'error';
                $subscriptionPayment->asaas_data = json_encode(array_merge(
                    json_decode($subscriptionPayment->asaas_data, true),
                    ['error' => $result]
                ));
                $subscriptionPayment->save();
                
                // Atualizar status da assinatura
                $subscription->status = 'Erro';
                $subscription->save();
                
                $centralDomains = config('tenancy.central_domains');
                $centralDomain = collect($centralDomains)
                    ->filter(fn($domain) => !in_array($domain, ['localhost', '127.0.0.1']))
                    ->first() ?? $centralDomains[0];
                
                return redirect()->away(
                    "https://{$centralDomain}/tenant-assinatura/failure"
                    . "?message=" . urlencode($result['message'] ?? 'Erro ao criar assinatura')
                    . "&tenant_id={$tenantId}"
                    . "&name=" . urlencode($plan->name)
                );
            }

            $asaasSubscription = $result['data'];

            // Atualizar o registro local com os dados do Asaas
            $subscriptionPayment->asaas_payment_id = $asaasSubscription['id'];
            $subscriptionPayment->asaas_invoice_url = $asaasSubscription['invoiceUrl'] ?? null;
            $subscriptionPayment->asaas_data = json_encode($asaasSubscription);
            // Manter como pending até webhook confirmar pagamento
            $subscriptionPayment->status = 'pending';
            $subscriptionPayment->save();
            
            // Atualizar referência na Subscription
            $subscription->mp_payment_id = $asaasSubscription['id'];
            $subscription->save();

            Log::info('✅ Assinatura Asaas criada com sucesso:', [
                'subscription_payment_id' => $subscriptionPayment->id,
                'subscription_id' => $subscription->id,
                'asaas_subscription_id' => $asaasSubscription['id'],
                'status' => 'pending (aguardando pagamento)'
            ]);

            // Obter link de pagamento (invoice URL) se disponível
            $invoiceUrl = $asaasSubscription['invoiceUrl'] ?? null;
            
            // Redirecionar para página de aguarde no domínio do tenant
            $waitUrl = "https://{$tenantId}.pagby.com.br/assinatura/wait?"
                . "payment_id={$subscriptionPayment->id}"
                . "&subscription_id={$asaasSubscription['id']}";
            
            if ($invoiceUrl) {
                $waitUrl .= "&invoice_url=" . urlencode($invoiceUrl);
            }

            return redirect()->away($waitUrl);

        } catch (\Exception $e) {
            Log::error('❌ Exceção ao criar assinatura Asaas:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Atualizar status se o pagamento foi criado
            if (isset($subscriptionPayment)) {
                $subscriptionPayment->status = 'error';
                $subscriptionPayment->asaas_data = json_encode(array_merge(
                    json_decode($subscriptionPayment->asaas_data, true) ?? [],
                    ['exception' => $e->getMessage()]
                ));
                $subscriptionPayment->save();
            }
            
            // Atualizar status da assinatura se foi criada
            if (isset($subscription)) {
                $subscription->status = 'Erro';
                $subscription->save();
            }

            $centralDomains = config('tenancy.central_domains');
            $centralDomain = collect($centralDomains)
                ->filter(fn($domain) => !in_array($domain, ['localhost', '127.0.0.1']))
                ->first() ?? $centralDomains[0];

            return redirect()->away(
                "https://{$centralDomain}/tenant-assinatura/failure"
                . "?message=" . urlencode('Erro ao processar assinatura: ' . $e->getMessage())
                . "&tenant_id={$tenantId}"
            );
        } finally {
            // Sempre limpar o contexto do tenant
            tenancy()->end();
        }
    }

    /**
     * Webhook Asaas - Recebe notificações de eventos
     */
    public function webhook(Request $request)
    {
        Log::info('=== Webhook Asaas Recebido ===');
        Log::info('Webhook Headers:', $request->headers->all());
        Log::info('Webhook Body:', $request->all());

        $body = $request->all();
        $event = $body['event'] ?? null;
        $paymentData = $body['payment'] ?? null;

        if (!$event || !$paymentData) {
            Log::warning('Webhook inválido - event ou payment ausente');
            return response('Invalid webhook', 400);
        }

        // Eventos relacionados a pagamentos/assinaturas
        switch ($event) {
            case 'PAYMENT_RECEIVED':
            case 'PAYMENT_CONFIRMED':
                $this->handlePaymentApproved($paymentData);
                break;
            
            case 'PAYMENT_OVERDUE':
            case 'PAYMENT_DELETED':
                $this->handlePaymentFailed($paymentData);
                break;
            
            case 'PAYMENT_CREATED':
                $this->handlePaymentCreated($paymentData);
                break;
        }

        return response('OK', 200);
    }

    /**
     * Processa pagamento aprovado
     */
    private function handlePaymentApproved($paymentData)
    {
        $asaasSubscriptionId = $paymentData['subscription'] ?? $paymentData['id'] ?? null;
        
        if (!$asaasSubscriptionId) {
            Log::warning('❌ Webhook: Pagamento sem ID', $paymentData);
            return;
        }

        Log::info('🔍 Webhook: Buscando pagamento por asaas_payment_id:', [
            'asaas_subscription_id' => $asaasSubscriptionId
        ]);

        // Buscar em todos os tenants qual tem este asaas_payment_id
        $tenants = Tenant::on('mysql')->get();
        
        foreach ($tenants as $tenant) {
            try {
                tenancy()->initialize($tenant);
                
                // Buscar pelo asaas_payment_id
                $subscriptionPayment = \App\Models\SubscriptionPayment::where('asaas_payment_id', $asaasSubscriptionId)->first();
                
                if ($subscriptionPayment) {
                    Log::info('✅ Webhook: Pagamento encontrado no tenant:', [
                        'tenant_id' => $tenant->id,
                        'subscription_payment_id' => $subscriptionPayment->id
                    ]);
                    
                    $oldStatus = $subscriptionPayment->status;
                    $subscriptionPayment->status = 'received';
                    $subscriptionPayment->payment_date = now();
                    $subscriptionPayment->received_at = now();
                    $subscriptionPayment->confirmed_at = now();
                    $subscriptionPayment->asaas_data = json_encode($paymentData);
                    $subscriptionPayment->save();

                    Log::info('✅ Webhook: Pagamento atualizado:', [
                        'subscription_payment_id' => $subscriptionPayment->id,
                        'old_status' => $oldStatus,
                        'new_status' => 'received'
                    ]);

                    // Ativar assinatura
                    $subscription = $subscriptionPayment->subscription;
                    if ($subscription) {
                        $subscription->status = 'Ativo';
                        $subscription->start_date = now();
                        $subscription->end_date = now()->addMonth();
                        $subscription->save();
                        
                        Log::info('✅ Webhook: Assinatura ativada:', [
                            'subscription_id' => $subscription->id,
                            'status' => 'Ativo',
                            'end_date' => $subscription->end_date
                        ]);
                    }
                    
                    tenancy()->end();
                    return;
                }
                
                tenancy()->end();
            } catch (\Exception $e) {
                tenancy()->end();
                Log::error('❌ Webhook: Erro ao verificar tenant:', [
                    'tenant_id' => $tenant->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        Log::warning('⚠️ Webhook: Pagamento não encontrado em nenhum tenant:', [
            'asaas_subscription_id' => $asaasSubscriptionId
        ]);
    }

    /**
     * Processa pagamento falhado ou vencido
     */
    private function handlePaymentFailed($paymentData)
    {
        $asaasSubscriptionId = $paymentData['subscription'] ?? $paymentData['id'] ?? null;
        
        if (!$asaasSubscriptionId) {
            Log::warning('❌ Webhook Failed: Pagamento sem ID', $paymentData);
            return;
        }

        Log::info('🔍 Webhook Failed: Buscando pagamento:', [
            'asaas_subscription_id' => $asaasSubscriptionId
        ]);

        // Buscar em todos os tenants
        $tenants = Tenant::on('mysql')->get();
        
        foreach ($tenants as $tenant) {
            try {
                tenancy()->initialize($tenant);
                
                $subscriptionPayment = \App\Models\SubscriptionPayment::where('asaas_payment_id', $asaasSubscriptionId)->first();
                
                if ($subscriptionPayment) {
                    Log::info('✅ Webhook Failed: Pagamento encontrado:', [
                        'tenant_id' => $tenant->id,
                        'subscription_payment_id' => $subscriptionPayment->id
                    ]);
                    
                    $subscriptionPayment->status = 'overdue';
                    $subscriptionPayment->asaas_data = json_encode($paymentData);
                    $subscriptionPayment->save();

                    // Desativar assinatura
                    $subscription = $subscriptionPayment->subscription;
                    if ($subscription) {
                        $subscription->status = 'Cancelado';
                        $subscription->save();
                        
                        Log::info('⚠️ Webhook: Assinatura cancelada:', [
                            'subscription_id' => $subscription->id,
                            'status' => 'Cancelado'
                        ]);
                    }
                    
                    tenancy()->end();
                    return;
                }
                
                tenancy()->end();
            } catch (\Exception $e) {
                tenancy()->end();
                Log::error('❌ Webhook Failed: Erro ao verificar tenant:', [
                    'tenant_id' => $tenant->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        Log::warning('⚠️ Webhook Failed: Pagamento não encontrado:', [
            'asaas_subscription_id' => $asaasSubscriptionId
        ]);
    }

    /**
     * Processa criação de pagamento (cobrança gerada pela assinatura)
     */
    private function handlePaymentCreated($paymentData)
    {
        Log::info('💳 Nova cobrança criada pela assinatura:', [
            'payment_id' => $paymentData['id'] ?? null,
            'subscription_id' => $paymentData['subscription'] ?? null,
            'value' => $paymentData['value'] ?? null
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use App\Models\TenantPlan;
use App\Models\TenantsPlansPayment;
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
        Log::info('Host:', ['host' => $request->getHost()]);
        
        $host = $request->getHost();
        $isCentral = in_array($host, ['www.pagby.com.br', 'pagby.com.br', 'localhost']);
        
        // Se está no CENTRAL, redireciona para o tenant
        if ($isCentral) {
            $payment_id = $request->query('payment_id');
            $subscriptionId = $request->query('subscription_id');
            
            $payment = null;
            
            if ($payment_id) {
                $payment = TenantsPlansPayment::on('mysql')->find($payment_id);
            } elseif ($subscriptionId) {
                $payment = TenantsPlansPayment::on('mysql')
                    ->where('asaas_subscription_id', $subscriptionId)
                    ->first();
            }
            
            if ($payment && $payment->tenant_id) {
                $tenantDomain = "{$payment->tenant_id}.pagby.com.br";
                $successUrl = "https://{$tenantDomain}/tenant-assinatura/success"
                    . "?payment_id={$payment->id}"
                    . "&plan_name=" . urlencode($payment->plan)
                    . "&price=" . urlencode(number_format($payment->amount, 2, ',', '.'));
                
                Log::info('Redirecionando para tenant:', ['url' => $successUrl]);
                return redirect()->away($successUrl);
            }
            
            Log::error('Pagamento não encontrado no central', [
                'payment_id' => $payment_id,
                'subscription_id' => $subscriptionId
            ]);
            return redirect()->route('home')->with('error', 'Pagamento não encontrado');
        }
        
        // Se está no TENANT, exibe a página de sucesso
        $payment_id = $request->query('payment_id');
        $plan_name = $request->query('plan_name');
        $price = $request->query('price');
        
        if ($payment_id) {
            $payment = TenantsPlansPayment::on('mysql')->find($payment_id);
            if ($payment) {
                $plan_name = $payment->plan;
                $price = number_format($payment->amount, 2, ',', '.');
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

        if ($planId) {
            $plan = TenantPlan::find($planId);
            if ($plan && $plan->tenant) {
                $tenantId = $plan->tenant_id;
            }
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

        // Busca o plano do tenant no central
        $plan = TenantPlan::on('mysql')
            ->where('tenant_id', $tenantId)
            ->where('plan_id', $planId)
            ->first();

        if (!$plan) {
            return redirect()->away("https://{$tenantId}.pagby.com.br/assinatura/failure?message=Plano não encontrado");
        }

        // Buscar tenant para obter wallet_id (subconta Asaas)
        $tenant = Tenant::on('mysql')->find($tenantId);
        
        // Cria registro local do pagamento (status pending) NA BASE CENTRAL
        $payment = TenantsPlansPayment::on('mysql')->create([
            'external_id' => 'asaas-subscription-' . uniqid(),
            'tenant_id' => $tenantId,
            'plan_id' => $planId,
            'plan' => $plan->name,
            'amount' => $plan->price,
            'status' => 'PENDING',
            'payer_data' => json_encode([
                'email' => $userEmail,
                'name' => $userName,
                'cpfCnpj' => $cpfCnpj
            ]),
        ]);

        Log::info('💰 Pagamento criado na base central:', [
            'payment_id' => $payment->id,
            'tenant_id' => $tenantId,
        ]);

        // Preparar dados do cliente
        $customerData = [
            'name' => $userName,
            'email' => $userEmail,
            'cpfCnpj' => $cpfCnpj,
        ];

        // Preparar dados da assinatura
        $subscriptionData = [
            'cycle' => 'MONTHLY',
            'value' => floatval($plan->price),
            'billingType' => 'UNDEFINED', // Cliente escolhe na hora de pagar
            'description' => "Assinatura {$plan->name} - PagBy",
            'nextDueDate' => now()->addDays(7)->format('Y-m-d'),
            'externalReference' => $payment->external_id,
        ];

        // NÃO configurar split - Assinatura PagBy é 100% para PagBy
        // Split é APENAS para quando cliente assina plano DO SALÃO (não da plataforma)
        $splitData = null;
        
        Log::info('💰 Assinatura PagBy: 100% PagBy (sem split)', [
            'tenant_id' => $tenantId,
            'plan' => $plan->name
        ]);

        try {
            // Criar assinatura no Asaas
            $result = $this->asaasService->criarAssinatura($customerData, $subscriptionData, $splitData);

            if (!$result['success']) {
                Log::error('❌ Erro ao criar assinatura Asaas:', $result);
                
                $payment->status = 'ERROR';
                $payment->asaas_data = json_encode($result);
                $payment->save();
                
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
            $payment->asaas_subscription_id = $asaasSubscription['id'];
            $payment->asaas_data = json_encode($asaasSubscription);
            // Manter como PENDING até webhook confirmar pagamento
            $payment->status = 'PENDING';
            $payment->save();

            Log::info('✅ Assinatura Asaas criada:', [
                'payment_id' => $payment->id,
                'asaas_subscription_id' => $asaasSubscription['id'],
                'status' => 'PENDING (aguardando pagamento)'
            ]);

            // NÃO ativar aqui - só webhook deve ativar após confirmar pagamento

            // Obter link de pagamento (invoice URL) se disponível
            $invoiceUrl = $asaasSubscription['invoiceUrl'] ?? null;
            
            // Redirecionar para página de aguarde no domínio do tenant
            $waitUrl = "https://{$tenantId}.pagby.com.br/assinatura/wait?"
                . "payment_id={$payment->id}"
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

            $payment->status = 'ERROR';
            $payment->asaas_data = json_encode(['error' => $e->getMessage()]);
            $payment->save();

            $centralDomains = config('tenancy.central_domains');
            $centralDomain = collect($centralDomains)
                ->filter(fn($domain) => !in_array($domain, ['localhost', '127.0.0.1']))
                ->first() ?? $centralDomains[0];

            return redirect()->away(
                "https://{$centralDomain}/tenant-assinatura/failure"
                . "?message=" . urlencode('Erro ao processar assinatura: ' . $e->getMessage())
                . "&tenant_id={$tenantId}"
            );
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
        $subscriptionId = $paymentData['subscription'] ?? null;
        
        if (!$subscriptionId) {
            Log::warning('Pagamento sem subscription_id', $paymentData);
            return;
        }

        // Buscar pagamento local pela subscription_id
        $payment = TenantsPlansPayment::on('mysql')
            ->where('asaas_subscription_id', $subscriptionId)
            ->first();

        if (!$payment) {
            Log::warning('Pagamento local não encontrado', [
                'subscription_id' => $subscriptionId
            ]);
            return;
        }

        $oldStatus = $payment->status;
        $payment->status = 'ACTIVE';
        $payment->asaas_data = json_encode($paymentData);
        $payment->save();

        Log::info('✅ Pagamento atualizado via webhook:', [
            'payment_id' => $payment->id,
            'old_status' => $oldStatus,
            'new_status' => 'ACTIVE'
        ]);

        // Ativar assinatura do tenant
        $this->activateTenantSubscription($payment);
    }

    /**
     * Processa pagamento falhado ou vencido
     */
    private function handlePaymentFailed($paymentData)
    {
        $subscriptionId = $paymentData['subscription'] ?? null;
        
        if (!$subscriptionId) {
            return;
        }

        $payment = TenantsPlansPayment::on('mysql')
            ->where('asaas_subscription_id', $subscriptionId)
            ->first();

        if (!$payment) {
            return;
        }

        $payment->status = 'OVERDUE';
        $payment->asaas_data = json_encode($paymentData);
        $payment->save();

        Log::info('⚠️ Pagamento vencido via webhook:', [
            'payment_id' => $payment->id,
            'status' => 'OVERDUE'
        ]);

        // Desativar assinatura do tenant
        $this->inactivateTenantSubscription($payment);
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

    /**
     * Ativa assinatura do tenant
     */
    private function activateTenantSubscription($payment)
    {
        if (in_array($payment->status, ['ACTIVE', 'APPROVED'])) {
            // Ativar o plano do tenant no central
            $tenantPlan = TenantPlan::on('mysql')
                ->where('tenant_id', $payment->tenant_id)
                ->where('name', $payment->plan)
                ->first();
            
            if ($tenantPlan) {
                $tenantPlan->active = true;
                $tenantPlan->save();
                
                Log::info('✅ Plano do tenant ativado:', [
                    'tenant_id' => $payment->tenant_id,
                    'name' => $tenantPlan->name
                ]);
            }

            // Inserir/atualizar assinatura na tabela subscriptions na base do tenant    
            $tenant = Tenant::on('mysql')->find($payment->tenant_id);
        
            if ($tenant) {
                $tenant->run(function () use ($payment, $tenantPlan) {
                    $payerData = is_array($payment->payer_data) 
                        ? $payment->payer_data 
                        : json_decode($payment->payer_data, true);
                    
                    $email = $payerData['email'] ?? null;
                    $user_id = null;

                    if ($email) {
                        $user_id = User::where('email', $email)->first()?->id;
                    }

                    $existing = Subscription::where('user_id', $user_id)
                        ->where('plan_id', $tenantPlan->plan_id)
                        ->first();

                    if ($existing) {
                        $existing->status = 'Ativo';
                        $existing->start_date = now();
                        $existing->end_date = now()->addMonth();
                        $existing->updated_by = $user_id;
                        $existing->save();
                        
                        Log::info('🔄 Assinatura atualizada no tenant');
                    } else {
                        Subscription::create([
                            'user_id' => $user_id,
                            'plan_id' => $tenantPlan->plan_id,
                            'mp_payment_id' => $payment->asaas_subscription_id,
                            'start_date' => now(),
                            'end_date' => now()->addMonth(),
                            'status' => 'Ativo',
                            'created_by' => $user_id,
                            'updated_by' => $user_id,
                        ]);
                        
                        Log::info('✨ Nova assinatura criada no tenant');
                    }
                });
            }
        }
    }

    /**
     * Desativa assinatura do tenant
     */
    private function inactivateTenantSubscription($payment)
    {
        if (in_array($payment->status, ['OVERDUE', 'CANCELLED', 'EXPIRED'])) {
            $tenantPlan = TenantPlan::on('mysql')
                ->where('tenant_id', $payment->tenant_id)
                ->where('name', $payment->plan)
                ->first();
            
            if ($tenantPlan) {
                $tenantPlan->active = false;
                $tenantPlan->save();
            }

            // Mudar o status da assinatura na base do tenant    
            $tenant = Tenant::on('mysql')->find($payment->tenant_id);
        
            if ($tenant) {
                $tenant->run(function () use ($payment, $tenantPlan) {
                    $payerData = is_array($payment->payer_data) 
                        ? $payment->payer_data 
                        : json_decode($payment->payer_data, true);
                    
                    $email = $payerData['email'] ?? null;
                    $user_id = null;

                    if ($email) {
                        $user_id = User::where('email', $email)->first()?->id;
                    }

                    $existing = Subscription::where('user_id', $user_id)
                        ->where('plan_id', $tenantPlan->plan_id)
                        ->first();

                    if ($existing) {
                        $existing->status = 'Cancelado';
                        $existing->updated_by = $user_id;
                        $existing->save();

                        Log::info('🚫 Assinatura cancelada no tenant:', [
                            'user_id' => $user_id,
                            'plan_id' => $existing->plan_id,
                            'tenant_id' => $payment->tenant_id,
                        ]);
                    }
                });
            }
        }
    }

    /**
     * Cancela assinatura
     */
    public function cancelarAssinatura(Request $request)
    {
        $paymentId = $request->input('payment_id');
        $payment = TenantsPlansPayment::on('mysql')->find($paymentId);

        if (!$payment || !$payment->asaas_subscription_id) {
            return response()->json([
                'success' => false, 
                'message' => 'Assinatura não encontrada.'
            ]);
        }

        try {
            $result = $this->asaasService->cancelarAssinatura($payment->asaas_subscription_id);

            if ($result['success']) {
                $payment->status = 'CANCELLED';
                $payment->save();
                
                $this->inactivateTenantSubscription($payment);
                
                $tenantDomain = "{$payment->tenant_id}.pagby.com.br";
                return redirect()->away(
                    "https://{$tenantDomain}/dashboard?tabelaAtiva=planos-de-assinatura&cancel=1"
                    . "&message=" . urlencode('Assinatura cancelada com sucesso!')
                );
            } else {
                return redirect()->back()->with('warning', 'Erro ao cancelar no Asaas.');
            }
        } catch (\Exception $e) {
            Log::error('Erro ao cancelar assinatura:', [
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Erro ao cancelar assinatura.');
        }
    }

    /**
     * Verifica status da assinatura
     */
    public function checkStatus($paymentId)
    {
        $payment = TenantsPlansPayment::on('mysql')->find($paymentId);

        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        if (!$payment->asaas_subscription_id) {
            return response()->json([
                'status' => $payment->status,
                'message' => 'Aguardando criação da assinatura'
            ]);
        }

        try {
            $asaasSubscription = $this->asaasService->consultarAssinatura($payment->asaas_subscription_id);

            if ($asaasSubscription) {
                // Apenas salvar os dados da assinatura, NÃO atualizar status do payment
                // O status da ASSINATURA (ACTIVE) é diferente do status do PAGAMENTO (PENDING/RECEIVED)
                $payment->asaas_data = json_encode($asaasSubscription);
                $payment->save();

                Log::info('✅ Dados da assinatura atualizados (status do payment mantido):', [
                    'payment_id' => $payment->id,
                    'payment_status' => $payment->status, // mantém PENDING
                    'subscription_status' => $asaasSubscription['status'] ?? 'unknown' // pode ser ACTIVE
                ]);

                // NÃO ativar aqui - só webhook deve ativar após confirmar PAGAMENTO
            }
        } catch (\Exception $e) {
            Log::error('Erro ao consultar status Asaas:', [
                'error' => $e->getMessage()
            ]);
        }

        return response()->json([
            'status' => $payment->status,
            'subscription_id' => $payment->asaas_subscription_id,
            'payment_id' => $payment->id,
            'updated_at' => $payment->updated_at->toISOString()
        ]);
    }

    /**
     * Debug de pagamento
     */
    public function debugPayment($paymentId)
    {
        $payment = TenantsPlansPayment::on('mysql')->find($paymentId);
        
        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        $debugInfo = [
            'payment' => [
                'id' => $payment->id,
                'tenant_id' => $payment->tenant_id,
                'external_id' => $payment->external_id,
                'asaas_subscription_id' => $payment->asaas_subscription_id,
                'status' => $payment->status,
                'plan' => $payment->plan,
                'amount' => $payment->amount,
                'created_at' => $payment->created_at,
                'updated_at' => $payment->updated_at
            ],
            'asaas_data' => $payment->asaas_data ? json_decode($payment->asaas_data, true) : null
        ];

        Log::info('🔧 Debug Asaas Payment Info:', $debugInfo);

        return response()->json($debugInfo);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\TenantPlan;
use App\Models\TenantsPlansPayment;
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

        // Buscar plano na tabela tenants_plans (banco CENTRAL)
        $plan = TenantPlan::on('mysql')
            ->where('tenant_id', $tenantId)
            ->where('plan_id', $planId)
            ->first();

        if (!$plan) {
            Log::error('Plano não encontrado', [
                'tenant_id' => $tenantId,
                'plan_id' => $planId,
                'user_email' => $userEmail
            ]);
            
            return redirect()->away("https://{$tenantId}.pagby.com.br/tenant-assinatura/failure?message=Plano não encontrado");
        }

        // Buscar tenant para obter wallet_id
        $tenant = Tenant::on('mysql')->find($tenantId);
        
        // Criar registro do pagamento
        $payment = TenantsPlansPayment::on('mysql')->create([
            'external_id' => 'asaas-sub-' . uniqid(),
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

        Log::info('💰 Pagamento criado:', ['payment_id' => $payment->id]);

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
            'billingType' => 'UNDEFINED', // Cliente escolhe forma de pagamento
            'description' => "Assinatura {$plan->name} - PagBy",
            'nextDueDate' => now()->addDays(7)->format('Y-m-d'),
            'externalReference' => $payment->external_id,
        ];

        // SubscriptionController gerencia APENAS planos DOS TENANTS (Corte, Barba, etc)
        // SEMPRE aplicar split: 90% para o salão, 10% para PagBy
        $splitData = null;
        if ($tenant && $tenant->asaas_wallet_id) {
            $splitData = [
                'walletId' => $tenant->asaas_wallet_id,
                'percentualValue' => 90, // 90% para o salão, 10% PagBy
            ];
            Log::info('💰 Plano do Tenant: Split 90% salão, 10% PagBy', [
                'plan_name' => $plan->name,
                'wallet_id' => $tenant->asaas_wallet_id
            ]);
        } else {
            Log::warning('⚠️ Tenant sem wallet_id - split NÃO configurado!', [
                'tenant_id' => $tenant?->id,
                'plan_name' => $plan->name
            ]);
        }

        try {
            // Criar assinatura no Asaas
            $result = $this->asaasService->criarAssinatura($customerData, $subscriptionData, $splitData);

            if (!$result['success']) {
                Log::error('❌ Erro ao criar assinatura:', $result);
                
                $payment->status = 'ERROR';
                $payment->asaas_data = json_encode($result);
                $payment->save();
                
                return redirect()->away(
                    "https://{$tenantId}.pagby.com.br/tenant-assinatura/failure"
                    . "?message=" . urlencode($result['message'] ?? 'Erro ao criar assinatura')
                );
            }

            $asaasSubscription = $result['data'];

            // Atualizar registro local
            $payment->asaas_subscription_id = $asaasSubscription['id'];
            $payment->asaas_data = json_encode($asaasSubscription);
            // Manter como PENDING até webhook confirmar pagamento
            $payment->status = 'PENDING';
            $payment->save();

            Log::info('✅ Assinatura criada:', [
                'payment_id' => $payment->id,
                'asaas_subscription_id' => $asaasSubscription['id'],
                'status' => 'PENDING - Aguardando confirmação de pagamento'
            ]);

            // NÃO ativar aqui - apenas quando webhook confirmar PAYMENT_RECEIVED ou PAYMENT_CONFIRMED

            // Obter link de pagamento
            $invoiceUrl = $asaasSubscription['invoiceUrl'] ?? null;
            
            // Redirecionar para página de aguarde no domínio CENTRAL (pagby.com.br)
            $centralDomains = config('tenancy.central_domains');
            $centralDomain = collect($centralDomains)
                ->filter(fn($domain) => !in_array($domain, ['localhost', '127.0.0.1']))
                ->first() ?? $centralDomains[0];
            
            $waitUrl = "https://{$centralDomain}/tenant-assinatura/wait?"
                . "payment_id={$payment->id}"
                . "&subscription_id={$asaasSubscription['id']}"
                . "&tenant_id={$tenantId}";
            
            if ($invoiceUrl) {
                $waitUrl .= "&invoice_url=" . urlencode($invoiceUrl);
            }

            return redirect()->away($waitUrl);

        } catch (\Exception $e) {
            Log::error('❌ Exceção:', [
                'error' => $e->getMessage()
            ]);

            $payment->status = 'ERROR';
            $payment->asaas_data = json_encode(['error' => $e->getMessage()]);
            $payment->save();

            $centralDomains = config('tenancy.central_domains');
            $centralDomain = collect($centralDomains)
                ->filter(fn($domain) => !in_array($domain, ['localhost', '127.0.0.1']))
                ->first() ?? $centralDomains[0];

            return redirect()->away(
                "https://{$centralDomain}/assinatura/failure"
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
            return response('Invalid', 400);
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
        $payment = TenantsPlansPayment::on('mysql')->find($paymentId);

        if (!$payment || !$payment->asaas_subscription_id) {
            return response()->json([
                'success' => false,
                'message' => 'Assinatura não encontrada'
            ]);
        }

        try {
            $result = $this->asaasService->cancelarAssinatura($payment->asaas_subscription_id);

            if ($result['success']) {
                $payment->status = 'CANCELLED';
                $payment->save();
                
                $this->inactivateTenantSubscription($payment);
                
                return redirect()->away(
                    "https://{$payment->tenant_id}.pagby.com.br/dashboard"
                    . "?tabelaAtiva=planos-de-assinatura"
                    . "&message=" . urlencode('Assinatura cancelada!')
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
    public function checkStatus($paymentId)
    {
        $payment = TenantsPlansPayment::on('mysql')->find($paymentId);

        if (!$payment) {
            return response()->json(['error' => 'Not found'], 404);
        }

        if ($payment->asaas_subscription_id) {
            try {
                $asaasSubscription = $this->asaasService->consultarAssinatura($payment->asaas_subscription_id);

                if ($asaasSubscription) {
                    // NÃO atualizar o status do payment com status da subscription
                    // A subscription pode estar ACTIVE mas o pagamento ainda PENDING
                    // Apenas atualizar asaas_data para referência
                    $payment->asaas_data = json_encode($asaasSubscription);
                    $payment->save();

                    // NÃO ativar baseado no status da subscription
                    // Apenas o webhook deve ativar quando pagamento for confirmado
                }
            } catch (\Exception $e) {
                Log::error('Erro ao consultar status:', ['error' => $e->getMessage()]);
            }
        }

        // Mapeia os status do Asaas para formato esperado pelo frontend
        $statusMap = [
            'PENDING' => 'pending',
            'RECEIVED' => 'approved',
            'CONFIRMED' => 'approved',
            'ACTIVE' => 'approved',
            'APPROVED' => 'approved',
            'OVERDUE' => 'pending',
            'REFUNDED' => 'refunded',
            'RECEIVED_IN_CASH' => 'approved',
        ];
        
        $asaasStatus = strtoupper($payment->status);
        $mappedStatus = $statusMap[$asaasStatus] ?? 'pending';

        return response()->json([
            'status' => $mappedStatus,
            'payment_id' => $payment->id,
            'subscription_id' => $payment->asaas_subscription_id,
            'asaas_status' => $asaasStatus,
            'updated_at' => $payment->updated_at
        ]);
    }

    /**
     * Páginas de retorno
     */
    public function success(Request $request)
    {
        $host = $request->getHost();
        $isCentral = in_array($host, ['www.pagby.com.br', 'pagby.com.br', 'localhost']);
        
        if ($isCentral) {
            $payment_id = $request->query('payment_id');
            $payment = TenantsPlansPayment::on('mysql')->find($payment_id);
            
            if ($payment) {
                return redirect()->away(
                    "https://{$payment->tenant_id}.pagby.com.br/tenant-assinatura/success?payment_id={$payment->id}"
                );
            }
        }
        
        // Busca dados do pagamento para exibir na página
        $payment_id = $request->query('payment_id');
        $payment = null;
        $plan_name = $request->query('plan_name');
        $price = $request->query('price');
        
        if ($payment_id) {
            $payment = TenantsPlansPayment::on('mysql')->find($payment_id);
            if ($payment) {
                // Tenta extrair nome do plano e valor do registro de pagamento
                if (!$plan_name && $payment->plan) {
                    $plan_name = $payment->plan;
                }
                if (!$price && $payment->amount) {
                    $price = number_format($payment->amount, 2, ',', '.');
                }
            }
        }
        
        return view('tenant-assinatura.success', [
            'payment_id' => $payment_id,
            'plan_name' => $plan_name ?? 'Plano de Assinatura',
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
        $payment_id = $request->query('payment_id');
        $subscription_id = $request->query('subscription_id');
        $invoice_url = $request->query('invoice_url');
        $tenant_id = $request->query('tenant_id');
        
        $payment = TenantsPlansPayment::on('mysql')->find($payment_id);
        
        if (!$payment) {
            return redirect()->route('tenant-assinatura.failure', [
                'message' => 'Pagamento não encontrado'
            ]);
        }
        
        // Se tenant_id não foi passado, pega do payment
        if (!$tenant_id) {
            $tenant_id = $payment->tenant_id;
        }
        
        // Busca dados do tenant e plano
        $tenant = Tenant::find($tenant_id);
        $asaasData = null;
        $invoiceUrl = null;
        
        if ($payment->asaas_data) {
            $asaasData = json_decode($payment->asaas_data, true);
            $invoiceUrl = $asaasData['invoiceUrl'] ?? null;
        }
        
        // Se não tem invoice_url, tenta pegar do asaas_data
        if (!$invoice_url && $invoiceUrl) {
            $invoice_url = $invoiceUrl;
        }
        
        return view('tenant-assinatura.wait', [
            'payment' => $payment,
            'subscription_id' => $subscription_id,
            'invoice_url' => $invoice_url,
            'tenant_id' => $tenant_id,
            'tenant_name' => $tenant ? $tenant->name : 'Cliente',
            'plan_name' => $payment->plan ?? 'Plano',
            'checkout_url' => $invoice_url ?? '#',
        ]);
    }

    /**
     * Debug (remover em produção)
     */
    public function debugPayment($paymentId)
    {
        $payment = TenantsPlansPayment::on('mysql')->find($paymentId);
        
        if (!$payment) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json([
            'payment' => $payment,
            'asaas_data' => json_decode($payment->asaas_data, true),
        ]);
    }

    // ============ MÉTODOS PRIVADOS ============

    public function handlePaymentApproved($paymentData)
    {
        $subscriptionId = $paymentData['subscription'] ?? null;
        if (!$subscriptionId) return;

        $payment = TenantsPlansPayment::on('mysql')
            ->where('asaas_subscription_id', $subscriptionId)
            ->first();

        if ($payment) {
            $payment->status = 'ACTIVE';
            $payment->asaas_data = json_encode($paymentData);
            $payment->save();

            Log::info('✅ Pagamento aprovado', ['payment_id' => $payment->id]);
            $this->activateTenantSubscription($payment);
        }
    }

    private function handlePaymentFailed($paymentData)
    {
        $subscriptionId = $paymentData['subscription'] ?? null;
        if (!$subscriptionId) return;

        $payment = TenantsPlansPayment::on('mysql')
            ->where('asaas_subscription_id', $subscriptionId)
            ->first();

        if ($payment) {
            $payment->status = 'OVERDUE';
            $payment->asaas_data = json_encode($paymentData);
            $payment->save();

            Log::info('⚠️ Pagamento vencido', ['payment_id' => $payment->id]);
            $this->inactivateTenantSubscription($payment);
        }
    }

    public function activateTenantSubscription($payment)
    {
        if (!in_array($payment->status, ['ACTIVE', 'APPROVED'])) return;

        Log::info('🔄 Ativando assinatura do tenant:', [
            'payment_id' => $payment->id,
            'tenant_id' => $payment->tenant_id,
            'plan_name' => $payment->plan
        ]);

        // Ativar plano
        $tenantPlan = TenantPlan::on('mysql')
            ->where('tenant_id', $payment->tenant_id)
            ->where('name', $payment->plan)
            ->first();
        
        if ($tenantPlan) {
            $tenantPlan->active = true;
            $tenantPlan->save();
            Log::info('✅ TenantPlan ativado:', ['plan_id' => $tenantPlan->plan_id]);
        } else {
            Log::warning('⚠️ TenantPlan não encontrado para ativar');
        }

        // Criar/atualizar assinatura na base do tenant
        $tenant = Tenant::on('mysql')->find($payment->tenant_id);
        
        if (!$tenant) {
            Log::error('❌ Tenant não encontrado:', ['tenant_id' => $payment->tenant_id]);
            return;
        }
            
        $tenant->run(function () use ($payment, $tenantPlan) {
            $payerData = json_decode($payment->payer_data, true);
            $email = $payerData['email'] ?? null;
            $user_id = null;

            if ($email) {
                $user = User::where('email', $email)->first();
                $user_id = $user?->id;
                Log::info('👤 Usuário encontrado:', ['user_id' => $user_id, 'email' => $email]);
            } else {
                Log::warning('⚠️ Email não encontrado no payer_data');
            }

            // Se não encontrou tenantPlan, buscar plan_id do payment
            $plan_id = $tenantPlan?->plan_id ?? null;
            
            if (!$plan_id) {
                // Tentar extrair do campo plan do payment (pode ser JSON)
                Log::warning('⚠️ plan_id não encontrado, tentando extrair do payment');
                return;
            }

            $existing = Subscription::where('user_id', $user_id)
                ->where('plan_id', $plan_id)
                ->first();

            if ($existing) {
                $existing->update([
                    'status' => 'Ativo',
                    'start_date' => now(),
                    'end_date' => now()->addMonth(),
                    'updated_by' => $user_id,
                ]);
                Log::info('✅ Subscription atualizada:', ['id' => $existing->id]);
            } else {
                $subscription = Subscription::create([
                    'user_id' => $user_id,
                    'plan_id' => $plan_id,
                    'mp_payment_id' => $payment->asaas_subscription_id,
                    'start_date' => now(),
                    'end_date' => now()->addMonth(),
                    'status' => 'Ativo',
                    'created_by' => $user_id,
                    'updated_by' => $user_id,
                ]);
                Log::info('✅ Subscription criada:', ['id' => $subscription->id]);
            }
        });
    }

    private function inactivateTenantSubscription($payment)
    {
        if (!in_array($payment->status, ['OVERDUE', 'CANCELLED', 'EXPIRED'])) return;

        $tenantPlan = TenantPlan::on('mysql')
            ->where('tenant_id', $payment->tenant_id)
            ->where('name', $payment->plan)
            ->first();
        
        if ($tenantPlan) {
            $tenantPlan->active = false;
            $tenantPlan->save();
        }

        $tenant = Tenant::on('mysql')->find($payment->tenant_id);
        
        if ($tenant) {
            $tenant->run(function () use ($payment, $tenantPlan) {
                $payerData = json_decode($payment->payer_data, true);
                $email = $payerData['email'] ?? null;
                $user_id = null;

                if ($email) {
                    $user_id = User::where('email', $email)->first()?->id;
                }

                $existing = Subscription::where('user_id', $user_id)
                    ->where('plan_id', $tenantPlan->plan_id)
                    ->first();

                if ($existing) {
                    $existing->update([
                        'status' => 'Cancelado',
                        'updated_by' => $user_id,
                    ]);
                }
            });
        }
    }
}

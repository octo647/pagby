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


class SubscriptionController extends Controller
{
   
   
    
    // Exibe página de sucesso
    public function success(Request $request)
    {
        $payment_id = $request->query('payment_id');
        $payment = TenantsPlansPayment::on('mysql')->where('external_id', $payment_id)->first();
      
        if ($payment) {
        $plan_name = $payment->plan;
        
        
        } else {
            $plan = null;
           
        }
        return view('tenant-assinatura.success', [
            
            'plan_name' => $plan_name ? $plan_name : null,
            'payment_id' => $payment_id,
            'price' => $payment ? number_format($payment->amount, 2, ',', '.') : null,
        ]);
    }

    // Exibe página de pendência
    public function pending(Request $request)
    {
        return view('tenant-assinatura.pending');
    }

    // Exibe página de falha
    public function failure(Request $request)
    {
      
        $message = $request->query('message', 'Não foi possível processar sua assinatura.');

        // Tente obter o tenant pelo plano ou assinatura
        $tenantId = $request->query('tenant_id');    
        $planId = $request->query('plan_id');

    if ($planId) {
        
        $plan = \App\Models\TenantPlan::find($planId);
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

    // Exibe página de aguarde
    public function wait(Request $request)
    {
        return view('tenant-assinatura.wait');
    }

    // Webhook MercadoPago
   
    public function webhook(Request $request)
{
    Log::info('=== Webhook MercadoPago Tenant Recebido ===');
    Log::info('Webhook Headers:', $request->headers->all());
    Log::info('Webhook Body:', $request->all());

    $body = $request->all();
    $topic = $body['topic'] ?? ($body['type'] ?? null);
    $dataId = $body['data']['id'] ?? ($body['id'] ?? ($body['resource'] ?? null));
    $accessToken = config('services.tenant.access_token');

    Log::info('🔍 Dados extraídos do webhook:', [
        'topic' => $topic,
        'dataId' => $dataId,
    ]);

    // Trata eventos de assinatura (preapproval)
    if (in_array($topic, ['preapproval', 'subscription_preapproval']) && $dataId) {
        $url = "https://api.mercadopago.com/preapproval/{$dataId}";
        $response = $this->mpGet($url, $accessToken);
        if ($response['http_code'] === 200) {
            $mpSubscription = $response['body'];
            Log::info('📄 Assinatura recebida via webhook:', [
                'mp_subscription_id' => $mpSubscription['id'],
                'status' => $mpSubscription['status'],
                'external_reference' => $mpSubscription['external_reference'] ?? 'não definido'
            ]);
            // Atualiza status local
            if (isset($mpSubscription['external_reference'])) {
                $payment = TenantsPlansPayment::where('external_id', $mpSubscription['external_reference'])->first();
                if ($payment) {
                    $oldStatus = $payment->status;
                    $payment->status = $mpSubscription['status'];
                    $payment->mercadopago_data = json_encode($mpSubscription);
                    $payment->save();
                    Log::info('✅ Assinatura atualizada:', [
                        'local_id' => $payment->id,
                        'old_status' => $oldStatus,
                        'new_status' => $payment->status,
                    ]);
                    if (in_array($mpSubscription['status'], ['authorized', 'active'])) {
                        $this->activateTenantSubscription($payment);
                    }
                    if (in_array($mpSubscription['status'], ['cancelled', 'paused'])) {
                        $this->inactivateTenantSubscription($payment);
                    }
                }
            }
        }
    }

    // Trata eventos de pagamento (payment ou subscription_authorized_payment)
    if (in_array($topic, ['payment', 'subscription_authorized_payment']) && $dataId) {
        $url = "https://api.mercadopago.com/v1/payments/{$dataId}";
        $response = $this->mpGet($url, $accessToken);
        if ($response['http_code'] === 200) {
            $mpPayment = $response['body'];
            Log::info('💳 Pagamento recebido via webhook:', [
                'mp_payment_id' => $mpPayment['id'],
                'status' => $mpPayment['status'],
                'external_reference' => $mpPayment['external_reference'] ?? 'não definido'
            ]);
            // Atualiza status local
            if (isset($mpPayment['external_reference'])) {
                $payment = TenantsPlansPayment::where('external_id', $mpPayment
                ['external_reference'])->first();
                if ($payment) {
                    $oldStatus = $payment->status;
                    $payment->status = $mpPayment['status'];
                    $payment->mercadopago_data = json_encode($mpPayment);
                    $payment->save();
                    Log::info('✅ Pagamento atualizado:', [
                        'local_id' => $payment->id,
                        'old_status' => $oldStatus,
                        'new_status' => $payment->status,
                    ]);
                    if (in_array($mpPayment['status'], ['approved', 'authorized'])) {
                        $this->activateTenantSubscription($payment);
                    }
                    
                    if (in_array($mpPayment['status'], ['cancelled', 'paused'])) {
                        $this->inactivateTenantSubscription($payment);
                    }

                }
            }
        }
    }

    return response('OK', 200);
}

// Helper para GET MercadoPago
private function mpGet($url, $accessToken)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return [
        'http_code' => $httpCode,
        'body' => json_decode($response, true)
    ];
}

    private function activateTenantSubscription($payment)
    {
        
        if ($payment->status === 'authorized' || $payment->status === 'active') {
            // Ativar o plano do tenant no central
            $tenantPlan = TenantPlan::on('mysql')->where('tenant_id', $payment->tenant_id)->where('name', $payment->plan)->first();
            
            if ($tenantPlan) {
                $tenantPlan->active = true;
                $tenantPlan->save();
                
                Log::info('✅ Plano do tenant ativado:', [
                    'tenant_id' => $payment->tenant_id,
                    'name' => $tenantPlan->name
                ]);
            }
        //insere a assinatura na tabela subscriptions na base do tenant    
        $tenant = Tenant::on('mysql')->find($payment->tenant_id);
    
        if ($tenant) {
            $tenant->run(function () use ($payment, $tenantPlan) {
                // Buscar o usuário na base do tenant
        $payerData = is_array($payment->payer_data) ? $payment->payer_data : json_decode($payment->payer_data, true);
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
        } else {

                Subscription::create([
                    'user_id' => $user_id, // ou o ID do usuário associado, se aplicável
                    'plan_id' => $tenantPlan->plan_id,
                    'mp_payment_id' => $payment->mp_payment_id,
                    'start_date' => now(),
                    'end_date' => now()->addMonth(),
                    'status' => 'Ativo',
                    'created_by' => $user_id,
                    'updated_by' => $user_id, 
                ]);
            }
        });
    }
        }
    }
   private function inactivateTenantSubscription($payment)
    {
        
        
        if ($payment->status === 'cancelled' || $payment->status === 'paused') {
            // Ativar o plano do tenant no central 
            $tenantPlan = TenantPlan::on('mysql')->where('tenant_id', $payment->tenant_id)->where('name', $payment->plan)->first();
            
            if ($tenantPlan) {
                $tenantPlan->active = true;
                $tenantPlan->save();
                
                Log::info('✅ Plano do tenant ativado:', [
                    'tenant_id' => $payment->tenant_id,
                    'name' => $tenantPlan->name
                ]);
            }
            
        //muda o status da assinatura na tabela subscriptions na base do tenant    
        $tenant = Tenant::on('mysql')->find($payment->tenant_id);
    
        if ($tenant) {
            $tenant->run(function () use ($payment, $tenantPlan) {
                // Buscar o usuário na base do tenant
        $payerData = is_array($payment->payer_data) ? $payment->payer_data : json_decode($payment->payer_data, true);
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
            $existing->start_date = now();
            $existing->end_date = now()->addMonth();
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

    
    public function store(Request $request)
    {
    // Recebe os dados via GET ou POST
    $planId = $request->input('plan_id');
    $tenantId = $request->input('tenant_id');
    $userEmail = $request->input('user_email');
  
    

    // Busca o plano do tenant no central
    $plan = TenantPlan::on('mysql')->where('tenant_id', $tenantId)
        ->where('plan_id', $planId)
        ->first();
        

    if (!$plan) {
        return redirect()->away("https://{$tenantId}.pagby.com.br/tenant-assinatura/failure?message=Plano não encontrado.");
    }

    // Cria registro local do pagamento (status pending) NA BASE CENTRAL
    $payment = TenantsPlansPayment::on('mysql')->create([
        'external_id' => 'tenant-subscription-' . uniqid(),
        'tenant_id' => $tenantId,
        'plan' => $plan->name,
        'amount' => $plan->price,        
        'status' => 'pending',
        'payer_data' => json_encode(['email' => $userEmail]),
    ]);
    

    Log::info('💰 Pagamento criado na base central:', [
        'payment_id' => $payment->id,
        'tenant_id' => $tenantId,

        
    ]);

    // Cria assinatura recorrente via API MercadoPago ORIGINÁRIA DO CENTRAL
    $accessToken = config('services.tenant.access_token'); // Token do central

    $data = [
        "reason" => $plan->name,
        "auto_recurring" => [
            "frequency" => 1,
            "frequency_type" => "months",
            "transaction_amount" => floatval($plan->price),
            "currency_id" => "BRL",
            
        ],
        "payer_email" => $userEmail,
        "back_url" => route('tenant-assinatura.congrats'), // Rota do central
        "status" => "pending",
        "external_reference" => $payment->external_id,
        "notification_url" => route('tenant-assinatura.webhook') // Webhook do central
    ];

    

    // Requisição ORIGINÁRIA DO DOMÍNIO CENTRAL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.mercadopago.com/preapproval');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json',
        'User-Agent: PagBy-Central/1.0' // Identificar como origem central
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
 


    // Se MercadoPago retornar erro
    if (isset($result['message'])) {
     
        Log::error('❌ Erro MercadoPago:', $result);
        return redirect()->away("https://{$tenantId}.pagby.com.br/tenant-assinatura/failure?message=" . urlencode($result['message']) . "&tenant_id={$tenantId}&name=" . urlencode($plan->name));
    }
    

    // Atualiza o registro local COM OS DADOS DO MERCADO PAGO
    if (isset($result['id'])) {
        $payment->mp_payment_id = $result['id'];
        $payment->mercadopago_data = json_encode($result);
        $payment->save();

        Log::info('✅ Assinatura MercadoPago criada:', [
            'payment_id' => $payment->id,
            'mp_subscription_id' => $result['id']
        ]);
    }
   

    // CRIA REGISTRO NA BASE DO TENANT (foo)
    $this->activateTenantSubscription($payment);

    // Redireciona de volta para o tenant específico
    if (isset($result['init_point'])) {
        $checkoutUrl = $result['init_point'];
    } else {
        $checkoutUrl = "https://www.mercadopago.com.br/checkout/v1/subscription/redirect/{$result['id']}";
    }

    // Redireciona para a página de WAIT DO TENANT ESPECÍFICO
    $waitUrl = "https://{$tenantId}.pagby.com.br/tenant-assinatura/wait?"
        . "paymentId={$payment->id}"
        . "&checkoutUrl=" . urlencode($checkoutUrl)
        . "&tenant_name=" . urlencode($plan->tenant->name ?? 'Tenant')
        . "&mp_subscription_id=" . ($result['id'] ?? '');

    return redirect()->away($waitUrl);
}
    public function cancelarAssinatura(Request $request)
    {
        $paymentId = $request->input('payment_id');
        
        
        $payment = TenantsPlansPayment::on('mysql')->find($paymentId);

        if (!$payment || !$payment->mp_payment_id) {
            return response()->json(['success' => false, 'message' => 'Assinatura não encontrada.']);
        }

        $accessToken = config('services.tenant.access_token');
        $url = "https://api.mercadopago.com/preapproval/{$payment->mp_payment_id}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['status' => 'cancelled']));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        // ... código de cancelamento no MercadoPago ...

    if ($httpCode === 200) {
        $payment->status = 'cancelled';
        $payment->save();
        $this->inactivateTenantSubscription($payment);
        $tenantDomain = "{$payment->tenant_id}.pagby.com.br";
        return redirect()->away("https://{$tenantDomain}/dashboard?tabelaAtiva=planos-de-assinatura&cancel=1&message=" . urlencode('Assinatura cancelada com sucesso!'));

        
    } else {
        return redirect()->back()->with('warning', 'Erro ao cancelar no MercadoPago.');
    }

        
    }

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
            'mp_payment_id' => $payment->mp_payment_id,
            'status' => $payment->status,
            'plan' => $payment->plan,
            'amount' => $payment->amount,
            'created_at' => $payment->created_at,
            'updated_at' => $payment->updated_at
        ],
        'mercadopago_data' => $payment->mercadopago_data ? json_decode($payment->mercadopago_data, true) : null
    ];

    Log::info('🔧 Debug Tenant Payment Info:', $debugInfo);

    return response()->json($debugInfo);
}



/**
 * Callback para quando o usuário completa o checkout com sucesso
 */
public function congrats(Request $request)
{

    Log::info('=== MercadoPago Congrats Callback ===');
    Log::info('Congrats Request:', $request->all());
    
    $subscriptionId = $request->get('preference-id') ?? $request->get('subscription_id');
    $status = $request->get('status', 'approved');
    
    if ($subscriptionId) {
        // Buscar pagamento pelo ID da assinatura
        $payment = TenantsPlansPayment::where('mp_payment_id', $subscriptionId)->first();
        
        if ($payment) {
            $payment->status = $status === 'approved' ? 'authorized' : $status;
            $payment->save();
            
            Log::info('✅ Pagamento atualizado via congrats:', [
                'payment_id' => $payment->id,
                'status' => $payment->status
            ]);
            
            // Ativar tenant se aprovado
            if ($status === 'approved') {
                $this->activateTenantSubscription($payment);
            }
            
            return redirect()->away("https://{$payment->tenant_id}.pagby.com.br/tenant-assinatura/success?payment_id={$payment->id}");
        }
    }
    
    // Fallback para success normal
    return redirect()->route('tenant-assinatura.success');
}

/**
 * Callback para quando o usuário cancela o checkout
 */
public function close(Request $request)
{
    Log::info('=== MercadoPago Close Callback ===');
    Log::info('Close Request:', $request->all());
    
    $subscriptionId = $request->get('preference-id') ?? $request->get('subscription_id');
    
    if ($subscriptionId) {
        $payment = TenantsPlansPayment::where('mp_payment_id', $subscriptionId)->first();
        if ($payment) {
            return redirect()->away("https://{$payment->tenant_id}.pagby.com.br/tenant-assinatura/failure?payment_id={$payment->id}&message=Pagamento cancelado pelo usuário");
        }
    }

    return redirect()->away("https://{$payment->tenant_id}.pagby.com.br/tenant-assinatura/failure?message=Pagamento cancelado&tenant={$payment->tenant_id}&name=" . urlencode($payment->plan));
}

public function checkPayments()
{
    $payments = TenantsPlansPayment::orderBy('created_at', 'DESC')
        ->limit(10)
        ->get(['id', 'external_id', 'status', 'mp_payment_id', 'created_at']);
    
    Log::info('📋 Últimos pagamentos:', $payments->toArray());
    
    return response()->json($payments);
}

public function checkStatus($paymentId)
{
    $payment = TenantsPlansPayment::on('mysql')->find($paymentId); 


    if (!$payment) {
        Log::error('Pagamento não encontrado para checkStatus:', ['payment_id' => $paymentId]);
        return response()->json(['error' => 'Payment not found'], 404);
    }

    Log::info('🔍 Verificando status da assinatura:', [
        'payment_id' => $paymentId,
        'current_status' => $payment->status,
        'external_id' => $payment->external_id,
        'mp_payment_id' => $payment->mp_payment_id
    ]);

    // Tentar primeiro com external_id, depois com mp_payment_id
    $preapprovalId = $payment->mp_payment_id;
   

    if (!$preapprovalId || strpos($preapprovalId, 'tenant-subscription-') === 0) {
        Log::warning('ID da assinatura não disponível ou temporário:', ['preapproval_id' => $preapprovalId]);
        return response()->json([
            'status' => $payment->status,
            'external_id' => $payment->external_id,
            'mp_payment_id' => $payment->mp_payment_id,
            'message' => 'Aguardando ID da assinatura'
        ]);
    }

    try {
        $accessToken = config('services.tenant.access_token');
        $url = 'https://api.mercadopago.com/preapproval/' . $preapprovalId;

        Log::info('Consultando MercadoPago:', ['url' => $url]);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
                'User-Agent: PagBy/1.0'
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        Log::info('📊 Resposta MercadoPago:', [
            'http_code' => $httpCode,
            'preapproval_id' => $preapprovalId,
            'curl_error' => $curlError
        ]);

        if ($httpCode === 200 && $response) {
            $mpSubscription = json_decode($response, true);

            if (isset($mpSubscription['id']) && isset($mpSubscription['status'])) {
                $oldStatus = $payment->status;
                $payment->status = $mpSubscription['status'];
                $payment->mp_payment_id = $mpSubscription['id'];
                
                // Atualizar external_id se estiver vazio
                if (!$payment->external_id) {
                    $payment->external_id = $mpSubscription['id'];
                }
                
                $payment->save();

                Log::info('✅ Status atualizado com sucesso:', [
                    'payment_id' => $payment->id,
                    'old_status' => $oldStatus,
                    'new_status' => $payment->status,
                    'mp_payment_id' => $payment->mp_payment_id
                ]);
            } else {
                Log::warning('Resposta incompleta do MercadoPago:', $mpSubscription);
            }
        } else {
            Log::error('Erro na consulta ao MercadoPago:', [
                'http_code' => $httpCode,
                'response' => $response,
                'curl_error' => $curlError
            ]);
        }

    } catch (\Exception $e) {
        Log::error('❌ Exceção ao consultar MercadoPago:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }

    return response()->json([
        'status' => $payment->status,
        'external_id' => $payment->external_id,
        'mp_payment_id' => $payment->mp_payment_id,
        'payment_id' => $payment->id,
        'updated_at' => $payment->updated_at->toISOString()
    ]);
}

}

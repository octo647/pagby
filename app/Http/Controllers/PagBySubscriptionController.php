<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\PagByPayment;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Preapproval;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\Entities\Preference;
use MercadoPago\Entities\Item;
use MercadoPago\Entities\Payer;

// app/Http/Controllers/PagBySubscriptionController.php
// Controlador para gerenciar pagamentos via MercadoPago dos planos de assinatura PagBy

class PagBySubscriptionController extends Controller
{
   private function configureMercadoPago()
{
    $accessToken = config('services.pagby.access_token');
    $environment = config('services.pagby.environment', 'sandbox');

    Log::info('=== Configurando MercadoPago ===');
    try {
        Log::info('Método 1 - Config', [
            'token' => substr($accessToken, 0, 20) . '...',
            'environment' => $environment
        ]);
        MercadoPagoConfig::setAccessToken($accessToken);
        Log::info('MercadoPago configurado com sucesso!');
    } catch (\Exception $e) {
        Log::error('❌ Erro ao configurar MercadoPago:', [
            'error' => $e->getMessage(),
            'environment' => $environment
        ]);
        throw $e;
    }
}

 public function choosePlan($plan)
{
    $plans = [
        'basico' => [
            'name' => 'Plano Básico',
            'description' => 'Ideal para começar',
            'price' => 29.90
        ],
        'premium' => [
            'name' => 'Plano Premium',
            'description' => 'Para quem quer mais',
            'price' => 59.90
        ]
    ];

    if (!isset($plans[$plan])) {
        return redirect()->route('home')->with('error', 'Plano não encontrado.');
    }

    $selectedPlan = $plans[$plan];
    $planData = $plans[$plan]; // ADICIONAR esta linha
    session(['selected_plan' => $plan]);

    return view('pagby-subscription.choose-plan', compact('selectedPlan', 'plan', 'planData'));
}
 public function selectPlan(Request $request)

{
    $plans = [
        'basico' => [
            'name' => 'Plano Básico',
            'description' => 'Ideal para começar',
            'price' => 29.90
        ],
        'premium' => [
            'name' => 'Plano Premium',
            'description' => 'Para quem quer mais',
            'price' => 59.90
        ]
    ];
//dd($request->all());
    $plan = $request->input('plan');
    $tenantId = $request->input('tenant_id');
    

    $selectedPlan = $plans[$plan];
    $planData = $plans[$plan]; // ADICIONAR esta linha
    session(['selected_plan' => $plan]);

    return view('pagby-subscription.select-plan', compact('selectedPlan', 'plan', 'planData'));
}
 


public function createSubscription(Request $request)
{
    $request->validate([
        'plan' => 'required|in:basico,premium',
        'tenant_id' => 'required|string'
    ]);

    $contactId = session('contact_id');
    $tenantId = $request->tenant_id;
    $mpPaymentId = $request->_token;
    

    if (!$contactId) {
        return redirect()->route('home')->with('error', 'Sessão expirada. Faça o registro novamente.');
    }

    $contact = Contact::find($contactId);
    if (!$contact) {
        return redirect()->route('home')->with('error', 'Contato não encontrado.');
    }

    $plans = [
        'basico' => [
            'name' => 'Básico',
            'description' => 'Ideal para começar',
            'price' => 29.90
        ],
        'premium' => [
            'name' => 'Premium',
            'description' => 'Para quem quer mais',
            'price' => 59.90
        ]
    ];

    $plan = $plans[$request->plan];

    // Cria registro local da assinatura
  
    $payment = PagByPayment::create([        
    'tenant_id' => 'temp_' . $contact->id,
    'contact_id' => $contact->id, // ADICIONAR este campo
    'mp_payment_id' => null,
    'external_id' => null, // ADICIONAR explicitamente
    'amount' => $contact->employee_count * $plan['price'], // Remover number_format
    'status' => 'pending',
    'plan' => $request->plan,
    'employee_count' => $contact->employee_count,
    'type' => 'subscription'
]);
    Log::info('📄 PagByPayment criado:', [
        'payment_id' => $payment->id,
        'amount' => $payment->amount,
        'plan' => $payment->plan
    ]);
  

    // Cria assinatura recorrente via API MercadoPago
    $accessToken = config('services.pagby.access_token');
    $data = [
        "reason" => $plan['name'],
        "auto_recurring" => [
            "frequency" => 1,
            "frequency_type" => "months",
            "transaction_amount" => number_format($contact->employee_count * $plan['price'], 2, '.', ''),
            "currency_id" => "BRL"
        ],
        "payer_email" => 'test_user_1515774707033786032@testuser.com',//$contact->email,
        "back_url" => route('pagby-subscription.success'),
        "status" => "pending",
        "external_reference" => 'pagby-subscription-' . $payment->id,
        "notification_url" => route('pagby-subscription.webhook')
    ];
    

    

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.mercadopago.com/preapproval');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    // Salva o id da assinatura recorrente
    if (isset($result['id'])) {
        $payment->mp_payment_id = $result['id'];
        $payment->external_id = $result['id'];
        $payment->save();
    }
    Log::info('Resposta MercadoPago Preapproval:', ['result' => $result]);
        // Redireciona para o checkout da assinatura
        if (isset($result['init_point'])) {
        session([
            'payment_id' => $payment->id,
            'checkout_url' => $result['init_point'],
            'tenant_name' => $contact->tenant_name,
            'plan_name' => $plan['name'],
            'plan_amount' => floatval($contact->employee_count * $plan['price'])
        ]);
        // Redireciona para a página de aguarde
        return redirect()->route('pagby-subscription.wait', ['paymentId' => $payment->id]);
        } else {
            // Redireciona para a página de falha
            return redirect()->route('pagby-subscription.failure', [
                'payment_id' => $payment->id,
                'tenant_name' => $contact->tenant_name,
                'plan_name' => $plan['name'],
                'message' => $result['message'] ?? 'Erro ao criar assinatura.'
                
            ]);
        }
}

public function renewSubscription(Request $request)
{
    $request->validate([
        'plan' => 'required|in:basico,premium',
        'tenant_id' => 'required|string'
    ]);
    

    $tenantId = $request->tenant_id;
    $tenant_name = Tenant::on('mysql')->find($tenantId)->name;
    $contact = Contact::on('mysql')->where('tenant_name', $tenant_name)->first();
    




    $mpPaymentId = $request->_token;
   
    

    if (!$contact->id) {
        return redirect()->route('home')->with('error', 'Sessão expirada. Faça o registro novamente.');
    }

    
    if (!$contact) {
        return redirect()->route('home')->with('error', 'Contato não encontrado.');
    }

    $plans = [
        'basico' => [
            'name' => 'Básico',
            'description' => 'Ideal para começar',
            'price' => 29.90
        ],
        'premium' => [
            'name' => 'Premium',
            'description' => 'Para quem quer mais',
            'price' => 59.90
        ]
    ];

    $plan = $plans[$request->plan];
    

    // Cria registro local da assinatura
  
    $payment = PagByPayment::on('mysql')->create([        
    'tenant_id' => $tenantId,
    'contact_id' => $contact->id, // ADICIONAR este campo
    'mp_payment_id' => null,
    'external_id' => null, // ADICIONAR explicitamente
    'amount' => $contact->employee_count * $plan['price'], // Remover number_format
    'status' => 'pending',
    'plan' => $request->plan,
    'employee_count' => $contact->employee_count,
    'type' => 'subscription'
]);


    Log::info('📄 PagByPayment criado:', [
        'payment_id' => $payment->id,
        'amount' => $payment->amount,
        'plan' => $payment->plan
    ]);
  

    // Cria assinatura recorrente via API MercadoPago
    $accessToken = config('services.pagby.access_token');
    $data = [
        "reason" => $plan['name'],
        "auto_recurring" => [
            "frequency" => 1,
            "frequency_type" => "months",
            "transaction_amount" => number_format($contact->employee_count * $plan['price'], 2, '.', ''),
            "currency_id" => "BRL"
        ],
        "payer_email" => 'test_user_1515774707033786032@testuser.com', //$contact->email,
        "back_url" => route('pagby-subscription.success'),
        "status" => "pending",
        "external_reference" => 'pagby-subscription-' . $payment->id,
        "notification_url" => route('pagby-subscription.webhook')
    ];
    

    

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.mercadopago.com/preapproval');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
   

    // Salva o id da assinatura recorrente
   
    if (isset($result['id'])) {
        $payment->mp_payment_id = $result['id'];
        $payment->external_id = $result['id'];
        $payment->save();
    }
Log::info('Resposta MercadoPago Preapproval:', ['result' => $result]);
    // Redireciona para o checkout da assinatura
    if (isset($result['init_point'])) {
    session([
        'payment_id' => $payment->id,
        'checkout_url' => $result['init_point'],
        'tenant_name' => $contact->tenant_name,
        'plan_name' => $plan['name'],
        'plan_amount' => floatval($contact->employee_count * $plan['price'])
    ]);
 
    // Redireciona para a página de aguarde
   
    return redirect()->route('tenant-assinatura.waitRenew', ['paymentId' => $payment->id]);

   // $this->waitRenew($payment->id);
} else {
    // Redireciona para a página de falha
    return redirect()->route('pagby-subscription.failure-renew', [
        'payment_id' => $payment->id,
        'tenant_name' => $contact->tenant_name,
        'plan_name' => $plan['name'],
        'message' => $result['message'] ?? 'Erro ao criar assinatura.'
        
    ]);
}
}

public function failureRenew(Request $request)
{
    Log::info('=== MercadoPago Failure Renew Callback ===');
    Log::info('Request completo:', $request->all());
    

    return view('pagby-subscription.failure-renew', [
        'payment_id' => $request->get('payment_id'),
        'tenant_name' => $request->get('tenant_name'),
        'plan_name' => $request->get('plan_name'),
        'message' => $request->get('message', 'Não foi possível processar sua renovação de assinatura.')
    ]);
}

// Adapte o webhook para assinaturas

public function webhook(Request $request)
{
    Log::info('=== MercadoPago Webhook RECEBIDO11 ===');
    Log::info('Webhook Headers:', $request->headers->all());
    Log::info('Webhook Body:', $request->all());

    $type = $request->input('type');
    $dataId = $request->input('data.id');

    if ($type === 'preapproval' && $dataId) {
        try {
            $accessToken = config('services.pagby.access_token');
            $url = 'https://api.mercadopago.com/v1/preapproval/' . $dataId;

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

            Log::info('📦 Resposta webhook assinatura:', [
                'http_code' => $httpCode,
                'preapproval_id' => $dataId
            ]);

            if ($httpCode === 200) {
                $mpSubscription = json_decode($response, true);

                Log::info('💰 Assinatura recebida via webhook:', [
                    'mp_id' => $mpSubscription['id'],
                    'status' => $mpSubscription['status'],
                    'external_reference' => $mpSubscription['external_reference'] ?? 'não definido'
                ]);

                // Buscar assinatura local pelo external_reference
                if (isset($mpSubscription['external_reference']) &&
                    strpos($mpSubscription['external_reference'], 'pagby-subscription-') === 0) {

                    $paymentId = str_replace('pagby-subscription-', '', $mpSubscription['external_reference']);
                    $payment = PagByPayment::find($paymentId);

                    if ($payment) {
                        $payment->status = $mpSubscription['status'];
                        $payment->mp_payment_id = $mpSubscription['id'];
                        $externalReference = 'pagby-subscription-' . $payment->id;
                        $payment->external_reference = $externalReference;
                        $payment->save();
                        

                        Log::info('✅ Assinatura atualizada via webhook:', [
                            'local_id' => $payment->id,
                            'new_status' => $payment->status,
                            'mp_id' => $mpSubscription['id']
                        ]);
                    } else {
                        Log::warning('⚠️ Assinatura local não encontrada:', ['payment_id' => $paymentId]);
                    }
                }
            }

        } catch (\Exception $e) {
            Log::error('❌ Erro no webhook assinatura:', [
                'error' => $e->getMessage(),
                'data_id' => $dataId
            ]);
        }
    }

    return response('OK', 200);
}
 



    private function updatePaymentFromMercadoPagoObject(PagByPayment $payment, $mpPayment)
    {
        $payment->status = $mpPayment->status;
        $payment->external_id = $mpPayment->id;
        $payment->save();
        
        Log::info('Payment atualizado:', [
            'local_id' => $payment->id,
            'mp_id' => $mpPayment->id,
            'status' => $mpPayment->status
        ]);
    }
    public function retorno(Request $request)
    {
        $status = $request->get('collection_status');
        if ($status === 'approved') {
            return redirect()->route('pagby-subscription.success');
        } elseif ($status === 'pending') {
            return redirect()->route('pagby-subscription.pending');
        } else {
            return redirect()->route('pagby-subscription.failure');
        }
    }

    public function success(Request $request)
    {
        Log::info('=== MercadoPago Success Callback ===');
        Log::info('Request completo:', $request->all());

        $paymentId = $request->get('external_reference');
        

        return view('pagby-subscription.success', [
            'payment_id' => $paymentId ?? session('payment_id'),
            'tenant_name' => session('tenant_name')
        ]);
    }

    public function successRenew(Request $request)
    {
        Log::info('=== MercadoPago Success Renew Callback ===');
        Log::info('Request completo:', $request->all());

        $paymentId = $request->get('external_reference');
        

        return view('pagby-subscription.success-renew', [
            'payment_id' => $paymentId ?? session('payment_id'),
            'plan' => session('plan_name'),
            'tenant_name' => session('tenant_name')
        ]);
    }

    public function failure(Request $request)
    {
        Log::info('=== MercadoPago Failure Callback ===');
        Log::info('Request completo:', $request->all());
        

        return view('pagby-subscription.failure', [
            'payment_id' => $request->get('payment_id'),
            'tenant_name' => $request->get('tenant_name'),
            'plan_name' => $request->get('plan_name'),
            'message' => $request->get('message', 'Não foi possível processar seu pagamento.')
        ]);
    }
    public function pending(Request $request)
    {
        Log::info('=== MercadoPago Pending Callback ===');
        Log::info('Request completo:', $request->all());

        return view('pagby-subscription.pending', [
            'payment_id' => $request->get('payment_id'),
            'tenant_name' => session('tenant_name')
        ]);
    }

public function checkStatus($paymentId)
{
    $payment = PagByPayment::on('mysql')->find($paymentId); // Corrigir nome da classe

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
    $preapprovalId = $payment->external_id ?? $payment->mp_payment_id;

    if (!$preapprovalId || strpos($preapprovalId, 'temp_') !== false) {
        Log::warning('ID da assinatura não disponível ou temporário:', ['preapproval_id' => $preapprovalId]);
        return response()->json([
            'status' => $payment->status,
            'external_id' => $payment->external_id,
            'mp_payment_id' => $payment->mp_payment_id,
            'message' => 'Aguardando ID da assinatura'
        ]);
    }

    try {
        $accessToken = config('services.pagby.access_token');
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

                // Desbloqueia o tenant se status for authorized ou approved
                if ($payment->status === 'authorized' || $payment->status === 'approved') {
                    $tenant = Tenant::on('mysql')->find($payment->tenant_id);
                    if ($tenant && $tenant->is_blocked) {
                        $tenant->is_blocked = false;
                        $tenant->save();
                        Log::info('Tenant desbloqueado via checkStatus:', [
                            'tenant_id' => $tenant->id,
                            'tenant_name' => $tenant->name
                        ]);
                    }
                }

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

public function debugPayment($paymentId)
{
    $payment = PagByPayment::find($paymentId);
    
    if (!$payment) {
        return response()->json(['error' => 'Payment not found'], 404);
    }

    $debugInfo = [
        'payment' => [
            'id' => $payment->id,
            'tenant_id' => $payment->tenant_id,
            'contact_id' => $payment->contact_id,
            'external_id' => $payment->external_id,
            'mp_payment_id' => $payment->mp_payment_id,
            'status' => $payment->status,
            'plan' => $payment->plan,
            'amount' => $payment->amount,
            'created_at' => $payment->created_at,
            'updated_at' => $payment->updated_at
        ],
        'session' => [
            'payment_id' => session('payment_id'),
            'checkout_url' => session('checkout_url'),
            'tenant_name' => session('tenant_name')
        ]
    ];

    Log::info('🔧 Debug Payment Info:', $debugInfo);

    return response()->json($debugInfo);
}

public function wait($paymentId)
{
    $payment = PagByPayment::on('mysql')->find($paymentId); // Corrigir nome da classe
    
    
    if (!$payment) {

        Log::error('Pagamento não encontrado na página wait:', ['payment_id' => $paymentId]);
        abort(404, 'Pagamento não encontrado');
    }
    
    $checkoutUrl = session('checkout_url');
    
    // Buscar dados do contato usando o contact_id do pagamento
    $contact = Contact::find(str_replace('temp_', '', $payment->tenant_id));

   
    if (!$contact) {
        $contact = Contact::on('mysql')->find($payment->contact_id);
        Log::warning('Contato não encontrado para o pagamento:', ['payment_id' => $paymentId, 'contact_id' => $payment->contact_id]);
    }
    
    $plans = [
        'basico' => ['name' => 'Plano Básico', 'price' => 29.90], // Corrigir chaves
        'premium' => ['name' => 'Plano Premium', 'price' => 59.90]
    ];
    
    $plan = $plans[$payment->plan] ?? ['name' => 'Plano ' . $payment->plan, 'price' => 0];
    
    Log::info('Página de aguarde acessada:', [
        'payment_id' => $paymentId,
        'checkout_url' => $checkoutUrl ? 'presente' : 'ausente',
        'contact_name' => $contact ? $contact->tenant_name : 'N/A',
        'plan_name' => $plan['name'],
        'payment_status' => $payment->status
    ]);
    
    return view('pagby-subscription.wait', [
        'payment' => $payment,
        'checkout_url' => $checkoutUrl,
        'payment_id' => $paymentId,
        'tenant_name' => $contact ? $contact->tenant_name : 'Não informado',
        'plan_name' => $plan['name']
    ]);
}

public function waitRenew($paymentId)
{
    $payment = PagByPayment::on('mysql')->find($paymentId); // Corrigir nome da classe
    
    
    
    if (!$payment) {

        Log::error('Pagamento não encontrado na página waitRenew:', ['payment_id' => $paymentId]);
        abort(404, 'Pagamento não encontrado');
    }
    
    $checkoutUrl = session('checkout_url');
 
    
    // Buscar dados do contato usando o contact_id do pagamento
    $contact = Contact::on('mysql')->find($payment->contact_id);
   
    if (!$contact) {
        
        Log::warning('Contato não encontrado para o pagamento:', ['payment_id' => $paymentId, 'contact_id' => $payment->contact_id]);
    }
    
    
    $plans = [
        'basico' => ['name' => 'Plano Básico', 'price' => 29.90], // Corrigir chaves
        'premium' => ['name' => 'Plano Premium', 'price' => 59.90]
    ];
    
    $plan = $plans[$payment->plan] ?? ['name' => 'Plano ' . $payment->plan, 'price' => 0];
    
    Log::info('Página de aguarde acessada:', [
        'payment_id' => $paymentId,
        'checkout_url' => $checkoutUrl ? 'presente' : 'ausente',
        'contact_name' => $contact ? $contact->tenant_name : 'N/A',
        'plan_name' => $plan['name'],
        'payment_status' => $payment->status
    ]);
    
    return view('tenant-assinatura.waitRenew', [
        'payment' => $payment,
        'checkout_url' => $checkoutUrl,
        'payment_id' => $paymentId,
        'tenant_name' => $contact ? $contact->tenant_name : 'Não informado',
        'plan_name' => $plan['name']
    ]);
}
}

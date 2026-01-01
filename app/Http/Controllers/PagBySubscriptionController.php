<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\PagByPayment;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\PagbyService;
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


    
  
    public function showPaymentForm(Request $request)
    {
        // Redireciona diretamente para o checkout seguro do Asaas, pulando o formulário
        $contactId = $request->contact_id ?? session('contact_id');
        $contact = $contactId ? \App\Models\Contact::find($contactId) : null;
        if (!$contact) {
            return redirect()->route('register-tenant')->with('error', 'Contato não encontrado. Faça o registro novamente.');
        }
        $planName = $contact->subscription_plan ?? session('selected_plan', 'Plano PagBy');
        // Novos planos dinâmicos
       
        $employeeCount = $contact->employee_count ?? 1;
        $pagbyService = new PagbyService();
    
        $amount = $pagbyService->calcularValorPlano($employeeCount, $planName);
        
        $customerData = [
            'name' => $contact->tenant_name ?? $contact->owner_name,
            'email' => $contact->email,
            'cpfCnpj' => $contact->cpf,
            'phone' => $contact->phone,
            'postalCode' => $contact->postal_code ?? null,
            'address' => $contact->address ?? null,
            'addressNumber' => $contact->address_number ?? null,
            'complement' => $contact->complement ?? null,
            'province' => $contact->province ?? null, // bairro
        ];
       
     
        $paymentData = [
            'name' => 'Assinatura PagBy - ' . ucfirst($planName),
            'description' => 'Pagamento de assinatura do plano ' . ucfirst($planName),
            'billingType' => 'UNDEFINED',
            'value' => $amount,
            'plan' => $planName, // Para referência futura
            
        ];
        $asaasService = new \App\Services\AsaasService();
        $asaasResult = $asaasService->criarCheckout($customerData, $paymentData);
        if ($asaasResult['success'] && !empty($asaasResult['data']['url'])) {
            // Logar retorno completo do Asaas para debug
            \Log::info('Retorno Asaas criarCheckout', ['asaas_data' => $asaasResult['data']]);
            // Salvar o pagamento localmente
            $payment = PagByPayment::create([
                'tenant_id' => 'temp_' . $contact->id,
                'contact_id' => $contact->id,
                'mp_payment_id' => null,
                'external_id' => $asaasResult['data']['paymentLink'] ?? $asaasResult['data']['id'] ?? null,
                'asaas_payment_id' => null, // Só será preenchido via webhook
                'amount' => $amount,
                'status' => 'pending',
                'employee_count' => $contact->employee_count ?? 1,
                'type' => 'subscription',
                'plan' => $planName,
                'description' => 'Checkout Asaas: ' . ($asaasResult['data']['url'] ?? '')
            ]);
            // Redireciona o usuário para a página de espera do pagamento
            return redirect()->route('pagby-subscription.wait', ['paymentId' => $payment->id]);
        } else {
            return redirect()->back()->with('error', 'Erro ao criar link de pagamento no Asaas.');
        }
    }

    /**
     * Processa o formulário de pagamento enviado pelo cliente.
     */
    public function processPayment(Request $request)
    {
        // Validação básica dos campos
        $validated = $request->validate([
            'payment_method' => 'required|in:credit_card,boleto,pix',
            'customer_name' => 'required|string',
            'customer_cpf' => 'required|string',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string',
            // Campos do cartão só se for cartão de crédito
            'card_number' => 'required_if:payment_method,credit_card',
            'holder_name' => 'required_if:payment_method,credit_card',
            'expiration' => 'required_if:payment_method,credit_card',
            'cvv' => 'required_if:payment_method,credit_card',
        ]);

        // Criar registro de pagamento fictício (ajuste conforme sua lógica real)
        // Buscar o contato pelo ID salvo na sessão
        $contactId = session('contact_id');
        $contact = $contactId ? \App\Models\Contact::find($contactId) : null;
        if (!$contact) {
            return redirect()->route('register-tenant')->with('error', 'Contato não encontrado. Faça o registro novamente.');
        }

        $planName = $contact->subscription_plan ?? session('selected_plan', 'Plano PagBy');
         // Novos planos dinâmicos
       
        $employeeCount = $contact->employee_count ?? 1;
        $pagbyService = new PagbyService();
    
        $amount = $pagbyService->calcularValorPlano($employeeCount, $planName);
        
        // Montar dados do cliente para o Asaas
        $customerData = [
            'name' => $contact->tenant_name ?? $contact->owner_name,
            'email' => $contact->email,
            'cpfCnpj' => $contact->cpf,
            'phone' => $contact->phone,
        ];

        // Montar dados do pagamento para o Asaas
        $paymentData = [
            'billingType' => $request->payment_method === 'credit_card' ? 'CREDIT_CARD' : strtoupper($request->payment_method),
            'value' => $amount,
            'description' => 'Assinatura PagBy',
            'dueDate' => now()->addDay()->format('Y-m-d'),
            'externalReference' => 'pagby-' . uniqid(),
        ];

        // Adicionar dados do cartão se for cartão de crédito
        if ($request->payment_method === 'credit_card') {
            $paymentData['creditCard'] = [
                'holderName' => $request->holder_name,
                'number' => $request->card_number,
                'expiryMonth' => substr(preg_replace('/\D/', '', $request->expiration), 0, 2),
                'expiryYear' => '20' . substr(preg_replace('/\D/', '', $request->expiration), 2, 2),
                'ccv' => $request->cvv,
            ];
        }

        // Chamar o serviço do Asaas para criar o link de checkout
        $asaasService = new \App\Services\AsaasService();
        $asaasResult = $asaasService->criarCheckout($customerData, $paymentData);

        if ($asaasResult['success'] && !empty($asaasResult['data']['url'])) {
            // Salvar o pagamento localmente (opcional, pode salvar o link ou id do checkout)
                $payment = PagByPayment::create([
                    'tenant_id' => 'temp_' . uniqid(),
                    'contact_id' => $contact->id,
                    'mp_payment_id' => null,
                    'external_id' => $asaasResult['data']['paymentLink'] ?? $asaasResult['data']['id'] ?? null,
                    'asaas_payment_id' => null, // Só será preenchido via webhook
                    'amount' => $amount,
                    'status' => 'pending',
                    'employee_count' => $contact->employee_count ?? 1,
                    'type' => 'subscription',
                    'plan' => $planName,
                    'description' => 'Checkout Asaas: ' . ($asaasResult['data']['url'] ?? '')
                ]);
            // Redireciona o usuário para o link seguro do Asaas
            return redirect()->away($asaasResult['data']['url']);
        } else {
            // Se falhar, redireciona para a página de erro ou exibe mensagem
            return redirect()->back()->with('error', 'Erro ao criar link de pagamento no Asaas.');
        }
    }

    /**
     * Inicia o pagamento via API Asaas.
     */
    public function asaasPay(Request $request, $paymentId)
    {
        $payment = PagByPayment::on('mysql')->find($paymentId);
        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Pagamento não encontrado.'], 404);
        }

        $contact = Contact::find($payment->contact_id);
        if (!$contact) {
            return response()->json(['success' => false, 'message' => 'Contato não encontrado.'], 404);
        }

        // Montar dados do cliente para o Asaas
        $customerData = [
            'name' => $contact->owner_name ?? $contact->tenant_name,
            'email' => $contact->email,
            'cpfCnpj' => $contact->cpf,
            'phone' => $contact->phone,
        ];

        // Montar dados do pagamento para o Asaas
        $paymentData = [
            'billingType' => 'CREDIT_CARD', // ou BOLETO, PIX, etc.
            'value' => $payment->amount,
            'description' => 'Assinatura PagBy',
            'dueDate' => now()->addDay()->format('Y-m-d'),
            'externalReference' => 'pagby-' . $payment->id,
        ];

        $asaasService = new \App\Services\AsaasService();
        $result = $asaasService->criarCheckout($customerData, $paymentData);

        if ($result['success']) {
            // Salvar ID/link do checkout Asaas no pagamento
            $payment->asaas_payment_id = $result['data']['id'] ?? null;
            $payment->description = 'Checkout Asaas: ' . ($result['data']['url'] ?? '');
            $payment->save();
            return response()->json(['success' => true, 'message' => 'Link de pagamento criado no Asaas!', 'asaas' => $result['data']]);
        }
        return response()->json(['success' => false, 'message' => $result['message'] ?? 'Erro ao criar link de pagamento no Asaas.']);
    }

    /**
     * Exibe a tela de aguarde do pagamento de assinatura.
     */
    public function wait($paymentId)
    {
        $payment = PagByPayment::on('mysql')->find($paymentId);
        if (!$payment) {
            Log::error('Pagamento não encontrado na página wait:', ['payment_id' => $paymentId]);
            abort(404, 'Pagamento não encontrado');
        }

        $checkoutUrl = session('checkout_url');
        // Busca o nome do salão a partir do contato
        $contact = null;
        if ($payment->contact_id) {
            $contact = \App\Models\Contact::find($payment->contact_id);
        }
        $tenantName = $contact ? ($contact->tenant_name ?? $contact->owner_name ?? 'Não informado') : 'Não informado';
        return view('pagby-subscription.wait', [
            'payment' => $payment,
            'checkout_url' => $checkoutUrl,
            'payment_id' => $paymentId,
            'tenant_name' => $tenantName,
            'plan_name' => $payment->plan ?? 'Não informado'
        ]);
    }
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
            'mensal' => [
                'name' => 'Mensal',
                'description' => 'Periodo de 1 mês',
                'price' => config('pricing.base_price_per_employee', 60.00)
            ],
            'trimestral' => [
                'name' => 'Trimestral',
                'description' => 'Periodo de 3 meses',
                'price' => 3*config('pricing.base_price_per_employee', 60.00)*0.8
            ],
            'semestral' => [
                'name' => 'Semestral',
                'description' => 'Periodo de 6 meses',
                'price' => 6*config('pricing.base_price_per_employee', 60.00)*0.7
            ],
            'anual' => [
                'name' => 'Anual',
                'description' => 'Periodo de 12 meses',
                'price' => 12*config('pricing.base_price_per_employee', 60.00)*0.6
            ],
        ];

        if (!isset($plans[$plan])) {
            return redirect()->route('home')->with('error', 'Plano não encontrado.');
        }

        $selectedPlan = $plans[$plan];
        $planData = $plans[$plan];
        session(['selected_plan' => $plan]);

        // Exibe a página de escolha do plano normalmente
        return view('pagby-subscription.choose-plan', compact('selectedPlan', 'plan', 'planData'));
    }
 
    public function selectPlan(Request $request)
    {
        $plans = [
            'mensal' => [
                'name' => 'Mensal',
                'description' => 'Periodo de 1 mês',
                'price' => config('pricing.base_price_per_employee', 60.00)
            ],
            'trimestral' => [
                'name' => 'Trimestral',
                'description' => 'Periodo de 3 meses',
                'price' => 3*config('pricing.base_price_per_employee', 60.00)*0.8
            ],
            'semestral' => [
                'name' => 'Semestral',
                'description' => 'Periodo de 6 meses',
                'price' => 6*config('pricing.base_price_per_employee', 60.00)*0.7
            ],
            'anual' => [
                'name' => 'Anual',
                'description' => 'Periodo de 12 meses',
                'price' => 12*config('pricing.base_price_per_employee', 60.00)*0.6
            ]
        ];
  
        // Novo modelo: apenas um plano, preço único
        $employeeCount = $request->input('employee_count') ?? $request->input('employee_count_hidden');
        $price = config('pricing.promo_price_first_year', 40.00); // valor promocional
        $planName = 'Plano Pagby';
        $planDescription = 'Assinatura única por funcionário';
        $planData = [
            'name' => $planName,
            'description' => $planDescription,
            'price' => $price,
            'employee_count' => $employeeCount
        ];
        session(['selected_plan' => $planName]);
        return view('pagby-subscription.select-plan', compact('planData'));
    }
 




    public function createSubscription(Request $request)
    {
        $request->validate([
            'plan' => 'required|string|in:mensal,trimestral,semestral,anual',
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

        // Novos planos dinâmicos
        $plan = $request->plan;
        $employeeCount = $contact->employee_count ?? 1;
        $pagbyService = new PagbyService();
        $amount = $pagbyService->calcularValorPlano($employeeCount, $plan);

        $payment = PagByPayment::create([
            'tenant_id' => 'temp_' . $contact->id,
            'contact_id' => $contact->id,
            'mp_payment_id' => null,
            'external_id' => null,
            'amount' => $amount,
            'status' => 'pending',
            'employee_count' => $employeeCount,
            'type' => 'subscription'
        ]);
        Log::info('📄 PagByPayment criado:', [
            'payment_id' => $payment->id,
            'amount' => $payment->amount,
            'employee_count' => $payment->employee_count
        ]);

        // Redireciona para a página de espera (wait) com o paymentId
        return redirect()->route('pagby-subscription.wait', ['paymentId' => $payment->id]);
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
            'plan' => 'required|in:mensal,trimestral,semestral,anual',
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
            'mensal' => [
                'name' => 'Mensal',
                'description' => 'Periodo de 1 mês',
                'price' => config('pricing.base_price_per_employee', 60.00)
            ],
            'trimestral' => [
                'name' => 'Trimestral',
                'description' => 'Periodo de 3 meses',
                'price' => 3*config('pricing.base_price_per_employee', 60.00)*0.8
            ],
            'semestral' => [
                'name' => 'Semestral',
                'description' => 'Periodo de 6 meses',
                'price' => 6*config('pricing.base_price_per_employee', 60.00)*0.7
            ],
            'anual' => [
                'name' => 'Anual',
                'description' => 'Periodo de 12 meses',
                'price' => 12*config('pricing.base_price_per_employee', 60.00)*0.6
            ]
        ];

        $plan = $plans[$request->plan];
        

        // Buscar créditos pendentes do tenant
        $pendingCredits = \App\Models\PlanAdjustment::getPendingCredits($tenantId);
        
        Log::info('Créditos pendentes encontrados:', [
            'tenant_id' => $tenantId,
            'pending_credits' => $pendingCredits,
        ]);

        // Cria registro local da assinatura
        $pagbyService = new PagbyService();    
        $baseAmount = $pagbyService->calcularValorPlano($request->numFuncionarios, $request->plan);
        
        // Aplicar créditos pendentes ao valor
        $finalAmount = max(0, $baseAmount - $pendingCredits); // Garante que não seja negativo
        
        Log::info('Cálculo de valor com créditos:', [
            'base_amount' => $baseAmount,
            'pending_credits' => $pendingCredits,
            'final_amount' => $finalAmount,
        ]);

        $payment = PagByPayment::on('mysql')->create([        
        'tenant_id' => $tenantId,
        'contact_id' => $contact->id, // ADICIONAR este campo
        'mp_payment_id' => null,
        'external_id' => null, // ADICIONAR explicitamente
        'amount' => $finalAmount, // Usar valor com créditos aplicados
        'status' => 'pending',
        'plan' => $request->plan,
        'employee_count' => $request->numFuncionarios,
        'type' => 'subscription'
    ]);
    Log::info('📄 PagByPayment criado:', [
        'payment_id' => $payment->id,
        'base_amount' => $baseAmount,
        'credits_applied' => $pendingCredits,
        'final_amount' => $finalAmount,
        'plan' => $payment->plan
    ]);
    
    // Se houver créditos aplicados, marcar como utilizados
    if ($pendingCredits > 0) {
        \App\Models\PlanAdjustment::applyPendingCredits($tenantId);
        Log::info('Créditos aplicados e marcados como utilizados:', [
            'tenant_id' => $tenantId,
            'credits_applied' => $pendingCredits,
        ]);
    }
    
    // Cria checkout de pagamento via Asaas
    $asaasService = new \App\Services\AsaasService();
    
    $customerData = [
        'name' => $contact->name,
        'email' => $contact->email,
        'cpfCnpj' => $contact->cpf_cnpj ?? '',
        'phone' => $contact->phone ?? ''
    ];
    
    $paymentData = [
        'name' => 'Renovação ' . $plan['name'] . ' - ' . $tenant_name,
        'description' => $plan['description'] . ' - ' . $request->numFuncionarios . ' funcionário(s)' . 
                        ($pendingCredits > 0 ? ' (Crédito aplicado: R$ ' . number_format($pendingCredits, 2, ',', '.') . ')' : ''),
        'billingType' => 'UNDEFINED',
        'value' => $finalAmount
    ];
    
    $asaasResult = $asaasService->criarCheckout($customerData, $paymentData);
    
    if ($asaasResult['success'] && !empty($asaasResult['data']['url'])) {
        // Salva o ID do checkout do Asaas
        $payment->external_id = $asaasResult['data']['id'];
        $payment->save();
        
        Log::info('✅ Checkout Asaas criado para renovação:', [
            'payment_id' => $payment->id,
            'checkout_id' => $asaasResult['data']['id'],
            'checkout_url' => $asaasResult['data']['url']
        ]);
        
        // Salva dados na sessão para a página de espera
        session([
            'payment_id' => $payment->id,
            'checkout_url' => $asaasResult['data']['url'],
            'tenant_name' => $contact->tenant_name,
            'plan_name' => $plan['name'],
            'employee_count' => $request->numFuncionarios,
            'plan_amount' => floatval($payment->amount)
        ]);

        // Redireciona para a página de aguarde
        return redirect()->route('tenant-assinatura.waitRenew', ['paymentId' => $payment->id]);
    } else {
        Log::error('❌ Erro ao criar checkout Asaas para renovação:', [
            'payment_id' => $payment->id,
            'error' => $asaasResult['message'] ?? 'Erro desconhecido'
        ]);
        
        // Redireciona para a página de bloqueio com mensagem de erro
        return redirect()->route('tenant.subscription.blocked')->with('error', 'Erro ao criar link de pagamento. Tente novamente.');
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
    Log::info('=== Webhook RECEBIDO ===');
    Log::info('Webhook Headers:', $request->headers->all());
    Log::info('Webhook Body:', $request->all());

    // Webhook Asaas
    if ($request->has('event') && $request->has('payment')) {
        $asaasPaymentId = $request->input('payment.id');
        $asaasStatus = $request->input('payment.status');
        $event = $request->input('event');
        
        Log::info('Webhook Asaas:', [
            'asaas_payment_id' => $asaasPaymentId, 
            'asaas_status' => $asaasStatus,
            'event' => $event
        ]);
        
        if ($asaasPaymentId) {
            // 1. Verificar se é um ajuste de plano (PlanAdjustment)
            $adjustment = \App\Models\PlanAdjustment::where('asaas_payment_id', $asaasPaymentId)->first();
            
            if ($adjustment && in_array($asaasStatus, ['RECEIVED', 'CONFIRMED', 'RECEIVED_IN_CASH'])) {
                $adjustment->markAsPaid();
                Log::info('Ajuste de plano marcado como pago', [
                    'adjustment_id' => $adjustment->id,
                    'tenant_id' => $adjustment->tenant_id,
                    'amount' => $adjustment->amount,
                    'asaas_status' => $asaasStatus,
                ]);
                return response('OK', 200);
            }
            
            // 2. Processar pagamento normal (PagByPayment)
            // Buscar por asaas_payment_id
            $payment = PagByPayment::where('asaas_payment_id', $asaasPaymentId)->first();
            // Se não encontrar, buscar por external_id = paymentLink
            if (!$payment && $request->input('payment.paymentLink')) {
                $payment = PagByPayment::where('external_id', $request->input('payment.paymentLink'))->first();
                // Se encontrar, atualizar o asaas_payment_id
                if ($payment) {
                    $payment->asaas_payment_id = $asaasPaymentId;
                }
            }
            if ($payment) {
                $oldStatus = $payment->status;
                $payment->status = $asaasStatus;
                $payment->save();
                Log::info('Pagamento atualizado via webhook Asaas', [
                    'payment_id' => $payment->id,
                    'old_status' => $oldStatus,
                    'new_status' => $asaasStatus
                ]);
                // Enviar e-mail apenas se mudou de não-aprovado para aprovado
                if (in_array($asaasStatus, ['RECEIVED', 'PAID', 'CONFIRMED']) && !in_array(strtoupper($oldStatus), ['RECEIVED', 'PAID', 'CONFIRMED'])) {
                    $contact = \App\Models\Contact::find($payment->contact_id);
                    if ($contact && $contact->email) {
                        try {
                            // Se é o primeiro pagamento aprovado, enviar boas-vindas
                            if ($payment->created_at->eq($payment->updated_at) || in_array(strtolower($oldStatus), ['pending', 'aguardando', ''])) {
                                \Mail::to($contact->email)->send(new \App\Mail\WelcomeSubscriptionMail($contact, $payment->plan));
                                Log::info('E-mail de boas-vindas enviado para ' . $contact->email);
                            } else {
                                // Renovação
                                \Mail::to($contact->email)->send(new \App\Mail\SubscriptionRenewedMail($contact, $payment->plan));
                                Log::info('E-mail de renovação enviado para ' . $contact->email);
                            }
                        } catch (\Exception $e) {
                            Log::error('Erro ao enviar e-mail de assinatura: ' . $e->getMessage());
                        }
                    }
                    
                    // Processar renovação ou novo tenant
                    if (str_starts_with($payment->tenant_id, 'temp_')) {
                        // Novo tenant
                        Log::info('💰 Pagamento APROVADO! Tenant NÃO será criado automaticamente. Aguardando onboarding manual.', [
                            'payment_id' => $payment->id,
                            'contact_id' => $payment->contact_id,
                            'tenant_id_atual' => $payment->tenant_id
                        ]);
                        Log::notice('⚠️ Atenção: Realize o onboarding manual do tenant após aprovação do pagamento.', [
                            'payment_id' => $payment->id,
                            'contact_id' => $payment->contact_id,
                            'tenant_id_atual' => $payment->tenant_id
                        ]);
                        // $this->criarTenantAposAprovacao($payment); // Automação desativada para onboarding manual
                    } else {
                        // Renovação de tenant existente
                        $this->renovarAssinaturaTenant($payment);
                    }
                }
            } else {
                Log::warning('Pagamento não encontrado para asaas_payment_id nem paymentLink', [
                    'asaas_payment_id' => $asaasPaymentId,
                    'paymentLink' => $request->input('payment.paymentLink')
                ]);
            }
        }
        return response('OK', 200);
    }

    // Webhook MercadoPago (legado)
    $type = $request->input('type');
    $dataId = $request->input('data.id');

    if ($type === 'preapproval' && $dataId) {
        // ...existing code for MercadoPago webhook...
    }

    return response('OK', 200);
    }
    



    /**
     * Renova a assinatura de um tenant existente após aprovação do pagamento
     */
    private function renovarAssinaturaTenant(PagByPayment $payment)
    {
        try {
            // Buscar o tenant
            $tenant = \App\Models\Tenant::on('mysql')->find($payment->tenant_id);
            if (!$tenant) {
                Log::error('❌ Tenant não encontrado para renovação', [
                    'payment_id' => $payment->id,
                    'tenant_id' => $payment->tenant_id
                ]);
                return;
            }
            
            // Calcular duração baseada no plano
            $durationDays = match($payment->plan) {
                'mensal' => 30,
                'trimestral' => 90,
                'semestral' => 180,
                'anual' => 365,
                default => 30
            };
            
            // Atualizar dados da assinatura
            $tenant->subscription_status = 'active';
            $tenant->is_blocked = false;
            $tenant->employee_count = $payment->employee_count ?? $tenant->employee_count;
            $tenant->current_plan = $payment->plan;
            
            // Se a assinatura ainda está válida, extender a partir da data de fim
            // Se já expirou, começar a partir de agora
            if ($tenant->subscription_ends_at && $tenant->subscription_ends_at->isFuture()) {
                $tenant->subscription_ends_at = $tenant->subscription_ends_at->addDays($durationDays);
            } else {
                $tenant->subscription_started_at = now();
                $tenant->subscription_ends_at = now()->addDays($durationDays);
            }
            
            $tenant->save();
            
            Log::info('✅ Assinatura renovada com sucesso!', [
                'tenant_id' => $tenant->id,
                'payment_id' => $payment->id,
                'plan' => $payment->plan,
                'duration_days' => $durationDays,
                'subscription_ends_at' => $tenant->subscription_ends_at->format('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Erro ao renovar assinatura do tenant:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payment_id' => $payment->id,
                'tenant_id' => $payment->tenant_id
            ]);
        }
    }

    /**
     * Cria o tenant automaticamente após aprovação do pagamento
     */
    private function criarTenantAposAprovacao(PagByPayment $payment)
    {
        // Buscar o contato associado
        $contact = Contact::find($payment->contact_id);
        if (!$contact) {
            Log::error('❌ Contato não encontrado para criar tenant', [
                'payment_id' => $payment->id,
                'contact_id' => $payment->contact_id
            ]);
            return;
        }
        
        // Verificar se o tenant já foi criado
        if (!str_starts_with($payment->tenant_id, 'temp_')) {
            Log::info('✅ Tenant já foi criado anteriormente', [
                'tenant_id' => $payment->tenant_id
            ]);
            return;
        }
        
        Log::info('🏗️ Criando tenant para:', [
            'tenant_name' => $contact->tenant_name,
            'owner_name' => $contact->owner_name,
            'email' => $contact->email
        ]);
        
        try {
            // Criar slug único para o tenant
            $baseSlug = \Illuminate\Support\Str::slug($contact->tenant_name);
            $slug = $baseSlug;
            $counter = 1;
            
            while (\App\Models\Tenant::where('id', $slug)->exists()) {
                $slug = $baseSlug . $counter;
                $counter++;
            }
            
            // Criar o tenant
            $tenant = \App\Models\Tenant::create([
                'id' => $slug,
                'name' => $contact->tenant_name,
                'email' => $contact->email,
                'phone' => $contact->phone,
                'fantasy_name' => $contact->tenant_name,
                'cnpj' => $contact->cpf, // Usando CPF como CNPJ temporário
                'type' => $this->mapContactTypeToTenantType($contact->tipo ?? 'Barbearia'),
                'subscription_status' => 'active', // Ativo após pagamento
                'subscription_plan' => $payment->plan ?? 'basico',
                'trial_ends_at' => null, // Sem trial, já pagou
                'subscription_start' => now(),
                'subscription_end' => now()->addMonth(), // 1 mês de assinatura
                'employee_count' => $payment->employee_count ?? 1,
                'is_blocked' => false,
            ]);
            
            // Criar domínio para o tenant
            $domain = $slug . '.' . config('app.domain', 'localhost');
            $tenant->domains()->create([
                'domain' => $domain
            ]);
            
            // Atualizar o pagamento com o tenant_id real
            $payment->tenant_id = $tenant->id;
            $payment->save();
            
            Log::info('✅ Tenant criado com sucesso!', [
                'tenant_id' => $tenant->id,
                'domain' => $domain,
                'payment_id' => $payment->id
            ]);
            
            // Enviar email de boas-vindas (opcional)
            // Mail::to($contact->email)->send(new TenantCreated($tenant, $contact));
            
        } catch (\Exception $e) {
            Log::error('❌ Erro ao criar tenant:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payment_id' => $payment->id
            ]);
            throw $e;
        }
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
        $payment = null;
        $planName = null;
        $clientName = null;
        if ($paymentId) {
            $payment = \App\Models\PagByPayment::find($paymentId);
            $planName = $payment ? $payment->plan : null;
            if ($payment && $payment->contact_id) {
                $contact = \App\Models\Contact::find($payment->contact_id);
                $clientName = $contact ? ($contact->tenant_name ?? $contact->owner_name ?? $contact->name ?? 'Cliente') : null;
            }
        }
        return view('pagby-subscription.success', [
            'payment_id' => $paymentId ?? session('payment_id'),
            'tenant_name' => $clientName ?? session('tenant_name'),
            'plan_name' => $planName
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
        $payment = PagByPayment::on('mysql')->find($paymentId);
        if (!$payment) {
            Log::error('Pagamento não encontrado para checkStatus:', ['payment_id' => $paymentId]);
            return response()->json(['error' => 'Payment not found'], 404);
        }

        // Se for cobrança Asaas
        if ($payment->asaas_payment_id) {
            $asaasService = new \App\Services\AsaasService();
            $asaas = $asaasService->consultarCobranca($payment->asaas_payment_id);
            if ($asaas) {
                $oldStatus = $payment->status;
                $payment->status = $asaas['status'] ?? $payment->status;
                $payment->save();
                
                // Se o status mudou para aprovado, processar renovação
                if (in_array($payment->status, ['RECEIVED', 'PAID', 'CONFIRMED']) && 
                    !in_array(strtoupper($oldStatus), ['RECEIVED', 'PAID', 'CONFIRMED'])) {
                    
                    if (!str_starts_with($payment->tenant_id, 'temp_')) {
                        // É uma renovação de tenant existente
                        $this->renovarAssinaturaTenant($payment);
                        Log::info('✅ Renovação processada via checkStatus', [
                            'payment_id' => $payment->id,
                            'tenant_id' => $payment->tenant_id
                        ]);
                    }
                }
                
                return response()->json([
                    'status' => $payment->status,
                    'asaas_payment_id' => $payment->asaas_payment_id,
                    'payment_id' => $payment->id,
                    'updated_at' => $payment->updated_at->toISOString(),
                    'asaas_status' => $asaas['status'] ?? null,
                    'asaas' => $asaas
                ]);
            } else {
                return response()->json([
                    'status' => $payment->status,
                    'asaas_payment_id' => $payment->asaas_payment_id,
                    'payment_id' => $payment->id,
                    'updated_at' => $payment->updated_at->toISOString(),
                    'asaas_status' => null,
                    'asaas' => null
                ]);
            }
        }

        // Fallback: MercadoPago (antigo)
        Log::info('🔍 Verificando status da assinatura:', [
            'payment_id' => $paymentId,
            'current_status' => $payment->status,
            'external_id' => $payment->external_id,
            'mp_payment_id' => $payment->mp_payment_id
        ]);
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
                Log::info('📄 PagByPayment criado:', [
                    'payment_id' => $payment->id,
                    'amount' => $payment->amount,
                    'plan' => $payment->plan
                ]);

                // Redireciona para a página de espera (wait) com o paymentId
                return redirect()->route('pagby-subscription.wait', ['paymentId' => $payment->id]);
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
        'mensal' => ['name' => 'Mensal', 'price' => config('pricing.base_price_per_employee')], // Corrigir chaves
        'trimestral' => ['name' => 'Trimestral', 'price' => 3 * config('pricing.base_price_per_employee') * 0.8],
        'semestral' => ['name' => 'Semestral', 'price' => 6 * config('pricing.base_price_per_employee') * 0.7],
        'anual' => ['name' => 'Anual', 'price' => 12 * config('pricing.base_price_per_employee') * 0.6],
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
        'plan_name' => $plan['name'],
        'employee_count' => $payment->employee_count,
    ]);
}

    /**
     * Endpoint para simular webhook do Asaas em ambiente de desenvolvimento
     * USO: GET /pagby-subscription/simulate-webhook/{payment_id}
     */
    public function simulateAsaasWebhook($paymentId)
    {
        if (!app()->environment(['local', 'development'])) {
            abort(403, 'Disponível apenas em ambiente de desenvolvimento');
        }
        
        $payment = PagByPayment::on('mysql')->find($paymentId);
        if (!$payment) {
            return response()->json(['error' => 'Pagamento não encontrado'], 404);
        }
        
        // Simular payload do webhook do Asaas
        $webhookPayload = [
            'event' => 'PAYMENT_RECEIVED',
            'payment' => [
                'id' => $payment->external_id ?? 'pay_' . uniqid(),
                'status' => 'RECEIVED', // Status de pagamento aprovado
                'customer' => $payment->contact_id,
                'value' => $payment->amount,
                'netValue' => $payment->amount * 0.95,
                'billingType' => 'CREDIT_CARD',
                'confirmedDate' => now()->toISOString(),
            ]
        ];
        
        Log::info('🧪 Simulando webhook Asaas para teste', [
            'payment_id' => $paymentId,
            'payload' => $webhookPayload
        ]);
        
        // Chamar o webhook internamente
        $request = Request::create(
            route('pagby-subscription.webhook'),
            'POST',
            $webhookPayload
        );
        
        $response = $this->webhook($request);
        
        return response()->json([
            'success' => true,
            'message' => 'Webhook simulado com sucesso',
            'payment_id' => $paymentId,
            'new_status' => $payment->fresh()->status,
            'tenant_created' => !str_starts_with($payment->fresh()->tenant_id, 'temp_'),
            'tenant_id' => $payment->fresh()->tenant_id,
            'payload' => $webhookPayload,
            'webhook_response' => $response->getStatusCode()
        ]);
    }
    
    /**
     * Mapeia o tipo do Contact para o tipo do Tenant
     */
    private function mapContactTypeToTenantType(?string $contactType): string
    {
        $map = [
            'Barbearia' => 'barbearia',
            'Salão de Beleza' => 'salao_beleza',
            'Outro' => 'barbearia', // Default
        ];
        
        return $map[$contactType] ?? 'barbearia';
    }
}

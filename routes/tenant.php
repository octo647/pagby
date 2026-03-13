<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\DashboardController;
use App\Models\Service;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\PaymentController;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\TenantPlan;
use App\Models\TenantsPlansPayment;
use App\Http\Controllers\PagBySubscriptionController;
use App\Models\PagByPayment;


Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    // Manifest.json dinâmico por tenant
    Route::get('/manifest.json', \App\Http\Controllers\ManifestController::class)->name('tenant.manifest');
    // ROTAS PÚBLICAS (SEM MIDDLEWARE)
    Route::get('/auth/google', [\App\Http\Controllers\Auth\SocialController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\SocialController::class, 'handleGoogleCallback'])->name('login.google.callback');
    // Nova rota para callback social centralizado
    Route::get('/auth/social-callback', [\App\Http\Controllers\Auth\SocialController::class, 'handleCentralSocialCallback'])->name('login.social.central.callback');
    Route::get('forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create']);
    Route::post('forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store']);
    Route::get('reset-password/{token}', [\App\Http\Controllers\Auth\NewPasswordController::class, 'create']);
    Route::post('reset-password', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store']);
    Route::get('register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);
    
    // Rota pública de agendamento (sem auth)
    Route::get('/agendar', function () {
        return view('cliente.agendamento');
    })->name('agendar');
    
    // Subscription routes (sempre disponíveis)
    
    Route::get('/subscription/plans', [\App\Http\Controllers\TenantSubscriptionController::class, 'showPlans'])->name('tenant.subscription.plans');
    Route::post('/subscription/select', [\App\Http\Controllers\TenantSubscriptionController::class, 'selectPlan'])->name('tenant.subscription.select');
    Route::get('/subscription/success', [\App\Http\Controllers\TenantSubscriptionController::class, 'success'])->name('tenant.subscription.success');
    Route::get('/subscription/blocked', [\App\Http\Controllers\TenantSubscriptionController::class, 'blocked'])->name('tenant.subscription.blocked');
    Route::post('/trial/start', [\App\Http\Controllers\TenantSubscriptionController::class, 'startTrial'])->name('tenant.trial.start'); 

    // ROTAS PROTEGIDAS (com bloqueio de assinatura)
    Route::middleware(['checkTenantSubscription'])->group(function () {
        // Todas as rotas protegidas do tenant devem ser declaradas aqui!
        Route::get('/', function () {
            $tenant = tenant();
            $services = Service::all();
            if (!$tenant) {
                abort(404, 'Tenant not found');
            }
            return view('tenants/' . ($tenant ? $tenant->id : 'default') . '/home', ['tenant' => $tenant, 'services' => $services]);
        })->name('tenant.home');
        Route::get('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
        // ... todas as outras rotas protegidas já existentes ...
    });
    
    //Escolher o plano quando vem do link de bloqueio
    Route::post('/subscription/select', [PagBySubscriptionController::class, 'selectPlan'])->name('pagby-subscription.select-plan');
    //Rota de renovação de assinatura
    Route::post('/tenant/renew-subscription', [PagBySubscriptionController::class, 'renewSubscription'])->name('tenant.renew');
    //rota página wait de renovação de assinatura

   
     Route::get('/tenant-assinatura/waitRenew/{paymentId}', [PagBySubscriptionController::class, 'waitRenew'])->name('tenant-assinatura.waitRenew');

    Route::get('/pagby-subscription/check-status/{paymentId}', [PagBySubscriptionController::class, 'checkStatus']);

    // Rota para cancelar assinatura de plano do tenant (mantém usuário no tenant)
    Route::post('/tenant-assinatura/cancelar', function (Request $request) {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Você precisa estar logado para cancelar a assinatura.');
        }

        $paymentId = $request->input('payment_id');
        
        try {
            // Buscar o pagamento na base do TENANT (não na central)
            $subscriptionPayment = \App\Models\SubscriptionPayment::find($paymentId);

            if (!$subscriptionPayment) {
                return redirect()->back()->with('error', 'Pagamento não encontrado.');
            }

            // Obter a assinatura relacionada
            $subscription = $subscriptionPayment->subscription;
            
            if (!$subscription || !$subscription->mp_payment_id) {
                return redirect()->back()->with('error', 'Assinatura não encontrada no sistema.');
            }

            $asaasSubscriptionId = $subscription->mp_payment_id;

            // Obter o accountId da subconta do tenant
            $tenant = tenancy()->tenant;
            $accountId = $tenant ? $tenant->asaas_account_id : null;

            Log::info('🔍 Dados para cancelamento:', [
                'subscription_id' => $subscription->id,
                'asaas_subscription_id' => $asaasSubscriptionId,
                'tenant_id' => $tenant?->id,
                'account_id' => $accountId
            ]);

            // Cancelar no Asaas (na subconta do tenant)
            $asaasService = app(\App\Services\AsaasService::class);
            $result = $asaasService->cancelarAssinatura($asaasSubscriptionId, $accountId);

            Log::info('Resultado do cancelamento Asaas:', [
                'payment_id' => $paymentId,
                'subscription_id' => $subscription->id,
                'asaas_subscription_id' => $asaasSubscriptionId,
                'result' => $result
            ]);

            // Se retornou 404, a assinatura não existe mais no Asaas (já foi cancelada ou nunca existiu)
            // Nesse caso, cancelamos localmente mesmo assim
            $asaasCancelado = $result['success'] || (isset($result['status']) && $result['status'] == 404);

            if ($asaasCancelado) {
                // Atualizar status da assinatura
                $subscription->status = 'Cancelado';
                $subscription->save();
                
                // Atualizar status do pagamento  
                $subscriptionPayment->status = 'cancelled';
                $subscriptionPayment->save();
                
                $message = $result['success'] 
                    ? 'Assinatura cancelada com sucesso!' 
                    : 'Assinatura cancelada localmente (não encontrada no Asaas).';
                
                return redirect()->route('tenant.dashboard')
                    ->with('success', $message)
                    ->with('tabelaAtiva', 'planos-de-assinatura');
            }

            // Outros erros (não 404)
            Log::warning('Falha ao cancelar no Asaas:', [
                'payment_id' => $paymentId,
                'subscription_id' => $subscription->id,
                'asaas_subscription_id' => $asaasSubscriptionId,
                'result' => $result,
                'message' => $result['message'] ?? 'Sem mensagem'
            ]);
            
            return redirect()->back()->with('error', 'Erro ao cancelar no Asaas: ' . ($result['message'] ?? 'Erro desconhecido'));
        } catch (\Exception $e) {
            Log::error('Erro ao cancelar assinatura do tenant:', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Erro ao processar cancelamento. Contate o suporte.');
        }
    })->middleware('auth')->name('tenant-assinatura.cancelar');
    
    
Route::get('/plans', function () {
            $plans = \App\Models\Plan::all();
            return view('plans/index', ['plans' => $plans]);
        })->middleware(['auth', 'verified'])->name('plans.index');

        Route::middleware(['auth', 'verified'])->group(function () {
            Route::resource('plans', \App\Http\Controllers\PlanController::class)->except(['show', 'index']);
        });

        Route::post('/subscriptions/store', [SubscriptionController::class, 'store'])->name('subscriptions.store');

        Route::get('/dashboard', [DashboardController::class, 'dashboard'])
            ->middleware(['auth', 'verified'])
            ->name('tenant.dashboard');

        Route::get('/agendamento', function () {
            return view('cliente.agendamento');
        })->middleware(['auth', 'verified'])->name('agendamento');

        Route::middleware('auth')->group(function () {
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        });

     // PAGAMENTOS DE AGENDAMENTOS (dentro dos tenants)
        Route::middleware(['auth'])->group(function () {
            Route::get('/payment', [PaymentController::class, 'show'])->name('payment.show');
            Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');
            Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
            Route::get('/payment/failure', [PaymentController::class, 'failure'])->name('payment.failure');
            Route::get('/payment/pending', [PaymentController::class, 'pending'])->name('payment.pending');
            Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');
        });

    //  ROTA DE FALHA NO PAGAMENTO
    Route::get('/tenant-assinatura/failure', function (Illuminate\Http\Request $request) {
        
        $message = $request->query('message', 'Não foi possível processar sua assinatura.');
        $planId = $request->query('plan_id');
        $tenantId = $request->query('tenant_id');
        $name = $request->query('name');
                
        // Se quiser, busque o plano do tenant aqui para exibir informações
        return view('tenant-assinatura.failure', [
            'message' => $message,
            'plan_id' => $planId,
            'tenant_id' => $tenantId,
            'name' => $name ?? null,
            
        ]);
    })->name('tenant-assinatura.failure');

    // Rota de checagem de status do pagamento
    Route::get('/tenant-assinatura/check-status/{paymentId}', [SubscriptionController::class, 'checkStatus'])
        ->name('tenant-assinatura.check-status');

    // Rota para buscar invoice URL dinamicamente
    Route::get('/tenant-assinatura/get-invoice/{paymentId}', function ($paymentId) {
        $payment = TenantsPlansPayment::on('mysql')->find($paymentId);
        
        if (!$payment || !$payment->asaas_subscription_id) {
            return response()->json(['success' => false, 'message' => 'Pagamento não encontrado']);
        }
        
        $asaasService = new \App\Services\AsaasService();
        try {
            $subscriptionPayments = $asaasService->listarCobrancasAssinatura($payment->asaas_subscription_id);
            
            if ($subscriptionPayments && isset($subscriptionPayments['data'][0])) {
                $invoiceUrl = $subscriptionPayments['data'][0]['invoiceUrl'] ?? null;
                $invoiceNumber = $subscriptionPayments['data'][0]['invoiceNumber'] ?? null;
                
                return response()->json([
                    'success' => true,
                    'invoiceUrl' => $invoiceUrl,
                    'invoiceNumber' => $invoiceNumber
                ]);
            }
            
            return response()->json(['success' => false, 'message' => 'Cobrança ainda não disponível']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    })->name('tenant-assinatura.get-invoice');

    //  ROTA DE SUCESSO NO PAGAMENTO

    Route::get('/tenant-assinatura/success', SubscriptionController::class . '@success')
        ->name('tenant-assinatura.success');    

    Route::get('/pagby-subscription/success_renew', [PagBySubscriptionController::class, 'successRenew'])
        ->name('pagby-subscription.success_renew');
    
    Route::get('/pagby-subscription/failure-renew', [PagBySubscriptionController::class, 'failureRenew'])
    ->name('pagby-subscription.failure-renew');




    //  ROTAS PROTEGIDAS (COM AUTH)
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'dashboard'])
            ->middleware(['auth', 'verified'])
            ->name('tenant.dashboard');


        Route::post('logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');                    
    });

    

});

// API interna para bot WhatsApp ativar usuários (fora dos middlewares)
Route::middleware([
    InitializeTenancyByDomain::class,
])->group(function () {
    Route::post('/api/whatsapp/activate', function(Request $request) {
        $phone = $request->input('phone');
        $jid = $request->input('jid'); // Novo: aceitar JID também
        $tenantName = tenant('name') ?? tenant('subdomain') ?? 'unknown';
        
        Log::info("🟣 API /whatsapp/activate chamada", [
            'tenant' => $tenantName,
            'phone_received' => $phone,
            'jid_received' => $jid
        ]);
        
        // Se recebeu JID, tenta buscar diretamente por ele primeiro
        if ($jid) {
            Log::info("🔍 Buscando por JID: $jid");
            $user = \App\Models\User::where('whatsapp_jid', $jid)->first();
            
            if ($user) {
                $user->whatsapp_activated = true;
                $user->save();
                
                Log::info("✅ Usuário ativado por JID!", [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'jid' => $jid
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'WhatsApp activated via JID',
                    'user' => $user->name
                ]);
            }
            
            Log::info("⚠️ Nenhum usuário encontrado com JID: $jid");
        }
        
        if (!$phone) {
            Log::warning("⚠️  Telefone e JID não fornecidos");
            return response()->json(['error' => 'Phone or JID required'], 400);
        }
        
        // Normaliza: remove tudo que não é dígito
        $normalizedPhone = preg_replace('/\D/', '', $phone);
        
        // Gera variações comuns de formato
        $variations = [
            $normalizedPhone,
            '0' . $normalizedPhone,
        ];
        
        // Se tem 10 dígitos (DDD + número sem 9), tenta adicionar o 9
        if (strlen($normalizedPhone) == 10) {
            $with9 = substr($normalizedPhone, 0, 2) . '9' . substr($normalizedPhone, 2);
            $variations[] = $with9;
            $variations[] = '0' . $with9;
        }
        
        // Se tem 11 dígitos (DDD + 9 + número), tenta remover o 9
        if (strlen($normalizedPhone) == 11) {
            $without9 = substr($normalizedPhone, 0, 2) . substr($normalizedPhone, 3);
            $variations[] = $without9;
            $variations[] = '0' . $without9;
        }
        
        // Remove variações duplicadas
        $variations = array_unique($variations);
        
        Log::info("🔄 Variações geradas", ['variations' => $variations]);
        
        // Lista usuários para debug
        $allUsers = \App\Models\User::select('id', 'name', 'phone')->get()->map(function($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'phone' => $u->phone,
                'normalized' => preg_replace('/\D/', '', $u->phone)
            ];
        });
        Log::info("👥 Usuários no banco", ['users' => $allUsers->toArray()]);
        
        // Busca no banco comparando telefones normalizados
        $users = \App\Models\User::where(function($query) use ($variations) {
            foreach ($variations as $variant) {
                $query->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '(', ''), ')', '') = ?", [$variant]);
            }
        })->get();
        
        $updated = 0;
        foreach ($users as $user) {
            $user->whatsapp_activated = true;
            // Auto-populate whatsapp_jid se foi fornecido e ainda não está salvo
            if ($jid && empty($user->whatsapp_jid)) {
                $user->whatsapp_jid = $jid;
                Log::info("📝 Auto-populando whatsapp_jid", [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'jid' => $jid
                ]);
            }
            $user->save();
            $updated++;
        }
        
        Log::info("📊 Resultado da busca", [
            'updated' => $updated,
            'success' => $updated > 0
        ]);
        
        return response()->json([
            'success' => $updated > 0,
            'updated' => $updated,
            'phone' => $normalizedPhone,
            'tried' => $variations
        ]);
    });
    
    // Endpoint para vincular telefone a JID (contas WhatsApp Business)
    Route::post('/api/whatsapp/link-jid', function(Request $request) {
        $phone = $request->input('phone');
        $jid = $request->input('jid');
        
        if (!$phone || !$jid) {
            return response()->json(['error' => 'Phone and JID required'], 400);
        }
        
        $normalizedPhone = preg_replace('/\D/', '', $phone);
        
        $variations = [
            $normalizedPhone,
            '0' . $normalizedPhone,
        ];
        
        if (strlen($normalizedPhone) == 10) {
            $with9 = substr($normalizedPhone, 0, 2) . '9' . substr($normalizedPhone, 2);
            $variations[] = $with9;
            $variations[] = '0' . $with9;
        }
        
        if (strlen($normalizedPhone) == 11) {
            $without9 = substr($normalizedPhone, 0, 2) . substr($normalizedPhone, 3);
            $variations[] = $without9;
            $variations[] = '0' . $without9;
        }
        
        $variations = array_unique($variations);
        
        $users = \App\Models\User::where(function($query) use ($variations) {
            foreach ($variations as $variant) {
                $query->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '(', ''), ')', '') = ?", [$variant]);
            }
        })->get();
        
        $linked = 0;
        foreach ($users as $user) {
            $user->whatsapp_jid = $jid;
            $user->save();
            $linked++;
            
            Log::info("✅ JID vinculado ao usuário", [
                'user_id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'jid' => $jid
            ]);
        }
        
        return response()->json([
            'success' => $linked > 0,
            'linked' => $linked,
            'user' => $linked > 0 ? $users->first()->name : null
        ]);
    });
});

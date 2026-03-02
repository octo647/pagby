<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TenantAdminController;
use App\Http\Controllers\PlanAdminController;
use App\Http\Controllers\TenantRegistrationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PagBySubscriptionController;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Middleware\VerifyCsrfToken;

// Todas as rotas devem estar dentro do loop central_domains para evitar conflitos com tenants

require __DIR__.'/subscription.php';
foreach (config('tenancy.central_domains') as $domain) {
    // Endpoint API para social login (central)
Route::get('/api/social-auth/{token}', function ($token) {
    $data = \Cache::get('social_auth_' . $token);
    if (!$data || !isset($data['user'])) {
        \Log::error('❌ Token não encontrado no cache', ['token' => $token]);
        return response()->json(['error' => 'Token inválido ou expirado.'], 404);
    }
    \Log::info('✅ Token validado com sucesso', ['email' => $data['user']['email']]);
    // Remove token do cache após uso (one-time use)
    \Cache::forget('social_auth_' . $token);
    return response()->json(['user' => $data['user']]);
})->name('api.social-auth');


    Route::domain($domain)->group(function () {
        Route::get('/', function () {
            return view('home');
        })->name('home');
        
        // Rotas de registro e contrato

        // Rota para envio do formulário de dúvidas sobre o modelo de negócio
        Route::post('/contato-duvida', [\App\Http\Controllers\ContatoDuvidaController::class, 'store'])->name('contato.duvida.store');
        // Rota GET de teste para diagnóstico
        Route::get('/contato-duvida', function() {
            \Log::info('GET /contato-duvida acessado');
            return response()->json(['ok' => true, 'msg' => 'GET /contato-duvida está funcionando']);
        });
        Route::get('/contrato', function() {
            return view('tenant.subscription.contrato', ['tenant' => (object)['fantasy_name' => 'Seu Negócio', 'cnpj' => '00.000.000/0000-00']]);
        })->name('contrato');
        
        Route::get('/register-tenant', [TenantRegistrationController::class, 'showForm'])->name('register-tenant');
        Route::post('/register-tenant', [TenantRegistrationController::class, 'register']);
        Route::get('/registration-success', [TenantRegistrationController::class, 'registrationSuccess'])->name('registration-success');
        Route::get('/registration-finalize/{contact_id?}', [PagBySubscriptionController::class, 'showPaymentForm'])->name('registration-finalize');
        
        // CORRIGIR: Escolher plano (nome correto da rota)
        Route::get('/escolher-plano/{plan}', [PagBySubscriptionController::class, 'choosePlan'])
             ->name('pagby-subscription.choose-plan')
             ->where('plan', 'mensal|trimestral|semestral|anual');
        
        Route::get('/funcionalidades', function () {
            return view('funcionalidades');
        })->name('funcionalidades');
        
        
        // CORRIGIR: ASSINATURAS PAGBY (nomes corretos)
        Route::prefix('pagby-subscription')->name('pagby-subscription.')->group(function () {   
            Route::get('/payment', [PagBySubscriptionController::class, 'showPaymentForm'])->name('payment');
            Route::post('/payment', [PagBySubscriptionController::class, 'processPayment'])->name('payment.process');
            Route::post('/asaas-pay/{paymentId}', [PagBySubscriptionController::class, 'asaasPay'])->name('asaas-pay');
            Route::post('/create', [PagBySubscriptionController::class, 'createSubscription'])->name('create');
            
            Route::get('/wait/{paymentId}', [PagBySubscriptionController::class, 'wait'])->name('wait');
            Route::get('/check-status/{paymentId}', [PagBySubscriptionController::class, 'checkStatus'])->name('check-status');
            Route::get('/success', [PagBySubscriptionController::class, 'success'])->name('success');
            Route::get('/failure', [PagBySubscriptionController::class, 'failure'])->name('failure');
            Route::get('/pending', [PagBySubscriptionController::class, 'pending'])->name('pending');
            Route::post('/webhook', [PagBySubscriptionController::class, 'webhook'])->name('webhook');
            Route::get('/history', [PagBySubscriptionController::class, 'history'])->name('history');
            
            // Endpoint de teste para simular webhook Asaas (apenas desenvolvimento)
            Route::get('/simulate-webhook/{paymentId}', [PagBySubscriptionController::class, 'simulateAsaasWebhook'])->name('simulate-webhook');
        });

        // Rota para buscar invoice de um pagamento de tenant (funciona no domínio central)
        Route::get('/tenant-assinatura/get-invoice/{paymentId}', function ($paymentId) {
            $tenantId = request('tenant_id');
            if (!$tenantId) {
                return response()->json(['success' => false, 'message' => 'tenant_id ausente']);
            }
            $tenant = \App\Models\Tenant::where('id', $tenantId)->first();
            if (!$tenant) {
                return response()->json(['success' => false, 'message' => 'Tenant não encontrado']);
            }
            tenancy()->initialize($tenant);
            $payment = \App\Models\TenantsPlansPayment::on('tenant')->find($paymentId);
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
        });

        // Rotas para assinaturas de planos dos tenants
        Route::prefix('tenant-assinatura')->name('tenant-assinatura.')->group(function () {
           Route::get('/congrats', [SubscriptionController::class, 'congrats'])->name('congrats');
           Route::post('/webhook', [SubscriptionController::class, 'webhook'])->name('webhook');
           Route::match(['get', 'post'], '/store', [SubscriptionController::class, 'store'])->name('store');
           Route::post('/cancelar', [SubscriptionController::class, 'cancelarAssinatura'])->name('cancelar');
           Route::get('/success', [SubscriptionController::class, 'success'])->name('success');
           Route::get('/failure', [SubscriptionController::class, 'failure'])->name('failure');
           Route::get('/wait', [SubscriptionController::class, 'wait'])->name('wait');
           Route::get('/check-status/{paymentId}', [SubscriptionController::class, 'checkStatus'])->name('check-status');
        });

        // Rotas para debug e pagamento automático
       /* Route::get('/pagby-subscription/create-test-user', [PagBySubscriptionController::class, 'createTestUser']);
        Route::get('/pagby-subscription/create-payment-new-user/{paymentId}', [PagBySubscriptionController::class, 'createPaymentWithNewUser']);
        Route::get('/pagby-subscription/create-payment-pix/{paymentId}', [PagBySubscriptionController::class, 'createPaymentWithGenericTest']);

        // Rotas para debug
        Route::get('/pagby-subscription/full-debug/{paymentId}', [PagBySubscriptionController::class, 'fullDebug']);
        Route::get('/pagby-subscription/create-auto-payment/{paymentId}', [PagBySubscriptionController::class, 'createAutoPayment']);
        Route::get('/pagby-subscription/debug-checkout-flow/{paymentId}', [PagBySubscriptionController::class, 'debugCheckoutFlow']);
        Route::get('/pagby-subscription/verify-vendedor', [PagBySubscriptionController::class, 'verifyVendedorCredentials']);
        */
        // ADMIN (com autenticação)
        Route::prefix('admin')->middleware(['auth', 'can:Admin'])->group(function () {
            Route::get('/tenants', [TenantAdminController::class, 'index'])->name('admin.tenants');
            Route::get('/tenants/{tenant}', [TenantAdminController::class, 'show'])->name('admin.tenants.show');
            Route::get('/planos', [PlanAdminController::class, 'index'])->name('admin.planos');

            // Verificação/atualização de status do pagamento Asaas
            Route::get('/asaas/verificar/{asaas_payment_id}', [\App\Http\Controllers\AsaasAdminController::class, 'verificarPagamento'])
                ->name('admin.asaas.verificar');
        });

        // ROTAS OAUTH
        Route::middleware(['web'])->group(function () {
            // Google OAuth (com tenant opcional na query)
            Route::get('/auth/google', [SocialController::class, 'redirectToGoogle'])
                ->name('login.google');
            Route::get('/auth/google/callback', [SocialController::class, 'handleGoogleCallback'])
                ->name('login.google.callback');
            // Facebook OAuth (com tenant opcional na query)
            Route::get('/auth/facebook', [SocialController::class, 'redirectToFacebook'])
                ->name('login.facebook');
            Route::get('/auth/facebook/callback', [SocialController::class, 'handleFacebookCallback'])
                ->name('login.facebook.callback');
        });

        Route::middleware(['auth'])->group(function () {
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
            Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
        });
        

        Route::post('webhook/mercado-pago', [SubscriptionController::class, 'webhook'])
            ->name('mercado-pago.webhook')
            ->withoutMiddleware([VerifyCsrfToken::class]);

        require __DIR__.'/auth.php';
    });
}











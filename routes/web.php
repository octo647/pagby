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
    Route::domain($domain)->group(function () {
        Route::get('/', function () {
            return view('home');
        })->name('home');
        
        // Rotas de registro e contrato
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
        });

        // ROTAS OAUTH
        Route::middleware(['web'])->group(function () {
            // Google OAuth
            Route::get('/auth/google', [SocialController::class, 'redirectToGoogle'])
                ->name('login.google');
            Route::get('/auth/google/callback', [SocialController::class, 'handleGoogleCallback'])
                ->name('login.google.callback');
            
            // Facebook OAuth
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











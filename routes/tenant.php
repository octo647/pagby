<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Illuminate\Support\Facades\Auth;
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
    

    //  ROTAS PÚBLICAS (SEM MIDDLEWARE)
    

    Route::get('register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])
                ->name('register');
    
    
    Route::post('register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);
// Subscription routes (sempre disponíveis)
    Route::get('/subscription/plans', [\App\Http\Controllers\TenantSubscriptionController::class, 'showPlans'])
        ->name('tenant.subscription.plans');
    Route::post('/subscription/select', [\App\Http\Controllers\TenantSubscriptionController::class, 'selectPlan'])
        ->name('tenant.subscription.select');
    Route::get('/subscription/success', [\App\Http\Controllers\TenantSubscriptionController::class, 'success'])
        ->name('tenant.subscription.success');
    Route::get('/subscription/blocked', [\App\Http\Controllers\TenantSubscriptionController::class, 'blocked'])
        ->name('tenant.subscription.blocked');
    Route::post('/trial/start', [\App\Http\Controllers\TenantSubscriptionController::class, 'startTrial'])
        ->name('tenant.trial.start');
    

    //  ROTA HOME PÚBLICA
    Route::middleware(['checkTenantSubscription'])->group(function () {
        Route::get('/', function () {
            $tenant = tenant();
            $services = Service::all();
            if (!$tenant) {
                abort(404, 'Tenant not found');
            }

            return view('tenants/' . ($tenant ? $tenant->id : 'default') . '/home', ['tenant' => $tenant, 'services' => $services]);
        })->name('tenant.home');
    Route::get('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])
                ->name('login');
    
    Route::post('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
        


    });
    
    //Escolher o plano quando vem do link de bloqueio
    Route::post('/subscription/select', [PagBySubscriptionController::class, 'selectPlan'])->name('pagby-subscription.select-plan');
    //Rota de renovação de assinatura
    Route::post('/tenant/renew-subscription', [PagBySubscriptionController::class, 'renewSubscription'])->name('tenant.renew');
    //rota página wait de renovação de assinatura

   
     Route::get('/tenant-assinatura/waitRenew/{paymentId}', [PagBySubscriptionController::class, 'waitRenew'])->name('tenant-assinatura.waitRenew');

     Route::get('/pagby-subscription/check-status/{paymentId}', [PagBySubscriptionController::class, 'checkStatus'])
    ->name('pagby-subscription.check-status');

    
    
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

    //  ROTA DE ESPERA DE PAGAMENTO
    Route::get('/tenant-assinatura/wait', function (Illuminate\Http\Request $request) {
        
        $payment_id = $request->query('paymentId');
        $payment = TenantsPlansPayment::on('mysql')->find($payment_id);
        $tenant_name = $request->query('tenant_name');     
        $plan_name = $payment->plan;
        
        $message = $request->query('message', 'Seu pagamento está sendo processado. Por favor, aguarde.');
        $checkoutUrl = $request->query('checkoutUrl');
        $amount = $payment->amount;
                
        // Se quiser, busque o plano do tenant aqui para exibir informações
        return view('tenant-assinatura.wait', [
            'tenant_name' => $tenant_name,
            'plan_name' => $plan_name,
            'payment' => $payment,
            'message' => $message,
            'checkout_url' => $checkoutUrl,
            'amount' => $amount,
        ]);
    })->name('tenant-assinatura.wait');

    // Rota de checagem de status do pagamento
    Route::get('/tenant-assinatura/check-status/{paymentId}', [SubscriptionController::class, 'checkStatus'])
        ->name('tenant-assinatura.check-status');

    //  ROTA DE SUCESSO NO PAGAMENTO

    Route::get('/tenant-assinatura/success', SubscriptionController::class . '@success')
        ->name('tenant-assinatura.success');    

    Route::get('/pagby-subscription/success_renew', [PagBySubscriptionController::class, 'successRenew'])
        ->name('pagby-subscription.success_renew');
    
    Route::get('/pagby-subscription/failure-renew', [PagBySubscriptionController::class, 'failureRenew'])
    ->name('pagby-subscription.failure-renew');



    //  ROTA SOCIAL CALLBACK
   Route::get('/auth/social-callback', function(Request $request) {
    // Recebe os dados do usuário via query string
    $userData = [
        'name'        => $request->get('name'),
        'email'       => $request->get('email'),
        'password'    => Hash::make(Str::random(24)),
        'email_verified_at' => now(),        
        'google_id' => $request->get('provider_id'),
        'photo'      => $request->get('avatar'),
    ];
   
    

    // Validação básica
    if (!$userData['email']) {
        return redirect('/login')->with('error', 'Dados de autenticação inválidos.');
    }

    $userModel = config('auth.providers.users.model');

    $user = $userModel::where('email', $userData['email'])->first();
    if (!$user->photo && $userData['photo']) {
    $user->photo = $userData['photo'];
    $user->save();
}
    
    if (!$user) {
        $user = $userModel::create([
            'name'              => $userData['name'] ?? $userData['email'],
            'email'             => $userData['email'],
            'password'          => Hash::make(Str::random(24)),
            'email_verified_at' => now(),
            'google_id'       => $userData['google_id'] ?? null,
            'photo'            => $userData['photo'],
        ]);
    } else {
        $user->update([
            'name' => $userData['name'] ?? $user->name,
            'google_id' => $userData['google_id'] ?? null,            
        ]);
    }
    
    Auth::guard('web')->login($user, true);
    
    return redirect()->intended('/dashboard');
});

    //  ROTAS PROTEGIDAS (COM AUTH)
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'dashboard'])
            ->middleware(['auth', 'verified'])
            ->name('tenant.dashboard');


        Route::post('logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
                    ->name('logout');
    });

    

});
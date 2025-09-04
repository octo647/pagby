<?php

declare(strict_types=1);

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;
use App\Models\Service;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    \Stancl\Tenancy\Middleware\ScopeSessions::class,

])->group(function () {

        // Rota de debug para verificar se o tenant está sendo identificado
        Route::get('/debug-tenant', function () {
            $tenant = tenant();
            return response()->json([
                'tenant' => $tenant ? $tenant->id : 'null',
                'domain' => request()->getHost(),
                'full_url' => request()->fullUrl(),
                'app_url' => config('app.url')
            ]);
        });

        // Rotas de autenticação específicas do tenant
        Route::middleware(['guest'])->group(function () {
            Route::get('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])
                        ->name('login');
            Route::post('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);

            Route::get('register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])
                        ->name('register');
            Route::post('register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);
            
            Route::get('forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])
                        ->name('password.request');
            Route::post('forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])
                        ->name('password.email');
        });

        Route::middleware('auth')->group(function () {
            Route::post('logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
                        ->name('logout');
        });

        // Rotas de assinatura (sempre disponíveis)
        Route::get('/subscription/plans', [\App\Http\Controllers\TenantSubscriptionController::class, 'showPlans'])
            ->name('tenant.subscription.plans');
        Route::post('/subscription/select', [\App\Http\Controllers\TenantSubscriptionController::class, 'selectPlan'])
            ->name('tenant.subscription.select');
        Route::get('/subscription/success', [\App\Http\Controllers\TenantSubscriptionController::class, 'success'])
            ->name('tenant.subscription.success');
        Route::get('/blocked', [\App\Http\Controllers\TenantSubscriptionController::class, 'blocked'])
            ->name('tenant.blocked');
        Route::post('/trial/start', [\App\Http\Controllers\TenantSubscriptionController::class, 'startTrial'])
            ->name('tenant.trial.start');

        // Rotas protegidas por verificação de assinatura
        Route::middleware(['checkTenantSubscription'])->group(function () {
            Route::get('/', function () {
                $tenant = tenant();
                $services = Service::all(); // Busca todos os serviços do tenant atual
                if (!$tenant) {
                    abort(404, 'Tenant not found');
                }
                
                return view('tenants/' . ($tenant ? $tenant->id : 'default') . '/home', ['tenant' => $tenant, 'services' => $services]);
            })->name('tenant.home');
            
            //Listagem dos planos
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
             Route::get('/payment', [\App\Http\Controllers\PaymentController::class, 'show'])->name('payment.page');
            Route::post('/payment', [\App\Http\Controllers\PaymentController::class, 'process'])->name('payment.process');
        });

    });


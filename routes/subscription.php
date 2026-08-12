<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionController;

/*
|--------------------------------------------------------------------------
| Subscription Routes - Asaas
|--------------------------------------------------------------------------
|
| Rotas para sistema de assinaturas com split de pagamentos via Asaas.
| Usa apenas Asaas, sem MercadoPago.
|
*/

Route::prefix('assinatura')->name('assinatura.')->group(function () {
    
    // Criar assinatura
    Route::post('/store', [SubscriptionController::class, 'store'])
        ->name('store');
    
    // Páginas de retorno
    Route::get('/success', [SubscriptionController::class, 'success'])
        ->name('success');
    
    Route::get('/pending', [SubscriptionController::class, 'pending'])
        ->name('pending');
    
    Route::get('/failure', [SubscriptionController::class, 'failure'])
        ->name('failure');
    
    Route::get('/wait', [SubscriptionController::class, 'wait'])
        ->name('wait');
    
    // Webhook Asaas (sem auth)
    Route::post('/webhook', [SubscriptionController::class, 'webhook'])
        ->name('webhook');
    
    // Cancelar assinatura
    Route::post('/cancelar', [SubscriptionController::class, 'cancelarAssinatura'])
        ->name('cancelar');
    
    // Verificar status (AJAX)
    Route::get('/check-status/{paymentId}', [SubscriptionController::class, 'checkStatus'])
        ->name('check-status');
    
    // Debug (remover em produção)
    Route::get('/debug/{paymentId}', [SubscriptionController::class, 'debugPayment'])
        ->name('debug');
});

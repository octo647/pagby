<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsaasSubscriptionController;

/*
|--------------------------------------------------------------------------
| Asaas Subscription Routes
|--------------------------------------------------------------------------
|
| Rotas para gerenciamento de assinaturas via Asaas com split de pagamentos.
| Estas rotas substituem as rotas do MercadoPago quando migrado para Asaas.
|
*/

// Rotas no domínio central e tenant
Route::prefix('asaas-assinatura')->name('asaas-assinatura.')->group(function () {
    
    // Criar assinatura (POST do formulário)
    Route::post('/store', [AsaasSubscriptionController::class, 'store'])
        ->name('store');
    
    // Páginas de retorno
    Route::get('/success', [AsaasSubscriptionController::class, 'success'])
        ->name('success');
    
    Route::get('/pending', [AsaasSubscriptionController::class, 'pending'])
        ->name('pending');
    
    Route::get('/failure', [AsaasSubscriptionController::class, 'failure'])
        ->name('failure');
    
    Route::get('/wait', [AsaasSubscriptionController::class, 'wait'])
        ->name('wait');
    
    // Webhook (não requer autenticação, mas deve validar assinatura Asaas)
    Route::post('/webhook', [AsaasSubscriptionController::class, 'webhook'])
        ->name('webhook');
    
    // Cancelar assinatura
    Route::post('/cancelar', [AsaasSubscriptionController::class, 'cancelarAssinatura'])
        ->name('cancelar');
    
    // Verificar status da assinatura (AJAX)
    Route::get('/check-status/{paymentId}', [AsaasSubscriptionController::class, 'checkStatus'])
        ->name('check-status');
    
    // Debug (remover em produção ou proteger com middleware admin)
    Route::get('/debug/{paymentId}', [AsaasSubscriptionController::class, 'debugPayment'])
        ->name('debug');
});

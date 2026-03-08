<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Payment;
use App\Models\SubscriptionPayment;
use App\Models\Appointment;
use Illuminate\Support\Facades\Log;

/**
 * Recebe webhooks das SUBCONTAS (não da master)
 * URL: https://pagby.com.br/api/subconta-webhook
 * 
 * Modelo SEM SPLIT - Asaas Subcontas
 * Processa 2 tipos de pagamento:
 * 1. Assinaturas de planos (subscription) → tenant{id}.subscriptions_payments
 * 2. Pagamentos avulsos → tenant{id}.payments
 * 
 * Ambos salvos no banco do TENANT (não central)
 */
class SubcontaWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('[Webhook Subconta] ===== WEBHOOK RECEBIDO =====');
        Log::info('[Webhook Subconta] Headers', $request->headers->all());
        Log::info('[Webhook Subconta] Payload', $request->all());
        
        try {
            $event = $request->input('event');
            $accountId = $request->input('account'); // ID da subconta que disparou
            $paymentId = $request->input('payment.id');
            $subscriptionId = $request->input('payment.subscription') ?? $request->input('subscription');
            
            Log::info('[Webhook Subconta] Identificando tipo', [
                'payment_id' => $paymentId,
                'subscription_id' => $subscriptionId,
                'is_subscription' => !empty($subscriptionId)
            ]);
            
            // Encontrar tenant pela subconta (usando conexão central)
            $tenant = \DB::connection('mysql')
                ->table('tenants')
                ->where('asaas_account_id', $accountId)
                ->first();
            
            if (!$tenant) {
                Log::warning('[Webhook Subconta] Tenant não encontrado', [
                    'account_id' => $accountId
                ]);
                return response()->json(['ok' => true, 'message' => 'Tenant not found'], 200);
            }
            
            Log::info('[Webhook Subconta] Tenant encontrado', [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name ?? 'N/A'
            ]);
            
            // Determinar tipo de pagamento e processar
            if (!empty($subscriptionId)) {
                // É um pagamento de ASSINATURA → tabela central
                return $this->processarPagamentoAssinatura($request, $tenant, $event);
            } else {
                // É um pagamento AVULSO → tabela do tenant
                return $this->processarPagamentoAvulso($request, $tenant, $event);
            }
            
        } catch (\Exception $e) {
            Log::error('[Webhook Subconta] ERRO', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Retornar 200 para não fazer o Asaas reenviar
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 200);
        }
    }
    
    /**
     * Processa pagamento de assinatura de plano
     * Modelo SEM SPLIT - Salva em: tenant{id}.subscriptions_payments (banco do tenant)
     */
    private function processarPagamentoAssinatura(Request $request, $tenant, string $event)
    {
        $paymentId = $request->input('payment.id');
        $subscriptionId = $request->input('payment.subscription') ?? $request->input('subscription');
        
        Log::info('[Webhook Subconta] Processando ASSINATURA', [
            'tenant_id' => $tenant->id,
            'subscription_id' => $subscriptionId,
            'payment_id' => $paymentId
        ]);
        
        // Inicializar tenancy (mesmo para assinaturas agora)
        $tenantModel = Tenant::find($tenant->id);
        tenancy()->initialize($tenantModel);
        
        // Garantir conexão do tenant
        config(['database.default' => 'tenant']);
        \DB::purge('tenant');
        \DB::reconnect('tenant');
        
        Log::info('[Webhook Subconta] Conexão trocada para tenant (assinatura)');
        
        // Buscar no banco do tenant
        $payment = SubscriptionPayment::on('tenant')
            ->where('asaas_payment_id', $paymentId)
            ->first();
        
        if (!$payment) {
            Log::warning('[Webhook Subconta] Pagamento de assinatura não encontrado', [
                'tenant_id' => $tenant->id,
                'payment_id' => $paymentId,
                'subscription_id' => $subscriptionId
            ]);
            return response()->json(['ok' => true, 'message' => 'Subscription payment not found'], 200);
        }
        
        // Processar evento da assinatura
        $this->processarEventoAssinatura($event, $payment, $request->all());
        
        Log::info('[Webhook Subconta] Assinatura processada com sucesso');
        
        return response()->json(['ok' => true, 'message' => 'Subscription processed'], 200);
    }
    
    /**
     * Processa pagamento avulso de serviço
     * Salva em: tenant{id}.payments (banco do tenant)
     */
    private function processarPagamentoAvulso(Request $request, $tenant, string $event)
    {
        $paymentId = $request->input('payment.id');
        
        Log::info('[Webhook Subconta] Processando pagamento AVULSO', [
            'tenant_id' => $tenant->id,
            'payment_id' => $paymentId
        ]);
        
        // Inicializar tenancy
        $tenantModel = Tenant::find($tenant->id);
        tenancy()->initialize($tenantModel);
        
        // Garantir conexão do tenant
        config(['database.default' => 'tenant']);
        \DB::purge('tenant');
        \DB::reconnect('tenant');
        
        Log::info('[Webhook Subconta] Conexão trocada para tenant');
        
        // Buscar no banco do tenant
        $payment = Payment::on('tenant')
            ->where('asaas_payment_id', $paymentId)
            ->first();
        
        if (!$payment) {
            Log::warning('[Webhook Subconta] Pagamento avulso não encontrado', [
                'tenant_id' => $tenant->id,
                'payment_id' => $paymentId
            ]);
            return response()->json(['ok' => true, 'message' => 'Payment not found'], 200);
        }
        
        // Processar evento avulso
        $this->processarEventoAvulso($event, $payment, $request->all());
        
        Log::info('[Webhook Subconta] Pagamento avulso processado com sucesso');
        
        return response()->json(['ok' => true, 'message' => 'Payment processed'], 200);
    }
    
    /**
     * Processa eventos de pagamentos de ASSINATURA
     * Modelo SEM SPLIT - Atualiza: tenant{id}.subscriptions_payments
     */
    private function processarEventoAssinatura(string $event, SubscriptionPayment $payment, array $data)
    {
        Log::info('[Webhook Subconta] Processando evento ASSINATURA', [
            'event' => $event,
            'payment_id' => $payment->id,
            'subscription_id' => $payment->subscription_id
        ]);
        
        switch ($event) {
            case 'PAYMENT_CREATED':
                $payment->status = 'pending';
                $payment->save();
                break;
                
            case 'PAYMENT_RECEIVED':
            case 'PAYMENT_CONFIRMED':
                // Pagamento de assinatura confirmado ✅
                $payment->markAsReceived([
                    'confirmed_at' => now(),
                    'received_at' => now()
                ]);
                
                // Atualizar subscription também
                if ($payment->subscription) {
                    $payment->subscription->status = 'Ativo';
                    $payment->subscription->save();
                }
                
                Log::info('[Webhook Subconta] Assinatura paga', [
                    'payment_id' => $payment->id,
                    'subscription_id' => $payment->subscription_id,
                    'amount' => $payment->amount
                ]);
                break;
                
            case 'PAYMENT_OVERDUE':
                // Assinatura vencida - notificar cliente
                $payment->markAsOverdue();
                
                // Atualizar subscription
                if ($payment->subscription) {
                    $payment->subscription->status = 'Expirado';
                    $payment->subscription->save();
                }
                
                Log::warning('[Webhook Subconta] Assinatura vencida', [
                    'payment_id' => $payment->id,
                    'subscription_id' => $payment->subscription_id
                ]);
                
                // TODO: Notificar cliente que a assinatura dele está vencida
                break;
                
            case 'PAYMENT_DELETED':
            case 'PAYMENT_REFUNDED':
                $payment->status = 'refunded';
                $payment->save();
                
                if ($payment->subscription) {
                    $payment->subscription->status = 'Cancelado';
                    $payment->subscription->save();
                }
                break;
        }
    }
    
    /**
     * Processa eventos de pagamentos AVULSOS
     * Atualiza: tenant{id}.payments
     */
    private function processarEventoAvulso($event, Payment $payment, array $data)
    {
        Log::info('[Webhook Subconta] Processando evento', [
            'event' => $event,
            'payment_id' => $payment->id,
            'appointment_id' => $payment->appointment_id
        ]);
        
        switch ($event) {
            case 'PAYMENT_CREATED':
                // Pagamento criado
                $payment->status = 'pending';
                $payment->save();
                break;
                
            case 'PAYMENT_RECEIVED':
            case 'PAYMENT_CONFIRMED':
                // Pagamento recebido/confirmado ✅
                $payment->status = 'paid';
                $payment->paid_at = now();
                $payment->save();
                
                // Atualizar appointment
                if ($payment->appointment) {
                    $payment->appointment->payment_status = 'paid';
                    $payment->appointment->save();
                }
                
                Log::info('[Webhook Subconta] Pagamento confirmado', [
                    'payment_id' => $payment->id,
                    'amount' => $payment->amount
                ]);
                break;
                
            case 'PAYMENT_OVERDUE':
                // Pagamento vencido ⚠️
                $payment->status = 'overdue';
                $payment->save();
                
                // Atualizar appointment
                if ($payment->appointment) {
                    $payment->appointment->payment_status = 'overdue';
                    $payment->appointment->save();
                }
                
                // Verificar se deve bloquear cliente
                $tenant = tenant();
                if ($tenant->getSetting('bloqueio_automatico_inadimplentes', false)) {
                    $customer = $payment->appointment->customer;
                    
                    // Contar pagamentos atrasados total
                    $totalOverdue = Payment::whereHas('appointment', function($q) use ($customer) {
                        $q->where('customer_id', $customer->id);
                    })
                    ->where('status', 'overdue')
                    ->count();
                    
                    // Bloquear se tem pagamentos atrasados
                    if ($totalOverdue > 0) {
                        $customer->is_blocked = true;
                        $customer->save();
                        
                        Log::info('[Webhook Subconta] Cliente bloqueado por inadimplência', [
                            'customer_id' => $customer->id,
                            'total_overdue' => $totalOverdue
                        ]);
                    }
                }
                
                // Notificar proprietário
                $this->notificarProprietario($payment);
                
                break;
                
            case 'PAYMENT_DELETED':
            case 'PAYMENT_REFUNDED':
                $payment->status = 'refunded';
                $payment->save();
                break;
        }
    }
    
    private function notificarProprietario(Payment $payment)
    {
        // Enviar notificação para proprietários
        $proprietarios = \App\Models\User::whereHas('roles', function($q) {
            $q->where('name', 'Proprietário');
        })->get();
        
        foreach ($proprietarios as $proprietario) {
            $proprietario->notify(
                new \App\Notifications\ClienteInadimplente($payment)
            );
        }
    }
}

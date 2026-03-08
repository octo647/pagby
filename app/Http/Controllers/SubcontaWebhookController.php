<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Payment;
use App\Models\Appointment;
use Illuminate\Support\Facades\Log;

/**
 * Recebe webhooks das SUBCONTAS (não da master)
 * URL: https://pagby.com.br/api/subconta-webhook
 */
class SubcontaWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('[Webhook Subconta] Recebido', $request->all());
        
        $event = $request->input('event');
        $accountId = $request->input('account'); // ID da subconta que disparou
        $paymentId = $request->input('payment.id');
        $paymentValue = $request->input('payment.value');
        $paymentStatus = $request->input('payment.status');
        
        // Encontrar tenant pela subconta
        $tenant = Tenant::on('mysql')
            ->where('asaas_account_id', $accountId)
            ->first();
        
        if (!$tenant) {
            Log::warning('[Webhook Subconta] Tenant não encontrado', [
                'account_id' => $accountId
            ]);
            return response()->json(['ok' => true], 200);
        }
        
        // Inicializar tenancy para acessar banco do tenant
        tenancy()->initialize($tenant);
        
        // Buscar payment no banco do tenant
        $payment = Payment::where('asaas_payment_id', $paymentId)->first();
        
        if (!$payment) {
            Log::warning('[Webhook Subconta] Payment não encontrado no tenant', [
                'tenant_id' => $tenant->id,
                'payment_id' => $paymentId
            ]);
            
            // Pode ser um pagamento criado fora do sistema
            return response()->json(['ok' => true], 200);
        }
        
        // Processar evento
        $this->processarEvento($event, $payment, $request->all());
        
        return response()->json(['ok' => true], 200);
    }
    
    private function processarEvento($event, Payment $payment, array $data)
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

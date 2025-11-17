<?php
// app/Http/Controllers/PaymentController.php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Payment;
use Illuminate\Http\Request;
use MercadoPago\SDK;
use MercadoPago\Preference;
use MercadoPago\Item;
use MercadoPago\Payment as MPPayment;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Só inicializar MP se as credenciais estiverem configuradas
        if (config('services.mercadopago.access_token')) {
            SDK::setAccessToken(config('services.mercadopago.access_token'));
        }
    }

    public function show(Request $request)
    {
        $pending = session('pending_appointment');
        if (!$pending) {
            return redirect()->route('dashboard')->with('error', 'Nenhum agendamento pendente.');
        }

        return view('payment', [
            'services' => $pending['services'],
            'total' => $pending['total'],
        ]);
    }

    public function process(Request $request)
    {
        $pending = session('pending_appointment');
        $paymentMethod = $request->input('payment_method');

        if (!$pending) {
            return redirect()->route('dashboard')->with('error', 'Nenhum agendamento pendente.');
        }

        try {
            if ($paymentMethod === 'pix') {
                return $this->processPixPayment($pending);
            }

            if ($paymentMethod === 'card') {
                return $this->processCardPayment($pending, $request);
            }

            if ($paymentMethod === 'presencial') {
                return $this->processInPersonPayment($pending);
            }

            return back()->with('error', 'Método de pagamento não suportado.');

        } catch (\Exception $e) {
            Log::error('Erro no processamento de pagamento: ' . $e->getMessage());
            return back()->with('error', 'Erro ao processar pagamento. Tente novamente.');
        }
    }

    private function processPixPayment($pending)
    {
        try {
            $preference = new Preference();
            
            $item = new Item();
            $item->title = "Agendamento - " . tenant('name');
            $item->description = "Serviços: " . implode(', ', array_column($pending['services'], 'name'));
            $item->quantity = 1;
            $item->unit_price = $pending['total'];
            
            $preference->items = array($item);
            
            // URLs de retorno
            $preference->back_urls = [
                "success" => route('payment.success'),
                "failure" => route('payment.failure'),
                "pending" => route('payment.pending')
            ];
            
            $preference->auto_return = "approved";
            
            // External reference para identificar o pagamento
            $preference->external_reference = "appointment|" . tenant('id') . "|" . time();
            
            // Configurar apenas PIX
            $preference->payment_methods = [
                "excluded_payment_types" => [
                    ["id" => "credit_card"],
                    ["id" => "debit_card"]
                ],
                "installments" => 1
            ];

            $preference->save();

            // Salvar dados na sessão para processar depois
            session([
                'payment_preference_id' => $preference->id,
                'payment_external_ref' => $preference->external_reference
            ]);

            return redirect($preference->init_point);

        } catch (\Exception $e) {
            Log::error('Erro ao criar preferência PIX: ' . $e->getMessage());
            throw $e;
        }
    }

    private function processCardPayment($pending, $request)
    {
        try {
            $preference = new Preference();
            
            $item = new Item();
            $item->title = "Agendamento - " . tenant('name');
            $item->description = "Serviços: " . implode(', ', array_column($pending['services'], 'name'));
            $item->quantity = 1;
            $item->unit_price = $pending['total'];
            
            $preference->items = array($item);
            
            $preference->back_urls = [
                "success" => route('payment.success'),
                "failure" => route('payment.failure'),
                "pending" => route('payment.pending')
            ];
            
            $preference->auto_return = "approved";
            $preference->external_reference = "appointment|" . tenant('id') . "|" . time();
            
            // Configurar parcelamento
            $preference->payment_methods = [
                "installments" => 12
            ];

            $preference->save();

            session([
                'payment_preference_id' => $preference->id,
                'payment_external_ref' => $preference->external_reference
            ]);

            return redirect($preference->init_point);

        } catch (\Exception $e) {
            Log::error('Erro ao criar preferência cartão: ' . $e->getMessage());
            throw $e;
        }
    }

    private function processInPersonPayment($pending)
    {
        // Criar agendamento diretamente
        $appointment = Appointment::create($pending + [
            'status' => 'Pendente',
            'payment_method' => 'presencial',
            'payment_status' => 'pending'
        ]);

        // Registrar "pagamento" presencial
        Payment::create([
            'appointment_id' => $appointment->id,
            'amount' => $pending['total'],
            'payment_method' => 'presencial',
            'status' => 'pending',
            'mp_payment_id' => null
        ]);

        session()->forget('pending_appointment');
        
        return redirect()->route('dashboard')
            ->with('success', 'Agendamento realizado! Pague no salão na data do atendimento.');
    }

    public function success(Request $request)
    {
        $paymentId = $request->get('payment_id');
        $status = $request->get('status');
        $externalReference = $request->get('external_reference');

        Log::info("Payment success - agendamento", [
            'payment_id' => $paymentId,
            'status' => $status,
            'external_reference' => $externalReference
        ]);

        if ($paymentId && $externalReference) {
            $this->processPaymentCallback($paymentId, $externalReference);
        }

        session()->forget(['pending_appointment', 'payment_preference_id', 'payment_external_ref']);

        return view('payment.success', [
            'title' => 'Pagamento Aprovado!',
            'message' => 'Seu agendamento foi confirmado e o pagamento processado com sucesso.',
            'payment_id' => $paymentId
        ]);
    }

    public function failure(Request $request)
    {
        Log::info("Payment failure - agendamento", $request->all());
        
        return view('payment.failure', [
            'title' => 'Pagamento Rejeitado',
            'message' => 'Não foi possível processar seu pagamento. Tente novamente ou escolha outro método.',
            'retry_url' => route('payment.show')
        ]);
    }

    public function pending(Request $request)
    {
        $paymentId = $request->get('payment_id');
        
        Log::info("Payment pending - agendamento", [
            'payment_id' => $paymentId
        ]);

        return view('payment.pending', [
            'title' => 'Pagamento Pendente',
            'message' => 'Seu pagamento está sendo processado. Você receberá uma confirmação em breve.',
            'payment_id' => $paymentId
        ]);
    }

    public function webhook(Request $request)
    {
        $data = $request->all();
        
        Log::info('Webhook MP - agendamento:', $data);

        if (isset($data['type']) && $data['type'] === 'payment') {
            $paymentId = $data['data']['id'];
            $this->processPaymentWebhook($paymentId);
        }

        return response()->json(['status' => 'ok'], 200);
    }

    private function processPaymentCallback($paymentId, $externalReference)
    {
        try {
            // Verificar se é pagamento de agendamento
            if (!str_starts_with($externalReference, 'appointment|')) {
                return;
            }

            $mpPayment = MPPayment::find_by_id($paymentId);
            $pending = session('pending_appointment');

            if (!$pending) {
                Log::warning("Sessão perdida para pagamento: {$paymentId}");
                return;
            }

            // Criar agendamento
            $appointment = Appointment::create($pending + [
                'status' => $mpPayment->status === 'approved' ? 'Confirmado' : 'Pendente',
                'payment_method' => 'mercadopago',
                'payment_status' => $mpPayment->status
            ]);

            // Registrar pagamento
            Payment::create([
                'appointment_id' => $appointment->id,
                'mp_payment_id' => $paymentId,
                'amount' => $mpPayment->transaction_amount,
                'payment_method' => $mpPayment->payment_method_id ?? 'pix',
                'status' => $mpPayment->status,
                'mp_data' => json_decode(json_encode($mpPayment), true)
            ]);

            Log::info("Agendamento criado via callback", [
                'appointment_id' => $appointment->id,
                'payment_id' => $paymentId,
                'status' => $mpPayment->status
            ]);

        } catch (\Exception $e) {
            Log::error('Erro no callback de agendamento: ' . $e->getMessage());
        }
    }

    private function processPaymentWebhook($paymentId)
    {
        try {
            $mpPayment = MPPayment::find_by_id($paymentId);
            $externalReference = $mpPayment->external_reference;
            
            if ($externalReference && str_starts_with($externalReference, 'appointment|')) {
                Log::info("Processando webhook agendamento", [
                    'payment_id' => $paymentId,
                    'status' => $mpPayment->status
                ]);

                // Buscar appointment pelo external_reference ou payment_id
                $payment = Payment::where('mp_payment_id', $paymentId)->first();
                if ($payment && $payment->appointment) {
                    $payment->update(['status' => $mpPayment->status]);
                    
                    if ($mpPayment->status === 'approved') {
                        $payment->appointment->update([
                            'status' => 'Confirmado',
                            'payment_status' => 'approved'
                        ]);
                    }
                }
            }
            
        } catch (\Exception $e) {
            Log::error('Erro no webhook agendamento: ' . $e->getMessage());
        }
    }
}
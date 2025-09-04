<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function show(Request $request)
    {
        // Pegue os dados do agendamento pendente da sessão
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
        // Aqui você processa o pagamento (Pix, cartão, etc)
        // Exemplo: se for Pix, gera QR Code; se for cartão, integra com gateway; se for presencial, só confirma
        $pending = session('pending_appointment');
    $paymentMethod = $request->input('payment_method');

    if ($paymentMethod === 'pix') {
        // Exemplo fictício de geração de QR Code Pix
        // Integre com API real do seu provedor de pagamentos
        $pixPayload = [
            'valor' => $pending['total'],
            'descricao' => 'Pagamento de agendamento',
            'chave_pix' => 'suachave@pix.com.br',
        ];

        // Aqui você chamaria a API do seu gateway (exemplo fictício)
        // $qrCode = GatewayPix::gerarQrCode($pixPayload);

        // Exemplo de QR Code fictício
        $qrCode = '00020126360014BR.GOV.BCB.PIX0114suachave@pix.com.br520400005303986540510.005802BR5920Nome do Salão6009SAO PAULO62070503***6304B14F';

        return view('payment_pix', [
            'qrCode' => $qrCode,
            'total' => $pending['total'],
        ]);
    }

    if ($paymentMethod === 'presencial') {
        // Crie o agendamento normalmente, status "aguardando pagamento"
        \App\Models\Appointment::create($pending + ['status' => 'Pendente']);
        session()->forget('pending_appointment');
        return redirect()->route('dashboard')->with('success', 'Agendamento realizado! Pague no salão.');
    }

    // Para cartão, integre com o gateway desejado
    // ...

    return back()->with('error', 'Método de pagamento não suportado.');

        // Após pagamento, crie o agendamento definitivo
        // Appointment::create(session('pending_appointment') + ['status' => 'confirmed']);

        session()->forget('pending_appointment');
        return redirect()->route('dashboard')->with('success', 'Pagamento realizado e agendamento confirmado!');
    }
}

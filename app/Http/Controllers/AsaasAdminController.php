<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AsaasService;
use App\Models\PagByPayment;

class AsaasAdminController extends Controller
{
    public function verificarPagamento($asaas_payment_id)
    {
        $asaas = new AsaasService();
        $status = $asaas->consultarCobranca($asaas_payment_id);

        // Busca o pagamento local
        $pagamento = PagByPayment::where('asaas_payment_id', $asaas_payment_id)->first();
        if ($pagamento && isset($status['status'])) {
            $pagamento->status = $status['status'];
            $pagamento->save();
        }

        return view('admin.asaas-status', [
            'asaas_payment_id' => $asaas_payment_id,
            'status' => $status,
            'pagamento' => $pagamento,
        ]);
    }
}

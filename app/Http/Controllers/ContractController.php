<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Models\Tenant;

class ContractController extends Controller
{
    public function showAccept()
    {
        $tenant = tenant();
        return view('tenant.subscription.accept-contract', compact('tenant'));
    }

    public function accept(Request $request)
    {
        $tenant = tenant();
        // Marcar aceite do contrato
        $tenant->contract_accepted_at = now();
        $tenant->save();
        // Redirecionar para página de pagamento Mercado Pago
        return redirect()->route('pagby-subscription.choose-plan', ['plan' => 'basico']);
    }
}

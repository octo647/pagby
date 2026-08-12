<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureContractAccepted
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $tenant = tenant();
       
        if ($tenant && empty($tenant->contract_accepted_at)) {
            // Evita loop infinito se já estiver na página de aceite
            if (!$request->routeIs('tenant.contract.accept-page') && !$request->routeIs('tenant.contract.accept')) {
                return redirect()->route('tenant.contract.accept-page');
            }
        }
        return $next($request);
    }
}

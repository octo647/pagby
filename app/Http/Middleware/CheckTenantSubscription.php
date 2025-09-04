<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica se estamos em contexto de tenant
        if (!tenant()) {
            return $next($request);
        }

        $tenant = tenant();

        // Páginas que não devem ser bloqueadas (seleção de plano, pagamento, etc.)
        $allowedRoutes = [
            'tenant.subscription.plans',
            'tenant.subscription.select',
            'tenant.subscription.payment',
            'tenant.subscription.success',
            'tenant.blocked',
            'logout',
        ];

        // Se está numa rota permitida, continua
        if (in_array($request->route()?->getName(), $allowedRoutes)) {
            return $next($request);
        }

        // Verifica se o tenant deve ser bloqueado
        if ($tenant->shouldBeBlocked()) {
            // Atualiza status se necessário
            if ($tenant->isTrialExpired() && $tenant->subscription_status === 'trial') {
                $tenant->subscription_status = 'expired';
                $tenant->is_blocked = true;
                $tenant->save();
            }

            if ($tenant->isSubscriptionExpired() && $tenant->subscription_status === 'active') {
                $tenant->subscription_status = 'expired';
                $tenant->is_blocked = true;
                $tenant->save();
            }

            // Redireciona para página de planos ou bloqueio
            return redirect()->route('tenant.blocked');
        }

        return $next($request);
    }
}

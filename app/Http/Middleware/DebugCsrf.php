<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class DebugCsrf
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST') && $request->is('login')) {
            $csrfTokenInput = $request->input('_token');
            $csrfTokenHeader = $request->header('X-CSRF-TOKEN');
            $sessionToken = $request->session()->token();
            $sessionId = $request->session()->getId();
            $host = $request->getHost();
            $tenant = tenant() ? tenant()->id : 'none';

            Log::info('[DebugCsrf] Login attempt', [
                'host' => $host,
                'tenant' => $tenant,
                'csrf_input' => $csrfTokenInput,
                'csrf_header' => $csrfTokenHeader,
                'session_token' => $sessionToken,
                'session_id' => $sessionId,
                'tokens_match' => hash_equals($sessionToken ?? '', $csrfTokenInput ?? ''),
                'session_domain' => config('session.domain'),
                'session_driver' => config('session.driver'),
            ]);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;

class HandleSessionErrors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (TokenMismatchException $e) {
            // Log o erro para debugging
            Log::warning('Token CSRF expirado detectado', [
                'url' => $request->url(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Limpar a sessão corrompida
            $request->session()->flush();
            $request->session()->regenerate();

            // Redirecionar para login com mensagem amigável
            return redirect()->route('login')->with('warning', 'Sua sessão expirou. Por favor, faça login novamente.');
        } catch (\Exception $e) {
            // Capturar outros erros relacionados a sessão
            if (str_contains($e->getMessage(), 'session') || str_contains($e->getMessage(), 'token')) {
                Log::warning('Erro de sessão detectado', [
                    'error' => $e->getMessage(),
                    'url' => $request->url(),
                    'ip' => $request->ip()
                ]);

                // Tentar limpar e regenerar sessão
                try {
                    $request->session()->flush();
                    $request->session()->regenerate();
                } catch (\Exception $sessionError) {
                    // Se não conseguir limpar a sessão, forçar redirect
                    Log::error('Não foi possível limpar sessão corrompida', [
                        'original_error' => $e->getMessage(),
                        'session_error' => $sessionError->getMessage()
                    ]);
                }

                return redirect()->route('login')->with('error', 'Ocorreu um erro de sessão. Tente fazer login novamente.');
            }

            // Re-throw other exceptions
            throw $e;
        }
    }
}

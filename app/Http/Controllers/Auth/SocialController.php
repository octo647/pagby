<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
public function redirectToGoogle(Request $request)
    {
        // Pegue o tenant da query string (vindo do botão de login do tenant)
        $tenantHost = $request->get('tenant');
   
        if (!$tenantHost) {
            return redirect('/login')->with('error', 'Tenant não identificado.');
        }

        // Salve o tenant na sessão do central
        session(['tenant_host' => $tenantHost]);

        // (Opcional) Defina as credenciais manualmente se necessário
        config([
            'services.google.client_id' => env('GOOGLE_CLIENT_ID'),
            'services.google.client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'services.google.redirect' => env('GOOGLE_REDIRECT_URI'),
        ]);


        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        // Obtenha os dados do usuário autenticado pelo Google
        \Log::info('Session ID: ' . session()->getId());
        \Log::info('Session data:', session()->all());
        $socialUser = Socialite::driver('google')->user();
      

        // Recupere o tenant da sessão do central
        $tenantHost = session('tenant_host');
        if (!$tenantHost) {
            return redirect('/login')->with('error', 'Tenant não identificado.');
        }

        // Monte a URL do tenant com os dados do usuário
        $params = http_build_query([
            'name'        => $socialUser->getName(),
            'email'       => $socialUser->getEmail(),
            'provider'    => 'google',
            'provider_id' => $socialUser->getId(),
            'avatar'      => $socialUser->getAvatar(),
        ]);
        $tenantUrl = "http://{$tenantHost}.localhost:8000/auth/social-callback?{$params}";

        return redirect()->away($tenantUrl);
    }

    /**
     * Redirecionar para o tenant com os dados do usuário
     */
    private function redirectToTenant($socialUser, $provider, $tenantHost)
    {
        // Criar token único
        $token = Str::random(60);
        
        // Salvar dados na sessão CENTRAL
        session()->put('social_auth_' . $token, [
            'user' => [
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
            ],
            'expires_at' => now()->addMinutes(10),
        ]);
        
        \Log::info("🎫 Token criado: " . $token);
        \Log::info("📍 Redirecionando para tenant: " . $tenantHost);
        
        // URL do tenant
        $tenantUrl = "http://{$tenantHost}/auth/social-callback?token={$token}";
        
        return redirect()->away($tenantUrl);
    }

    /**
     * Rota de teste simples
     */
    public function test()
    {
        return response()->json([
            'status' => 'working',
            'message' => 'SocialController está funcionando!',
            'timestamp' => now()
        ]);
    }
}
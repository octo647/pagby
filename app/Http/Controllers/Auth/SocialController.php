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
        // No contexto do tenant, não precisa buscar via query string
        $tenantHost = tenant('id') ?? (tenant() ? tenant()->id : null);
        if (!$tenantHost) {
            return redirect('/login')->with('error', 'Tenant não identificado.');
        }

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
        $socialUser = Socialite::driver('google')->user();

        // Aqui você pode autenticar ou registrar o usuário diretamente no tenant
        $userModel = config('auth.providers.users.model');
        $user = $userModel::where('email', $socialUser->getEmail())->first();
        if (!$user) {
            $user = $userModel::create([
                'name' => $socialUser->getName() ?? $socialUser->getEmail(),
                'email' => $socialUser->getEmail(),
                'password' => Hash::make(Str::random(24)),
                'email_verified_at' => now(),
                'google_id' => $socialUser->getId(),
                'photo' => $socialUser->getAvatar(),
            ]);
        } else {
            $user->update([
                'name' => $socialUser->getName() ?? $user->name,
                'google_id' => $socialUser->getId(),
            ]);
        }

        Auth::guard('web')->login($user, true);
        return redirect()->intended('/dashboard');
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
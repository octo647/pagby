<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class GoogleController extends \App\Http\Controllers\Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
    $googleUser = Socialite::driver('google')->user();

        // Multi-tenant: identificar tenant pelo subdomínio, sessão ou contexto
        // Exemplo: $tenantId = tenant('id');

        $user = User::updateOrCreate([
            'email' => $googleUser->getEmail(),
        ], [
            'name' => $googleUser->getName(),
            'photo' => $googleUser->getAvatar(),
            // Adicione outros campos conforme necessário
        ]);

        Auth::login($user);

        // Redirecionar para dashboard do tenant
        return redirect()->intended('/dashboard');
    }
}
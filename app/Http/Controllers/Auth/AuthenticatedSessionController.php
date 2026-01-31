<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        // Verifica se o usuário está ativo após autenticar
        if (Auth::user()->status !== 'Ativo') {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Seu usuário está inativo. Fale com o administrador.',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

    // Exemplo: ajuste conforme sua lógica de papéis
    if ($user->hasRole('Funcionário')) {
        return redirect()->route('tenant.dashboard', ['tabelaAtiva' => 'agenda']);
        } elseif ($user->hasRole('Proprietário')) {
            return redirect()->route('tenant.dashboard', ['tabelaAtiva' => 'gerenciar-comandas']);
    } elseif ($user->hasRole('Admin')) {
        return redirect()->route('tenant.dashboard', ['tabelaAtiva' => 'contatos']); // Redireciona para a página index
    } else {
        return redirect()->route('tenant.dashboard');
    }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

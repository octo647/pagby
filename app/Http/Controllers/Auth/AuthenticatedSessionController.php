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

        // IMPORTANTE: Salvar booking_data antes de regenerar a sessão
        $bookingData = session('booking_data');
        $requiresLogin = session('requires_login_for_booking');
        $hasBookingData = session()->has('booking_data');
        
        \Log::info('Login: Before regenerate', [
            'has_booking_data' => $hasBookingData,
            'booking_data' => $bookingData,
            'requires_login' => $requiresLogin,
            'all_session_keys' => array_keys(session()->all()),
        ]);

        $request->session()->regenerate();
        
        \Log::info('Login: After regenerate', [
            'all_session_keys' => array_keys(session()->all()),
        ]);
        
        // Restaurar booking_data após regenerar
        if ($hasBookingData && $bookingData) {
            session()->put('booking_data', $bookingData);
            if ($requiresLogin) {
                session()->put('requires_login_for_booking', $requiresLogin);
            }
            
            \Log::info('Login: Booking data restored after regenerate', [
                'restored_data' => session('booking_data'),
                'has_booking_data_now' => session()->has('booking_data'),
            ]);
        } else {
            \Log::warning('Login: No booking data to restore', [
                'hasBookingData' => $hasBookingData,
                'bookingData' => $bookingData,
            ]);
        }

        $user = Auth::user();
        
        // Se há dados de agendamento pendentes, redirecionar para /agendar
        if (session()->has('booking_data')) {
            \Log::info('Login: Redirecting to /agendar with booking data');
            return redirect()->route('agendar');
        }

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

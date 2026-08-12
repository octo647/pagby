<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'salon_id' => tenancy()->tenant->id,
            'phone' => $request->phone,
        ]);

        $user->assignRole(3, 0);//atribui a função de cliente ao novo usuário

        event(new Registered($user));

        // Envia email de boas-vindas
        if ($user->email) {
            try {
                \Illuminate\Support\Facades\Log::info('📧 Tentando enviar e-mail de boas-vindas', [
                    'email' => $user->email,
                    'tenant' => tenancy()->tenant->id ?? 'N/A'
                ]);
                
                \Illuminate\Support\Facades\Mail::to($user->email)
                    ->send(new \App\Mail\WelcomeUser($user));
                
                \Illuminate\Support\Facades\Log::info('✅ E-mail de boas-vindas enviado', [
                    'email' => $user->email
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('❌ Erro ao enviar e-mail de boas-vindas', [
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ], [
            'current_password.required' => 'Por favor, informe sua senha atual.',
            'current_password.current_password' => 'A senha atual está incorreta. Verifique e tente novamente.',
            'password.required' => 'Por favor, informe uma nova senha.',
            'password.confirmed' => 'A confirmação da senha não confere. Digite novamente.',
        ], [
            'current_password' => 'senha atual',
            'password' => 'nova senha',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}

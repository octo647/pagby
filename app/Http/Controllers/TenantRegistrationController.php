<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact; // Certifique-se de que o modelo Contact está importado

class TenantRegistrationController extends Controller
{
    public function showForm()
    {
        return view('register-tenant');
    }

    public function register(Request $request)
    {
        // Lógica para registrar um novo contato com validação de email único
        $validatedData = $request->validate([
            'owner_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:contacts,email',
            'phone' => 'required|string|max:20',
            'tipo' => 'required|string|max:50',
            'salon_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'neighborhood' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
        ], [
            // Mensagens personalizadas para validação
            'email.unique' => 'Este email já está registrado em nosso sistema. Por favor, use um email diferente ou entre em contato conosco.',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'Por favor, insira um email válido.',
            'owner_name.required' => 'O nome do proprietário é obrigatório.',
            'salon_name.required' => 'O nome do salão é obrigatório.',
            'phone.required' => 'O telefone é obrigatório.',
            'tipo.required' => 'Por favor, selecione o tipo de estabelecimento.',
            'address.required' => 'O endereço é obrigatório.',
            'neighborhood.required' => 'O bairro é obrigatório.',
            'city.required' => 'A cidade é obrigatória.',
            'state.required' => 'O estado é obrigatório.',
        ]);

        try {
            // Aqui você pode adicionar a lógica para salvar os dados no banco de dados
            Contact::create($validatedData);

            // Adiciona um token temporário para proteger a página de sucesso
            session(['registration_completed' => true, 'registration_time' => now()]);

            return redirect()->route('registration-success')
                ->with('success', 'Registro realizado com sucesso!');
                
        } catch (\Illuminate\Database\QueryException $e) {
            // Captura erros de banco de dados como segunda linha de defesa
            if ($e->getCode() == 23000) { // Código para constraint violation
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['email' => 'Este email já está registrado em nosso sistema. Por favor, use um email diferente.']);
            }
            
            // Para outros erros de banco, retorna erro genérico
            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'Ocorreu um erro interno. Por favor, tente novamente mais tarde.']);
                
        } catch (\Exception $e) {
            // Captura qualquer outro erro
            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'Ocorreu um erro inesperado. Por favor, tente novamente.']);
        }
    }

    public function registrationSuccess(Request $request)
    {
        // Verifica se há um registro completado na sessão
        // e se foi feito nos últimos 10 minutos (para evitar acesso direto)
        if (!session()->has('registration_completed') || 
            !session()->has('registration_time') ||
            now()->diffInMinutes(session('registration_time')) > 10) {
            
            return redirect()->route('register-tenant')
                ->with('error', 'Acesso negado. Por favor, registre um salão primeiro.');
        }

        // Remove as variáveis de sessão após exibir a página
        session()->forget(['registration_completed', 'registration_time']);

        return view('registration-success');
    }

}

<?php
namespace App\Http\Controllers;

use App\Models\ContatoDuvida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContatoDuvidaController extends Controller
{
    public function store(Request $request)
    {
        Log::info('Recebido POST /contato-duvida', [
            'ip' => $request->ip(),
            'input' => $request->all(),
        ]);

        try {
            $validated = $request->validate([
                'nome' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'telefone' => 'nullable|string|max:30',
                'mensagem' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Falha de validação no contato de dúvida', $e->errors());
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        $contato = ContatoDuvida::create($validated);

        // Enviar e-mail para o usuário
        try {
            Mail::send('emails.contato_duvida_confirmacao', ['contato' => $contato], function ($message) use ($contato) {
                $message->to($contato->email)
                    ->subject('Recebemos sua dúvida - Pagby');
            });
        } catch (\Exception $e) {
            Log::error('Erro ao enviar e-mail de confirmação para o usuário: ' . $e->getMessage());
        }

        // Enviar e-mail para o suporte
        try {
            Mail::send('emails.contato_duvida_suporte', ['contato' => $contato], function ($message) use ($contato) {
                $message->to('suportepagby@gmail.com')
                    ->subject('Novo contato de dúvida sobre o modelo de negócio');
            });
        } catch (\Exception $e) {
            Log::error('Erro ao enviar e-mail para o suporte: ' . $e->getMessage());
        }

        return response()->json(['success' => true]);
    }
}

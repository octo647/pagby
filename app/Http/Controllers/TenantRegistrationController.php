<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Services\AsaasService;
use Illuminate\Support\Facades\Log;

class TenantRegistrationController extends Controller
{
    public function showForm(Request $request)
    {
        $selectedPlan = $request->get('plan'); // Receber o plano selecionado
        $employeeCount = $request->get('employees'); // Receber número de funcionários
        // Aceitar trial como plano válido
        if ($selectedPlan) {
            session(['selected_plan' => $selectedPlan]);
        }
        if ($employeeCount) {
            session(['selected_employee_count' => $employeeCount]);
        }
        return view('register-tenant', [
            'selectedPlan' => $selectedPlan ?? session('selected_plan'),
            'selectedEmployeeCount' => $employeeCount ?? session('selected_employee_count')
        ]);
    }

    public function register(Request $request)
    {
        // Lógica para registrar um novo contato com validação de email único
        $validatedData = $request->validate([ 
            'owner_name' => 'required|string|max:255|min:3',
            'cpf' => 'nullable|string|max:14',
            'email' => 'required|email|max:255|unique:contacts,email',
            'phone' => 'required|string|min:10|max:15',
            'tipo' => 'required|in:Barbearia,Salão de Beleza,Outro',
            'tenant_name' => 'required|string|max:255|min:2',
            'selected_employee_count' => 'nullable|integer|min:1',
            'cep' => 'required|string|size:9', // 00000-000
            'address' => 'required|string|max:255',
            'number' => 'nullable|string|max:20',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|size:2',
            'selected_plan' => 'required|string|in:mensal,trimestral,semestral,anual',
            'contract_accepted' => 'required|accepted',
        ], [
            // Mensagens personalizadas para validação
            'email.unique' => 'Este email já está registrado em nosso sistema. Por favor, use um email diferente ou entre em contato conosco pelo e-mail <a href="mailto:suporte@pagby.com.br" class="text-blue-500">suporte@pagby.com.br</a> ou pelo WhatsApp: <a href="https://wa.me/5532987007302" class="text-blue-500">(32) 98700-7302</a>.',
            'cpf.required' => 'O CPF é obrigatório.',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'Por favor, insira um email válido.',
            'owner_name.required' => 'O nome do proprietário é obrigatório.',
            'tenant_name.required' => 'O nome do estabelecimento é obrigatório.',
            'employee_count.integer' => 'O número de funcionários deve ser um valor numérico.',
            'employee_count.min' => 'O número de funcionários deve ser pelo menos 1.',
            'phone.required' => 'O telefone é obrigatório.',
            'tipo.required' => 'Por favor, selecione o tipo de estabelecimento.',
            'address.required' => 'O endereço é obrigat&oacute;rio.',
            'neighborhood.required' => 'O bairro é obrigat&oacute;rio.',
            'city.required' => 'A cidade é obrigat&oacute;ria.',
            'state.required' => 'O estado é obrigat&oacute;rio.',
            'owner_name.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'phone.min' => 'O telefone deve ter pelo menos 10 d&iacute;gitos.',
            'cep.size' => 'O CEP deve ter 9 caracteres (00000-000).',
            'state.size' => 'Selecione um estado v&aacute;lido.'
        ]);

        try {
            // Limpar telefone e CEP e CPF para armazenar apenas números
            $validatedData['phone'] = preg_replace('/\D/', '', $validatedData['phone']);
            $validatedData['cep'] = preg_replace('/\D/', '', $validatedData['cep']);
            $validatedData['cpf'] = preg_replace('/\D/', '', $validatedData['cpf']);

            // Salvar data/hora do aceite do contrato
            $validatedData['contract_accepted_at'] = now();

            // Salvar o plano de assinatura no campo subscription_plan
            $validatedData['subscription_plan'] = $validatedData['selected_plan'] ?? null;

            // Salvar o número de funcionários corretamente
            $validatedData['employee_count'] = $validatedData['selected_employee_count'] ?? 1;

            // Criar o contato no banco de dados
            $contact = Contact::create($validatedData);

            // INTEGRAÇÃO ASAAS: criar cliente e salvar customer_id
            $asaas = new AsaasService();
            $customerData = [
                'name' => $contact->owner_name,
                'email' => $contact->email,
                'cpfCnpj' => $contact->cpf,
                'phone' => $contact->phone,
            ];
            $customerId = $asaas->getOrCreateCustomer($customerData);
            if ($customerId) {
                $contact->asaas_customer_id = $customerId;
                $contact->save();
            }

            // Pegar o plano da sessão se existir
            $selectedPlan = $validatedData['selected_plan'] ?? session('selected_plan');

            // Adiciona um token temporário para proteger a página de sucesso
            session([
                'registration_completed' => true, 
                'registration_time' => now(),  
                'contact_id' => $contact->id,
                'selected_plan' => $selectedPlan
            ]);

            Log::info('Registro de contato realizado com sucesso', [
                'contact_id' => $contact->id,
                'email' => $contact->email,
                'selected_plan' => $selectedPlan
            ]);

            return redirect()->route('registration-success')
                ->with('success', 'Registro realizado com sucesso!');
                
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Erro de banco de dados no registro', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'data' => $request->except(['_token'])
            ]);
            
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
            Log::error('Erro geral no registro', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->except(['_token'])
            ]);
            
            // Captura qualquer outro erro
            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'Ocorreu um erro inesperado: ' . $e->getMessage()]);
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

        $contactId = session('contact_id');
        $selectedPlan = session('selected_plan');

        // As variáveis de sessão só serão removidas após o início do pagamento

        return view('registration-success', [
            'contact_id' => $contactId,
            'selected_plan' => $selectedPlan
        ]);
    }
}
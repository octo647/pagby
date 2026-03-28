<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

            // INTEGRAÇÃO ASAAS: Removida do cadastro inicial
            // A integração com Asaas só acontecerá quando o tenant decidir assinar após o trial
            // Isso evita erros de API durante o teste grátis e simplifica o onboarding
            
            // CRIAR TENANT AUTOMATICAMENTE COM STATUS TRIAL
            $tenant = $this->createTrialTenant($contact);

            // Pegar o plano da sessão se existir
            $selectedPlan = $validatedData['selected_plan'] ?? session('selected_plan');

            // Adiciona um token temporário para proteger a página de sucesso
            session([
                'registration_completed' => true, 
                'registration_time' => now(),  
                'contact_id' => $contact->id,
                'contact_email' => $contact->email,
                'selected_plan' => $selectedPlan,
                'tenant_id' => $tenant->id,
                'tenant_domain' => $tenant->domains->first()->domain ?? null
            ]);

            Log::info('Registro de contato realizado com sucesso', [
                'contact_id' => $contact->id,
                'email' => $contact->email,
                'selected_plan' => $selectedPlan,
                'tenant_id' => $tenant->id
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
        $tenantId = session('tenant_id');
        $tenantDomain = session('tenant_domain');

        // As variáveis de sessão só serão removidas após o início do pagamento

        return view('registration-success', [
            'contact_id' => $contactId,
            'selected_plan' => $selectedPlan,
            'tenant_id' => $tenantId,
            'tenant_domain' => $tenantDomain
        ]);
    }

    /**
     * Cria um tenant com período de trial de 30 dias
     */
    private function createTrialTenant(Contact $contact)
    {
        Log::info('🏗️ Criando tenant trial para:', [
            'tenant_name' => $contact->tenant_name,
            'owner_name' => $contact->owner_name,
            'email' => $contact->email
        ]);
        
        try {
            // Criar slug único para o tenant
            $baseSlug = Str::slug($contact->tenant_name);
            $slug = $baseSlug;
            $counter = 1;
            
            while (\App\Models\Tenant::where('id', $slug)->exists()) {
                $slug = $baseSlug . $counter;
                $counter++;
            }
            
            // Criar o tenant com status trial
            $tenant = \App\Models\Tenant::create([
                'id' => $slug,
                'name' => $contact->tenant_name,
                'email' => $contact->email,
                'phone' => $contact->phone,
                'fantasy_name' => $contact->tenant_name,
                'cnpj' => $contact->cpf, // Usando CPF como CNPJ temporário
                'type' => $this->mapContactTypeToTenantType($contact->tipo ?? 'Barbearia'),
                'template' => 'Padrao', // Template padrão para novos tenants
                'subscription_status' => 'trial', // Status trial
                'subscription_plan' => $contact->subscription_plan ?? 'mensal',
                'trial_started_at' => now(),
                'trial_ends_at' => now()->addDays(30), // 30 dias de trial
                'subscription_started_at' => null,
                'subscription_ends_at' => null,
                'employee_count' => $contact->employee_count ?? 1,
                'is_blocked' => false,
                'address' => $contact->address,
                'number' => $contact->number,
                'complement' => $contact->complement,
                'neighborhood' => $contact->neighborhood,
                'cep' => $contact->cep,
                'city' => $contact->city,
                'state' => $contact->state,
            ]);
            
            // Criar domínio para o tenant
            $domainSuffix = config('app.tenant_domain_suffix', '.pagby.com.br');
            $domain = $slug . $domainSuffix;
            $tenant->domains()->create([
                'domain' => $domain
            ]);
            
            // Atualizar o contato com o tenant_id
            $contact->tenant_id = $tenant->id;
            $contact->save();

            Log::info('🔍 Verificando diretórios ANTES de criar', [
                'tenant_id' => $slug,
                'public_exists' => is_dir(public_path("tenants/$slug")),
                'storage_exists' => is_dir(storage_path("tenant$slug")),
                'views_exists' => is_dir(resource_path("views/tenants/$slug")),
            ]);

            // Criar estrutura de diretórios do tenant
            $this->createTenantDirectories($slug);
            
            Log::info('🔍 Verificando diretórios DEPOIS de criar', [
                'tenant_id' => $slug,
                'public_exists' => is_dir(public_path("tenants/$slug")),
                'storage_exists' => is_dir(storage_path("tenant$slug")),
                'views_exists' => is_dir(resource_path("views/tenants/$slug")),
            ]);
            
            // Inicializar tenant e criar usuário owner
            $this->initializeTenantDatabase($tenant, $contact);
            
            Log::info('✅ Tenant trial criado com sucesso!', [
                'tenant_id' => $tenant->id,
                'domain' => $domain,
                'trial_ends_at' => $tenant->trial_ends_at->format('Y-m-d H:i:s')
            ]);
            
            return $tenant;
            
        } catch (\Exception $e) {
            Log::error('❌ Erro ao criar tenant trial:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'contact_id' => $contact->id
            ]);
            throw $e;
        }
    }

    /**
     * Inicializa o banco de dados do tenant e cria o usuário owner
     */
    private function initializeTenantDatabase(\App\Models\Tenant $tenant, Contact $contact)
    {
        tenancy()->initialize($tenant);
        
        try {
            // 1. Criar as roles básicas (deve ser feito antes de criar o usuário)
            Log::info('🌱 Criando roles para tenant', ['tenant_id' => $tenant->id]);
            
            $roles = [
                ['role' => 'Proprietário'],
                ['role' => 'Funcionário'],
                ['role' => 'Cliente'],
            ];

            foreach ($roles as $roleData) {
                \App\Models\Role::firstOrCreate($roleData);
            }
            
            Log::info('✅ Roles criadas', [
                'tenant_id' => $tenant->id,
                'roles_count' => \App\Models\Role::count()
            ]);
            
            // 2. Criar o usuário owner no banco do tenant
            $user = \App\Models\User::create([
                'name' => $contact->owner_name,
                'email' => $contact->email,
                'cpf' => $contact->cpf,
                'phone' => $contact->phone,
                'password' => Hash::make('123456'), // Senha padrão
                'email_verified_at' => now(),
                'status' => 'Ativo',
                'origin' => 'registration',
            ]);

            // 3. Atribuir role de Proprietário
            $ownerRole = \App\Models\Role::where('role', 'Proprietário')->first();
            if ($ownerRole) {
                $user->roles()->attach($ownerRole->id);
                
                Log::info('✅ Role de Proprietário atribuída', [
                    'tenant_id' => $tenant->id,
                    'user_id' => $user->id,
                    'role_id' => $ownerRole->id
                ]);
            } else {
                Log::error('❌ Role Proprietário não encontrada no tenant', [
                    'tenant_id' => $tenant->id,
                    'roles_available' => \App\Models\Role::pluck('role')->toArray()
                ]);
            }

            Log::info('✅ Usuário owner criado no tenant', [
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
                'email' => $user->email
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro ao inicializar banco do tenant:', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        } finally {
            tenancy()->end();
        }
    }

    /**
     * Cria estrutura de diretórios para o tenant
     */
    private function createTenantDirectories(string $tenantId): void
    {
        Log::info('📁 [INICIO] Criando diretórios do tenant', ['tenant_id' => $tenantId]);
        
        try {
            // Diretórios principais
            $directories = [
                public_path("tenants/$tenantId"),
                storage_path("tenant$tenantId"),
                resource_path("views/tenants/$tenantId"),
                storage_path("tenant$tenantId/profile-photos"),
                storage_path("tenant$tenantId/services"),
                storage_path("tenant$tenantId/gallery"),
                storage_path("tenant$tenantId/cache"),
                storage_path("tenant$tenantId/framework/cache"),
                resource_path("views/tenants/$tenantId/partials"),
                resource_path("views/tenants/$tenantId/components"),
            ];

            // Criar todos os diretórios
            foreach ($directories as $directory) {
                if (!is_dir($directory)) {
                    if (!mkdir($directory, 0775, true)) {
                        throw new \Exception("Não foi possível criar o diretório: $directory");
                    }
                    Log::info("✓ Diretório criado", ['directory' => $directory]);
                }
            }

            // Copiar template Padrao (editável) para o tenant
            // Determinar tipo de template baseado no tipo de estabelecimento
            $templateType = match($contact->tipo ?? 'Barbearia') {
                'Barbearia' => 'Barbearias',
                'Salão de Beleza' => 'Salões',
                default => 'Salões'
            };
            
            $templateHome = resource_path("Templates/{$templateType}/Padrao/home.blade.php");
            $tenantHome = resource_path("views/tenants/$tenantId/home.blade.php");

            if (file_exists($templateHome)) {
                if (!copy($templateHome, $tenantHome)) {
                    throw new \Exception("Erro ao copiar template home.blade.php");
                }
                Log::info('✓ Template Padrao copiado', [
                    'tenant_id' => $tenantId,
                    'template_type' => $templateType,
                    'from' => $templateHome
                ]);
            } else {
                Log::warning('⚠ Template Padrao não encontrado', [
                    'path' => $templateHome,
                    'template_type' => $templateType
                ]);
            }

            // Criar symlink de storage
            $storagePath = storage_path("tenant$tenantId/app/public");
            $publicPath = public_path("storage/tenants/$tenantId");

            if (!is_dir($storagePath)) {
                mkdir($storagePath, 0775, true);
            }

            if (!is_dir(dirname($publicPath))) {
                mkdir(dirname($publicPath), 0775, true);
            }

            if (is_link($publicPath)) {
                @unlink($publicPath);
            }

            if (@symlink($storagePath, $publicPath)) {
                Log::info('✓ Symlink de storage criado', ['tenant_id' => $tenantId]);
            } else {
                Log::warning('⚠ Não foi possível criar symlink de storage', ['tenant_id' => $tenantId]);
            }
            
            Log::info('✅ Estrutura de diretórios criada com sucesso', ['tenant_id' => $tenantId]);
            
        } catch (\Exception $e) {
            Log::error('❌ Erro ao criar diretórios do tenant', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Mapeia o tipo de contato para o tipo de tenant
     */
    private function mapContactTypeToTenantType(string $contactType): string
    {
        $map = [
            'Barbearia' => 'barbearia',
            'Salão de Beleza' => 'salao',
            'Outro' => 'outro'
        ];
        
        return $map[$contactType] ?? 'barbearia';
    }
}
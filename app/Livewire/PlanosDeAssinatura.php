<?php

namespace App\Livewire;



use Livewire\Component;
use App\Models\Plan; // Certifique-se de que o modelo Plan está importado 
use App\Models\Service; // Certifique-se de que o modelo Service está importado
use Illuminate\Support\Facades\Auth; // Para verificar o papel do usuário
use App\Models\Branch; // Certifique-se de que o modelo Branch está importado
use App\Models\Subscription; // Certifique-se de que o modelo Subscription está importado
use App\Models\User; // Certifique-se de que o modelo User está importado
use App\Services\AsaasService; // Para criar subconta Asaas


class PlanosDeAssinatura extends Component
    
{
    public $planos = [];

    public $planoSelecionado = null;
    public $modalAberto = false;
    public $openNovoPlano = false;
    public $nomePlano = '';
    public $preco = 0.0;
    public $duracaoDias = 0;
    public $servicosIncluidos = [];
    public $servicosAdicionais = []; 
    public $servicosAdicionaisDescontos = []; // Array para armazenar descontos dos serviços adicionais
    public $todosServicos = [];  
    public $features_keys = [];
    public $features_values = [];
    public $modalNovoPlano = false;
    public $allowedDays = []; // Array para armazenar os dias permitidos
    public $assinaturaAtivaId = null; // ID da assinatura ativa (não o objeto)

    // Propriedades para modal de dados faltantes
    public $modalDadosFaltantes = false;
    public $tipoPessoa = 'juridica'; // 'juridica' ou 'fisica'
    public $cnpj = '';
    public $cpf = '';
    public $dataNascimento = '';
    public $companyType = 'MEI'; // MEI, LIMITED, INDIVIDUAL, ASSOCIATION


    /**
     * Cria subconta Asaas para o tenant e salva o walletId.
     * Chame este método após criar o plano, se o tenant ainda não tiver walletId.
     */
    protected function criarSubcontaAsaasSeNecessario()
    {
        $tenant = tenant();
        if (!$tenant || $tenant->asaas_wallet_id) {
            return;
        }
        // Verifica se a conta Pagby é filha (subconta) e evita criar subconta se for
        $asaasService = new \App\Services\AsaasService();
        if (str_starts_with($asaasService->apiKey ?? '', '$aact_')) { // Exemplo: prefixo de apiKey de subconta
            \Log::warning('Conta Pagby é subconta no Asaas. Não é permitido criar subcontas filhas. Ignorando criação de subconta para o tenant.', [
                'tenant_id' => $tenant->id,
                'apiKey' => $asaasService->apiKey
            ]);
            return;
        }
        // ...existing code para criar subconta se permitido...
    }
    
    // Listener para atualizar descontos quando serviços adicionais mudarem
    public function updatedServicosAdicionais()
    {
        $this->sincronizarDescontos();
    }
    
    // Método público para sincronizar descontos
    public function sincronizarDescontos()
    {
        // Inicializar descontos para novos serviços adicionais
        foreach ($this->servicosAdicionais as $servicoId) {
            if (!isset($this->servicosAdicionaisDescontos[$servicoId])) {
                $this->servicosAdicionaisDescontos[$servicoId] = 0;
            }
        }
        
        // Remover descontos de serviços que não estão mais selecionados
        $this->servicosAdicionaisDescontos = array_intersect_key(
            $this->servicosAdicionaisDescontos, 
            array_flip($this->servicosAdicionais)
        );
    }
    
    public function mount()
    {
        $planos = Plan::with('services', 'additionalServices')->get();    
        $this->todosServicos = \App\Models\Service::all();

        // Defina o ID da assinatura ativa do usuário autenticado (salva apenas o ID, não o objeto)
        $user = Auth::user();
        $tenantId = tenant() ? tenant()->id : null;
        $this->assinaturaAtivaId = null;
        if ($user && $tenantId) {
            $assinatura = \App\Models\TenantsPlansPayment::on('mysql')
                ->where('tenant_id', $tenantId)
                ->where('payer_data', 'like', '%'.$user->email.'%')
                ->whereIn('status', ['authorized', 'active', 'approved', 'RECEIVED'])
                ->latest()
                ->first();
            
            if ($assinatura) {
                $this->assinaturaAtivaId = $assinatura->id;
            }
        }
    

        $this->planos = $planos->map(function ($plano) {
            $additionalServicesWithDiscounts = $plano->additionalServices->map(function ($service) {
                return [
                    'name' => $service->service,
                    'discount' => $service->pivot->discount ?? 0
                ];
            })->toArray();

            return [
                'id' => $plano->id,
                'name' => $plano->name,
                'price' => $plano->price,
                'services' => $plano->services->pluck('service')->toArray(),
                'additional_services' => $plano->additionalServices->pluck('service')->toArray(),
                'additional_services_with_discounts' => $additionalServicesWithDiscounts,
                'duration_days' => $plano->duration_days,
                'features' => $plano->features,
            ];
        })->toArray();
}

    // Método helper para obter a assinatura ativa (quando necessário)
    public function getAssinaturaAtivaProperty()
    {
        if (!$this->assinaturaAtivaId) {
            return null;
        }
        
        return \App\Models\TenantsPlansPayment::on('mysql')->find($this->assinaturaAtivaId);
    }
    
    public function allowedDays()
    {
        // Retorna os dias permitidos para o agendamento
        return $this->allowedDays;

    }
    public function assinarPlano($planoId)
    {
        \Log::debug('INICIO assinarPlano', [
            'user_id' => Auth::id(),
            'planoId' => $planoId,
        ]);
        $plano = Plan::find($planoId);
        $user = Auth::user();
        $tenant = tenant();
        
        if ($plano) {
            \Log::debug('Plano encontrado', ['plano_id' => $plano->id, 'user_id' => $user->id]);

            // Verifica se o usuário já tem um plano ativo
            if (is_object($user) && method_exists($user, 'hasRole') && $user->hasRole('Cliente') && $user->plano_atual) {
                \Log::debug('Usuário já possui plano ativo', ['user_id' => $user->id]);
                session()->flash('message', 'Você já possui um plano ativo.');
                return;
            }

            // VALIDAÇÃO: Verificar se wallet_id é válido antes de criar assinatura
            $walletIdInvalido = false;
            if (!empty($tenant->asaas_wallet_id) && str_starts_with($tenant->asaas_wallet_id, 'cus_')) {
                $walletIdInvalido = true;
                \Log::warning('⚠️ Tentativa de assinar plano com Wallet ID inválido', [
                    'tenant_id' => $tenant->id,
                    'asaas_wallet_id' => $tenant->asaas_wallet_id,
                    'plano_id' => $planoId
                ]);
            }
            
            // Se wallet_id inválido OU não preenchido, NÃO PODE ASSINAR (proprietário precisa configurar)
            if ($walletIdInvalido || empty($tenant->asaas_wallet_id)) {
                \Log::error('❌ Bloqueando assinatura: wallet_id inválido ou não configurado', [
                    'tenant_id' => $tenant->id,
                    'wallet_id' => $tenant->asaas_wallet_id,
                    'user_role' => $user->roles->pluck('name'),
                ]);
                
                session()->flash('error', 'Este salão ainda não está configurado para receber assinaturas. Por favor, entre em contato com o proprietário do salão.');
                return;
            }

            // Verifica se o usuário tem CPF ou CNPJ cadastrado
            $cpfCnpj = $user->cpf ?? $tenant->cnpj ?? '';
            if (empty($cpfCnpj)) {
                session()->flash('warning', 'É necessário ter CPF ou CNPJ cadastrado para assinar um plano. Por favor, atualize seu perfil.');
                return;
            }

            // Dados para assinatura - seleciona domínio correto
            $centralDomains = config('tenancy.central_domains');
            // Filtra localhost e 127.0.0.1 e pega o primeiro domínio de produção
            $centralDomain = collect($centralDomains)
                ->filter(fn($domain) => !in_array($domain, ['localhost', '127.0.0.1']))
                ->first() ?? $centralDomains[0];

            $tenant_id = tenant()->id;
            $urlCentral = "https://{$centralDomain}/tenant-assinatura/store"
                . "?tenant_id={$tenant_id}"
                . "&plan_id={$planoId}"
                . "&user_email=" . urlencode($user->email)
                . "&user_name=" . urlencode($user->name)
                . "&cpf_cnpj=" . urlencode($cpfCnpj);
            \Log::debug('Redirecionando para assinatura', ['url' => $urlCentral]);
            return redirect()->away($urlCentral);
        } else {
            \Log::debug('Plano não encontrado', ['planoId' => $planoId]);
        }
    }
           



     
    
    public function abrirModalNovoPlano()
    {
        // Verificar se o tenant tem os dados necessários para criar subconta Asaas
        $tenant = tenant();
        $user = Auth::user();
        
        if (!$tenant || !$user) {
            session()->flash('error', 'Erro ao identificar o tenant ou usuário.');
            return;
        }

        // Log detalhado para debug
        \Log::info('🔍 VALIDAÇÃO DADOS MODAL', [
            'tenant_id' => $tenant->id,
            'tenant_cnpj' => $tenant->cnpj,
            'tenant_asaas_wallet_id' => $tenant->asaas_wallet_id,
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_cpf' => $user->cpf,
            'user_birthdate' => $user->birthdate,
            'user_birthdate_type' => gettype($user->birthdate),
            'user_birthdate_is_carbon' => $user->birthdate instanceof \Carbon\Carbon,
        ]);

        // Verificar se o wallet_id existente é inválido (Customer ID ao invés de Wallet ID)
        $walletIdInvalido = false;
        if (!empty($tenant->asaas_wallet_id)) {
            // Wallet ID válido é UUID (formato: 2dd7ca51-c51d-410e-b0f5-6fee73aed5c7)
            // Customer ID inválido começa com "cus_"
            if (str_starts_with($tenant->asaas_wallet_id, 'cus_')) {
                $walletIdInvalido = true;
                \Log::warning('⚠️ Wallet ID inválido detectado (Customer ID)', [
                    'tenant_id' => $tenant->id,
                    'asaas_wallet_id' => $tenant->asaas_wallet_id
                ]);
            }
        }

        // Verificar se tem CNPJ (pessoa jurídica) ou CPF (pessoa física) com data de nascimento
        $temCNPJ = !empty($tenant->cnpj) && strlen(preg_replace('/[^0-9]/', '', $tenant->cnpj)) >= 14;
        $temCPF = !empty($user->cpf) && strlen(preg_replace('/[^0-9]/', '', $user->cpf)) >= 11;
        $temDataNascimento = !empty($user->birthdate);
        
        \Log::info('🔍 VERIFICAÇÕES', [
            'temCNPJ' => $temCNPJ,
            'temCPF' => $temCPF,
            'temDataNascimento' => $temDataNascimento,
            'walletIdInvalido' => $walletIdInvalido,
        ]);
        
        // Caso 1: Wallet ID inválido (Customer ID) - precisa recriar subconta
        if ($walletIdInvalido) {
            \Log::info('✋ Modal aberto: Wallet ID inválido (Customer ID) - requer dados para criar subconta válida');
            $this->modalDadosFaltantes = true;
            
            // Pré-preencher campos se existirem
            if ($temCNPJ) {
                $this->tipoPessoa = 'juridica';
                $this->cnpj = $tenant->cnpj;
            } elseif ($temCPF) {
                $this->tipoPessoa = 'fisica';
                $this->cpf = $user->cpf;
                // Pré-preencher data de nascimento se existir
                if ($temDataNascimento) {
                    // Garantir formato Y-m-d para input date HTML
                    $this->dataNascimento = $user->birthdate instanceof \Carbon\Carbon 
                        ? $user->birthdate->format('Y-m-d') 
                        : $user->birthdate;
                }
            }
            
            \Log::info('📋 Campos pré-preenchidos', [
                'tipoPessoa' => $this->tipoPessoa,
                'cnpj' => $this->cnpj ?? 'null',
                'cpf' => $this->cpf ?? 'null',
                'dataNascimento' => $this->dataNascimento ?? 'null',
            ]);
            
            return;
        }
        
        // Caso 2: Não tem CNPJ nem CPF - solicitar dados
        if (!$temCNPJ && !$temCPF) {
            \Log::info('✋ Modal aberto: Sem CNPJ e sem CPF');
            $this->modalDadosFaltantes = true;
            return;
        }
        
        // Caso 3: Tem CPF mas não tem data de nascimento - solicitar data de nascimento
        if ($temCPF && !$temDataNascimento) {
            \Log::info('✋ Modal aberto: Tem CPF mas sem data de nascimento');
            $this->tipoPessoa = 'fisica';
            $this->cpf = $user->cpf;
            $this->modalDadosFaltantes = true;
            return;
        }
        
        // Caso 4: Tem CNPJ mas é necessário validá-lo (pode estar inválido ou incompleto)
        if ($temCNPJ && strlen(preg_replace('/[^0-9]/', '', $tenant->cnpj)) < 14) {
            \Log::info('✋ Modal aberto: CNPJ incompleto');
            $this->tipoPessoa = 'juridica';
            $this->cnpj = $tenant->cnpj;
            $this->modalDadosFaltantes = true;
            return;
        }
        
        // Dados OK, mas verificar se tem wallet_id válido
        \Log::info('✅ Dados OK - Verificando wallet_id');
        
        // Se não tem wallet_id válido, criar subconta Asaas antes de permitir criar plano
        $precisaCriarSubconta = empty($tenant->asaas_wallet_id) || str_starts_with($tenant->asaas_wallet_id, 'cus_');
        
        if ($precisaCriarSubconta) {
            \Log::info('🏗️ Criando subconta Asaas antes de abrir modal de plano');
            $this->criarSubcontaAsaas($tenant, $user);
            
            // Recarregar tenant para verificar se wallet_id foi criado
            $tenant->refresh();
            
            // Se ainda não tem wallet_id válido, bloquear criação de plano
            if (empty($tenant->asaas_wallet_id) || str_starts_with($tenant->asaas_wallet_id, 'cus_')) {
                \Log::error('❌ Não foi possível criar subconta Asaas');
                session()->flash('error', 'Não foi possível configurar a conta de pagamento. Entre em contato com o suporte.');
                return;
            }
            
            \Log::info('✅ Subconta criada com sucesso, prosseguindo para criação de plano');
        }
        
        // Abrir modal de criação de plano
        $this->reset(['nomePlano', 'preco', 'duracaoDias', 'servicosIncluidos', 'servicosAdicionais', 'servicosAdicionaisDescontos', 'features_keys', 'features_values', 'allowedDays']);
        $this->modalNovoPlano = true;
    }

    public function salvarDadosFaltantes()
    {
        $tenant = tenant();
        $user = Auth::user();
        
        if (!$tenant || !$user) {
            session()->flash('error', 'Erro ao identificar o tenant ou usuário.');
            return;
        }

        // Validar dados conforme tipo de pessoa
        if ($this->tipoPessoa === 'juridica') {
            $this->validate([
                'cnpj' => 'required|string|min:14|max:18',
                'companyType' => 'required|in:MEI,LIMITED,INDIVIDUAL,ASSOCIATION'
            ], [
                'cnpj.required' => 'O CNPJ é obrigatório',
                'cnpj.min' => 'O CNPJ deve ter pelo menos 14 dígitos',
                'companyType.required' => 'Tipo de empresa é obrigatório'
            ]);

            // Remover formatação do CNPJ
            $cnpjLimpo = preg_replace('/[^0-9]/', '', $this->cnpj);
            
            // Atualizar tenant
            $tenant->cnpj = $cnpjLimpo;
            $tenant->save();

        } else {
            $this->validate([
                'cpf' => 'required|string|min:11|max:14',
                'dataNascimento' => 'required|date|before:today'
            ], [
                'cpf.required' => 'O CPF é obrigatório',
                'cpf.min' => 'O CPF deve ter pelo menos 11 dígitos',
                'dataNascimento.required' => 'A data de nascimento é obrigatória',
                'dataNascimento.before' => 'A data de nascimento deve ser anterior a hoje'
            ]);

            // Remover formatação do CPF
            $cpfLimpo = preg_replace('/[^0-9]/', '', $this->cpf);
            
            // Atualizar CPF e data de nascimento do usuário proprietário
            $user->cpf = $cpfLimpo;
            $user->birthdate = $this->dataNascimento;
            $user->save();
        }

        // Criar subconta Asaas automaticamente
        $this->criarSubcontaAsaas($tenant, $user);

        // Fechar modal de dados faltantes
        $this->modalDadosFaltantes = false;
        
        // Abrir modal de criação de plano
        $this->reset(['nomePlano', 'preco', 'duracaoDias', 'servicosIncluidos', 'servicosAdicionais', 'servicosAdicionaisDescontos', 'features_keys', 'features_values', 'allowedDays']);
        $this->modalNovoPlano = true;
        
        session()->flash('success', 'Dados salvos com sucesso!');
    }

    private function criarSubcontaAsaas($tenant, $user)
    {
        try {
            \Log::info('🏗️ Iniciando criação de subconta Asaas', [
                'tenant_id' => $tenant->id,
                'user_id' => $user->id
            ]);

            $asaasService = new AsaasService();
            
            // Preparar dados da subconta
            // Usar !empty() pois string vazia não é null
            $cpfCnpj = !empty($tenant->cnpj) ? $tenant->cnpj : $user->cpf;
            
            // Validar CPF/CNPJ antes de enviar
            if (strlen($cpfCnpj) == 11) {
                // Validar CPF
                if (!$this->validarCPF($cpfCnpj)) {
                    \Log::error('❌ CPF inválido detectado antes de enviar ao Asaas', [
                        'cpf' => $cpfCnpj,
                        'tenant_id' => $tenant->id
                    ]);
                    session()->flash('error', 'O CPF cadastrado é inválido. Por favor, corrija o CPF no seu perfil.');
                    return;
                }
            }
            
            $accountData = [
                'name' => $tenant->fantasy_name ?? $tenant->name ?? $tenant->id,
                'email' => !empty($tenant->email) ? $tenant->email : $user->email,
                'cpfCnpj' => $cpfCnpj,
            ];

            // Adicionar telefone se disponível
            if ($tenant->phone) {
                $phone = preg_replace('/[^0-9]/', '', $tenant->phone);
                if (strlen($phone) >= 10) {
                    $ddd = substr($phone, 0, 2);
                    $number = substr($phone, 2);
                    $accountData['mobilePhone'] = $ddd . $number;
                }
            }

            // Adicionar endereço se disponível
            if ($tenant->address && $tenant->city && $tenant->state) {
                $accountData['address'] = $tenant->address;
                $accountData['addressNumber'] = $tenant->number ?? 'S/N';
                $accountData['province'] = $tenant->neighborhood ?? '';
                $accountData['postalCode'] = preg_replace('/[^0-9]/', '', $tenant->cep ?? '');
            }

            // Para CNPJ, adicionar companyType
            if (!empty($tenant->cnpj)) {
                $accountData['companyType'] = $this->companyType ?? 'MEI';
                $accountData['incomeValue'] = 5000;
            } else {
                // Para CPF, adicionar data de nascimento (formato Y-m-d)
                $birthDate = $user->birthdate;
                if ($birthDate instanceof \Carbon\Carbon) {
                    $birthDate = $birthDate->format('Y-m-d');
                }
                $accountData['birthDate'] = $birthDate;
                $accountData['incomeValue'] = 3000;
            }

            \Log::info('📤 Enviando dados para Asaas', ['accountData' => $accountData]);

            $result = $asaasService->criarSubconta($accountData);

            if ($result['success']) {
                $walletId = $result['data']['walletId'];
                
                // Atualizar tenant com wallet_id
                $tenant->asaas_wallet_id = $walletId;
                $tenant->asaas_account_data = json_encode($result['data']);
                $tenant->save();

                \Log::info('✅ Subconta Asaas criada com sucesso', [
                    'tenant_id' => $tenant->id,
                    'wallet_id' => $walletId
                ]);

                session()->flash('success', 'Subconta Asaas criada com sucesso! Wallet ID: ' . $walletId);
            } else {
                \Log::error('❌ Erro ao criar subconta Asaas', [
                    'tenant_id' => $tenant->id,
                    'error' => $result['message']
                ]);
                
                session()->flash('warning', 'Dados salvos, mas houve um problema ao criar a subconta Asaas. Entre em contato com o suporte.');
            }
        } catch (\Exception $e) {
            \Log::error('❌ Exceção ao criar subconta Asaas', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            session()->flash('warning', 'Dados salvos, mas houve um erro ao criar a subconta Asaas. Entre em contato com o suporte.');
        }
    }

    public function salvarNovoPlano()
    {
        \Log::debug('INICIO salvarNovoPlano', [
            'user_id' => Auth::id(),
            'nomePlano' => $this->nomePlano,
            'preco' => $this->preco,
            'duracaoDias' => $this->duracaoDias,
            'servicosIncluidos' => $this->servicosIncluidos,
            'servicosAdicionais' => $this->servicosAdicionais,
        ]);

        // Validar dados do plano
        $this->validate([
            'nomePlano' => 'required|string|max:255',
            'preco' => 'required|numeric|min:0',
            'duracaoDias' => 'required|integer|min:1',
            'servicosIncluidos' => 'array',
            'servicosAdicionais' => 'array',
        ]);
        
        $features = [];
        foreach ($this->features_keys as $i => $key) {
            $key = trim($key);
            if ($key !== '') {
                $features[$key] = $this->features_values[$i] ?? '';
            }
        }

        $plano = Plan::create([
            'name' => $this->nomePlano,
            'price' => $this->preco,
            'duration_days' => $this->duracaoDias,
            'features' => $features,
            'allowed_days' => $this->allowedDays, // Adiciona os dias permitidos
            'created_by' => Auth::id(), // ID do usuário que criou o plano
        ]);
        \Log::debug('Plano criado?', ['plano_id' => $plano->id ?? null]);
        $plano->services()->sync($this->servicosIncluidos);

        // Sincronizar serviços adicionais com descontos
        $servicosAdicionaisComDescontos = [];
        foreach ($this->servicosAdicionais as $servicoId) {
            $servicosAdicionaisComDescontos[$servicoId] = [
                'discount' => $this->servicosAdicionaisDescontos[$servicoId] ?? 0
            ];
        }
        $plano->additionalServices()->sync($servicosAdicionaisComDescontos);

        // Preencher tabela tenants_plans na base central
        $tenantId = tenant() ? tenant()->id : null;
        if ($tenantId) {
            \App\Models\TenantPlan::on('mysql')->create([
                'tenant_id' => $tenantId,
                'plan_id' => $plano->id,  // ID do plano no banco do tenant
                'name' => $this->nomePlano,
                'price' => $this->preco,
                'duration_days' => $this->duracaoDias,
                'active' => true,
            ]);
            
            // Verificar se subconta Asaas existe (já é criada automaticamente via Observer)
            $this->verificarSubcontaAsaas();
        }

        // Atualiza a lista de planos
        $this->modalNovoPlano = false;
        $this->mount();
        \Log::debug('FIM salvarNovoPlano', [
            'user_id' => Auth::id(),
            'plano_id' => $plano->id ?? null,
        ]);
        session()->flash('message', 'Plano criado com sucesso!');
    }
    public function addFeature()
    {
 
        $this->features_keys[] = '';
        $this->features_values[] = '';
    }

    public function removeFeature($index)
    {
        unset($this->features_keys[$index], $this->features_values[$index]);
        $this->features_keys = array_values($this->features_keys);
        $this->features_values = array_values($this->features_values);
    }

    public function editPlan($planoId)
    {
        $this->planoSelecionado = Plan::find($planoId);
        $this->nomePlano = $this->planoSelecionado->name;
        $this->preco = $this->planoSelecionado->price;
        $this->duracaoDias = $this->planoSelecionado->duration_days;
        $this->modalAberto = true;
        // Carregar os serviços incluídos e adicionais
        $this->servicosIncluidos = $this->planoSelecionado->services->pluck('id')->toArray();       
        $this->servicosAdicionais = $this->planoSelecionado->additionalServices->pluck('id')->toArray();
        
        // Carregar descontos dos serviços adicionais
        $this->servicosAdicionaisDescontos = [];
        foreach ($this->planoSelecionado->additionalServices as $servico) {
            $this->servicosAdicionaisDescontos[$servico->id] = $servico->pivot->discount ?? 0;
        }
        
        // Carregar os dias permitidos
        $this->allowedDays = $this->planoSelecionado->allowed_days ?? [];


        // Certifique-se de que 'features' é um array, se não estiver definido
        // Aqui está o ponto importante:
    
        $features = $this->planoSelecionado->features ?? [];
        if (is_array($features)) {
            $this->features_keys = array_keys($features);
            $this->features_values = array_values($features);
        } else {
            $this->features_keys = [];
            $this->features_values = [];
        }
        

    }
    public function updatePlan()
    {
        $this->validate([
            'nomePlano' => 'required|string|max:255',
            'preco' => 'required|numeric|min:0',
            'duracaoDias' => 'required|integer|min:1',
            'servicosIncluidos' => 'array',
            'servicosAdicionais' => 'array',
        ]);

        if ($this->planoSelecionado) {
        
            $this->planoSelecionado->name = $this->nomePlano;
            $this->planoSelecionado->price = $this->preco;
            $this->planoSelecionado->duration_days = $this->duracaoDias;
            $this->planoSelecionado->allowed_days = $this->allowedDays; // Atualiza os dias permitidos
            $features = [];
            foreach ($this->features_keys as $i => $key) {
                $key = trim($key);
                if ($key !== '') {
                    $features[$key] = $this->features_values[$i] ?? '';
                }
            }
         
            $this->planoSelecionado->features = $features;
                
            $this->planoSelecionado->services()->sync($this->servicosIncluidos);
            
            // Sincronizar serviços adicionais com descontos
            $servicosAdicionaisComDescontos = [];
            foreach ($this->servicosAdicionais as $servicoId) {
                $servicosAdicionaisComDescontos[$servicoId] = [
                    'discount' => $this->servicosAdicionaisDescontos[$servicoId] ?? 0
                ];
            }
            $this->planoSelecionado->additionalServices()->sync($servicosAdicionaisComDescontos);

           
            $this->planoSelecionado->save();

            // Atualizar também na tabela tenants_plans do banco central
            $tenantId = tenant() ? tenant()->id : null;
            if ($tenantId) {
                $tenantPlan = \App\Models\TenantPlan::on('mysql')
                    ->where('tenant_id', $tenantId)
                    ->where('plan_id', $this->planoSelecionado->id)
                    ->first();
                
                if ($tenantPlan) {
                    $tenantPlan->update([
                        'name' => $this->nomePlano,
                        'price' => $this->preco,
                        'duration_days' => $this->duracaoDias,
                    ]);
                } else {
                    // Se não existir, criar
                    \App\Models\TenantPlan::on('mysql')->create([
                        'tenant_id' => $tenantId,
                        'plan_id' => $this->planoSelecionado->id,
                        'name' => $this->nomePlano,
                        'price' => $this->preco,
                        'duration_days' => $this->duracaoDias,
                        'active' => true,
                    ]);
                }
            }

            // recarregar os planos para refletir as alterações
            $this->planos = Plan::with('services', 'additionalServices')->get()->map(function ($plano) {
                // Mapear serviços adicionais com seus descontos
                $additionalServicesWithDiscounts = $plano->additionalServices->map(function ($service) {
                    return [
                        'name' => $service->service,
                        'discount' => $service->pivot->discount ?? 0
                    ];
                })->toArray();
                
                return [
                    'id' => $plano->id,
                    'name' => $plano->name,
                    'price' => $plano->price,
                    'services' => $plano->services->pluck('service')->toArray(),                
                    'additional_services' => $plano->additionalServices->pluck('service')->toArray(),
                    'additional_services_with_discounts' => $additionalServicesWithDiscounts,
                    'duration_days' => $plano->duration_days,
                    'features' => $plano->features,
                ];
            })->toArray();

           // $this->dispatchBrowserEvent('planUpdated', ['message' => 'Plano atualizado com sucesso!']);
        }

        $this->modalAberto = false;
        session()->flash('message', 'Plano atualizado com sucesso!');
    }   
    public function deletePlan($planoId)
    {
        $plano = Plan::find($planoId);
        
        if ($plano) {
            // Verifica se existem subscriptions vinculadas ao plano
            $subscriptionCount = Subscription::where('plan_id', $planoId)->count();
            
            if ($subscriptionCount > 0) {
                session()->flash('error', "Não é possível excluir este plano pois existem {$subscriptionCount} cliente(s) com assinatura ativa vinculada a ele.");
                $this->modalAberto = false;
                return;
            }
            
            try {
                // Exclui também o registro na tabela tenants_plans (central)
                $tenantId = tenant('id') ?? (tenant()?->id ?? null);
                if ($tenantId) {
                    \App\Models\TenantPlan::on('mysql')->where('tenant_id', $tenantId)->where('plan_id', $planoId)->delete();
                }
                $plano->delete();
                // Recarregar os planos para refletir a exclusão
                $this->mount();
                
                session()->flash('message', 'Plano excluído com sucesso!');
            } catch (\Exception $e) {
                session()->flash('error', 'Erro ao excluir o plano. Por favor, tente novamente.');
            }
        }
        
        $this->modalAberto = false;
    }
    
    private function validarCPF($cpf)
    {
        // Remove caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        // Verifica se tem 11 dígitos
        if (strlen($cpf) != 11) {
            return false;
        }
        
        // Verifica se todos os dígitos são iguais (CPF inválido)
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        
        // Calcula os dígitos verificadores
        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Verifica se subconta Asaas existe para o tenant
     * Subconta é criada automaticamente via TenantObserver
     */
    private function verificarSubcontaAsaas()
    {
        $tenant = tenant();
        
        if (!$tenant) {
            return;
        }
        
        // Se já tem subconta, está ok
        if ($tenant->asaas_account_id && $tenant->asaas_wallet_id) {
            \Log::info('[PlanosDeAssinatura] Tenant possui subconta Asaas', [
                'tenant_id' => $tenant->id,
                'account_id' => $tenant->asaas_account_id,
                'wallet_id' => $tenant->asaas_wallet_id
            ]);
            return;
        }
        
        // Se não tem, logar aviso
        \Log::warning('[PlanosDeAssinatura] ⚠️ Tenant sem subconta Asaas!', [
            'tenant_id' => $tenant->id,
            'name' => $tenant->name,
            'message' => 'Subconta deve ser criada automaticamente pelo TenantObserver. Verifique se Observer está registrado.'
        ]);
        
        // Opcional: tentar criar manualmente se falhou no observer
        // Descomente apenas se precisar de fallback
        // $this->criarSubcontaAsaas($tenant, auth()->user());
    }
    
    public function render()
    {
        return view('livewire.planos-de-assinatura', [
            'planos' => $this->planos,
            'assinaturaAtiva' => $this->assinaturaAtiva,
            
        ]);
    }
}

<?php

namespace App\Livewire;



use Livewire\Component;
use App\Models\Plan; // Certifique-se de que o modelo Plan está importado 
use App\Models\Service; // Certifique-se de que o modelo Service está importado
use Illuminate\Support\Facades\Auth; // Para verificar o papel do usuário
use App\Models\Branch; // Certifique-se de que o modelo Branch está importado
use App\Models\Subscription; // Certifique-se de que o modelo Subscription está importado
use App\Models\User; // Certifique-se de que o modelo User está importado


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
        if ($plano) {
            \Log::debug('Plano encontrado', ['plano_id' => $plano->id, 'user_id' => $user->id]);
            
            // Verifica se o usuário já tem um plano ativo
            if (is_object($user) && method_exists($user, 'hasRole') && $user->hasRole('Cliente') && $user->plano_atual) {
                \Log::debug('Usuário já possui plano ativo', ['user_id' => $user->id]);
                session()->flash('message', 'Você já possui um plano ativo.');
                return;
            }
            
            // Verifica se o usuário tem CPF ou CNPJ cadastrado
            $cpfCnpj = $user->cpf ?? $user->cnpj ?? '';
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
        $this->reset(['nomePlano', 'preco', 'duracaoDias', 'servicosIncluidos', 'servicosAdicionais', 'servicosAdicionaisDescontos', 'features_keys', 'features_values', 'allowedDays']);
        $this->modalNovoPlano = true;

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
            $plano->delete();
            // Recarregar os planos para refletir a exclusão
            $this->mount();
            
            session()->flash('message', 'Plano excluído com sucesso!');
            
        }
        $this->modalAberto = false; // <-- garanta que o modal está fechado
    }
    
    public function render()
    {
        return view('livewire.planos-de-assinatura', [
            'planos' => $this->planos,
            'assinaturaAtiva' => $this->assinaturaAtiva,
            
        ]);
    }
}

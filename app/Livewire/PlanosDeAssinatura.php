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
        $this->planos = $planos->map(function ($plano) {
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
       
      
    }
    public function allowedDays()
    {
        // Retorna os dias permitidos para o agendamento
        return $this->allowedDays;

    }
    public function assinarPlano($planoId)
    {
        // Auth::setUser(Auth::user()->fresh()); // <-- Removido pois pode causar erro se não for um modelo Eloquent
        $plano = Plan::find($planoId);
        if ($plano) {
            // Verifica se o usuário já tem um plano ativo
            if (Auth::user()->hasRole('Cliente') && Auth::user()->plano_atual) {
                session()->flash('message', 'Você já possui um plano ativo.');
                return;
            }

            // Cria a assinatura
            Auth::user()->subscriptions()->updateOrCreate(
                ['user_id' => Auth::id(), 'plan_id' => $plano->id],
                ['start_date' => now(),
                'end_date' => now()->addDays($plano->duration_days),
                'status' => 'Ativo',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id()]
            );
            // Recarrega o usuário autenticado para atualizar os relacionamentos
            // Auth::setUser(Auth::user()->fresh());
    
            $this->mount();
            // Dispara um evento para recarregar a página
            $this->dispatch('reloadPage');

            session()->flash('message', 'Assinatura realizada com sucesso!');
        }
    }
    public function cancelarAssinatura()
    {
        // Auth::setUser(Auth::user()->fresh()); // <-- Removido pois pode causar erro se não for um modelo Eloquent
        $subscription = Auth::user()->subscriptions()->where('status', 'Ativo')->first();
        if ($subscription) {
            $subscription->status = 'Cancelado';
            $subscription->save();
            session()->flash('message', 'Assinatura cancelada com sucesso!');
        } else {
            session()->flash('message', 'Nenhuma assinatura ativa encontrada.');
        }
        // Auth::setUser(Auth::user()->fresh());
        $this->mount();
        $this->dispatch('reloadPage');
  
    }
    public function abrirModalNovoPlano()
    {
        $this->reset(['nomePlano', 'preco', 'duracaoDias', 'servicosIncluidos', 'servicosAdicionais', 'servicosAdicionaisDescontos', 'features_keys', 'features_values', 'allowedDays']);
        $this->modalNovoPlano = true;

    }
    public function salvarNovoPlano()
    {
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
        $plano->services()->sync($this->servicosIncluidos);
        
        // Sincronizar serviços adicionais com descontos
        $servicosAdicionaisComDescontos = [];
        foreach ($this->servicosAdicionais as $servicoId) {
            $servicosAdicionaisComDescontos[$servicoId] = [
                'discount' => $this->servicosAdicionaisDescontos[$servicoId] ?? 0
            ];
        }
        $plano->additionalServices()->sync($servicosAdicionaisComDescontos);

        // Atualiza a lista de planos
        $this->modalNovoPlano = false;
        $this->mount();
        
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
        return view('livewire.planos-de-assinatura');
    }
}

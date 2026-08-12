<?php
namespace App\Livewire\Proprietario;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Comanda;
use App\Models\ComandaServico;
use App\Models\ComandaProduto;
use App\Models\Branch;
use App\Models\User;
use App\Models\Service;
use App\Models\BranchService;
use App\Models\Estoque;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GerenciarComandas extends Component
{
    public function salvarDescontosPainel()
    {
        $this->validate([
            'desconto_servicos' => 'numeric|min:0',
            'desconto_produtos' => 'numeric|min:0',
        ]);
        if ($this->comanda_detalhes) {
            $comanda = \App\Models\Comanda::find($this->comanda_detalhes->id);
            if ($comanda) {
                $comanda->desconto_servicos = $this->desconto_servicos;
                $comanda->desconto_produtos = $this->desconto_produtos;
                $comanda->save();
                $comanda->recalcularTotais();
                // Atualiza o painel com os novos valores
                $this->comanda_detalhes = $comanda->fresh([
                    'branch',
                    'funcionario',
                    'comandaServicos.service',
                    'comandaServicos.funcionario',
                    'comandaProdutos.estoque',
                    'appointment'
                ]);
                session()->flash('message', 'Descontos atualizados com sucesso!');
            } else {
                session()->flash('error', 'Comanda não encontrada para atualizar descontos.');
            }
        }
    }
    public function selecionarFuncionarioServico($id)
    {
        $this->funcionario_id = $id;
        $this->funcionario_servico_id = $id;
        $this->atualizarServicosFuncionario();
    }
    // Atualiza debug do serviço ao selecionar
    public function atualizarDebugServico()
    {
        \Illuminate\Support\Facades\Log::info('atualizarDebugServico chamado', [
            'service_id' => $this->service_id,
            'branch_id' => $this->branch_id,
        ]);
    }
    public $client_id = '';
    use WithPagination;
    public $servicos_funcionario = [];

    // Atualiza os serviços do funcionário selecionado
    public function atualizarServicosFuncionario()
    {
        \Illuminate\Support\Facades\Log::info('atualizarServicosFuncionario chamado', [
            'funcionario_id' => $this->funcionario_id,
            'branch_id' => $this->branch_id
        ]);
        if ($this->funcionario_id) {
            $user = User::find($this->funcionario_id);
            if ($user) {
                $servicos = $user->services()
                    ->wherePivot('is_active', true)
                    ->get();
                \Illuminate\Support\Facades\Log::info('Serviços do funcionário encontrados', ['servicos' => $servicos->toArray()]);
                $this->servicos_funcionario = $servicos;
                $this->servicosDisponiveis = $servicos;
            } else {
                \Illuminate\Support\Facades\Log::info('Usuário não encontrado');
                $this->servicos_funcionario = collect();
                $this->servicosDisponiveis = collect();
            }
        } else {
            \Illuminate\Support\Facades\Log::info('Funcionário não selecionado');
            $this->servicos_funcionario = collect();
            $this->servicosDisponiveis = collect();
        }
    }
    use WithPagination;
    public $searchTerm = '';
    public $filtro_branch = '';
    public $filtro_status = '';
    public $filtro_funcionario = '';
    public $search = '';
    public $data_inicio = '';
    public $data_fim = '';
    public $branches;
    public $users;
    public $mostrar_painel_detalhes = false;
    public $comanda_detalhes = null;
    public $comanda_painel_id = null; // ID da comanda aberta no painel
    public $mostrandoFormServico = false;
    public $mostrandoFormProduto = false;
    public $mostrar_modal = false;
    public $editando_id = null;
    public $cliente_nome = '';
    public $cliente_telefone = '';
    public $funcionario_id = '';
    public $branch_id = '';
    public $observacoes = '';
    public $desconto_servicos = 0;
    public $desconto_produtos = 0;
    public $mostrar_modal_servico = false;
    public $service_id = '';
    public $funcionario_servico_id = '';
    public $quantidade_servico = 1;
    public $preco_servico = '';
    public $obs_servico = '';
    public $tempo_servico = '';
    public $servicosDisponiveis;
    public $funcionarios_da_filial;
    public $mostrar_modal_produto = false;
    public $estoque_id = '';
    public $quantidade_produto = 1;
    public $preco_produto = '';
    public $obs_produto = '';

    // Atualiza a lista de funcionários da filial selecionada
    public function atualizarFuncionariosFilial()
    {
        if ($this->branch_id) {
            $this->funcionarios_da_filial = User::whereHas('branches', function($q) {
                $q->where('branch_id', $this->branch_id);
            })->whereHas('roles', function($query) {
                $query->whereIn('role', ['Funcionário', 'Proprietário']);
            })->get();
        } else {
            $this->funcionarios_da_filial = collect();
        }
    }

    public function escolherCliente($nome, $telefone)
    {
        $this->cliente_nome = $nome;
        $this->cliente_telefone = $telefone;
        $this->searchTerm = '';
    }

    // ID da comanda sendo editada para adicionar itens
    public $comanda_atual_id = null;

    protected $rules = [
        'cliente_nome' => 'required|string|max:255',
        'cliente_telefone' => 'nullable|string|max:20',
        'funcionario_id' => 'required|exists:users,id',
        'branch_id' => 'required|exists:branches,id',
        'observacoes' => 'nullable|string',
        'desconto_servicos' => 'numeric|min:0',
        'desconto_produtos' => 'numeric|min:0',
    ];

    public function mount()
    {
        $this->loadFilterData();
        $this->servicosDisponiveis = collect();
        $this->funcionarios_da_filial = collect();
    }

    public function loadFilterData()
    {
        // Carregar dados para os filtros
        $this->branches = Branch::all();
        $this->users = User::whereHas('roles', function($query) {
            $query->whereIn('role', ['Funcionário', 'Proprietário']);
        })->get();
    }

    public function render()
    {
        $salon_users = [];
        $query = Comanda::with(['branch', 'funcionario', 'comandaServicos.service', 'comandaServicos.funcionario', 'comandaProdutos.estoque', 'appointment']);

        // Aplicar filtros
        if ($this->filtro_branch) {
            $query->where('branch_id', $this->filtro_branch);
        }
        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->where('cliente_nome', 'like', '%'.$this->searchTerm.'%')
                  ->orWhere('cliente_telefone', 'like', '%'.$this->searchTerm.'%');
            });
        }

        if ($this->filtro_status) {
            $query->where('status', $this->filtro_status);
        }

        if ($this->filtro_funcionario) {
            $query->where('funcionario_id', $this->filtro_funcionario);
        }

        if ($this->data_inicio) {
            $query->where(function($q) {
                $q->whereHas('appointment', function($sub) {
                    $sub->whereDate('appointment_date', '>=', $this->data_inicio);
                })
                ->orWhere(function($sub) {
                    $sub->whereNull('appointment_id')
                        ->whereDate('data_abertura', '>=', $this->data_inicio);
                });
            });
        }

        if ($this->data_fim) {
            $query->where(function($q) {
                $q->whereHas('appointment', function($sub) {
                    $sub->whereDate('appointment_date', '<=', $this->data_fim);
                })
                ->orWhere(function($sub) {
                    $sub->whereNull('appointment_id')
                        ->whereDate('data_abertura', '<=', $this->data_fim);
                });
            });
        }

        $comandas = $query->orderBy('data_abertura', 'desc')->paginate(15);

        // Se o painel estiver aberto, filtrar por filial da comanda
        if ($this->mostrar_painel_detalhes && $this->comanda_detalhes) {
            $branchId = $this->comanda_detalhes->branch_id;
            // Buscar apenas serviços disponíveis na filial da comanda
            $branchServices = BranchService::where('branch_id', $branchId)
                ->where('is_active', true)
                ->with('service')
                ->get();
            // Filtrar funcionários apenas da filial da comanda
            $users = User::whereHas('branches', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })->get();
            // Filtrar produtos apenas da filial da comanda
            $produtos_estoque = Estoque::with('branch')
                ->where('branch_id', $branchId)
                ->where('quantidade_atual', '>', 0)
                ->get();
        } else {
            // Caso padrão - todos os registros
            $branchServices = collect(); // Vazio quando não há painel aberto
            if ($this->branch_id) {
                $users = User::whereHas('branches', function($q) {
                    $q->where('branch_id', $this->branch_id);
                })->get();
            } else {
                $users = $this->users ?? User::all();
            }
            $produtos_estoque = Estoque::with('branch')->where('quantidade_atual', '>', 0)->get();
        }
         if($this->searchTerm!==''){
        $salon_users = User::query()
            ->when($this->searchTerm, function($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%'.$this->searchTerm.'%')
                      ->orWhere('email', 'like', '%'.$this->searchTerm.'%');
                });
            })
            ->orderBy('name', 'asc')
            ->with('roles')
            ->paginate(5);
    // $this->salon_users = $salon_users; // Removido para evitar erro de propriedade indefinida
        
      }

        $clientes = User::whereHas('roles', function($query) {
            $query->where('role', 'Cliente');
        })->get();
        return view('livewire.proprietario.gerenciar-comandas', [
            'comandas' => $comandas,
            'branchServices' => $branchServices,
            'users' => $users,
            'clientes' => $clientes,
            'produtos_estoque' => $produtos_estoque,
            'funcionarios_da_filial' => $this->funcionarios_da_filial ?? collect(),
            'salon_users' => $salon_users,
            'servicos_funcionario' => $this->servicos_funcionario ?? [],
        ]);
    }

    public function abrirModal($id = null)
    {
        $this->resetValidation();
        
        if ($id) {
            $comanda = Comanda::find($id);
            $this->editando_id = $id;
            $this->cliente_nome = $comanda->cliente_nome;
            $this->cliente_telefone = $comanda->cliente_telefone;
            $this->funcionario_id = $comanda->funcionario_id;
            $this->branch_id = $comanda->branch_id;
            $this->observacoes = $comanda->observacoes;
            $this->desconto_servicos = $comanda->desconto_servicos;
            $this->desconto_produtos = $comanda->desconto_produtos;
        } else {
            $this->resetForm();
        }

        $this->mostrar_modal = true;
    }



    public function fecharModal()
    {
        $this->mostrar_modal = false;
        $this->resetForm();
    }

    public function abrirPainelDetalhes($comandaId)
    {
        try {
            $this->comanda_detalhes = Comanda::with([
                'branch', 
                'funcionario', 
                'comandaServicos.service', 
                'comandaServicos.funcionario',
                'comandaProdutos.estoque',
                'appointment'
            ])->find($comandaId);
            
            if (!$this->comanda_detalhes) {
                session()->flash('error', 'Comanda não encontrada!');
                return;
            }
            
            // Limpar formulários anteriores
            $this->service_id = '';
            $this->funcionario_servico_id = '';
            $this->quantidade_servico = 1;
            $this->preco_servico = '';
            $this->obs_servico = '';
            $this->tempo_servico = '';
            $this->servicosDisponiveis = collect();
            
            // Filtrar funcionários apenas da filial da comanda
            $this->funcionarios_da_filial = User::whereHas('branches', function($q) {
                $q->where('branch_id', $this->comanda_detalhes->branch_id);
            })->whereHas('roles', function($query) {
                $query->whereIn('role', ['Funcionário', 'Proprietário']);
            })->get();
            
            $this->mostrar_painel_detalhes = true;
            $this->comanda_atual_id = $comandaId;
            $this->comanda_painel_id = $comandaId; // Salvar ID do painel
            
            session()->flash('message', 'Painel de detalhes aberto');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao abrir painel: ' . $e->getMessage());
        }
    }

    public function fecharPainelDetalhes()
    {
        $this->mostrar_painel_detalhes = false;
        $this->comanda_detalhes = null;
        $this->comanda_atual_id = null;
        $this->comanda_painel_id = null;
        $this->mostrandoFormServico = false;
        $this->mostrandoFormProduto = false;
        
        // Limpar dados do formulário de serviços
        $this->service_id = '';
        $this->funcionario_servico_id = '';
        $this->quantidade_servico = 1;
        $this->preco_servico = '';
        $this->obs_servico = '';
        $this->tempo_servico = '';
        $this->servicosDisponiveis = collect();
        $this->funcionarios_da_filial = collect();
    }

    public function resetForm()
    {
        $this->editando_id = null;
        $this->cliente_nome = '';
        $this->cliente_telefone = '';
        $this->funcionario_id = '';
        $this->observacoes = '';
        $this->desconto_servicos = 0;
        $this->desconto_produtos = 0;
        // NÃO resetar $service_id aqui para manter o valor selecionado
        $user = Auth::user();
        $isProprietario = $user->roles->contains('name', 'Proprietario');
        if (!$isProprietario) {
            $this->branch_id = $user->branches->first()->id ?? '';
        }
    }

    public function salvar()
    {
        $this->validate();

        try {
            // DEBUG: Mostrar preço do serviço antes de adicionar
            if ($this->service_id && $this->branch_id && $this->funcionario_servico_id) {
                $service = \App\Models\Service::find($this->service_id);
                $user = \App\Models\User::find($this->funcionario_servico_id);
                $pivot = $user ? $user->services()->where('service_id', $this->service_id)->first() : null;
                $pivotData = $pivot && $pivot->pivot ? $pivot->pivot->toArray() : [];
                $precoFuncionario = null;
                if ($pivot && $pivot->pivot) {
                    if (isset($pivot->pivot->custom_price) && $pivot->pivot->custom_price > 0) {
                        $precoFuncionario = $pivot->pivot->custom_price;
                    } elseif (isset($pivot->pivot->price) && $pivot->pivot->price > 0) {
                        $precoFuncionario = $pivot->pivot->price;
                    }
                }
                $precoBranch = $service ? $service->getPriceForBranch($this->branch_id) : null;
                $precoFinal = $precoFuncionario ?? $precoBranch ?? ($service ? $service->price : 0);
                \Illuminate\Support\Facades\Log::info('DEBUG - Preço do serviço antes de adicionar à comanda', [
                    'service_id' => $this->service_id,
                    'branch_id' => $this->branch_id,
                    'funcionario_id' => $this->funcionario_servico_id,
                    'pivot' => $pivotData,
                    'preco_funcionario' => $precoFuncionario,
                    'preco_branch' => $precoBranch,
                    'preco_padrao' => $service ? $service->price : 0,
                    'preco_final' => $precoFinal
                ]);
            }
            if ($this->editando_id) {
                // Editar comanda existente
                $comanda = Comanda::find($this->editando_id);
                $comanda->update([
                    'cliente_nome' => $this->cliente_nome,
                    'cliente_telefone' => $this->cliente_telefone,
                    'funcionario_id' => $this->funcionario_id,
                    'branch_id' => $this->branch_id,
                    'observacoes' => $this->observacoes,
                    'desconto_servicos' => $this->desconto_servicos,
                    'desconto_produtos' => $this->desconto_produtos,
                ]);
                $comanda->recalcularTotais();
                session()->flash('message', 'Comanda atualizada com sucesso!');
            } else {
                // Criar nova comanda
                $numeroComanda = Comanda::gerarNumeroComanda($this->branch_id);
                $comanda = Comanda::create([
                    'branch_id' => $this->branch_id,
                    'numero_comanda' => $numeroComanda,
                    'cliente_nome' => $this->cliente_nome,
                    'cliente_telefone' => $this->cliente_telefone,
                    'funcionario_id' => $this->funcionario_id,
                    'observacoes' => $this->observacoes,
                    'desconto_servicos' => $this->desconto_servicos,
                    'desconto_produtos' => $this->desconto_produtos,
                    'client_id' => $this->client_id,
                ]);
                // Se serviço foi selecionado, adiciona à comanda
                \Illuminate\Support\Facades\Log::info('Dados recebidos ao salvar comanda', [
                    'service_id' => $this->service_id,
                    'funcionario_servico_id' => $this->funcionario_servico_id,
                    'branch_id' => $this->branch_id,
                    'quantidade_servico' => $this->quantidade_servico,
                    'obs_servico' => $this->obs_servico
                ]);
                    if ($comanda && $this->service_id) {
                        if (empty($this->funcionario_servico_id)) {
                            session()->flash('error', 'Selecione o funcionário do serviço para adicionar à comanda.');
                        } else {
                    // Buscar preço do serviço considerando a filial
                    $service = \App\Models\Service::find($this->service_id);
                    $user = \App\Models\User::find($this->funcionario_servico_id);
                    $pivot = $user ? $user->services()->where('service_id', $this->service_id)->first() : null;
                    $pivotData = $pivot && $pivot->pivot ? $pivot->pivot->toArray() : [];
                    $precoFuncionario = null;
                    if ($pivot && $pivot->pivot) {
                        if (isset($pivot->pivot->custom_price) && $pivot->pivot->custom_price > 0) {
                            $precoFuncionario = $pivot->pivot->custom_price;
                        } elseif (isset($pivot->pivot->price) && $pivot->pivot->price > 0) {
                            $precoFuncionario = $pivot->pivot->price;
                        }
                    }
                    $precoBranch = $service ? $service->getPriceForBranch($this->branch_id) : null;
                    $precoFinal = $precoFuncionario ?? $precoBranch ?? ($service ? $service->price : 0);
                    \Illuminate\Support\Facades\Log::info('Preço do serviço ao salvar comanda', [
                        'service_id' => $this->service_id,
                        'branch_id' => $this->branch_id,
                        'funcionario_id' => $this->funcionario_servico_id,
                        'pivot' => $pivotData,
                        'preco_funcionario' => $precoFuncionario,
                        'preco_branch' => $precoBranch,
                        'preco_padrao' => $service ? $service->price : 0,
                        'preco_final' => $precoFinal
                    ]);
                    $comanda->adicionarServico(
                        $this->service_id,
                        $this->funcionario_servico_id,
                        $this->quantidade_servico ?? 1,
                        $precoFinal,
                        $this->obs_servico ?? null
                    );
                        }
                }
                session()->flash('message', 'Comanda criada com sucesso!');
            }
            $this->fecharModal();
            $this->service_id = '';
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao salvar comanda: ' . $e->getMessage());
        }
    }

    public function abrirModalServico($comandaId)
    {
        // Debug: verificar o ID recebido
        if (!$comandaId) {
            session()->flash('error', 'ID da comanda não foi passado para o modal!');
            return;
        }
        
        // Carregar comanda para obter a filial
        $comanda = Comanda::find($comandaId);
        if (!$comanda) {
            session()->flash('error', 'Comanda não encontrada!');
            return;
        }
        
        $this->comanda_atual_id = $comandaId;
        $this->service_id = '';
        $this->funcionario_servico_id = '';
        $this->quantidade_servico = 1;
        $this->preco_servico = '';
        $this->obs_servico = '';
        $this->tempo_servico = '';
        $this->servicosDisponiveis = collect();
        
        // Filtrar funcionários apenas da filial da comanda
        $this->funcionarios_da_filial = User::whereHas('branches', function($q) use ($comanda) {
            $q->where('branch_id', $comanda->branch_id);
        })->whereHas('roles', function($query) {
            $query->whereIn('role', ['Funcionário', 'Proprietário']);
        })->get();
        
        $this->mostrar_modal_servico = true;
        
        // Debug: confirmar que foi definido
        session()->flash('message', 'Modal aberto para comanda ID: ' . $this->comanda_atual_id);
            // Garante que os serviços do funcionário sejam atualizados ao abrir o modal
            $this->atualizarServicosFuncionario();
    }

    public function fecharModalServico()
    {
        $this->mostrar_modal_servico = false;
        $this->comanda_atual_id = null;
        $this->service_id = '';
        $this->funcionario_servico_id = '';
        $this->quantidade_servico = 1;
        $this->preco_servico = '';
        $this->obs_servico = '';
        $this->tempo_servico = '';
        $this->servicosDisponiveis = collect();
        $this->funcionarios_da_filial = collect();
    }

    public function atualizarFuncionarioServico()
    {
        // Limpar serviço selecionado quando funcionário muda
        $this->service_id = '';
        $this->preco_servico = '';
        $this->tempo_servico = '';
        
        // Atualizar lista de serviços disponíveis baseado no funcionário
        $this->atualizarServicosDisponiveis();
    }
    
    protected function atualizarServicosDisponiveis()
    {
        if ($this->funcionario_servico_id && $this->comanda_detalhes) {
            // Primeiro, tentar carregar serviços da filial (configuração específica)
            $branchServices = BranchService::where('branch_id', $this->comanda_detalhes->branch_id)
                ->where('is_active', true)
                ->whereHas('service.users', function($query) {
                    $query->where('user_id', $this->funcionario_servico_id)
                          ->where('is_active', true);
                })
                ->with('service')
                ->get();

            // Se não há configuração específica da filial, usar serviços padrão
            if ($branchServices->isEmpty()) {
                // Buscar serviços que o funcionário pode executar (sem filtro de filial)
                $servicosDoFuncionario = Service::whereHas('users', function($query) {
                        $query->where('user_id', $this->funcionario_servico_id)
                              ->where('is_active', true);
                    })
                    ->get();

                // Criar objetos simulando BranchService para manter compatibilidade
                $this->servicosDisponiveis = $servicosDoFuncionario->map(function($service) {
                    return (object) [
                        'service_id' => $service->id,
                        'service' => $service,
                        'service_name' => $service->service, // Para compatibilidade com view
                        'price' => $service->price,
                        'duration_minutes' => $service->time,
                        'formatted_price' => 'R$ ' . number_format($service->price, 2, ',', '.'),
                        'formatted_duration' => $service->time . ' min'
                    ];
                });
            } else {
                $this->servicosDisponiveis = $branchServices;
            }
        } else {
            $this->servicosDisponiveis = collect();
        }
    }

    public function atualizarPrecoServico()
    {
        if ($this->service_id && $this->funcionario_servico_id && $this->comanda_detalhes) {
            // Primeiro, tentar buscar configuração específica da filial (BranchService)
            $branchService = BranchService::where('branch_id', $this->comanda_detalhes->branch_id)
                ->where('service_id', $this->service_id)
                ->first();
                
            if ($branchService) {
                // Verificar se funcionário tem tempo personalizado para este serviço
                $funcionario = User::find($this->funcionario_servico_id);
                $tempoPersonalizado = $funcionario->services()
                    ->where('service_id', $this->service_id)
                    ->first();
                
                $this->preco_servico = $branchService->price;
                
                if ($tempoPersonalizado && $tempoPersonalizado->pivot->custom_duration_minutes) {
                    $this->tempo_servico = $tempoPersonalizado->pivot->custom_duration_minutes;
                } else {
                    $this->tempo_servico = $branchService->duration_minutes;
                }
            } else {
                // Fallback: usar dados padrão do serviço
                $service = Service::find($this->service_id);
                
                if ($service) {
                    // Verificar se funcionário tem tempo personalizado para este serviço
                    $funcionario = User::find($this->funcionario_servico_id);
                    $tempoPersonalizado = $funcionario->services()
                        ->where('service_id', $this->service_id)
                        ->first();
                    
                    $this->preco_servico = $service->price;
                    
                    if ($tempoPersonalizado && $tempoPersonalizado->pivot->custom_duration_minutes) {
                        $this->tempo_servico = $tempoPersonalizado->pivot->custom_duration_minutes;
                    } else {
                        $this->tempo_servico = $service->time;
                    }
                }
            }
        }
    }

    public function adicionarServico()
    {
        $this->validate([
            'service_id' => 'required|exists:services,id',
            'funcionario_servico_id' => 'required|exists:users,id',
            'quantidade_servico' => 'required|integer|min:1',
            'preco_servico' => 'required|numeric|min:0',
        ]);
        //verifica se o serviço está incluído em um plano de assinatura do cliente
        // inicialmente verifica se o cliente tem um plano de assinatura ativo
        $cliente = User::find($this->cliente_id);
        

        try {
            // Usar o ID da comanda em ordem de prioridade
            $comandaId = $this->comanda_atual_id ?? $this->comanda_painel_id;
            
            // Debug: verificar o ID da comanda
            if (!$comandaId) {
                session()->flash('error', 'ID da comanda não definido! Atual: ' . ($this->comanda_atual_id ?? 'null') . ' | Painel: ' . ($this->comanda_painel_id ?? 'null'));
                return;
            }

            $comanda = Comanda::find($comandaId);
            
            if (!$comanda) {
                session()->flash('error', 'Comanda não encontrada! ID: ' . $comandaId);
                return;
            }

            $comanda->adicionarServico(
                $this->service_id,
                $this->funcionario_servico_id,
                $this->quantidade_servico,
                $this->preco_servico,
                $this->obs_servico
            );

            session()->flash('message', 'Serviço adicionado com sucesso!');
            $this->fecharModalServico();
            
            // Recarregar detalhes se o painel estiver aberto
            if ($this->mostrar_painel_detalhes && $this->comanda_detalhes && $this->comanda_detalhes->id == $comandaId) {
                $this->abrirPainelDetalhes($comandaId);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao adicionar serviço: ' . $e->getMessage());
        }
    }

    public function adicionarServicoPainel()
    {
        $this->validate([
            'service_id' => 'required|exists:services,id',
            'funcionario_servico_id' => 'required|exists:users,id',
            'quantidade_servico' => 'required|integer|min:1',
            'preco_servico' => 'required|numeric|min:0',
        ]);

        try {
            // Usar diretamente o ID da comanda do painel
            $comandaId = $this->comanda_detalhes->id;
            
            $comanda = Comanda::find($comandaId);
            
            if (!$comanda) {
                session()->flash('error', 'Comanda não encontrada!');
                return;
            }

            $comanda->adicionarServico(
                $this->service_id,
                $this->funcionario_servico_id,
                $this->quantidade_servico,
                $this->preco_servico,
                $this->obs_servico
            );

            session()->flash('message', 'Serviço adicionado com sucesso!');
            
            // Limpar formulário
            $this->service_id = '';
            $this->funcionario_servico_id = '';
            $this->quantidade_servico = 1;
            $this->preco_servico = '';
            $this->obs_servico = '';
            $this->tempo_servico = '';
            $this->servicosDisponiveis = collect();
            $this->mostrandoFormServico = false;
            
            // Recarregar detalhes do painel
            $this->abrirPainelDetalhes($comandaId);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao adicionar serviço: ' . $e->getMessage());
        }
    }

    public function abrirModalProduto($comandaId)
    {
        // Debug: verificar o ID recebido
        if (!$comandaId) {
            session()->flash('error', 'ID da comanda não foi passado para o modal!');
            return;
        }
        
        $this->comanda_atual_id = $comandaId;
        $this->estoque_id = '';
        $this->quantidade_produto = 1;
        $this->preco_produto = '';
        $this->obs_produto = '';
        $this->mostrar_modal_produto = true;
        
        // Debug: confirmar que foi definido
        session()->flash('message', 'Modal produto aberto para comanda ID: ' . $this->comanda_atual_id);
    }

    public function fecharModalProduto()
    {
        $this->mostrar_modal_produto = false;
        $this->comanda_atual_id = null;
    }

    public function atualizarPrecoProduto()
    {
        // ...existing code...
        // Removido bloco catch fora de contexto e corrigido fechamento do método
    }

    public function adicionarProdutoPainel()
    {
        $this->validate([
            'estoque_id' => 'required|exists:estoque,id',
            'quantidade_produto' => 'required|integer|min:1',
            'preco_produto' => 'required|numeric|min:0',
        ]);

        try {
            // Usar diretamente o ID da comanda do painel
            $comandaId = $this->comanda_detalhes->id;
            
            $comanda = Comanda::find($comandaId);
            
            if (!$comanda) {
                session()->flash('error', 'Comanda não encontrada!');
                return;
            }

            $comanda->adicionarProduto(
                $this->estoque_id,
                $this->quantidade_produto,
                $this->preco_produto,
                $this->obs_produto
            );

            session()->flash('message', 'Produto adicionado com sucesso!');
            
            // Limpar formulário
            $this->estoque_id = '';
            $this->quantidade_produto = 1;
            $this->preco_produto = '';
            $this->obs_produto = '';
            $this->mostrandoFormProduto = false;
            
            // Recarregar detalhes do painel
            $this->abrirPainelDetalhes($comandaId);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao adicionar produto: ' . $e->getMessage());
        }
    }

    public function finalizarComanda($id)
    {
        try {
            $comanda = Comanda::find($id);
            $comanda->finalizar();
            session()->flash('message', 'Comanda finalizada com sucesso!');
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao finalizar comanda: ' . $e->getMessage());
        }
    }

    public function cancelarComanda($id)
    {
        try {
            $comanda = Comanda::find($id);
            $comanda->cancelar();
            session()->flash('message', 'Comanda cancelada com sucesso!');
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao cancelar comanda: ' . $e->getMessage());
        }
    }

    public function excluirServico($servicoId)
    {
        try {
            $comandaServico = ComandaServico::find($servicoId);
            $comanda = $comandaServico->comanda;
            $comandaServico->delete();
            $comanda->recalcularTotais();
            session()->flash('message', 'Serviço removido com sucesso!');
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao remover serviço: ' . $e->getMessage());
        }
    }

    public function excluirProduto($produtoId)
    {
        try {
            $comandaProduto = ComandaProduto::find($produtoId);
            $comanda = $comandaProduto->comanda;
            $comandaProduto->delete();
            $comanda->recalcularTotais();
            session()->flash('message', 'Produto removido com sucesso!');
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao remover produto: ' . $e->getMessage());
        }
    }

    public function limparFiltros()
    {
        $this->filtro_branch = '';
        $this->filtro_status = '';
        $this->filtro_funcionario = '';
        $this->search = '';
        $this->data_inicio = today()->format('Y-m-d');
        $this->data_fim = today()->format('Y-m-d');
        $this->resetPage();
    }

    public function updatedFiltroBranch()
    {
        $this->resetPage();
    }

    public function updatedFiltroStatus()
    {
        $this->resetPage();
    }

    public function updatedFiltroFuncionario()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
    // Não resetar a página, apenas atualizar os resultados
    // Se quiser resetar, pode usar $this->gotoPage(1); para garantir que sempre mostra a primeira página
    $this->gotoPage(1);
    }

    public function updatedDataInicio($value)
    {
        $this->data_inicio = \Carbon\Carbon::parse($value)->format('Y-m-d');
        $this->resetPage();
    }

    public function updatedDataFim($value)
    {
        $this->data_fim = \Carbon\Carbon::parse($value)->format('Y-m-d');
        $this->resetPage();
    }


}
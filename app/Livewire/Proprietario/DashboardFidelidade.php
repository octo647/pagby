<?php

namespace App\Livewire\Proprietario;

use App\Models\Branch;
use App\Models\Estoque;
use App\Models\FidelidadeReward;
use App\Models\Service;
use App\Models\ComandaProduto;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class DashboardFidelidade extends Component
{
    use WithPagination;

    public $branch_id;
    public $periodo = '30'; // dias
    public $abaSelecionada = 'visao-geral';
    
    // Controle de modals
    public $modalVinculo = false;
    public $serviceId;
    public $estoqueId;
    public $prioridade = 1;
    public $percentualDesconto = 0;
    public $observacoes;

    protected $queryString = ['abaSelecionada'];

    public function mount()
    {
        try {
            \Log::info('DashboardFidelidade mount() iniciado');
            // Pegar primeira filial se não especificado
            if (!$this->branch_id) {
                $this->branch_id = Branch::first()?->id ?? 1;
                \Log::info('Branch ID definido: ' . $this->branch_id);
            }
        } catch (\Exception $e) {
            \Log::error('Erro no mount(): ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            $this->branch_id = 1;
        }
    }

    public function abrirModalVinculo($serviceId = null)
    {
        $this->serviceId = $serviceId;
        $this->estoqueId = null;
        $this->prioridade = 1;
        $this->percentualDesconto = 0;
        $this->observacoes = '';
        $this->modalVinculo = true;
    }

    public function vincularProduto()
    {
        $this->validate([
            'serviceId' => 'required|exists:services,id',
            'estoqueId' => 'required|exists:estoque,id',
            'prioridade' => 'required|integer|min:1',
            'percentualDesconto' => 'nullable|numeric|min:0|max:100',
        ]);

        $service = Service::find($this->serviceId);
        
        // Verificar se já existe vínculo
        if ($service->produtosRecomendados()->where('estoque_id', $this->estoqueId)->exists()) {
            session()->flash('error', 'Este produto já está vinculado a este serviço.');
            return;
        }

        $service->produtosRecomendados()->attach($this->estoqueId, [
            'priority' => $this->prioridade,
            'discount_percentage' => $this->percentualDesconto ?: null,
            'is_active' => true,
            'observacoes' => $this->observacoes,
        ]);

        session()->flash('message', 'Produto vinculado com sucesso!');
        $this->modalVinculo = false;
        $this->reset(['serviceId', 'estoqueId', 'prioridade', 'percentualDesconto', 'observacoes']);
    }

    public function removerVinculo($serviceId, $estoqueId)
    {
        $service = Service::find($serviceId);
        $service->produtosRecomendados()->detach($estoqueId);
        
        session()->flash('message', 'Vínculo removido com sucesso!');
    }

    public function toggleAtivo($serviceId, $estoqueId)
    {
        $service = Service::find($serviceId);
        $vinculo = DB::table('service_estoque')
            ->where('service_id', $serviceId)
            ->where('estoque_id', $estoqueId)
            ->first();

        DB::table('service_estoque')
            ->where('service_id', $serviceId)
            ->where('estoque_id', $estoqueId)
            ->update(['is_active' => !$vinculo->is_active]);

        session()->flash('message', 'Status atualizado!');
    }

    public function getEstatisticasProperty()
    {
        try {
            $dataInicio = now()->subDays($this->periodo);

            return [
                'total_recompensas_ativas' => FidelidadeReward::where('status', 'ativo')
                    ->where('validade', '>=', now())
                    ->where('valor_credito', '>', 0) // Excluir recompensas com valor zero
                    ->count(),
                
                'valor_total_creditos' => FidelidadeReward::where('status', 'ativo')
                    ->where('tipo', 'credito')
                    ->where('validade', '>=', now())
                    ->where('valor_credito', '>', 0) // Excluir recompensas com valor zero
                    ->sum('valor_credito') ?? 0,
                
                'vendas_produtos_periodo' => ComandaProduto::whereHas('comanda', function($q) use ($dataInicio) {
                        $q->where('branch_id', $this->branch_id)
                          ->where('data_abertura', '>=', $dataInicio);
                    })
                    ->count(),
                
                'clientes_ativos' => FidelidadeReward::where('status', 'ativo')
                    ->where('validade', '>=', now())
                    ->where('valor_credito', '>', 0) // Excluir recompensas com valor zero
                    ->distinct('user_id')
                    ->count('user_id'),
                
                'recompensas_usadas_periodo' => FidelidadeReward::where('status', 'usado')
                    ->where('updated_at', '>=', $dataInicio)
                    ->count(),
            ];
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar estatísticas: ' . $e->getMessage());
            return [
                'total_recompensas_ativas' => 0,
                'valor_total_creditos' => 0,
                'vendas_produtos_periodo' => 0,
                'clientes_ativos' => 0,
                'recompensas_usadas_periodo' => 0,
            ];
        }
    }

    public function getProdutosMaisVendidosProperty()
    {
        try {
            return Estoque::where('branch_id', $this->branch_id)
                ->where('total_vendas', '>', 0)
                ->orderBy('total_vendas', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar produtos mais vendidos: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return collect();
        }
    }

    public function getVinculosProperty()
    {
        try {
            return Service::with(['produtosRecomendados' => function($q) {
                    $q->wherePivot('is_active', true)
                      ->orderBy('priority');
                }])
                ->get();
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar vínculos: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return collect();
        }
    }

    public function getRecompensasRecentesProperty()
    {
        try {
            return FidelidadeReward::with('user')
                ->where('status', 'ativo')
                ->where('validade', '>=', now())
                ->where('valor_credito', '>', 0) // Excluir recompensas com valor zero
                ->latest()
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar recompensas recentes: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return collect();
        }
    }

    public function getServicosProperty()
    {
        try {
            return Service::orderBy('service')->get();
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar serviços: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return collect();
        }
    }

    public function getProdutosProperty()
    {
        try {
            return Estoque::where('branch_id', $this->branch_id)
                ->where('quantidade_atual', '>', 0)
                ->orderBy('produto_nome')
                ->get();
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar produtos: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return collect();
        }
    }

    public function render()
    {
        try {
            \Log::info('DashboardFidelidade render() iniciado');
            
            $branches = Branch::all();
            \Log::info('Branches carregadas: ' . $branches->count());
            
            $estatisticas = $this->estatisticas;
            \Log::info('Estatísticas carregadas');
            
            $produtosMaisVendidos = $this->produtosMaisVendidos;
            \Log::info('Produtos mais vendidos carregados: ' . $produtosMaisVendidos->count());
            
            $vinculos = $this->vinculos;
            \Log::info('Vínculos carregados: ' . $vinculos->count());
            
            $recompensasRecentes = $this->recompensasRecentes;
            \Log::info('Recompensas recentes carregadas: ' . $recompensasRecentes->count());
            
            $servicos = $this->servicos;
            \Log::info('Serviços carregados: ' . $servicos->count());
            
            $produtos = $this->produtos;
            \Log::info('Produtos carregados: ' . $produtos->count());
            
            \Log::info('DashboardFidelidade render() concluído com sucesso');
            
            return view('livewire.proprietario.dashboard-fidelidade', [
                'branches' => $branches,
                'estatisticas' => $estatisticas,
                'produtosMaisVendidos' => $produtosMaisVendidos,
                'vinculos' => $vinculos,
                'recompensasRecentes' => $recompensasRecentes,
                'servicos' => $servicos,
                'produtos' => $produtos,
            ]);
        } catch (\Exception $e) {
            \Log::error('ERRO CRÍTICO no render(): ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('Linha: ' . $e->getLine());
            \Log::error('Arquivo: ' . $e->getFile());
            
            // Retornar view de erro
            return view('livewire.proprietario.dashboard-fidelidade-erro', [
                'erro' => $e->getMessage()
            ]);
        }
    }
}

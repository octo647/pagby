<?php

namespace App\Livewire\Proprietario;

use App\Models\Service;
use App\Models\Estoque;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class GerenciarProdutosServicos extends Component
{
    public $serviceId;
    public $service;
    public $branchId;
    public $produtosVinculados = [];
    public $produtosDisponiveis = [];
    
    // Modal de adicionar produto
    public $showModal = false;
    public $selectedProdutoId;
    public $priority = 1;
    public $discount_percentage = 0;
    public $observacoes = '';

    protected $rules = [
        'selectedProdutoId' => 'required|exists:estoque,id',
        'priority' => 'required|integer|min:1|max:10',
        'discount_percentage' => 'nullable|numeric|min:0|max:100',
        'observacoes' => 'nullable|string|max:500',
    ];

    public function mount($serviceId, $branchId = null)
    {
        $this->serviceId = $serviceId;
        $this->branchId = $branchId ?? auth()->user()->branches->first()?->id;
        
        // Verifica permissão
        if (!auth()->user()->hasRole('Proprietário')) {
            abort(403);
        }

        $this->service = Service::findOrFail($serviceId);
        $this->carregarDados();
    }

    public function carregarDados()
    {
        // Produtos já vinculados ao serviço
        $this->produtosVinculados = DB::table('service_estoque')
            ->join('estoque', 'service_estoque.estoque_id', '=', 'estoque.id')
            ->where('service_estoque.service_id', $this->serviceId)
            ->where('estoque.branch_id', $this->branchId)
            ->select(
                'service_estoque.id as pivot_id',
                'estoque.id',
                'estoque.produto_nome',
                'estoque.categoria',
                'estoque.preco_unitario',
                'estoque.quantidade_atual',
                'estoque.total_vendas',
                'service_estoque.priority',
                'service_estoque.discount_percentage',
                'service_estoque.is_active',
                'service_estoque.observacoes'
            )
            ->orderBy('service_estoque.priority')
            ->get()
            ->toArray();

        // Produtos disponíveis (que ainda não estão vinculados)
        $produtosVinculadosIds = collect($this->produtosVinculados)->pluck('id')->toArray();
        
        $this->produtosDisponiveis = Estoque::where('branch_id', $this->branchId)
            ->whereNotIn('id', $produtosVinculadosIds)
            ->where('quantidade_atual', '>', 0)
            ->orderBy('produto_nome')
            ->get(['id', 'produto_nome', 'categoria', 'preco_unitario', 'total_vendas'])
            ->toArray();
    }

    public function abrirModal()
    {
        $this->resetModalFields();
        $this->showModal = true;
    }

    public function fecharModal()
    {
        $this->showModal = false;
        $this->resetModalFields();
    }

    private function resetModalFields()
    {
        $this->selectedProdutoId = null;
        $this->priority = count($this->produtosVinculados) + 1;
        $this->discount_percentage = 0;
        $this->observacoes = '';
        $this->resetValidation();
    }

    public function vincularProduto()
    {
        $this->validate();

        // Verifica se produto já está vinculado
        $existe = DB::table('service_estoque')
            ->where('service_id', $this->serviceId)
            ->where('estoque_id', $this->selectedProdutoId)
            ->exists();

        if ($existe) {
            session()->flash('error', 'Este produto já está vinculado a este serviço.');
            return;
        }

        DB::table('service_estoque')->insert([
            'service_id' => $this->serviceId,
            'estoque_id' => $this->selectedProdutoId,
            'priority' => $this->priority,
            'discount_percentage' => $this->discount_percentage,
            'is_active' => true,
            'observacoes' => $this->observacoes,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        session()->flash('message', 'Produto vinculado com sucesso!');
        $this->carregarDados();
        $this->fecharModal();
    }

    public function removerVinculo($pivotId)
    {
        DB::table('service_estoque')->where('id', $pivotId)->delete();
        
        session()->flash('message', 'Vínculo removido com sucesso!');
        $this->carregarDados();
    }

    public function toggleAtivo($pivotId)
    {
        $vinculo = DB::table('service_estoque')->where('id', $pivotId)->first();
        
        DB::table('service_estoque')
            ->where('id', $pivotId)
            ->update([
                'is_active' => !$vinculo->is_active,
                'updated_at' => now(),
            ]);

        $this->carregarDados();
    }

    public function atualizarPrioridade($pivotId, $novaPrioridade)
    {
        DB::table('service_estoque')
            ->where('id', $pivotId)
            ->update([
                'priority' => $novaPrioridade,
                'updated_at' => now(),
            ]);

        $this->carregarDados();
    }

    public function render()
    {
        // Produtos mais vendidos da filial (para referência)
        $maisVendidos = Estoque::where('branch_id', $this->branchId)
            ->maisVendidos(5)
            ->get();

        return view('livewire.proprietario.gerenciar-produtos-servicos', [
            'maisVendidos' => $maisVendidos,
        ]);
    }
}

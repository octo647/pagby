<?php

namespace App\Livewire\Proprietario;

use Livewire\Component;
use App\Models\Estoque;
use App\Models\Branch;
use Livewire\WithPagination;

class GerenciarEstoque extends Component
{
    use WithPagination;

    // Filtros
    public $branch_id = '';
    public $categoria_filtro = '';
    public $busca = '';
    public $filtro_status = 'todos'; // todos, estoque_baixo, vencidos, vencendo

    // Formulário
    public $modalAberto = false;
    public $editando = false;
    public $estoqueId;
    
    // Campos do formulário
    public $produto_nome;
    public $categoria;
    public $quantidade_atual;
    public $quantidade_minima;
    public $preco_unitario;
    public $fornecedor;
    public $data_validade;
    public $observacoes;

    // Dados
    public $filiais = [];
    public $categorias = [];

    protected $rules = [
        'branch_id' => 'required|exists:branches,id',
        'produto_nome' => 'required|string|max:255',
        'categoria' => 'nullable|string|max:255',
        'quantidade_atual' => 'required|integer|min:0',
        'quantidade_minima' => 'required|integer|min:0',
        'preco_unitario' => 'nullable|numeric|min:0',
        'fornecedor' => 'nullable|string|max:255',
        'data_validade' => 'nullable|date|after:today',
        'observacoes' => 'nullable|string',
    ];

    protected $messages = [
        'branch_id.required' => 'Selecione uma filial.',
        'produto_nome.required' => 'O nome do produto é obrigatório.',
        'quantidade_atual.required' => 'A quantidade atual é obrigatória.',
        'quantidade_atual.min' => 'A quantidade atual deve ser maior ou igual a 0.',
        'quantidade_minima.required' => 'A quantidade mínima é obrigatória.',
        'quantidade_minima.min' => 'A quantidade mínima deve ser maior ou igual a 0.',
        'preco_unitario.min' => 'O preço unitário deve ser maior ou igual a 0.',
        'data_validade.after' => 'A data de validade deve ser posterior à data atual.',
    ];

    public function mount()
    {
        $this->filiais = Branch::all();
        $this->carregarCategorias();
    }

    public function carregarCategorias()
    {
        $this->categorias = Estoque::select('categoria')
            ->distinct()
            ->whereNotNull('categoria')
            ->where('categoria', '!=', '')
            ->pluck('categoria')
            ->sort();
    }

    public function updatedBranchId()
    {
        $this->resetPage();
    }

    public function updatedCategoriaFiltro()
    {
        $this->resetPage();
    }

    public function updatedBusca()
    {
        $this->resetPage();
    }

    public function updatedFiltroStatus()
    {
        $this->resetPage();
    }

    public function abrirModal()
    {
        $this->resetForm();
        $this->modalAberto = true;
        $this->editando = false;
    }

    public function editarEstoque($id)
    {
        $estoque = Estoque::findOrFail($id);
        
        $this->estoqueId = $estoque->id;
        $this->branch_id = $estoque->branch_id;
        $this->produto_nome = $estoque->produto_nome;
        $this->categoria = $estoque->categoria;
        $this->quantidade_atual = $estoque->quantidade_atual;
        $this->quantidade_minima = $estoque->quantidade_minima;
        $this->preco_unitario = $estoque->preco_unitario;
        $this->fornecedor = $estoque->fornecedor;
        $this->data_validade = $estoque->data_validade ? $estoque->data_validade->format('Y-m-d') : '';
        $this->observacoes = $estoque->observacoes;
        
        $this->modalAberto = true;
        $this->editando = true;
    }

    public function fecharModal()
    {
        $this->modalAberto = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'estoqueId', 'produto_nome', 'categoria', 'quantidade_atual', 
            'quantidade_minima', 'preco_unitario', 'fornecedor', 
            'data_validade', 'observacoes'
        ]);
        $this->resetErrorBag();
    }

    public function salvar()
    {
        $this->validate();

        $dados = [
            'branch_id' => $this->branch_id,
            'produto_nome' => $this->produto_nome,
            'categoria' => $this->categoria,
            'quantidade_atual' => $this->quantidade_atual,
            'quantidade_minima' => $this->quantidade_minima,
            'preco_unitario' => $this->preco_unitario ?: null,
            'fornecedor' => $this->fornecedor,
            'data_validade' => $this->data_validade ?: null,
            'observacoes' => $this->observacoes,
        ];

        if ($this->editando) {
            Estoque::find($this->estoqueId)->update($dados);
            session()->flash('mensagem', 'Produto atualizado com sucesso!');
        } else {
            Estoque::create($dados);
            session()->flash('mensagem', 'Produto cadastrado com sucesso!');
        }

        $this->fecharModal();
        $this->carregarCategorias();
    }

    public function excluir($id)
    {
        Estoque::findOrFail($id)->delete();
        session()->flash('mensagem', 'Produto excluído com sucesso!');
    }

    public function getEstoqueProperty()
    {
        $query = Estoque::with('branch');

        // Filtro por filial
        if ($this->branch_id) {
            $query->where('branch_id', $this->branch_id);
        }

        // Filtro por categoria
        if ($this->categoria_filtro) {
            $query->where('categoria', $this->categoria_filtro);
        }

        // Busca por nome do produto
        if ($this->busca) {
            $query->where('produto_nome', 'like', '%' . $this->busca . '%');
        }

        // Filtro por status
        switch ($this->filtro_status) {
            case 'estoque_baixo':
                $query->estoqueBaixo();
                break;
            case 'vencidos':
                $query->vencidos();
                break;
            case 'vencendo':
                $query->vencendoEmBreve();
                break;
        }

        return $query->orderBy('produto_nome')->paginate(15);
    }

    public function render()
    {
        return view('livewire.proprietario.gerenciar-estoque', [
            'estoque' => $this->estoque,
        ]);
    }
}
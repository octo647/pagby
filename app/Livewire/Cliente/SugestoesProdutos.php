<?php

namespace App\Livewire\Cliente;

use App\Models\Service;
use App\Models\Estoque;
use Livewire\Component;

class SugestoesProdutos extends Component
{
    public $serviceId;
    public $branchId;
    public $produtosSugeridos = [];
    public $produtosSelecionados = [];
    public $mostrarSugestoes = false;

    protected $listeners = ['servicoSelecionado' => 'carregarProdutos'];

    public function mount($serviceId = null, $branchId = null)
    {
        $this->serviceId = $serviceId;
        $this->branchId = $branchId;
        
        if ($this->serviceId && $this->branchId) {
            $this->carregarProdutos();
        }
    }

    public function carregarProdutos($serviceId = null, $branchId = null)
    {
        // Atualizar IDs se fornecidos
        if ($serviceId) {
            $this->serviceId = $serviceId;
        }
        if ($branchId) {
            $this->branchId = $branchId;
        }

        // Carregar produtos sugeridos usando a lógica hierárquica
        if ($this->serviceId && $this->branchId) {
            $service = Service::find($this->serviceId);
            
            if ($service) {
                $this->produtosSugeridos = $service
                    ->getProdutosSugeridos($this->branchId, 3)
                    ->map(function($produto) {
                        return [
                            'id' => $produto->id,
                            'nome' => $produto->produto_nome,
                            'preco' => $produto->preco_unitario,
                            'categoria' => $produto->categoria,
                            'disponivel' => $produto->quantidade_atual,
                            // Desconto especial se vinculado manualmente
                            'desconto' => $produto->pivot->discount_percentage ?? 0,
                            'preco_final' => $produto->preco_unitario * (1 - ($produto->pivot->discount_percentage ?? 0) / 100),
                        ];
                    })
                    ->toArray();

                $this->mostrarSugestoes = !empty($this->produtosSugeridos);
            }
        }
    }

    public function toggleProduto($produtoId)
    {
        if (in_array($produtoId, $this->produtosSelecionados)) {
            // Remove se já estava selecionado
            $this->produtosSelecionados = array_values(
                array_filter($this->produtosSelecionados, fn($id) => $id != $produtoId)
            );
        } else {
            // Adiciona se não estava
            $this->produtosSelecionados[] = $produtoId;
        }

        // Emite evento para componente pai com produtos selecionados
        $this->dispatch('produtosSelecionados', $this->produtosSelecionados);
    }

    public function isProdutoSelecionado($produtoId)
    {
        return in_array($produtoId, $this->produtosSelecionados);
    }

    public function getValorTotalProdutos()
    {
        $total = 0;
        foreach ($this->produtosSugeridos as $produto) {
            if (in_array($produto['id'], $this->produtosSelecionados)) {
                $total += $produto['preco_final'];
            }
        }
        return $total;
    }

    public function render()
    {
        return view('livewire.cliente.sugestoes-produtos');
    }
}

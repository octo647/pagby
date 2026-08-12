<?php

namespace App\Livewire\Cliente;

use App\Models\FidelidadeReward;
use Livewire\Component;

class MinhasRecompensas extends Component
{
    public $recompensasAtivas = [];
    public $recompensasSelecionadas = []; // Array para permitir múltiplas seleções
    public $mostrarRecompensas = false;

    protected $listeners = ['reloadRecompensas' => 'carregarRecompensas'];

    public function mount()
    {
        $this->carregarRecompensas();
    }

    public function carregarRecompensas()
    {
        if (!auth()->check()) {
            $this->recompensasAtivas = [];
            $this->mostrarRecompensas = false;
            return;
        }

        $this->recompensasAtivas = FidelidadeReward::where('user_id', auth()->id())
            ->where('status', 'ativo')
            ->where('validade', '>=', now())
            ->where('valor_credito', '>', 0) // Excluir recompensas com valor zero
            ->orderBy('validade', 'asc') // Prioriza recompensas que expiram primeiro
            ->get()
            ->map(function($reward) {
                return [
                    'id' => $reward->id,
                    'tipo' => $reward->tipo,
                    'valor_credito' => $reward->valor_credito,
                    'codigo_cupom' => $reward->codigo_cupom,
                    'percentual_desconto' => $reward->percentual_desconto,
                    'servico_gratis_id' => $reward->servico_gratis_id,
                    'validade' => $reward->validade->format('d/m/Y'),
                    'dias_restantes' => max(0, (int) now()->diffInDays($reward->validade->endOfDay(), false)),
                    'origem' => $reward->origem,
                ];
            })
            ->toArray();

        $this->mostrarRecompensas = !empty($this->recompensasAtivas);
    }

    public function selecionarRecompensa($recompensaId)
    {
        if (in_array($recompensaId, $this->recompensasSelecionadas)) {
            // Desselecionar - remover do array
            $this->recompensasSelecionadas = array_values(
                array_filter($this->recompensasSelecionadas, fn($id) => $id !== $recompensaId)
            );
            
            $recompensa = collect($this->recompensasAtivas)
                ->firstWhere('id', $recompensaId);
            
            if ($recompensa) {
                $this->dispatch('recompensaDeselecionada', $recompensa);
            }
        } else {
            // Selecionar - adicionar ao array
            $this->recompensasSelecionadas[] = $recompensaId;
            
            $recompensa = collect($this->recompensasAtivas)
                ->firstWhere('id', $recompensaId);
            
            if ($recompensa) {
                $this->dispatch('recompensaSelecionada', $recompensa);
            }
        }
    }

    public function getTipoBadgeClass($tipo)
    {
        return match($tipo) {
            'credito' => 'bg-green-100 text-green-800 border-green-300',
            'cupom' => 'bg-purple-100 text-purple-800 border-purple-300',
            'servico_gratis' => 'bg-blue-100 text-blue-800 border-blue-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }

    public function getTipoIcon($tipo)
    {
        return match($tipo) {
            'credito' => '💰',
            'cupom' => '🎫',
            'servico_gratis' => '🎁',
            default => '⭐',
        };
    }

    public function getTipoLabel($tipo)
    {
        return match($tipo) {
            'credito' => 'Crédito',
            'cupom' => 'Cupom de Desconto',
            'servico_gratis' => 'Serviço Grátis',
            default => 'Recompensa',
        };
    }

    public function render()
    {
        return view('livewire.cliente.minhas-recompensas');
    }
}

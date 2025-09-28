<?php

namespace App\Livewire\Proprietario;

use Livewire\Component;
use App\Models\Avaliacao;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Avaliacoes extends Component
{   
    public $avaliacoes = [];
    public $funcionarios = [];
    public $estatisticas = [];
    
    // Filtros
    public $funcionarioSelecionado = null;
    public $dataInicio = null;
    public $dataFim = null;

    public function mount()
    {
        // Definir período padrão (último mês)
        $this->dataInicio = now()->subMonth()->startOfMonth()->format('Y-m-d');
        $this->dataFim = now()->endOfMonth()->format('Y-m-d');
        
        // Carregar funcionários
        $this->carregarFuncionarios();
        
        // Carregar avaliações
        $this->atualizarAvaliacoes();
    }

    public function updatedFuncionarioSelecionado()
    {
        $this->atualizarAvaliacoes();
    }

    public function updatedDataInicio()
    {
        $this->atualizarAvaliacoes();
    }

    public function updatedDataFim()
    {
        $this->atualizarAvaliacoes();
    }

    public function carregarFuncionarios()
    {
        $this->funcionarios = User::whereHas('roles', function($query) {
            $query->where('role', 'Funcionário');
        })
        ->whereHas('appointments.avaliacao') // Só funcionários que têm avaliações
        ->orderBy('name')
        ->get();
    }

    public function atualizarAvaliacoes()
    {
        $query = Avaliacao::with([
            'user',
            'appointment.employee'
        ])
        ->whereHas('appointment', function($appointmentQuery) {
            if ($this->funcionarioSelecionado) {
                $appointmentQuery->where('employee_id', $this->funcionarioSelecionado);
            }
        })
        ->whereBetween('created_at', [
            $this->dataInicio . ' 00:00:00',
            $this->dataFim . ' 23:59:59'
        ])
        ->orderByDesc('created_at');

        $this->avaliacoes = $query->get();
        $this->calcularEstatisticas();
    }

    public function calcularEstatisticas()
    {
        $avaliacoes = collect($this->avaliacoes);
        
        // Estatísticas gerais
        $totalAvaliacoes = $avaliacoes->count();
        $mediaNota = $totalAvaliacoes > 0 ? round($avaliacoes->avg('avaliacao'), 1) : 0;
        
        // Distribuição de notas
        $distribuicaoNotas = $avaliacoes->groupBy('avaliacao')
            ->map(function ($grupo) {
                return $grupo->count();
            })
            ->sortKeys();

        // Estatísticas por funcionário
        $estatisticasFuncionarios = $avaliacoes->groupBy('appointment.employee.name')
            ->map(function ($avaliacoesFuncionario, $nomeFuncionario) {
                $total = $avaliacoesFuncionario->count();
                $media = $total > 0 ? round($avaliacoesFuncionario->avg('avaliacao'), 1) : 0;
                $melhorNota = $avaliacoesFuncionario->max('avaliacao') ?? 0;
                $piorNota = $avaliacoesFuncionario->min('avaliacao') ?? 0;
                
                return [
                    'nome' => $nomeFuncionario,
                    'total' => $total,
                    'media' => $media,
                    'melhor_nota' => $melhorNota,
                    'pior_nota' => $piorNota,
                    'cinco_estrelas' => $avaliacoesFuncionario->where('avaliacao', 5)->count(),
                    'uma_estrela' => $avaliacoesFuncionario->where('avaliacao', 1)->count(),
                ];
            })
            ->sortByDesc('media');

        $this->estatisticas = [
            'total_avaliacoes' => $totalAvaliacoes,
            'media_geral' => $mediaNota,
            'distribuicao_notas' => $distribuicaoNotas,
            'por_funcionario' => $estatisticasFuncionarios,
            'melhor_avaliacao' => $avaliacoes->max('avaliacao') ?? 0,
            'pior_avaliacao' => $avaliacoes->min('avaliacao') ?? 0,
        ];
    }

    public function limparFiltros()
    {
        $this->funcionarioSelecionado = null;
        $this->dataInicio = now()->subMonth()->startOfMonth()->format('Y-m-d');
        $this->dataFim = now()->endOfMonth()->format('Y-m-d');
        $this->atualizarAvaliacoes();
    }

    public function render()
    {
        return view('livewire.proprietario.avaliacoes');
    }
}

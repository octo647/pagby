<?php

namespace App\Livewire\Funcionario;

use Livewire\Component;
use App\Models\Branch;
use App\Models\User;
use App\Models\PlanMonthlyRevenue;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;

class ControlePagamentoPlanos extends Component
{
    public $planos = [];
    public $selectedPlano;
    public $branches = [];
    public $selectedBranch;
    public $faturamentoPlanos = 0;
    public $mesFaturamento;
    public $pagamentos = [];
    public $funcionarioId;

    public function mount()
    {
        $this->funcionarioId = Auth::id();
        $user = User::find($this->funcionarioId);
        $branch = $user->branches()->first();
        $this->planos = Plan::all();
        $this->selectedBranch = $branch ? $branch->id : null;
        $this->selectedPlano = $this->planos[0]->id ?? null;
        $this->mesFaturamento = date('Y-m');
        $this->carregarFaturamentoPlano();
        $this->calcularPagamentos();
    }

    public function updatedSelectedPlano()
    {
        $this->carregarFaturamentoPlano();
        $this->calcularPagamentos();
    }

    public function updatedSelectedBranch()
    {
        $this->carregarFaturamentoPlano();
        $this->calcularPagamentos();
    }

    public function updatedMesFaturamento()
    {
        $this->carregarFaturamentoPlano();
        $this->calcularPagamentos();
    }

    public function carregarFaturamentoPlano()
    {
        if ($this->selectedPlano && $this->selectedBranch && $this->mesFaturamento) {
            $registro = PlanMonthlyRevenue::where('plan_id', $this->selectedPlano)
                ->where('branch_id', $this->selectedBranch)
                ->where('month', $this->mesFaturamento.'-01')
                ->first();
            $this->faturamentoPlanos = $registro ? $registro->revenue : 0;
        } else {
            $this->faturamentoPlanos = 0;
        }
    }

    public function calcularPagamentos()
    {
        $this->pagamentos = [];
        if (!$this->selectedPlano || !$this->selectedBranch || !$this->mesFaturamento) {
            return;
        }

        $inicioMes = $this->mesFaturamento . '-01';
        $fimMes = date('Y-m-t', strtotime($inicioMes));

        $plano = Plan::find($this->selectedPlano);
        if (!$plano) return;
        $servicosPlano = $plano->services;
        if (!$servicosPlano || count($servicosPlano) === 0) return;

        $comandas = \App\Models\Comanda::where('branch_id', $this->selectedBranch)
            ->where('status', 'Finalizada')
            ->whereDate('data_fechamento', '>=', $inicioMes)
            ->whereDate('data_fechamento', '<=', $fimMes)
            ->pluck('id');

        $comandaServicos = \App\Models\ComandaServico::whereIn('comanda_id', $comandas)
            ->whereIn('service_id', $servicosPlano->pluck('id'))
            ->get();

        $tempos = [];
        $tempoTotal = 0;
        foreach ($comandaServicos as $cs) {
            $funcId = $cs->funcionario_id;
            $service = $servicosPlano->where('id', $cs->service_id)->first();
            if (!$service) continue;
            $minutos = $service->getDurationForEmployee($this->selectedBranch, $funcId) * $cs->quantidade;
            $tempos[$funcId] = ($tempos[$funcId] ?? 0) + $minutos;
            $tempoTotal += $minutos;
        }

        if ($tempoTotal == 0) {
            $this->pagamentos = [
                'tempo_total' => 0,
                'valor' => 0,
                'percentual_tempo' => 0,
            ];
            return;
        }

        $branch = Branch::find($this->selectedBranch);
        $percentualComissao = $branch && isset($branch->commission) ? $branch->commission : 0;
        $faturamento = $this->faturamentoPlanos;
        $totalComissao = $faturamento * ($percentualComissao / 100);

        $tempoFuncionario = $tempos[$this->funcionarioId] ?? 0;
        $percentualTempo = $tempoFuncionario / $tempoTotal * 100;
        $valor = $totalComissao * ($tempoFuncionario / $tempoTotal);
        $this->pagamentos = [
            'tempo_total' => $tempoFuncionario,
            'valor' => $valor,
            'percentual_tempo' => round($percentualTempo, 2),
        ];
    }

    public function render()
    {
        return view('livewire.funcionario.controle-pagamento-planos');
    }
}

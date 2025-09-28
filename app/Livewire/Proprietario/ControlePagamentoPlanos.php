<?php

namespace App\Livewire\Proprietario;

use Livewire\Component;
use App\Models\Branch;
use App\Models\User;

use App\Models\PlanMonthlyRevenue;
use App\Models\Plan;

class ControlePagamentoPlanos extends Component
{
    public $planos = [];
    public $selectedPlano;
    public $branches = [];
    public $selectedBranch;
    public $funcionarios = [];
    public $selectedFuncionario = '';
    public $faturamentoPlanos = 0;
    public $pagamentos = [];
    public $mesFaturamento;

    public function mount()
    {
        $this->branches = Branch::all();
        $this->planos = Plan::all();
        $this->selectedBranch = $this->branches[0]->id ?? null;
        $this->selectedPlano = $this->planos[0]->id ?? null;
        $this->mesFaturamento = date('Y-m');
        $this->atualizarFuncionarios();
        $this->carregarFaturamentoPlano();
    }

    public function updatedSelectedPlano()
    {
        $this->carregarFaturamentoPlano();
    }

    public function updatedSelectedBranch()
    {
        $this->atualizarFuncionarios();
        $this->carregarFaturamentoPlano();
    }

    public function updatedMesFaturamento()
    {
        $this->carregarFaturamentoPlano();
    }

    public function updatedFaturamentoPlanos($value)
    {
        $this->salvarFaturamentoPlano($value);
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

    public function salvarFaturamentoPlano($valor)
    {
        if ($this->selectedPlano && $this->selectedBranch && $this->mesFaturamento) {
            PlanMonthlyRevenue::updateOrCreate(
                [
                    'plan_id' => $this->selectedPlano,
                    'branch_id' => $this->selectedBranch,
                    'month' => $this->mesFaturamento.'-01',
                ],
                [
                    'revenue' => $valor,
                ]
            );
        }
    }

    public function atualizarFuncionarios()
    {
        if ($this->selectedBranch) {
            $this->funcionarios = User::whereHas('branches', function($query) {
                $query->where('branches.id', $this->selectedBranch);
            })->get(); // Collection
        } else {
            $this->funcionarios = collect();
        }
    }

    public function calcularPagamentos()
    {
        $this->pagamentos = [];
        if (!$this->selectedPlano || !$this->selectedBranch || !$this->mesFaturamento) {
            return;
        }

        // Determinar o período do mês selecionado
        $inicioMes = $this->mesFaturamento . '-01';
        $fimMes = date('Y-m-t', strtotime($inicioMes));

        // Buscar serviços do plano
        $plano = null;
        foreach ($this->planos as $p) {
            if ($p->id == $this->selectedPlano) {
                $plano = $p;
                break;
            }
        }
        if (!$plano) return;
        $servicosPlano = $plano->services;
        if (!$servicosPlano || count($servicosPlano) === 0) return;

        // Buscar comandas finalizadas da filial no mês
        $comandas = \App\Models\Comanda::where('branch_id', $this->selectedBranch)
            ->where('status', 'Finalizada')
            ->whereDate('data_fechamento', '>=', $inicioMes)
            ->whereDate('data_fechamento', '<=', $fimMes)
            ->pluck('id');

        // Buscar todos os ComandaServico desses serviços, nessas comandas
        $comandaServicos = \App\Models\ComandaServico::whereIn('comanda_id', $comandas)
            ->whereIn('service_id', $servicosPlano->pluck('id'))
            ->get();

        // Calcular tempo total por funcionário
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
            $this->pagamentos = [];
            return;
        }

        // Buscar funcionários envolvidos
        $funcionarios = $this->funcionarios->filter(function($f) use ($tempos) {
            return isset($tempos[$f->id]);
        });

        // Buscar comissão da filial (exemplo: campo commission_percent)
        $branch = null;
        foreach ($this->branches as $b) {
            if ($b->id == $this->selectedBranch) {
                $branch = $b;
                break;
            }
        }
    $percentualComissao = $branch && isset($branch->commission) ? $branch->commission : 0;
        $faturamento = $this->faturamentoPlanos;
        $totalComissao = $faturamento * ($percentualComissao / 100);

        // Filtrar apenas funcionários com tempo > 0
        $pagamentos = [];
        $somaPercentuais = 0;
        foreach ($funcionarios as $func) {
            $tempo = $tempos[$func->id];
            if ($tempo <= 0) continue;
            $percentual = $tempo / $tempoTotal;
            $somaPercentuais += $percentual;
            $pagamentos[] = [
                'funcionario' => $func,
                'tempo_total' => $tempo,
                'percentual_tempo' => $percentual, // ainda em fração
            ];
        }
        // Normalizar para garantir soma 100%
        if ($somaPercentuais > 0) {
            foreach ($pagamentos as &$p) {
                $p['percentual_tempo'] = round(($p['percentual_tempo'] / $somaPercentuais) * 100, 2);
                $p['valor'] = $totalComissao * ($p['percentual_tempo'] / 100);
            }
        }
        $this->pagamentos = $pagamentos;
    }

    public function render()
    {
        return view('livewire.proprietario.controle-pagamento-planos');
    }
}

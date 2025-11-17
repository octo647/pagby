<?php
namespace App\Livewire\Proprietario;

use App\Models\Comanda;
use App\Models\Branch;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class BalancoDiario extends Component
{
    public $caixa;
    public $entrada = 0;
    public $saida = 0;
    public $saldo_final = 0;
    public $comandasDoDia;
    public $totalPago = 0;
    public $totalCaixa = 0;
    public $data;
    public $commission = 0;
    public $value_commission = 0;
    public $branches = [];
    public $branch_id = null;

    public function mount()
    {
        $this->data = now()->format('Y-m-d');
        $this->branches = Branch::all();
        $this->branch_id = $this->branches->first()->id ?? null;
        $this->comandasDoDia = collect();
        $this->carregarComandas();
        $this->buscarCaixa();
    }

    public function updatedData()
    {
        $this->carregarComandas();
        $this->buscarCaixa();
    }

    public function updatedBranchId()
    {
        $this->carregarComandas();
        $this->buscarCaixa();
    }

    public function carregarComandas()
    {
        $dia = $this->data ?? now()->format('Y-m-d');
        $branchId = $this->branch_id;
        $this->comandasDoDia = Comanda::with(['branch', 'funcionario', 'comandaServicos', 'comandaProdutos'])
            ->whereDate('data_abertura', $dia)
            ->where('branch_id', $branchId)
            ->get();

        $this->totalPago = $this->comandasDoDia
            ->where('status', 'Finalizada')
            ->sum('total_geral');
        $this->commission = Branch::where('id', $branchId)
            ->value('commission');
        $this->value_commission = ($this->totalPago * $this->commission) / 100;
    }

    public function buscarCaixa()
    {
        $this->caixa = \App\Models\Caixa::where('branch_id', $this->branch_id)
            ->where('data', $this->data)
            ->first();
        if ($this->caixa) {
            $this->entrada = $this->caixa->total_entrada;
            $this->saida = $this->caixa->total_saida;
            $this->saldo_final = $this->caixa->saldo_final;
        } else {
            $this->entrada = 0;
            $this->saida = 0;
            $this->saldo_final = 0;
        }
    }

    public function salvarCaixa()
    {
        $caixa = \App\Models\Caixa::updateOrCreate(
            [
                'branch_id' => $this->branch_id,
                'data' => $this->data,
            ],
            [
                'total_entrada' => $this->entrada,
                'total_saida' => $this->saida,
                'saldo_final' => $this->entrada - $this->saida,
            ]
        );
        $this->saldo_final = $caixa->saldo_final;
        $this->caixa = $caixa;
        session()->flash('message', 'Caixa salvo com sucesso!');
    }

    public function atualizarStatus($comandaId, $novoStatus)
    {
        $comanda = Comanda::find($comandaId);
        if ($comanda) {
            if ($novoStatus === 'Finalizada') {
                $comanda->finalizar();
            } elseif ($novoStatus === 'Cancelada') {
                $comanda->cancelar();
            } else {
                $comanda->status = $novoStatus;
                $comanda->save();
            }
            $this->carregarComandas();
            session()->flash('message', 'Status da comanda atualizado com sucesso!');
        }
    }

    public function render()
    {
        return view('livewire.proprietario.balanco-diario', [
            'comandasDoDia' => $this->comandasDoDia,
            'total_pago' => $this->totalPago,
        ]);
    }
}
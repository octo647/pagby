<?php
namespace App\Livewire\Proprietario;

use App\Models\Appointment;
use App\Models\Branch;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class BalancoDiario extends Component
{
    public $caixa;
    public $entrada = 0;
    public $saida = 0;
    public $saldo_final = 0;
    public $agendamentosDoDia;
    public $totalPago = 0;
    public $totalCaixa = 0;
    public $data;
    public $comission = 0;
    public $value_comission = 0;
    public $branches = [];
    public $branch_id = null;

    public function mount()
    {
        $this->data = now()->format('Y-m-d');
        $this->branches = Branch::all();
        $this->branch_id = $this->branches->first()->id ?? null;
        $this->agendamentosDoDia = collect();
        $this->carregarAgendamentos();
        $this->buscarCaixa();
    }

    public function updatedData()
    {
        $this->carregarAgendamentos();
        $this->buscarCaixa();
    }

    public function updatedBranchId()
    {
        $this->carregarAgendamentos();
        $this->buscarCaixa();
    }

    public function carregarAgendamentos()
    {
        $dia = $this->data ?? now()->format('Y-m-d');
        $branchId = $this->branch_id;
        $this->agendamentosDoDia = Appointment::with(['customer', 'branch', 'employee'])
            ->whereDate('appointment_date', $dia)
            ->where('branch_id', $branchId)
            ->get();

        $this->totalPago = $this->agendamentosDoDia
            ->whereIn('status', ['Confirmado', 'Realizado'])
            ->sum('total');
        $this->comission = Branch::where('id', $branchId)
            ->value('comission');
        $this->value_comission = ($this->totalPago * $this->comission) / 100;
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

    public function atualizarStatus($agendamentoId, $novoStatus)
    {
        $agendamento = Appointment::find($agendamentoId);
        if ($agendamento) {
            $agendamento->status = $novoStatus;
            if ($novoStatus === 'Cancelado') {
                $agendamento->cancellation_reason = 'Cancelado pelo proprietário';
                $agendamento->cancellation_time = now();
                $agendamento->cancellation_by = Auth::id();
                $agendamento->cancellation_date = now();
            } else {
                $agendamento->cancellation_reason = null;
            }
            $agendamento->updated_at = now();
            $agendamento->updated_by = Auth::id();
            $agendamento->save();
            $this->carregarAgendamentos();
        }
    }

    public function render()
    {
        return view('livewire.proprietario.balanco-diario', [
            'agendamentosDoDia' => $this->agendamentosDoDia,
            'total_pago' => $this->totalPago,
        ]);
    }
}
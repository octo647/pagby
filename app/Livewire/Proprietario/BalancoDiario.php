<?php

namespace App\Livewire\Proprietario;

use App\Models\Appointment;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class BalancoDiario extends Component
{
    public $agendamentosDoDia; // Inicialize como array vazio
    public $totalPago = 0;
    public $totalCaixa = 0;
    public $data;

    public function mount()
    {
        $this->data = now()->format('Y-m-d');
        $this->carregarAgendamentos();  
    }

    public function updatedData()
    {
        $this->carregarAgendamentos();
    }

    public function carregarAgendamentos()
    {
        $dia = $this->data ?? now()->format('Y-m-d');

        $this->agendamentosDoDia = Appointment::with(['customer', 'branch', 'employee'])
            ->whereDate('appointment_date', $dia)
            ->get();

        $this->totalPago = $this->agendamentosDoDia
            ->whereIn('status', ['Confirmado', 'Realizado'])
            ->sum('total');
    }
    public function atualizarStatus($agendamentoId, $novoStatus)
    {
        $agendamento = Appointment::find($agendamentoId);
        if ($agendamento) {
            $agendamento->status = $novoStatus;
            if ($novoStatus === 'Cancelado') {
                $agendamento->cancellation_reason = 'Cancelado pelo proprietário';
                $agendamento->cancellation_time = now(); // Define o horário de cancelamento
                $agendamento->cancellation_by = Auth::id(); // Define o usuário que cancelou
                $agendamento->cancellation_date = now(); // Define a data de cancelamento
            } else {
                $agendamento->cancellation_reason = null; // Limpa o motivo de cancelamento se não for cancelado
            }
            $agendamento->updated_at = now(); // Atualiza o timestamp de atualização
            $agendamento->updated_by = Auth::id(); // Define o usuário que atualizou
            $agendamento->save();
            $this->carregarAgendamentos(); // Recarrega os agendamentos após a atualização
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
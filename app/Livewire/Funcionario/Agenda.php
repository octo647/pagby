<?php

namespace App\Livewire\Funcionario;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Agenda extends Component
{
    public $selectedDate;

    protected $listeners = ['atualizarStatus'];

    public function mount()
    {
        $this->selectedDate = now()->toDateString();
    }

    public function atualizarStatus($id, $status)
    {
        $agendamento = \App\Models\Appointment::find($id);
        if ($agendamento) {
            $agendamento->status = $status;
            $agendamento->save();

            // Se for cancelado, apaga a comanda correspondente
            if ($status === 'Cancelado') {
                \App\Models\Comanda::where('appointment_id', $agendamento->id)->delete();
            }
        }
        // Opcional: emitir evento ou mensagem de feedback
    }

    public function confirmarCancelamento($id)
    {
        // Emite um evento para o JavaScript (Livewire v3+)
        $this->dispatch('confirm-cancel', id: $id);
    }

    public function render()
    {
        $user = Auth::user();
        $query = \App\Models\Appointment::where('employee_id', $user->id);
        if ($this->selectedDate) {
            $query->whereDate('appointment_date', $this->selectedDate);
        } else {
            $query->where('appointment_date', '>=', now()->toDateString());
        }
        $agendamentos = $query->orderBy('appointment_date')->orderBy('start_time')->get();
        return view('livewire.funcionario.agenda', compact('agendamentos'));
    }
}

<?php

namespace App\Livewire\Funcionario;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Agenda extends Component
{
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
        // Emite um evento para o JavaScript;
        $this->dispatchBrowserEvent('confirm-cancel', ['id' => $id]);
    }

    public function render()
    {
        // Verifica se o usuário está autenticado
        $user = Auth::user();

        // Busca os próximos agendamentos do funcionário logado
        $agendamentos = \App\Models\Appointment::where('employee_id', $user->id)
            ->where('appointment_date', '>=', now()->toDateString())
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->get();

        return view('livewire.funcionario.agenda', compact('agendamentos'));
    }
}

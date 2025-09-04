<?php

namespace App\Livewire\Cliente;

use Livewire\Component;

class Historico extends Component
{   
    public $historico;
    public $showModal = false;
    public $avaliacao = null;
    public $comentario = null;
 
    public $appointmentId = null;
 
    public function mount()
    {
        $cliente = auth()->user();
        $historico = $cliente->clientAppointments()
        ->with('avaliacao', 'employee') // Certifique-se de que o relacionamento está definido corretamente no modelo Appointment
        ->orderByDesc('appointment_date')
        ->get();        
        $this->historico = $historico;
        //dd($this->historico);        
    }
    public function avaliacao($appointmentId)
    {
        $appointment = \App\Models\Appointment::find($appointmentId);
        if ($appointment) {
            return $appointment->avaliacao; // Certifique-se de que o relacionamento está definido corretamente no modelo Appointment
        }
        return null;
    }
    public function showModal($appointmentId)
    {
        $this->showModal = true;
        $this->emit('showModal', $appointmentId);
    }
    public function closeModal()
    {
        $this->showModal = false;
        
    }
    public function avaliar($appointmentId)
    {
        $this->showModal = true;
        $this->appointmentId = $appointmentId;
        $this->avaliacao = null;
        $this->comentario = null;
    }
    public function updateAvaliacao()
    {
        $appointment = \App\Models\Appointment::find($this->appointmentId);
        $avaliacao = $this->avaliacao;
        $comentario = $this->comentario;
        
        
        if (!$avaliacao || !$comentario) {
            session()->flash('error', 'Avaliação e comentário são obrigatórios.');
            return;
        }
       
        if ($appointment) {
            $appointment->avaliacao()->updateOrCreate(
                ['appointment_id' => $this->appointmentId],
                [
                    'user_id' => auth()->user()->id,
                    'branch_id' => $appointment->branch_id, 
                    'appointment_id' => $this->appointmentId,
                    'data' => now(),
                    'avaliacao' => $avaliacao,
                    'comentario' => $comentario,
                ]       
            );
            $this->closeModal();
            session()->flash('message', 'Avaliação atualizada com sucesso!');
        } else {
            session()->flash('error', 'Agendamento não encontrado.');
        }
    }
    public function editarAvaliacao($appointmentId)
    {
        $this->showModal = true;
        $this->appointmentId = $appointmentId;
        
        $avaliacao = \App\Models\Avaliacao::where('appointment_id', $appointmentId)->first();
        if ($avaliacao) {
            $this->avaliacao = $avaliacao->avaliacao;
            $this->comentario = $avaliacao->comentario;
        } else {
            $this->avaliacao = null;
            $this->comentario = null;
        }
    }   

    public function render()
    {
        return view('livewire.cliente.historico');
    }
}

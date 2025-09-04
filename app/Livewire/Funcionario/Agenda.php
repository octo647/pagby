<?php

namespace App\Livewire\Funcionario;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Agenda extends Component
{
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

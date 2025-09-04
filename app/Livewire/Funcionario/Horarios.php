<?php

namespace App\Livewire\Funcionario;

use Livewire\Component;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Horarios extends Component
{   
    public $horarios;
    public $funcionarioId;
    public $tabelaAtiva = 'horarios';


    public function mount()
    {   
        $dias = [
            'Sunday' => 'Domingo',
            'Monday' => 'Segunda',
            'Tuesday' => 'Terça',
            'Wednesday' => 'Quarta',
            'Thursday' => 'Quinta',
            'Friday' => 'Sexta',
            'Saturday' => 'Sábado'
        ];
        $this->funcionarioId = auth()->user()->id;
        $this->horarios = Schedule::where('user_id', $this->funcionarioId)
        ->orderBy('day_of_week', 'asc')
        ->orderBy('start_time', 'asc')
        ->get()
        ->map(function ($horario) use ($dias) {
            $horario->day_name = $dias[$horario->day_of_week];
            return $horario;
        });
        
            
    }


    public function render()
    {
        return view('livewire.funcionario.horarios');
    }
}

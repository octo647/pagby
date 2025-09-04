<?php

namespace App\Livewire\Proprietario;

use Livewire\Component;
use App\Models\Avaliacao;


class Avaliacoes extends Component
{   
    public $avaliacoes = [];

    public function mount()
    {
        $this->avaliacoes = \App\Models\Avaliacao::with([
            'user',
            'appointment.employee'
        ])
        ->orderByDesc('created_at')
        ->limit(50)
        ->get();
        
    }
    public function render()
    {
        return view('livewire.proprietario.avaliacoes');
    }
}

<?php

namespace App\Livewire\Cliente;

use Livewire\Component;

class Avaliacoes extends Component
{
    public $avaliacoes;
    
    public function mount(): void
    {
        $customer_id = auth()->user()->id;

        $this->avaliacoes = \App\Models\Avaliacao::where('user_id', $customer_id)
            ->orderBy('created_at', 'desc')
            ->get();
            
    }
    public function render()
    {
        return view('livewire.cliente.avaliacoes');
    }
}

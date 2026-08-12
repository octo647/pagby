<?php

namespace App\Livewire\Funcionario;

use Livewire\Component;
use App\Models\User;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class Servicos extends Component
{
    public $serviços = [];
    public function mount()
    {
        $servicos = Auth::user()->services;    

        $this->serviços = $servicos;
       
    }

    public function render()
    {
        return view('livewire.funcionario.servicos');
    }
}

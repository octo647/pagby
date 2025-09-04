<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class Planos extends Component
{
    public $planos;

    public function mount()
    {
        $this->planos = [
            (object)[
                'nome' => 'Plano Básico',
                'preco' => 29.90,
                'beneficios' => 'Um profissional, Todas as funcionalidades,Suporte via e-mail'
            ],
            (object)[
                'nome' => 'Plano Intermediário',
                'preco' => 59.90,
                'beneficios' => 'Dois profissionais, Todas as funcionalidades, Suporte via chat'
            ],
            (object)[
                'nome' => 'Plano Avançado',
                'preco' => 99.90,
                'beneficios' => 'Três ou mais profissionais, Todas as funcionalidades, Suporte prioritário'
            ],
        ];
    }

    public function render()
    {
        return view('livewire.admin.planos');
    }
}

<?php

namespace App\Livewire\Admin;


use Livewire\Component;
use App\Models\PagByPayment;
use App\Models\User;

class Planos extends Component
{
    public $payments;

    public function mount()
    {
        // Busca os pagamentos mais recentes (pode paginar depois)
        $this->payments = PagByPayment::with('tenant')->orderByDesc('id')->take(50)->get();
        
    }

    public function render()
    {
        return view('livewire.admin.planos', [
            'payments' => $this->payments
        ]);
    }
}

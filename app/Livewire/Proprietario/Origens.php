<?php

namespace App\Livewire\Proprietario;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Origens extends Component
{
    public $origensClientes = [];
    public $origensClientesLabels = [];
    public $origensClientesValores = [];
    public function mount()
{
    $this->origensClientes = $origensClientes = \App\Models\User::select('origin', DB::raw('count(*) as total'))
        ->groupBy('origin')
        ->orderByDesc('total')
        ->get();
    $this->origensClientesLabels = $origensClientes->pluck('origin')->map(fn($o) => $o ?? 'Não informado')->toArray();
    $this->origensClientesValores = $origensClientes->pluck('total')->toArray();
}
    
    
    
    public function render()
    {
        return view('livewire.proprietario.origens');
    }
}

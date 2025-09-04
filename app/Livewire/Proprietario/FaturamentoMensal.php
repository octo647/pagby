<?php

namespace App\Livewire\Proprietario;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\Component;

class FaturamentoMensal extends Component
{   

    public $faturamentoMensal = [];

    public function mount()
    {
        $this->faturamentoMensal = DB::table('appointments')
            ->selectRaw('DATE_FORMAT(appointment_date, "%Y-%m") as mes, SUM(total) as total')
            ->where('status', ['Confirmado', 'Realizado']) // ajuste conforme seu sistema
            ->where('appointment_date', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes')
            ->toArray();
    }

    public function render()
    {
        return view('livewire.proprietario.faturamento-mensal');
    }
}

<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class HorariosPico extends Component
{
    
    public $horarios = [];
    public $periodStart;
    public $periodEnd;
    public $horasLabels = [];
    public $horasValores = [];
    public function mount()
    {
        $this->periodStart = now()->subMonths(1)->startOfMonth()->format('Y-m-d');
        
        $this->periodEnd = now()->endOfMonth()->format('Y-m-d');
      
         $horarios = \App\Models\Appointment::select(
            DB::raw('HOUR(start_time) as hora'),
            DB::raw('COUNT(*) as total')
        )
        //->whereBetween('appointment_date', [$this->periodStart, $this->periodEnd])
        ->groupBy(DB::raw('HOUR(start_time)'))
        ->orderBy('hora')
        ->get();
        $this->horarios = $horarios;
      

    $this->horasLabels = $horarios->pluck('hora')->map(function($h) {
        return str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
    })->values()->toArray();
    

    $this->horasValores = $horarios->pluck('total')->values()->toArray();
    
    }


    public function render()
    {
        return view('livewire.horarios-pico',[
            'horarios' => $this->horarios,
            'horasLabels' => $this->horasLabels,
            'horasValores' => $this->horasValores,
        ]);
    }
}

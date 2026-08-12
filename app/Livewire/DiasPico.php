<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class DiasPico extends Component
{
    public $diasPico = [];
    public $periodStart; // Data de início do período
    public $periodEnd; // Data de fim do período
    public $diasLabels = [];
    public $diasValores = [];
public function mount()
{   
    $this->periodStart = now()->subMonth()->startOfMonth()->format('Y-m-d');
    $this->periodEnd = now()->endOfMonth()->format('Y-m-d');
    $this->atualizaDiasPico();
}
public function updatedPeriodStart()
{
    $this->atualizaDiasPico();
}

public function updatedPeriodEnd()
{
    $this->atualizaDiasPico();
}

public function atualizaDiasPico()
{
    $dias = \App\Models\Appointment::select(
            DB::raw('DAYOFWEEK(appointment_date) as dia_semana'),
            DB::raw('COUNT(*) as total')
        )
        ->whereBetween('appointment_date', [$this->periodStart, $this->periodEnd]) // ajuste o período se quiser
        ->groupBy(DB::raw('DAYOFWEEK(appointment_date)'))
        ->orderBy('dia_semana')
        ->get();
      

    // Mapeia para nomes dos dias (1=Domingo, 2=Segunda, ..., 7=Sábado)
    $nomesDias = [1 => 'Domingo', 2 => 'Segunda', 3 => 'Terça', 4 => 'Quarta', 5 => 'Quinta', 6 => 'Sexta', 7 => 'Sábado'];
    $this->diasPico = $dias->map(function($item) use ($nomesDias) {
        return [
            'dia' => $nomesDias[$item->dia_semana] ?? $item->dia_semana,
            'total' => $item->total
        ];
    });
    $this->diasLabels = $dias->pluck('dia_semana')->map(fn($d) => $nomesDias[$d])->toArray();
    $this->diasValores = $dias->pluck('total')->toArray();

    // Dispatchar evento para atualizar o gráfico
    $this->dispatch('atualizar-grafico-dias-pico', 
        labels: $this->diasLabels,
        valores: $this->diasValores
    );
}
    public function render()
    {
        return view('livewire.dias-pico');
    }
}

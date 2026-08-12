<?php

namespace App\Livewire\Proprietario;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class TicketMedio extends Component
{
    public $ticketMedioClientes = [];
    public $periodStart;
    public $periodEnd;  
    public $minAppointments = 1;
    use WithPagination;
    public function mount()
    {
        
        $this->periodStart = now()->subMonths(6)->startOfMonth()->format('Y-m-d');
        $this->periodEnd = now()->endOfMonth()->format('Y-m-d');

    }
    public function render()
    { 
        $clientes = \App\Models\User::query()
    ->select(
        'users.id',
        'users.name',
        'users.email',
        DB::raw('COUNT(a.id) as total_agendamentos'),
        DB::raw('COALESCE(SUM(a.total),0) as total_gasto'),
        DB::raw('COALESCE(SUM(a.total)/NULLIF(COUNT(a.id),0),0) as ticket_medio')
    )
    ->join('appointments as a', function($join) {
        $join->on('a.customer_id', '=', 'users.id')
             ->whereBetween('a.appointment_date', [$this->periodStart, $this->periodEnd]);
    })
    ->groupBy('users.id', 'users.name', 'users.email')
    ->having('total_agendamentos', '>=', $this->minAppointments)
    ->orderByDesc('ticket_medio')
    ->paginate(10);

        return view('livewire.proprietario.ticket-medio', [
            'clientes' => $clientes,
        ] );
    }
}

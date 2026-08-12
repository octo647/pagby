<?php

namespace App\Livewire\Proprietario;

use App\Models\Appointment;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class PublicBookingStats extends Component
{
    use WithPagination;

    public $filter = 'today'; // today, week, month, all
    public $search = '';

    public function render()
    {
        $query = Appointment::query()
            ->whereHas('customer', function($q) {
                // Filtra apenas agendamentos de clientes criados via agendamento público
                $q->where('created_via_public_booking', true);
            })
            ->with(['customer', 'employee', 'branch'])
            ->orderBy('appointment_date', 'desc')
            ->orderBy('start_time', 'desc');

        // Filtro por período
        switch ($this->filter) {
            case 'today':
                $query->whereDate('appointment_date', today());
                break;
            case 'week':
                $query->whereBetween('appointment_date', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ]);
                break;
            case 'month':
                $query->whereMonth('appointment_date', now()->month)
                      ->whereYear('appointment_date', now()->year);
                break;
        }

        // Busca por nome do cliente
        if ($this->search) {
            $query->whereHas('customer', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        $appointments = $query->paginate(10);

        // Estatísticas
        $stats = $this->getStats();

        return view('livewire.proprietario.public-booking-stats', [
            'appointments' => $appointments,
            'stats' => $stats,
        ]);
    }

    private function getStats()
    {
        $baseQuery = Appointment::query()
            ->whereHas('customer', function($q) {
                $q->where('created_via_public_booking', true);
            });

        return [
            'today' => (clone $baseQuery)->whereDate('appointment_date', today())->count(),
            'week' => (clone $baseQuery)->whereBetween('appointment_date', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'month' => (clone $baseQuery)->whereMonth('appointment_date', now()->month)
                                         ->whereYear('appointment_date', now()->year)
                                         ->count(),
            'total' => (clone $baseQuery)->count(),
        ];
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}

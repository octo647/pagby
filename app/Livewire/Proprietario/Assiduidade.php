<?php
namespace App\Livewire\Proprietario;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use App\Models\Appointment;

class Assiduidade extends \Livewire\Component
{   
    public $search = '';
    public $minAppointments = 1;
    public $periodStart;
    public $periodEnd;
    public $daysSinceLast = null;
    public $showAll = false;
    use WithPagination;
    
    public function mount()
    {
        $this->periodStart = now()->subMonths(6)->format('Y-m-d');
        $this->periodEnd = now()->format('Y-m-d');
        }
    

    public function render()
    {
        $query = User::query()
        ->whereHas('clientAppointments')
        ->withCount('clientAppointments')
        ->when($this->search, fn($q) =>
            $q->where('name', 'like', "%{$this->search}%")
              ->orWhere('email', 'like', "%{$this->search}%")
        )
        ->having('client_appointments_count', '>=', $this->minAppointments)
        // Subquery para pegar a data do último agendamento
        ->addSelect([
            'ultimo_agendamento' => Appointment::select('appointment_date')
                ->whereColumn('customer_id', 'users.id')
                ->orderByDesc('appointment_date')
                ->limit(1)
        ])
        // Filtro SQL: dias desde o último agendamento
        ->when($this->daysSinceLast, function($q) {
    $q->havingRaw('DATEDIFF(NOW(), ultimo_agendamento) >= ?', [$this->daysSinceLast]);
})
        ->orderByDesc('ultimo_agendamento');
        
    // Se showall for verdadeiro, retorna todos os clientes sem paginação
    $clientes = $this->showAll
    ? $query->get()
    : $query->paginate(10);

    return view('livewire.proprietario.assiduidade', [
        'clientes' => $clientes,
    ]);
  
    }
}
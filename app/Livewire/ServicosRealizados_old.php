<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
        

class ServicosRealizados extends Component
{
    use WithPagination {
    WithPagination::resetPage as baseResetPage;
    }
    public $agendamentos=[];
    public $showModal = false;
    public $selectedUserId = null;

    public $chartData; // Dados do gráfico
    public function getPageName()
    {
        return 'appointmentsPage';
    }
    public function resetPage($pageName = 'page')
    {
        $this->baseResetPage($pageName);
    }


    public function showUserDetails($userId)
    {
        $this->selectedUserId = $userId;
        $this->showModal = true;
        $this->resetPage('appointmentsPage'); // importante para resetar a paginação do modal
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedUserId = null;
    }

    public function mount()
    {
        // Carrega os agendamentos dos últimos 6 meses de modo paginado
        $this->agendamentos = \App\Models\Appointment::with(['employee', 'customer', 'branch'])
        ->where('appointment_date', '>=', now()->subMonths(6)->startOfMonth())
        ->get();   
        // Pega os últimos 6 meses
        $months = collect(range(0, 5))->map(function ($i) {
            return now()->subMonths(5 - $i)->format('m/Y');
        });

    // Agrupa por funcionário

    $funcionarios = collect($this->agendamentos)->groupBy('employee.name');


    $datasets = [];
    $colors = ['#3b82f6', '#f59e42', '#10b981', '#ef4444', '#a78bfa', '#f472b6', '#facc15', '#34d399', '#6366f1', '#f87171', '#60a5fa', '#fbbf24'];

    foreach ($funcionarios as $nome => $agendamentos) {
        $data = [];
        
        foreach ($months as $mes) {
            $data[] = $agendamentos->filter(function ($a) use ($mes) {
                return \Carbon\Carbon::parse($a->appointment_date)->format('m/Y') === $mes;
            })->count();
        }
        $datasets[] = [
            'label' => $nome ?: 'Sem nome',
            'data' => $data,
            'backgroundColor' => $colors[array_rand($colors)],
        ];
    }

    $this->chartData = [
        'labels' => $months,
        'datasets' => $datasets,
    ];
    
        
    }
    public function render()
    {
        $appointments = new LengthAwarePaginator([], 0, 5); // paginator vazio
    $user = null;

    if ($this->selectedUserId) {
        $user = User::find($this->selectedUserId);
        if ($user) {
            $appointments = $user->clientAppointments()
                ->orderBy('appointment_date', 'desc')
                ->paginate(5, ['*'], 'appointmentsPage');
        }
    }
     

    return view('livewire.servicos-realizados', [
        'appointments' => $appointments,
        'user' => $user,
        'showModal' => $this->showModal,
        'selectedUserId' => $this->selectedUserId,        
        'chartData' => $this->chartData,
    ]);
    }





}

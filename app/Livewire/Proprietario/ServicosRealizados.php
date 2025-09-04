<?php

namespace App\Livewire\Proprietario;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\BranchUser;

use App\Models\Appointment;
use Illuminate\Support\Facades\DB;  
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ServicosRealizados extends Component
{
    // Usando a trait WithPagination para paginação
    use WithPagination;
    // Propriedades do componente
    public $employees = []; // Lista de funcionários
    public $branches = []; // Lista de filiais
    
    public $selectedEmployee = null; // Funcionário selecionado
    public $selectedBranch = null; // Filial selecionada
    public $selectedDate = null; // Data selecionada 
    public $selectedMonth = null; // Mês selecionado
    public $selectedYear = null; // Ano selecionado     
    public $links; // Links de paginação
    public $showMonthFilter = false; // Exibir filtro de mês
    public $showDateFilter = false; // Exibir filtro de ano
    public $selectedTime = ''; // Hora selecionada
    public $showModal = false; // Exibir modal de detalhes do funcionário
    public $employeeDetails = []; // Detalhes do funcionário selecionado

    
    public function mount()
    {
        // Carregar funcionários e filiais
        $employees_id = BranchUser::pluck('user_id')->toArray();
        $this->employees = User::whereIn('id', $employees_id)->get();
        $this->branches = DB::table('branches')->get();
        $this->selectedMonth = null;
        $this->selectedYear = null;
        
        
        
        // Carregar agendamentos iniciais

        
    }
    public function updatedSelectedTime($value)
    {
        if ($value === 'mes_ano') {
            $this->showMonthFilter = true;
            $this->showDateFilter = false;
        } elseif ($value === 'data') {
            $this->showMonthFilter = false;
            $this->showDateFilter = true;
        } else {
            $this->showMonthFilter = false;
            $this->showDateFilter = false;
        }
    }
    public function showEmployeeDetails($userId)
    {
        // Buscar detalhes do funcionário selecionado
        $this->employeeDetails['nome'] = User::find($userId)->name;
        $this->employeeDetails['email'] = User::find($userId)->email;
        $this->employeeDetails['telefone'] = User::find($userId)->phone;
        $filiais_ids = BranchUser::where('user_id', $userId)->pluck('branch_id')->toArray();
        $this->employeeDetails['filiais'] = DB::table('branches')->whereIn('id', $filiais_ids)->pluck('branch_name')->toArray(); // Obter nomes das filiais do funcionário

        $this->employeeDetails['agendamentos'] = Appointment::where('employee_id', $userId)->count(); // Contar agendamentos do funcionário
        $this->employeeDetails['meses_trabalhados'] = Appointment::where('employee_id', $userId)
            ->select(DB::raw('COUNT(DISTINCT MONTH(appointment_date)) as meses_trabalhados'))
            ->value('meses_trabalhados'); // Contar meses trabalhados do funcionário
        $this->employeeDetails['faturamento_total'] = Appointment::where('employee_id', $userId)
            ->sum('total'); // Somar faturamento total do funcionário
        $this->employeeDetails['tem_agendamentos'] = Appointment::where('employee_id', $userId)->where('appointment_date','>=', now())->exists(); // Verificar se o funcionário tem agendamentos
        $this->employeeDetails['datas_agendamentos'] = Appointment::where('employee_id', $userId)->where('appointment_date', '>=', now())
           // ->select(DB::raw('start_time as hora'))          
            ->get();
          //  dd($this->employeeDetails['datas_agendamentos']);

        // Verificar se o funcionário foi encontrado
        if ($this->employeeDetails) {
            $this->showModal = true; // Exibir modal com detalhes do funcionário


        } else {
            $this->showModal = false; // Ocultar modal se não encontrar o funcionário
        }
    }
    public function closeModal()
    {
        $this->showModal = false; // Fechar modal
        $this->employeeDetails = []; // Limpar detalhes do funcionário
    }
   
    public function resetFilters()
    {
        $this->selectedEmployee = null;
        $this->selectedBranch = null;
        $this->selectedDate = null;
        $this->selectedTime = '';
        $this->selectedMonth = now()->format('m');
        $this->selectedYear = now()->format('Y');
        $this->showMonthFilter = false;
        $this->showDateFilter = false;
        $this->resetPage();
    }

    public function render()
    {
        // Renderizar a view com os dados necessários
        
        $agendamentos = Appointment::with(['employee', 'branch', 'customer'])
            ->when($this->selectedEmployee, fn($query) => $query->where('employee_id', $this->selectedEmployee))
            ->when($this->selectedBranch, fn($query) => $query->where('branch_id', $this->selectedBranch))
            ->when($this->selectedMonth, fn($query) => $query->whereMonth('appointment_date', $this->selectedMonth))  
            ->when($this->selectedYear, fn($query) => $query->whereYear('appointment_date', $this->selectedYear))
            ->when($this->selectedDate, fn($query) => $query->whereDate('appointment_date', $this->selectedDate))
            ->paginate(10);
        if(is_null($agendamentos)) {
            $agendamentos = new LengthAwarePaginator([], 0, 10);
        }
        // Agrupar agendamentos por mês e funcionário
        $grouped = collect($agendamentos->items())->groupBy(function ($item) {
            return Carbon::parse($item->appointment_date)->format('m/Y');
        })->map(function ($group) {
            return $group->groupBy('employee.name')->map(function ($subGroup) {
                return $subGroup->count();
            });
        });
        

        return view('livewire.proprietario.servicos-realizados', [
            'agendamentos' => $agendamentos,
            'grouped' => $grouped,  
            'showMonthFilter' => $this->showMonthFilter,
            'showDateFilter' => $this->showDateFilter,
            'employeeDetails' => $this->employeeDetails,
        ]);
    }

}
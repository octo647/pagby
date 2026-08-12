<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Service;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class PublicBooking extends Component
{
    public $step = 1;
    public $branches = [];
    public $employees = [];
    public $services = [];
    public $availableTimes = [];
    
    public $selectedBranch = null;
    public $selectedEmployee = null;
    public $selectedServices = [];
    public $selectedDate = null;
    public $selectedTime = null;
    
    // Dados do cliente
    public $customerName = '';
    public $customerEmail = '';
    public $customerPhone = '';
    public $customerWhatsapp = false;
    public $observation = '';

    protected $queryString = ['step'];

    public function mount()
    {
        $this->branches = Branch::all();
    }

    public function selectBranch($branchId)
    {
        $this->selectedBranch = $branchId;
        
        // Busca funcionários da filial
        $this->employees = User::whereHas('roles', function($q) {
            $q->where('role', 'Funcionário');
        })
        ->whereHas('branches', function($q) use ($branchId) {
            $q->where('branches.id', $branchId);
        })
        ->where('status', 'Ativo')
        ->get();
        
        $this->step = 2;
    }

    public function selectEmployee($employeeId)
    {
        $this->selectedEmployee = $employeeId;
        
        // Busca serviços do funcionário
        $this->services = User::find($employeeId)->services;
        
        $this->step = 3;
    }

    public function toggleService($serviceId)
    {
        if (in_array($serviceId, $this->selectedServices)) {
            $this->selectedServices = array_diff($this->selectedServices, [$serviceId]);
        } else {
            $this->selectedServices[] = $serviceId;
        }
    }

    public function confirmServices()
    {
        $this->validate([
            'selectedServices' => 'required|array|min:1',
        ]);
        
        $this->step = 4;
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->loadAvailableTimes();
    }

    public function loadAvailableTimes()
    {
        // Log explícito para garantir execução
        \Log::info('PublicBooking: INICIO loadAvailableTimes', [
            'selectedEmployee' => $this->selectedEmployee,
            'selectedDate' => $this->selectedDate,
            'selectedBranch' => $this->selectedBranch,
            'selectedServices' => $this->selectedServices,
            'step' => $this->step,
        ]);

        $employee = User::find($this->selectedEmployee);
        $dayOfWeek = date('l', strtotime($this->selectedDate));
        \Log::info('PublicBooking: dayOfWeek', ['dayOfWeek' => $dayOfWeek]);

        // Log todos os schedules do funcionário para depuração
        $allSchedules = $employee->schedules()->get()->toArray();
        \Log::info('PublicBooking: all employee schedules', $allSchedules);

        $schedule = $employee->schedules()
            ->where('day_of_week', $dayOfWeek)
            ->where('status', 'active')
            ->first();

        if (!$schedule) {
            \Log::warning('PublicBooking: Nenhum schedule encontrado', [
                'employee_id' => $this->selectedEmployee,
                'dayOfWeek' => $dayOfWeek
            ]);
            $this->availableTimes = [];
            return;
        }

        $times = [];
        $start = strtotime($schedule->start_time);
        $end = strtotime($schedule->end_time);

        while ($start < $end) {
            $timeSlot = date('H:i', $start);
            $hasAppointment = Appointment::where('employee_id', $this->selectedEmployee)
                ->where('appointment_date', $this->selectedDate)
                ->where('start_time', $timeSlot)
                ->exists();
            if (!$hasAppointment) {
                $times[] = $timeSlot;
            }
            $start = strtotime('+30 minutes', $start);
        }

        \Log::info('PublicBooking: Horários disponíveis', ['times' => $times]);
        $this->availableTimes = $times;
    }

    public function selectTime($time)
    {
        $this->selectedTime = $time;
        $this->step = 5;
    }

    public function confirmBooking()
    {
        $this->validate([
            'customerName' => 'required|string|max:255',
            'customerEmail' => 'required|email|max:255',
            'customerPhone' => 'required|string|max:20',
        ]);

        // Busca ou cria o cliente
        $customer = User::where('email', $this->customerEmail)->first();
        
        if (!$customer) {
            // Cria novo usuário
            $customer = User::create([
                'name' => $this->customerName,
                'email' => $this->customerEmail,
                'phone' => $this->customerPhone,
                'whatsapp' => $this->customerWhatsapp,
                'password' => Hash::make(substr($this->customerPhone, -4)), // Senha temporária
                'status' => 'Ativo',
                'created_via_public_booking' => true,
            ]);
            
            // Atribui role de Cliente
            $clientRole = Role::where('role', 'Cliente')->first();
            if ($clientRole) {
                $customer->roles()->attach($clientRole->id);
            }
        }

        // Calcula duração total dos serviços
        $totalDuration = Service::whereIn('id', $this->selectedServices)->sum('time');
        $endTime = date('H:i', strtotime($this->selectedTime) + ($totalDuration * 60));

        // Cria o agendamento
        $appointment = Appointment::create([
            'customer_id' => $customer->id,
            'employee_id' => $this->selectedEmployee,
            'branch_id' => $this->selectedBranch,
            'appointment_date' => $this->selectedDate,
            'start_time' => $this->selectedTime,
            'end_time' => $endTime,
            'services' => json_encode($this->selectedServices),
            'status' => 'Pendente',
            'observation' => $this->observation,
        ]);

        $this->step = 6;
        session()->flash('appointment_id', $appointment->id);
    }

    public function back()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function render()
    {
        $tenant = tenant();
        
        return view('livewire.public-booking', [
            'tenantName' => $tenant->id ?? 'Salão',
        ])->layout('layouts.public');
    }
}

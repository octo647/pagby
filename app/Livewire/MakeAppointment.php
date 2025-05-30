<?php


namespace App\Livewire;

use Livewire\Component;
use App\Models\Schedule;
use App\Models\Subscription;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;


class MakeAppointment extends Component
{
    public $chosen_services = [];
    public $forward_days = [];
    public $available_times = [];
    public $selected_day = null;
    public $selected_time = null;
    public $chosen_services_names = [];

    public function selectTime($time)
    {
    $this->selected_time = $time; // Define o horário selecionado
    }

    #[On('ch_services')]// Evento para receber os serviços escolhidos   
    public function chose($ch_services): void
    {
    
    $this->chosen_services = $ch_services;
    $chosen_employee = $ch_services[0]['id'] ?? null;


    if (!$chosen_employee) {// Se não houver funcionário escolhido, limpa os horários e dias disponíveis  
        $this->available_times = [];
        $this->forward_days = [];
        return;
    }

    $total_service_time = $this->calculateTotalServiceTime($ch_services);

    $all_days = $this->getNextDays(7);

    $schedules = Schedule::where('user_id', $chosen_employee)->get();

    // Filtra apenas os dias em que o funcionário tem schedule
    $this->forward_days = array_filter($all_days, function($day) use ($schedules) {
        $weekday = strtolower(date('l', strtotime($day)));
        return $schedules->where('day_of_week', $weekday)->first();
    });

    $appointments = Appointment::where('employee_id', $chosen_employee)
        ->whereIn('appointment_date', $this->forward_days)
        ->get();
    $this->available_times = $this->getAvailableTimes($schedules, $appointments, $total_service_time);
    // Verifica se o usuário tem uma assinatura ativa
if (!Auth::check()) {
     $this->addError('auth', 'Você precisa estar logado para agendar.');
    return;
} 
{{dd($ch_services);}}
 $subscription = Subscription::where('user_id', Auth::id())
    ->where('branch_id', $ch_services[0]['branch_id'])
    ->where('start_date', '<=', now())
    ->where('end_date', '>=', now())
    ->first();

// Verifica se a assinatura existe e se o serviço está incluído no plano    

$plan_services = $subscription->plan->services ?? [];
$chosen_service_ids = array_column($ch_services, 'id');
$all_included = !array_diff($chosen_service_ids, $plan_services);

if (!$subscription || !$all_included) {
    $this->addError('subscription', 'Você não possui uma assinatura ativa ou o serviço escolhido não está incluído no seu plano.');
    return;
}
else {
    // Se a assinatura for válida, permite o agendamento
    $this->dispatch('subscriptionValid', ['subscription' => $subscription]);
    $this->dispatch('servicesChosen', ['services' => $ch_services]);
    $this->dispatch('availableTimesUpdated', ['available_times' => $this->available_times]);
    $this->dispatch('forwardDaysUpdated', ['forward_days' => $this->forward_days]);
    $this->dispatch('chosenServicesUpdated', ['chosen_services' => $ch_services]);
    $this->dispatch('chosenServicesNamesUpdated', ['chosen_services_names' => $this->chosen_services_names]);
    $this->dispatch('selectedDayUpdated', ['selected_day' => $this->selected_day]);
    $this->dispatch('selectedTimeUpdated', ['selected_time' => $this->selected_time]);  
}
}
   
    protected function calculateTotalServiceTime($services)
    {
        $total = 0;
        foreach ($services as $service) {
           
            $total += $service['time'] ?? 0;
      
        }        
        return $total;        
    }

    protected function getNextDays($days = 7)
    {
        $forward_days = [];
        for ($j = 0; $j < $days; $j++) {
            $forward_days[] = date('Y-m-d', strtotime("+$j day"));
        }
        return $forward_days;
    }
    protected function getAvailableTimes($schedules, $appointments, $service_time)
    {
    $available = [];

    foreach ($this->forward_days as $day) {
        $weekday = strtolower(date('l', strtotime($day)));
        $schedule = $schedules->where('day_of_week', $weekday)->first();

        if (!$schedule) {
            $available[$day] = [];
            continue;
        }

        $start_time = strtotime($day . ' ' . $schedule->start_time);

        $end_time = strtotime($day . ' ' . $schedule->end_time);
        

        // Pega o horário de almoço do funcionário (se existir)
        $lunch_start = $schedule->lunch_start ? strtotime($day . ' ' . $schedule->lunch_start) : null;
        $lunch_end = $schedule->lunch_end ? strtotime($day . ' ' . $schedule->lunch_end) : null;

         

 


$busy = $appointments->filter(function($a) use ($day) {
    if ($a->appointment_date instanceof \Carbon\Carbon) {
        return $a->appointment_date->format('Y-m-d') === $day;
    }
    return date('Y-m-d', strtotime($a->appointment_date)) === $day;
});
    

$busy_slots = $busy->map(function($a) use ($day) {
    $start = strtotime($day . ' ' . (is_object($a->start_time) ? $a->start_time->format('H:i:s') : $a->start_time));
    $end = strtotime($day . ' ' . (is_object($a->end_time) ? $a->end_time->format('H:i:s') : $a->end_time));
    return [
        'start' => $start,
        'end' => $end,
    ];
})->filter(function($b) {
    return $b['start'] !== false && $b['end'] !== false;
})->values()->toArray();


    // Filtra apenas os agendamentos que têm horário de término
    // e converte para o formato de array com 'start' e 'end'


        $slots = [];
        for ($slot = $start_time; $slot + ($service_time * 60) <= $end_time; $slot += 15 * 60) {
            $slot_end = $slot + ($service_time * 60);
            //Ignora horários passados no dia de hoje
            //dd($day, date('Y-m-d'), $slot, time());
            if ($day === date('Y-m-d') && $slot < time()) {
            continue;
            }
            

            // Pular intervalo de almoço (se definido)
            if ($lunch_start && $lunch_end && ($slot < $lunch_end && $slot_end > $lunch_start)) {
                continue;
            }

            // Verifica conflito com agendamentos
            $conflict = false;
           
            foreach ($busy_slots as $b) {                
                
                if (($slot < $b['end']) && ($slot_end > $b['start'])) {
                    $conflict = true;
                    break;
                }
            }
            if (!$conflict) {
                $slots[] = [date('H:i', $slot), date('H:i', $slot_end)];
            }
        }
        $available[$day] = $slots;
    }

    return $available;
    
   

}
public function confirmTime()
{
    if (!$this->selected_day || !$this->selected_time) {
        $this->addError('selected_time', 'Selecione um dia e horário.');
        return;
    }

    // Recupera o funcionário escolhido
    $employee_id = $this->chosen_services[0]['id'];
    $employee = User::find($employee_id);
    // Verifica se o funcionário tem branch associado
    $branch = $employee->branches()->first();
    
    if (!$branch) {
        $this->addError('branch_id', 'Não foi possível identificar o salão do funcionário.');
        return;
    }

    // Recupera o branch_id do relacionamento do funcionário
    $branch_id = $employee->branches[0]->pivot->branch_id ?? null;
    
    // Verifica se o branch_id é válido
    if (!$branch_id) {
        $this->addError('branch_id', 'Não foi possível identificar o salão do funcionário.');
        return;
    }
     $service_names = [];

    // Verifica os serviços escolhidos
    if (empty($this->chosen_services)) {
        $this->addError('chosen_services', 'Selecione pelo menos um serviço.');
        return;
    }
    else
    {
        foreach ($this->chosen_services as $item =>$service) {
            if($item>0){
                $service_model = Service::find($service);
            
                if ($service_model) {
                    $service_names[] = $service_model[0]->service;
                };
            }
        }
        // Junta os nomes em uma string separada por vírgula
        $services_string = implode(', ', $service_names);
    }
    


    // Insere os campos conforme a tabela appointments
    
    Appointment::create([
        'employee_id' => $this->chosen_services[0]['id'],
        'branch_id' => $branch_id,
        'customer_id' => Auth::id(), 
        'services' => $services_string,
        'appointment_date' => $this->selected_day,
        'start_time' => $this->selected_time,
        'end_time' => date('H:i', strtotime($this->selected_time) + ($this->calculateTotalServiceTime($this->chosen_services) * 60)),
        'total' => array_sum(array_column($this->chosen_services, 'price')),
        'notes' => 'Agendamento realizado via sistema', // ou outro campo de notas
        'updated_by' => Auth::id(), // atualizado pelo cliente ou outro identificador do atualizador
        'created_by' => Auth::id(), // criado pelo cliente ou outro identificador do criador
        'status' => 'confirmed',
    ]);

    session()->flash('success', 'Agendamento realizado com sucesso!');
    // Opcional: Redirecionar ou emitir evento
     return redirect()->route('dashboard');
}

    public function render()
    {
        return view('livewire.make-appointment', [
            'available_times' => $this->available_times,
            'forward_days' => $this->forward_days,
        ]);
    }
}
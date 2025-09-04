<?php


namespace App\Livewire\Cliente;

use Livewire\Component;
use App\Models\Schedule;
use App\Models\Subscription;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use App\Models\Plan;


class MakeAppointment extends Component
{
    public $chosen_services = [];
    public $forward_days = [];
    public $days_with_appointments = []; // Dias que já têm agendamentos do cliente
    public $available_times = [];
    public $selected_day = null;
    public $selected_time = null;
    public $chosen_services_names = [];
    public $chosen_service_ids = [];
    public $plan_services = [];
    public $plan_additional_services = [];
    public $additional_services = [];
    public $total = 0;
    public $ch_professional = null;
    public $services_string = '';
    public $allowed_days = []; // Dias para agendamento
    public $ch_services = [];
    public $ch_professional_id = null;


    public function selectTime($time)
    {
    $this->selected_time = $time; // Define o horário selecionado
    }
    
    #[On('ch_professional')]
    public function setProfessional($professional)
    {
        $this->ch_professional = User::where('id', $professional)
            ->with('branches')
            ->first();           
       
    }

    #[On('ch_services')]
    public function setServices($services)
    {
        $this->ch_services = Service::whereIn('id', $services)
            ->get()
            ->pluck('id')
            ->toArray(); // Pluck apenas os IDs dos serviços escolhidos
            $this->chosen_service_ids = $services;
       
        $this->escolhidos(); // Chama o método escolhidos() para processar os serviços escolhidos
    }
    
    public function escolhidos(): void
    {
    // Recebe os serviços e o profissional escolhidos

  
    $ch_services = $this->ch_services;  //ids dos serviços escolhidos  
    $ch_professional = $this->ch_professional;//coleção do profissional escolhido
   
    // Verifica se o profissional e os serviços foram escolhidos
    if (empty($ch_professional)) {
        $this->addError('professional', 'Por favor, escolha um funcionário.');
        return;
    }
    if (empty($ch_services)) {
        $this->addError('services', 'Por favor, escolha pelo menos um serviço.');
        return;
    }    
    // Verifica se o usuário está autenticado
    // Se não estiver logado, exibe mensagem de erro

    if (!Auth::check()) {
        $this->addError('auth', 'Você precisa estar logado para agendar.');
        return;
    } 
   
    // Verifica se os serviços escolhidos são válidos e os nomes dos serviços
    $service_names = Service::whereIn('id', $ch_services)->pluck('service')->toArray();
    $this->services_string = implode(', ', $service_names);
   
    $ch_professional_id = $ch_professional->id ?? null;
    $this->ch_professional_id = $ch_professional->id;
    
    

    // Verifica se o funcionário foi escolhido
    if (!$ch_professional_id || !is_numeric($ch_professional_id)) {
        $this->addError('employee', 'Por favor, escolha um funcionário.');
        return;
    }
   // Verifica se os serviços foram escolhidos
    if (!$ch_professional_id) {// Se não houver funcionário escolhido, limpa os horários e dias disponíveis  
        $this->available_times = [];
        $this->forward_days = [];
        return;
    }
    // Calcula o tempo total dos serviços escolhidos
    $total_service_time = $this->calculateTotalServiceTime($ch_services);
    //Obtém os próximos 7 dias
    $all_days = $this->getNextDays(7);
    // Obtém os horários disponíveis do funcionário
    $schedules = Schedule::where('user_id', $ch_professional_id)->get();
    // Filtra apenas os dias em que o funcionário tem schedule
    $this->forward_days = array_filter($all_days, function($day) use ($schedules) {
        $weekday = date('l', strtotime($day));        
        return $schedules->where('day_of_week', $weekday)->first();
    });
    // Se não houver dias disponíveis, limpa os horários e retorna
    if (empty($this->forward_days)) {
        $this->available_times = [];
        $this->addError('no_available_days', 'Não há dias disponíveis para agendamento com o profissional escolhido.');
        return;
    }
    // Obtém os agendamentos do funcionário nos próximos 7 dias
    $appointments = Appointment::where('employee_id', $ch_professional_id)
        ->whereIn('appointment_date', $this->forward_days)
        ->get();
    
    $this->available_times = $this->getAvailableTimes($schedules, $appointments, $total_service_time);

    //verifica se há algum plano ativo disponível para o usuário
    $existePlano = Plan::where('active', true)->exists();
   
    
    // Verifica se o usuário tem assinatura ativa
    if($existePlano){
    $subscription = Subscription::where('user_id', Auth::id())        
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now())
        ->where('status', 'Ativo')
        ->with('plan.additionalServices') // Inclui os serviços adicionais do plano
        ->first();
        if(!$subscription) {
            $this->addError('subscription', 'Você não possui uma assinatura ativa. Você gostaria de conhecer nossos planos?
            <a href="'.route('dashboard', ['tabelaAtiva' => 'planos-de-assinatura']).'" class="underline text-blue-600 hover:text-blue-900">Clique aqui</a> para ver nossos planos.');
            return;
        }
    }
    else {
        $subscription = null; // Se não houver plano, define como nulo
    }
    

    if ($subscription && $subscription->plan && $subscription->plan->allowed_days) {
    // Garante que é array e normaliza para minúsculo/sem acento
    $this->allowed_days = array_map(function($d) {
        return iconv('UTF-8', 'ASCII//TRANSLIT', strtolower($d));
    }, $subscription->plan->allowed_days);
    } else {
        $this->allowed_days = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'];// Se não houver plano, permite todos os dias
    }   


    // Verifica se a assinatura existe e se o serviço está incluído no plano
    
    
    $plan_services = ($subscription && $subscription->plan)
    ? $subscription->plan->services->pluck('id')->toArray()
    : [];// ids dos serviços do plano

    $plan_additional_services = ($subscription && $subscription->plan)
    ? $subscription->plan->additionalServices : []; //coleção de serviços adicionais do plano com os descontos associados  
   
    $this->plan_services = $plan_services;
    
    $this->plan_additional_services = $plan_additional_services;
    $additional_services = [];
    foreach ($this->plan_additional_services as $index=>$plan_additional_service) {
        //dd($plan_additional_service);
        $additional_services[] = [
            'id' => $plan_additional_service->id,
            'name' => $plan_additional_service->service,
            'discount' => $plan_additional_service->pivot->discount ?? 0, // Desconto em porcentagem
        ];
    }
    $this->additional_services = $additional_services;
    //dd($this->additional_services);
    $chosen_service_ids = $ch_services;
    


    // Verifica se o usuário tem serviços incluídos na assinatura
    $all_included = !array_diff($chosen_service_ids, $plan_services);
  // dd($chosen_service_ids);
    if ($subscription && !$all_included ) {
        //dd($subscription, $all_included);
        $this->addError('subscription', 'Pelo menos um dos serviços escolhidos não está incluído no seu plano.');
        return;
    }
    // Se o usuário não tem assinatura ou se a assinatura não cobre todos os serviços escolhidos, exibe mensagem de erro
    
    if ($subscription && $all_included) {
        // Se a assinatura for válida, permite o agendamento
        session()->flash("assinatura-valida", 'O serviço está incluído em sua assinatura. Você pode prosseguir com o agendamento.');
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
   
    protected function calculateTotalServiceTime($ch_services)
    {
        
        $total = 0;
        $services = Service::whereIn('id', $ch_services)->get();
        foreach ($services as $service) {
        $total += $service->time ?? 0;
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
        $weekday = date('l', strtotime($day));
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
    try {
        if (!$this->selected_day || !$this->selected_time) {
            $this->addError('selected_time', 'Selecione um dia e horário.');
            return;
        }
        
        if (!$this->ch_professional_id) {
            $this->addError('professional', 'Selecione um funcionário.');
            return;
        }
        
        if (empty($this->ch_services)) {
            $this->addError('services', 'Selecione pelo menos um serviço.');
            return;
        }
        
        // Verificar se o cliente já tem agendamento na data selecionada
        $existing_appointment = \App\Models\Appointment::where('customer_id', \Illuminate\Support\Facades\Auth::id())
            ->where('appointment_date', $this->selected_day)
            ->whereIn('status', ['Confirmado', 'Pendente'])
            ->first();
            
        if ($existing_appointment) {
            $this->addError('selected_day', 'Você já possui um agendamento marcado para esta data. Apenas um agendamento por dia é permitido.');
            return;
        }
        
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('ERRO no confirmTime: ' . $e->getMessage());
        $this->addError('general', 'Erro interno. Tente novamente.');
        return;
    }
    
    $weekday_en = strtolower(date('l', strtotime($this->selected_day)));
$dias_pt = [
    'monday' => 'segunda',
    'tuesday' => 'terca',
    'wednesday' => 'quarta',
    'thursday' => 'quinta',
    'friday' => 'sexta',
    'saturday' => 'sabado',
    'sunday' => 'domingo',
];
$weekday_pt = $dias_pt[$weekday_en] ?? $weekday_en;
 
    
    
   
    $branch = User::find($this->ch_professional_id)?->branches()->first();
    $branch_id = $branch->id ?? null;
     
    if (!$branch) {
        $this->addError('branch_id', 'Não foi possível identificar o salão do funcionário.');
        return;
    }
   

    $subscription = Subscription::where('user_id', Auth::id())
    ->where('start_date', '<=', now())
    ->where('end_date', '>=', now())
    ->where('status', 'Ativo')
    ->with('plan')
    ->first();
    // verifica os dias permitidos para agendamento
    $this->allowed_days = $subscription && $subscription->plan && $subscription->plan->allowed_days
    ? $subscription->plan->allowed_days
    : [];
    
    $allowed_days = array_map('strtolower', $this->allowed_days);
    // Verifica se o dia selecionado está entre os dias permitidos

    //$fora_do_plano = true;
    // Se não há assinatura, permita todos os dias
    if (empty($allowed_days)) {
        $fora_do_plano = false;
    } else {
        $fora_do_plano = !in_array($weekday_pt, $allowed_days);
    }

    if ($fora_do_plano) {
        // Exemplo: apenas alerta, mas permite continuar
        session()->flash('warning', 'Atenção: seu plano não cobre agendamento neste dia. O serviço será cobrado normalmente.');
        // Você pode também definir uma flag para cobrar o valor normal
    }


    // Verifica se a assinatura existe e se o serviço está incluído no plano
    
    $plan_services = ($subscription && $subscription->plan)
    ? $subscription->plan->services->pluck('id')->toArray()
    : [];
    
    $chosen_service_ids = $this->ch_services ?? [];
    
    // Verifica se o usuário tem serviços incluídos na assinatura
    
    

    $included_services = $subscription && $subscription->plan ? $subscription->plan->services->pluck('id')->toArray() : [];
    $additional_services_ids = ($subscription && $subscription->plan && $subscription->plan->additionalServices)
    ? $subscription->plan->additionalServices->pluck('id')->toArray()
    : [];
   
    
   

   

    
    $this->chosen_service_ids = array_map('intval', $chosen_service_ids);
    $this->plan_services = $subscription && $subscription->plan
    ? $subscription->plan->services->pluck('id')->map(fn($id) => (int)$id)->toArray()
    : [];
    $total = 0;
    
    foreach ($this->ch_services as $index=>$service) {
        $service_model = Service::find($service);
    
        if (!$service_model) continue;

        $service_names[] = $service_model->service;

        // Se for fora do plano, cobra todos os serviços normalmente
        if ($fora_do_plano) {
             session()->flash('warning', 'Atenção: seu plano não cobre agendamento neste dia. O serviço será cobrado normalmente.');
            $total += $service_model->price;
            continue;
        }

        // Serviço incluído no plano: grátis
        if (in_array($service_model->id, $included_services)) {
            continue;
        }

        // Serviço adicional com desconto
        $discount = 0;
        foreach ($this->additional_services as $add) {
            
            
        if (          
            $add['id'] == $service_model->id

        ) {
            // Remove o % e converte para float
            $discount = floatval(str_replace('%', '', $add['discount']));
        
            //break;
        }
    }

    $price = $service_model->price;
    if ($discount > 0) {
        $price = $price - ($price * ($discount / 100));
    }

    $total += $price;
}
$requireAdvance = $branch->require_advance_payment ?? false;
//dd($requireAdvance, $total, $branch->require_advance_payment);

if ($total > 0 && $requireAdvance) {
    // Salve os dados do agendamento em sessão ou como "pendente"
    session()->put('pending_appointment', [
        'services' => $this->services_string,
        'total' => $total,
        'employee_id' => $this->ch_professional_id,
        'customer_id' => Auth::id(),
        'branch_id' => $branch_id,
        'appointment_date' => $this->selected_day,
        'start_time' => $this->selected_time,
        'end_time' => date('H:i', strtotime($this->selected_time) + ($this->calculateTotalServiceTime($this->ch_services) * 60)),
        'notes' => 'Agendamento realizado via sistema',
        'updated_by' => Auth::id(),
        'created_by' => Auth::id(),         
    ]);
    
    // Redirecione para a página de pagamento
    return $this->redirect(route('payment.page'), navigate: true);
}
    
   

    
    $employee = User::find($this->ch_professional_id);
    $branch = $this->ch_professional?->branches()->first();

    if (!$branch) {
        $this->addError('branch_id', 'Não foi possível identificar o salão do funcionário.');
        return;
    }

    

    if (!$branch_id) {
        $this->addError('branch_id', 'Não foi possível identificar o salão do funcionário.');
        return;
    }

    // Monta nomes dos serviços
    
    $services_string = implode(', ', $service_names);

    Appointment::create([
        'employee_id' => $this->ch_professional_id,
        'branch_id' => $branch_id,
        'customer_id' => Auth::id(),
        'services' => $services_string,
        'appointment_date' => $this->selected_day,
        'start_time' => $this->selected_time,
        'end_time' => date('H:i', strtotime($this->selected_time) + ($this->calculateTotalServiceTime($this->ch_services) * 60)),
        'total' => $total,
        'notes' => 'Agendamento realizado via sistema',
        'updated_by' => Auth::id(),
        'created_by' => Auth::id(),
        'status' => 'Confirmado',
    ]);

    session()->flash('success', 'Agendamento realizado com sucesso!');
    return $this->redirect(route('tenant.dashboard'), navigate: true);
}
public function render()
{
    // Buscar dias que já têm agendamentos do cliente atual
    $this->days_with_appointments = \App\Models\Appointment::where('customer_id', \Illuminate\Support\Facades\Auth::id())
        ->whereIn('status', ['Confirmado', 'Pendente'])
        ->pluck('appointment_date')
        ->map(function($date) {
            return date('Y-m-d', strtotime($date));
        })
        ->toArray();

    return view('livewire.cliente.make-appointment', [
        'chosen_service_ids' => $this->chosen_service_ids,
        'plan_services' => $this->plan_services,
        'allowed_days' => $this->allowed_days,
        'days_with_appointments' => $this->days_with_appointments,
        // ...outras variáveis simples...
    ]);
}
  
}
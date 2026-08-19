<?php


namespace App\Livewire\Cliente;

use Livewire\Component;
use App\Models\Schedule;
use App\Models\Subscription;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;
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
    
    // Fidelidade - Produtos e Recompensas
    public $produtosSelecionados = [];
    public $recompensasSelecionadas = []; // Array para múltiplas recompensas
    public $descontoRecompensa = 0;
    public $recompensasDisponiveis = []; // Banner topo (3 mais próximas)
    public $recompensasAtivasCompletas = []; // Lista completa para seleção

    protected $listeners = [
        'produtosSelecionados' => 'atualizarProdutos',
    ];

    public function mount()
    {
        // Restaurar dados de agendamento da sessão após login
        if (Auth::check() && session()->has('booking_data')) {
            $data = session('booking_data');
            
            // Verificar se os dados não são muito antigos (5 minutos)
            if (isset($data['timestamp']) && (now()->timestamp - $data['timestamp']) < 300) {
                // Restaurar IDs e dados básicos
                $this->ch_professional_id = $data['professional'];
                $this->ch_services = $data['services'];
                $this->chosen_service_ids = $data['services'];
                $this->selected_day = $data['date'];
                $this->selected_time = $data['time'];
                
                // Carregar profissional com relacionamentos
                $professional = User::where('id', $data['professional'])
                    ->with('branches')
                    ->first();
                    
                if ($professional) {
                    $this->ch_professional = $professional;
                    
                    // Calcular horários disponíveis antes de limpar a sessão
                    $this->escolhidos();
                    
                    // Disparar eventos para atualizar componentes filhos
                    $this->dispatch('professional-restored', professional: $this->ch_professional_id);
                    $this->dispatch('services-restored', services: $this->ch_services);
                    
                    // Disparar evento window para Alpine.js (múltiplas tentativas para garantir que Alpine está pronto)
                    $this->js("
                        const eventData = {
                            professional: {$this->ch_professional_id},
                            services: " . json_encode($this->ch_services) . ",
                            date: '{$this->selected_day}',
                            time: '{$this->selected_time}'
                        };
                        
                        function dispatchRestoreEvent() {
                            window.dispatchEvent(new CustomEvent('booking-data-restored', { 
                                detail: eventData
                            }));
                        }
                        
                        dispatchRestoreEvent();
                        setTimeout(dispatchRestoreEvent, 100);
                        setTimeout(dispatchRestoreEvent, 300);
                    ");
                    
                    // Limpar da sessão
                    session()->forget('booking_data');
                    session()->forget('requires_login_for_booking');
                } else {
                    \Log::error('Booking data restoration failed: Professional not found', ['id' => $data['professional']]);
                }
            } else {
                \Log::warning('Booking data expired or invalid timestamp');
            }
            
            // Limpar da sessão se houve erro
            if (!isset($professional) || !$professional) {
                session()->forget('booking_data');
                session()->forget('requires_login_for_booking');
            }
        }
        
        // Carregar recompensas disponíveis do usuário
        $this->carregarRecompensas();
    }
    
    public function carregarRecompensas()
    {
        if (!auth()->check()) {
            $this->recompensasDisponiveis = [];
            $this->recompensasAtivasCompletas = [];
            return;
        }

        // Lista completa de recompensas ativas
        $this->recompensasAtivasCompletas = \App\Models\FidelidadeReward::where('user_id', auth()->id())
            ->where('status', 'ativo')
            ->where('validade', '>=', now())
            ->where('valor_credito', '>', 0) // Excluir recompensas com valor zero
            ->orderBy('validade', 'asc') // Prioriza recompensas que expiram primeiro
            ->get()
            ->map(function($reward) {
                return [
                    'id' => $reward->id,
                    'tipo' => $reward->tipo,
                    'valor_credito' => $reward->valor_credito,
                    'codigo_cupom' => $reward->codigo_cupom,
                    'percentual_desconto' => $reward->percentual_desconto,
                    'servico_gratis_id' => $reward->servico_gratis_id,
                    'validade' => $reward->validade->format('d/m/Y'),
                    'dias_restantes' => max(0, (int) now()->diffInDays($reward->validade->endOfDay(), false)),
                    'origem' => $reward->origem,
                ];
            })
            ->toArray();
        
        // Banner topo - apenas 3 mais próximas de expirar
        $this->recompensasDisponiveis = array_slice($this->recompensasAtivasCompletas, 0, 3);
    }
    
    public function toggleRecompensa($recompensaId)
    {
        if (in_array($recompensaId, $this->recompensasSelecionadas)) {
            // Desselecionar - remover do array
            $this->recompensasSelecionadas = array_values(
                array_filter($this->recompensasSelecionadas, fn($id) => $id !== $recompensaId)
            );
        } else {
            // Selecionar - adicionar ao array
            $this->recompensasSelecionadas[] = $recompensaId;
        }
        
        // Recalcular desconto total
        $this->descontoRecompensa = collect($this->recompensasAtivasCompletas)
            ->whereIn('id', $this->recompensasSelecionadas)
            ->where('tipo', 'credito')
            ->sum('valor_credito');
    }


    public function selectTime($time)
    {
        $this->selected_time = $time;
    }
    
    public function forceRecalculate()
    {
        if ($this->ch_professional && $this->ch_services && !empty($this->ch_services)) {
            $this->escolhidos();
        }
    }
    
    #[On('ch_professional')]
    public function setProfessional($professional)
    {
        $this->ch_professional = User::where('id', $professional)
            ->with('branches')
            ->first();
        
        $this->ch_professional_id = $professional;
    }

    #[On('ch_services')]
    public function setServices($services)
    {
        $this->ch_services = Service::whereIn('id', $services)
            ->get()
            ->pluck('id')
            ->toArray();
        $this->chosen_service_ids = $services;
        
        $this->escolhidos();
    }
    
    public function escolhidos(): void
    {
    $ch_services = $this->ch_services;
    $ch_professional = $this->ch_professional;
   
    // Verifica se o profissional e os serviços foram escolhidos
    if (empty($ch_professional)) {
        $this->addError('professional', 'Por favor, escolha um funcionário.');
        return;
    }
    if (empty($ch_services)) {
        $this->addError('services', 'Por favor, escolha pelo menos um serviço.');
        return;
    }    
    
    // NOTA: A verificação de autenticação foi movida para o método de confirmar agendamento
    // para permitir que usuários não autenticados vejam horários disponíveis
   
    // Verifica se os serviços escolhidos são válidos e os nomes dos serviços
    $service_names = Service::whereIn('id', $ch_services)->pluck('service')->toArray();
    $this->services_string = implode(', ', $service_names);
   
    if (!$this->ch_professional_id && $ch_professional) {
        $this->ch_professional_id = $ch_professional->id;
    }
    
    $ch_professional_id = $this->ch_professional_id;

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
    //Obtém os próximos 21 dias
    $all_days = $this->getNextDays(21);
    // Obtém os horários disponíveis do funcionário
    $schedules = Schedule::where('user_id', $ch_professional_id)->get();
    // Mantém todos os 21 dias, mas marca os dias sem agenda como indisponíveis na view
    $this->forward_days = $all_days;
    // Se não houver dias disponíveis, limpa os horários e retorna
    if (empty($this->forward_days)) {
        $this->available_times = [];
        $this->addError('no_available_days', 'Não há dias disponíveis para agendamento com o profissional escolhido.');
        return;
    }
    // Obtém os agendamentos do funcionário nos próximos 7 dias (excluindo cancelados)
    $appointments = Appointment::where('employee_id', $ch_professional_id)
        ->whereIn('appointment_date', $this->forward_days)
        ->where('status', '!=', 'Cancelado')
        ->get();
    
    $this->available_times = $this->getAvailableTimes($schedules, $appointments, $total_service_time);

    $existePlano = Plan::where('active', true)->exists();
    
    $subscription = null;
    if($existePlano && Auth::check()){
        $subscription = Subscription::where('user_id', Auth::id())        
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->with('plan.additionalServices')
            ->first();
    }
    
    // Se não houver plano ativo no sistema ou usuário não tiver subscription,
    // permite agendamento em todos os dias (será cobrado por serviço)
    

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

    protected function getNextDays($days = 21)
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
        // Verificar autenticação antes de confirmar o agendamento
        if (!Auth::check()) {
            if (!$this->ch_professional_id || empty($this->ch_services) || !$this->selected_day || !$this->selected_time) {
                $this->addError('general', 'Dados incompletos. Selecione profissional, serviços, dia e horário.');
                return;
            }
            
            // Salvar na sessão
            $bookingData = [
                'professional' => $this->ch_professional_id,
                'services' => $this->ch_services,
                'date' => $this->selected_day,
                'time' => $this->selected_time,
                'timestamp' => now()->timestamp,
            ];
            
            session()->put('booking_data', $bookingData);
            session()->put('requires_login_for_booking', true);
            
            return redirect()->route('tenant.login')->with('message', 'Faça login para confirmar seu agendamento');
        }
        
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
            ->where('status', 'Pendente')
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
// Removido: lógica de pagamento antecipado e sessão. Sempre cria o agendamento no banco.
    
   

    
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

    // ========================================
    // APLICAR RECOMPENSAS (múltiplas)
    // ========================================
    $descontoAplicado = 0;
    $recompensasUtilizadas = [];
    $totalRestante = $total;
    
    if (!empty($this->recompensasSelecionadas)) {
        // Buscar recompensas completas do banco e ordenar por validade
        $recompensasOrdenadas = \App\Models\FidelidadeReward::whereIn('id', $this->recompensasSelecionadas)
            ->where('status', 'ativo')
            ->where('validade', '>=', now())
            ->where('valor_credito', '>', 0) // Excluir recompensas com valor zero
            ->orderBy('validade', 'asc') // Prioriza as que expiram primeiro
            ->get();
        
        \Log::info('Recompensas ordenadas por validade', [
            'total' => $recompensasOrdenadas->count(),
            'ordem' => $recompensasOrdenadas->pluck('id')->toArray()
        ]);
        
        foreach ($recompensasOrdenadas as $recompensa) {
            if ($totalRestante <= 0) {
                // Já aplicou desconto suficiente
                break;
            }
            
            // Verificar se ainda é válida
            if ($recompensa->status !== 'ativo' || $recompensa->validade < now()) {
                \Log::warning('Recompensa inválida ou expirada', [
                    'recompensa_id' => $recompensa->id
                ]);
                continue;
            }
            
            $descontoAtual = 0;
            
            // Calcular desconto baseado no tipo
            if ($recompensa->tipo === 'credito') {
                // Crédito: abater do total restante (limitado ao valor total)
                $descontoAtual = min($recompensa->valor_credito, $totalRestante);
                $saldoRestante = $recompensa->valor_credito - $descontoAtual;
                
                if ($saldoRestante > 0) {
                    // Há saldo restante - atualizar valor e manter ativa
                    $recompensa->update([
                        'valor_credito' => $saldoRestante,
                        // Mantém status 'ativo' para uso futuro
                    ]);
                    
                    \Log::info('Crédito parcialmente utilizado', [
                        'recompensa_id' => $recompensa->id,
                        'valor_original' => $recompensa->valor_credito + $descontoAtual,
                        'desconto_aplicado' => $descontoAtual,
                        'saldo_restante' => $saldoRestante,
                    ]);
                } else {
                    // Crédito totalmente utilizado - marcar como usado
                    $recompensa->update([
                        'valor_credito' => 0,
                        'status' => 'usado',
                        'usado_em' => now(),
                    ]);
                    
                    \Log::info('Crédito totalmente utilizado', [
                        'recompensa_id' => $recompensa->id,
                        'valor_utilizado' => $descontoAtual,
                    ]);
                }
                
                $totalRestante -= $descontoAtual;
                $descontoAplicado += $descontoAtual;
                
            } elseif ($recompensa->tipo === 'cupom') {
                // Cupom: aplicar percentual no total restante (uso único)
                $descontoAtual = $totalRestante * ($recompensa->percentual_desconto / 100);
                
                // Cupons são sempre de uso único
                $recompensa->update([
                    'status' => 'usado',
                    'usado_em' => now(),
                ]);
                
                $totalRestante -= $descontoAtual;
                $descontoAplicado += $descontoAtual;
                
            } elseif ($recompensa->tipo === 'servico_gratis') {
                // Serviço grátis: 100% de desconto no total restante (uso único)
                $descontoAtual = $totalRestante;
                
                // Serviço grátis é sempre de uso único
                $recompensa->update([
                    'status' => 'usado',
                    'usado_em' => now(),
                ]);
                
                $totalRestante = 0;
                $descontoAplicado += $descontoAtual;
            }
            
            $recompensasUtilizadas[] = [
                'id' => $recompensa->id,
                'tipo' => $recompensa->tipo,
                'desconto' => $descontoAtual,
            ];
            
            \Log::info('Recompensa aplicada', [
                'recompensa_id' => $recompensa->id,
                'tipo' => $recompensa->tipo,
                'desconto_aplicado' => $descontoAtual,
                'total_restante' => $totalRestante,
            ]);
        }
        
        // Atualizar total final
        $total = max(0, $totalRestante);
        
        \Log::info('Todas as recompensas processadas', [
            'total_recompensas' => count($recompensasUtilizadas),
            'desconto_total_aplicado' => $descontoAplicado,
            'total_final' => $total,
        ]);
    }

    $appointment = Appointment::create([
        'employee_id' => $this->ch_professional_id,
        'branch_id' => $branch_id,
        'customer_id' => Auth::id(),
        'services' => $services_string,
        'appointment_date' => $this->selected_day,
        'start_time' => $this->selected_time,
        'end_time' => date('H:i', strtotime($this->selected_time) + ($this->calculateTotalServiceTime($this->ch_services) * 60)),
        'total' => $total,
        'notes' => 'Agendamento realizado via sistema' . 
                   (!empty($recompensasUtilizadas) ? " | Recompensas aplicadas: " . count($recompensasUtilizadas) . " (Total desconto: R$ " . number_format($descontoAplicado, 2, ',', '.') . ")" : ''),
        'updated_by' => Auth::id(),
        'created_by' => Auth::id(),
        'status' => 'Pendente',
    ]);
    
    // Criar comanda automaticamente após criar o agendamento
    try {
        \Log::info('📋 Iniciando criação de comanda', [
            'appointment_id' => $appointment->id,
            'produtos_selecionados' => $this->produtosSelecionados,
            'total_produtos' => count($this->produtosSelecionados ?? []),
        ]);
        
        $comanda = \App\Models\Comanda::criarDeAgendamento($appointment, 'Comanda criada automaticamente ao agendar.');
        
        \Log::info('✅ Comanda criada com sucesso', [
            'comanda_id' => $comanda->id,
            'numero_comanda' => $comanda->numero_comanda,
        ]);
        
        // ========================================
        // REGISTRAR USO DAS RECOMPENSAS NA COMANDA
        // ========================================
        if (!empty($recompensasUtilizadas) && $comanda) {
            foreach ($recompensasUtilizadas as $recompensaData) {
                $recompensa = \App\Models\FidelidadeReward::find($recompensaData['id']);
                
                // Só registrar na comanda se foi totalmente utilizada
                // (crédito parcial ainda pode ser usado em outra comanda)
                if ($recompensa && $recompensa->status === 'usado') {
                    $recompensa->update([
                        'usado_em_comanda_id' => $comanda->id,
                    ]);
                    
                    \Log::info('✅ Recompensa registrada na comanda', [
                        'recompensa_id' => $recompensa->id,
                        'tipo' => $recompensa->tipo,
                        'desconto' => $recompensaData['desconto'],
                        'comanda_id' => $comanda->id,
                    ]);
                }
            }
        }
        
        // ========================================
        // ADICIONAR PRODUTOS SELECIONADOS À COMANDA
        // ========================================
        if (!empty($this->produtosSelecionados) && $comanda) {
            \Log::info('🛍️ Iniciando adição de produtos', [
                'total_produtos' => count($this->produtosSelecionados),
                'produtos' => $this->produtosSelecionados,
            ]);
            
            foreach ($this->produtosSelecionados as $produtoId) {
                $produto = \App\Models\Estoque::find($produtoId);
                
                if ($produto && $produto->quantidade_atual > 0) {
                    // Criar ComandaProduto (que vai disparar o Observer para criar recompensa)
                    $comandaProduto = \App\Models\ComandaProduto::create([
                        'comanda_id' => $comanda->id,
                        'estoque_id' => $produto->id,
                        'quantidade' => 1,
                        'preco_unitario' => $produto->preco_unitario,
                        'subtotal' => $produto->preco_unitario,
                    ]);
                    
                    \Log::info('✅ Produto adicionado à comanda (Observer deve disparar agora)', [
                        'comanda_produto_id' => $comandaProduto->id,
                        'comanda_id' => $comanda->id,
                        'produto_id' => $produto->id,
                        'produto_nome' => $produto->produto_nome,
                        'valor' => $produto->preco_unitario,
                    ]);
                } else {
                    \Log::warning('⚠️ Produto não encontrado ou sem estoque', [
                        'produto_id' => $produtoId,
                        'existe' => $produto ? 'sim' : 'não',
                        'quantidade_atual' => $produto->quantidade_atual ?? 0,
                    ]);
                }
            }
            
            \Log::info('✅ Processo de adição de produtos concluído');
        } else {
            if (empty($this->produtosSelecionados)) {
                \Log::info('ℹ️ Nenhum produto selecionado para adicionar');
            }
            if (!$comanda) {
                \Log::error('❌ Comanda não foi criada');
            }
        }
        
    } catch (\Throwable $e) {
        Log::error('❌ [MakeAppointment] Erro ao criar comanda automaticamente', [
            'appointment_id' => $appointment?->id,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);
    }

    // Envia notificação de confirmação com .ics para o cliente
    if ($appointment && $appointment->customer) {
        $appointment->customer->notify(new \App\Notifications\AppointmentConfirmed($appointment));
    } else {
        // Cliente não associado; nenhuma notificação enviada
    }

    $mensagemSucesso = 'Agendamento realizado com sucesso!';
    if ($descontoAplicado > 0) {
        $mensagemSucesso .= ' Desconto de R$ ' . number_format($descontoAplicado, 2, ',', '.') . ' aplicado.';
        
        // Informar sobre saldo restante de crédito
        if ($saldoRestante > 0) {
            $mensagemSucesso .= ' Você ainda tem R$ ' . number_format($saldoRestante, 2, ',', '.') . ' de crédito disponível!';
        }
    }
    if (!empty($this->produtosSelecionados)) {
        $mensagemSucesso .= ' ' . count($this->produtosSelecionados) . ' produto(s) adicionado(s). Você ganhará créditos!';
    }

    session()->flash('success', $mensagemSucesso);
    return $this->redirect(route('tenant.dashboard', ['tabelaAtiva' => 'appointments']), navigate: true);
}

    // Métodos para Fidelidade - Produtos e Recompensas
    public function atualizarProdutos($produtos)
    {
        $this->produtosSelecionados = is_array($produtos) ? $produtos : [];
        \Log::info('Produtos selecionados atualizados', [
            'produtos' => $this->produtosSelecionados
        ]);
    }

public function render()
{
    // Buscar dias que já têm agendamentos do cliente atual
    $this->days_with_appointments = \App\Models\Appointment::where('customer_id', \Illuminate\Support\Facades\Auth::id())
        ->where('status', 'Pendente')
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
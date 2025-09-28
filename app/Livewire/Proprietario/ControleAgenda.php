<?php

namespace App\Livewire\Proprietario;

use Livewire\Component;

class ControleAgenda extends Component
{
    public function atualizarAgenda()
    {
        $this->loadHorarios();
        $this->calcularDiasIndisponiveis();
        $this->showModal = false;
        $this->selectedDay = null;
        $this->intervalosDoDia = [];
    }
    public $diasIndisponiveis = [];
    public $dias_pt = [
        'Monday' => 'Segunda',
        'Tuesday' => 'Terça',
        'Wednesday' => 'Quarta',
        'Thursday' => 'Quinta',
        'Friday' => 'Sexta',
        'Saturday' => 'Sábado',
        'Sunday' => 'Domingo',
    ];
    public $showModal = false;
    public $selectedDay = null;
    public $selectedDayOfWeek = null;
    public $selectedDayOfWeekPt = null;
    public $intervalosDoDia = [];
    public $funcionarios;
    public $selectedFuncionario;
    public $horarios;
    public $bloqueios = [];

    public function openModal($date, $dayOfWeek)
    {
    // Descobre a próxima data correspondente ao dia da semana
    
    $this->selectedDay = $date;
    $this->selectedDayOfWeek = $dayOfWeek;
    $this->selectedDayOfWeekPt = $this->dias_pt[$dayOfWeek] ?? $dayOfWeek;
    $this->intervalosDoDia = $this->generateSlots($dayOfWeek, $date);
    $this->showModal = true;
    }

    // Gera slots de 15min para o dia selecionado, igual ao cliente
    public function generateSlots($dayOfWeek, $date)
    {
        $slots = [];
        $horario = $this->horarios->where('day_of_week', $dayOfWeek)->first();
        if (!$horario) return $slots;

        $start_time = strtotime($date . ' ' . $horario->start_time);
        $end_time = strtotime($date . ' ' . $horario->end_time);
        $lunch_start = $horario->lunch_start ? strtotime($date . ' ' . $horario->lunch_start) : null;
        $lunch_end = $horario->lunch_end ? strtotime($date . ' ' . $horario->lunch_end) : null;

        // Buscar todos appointments do funcionário para o dia
        $appointments = \App\Models\Appointment::where('employee_id', $this->selectedFuncionario)
            ->where('appointment_date', $date)
            ->get();

        $busy_slots = $appointments->map(function($a) use ($date) {
            $start = strtotime($date . ' ' . $a->start_time);
            $end = strtotime($date . ' ' . $a->end_time);
            return [ 'start' => $start, 'end' => $end, 'status' => $a->status ];
        })->filter(function($b) {
            return $b['start'] !== false && $b['end'] !== false;
        })->values()->toArray();

        for ($slot = $start_time; $slot + (15 * 60) <= $end_time; $slot += 15 * 60) {
            $slot_end = $slot + (15 * 60);
            // Ignora horários passados no dia de hoje
            if ($date === date('Y-m-d') && $slot < time()) continue;
            // Pular intervalo de almoço
            if ($lunch_start && $lunch_end && ($slot < $lunch_end && $slot_end > $lunch_start)) continue;

            // Verifica se o slot está ocupado
            $ocupado = false;
            $bloqueado = false;
            foreach ($busy_slots as $busy) {
                if ($slot < $busy['end'] && $slot_end > $busy['start']) {
                    if ($busy['status'] === 'bloqueio') $bloqueado = true;
                    else $ocupado = true;
                }
            }
            $slots[] = [
                'start' => date('H:i', $slot),
                'end' => date('H:i', $slot_end),
                'ocupado' => $ocupado,
                'bloqueado' => $bloqueado,
                'timestamp' => $slot,
                'date' => $date,
            ];
        }
        return $slots;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedDay = null;
        $this->intervalosDoDia = [];
    }

    public function mount()
    {
        $this->funcionarios = \App\Models\User::whereHas('roles', function($q){ $q->where('role_id', 2); })->get();
        $this->selectedFuncionario = $this->funcionarios->first()->id ?? null;
        $this->loadHorarios();
        $this->calcularDiasIndisponiveis();
    }

    public function updatedSelectedFuncionario()
    {
        logger('Livewire: updatedSelectedFuncionario chamado para id ' . $this->selectedFuncionario);
        $this->loadHorarios();
        $this->calcularDiasIndisponiveis();
        $this->showModal = false;
        $this->selectedDay = null;
        $this->intervalosDoDia = [];
    }
    public function calcularDiasIndisponiveis()
    {
        $dias = [];
        $carbon = \Carbon\Carbon::now();
        for ($i = 0; $i < 21; $i++) {
            $date = $carbon->copy()->addDays($i);
            $dayOfWeek = $date->format('l');
            $slots = $this->generateSlots($dayOfWeek, $date->format('Y-m-d'));
            // Se não há slots, ou todos estão ocupados/bloqueados, é indisponível
            $indisponivel = count($slots) === 0 || (count($slots) > 0 && collect($slots)->every(function($s){ return $s['ocupado'] || $s['bloqueado']; }));
            if ($indisponivel) {
                $dias[$date->format('Y-m-d')] = true;
            }
        }
        $this->diasIndisponiveis = $dias;
    }

    public function loadHorarios()
    {
        $this->horarios = \App\Models\Schedule::where('user_id', $this->selectedFuncionario)->get();
        // Buscar appointments de bloqueio para o funcionário
        $this->bloqueios = \App\Models\Appointment::where('employee_id', $this->selectedFuncionario)
            ->where('status', 'bloqueio')
            ->pluck('start_time', 'id')->toArray();
    }

    public function toggleBloqueio($dia, $start, $end)
    {
        // Garante que $dia está no formato Y-m-d
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dia)) {
            $dia = now()->next($dia)->format('Y-m-d');
        }
        $bloqueio = \App\Models\Appointment::where('employee_id', $this->selectedFuncionario)
            ->where('status', 'bloqueio')
            ->where('appointment_date', $dia)
            ->where('start_time', $start)
            ->where('end_time', $end)
            ->first();
        if ($bloqueio) {
            $bloqueio->delete();
        } else {
            $funcionario = \App\Models\User::find($this->selectedFuncionario);
            $branch_id = $funcionario->branches()->first()->id ?? null;
            \App\Models\Appointment::create([
                'employee_id' => $this->selectedFuncionario,
                'branch_id' => $branch_id,
                'customer_id' => \Illuminate\Support\Facades\Auth::id(),
                'appointment_date' => $dia,
                'start_time' => $start,
                'end_time' => $end,
                'services' => 'Bloqueio',
                'status' => 'bloqueio',
            ]);
        }
        $this->loadHorarios();
        $this->intervalosDoDia = $this->generateSlots($this->selectedDayOfWeek ?? '', $dia);
    }

    public function render()
    {
        return view('livewire.proprietario.controle-agenda', [
            'funcionarios' => $this->funcionarios,
            'horarios' => $this->horarios,
            'bloqueios' => $this->bloqueios,
            'showModal' => $this->showModal,
            'selectedDay' => $this->selectedDay,
            'intervalosDoDia' => $this->intervalosDoDia,
            'diasIndisponiveis' => $this->diasIndisponiveis,
        ]);
    }
}

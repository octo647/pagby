<?php

namespace App\Livewire\Funcionario;

use App\Models\Appointment;
use Livewire\Component;
use App\Models\Avaliacao; // Certifique-se de que o modelo Avaliacao está correto
use App\Models\User; // Certifique-se de que o modelo User está correto

class Estatisticas extends Component
{
    /**
     * The component's properties.
     *
     * @var array
     */
   public $totalServicosRealizados = 0;
   public $servicosMaisSolicitados = [];
   public $totalHorariosAgendados = 0;
   public $horariosPico = [];
   public $mediaAvaliacoes = 0;
   public $avaliacoesMaisFrequentes = [];
   public $totalServicosOferecidos = 0;
   public $servicosPopulares = []; 
   public $servicos = [];

    public function render()
    {
        $funcionario = auth()->user(); // supondo que o usuário autenticado é o funcionário
        
        //para calcular os serviços mais solicitados, vamos contar os diferentes serviços realizados
        $servicosStrings = Appointment::where('employee_id', $funcionario->id)
            ->where('status', 'Realizado')
            ->select('services')
            ->pluck('services')
            ->toArray();
            
        $frequenciaServicos = [];
        foreach ($servicosStrings as $servicos) {
            foreach (array_map('trim', explode('/', $servicos)) as $servico) {
            if ($servico !== '') {
            $frequenciaServicos[$servico] = ($frequenciaServicos[$servico] ?? 0) + 1;
                }
            }
        }
        // Ordena do mais frequente para o menos frequente
        arsort($frequenciaServicos);

        // Agora $frequenciaServicos é um array: ['Corte' => 12, 'Escova' => 8, ...]
        $this->servicosMaisSolicitados = $frequenciaServicos;
        //total de serviços realizados
        $this->totalServicosRealizados = array_sum($frequenciaServicos);
        //total de horários agendados
        $this->totalHorariosAgendados = Appointment::where('employee_id', $funcionario->id)->count();
        //horários de pico
        $horarios = Appointment::where('employee_id', $funcionario->id)
            ->where('status', 'Realizado')
            ->select('start_time')
            ->pluck('start_time')
            ->toArray();
        $horariosFrequentes = array_count_values($horarios);
        arsort($horariosFrequentes);
        $this->horariosPico = array_slice(array_keys($horariosFrequentes), 0, 4); // Pega os 3 horários mais frequentes
        //média de avaliações
        // 1. Pegue todos os IDs de appointments do funcionário
    $appointmentIds = Appointment::where('employee_id', $funcionario->id)->pluck('id')->toArray();

    // 2. Busque todas as avaliações desses appointments
    $avaliacoes = Avaliacao::whereIn('appointment_id', $appointmentIds)
        ->pluck('avaliacao')
        ->toArray();

    // 3. Calcule a média das avaliações
    if (count($avaliacoes) > 0) {
        $this->mediaAvaliacoes = array_sum($avaliacoes) / count($avaliacoes);
    } else {
        $this->mediaAvaliacoes = 0;
    }

    // 4. Calcule as avaliações mais frequentes
    $avaliacoesFrequentes = array_count_values($avaliacoes);
    arsort($avaliacoesFrequentes);
    $this->avaliacoesMaisFrequentes = array_slice(array_keys($avaliacoesFrequentes), 0, 3);

        
        
        
        
        
        $this->totalServicosOferecidos = 20; // Example value
        $this->servicosPopulares = ['Serviço X', 'Serviço Y', 'Serviço Z']; // Example values   
        return view('livewire.funcionario.estatisticas', [
            'totalServicosRealizados' => $this->totalServicosRealizados,
            'servicosMaisSolicitados' => $this->servicosMaisSolicitados,
            'totalHorariosAgendados' => $this->totalHorariosAgendados,
            'horariosPico' => $this->horariosPico,
            'mediaAvaliacoes' => $this->mediaAvaliacoes,
            'avaliacoesMaisFrequentes' => $this->avaliacoesMaisFrequentes,
            'totalServicosOferecidos' => $this->totalServicosOferecidos,
            'servicosPopulares' => $this->servicosPopulares
        ]   );
    }
}

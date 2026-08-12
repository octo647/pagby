<?php

namespace App\Livewire\Proprietario;

use Livewire\Component;

class ClientesNovosAntigos extends Component

{
    public $retencaoLabels = [];
    public $retencaoNovos = [];
    public $retencaoRecorrentes = [];

    public function mount()
    {
        $inicio = now()->subMonths(11)->startOfMonth();
        $fim = now()->endOfMonth();

        // Busca todos os agendamentos do período de 1 ano
        $agendamentos = \App\Models\Appointment::whereBetween('appointment_date', [$inicio, $fim])
            ->orderBy('appointment_date')
            ->get(['customer_id', 'appointment_date']);

        $clientesPrimeiroAgendamento = [];

        // Mapeia o primeiro agendamento de cada cliente
        foreach ($agendamentos as $agendamento) {
            $cid = $agendamento->customer_id;
            $data = $agendamento->appointment_date;
            if (!isset($clientesPrimeiroAgendamento[$cid]) || $data < $clientesPrimeiroAgendamento[$cid]) {
                $clientesPrimeiroAgendamento[$cid] = $data;
            }
        }

        // Prepara os meses
        $meses = [];
        $data = $inicio->copy();
        while ($data <= $fim) {
            $meses[] = $data->format('Y-m');
            $data->addMonth();
        }

        $novosPorMes = [];
        $recorrentesPorMes = [];

        foreach ($meses as $mes) {
            $inicioMes = \Carbon\Carbon::createFromFormat('Y-m', $mes)->startOfMonth();
            $fimMes = \Carbon\Carbon::createFromFormat('Y-m', $mes)->endOfMonth();

            // Clientes que agendaram neste mês
            $clientesNoMes = $agendamentos->filter(function($a) use ($inicioMes, $fimMes) {
                return $a->appointment_date >= $inicioMes->format('Y-m-d') && $a->appointment_date <= $fimMes->format('Y-m-d');
            })->pluck('customer_id')->unique();

            // Novos: primeiro agendamento é neste mês
            $novos = $clientesNoMes->filter(function($cid) use ($clientesPrimeiroAgendamento, $inicioMes, $fimMes) {
                $primeiro = $clientesPrimeiroAgendamento[$cid] ?? null;
                return $primeiro && $primeiro >= $inicioMes->format('Y-m-d') && $primeiro <= $fimMes->format('Y-m-d');
            })->count();

            // Recorrentes: já tinham agendado antes
            $recorrentes = $clientesNoMes->count() - $novos;

            $novosPorMes[] = $novos;
            $recorrentesPorMes[] = $recorrentes;
        }

            $this->retencaoLabels = array_map(function($mes) {
                return \Carbon\Carbon::createFromFormat('Y-m', $mes)->format('m/Y');
            }, $meses);
            $this->retencaoNovos = $novosPorMes;
            $this->retencaoRecorrentes = $recorrentesPorMes;
    }

    public function render()
    {
        return view('livewire.proprietario.clientes-novos-antigos');
    }
}

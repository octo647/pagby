<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Models\Comanda;
use Illuminate\Support\Facades\Log;

class AppointmentObserver
{
    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        // Verificar se o agendamento foi criado já com status 'Confirmado'
        if ($appointment->status === 'Confirmado') {
            $this->criarComandaAutomatica($appointment);
        }
    }

    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void
    {
        // Verificar se o status mudou para 'Confirmado'
        if ($appointment->isDirty('status') && $appointment->status === 'Confirmado') {
            $this->criarComandaAutomatica($appointment);
        }
    }

    /**
     * Criar comanda automaticamente quando agendamento for confirmado
     */
    private function criarComandaAutomatica(Appointment $appointment): void
    {
        try {
            // Verificar se já existe uma comanda para este agendamento
            $comandaExistente = Comanda::where('appointment_id', $appointment->id)->first();
            if ($comandaExistente) {
                Log::info("Comanda já existe para o agendamento {$appointment->id}");
                return;
            }

            // Verificar se a configuração de geração automática está habilitada
            $geracaoAutomaticaHabilitada = config('app.gerar_comandas_automaticamente', true);
            if (!$geracaoAutomaticaHabilitada) {
                Log::info("Geração automática de comandas está desabilitada");
                return;
            }

            // Criar a comanda automaticamente
            $comanda = Comanda::criarDeAgendamento(
                $appointment, 
                'Comanda gerada automaticamente ao confirmar o agendamento'
            );

            Log::info("Comanda {$comanda->numero_comanda} criada automaticamente para o agendamento {$appointment->id}");
            
        } catch (\Exception $e) {
            Log::error("Erro ao criar comanda automaticamente para agendamento {$appointment->id}: " . $e->getMessage());
        }
    }

    /**
     * Handle the Appointment "deleted" event.
     */
    public function deleted(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "restored" event.
     */
    public function restored(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "force deleted" event.
     */
    public function forceDeleted(Appointment $appointment): void
    {
        //
    }
}

<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Models\Comanda;
use App\Services\WhatsAppQueueService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AppointmentObserver
{
    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        // Verificar se o agendamento foi criado já com status 'Confirmado'
            // (Removido: lógica de status 'Confirmado')

        $this->notifyEmployee($appointment);
    }

    /**
     * Notifica o profissional via WhatsApp sobre o novo agendamento
     */
    private function notifyEmployee(Appointment $appointment): void
    {
        try {
            $employee = $appointment->employee;

            if (!$employee || !$employee->phone || !$employee->whatsapp_activated) {
                return;
            }

            $serviceNames = is_string($appointment->services) && $appointment->services !== ''
                ? $appointment->services
                : 'Serviço agendado';

            $tenantName = tenant('name') ?? tenant('id') ?? 'Salão';
            $branchName = $appointment->branch?->branch_name ?? 'Unidade';
            $date = Carbon::parse($appointment->appointment_date)->format('d/m/Y');
            $time = Carbon::parse($appointment->start_time)->format('H:i');

            $message = "📅 *Novo Agendamento*\n\n"
                . "Olá, *{$employee->name}*!\n\n"
                . "Você tem um novo horário marcado:\n\n"
                . "🕐 *Data e hora:* {$date} às {$time}\n"
                . "✂️ *Serviço:* {$serviceNames}\n"
                . "📍 *Local:* {$branchName}\n\n"
                . "_Mensagem automática de {$tenantName}_";

            app(WhatsAppQueueService::class)->queue($employee->phone, $message, [
                'type' => 'new_appointment_employee',
                'appointment_id' => $appointment->id,
                'employee_id' => $employee->id,
            ]);
        } catch (\Throwable $e) {
            Log::error("Erro ao notificar profissional sobre agendamento {$appointment->id}: " . $e->getMessage());
        }
    }

    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void
    {
        // Verificar se o status mudou para 'Confirmado'
            // (Removido: lógica de status 'Confirmado')
    }

    /**
    * Criar comanda automaticamente quando agendamento for realizado
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

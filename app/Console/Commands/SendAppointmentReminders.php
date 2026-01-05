<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders {--hours=24 : Horas de antecedência}';
    protected $description = 'Envia lembretes de agendamentos por WhatsApp';

    public function handle()
    {
        $hoursAhead = (int) $this->option('hours');
        $this->info("Verificando agendamentos nas próximas {$hoursAhead} horas...");

        $tenants = Tenant::all();
        $totalReminders = 0;

        foreach ($tenants as $tenant) {
            tenancy()->initialize($tenant);
            
            $reminders = $this->processTenantsAppointments($tenant, $hoursAhead);
            $totalReminders += $reminders;
            
            tenancy()->end();
        }

        if ($totalReminders > 0) {
            $this->info("✅ {$totalReminders} lembretes adicionados à fila");
        } else {
            $this->info("ℹ️ Nenhum agendamento encontrado para enviar lembretes");
        }

        return 0;
    }

    private function processTenantsAppointments(Tenant $tenant, int $hoursAhead)
    {
        $now = Carbon::now();
        $targetTime = $now->copy()->addHours($hoursAhead);

        // Busca agendamentos confirmados nas próximas X horas que ainda não foram lembrados
        $appointments = Appointment::where('status', 'confirmed')
            ->whereBetween('appointment_date', [
                $now->format('Y-m-d'),
                $targetTime->format('Y-m-d')
            ])
            ->whereNull('reminder_sent_at')
            ->with(['customer', 'employee', 'branch'])
            ->get();

        if ($appointments->isEmpty()) {
            return 0;
        }

        $commandsFile = storage_path('app/whatsapp_commands.json');
        $commands = file_exists($commandsFile) ? json_decode(file_get_contents($commandsFile), true) : [];
        
        if (!is_array($commands)) {
            $commands = [];
        }

        $addedCount = 0;

        foreach ($appointments as $appointment) {
            $appointmentDateTime = Carbon::parse($appointment->appointment_date . ' ' . $appointment->start_time);
            
            // Verifica se está dentro do período de antecedência
            $hoursUntilAppointment = $now->diffInHours($appointmentDateTime, false);
            
            if ($hoursUntilAppointment < 0 || $hoursUntilAppointment > $hoursAhead) {
                continue;
            }

            // Verifica se cliente tem telefone e WhatsApp ativado
            if (!$appointment->customer || 
                !$appointment->customer->phone || 
                !$appointment->customer->whatsapp_activated) {
                continue;
            }

            // Monta lista de serviços
            $serviceNames = 'Serviços agendados';
            if (is_string($appointment->services)) {
                $serviceNames = $appointment->services;
            }

            // Verifica se tem pagamento pendente
            $hasPendingPayment = $appointment->payments()
                ->whereIn('status', ['pending', 'waiting'])
                ->exists();

            $commands[] = [
                'type' => 'appointment_reminder',
                'appointment_id' => $appointment->id,
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name ?? $tenant->id,
                'customer_name' => $appointment->customer->name,
                'customer_phone' => $appointment->customer->phone,
                'appointment_date' => Carbon::parse($appointment->appointment_date)->format('d/m/Y'),
                'appointment_time' => Carbon::parse($appointment->start_time)->format('H:i'),
                'employee_name' => $appointment->employee->name ?? 'Profissional',
                'service_names' => $serviceNames,
                'branch_name' => $appointment->branch->name ?? 'Unidade',
                'observation' => $appointment->observation,
                'has_pending_payment' => $hasPendingPayment,
                'total_price' => $appointment->total_price ?? 0,
                'created_at' => now()->toIso8601String(),
                'retries' => 0
            ];

            // Marca como lembrete enviado
            $appointment->update(['reminder_sent_at' => now()]);
            
            $addedCount++;
        }

        // Salva comandos
        if ($addedCount > 0) {
            file_put_contents($commandsFile, json_encode($commands, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $this->info("  📱 {$addedCount} lembretes para tenant: {$tenant->id}");
        }

        return $addedCount;
    }
}

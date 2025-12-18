<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Storage;

class AppointmentConfirmed extends Notification
{
    use Queueable;

    protected $appointment;
    protected $icsContent;

    public function __construct($appointment)
    {
        $this->appointment = $appointment;
        $this->icsContent = $this->generateIcs($appointment);
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('Confirmação de Agendamento')
            ->greeting('Olá, ' . ($notifiable->name ?? ''))
            ->line('Seu agendamento foi confirmado!')
            ->line('Data: ' . $this->appointment->appointment_date)
            ->line('Horário: ' . $this->appointment->start_time . ' - ' . $this->appointment->end_time)
            ->line('Serviço(s): ' . $this->appointment->services)
            ->line('Funcionário: ' . ($this->appointment->employee ? $this->appointment->employee->name : ''))
            ->line('Obrigado por agendar conosco!');

        // Salva o arquivo temporário .ics
        $icsFile = 'agendamento_' . $this->appointment->id . '.ics';
        Storage::disk('local')->put($icsFile, $this->icsContent);
        $mail->attach(storage_path('app/' . $icsFile), [
            'as' => 'agendamento.ics',
            'mime' => 'text/calendar',
        ]);

        return $mail;
    }

    private function generateIcs($appointment)
    {
        // Debug: log datas e horas
        \Log::info('ICS DEBUG', [
            'appointment_date' => $appointment->appointment_date,
            'start_time' => $appointment->start_time,
            'end_time' => $appointment->end_time,
        ]);
        // Garante que os campos estejam no formato correto
        $date = $appointment->appointment_date instanceof \Carbon\Carbon
            ? $appointment->appointment_date->format('Y-m-d')
            : (string) $appointment->appointment_date;

        $startTime = substr($appointment->start_time, 0, 5); // pega apenas HH:MM
        $endTime = substr($appointment->end_time, 0, 5); // pega apenas HH:MM

        $startDateTime = $date . ' ' . $startTime;
        $endDateTime = $date . ' ' . $endTime;

        $start = date('Ymd\THis', strtotime($startDateTime));
        $end = date('Ymd\THis', strtotime($endDateTime));
        $summary = 'Agendamento: ' . ($appointment->services ?? '');
        $description = 'Agendamento com ' . ($appointment->employee ? $appointment->employee->name : '') . ' no salão.';
        $location = $appointment->branch->address ?? '';
        $uid = uniqid();
        return "BEGIN:VCALENDAR\nVERSION:2.0\nPRODID:-//pagby//EN\nBEGIN:VEVENT\nUID:$uid\nDTSTAMP:$start\nDTSTART:$start\nDTEND:$end\nSUMMARY:$summary\nDESCRIPTION:$description\nLOCATION:$location\nEND:VEVENT\nEND:VCALENDAR";
    }
}

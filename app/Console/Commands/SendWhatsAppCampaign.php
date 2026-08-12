<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SendWhatsAppCampaign extends Command
{
    protected $signature = 'whatsapp:campaign {message} {--targets=all}';
    protected $description = 'Envia campanha via WhatsApp para usuários específicos';

    public function handle()
    {
        $message = $this->argument('message');
        $targets = $this->option('targets');
        
        // Busca usuários com WhatsApp ativado
        $users = \App\Models\User::where('whatsapp_activated', true)
            ->whereNotNull('phone')
            ->get();
        
        $this->info("📱 Encontrados {$users->count()} usuários com WhatsApp ativo");
        
        if (!$this->confirm('Deseja enviar a campanha para estes usuários?')) {
            $this->warn('Campanha cancelada');
            return;
        }
        
        // Carrega comandos existentes
        $commandsFile = storage_path('app/whatsapp_commands.json');
        $commands = [];
        
        if (file_exists($commandsFile)) {
            $commands = json_decode(file_get_contents($commandsFile), true) ?? [];
        }
        
        // Adiciona novos comandos
        $sent = 0;
        foreach ($users as $user) {
            $commands[] = [
                'type' => 'send_message',
                'to' => $user->phone,
                'message' => $message,
                'created_at' => now()->toIso8601String(),
                'metadata' => [
                    'campaign' => true,
                    'user_id' => $user->id,
                    'user_name' => $user->name
                ]
            ];
            $sent++;
        }
        
        // Salva comandos
        file_put_contents($commandsFile, json_encode($commands, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $this->info("✅ {$sent} mensagens adicionadas à fila");
        $this->info("🔔 Serão enviadas nos próximos 30 segundos");
        
        return Command::SUCCESS;
    }
}

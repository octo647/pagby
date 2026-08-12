<?php

namespace App\Services;

class WhatsAppQueueService
{
    private $commandsFile;
    
    public function __construct()
    {
        $this->commandsFile = storage_path('app/whatsapp_commands.json');
    }
    
    /**
     * Adiciona mensagem à fila do WhatsApp
     */
    public function queue($phone, $message, $metadata = [])
    {
        $commands = $this->getCommands();
        
        $commands[] = [
            'type' => 'send_message',
            'to' => $this->formatPhone($phone),
            'message' => $message,
            'created_at' => now()->toIso8601String(),
            'metadata' => array_merge([
                'queued_at' => now()->format('Y-m-d H:i:s')
            ], $metadata)
        ];
        
        $this->saveCommands($commands);
        
        return true;
    }
    
    /**
     * Envia campanha para múltiplos usuários
     */
    public function campaign($users, $message, $campaignName = null)
    {
        $commands = $this->getCommands();
        $queued = 0;
        
        foreach ($users as $user) {
            if (!$user->whatsapp_activated || !$user->phone) {
                continue;
            }
            
            $commands[] = [
                'type' => 'send_message',
                'to' => $this->formatPhone($user->phone),
                'message' => $message,
                'created_at' => now()->toIso8601String(),
                'metadata' => [
                    'campaign' => $campaignName ?? 'default',
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'tenant' => tenant('id')
                ]
            ];
            
            $queued++;
        }
        
        $this->saveCommands($commands);
        
        return $queued;
    }
    
    /**
     * Notifica sobre nova assinatura
     */
    public function notifyNewSubscription($tenant, $plan)
    {
        $message = "🎉 *Nova Assinatura PagBy!*\n\n";
        $message .= "Salão: *{$tenant->name}*\n";
        $message .= "Plano: *{$plan->name}*\n";
        $message .= "Valor: R$ {$plan->price}\n\n";
        $message .= "_Obrigado por confiar no PagBy!_";
        
        // Envia para o proprietário do tenant
        if ($tenant->owner_phone) {
            $this->queue($tenant->owner_phone, $message, [
                'type' => 'subscription_notification',
                'tenant_id' => $tenant->id
            ]);
        }
    }
    
    /**
     * Lembra sobre vencimento próximo
     */
    public function remindExpiration($user, $daysLeft)
    {
        $emoji = $daysLeft <= 1 ? '🚨' : '⏰';
        
        $message = "{$emoji} *Lembrete de Vencimento*\n\n";
        $message .= "Olá, *{$user->name}*!\n\n";
        $message .= "Seu plano vence em *{$daysLeft} " . ($daysLeft == 1 ? 'dia' : 'dias') . "*.\n\n";
        $message .= "Renove agora para não perder acesso:\n";
        $message .= "👉 pagby.com.br/renovar";
        
        $this->queue($user->phone, $message, [
            'type' => 'expiration_reminder',
            'days_left' => $daysLeft,
            'user_id' => $user->id
        ]);
    }
    
    private function formatPhone($phone)
    {
        return preg_replace('/\D/', '', $phone);
    }
    
    private function getCommands()
    {
        if (!file_exists($this->commandsFile)) {
            return [];
        }
        
        return json_decode(file_get_contents($this->commandsFile), true) ?? [];
    }
    
    private function saveCommands($commands)
    {
        file_put_contents(
            $this->commandsFile,
            json_encode($commands, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }
}

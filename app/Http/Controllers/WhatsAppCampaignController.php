<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WhatsAppCampaignController extends Controller
{
    /**
     * Envia campanha via WhatsApp
     */
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'users' => 'required|array',
            'users.*' => 'exists:users,id'
        ]);
        
        // Busca usuários
        $users = \App\Models\User::whereIn('id', $request->users)
            ->where('whatsapp_activated', true)
            ->whereNotNull('phone')
            ->get();
        
        // Carrega comandos existentes
        $commandsFile = storage_path('app/whatsapp_commands.json');
        $commands = [];
        
        if (file_exists($commandsFile)) {
            $commands = json_decode(file_get_contents($commandsFile), true) ?? [];
        }
        
        // Adiciona novos comandos
        foreach ($users as $user) {
            $commands[] = [
                'type' => 'send_message',
                'to' => $user->phone,
                'message' => $request->message,
                'created_at' => now()->toIso8601String(),
                'metadata' => [
                    'campaign' => true,
                    'campaign_name' => $request->campaign_name ?? 'Manual',
                    'user_id' => $user->id,
                    'sent_by' => auth()->id()
                ]
            ];
        }
        
        // Salva comandos
        file_put_contents($commandsFile, json_encode($commands, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        return response()->json([
            'success' => true,
            'queued' => count($users),
            'message' => "Campanha enviada para {$users->count()} usuários"
        ]);
    }
    
    /**
     * Envia para um único usuário
     */
    public function sendToUser(Request $request, $userId)
    {
        $user = \App\Models\User::findOrFail($userId);
        
        if (!$user->whatsapp_activated || !$user->phone) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não tem WhatsApp ativado'
            ], 400);
        }
        
        $this->queueMessage($user->phone, $request->message, [
            'user_id' => $user->id,
            'type' => $request->type ?? 'notification'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Mensagem enfileirada'
        ]);
    }
    
    /**
     * Helper para adicionar mensagem à fila
     */
    private function queueMessage($phone, $message, $metadata = [])
    {
        $commandsFile = storage_path('app/whatsapp_commands.json');
        $commands = [];
        
        if (file_exists($commandsFile)) {
            $commands = json_decode(file_get_contents($commandsFile), true) ?? [];
        }
        
        $commands[] = [
            'type' => 'send_message',
            'to' => $phone,
            'message' => $message,
            'created_at' => now()->toIso8601String(),
            'metadata' => $metadata
        ];
        
        file_put_contents($commandsFile, json_encode($commands, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}

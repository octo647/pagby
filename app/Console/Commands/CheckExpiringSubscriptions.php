<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TenantsPlansPayment;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CheckExpiringSubscriptions extends Command
{
    protected $signature = 'subscriptions:check-expiring {--days=3 : Dias antes do vencimento}';
    protected $description = 'Verifica assinaturas que vão expirar e gera lembretes';

    public function handle()
    {
        $days = (int) $this->option('days');
        $targetDate = Carbon::now()->addDays($days)->format('Y-m-d');
        
        $this->info("🔍 Verificando assinaturas que vencem em {$days} dias ({$targetDate})...");
        
        // Busca assinaturas ativas que vencem no período especificado
        // Como expires_at pode estar vazio, calcula baseado em created_at + 30 dias
        $expiringPayments = TenantsPlansPayment::on('mysql')
            ->where('status', 'ACTIVE')
            ->whereRaw("DATE_ADD(DATE(created_at), INTERVAL 30 DAY) = ?", [$targetDate])
            ->get();
        
        $this->info("📋 Encontradas {$expiringPayments->count()} assinaturas expirando");
        
        foreach ($expiringPayments as $payment) {
            try {
                // Busca informações do tenant
                $tenant = Tenant::find($payment->tenant_id);
                
                if (!$tenant) {
                    $this->warn("⚠️  Tenant {$payment->tenant_id} não encontrado");
                    continue;
                }
                
                // Decodifica payer_data para pegar dados do cliente
                $payerData = json_decode($payment->payer_data, true);
                $email = $payerData['email'] ?? null;
                $name = $payerData['name'] ?? 'Cliente';
                $cpf = $payerData['cpfCnpj'] ?? null;
                
                // Busca o telefone do usuário no tenant
                $phone = null;
                if ($email || $cpf || $name) {
                    try {
                        tenancy()->initialize($tenant);
                        
                        // Tenta buscar por email primeiro
                        $user = \App\Models\User::on('tenant')->where('email', $email)->first();
                        
                        // Se não encontrar por email, tenta por CPF
                        if (!$user && $cpf) {
                            $user = \App\Models\User::on('tenant')->where('cpf', $cpf)->first();
                            if ($user) {
                                $this->info("   📋 Usuário encontrado por CPF");
                            }
                        }
                        
                        // Se não encontrar por CPF, tenta por nome
                        if (!$user && $name) {
                            $user = \App\Models\User::on('tenant')->where('name', $name)->first();
                            if ($user) {
                                $this->info("   📝 Usuário encontrado por nome");
                            }
                        }
                        
                        if ($user) {
                            $phone = $user->phone;
                            $name = $user->name; // Atualiza com o nome atual
                            $email = $user->email; // Atualiza com o email atual
                            $this->info("   📱 Telefone encontrado: {$phone}");
                        } else {
                            $this->warn("   ⚠️  Usuário não encontrado no tenant");
                        }
                        tenancy()->end();
                    } catch (\Exception $e) {
                        $this->warn("⚠️  Erro ao buscar usuário: {$e->getMessage()}");
                        tenancy()->end();
                    }
                }
                
                // Calcula a data de vencimento (created_at + 30 dias)
                $dueDate = Carbon::parse($payment->created_at)->addDays(30)->format('Y-m-d');
                
                // Armazena lembrete no arquivo JSON para o bot WhatsApp processar
                $this->storeReminder([
                    'payment_id' => $payment->id,
                    'tenant_id' => $payment->tenant_id,
                    'tenant_name' => $tenant->name ?? $payment->tenant_id,
                    'plan_name' => $payment->plan_id,
                    'amount' => $payment->amount,
                    'due_date' => $dueDate,
                    'days_until_due' => $days,
                    'customer_name' => $name,
                    'customer_phone' => $phone,
                    'customer_email' => $email,
                    'payment_url' => $payment->checkout_url ?? null,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'sent' => false,
                ]);
                
                $this->info("✅ Lembrete criado: {$name} - Plano {$payment->plan_id} - R$ {$payment->amount}");
                
                Log::info('📅 Lembrete de assinatura criado', [
                    'payment_id' => $payment->id,
                    'tenant' => $payment->tenant_id,
                    'customer' => $name,
                    'due_date' => $payment->next_due_date,
                ]);
                
            } catch (\Exception $e) {
                $this->error("❌ Erro ao processar payment {$payment->id}: {$e->getMessage()}");
                Log::error('Erro ao criar lembrete de assinatura', [
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        
        $this->info('✅ Verificação concluída!');
        return 0;
    }
    
    /**
     * Envia comando para o bot WhatsApp processar
     */
    private function storeReminder(array $data)
    {
        $commandsFile = storage_path('app/whatsapp_commands.json');
        
        // Carrega comandos existentes
        $commands = [];
        if (file_exists($commandsFile)) {
            $content = file_get_contents($commandsFile);
            $commands = json_decode($content, true) ?? [];
        }
        
        // Verifica duplicatas
        $exists = collect($commands)->contains(function ($cmd) use ($data) {
            return $cmd['type'] === 'send_message' 
                && $cmd['to'] === $data['customer_phone']
                && str_contains($cmd['message'], $data['due_date']);
        });
        
        if (!$exists) {
            // Formata mensagem de lembrete
            $daysText = $data['days_until_due'] === 1 ? 'amanhã' : "em {$data['days_until_due']} dias";
            
            $message = "🔔 *Lembrete de Vencimento - {$data['tenant_name']}*\n\n";
            $message .= "Olá, *{$data['customer_name']}*! 👋\n\n";
            $message .= "Seu plano *{$data['plan_name']}* vence *{$daysText}* (" . \Carbon\Carbon::parse($data['due_date'])->format('d/m/Y') . ").\n\n";
            $message .= "💰 Valor: *R$ " . number_format($data['amount'], 2, ',', '.') . "*\n\n";
            
            if (!empty($data['payment_url'])) {
                $message .= "🔗 Link para renovação:\n{$data['payment_url']}\n\n";
            }
            
            $message .= "📲 Renove agora para continuar aproveitando todos os benefícios!\n\n";
            $message .= "_Mensagem automática do PagBy_";
            
            // Adiciona comando à fila
            $commands[] = [
                'type' => 'send_message',
                'to' => $data['customer_phone'],
                'message' => $message,
                'created_at' => now()->toIso8601String(),
                'metadata' => [
                    'payment_id' => $data['payment_id'],
                    'tenant_id' => $data['tenant_id'],
                    'due_date' => $data['due_date'],
                ]
            ];
            
            file_put_contents(
                $commandsFile,
                json_encode($commands, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );
            
            $this->info("✅ Comando criado: {$data['customer_name']} - Vence em {$data['days_until_due']} dias");
        }
    }
}

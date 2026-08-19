<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PagByPayment;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckExpiringPagByPayments extends Command
{
    protected $signature = 'pagby:check-expiring-payments {--days=3 : Dias antes do vencimento}';
    protected $description = 'Verifica planos PagBy (SaaS) que vão expirar e gera lembretes';

    public function handle()
    {
        $days = (int) $this->option('days');
        $targetDate = Carbon::now()->addDays($days)->format('Y-m-d');
        
        $this->info("🔍 Verificando planos PagBy que vencem em {$days} dias ({$targetDate})...");
        
        // Busca pagamentos PagBy que vencem no período especificado
        // Pagamentos do PagBy têm período de 30 dias a partir do created_at
        $expiringPayments = PagByPayment::on('mysql')
            ->whereIn('status', ['PENDING', 'CONFIRMED', 'RECEIVED'])
            ->whereRaw("DATE_ADD(DATE(created_at), INTERVAL 30 DAY) = ?", [$targetDate])
            ->get();
        
        $this->info("📋 Encontrados {$expiringPayments->count()} planos PagBy expirando");
        
        foreach ($expiringPayments as $payment) {
            try {
                // Busca informações do tenant
                $tenant = Tenant::find($payment->tenant_id);
                
                if (!$tenant) {
                    $this->warn("⚠️  Tenant {$payment->tenant_id} não encontrado");
                    continue;
                }
                
                // Busca o telefone do proprietário no tenant
                $phone = null;
                $ownerName = null;
                $ownerEmail = null;
                
                try {
                    tenancy()->initialize($tenant);
                    
                    // Busca usuário Proprietário no tenant
                    $owner = \App\Models\User::on('tenant')
                        ->whereHas('roles', function($query) {
                            $query->where('name', 'Proprietário');
                        })
                        ->where('whatsapp_activated', true)
                        ->first();
                    
                    if ($owner) {
                        $phone = $owner->phone;
                        $ownerName = $owner->name;
                        $ownerEmail = $owner->email;
                        $this->info("   📱 Proprietário encontrado: {$ownerName} - {$phone}");
                    } else {
                        // Se não encontrar proprietário com WhatsApp ativado, busca qualquer proprietário
                        $owner = \App\Models\User::on('tenant')
                            ->whereHas('roles', function($query) {
                                $query->where('name', 'Proprietário');
                            })
                            ->first();
                        
                        if ($owner) {
                            $phone = $owner->phone;
                            $ownerName = $owner->name;
                            $ownerEmail = $owner->email;
                            $this->warn("   ⚠️  Proprietário sem WhatsApp ativado: {$ownerName}");
                        } else {
                            $this->warn("   ⚠️  Nenhum proprietário encontrado no tenant");
                        }
                    }
                    
                    tenancy()->end();
                } catch (\Exception $e) {
                    $this->warn("⚠️  Erro ao buscar proprietário: {$e->getMessage()}");
                    tenancy()->end();
                }
                
                // Se não encontrou telefone, pula
                if (!$phone) {
                    $this->warn("⚠️  Sem telefone para enviar lembrete");
                    continue;
                }
                
                // Calcula a data de vencimento (created_at + 30 dias)
                $dueDate = Carbon::parse($payment->created_at)->addDays(30);
                
                // Determina o nome do plano baseado no número de funcionários
                $planName = "Plano PagBy";
                if ($payment->employee_count) {
                    $planName .= " ({$payment->employee_count} " . 
                        ($payment->employee_count == 1 ? 'funcionário' : 'funcionários') . ")";
                }
                
                // Armazena lembrete no arquivo JSON para o bot WhatsApp processar
                $this->storeReminder([
                    'payment_id' => $payment->id,
                    'tenant_id' => $payment->tenant_id,
                    'tenant_name' => $tenant->name ?? $tenant->id,
                    'plan_name' => $planName,
                    'amount' => $payment->amount,
                    'due_date' => $dueDate->format('Y-m-d'),
                    'days_until_due' => $days,
                    'customer_name' => $ownerName,
                    'customer_phone' => $phone,
                    'customer_email' => $ownerEmail,
                    'payment_url' => config('app.url') . '/subscription/plans',
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'sent' => false,
                ]);
                
                $this->info("✅ Lembrete criado: {$ownerName} - {$planName} - R$ {$payment->amount}");
                
                Log::info('📅 Lembrete de plano PagBy criado', [
                    'payment_id' => $payment->id,
                    'tenant' => $payment->tenant_id,
                    'customer' => $ownerName,
                    'due_date' => $dueDate->format('Y-m-d'),
                ]);
                
            } catch (\Exception $e) {
                $this->error("❌ Erro ao processar payment {$payment->id}: {$e->getMessage()}");
                Log::error('Erro ao criar lembrete de plano PagBy', [
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
        $commandsFile = base_path('storage/app/whatsapp_commands.json');
        
        // Carrega comandos existentes
        $commands = [];
        if (file_exists($commandsFile)) {
            $content = file_get_contents($commandsFile);
            $commands = json_decode($content, true) ?? [];
        }
        
        // Verifica duplicatas
        $exists = collect($commands)->contains(function ($cmd) use ($data) {
            return $cmd['type'] === 'payment_reminder' 
                && $cmd['customer_phone'] === $data['customer_phone']
                && str_contains($cmd['message'] ?? '', $data['due_date']);
        });
        
        if (!$exists) {
            // Formata mensagem de lembrete
            $daysText = $data['days_until_due'] === 1 ? 'amanhã' : "em {$data['days_until_due']} dias";
            
            $message = "🔔 *Lembrete de Vencimento - PagBy*\n\n";
            $message .= "Olá, *{$data['customer_name']}*! 👋\n\n";
            $message .= "Seu *{$data['plan_name']}* vence *{$daysText}* (" . \Carbon\Carbon::parse($data['due_date'])->format('d/m/Y') . ").\n\n";
            $message .= "💰 Valor: *R$ " . number_format($data['amount'], 2, ',', '.') . "*\n\n";
            $message .= "🔗 Acesse o painel para renovar:\n{$data['payment_url']}\n\n";
            $message .= "📲 Renove agora para continuar usando o PagBy sem interrupções!\n\n";
            $message .= "_Mensagem automática do PagBy_";
            
            // Adiciona comando à fila
            $commands[] = [
                'type' => 'payment_reminder',
                'customer_phone' => $data['customer_phone'],
                'customer_name' => $data['customer_name'],
                'plan_name' => $data['plan_name'],
                'amount' => $data['amount'],
                'due_date' => $data['due_date'],
                'days_until_due' => $data['days_until_due'],
                'payment_url' => $data['payment_url'],
                'tenant_name' => $data['tenant_name'],
                'message' => $message,
                'created_at' => now()->toIso8601String(),
                'metadata' => [
                    'payment_id' => $data['payment_id'],
                    'tenant_id' => $data['tenant_id'],
                    'payment_type' => 'pagby_saas',
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

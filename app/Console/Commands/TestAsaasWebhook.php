<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PagByPayment;
use Illuminate\Support\Facades\Log;

class TestAsaasWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asaas:test-webhook {payment_id : ID do pagamento na tabela pag_by_payments}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simula um webhook do Asaas para testar a criação automática do tenant';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $paymentId = $this->argument('payment_id');
        
        $this->info("🔍 Buscando pagamento ID: {$paymentId}...");
        
        $payment = PagByPayment::on('mysql')->find($paymentId);
        
        if (!$payment) {
            $this->error("❌ Pagamento não encontrado!");
            return 1;
        }
        
        $this->info("✅ Pagamento encontrado:");
        $this->table(
            ['Campo', 'Valor'],
            [
                ['ID', $payment->id],
                ['Tenant ID', $payment->tenant_id],
                ['Status Atual', $payment->status],
                ['Valor', 'R$ ' . number_format($payment->amount, 2, ',', '.')],
                ['Plano', $payment->plan ?? 'N/A'],
                ['Contact ID', $payment->contact_id],
            ]
        );
        
        if (!str_starts_with($payment->tenant_id, 'temp_')) {
            $this->warn("⚠️  Tenant já foi criado anteriormente!");
            $this->info("Tenant ID: {$payment->tenant_id}");
            
            if (!$this->confirm('Deseja simular o webhook mesmo assim?')) {
                return 0;
            }
        }
        
        $this->info("\n🚀 Simulando webhook do Asaas...");
        
        // Simular payload do webhook
        $webhookPayload = [
            'event' => 'PAYMENT_RECEIVED',
            'payment' => [
                'id' => $payment->external_id ?? 'pay_test_' . uniqid(),
                'status' => 'RECEIVED',
                'customer' => $payment->contact_id,
                'value' => $payment->amount,
                'netValue' => $payment->amount * 0.95,
                'billingType' => 'CREDIT_CARD',
                'confirmedDate' => now()->toISOString(),
            ]
        ];
        
        try {
            // Chamar o webhook internamente via controller
            $this->info("📡 Processando webhook internamente...");
            
            $controller = new \App\Http\Controllers\PagBySubscriptionController();
            $request = new \Illuminate\Http\Request($webhookPayload);
            
            // Simular headers do Asaas
            $request->headers->set('Content-Type', 'application/json');
            $request->headers->set('User-Agent', 'Asaas-Webhook-Test');
            
            // Processar webhook
            $response = $controller->webhook($request);
            
            $this->info("✅ Webhook processado com sucesso!");
            
            // Recarregar o pagamento
            $payment = $payment->fresh();
            
            $this->info("\n📊 Resultado:");
            $this->table(
                ['Campo', 'Valor'],
                [
                    ['Novo Status', $payment->status],
                    ['Tenant ID', $payment->tenant_id],
                    ['Tenant Criado?', !str_starts_with($payment->tenant_id, 'temp_') ? 'SIM ✅' : 'NÃO ❌'],
                ]
            );
            
            if (!str_starts_with($payment->tenant_id, 'temp_')) {
                $tenant = \App\Models\Tenant::find($payment->tenant_id);
                if ($tenant) {
                    $domain = $tenant->domains->first();
                    $this->info("\n🎉 Tenant criado com sucesso!");
                    $this->info("🔗 Domínio: " . ($domain ? $domain->domain : 'N/A'));
                    $this->info("📧 Email: {$tenant->email}");
                    $this->info("📅 Assinatura até: " . $tenant->subscription_end?->format('d/m/Y'));
                }
            } else {
                $this->warn("\n⚠️  Tenant não foi criado. Verifique os logs:");
                $this->info("tail -f storage/logs/laravel.log | grep -i 'tenant\\|erro'");
            }
            
            $this->newLine();
            $this->info("📝 Verifique os logs completos:");
            $this->info("tail -f storage/logs/laravel.log");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Erro ao simular webhook:");
            $this->error($e->getMessage());
            Log::error('Erro ao simular webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}

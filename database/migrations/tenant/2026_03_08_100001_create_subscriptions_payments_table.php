<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela para controlar cada pagamento mensal das assinaturas dos clientes
     * (modelo SEM split - subconta do salão)
     */
    public function up(): void
    {
        Schema::create('subscriptions_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            
            // Asaas
            $table->string('asaas_payment_id')->unique()->comment('ID da cobrança individual no Asaas');
            $table->string('asaas_invoice_url')->nullable()->comment('URL do boleto/invoice');
            
            // Detalhes do pagamento
            $table->decimal('amount', 10, 2)->comment('Valor cobrado');
            $table->decimal('net_value', 10, 2)->nullable()->comment('Valor líquido (descontando taxas Asaas)');
            $table->string('billing_type')->comment('PIX, BOLETO, CREDIT_CARD');
            $table->date('due_date')->comment('Data de vencimento');
            $table->date('payment_date')->nullable()->comment('Data do pagamento efetivo');
            
            // Status
            $table->enum('status', [
                'pending',      // Aguardando pagamento
                'confirmed',    // Confirmado mas não recebido
                'received',     // Recebido ✅
                'overdue',      // Vencido
                'refunded',     // Estornado
                'cancelled'     // Cancelado
            ])->default('pending');
            
            // Dados completos do webhook
            $table->json('asaas_data')->nullable()->comment('Payload completo do Asaas');
            
            // Auditoria
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
            
            // Índices
            $table->index(['subscription_id', 'status']);
            $table->index('due_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions_payments');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona campos Asaas à tabela subscriptions
     * (modelo SEM split - subconta do salão)
     */
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // ID da assinatura no Asaas (gera cobranças recorrentes)
            $table->string('asaas_subscription_id')->nullable()->unique()
                ->after('mp_payment_id')
                ->comment('ID da assinatura recorrente no Asaas');
            
            // ID do customer no Asaas (relacionado ao user_id)
            $table->string('asaas_customer_id')->nullable()
                ->after('asaas_subscription_id')
                ->comment('ID do cliente no Asaas');
            
            // Configurações da assinatura
            $table->string('billing_type')->nullable()
                ->after('asaas_customer_id')
                ->comment('Forma de pagamento: PIX, BOLETO, CREDIT_CARD');
            
            $table->decimal('value', 10, 2)->nullable()
                ->after('billing_type')
                ->comment('Valor da cobrança recorrente');
            
            $table->enum('cycle', ['MONTHLY', 'QUARTERLY', 'SEMIANNUALLY', 'YEARLY'])
                ->default('MONTHLY')
                ->after('value')
                ->comment('Ciclo de cobrança');
            
            $table->date('next_due_date')->nullable()
                ->after('end_date')
                ->comment('Próxima data de vencimento');
            
            // Dados do webhook
            $table->json('asaas_data')->nullable()
                ->after('next_due_date')
                ->comment('Dados completos da assinatura no Asaas');
            
            // Índices
            $table->index('asaas_subscription_id');
            $table->index('asaas_customer_id');
            $table->index('next_due_date');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex(['asaas_subscription_id']);
            $table->dropIndex(['asaas_customer_id']);
            $table->dropIndex(['next_due_date']);
            
            $table->dropColumn([
                'asaas_subscription_id',
                'asaas_customer_id',
                'billing_type',
                'value',
                'cycle',
                'next_due_date',
                'asaas_data'
            ]);
        });
    }
};

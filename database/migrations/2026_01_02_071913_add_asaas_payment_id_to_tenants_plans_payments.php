<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mysql')->table('tenants_plans_payments', function (Blueprint $table) {
            // Adiciona campo para ID de pagamento individual do Asaas
            $table->string('asaas_payment_id')->nullable()->after('asaas_subscription_id')
                ->comment('ID do pagamento individual no Asaas (cada cobrança da assinatura)');
            
            // Índice para busca rápida por webhook
            $table->index('asaas_payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->table('tenants_plans_payments', function (Blueprint $table) {
            $table->dropIndex(['asaas_payment_id']);
            $table->dropColumn('asaas_payment_id');
        });
    }
};

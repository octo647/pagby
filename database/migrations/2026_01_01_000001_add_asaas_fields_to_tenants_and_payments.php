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
        // Adiciona campos Asaas na tabela tenants
        Schema::connection('mysql')->table('tenants', function (Blueprint $table) {
            $table->string('asaas_wallet_id')->nullable()->after('subscription_status')
                ->comment('ID da subconta Asaas para split de pagamentos');
            $table->text('asaas_account_data')->nullable()->after('asaas_wallet_id')
                ->comment('Dados completos da subconta Asaas (JSON)');
        });

        // Adiciona campos Asaas na tabela tenants_plans_payments
        Schema::connection('mysql')->table('tenants_plans_payments', function (Blueprint $table) {
            $table->string('asaas_subscription_id')->nullable()->after('mp_payment_id')
                ->comment('ID da assinatura no Asaas');
            $table->text('asaas_data')->nullable()->after('mercadopago_data')
                ->comment('Dados completos da assinatura Asaas (JSON)');
            
            // Adicionar índice para busca rápida
            $table->index('asaas_subscription_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->table('tenants', function (Blueprint $table) {
            $table->dropColumn(['asaas_wallet_id', 'asaas_account_data']);
        });

        Schema::connection('mysql')->table('tenants_plans_payments', function (Blueprint $table) {
            $table->dropIndex(['asaas_subscription_id']);
            $table->dropColumn(['asaas_subscription_id', 'asaas_data']);
        });
    }
};

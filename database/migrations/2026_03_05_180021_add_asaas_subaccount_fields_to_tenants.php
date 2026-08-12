<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adiciona campos necessários para o modelo sem split:
     * - Subcontas Asaas podem operar independentemente
     * - Cada tenant recebe pagamentos diretos (100%, sem split)
     * - API key da subconta permite emitir cobranças em nome do tenant
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // ID da subconta Asaas (diferente de wallet_id usado no split)
            $table->string('asaas_account_id')->nullable()
                ->after('asaas_account_data')
                ->comment('ID da subconta Asaas (account) para modelo sem split');
            
            // API key da subconta para receber pagamentos diretos
            // IMPORTANTE: Será armazenada CRIPTOGRAFADA por segurança
            $table->text('asaas_api_key')->nullable()
                ->after('asaas_account_id')
                ->comment('API key da subconta Asaas (CRIPTOGRAFADA)');
            
            // Status de aprovação da subconta
            $table->enum('asaas_account_status', [
                'pending',      // Aguardando aprovação Asaas (até 48h em produção)
                'active',       // Ativa - pode receber pagamentos
                'rejected',     // Rejeitada pelo Asaas
                'disabled'      // Desabilitada manualmente
            ])->nullable()
                ->after('asaas_api_key')
                ->comment('Status de aprovação da subconta Asaas');
            
            // Data de ativação da subconta
            $table->timestamp('asaas_account_activated_at')->nullable()
                ->after('asaas_account_status')
                ->comment('Data de aprovação/ativação da subconta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'asaas_account_id',
                'asaas_api_key',
                'asaas_account_status',
                'asaas_account_activated_at'
            ]);
        });
    }
};

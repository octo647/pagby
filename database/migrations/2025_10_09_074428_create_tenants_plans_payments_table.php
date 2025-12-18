<?php
// database/migrations/xxxx_create_payments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
         Schema::create('tenants_plans_payments', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique(); // ID do MercadoPago
            $table->string('mp_payment_id')->nullable(); // ID da preferência
            $table->string('tenant_id')->nullable(); // ID do tenant do qual o usuário pertence
            $table->string('plan_id'); //identificação do plano do tenant            
            $table->decimal('amount', 10, 2); // Valor do pagamento            
            $table->string('status'); // pending, approved, rejected, cancelled
            $table->string('payment_method')->nullable();
            $table->string('payment_type')->nullable();
            $table->json('payer_data')->nullable(); // Dados do pagador
            $table->json('mercadopago_data')->nullable(); // Resposta completa do MP
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();           
            $table->index(['status', 'plan_id']);
            $table->index('external_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants_plans_payments');
    }
};
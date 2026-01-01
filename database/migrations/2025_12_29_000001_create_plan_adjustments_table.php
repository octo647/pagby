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
        Schema::connection('mysql')->create('plan_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->enum('type', ['credit', 'debit']); // crédito ou débito
            $table->decimal('amount', 10, 2); // valor do ajuste
            $table->integer('employee_count_before'); // número anterior de funcionários
            $table->integer('employee_count_after'); // novo número de funcionários
            $table->string('plan_period'); // mensal, trimestral, etc
            $table->integer('days_remaining'); // dias restantes do plano
            $table->decimal('percentage_remaining', 5, 2); // percentual restante
            $table->enum('status', ['pending', 'applied', 'paid', 'cancelled'])->default('pending');
            $table->string('asaas_payment_id')->nullable(); // ID da cobrança no Asaas (se débito)
            $table->string('asaas_invoice_url')->nullable(); // URL da fatura (se débito)
            $table->timestamp('applied_at')->nullable(); // quando foi aplicado (para créditos)
            $table->timestamp('paid_at')->nullable(); // quando foi pago (para débitos)
            $table->text('notes')->nullable(); // observações
            $table->timestamps();
            
            // Foreign key
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->dropIfExists('plan_adjustments');
    }
};

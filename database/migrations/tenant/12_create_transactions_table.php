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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique(); // Código único da transação
            $table->unsignedBigInteger('appointment_id'); // Relaciona com a tabela appointments
            $table->foreign('appointment_id')
                ->references('id')
                ->on('appointments')
                ->cascadeOnDelete();
            $table->decimal('amount', 10, 2); // Valor da transação
            $table->string('payment_method'); // Método de pagamento (ex.: cartão, dinheiro)
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending'); // Status da transação
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
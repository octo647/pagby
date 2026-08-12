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
        Schema::create('comandas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('users')->onDelete('set null');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('set null')->comment('Agendamento associado à comanda (opcional)');
            $table->string('numero_comanda')->unique(); // Número sequencial da comanda
            $table->string('cliente_nome');
            $table->string('cliente_telefone')->nullable();
            $table->foreignId('funcionario_id')->constrained('users')->onDelete('cascade'); // Funcionário responsável
            $table->enum('status', ['Aberta', 'Finalizada', 'Cancelada'])->default('Aberta');
            $table->timestamp('data_abertura')->useCurrent();
            $table->timestamp('data_fechamento')->nullable();
            $table->decimal('subtotal_servicos', 10, 2)->default(0);
            $table->decimal('subtotal_produtos', 10, 2)->default(0);
            $table->decimal('desconto_servicos', 10, 2)->default(0);
            $table->decimal('desconto_produtos', 10, 2)->default(0);
            $table->decimal('total_geral', 10, 2)->default(0);
            $table->text('observacoes')->nullable();
            $table->timestamps();
            // Índices para otimizar consultas
            $table->index(['branch_id', 'status']);
            $table->index(['funcionario_id', 'data_abertura']);
            $table->index('numero_comanda');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comandas');
    }
};
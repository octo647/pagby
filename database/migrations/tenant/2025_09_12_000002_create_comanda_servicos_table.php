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
        Schema::create('comanda_servicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comanda_id')->constrained('comandas')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->foreignId('funcionario_id')->constrained('users')->onDelete('cascade'); // Funcionário que executará o serviço
            $table->integer('quantidade')->default(1);
            $table->decimal('preco_unitario', 8, 2);
            $table->decimal('subtotal', 10, 2); // quantidade * preco_unitario
            $table->enum('status_servico', ['Aguardando', 'Em Andamento', 'Concluído'])->default('Aguardando');
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            // Índices
            $table->index(['comanda_id', 'status_servico']);
            $table->index('funcionario_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comanda_servicos');
    }
};
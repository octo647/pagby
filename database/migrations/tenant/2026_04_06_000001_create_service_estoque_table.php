<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabela pivot para relacionar serviços com produtos recomendados.
     * Permite configurar quais produtos sugerir quando o cliente agendar um serviço.
     */
    public function up(): void
    {
        Schema::create('service_estoque', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->foreignId('estoque_id')->constrained('estoque')->onDelete('cascade');
            
            // Ordem de prioridade na sugestão (1 = primeiro a ser oferecido)
            $table->integer('priority')->default(1);
            
            // Desconto opcional para incentivar compra (ex: 10 = 10% off)
            $table->decimal('discount_percentage', 5, 2)->default(0);
            
            // Se está ativo para sugestão
            $table->boolean('is_active')->default(true);
            
            // Observações internas (ex: "Recomendar apenas para cortes premium")
            $table->text('observacoes')->nullable();
            
            $table->timestamps();
            
            // Índices para performance
            $table->index('service_id');
            $table->index('estoque_id');
            $table->index(['service_id', 'priority', 'is_active']);
            
            // Evitar duplicação: mesmo produto não pode estar 2x no mesmo serviço
            $table->unique(['service_id', 'estoque_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_estoque');
    }
};

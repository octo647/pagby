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
        Schema::create('estoque', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('produto_nome');
            $table->string('categoria')->nullable();
            $table->integer('quantidade_atual')->default(0);
            $table->integer('quantidade_minima')->default(0);
            $table->decimal('preco_unitario', 10, 2)->nullable();
            $table->string('fornecedor')->nullable();
            $table->date('data_validade')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            // Índices para otimizar consultas
            $table->index(['branch_id', 'categoria']);
            $table->index('produto_nome');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estoque');
    }
};
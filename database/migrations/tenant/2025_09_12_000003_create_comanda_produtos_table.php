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
        Schema::create('comanda_produtos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('funcionario_id')->nullable();
            $table->foreign('funcionario_id')->references('id')->on('users')->nullOnDelete();
            $table->foreignId('comanda_id')->constrained('comandas')->onDelete('cascade');
            $table->foreignId('estoque_id')->constrained('estoque')->onDelete('cascade'); // Produto do estoque
            $table->integer('quantidade');
            $table->decimal('preco_unitario', 8, 2); // Preço de venda (pode ser diferente do preço do estoque)
            $table->decimal('subtotal', 10, 2); // quantidade * preco_unitario
            $table->text('observacoes')->nullable();
            $table->timestamps();
            // Índices
            $table->index('comanda_id');
            $table->index('estoque_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comanda_produtos');
    }
};
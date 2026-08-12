<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Sistema de recompensas e fidelidade.
     * Clientes ganham bônus ao comprar produtos que podem usar em serviços.
     */
    public function up(): void
    {
        Schema::create('fidelidade_rewards', function (Blueprint $table) {
            $table->id();
            
            // Cliente que ganhou a recompensa
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Tipo de recompensa
            $table->enum('tipo', ['credito', 'cupom', 'servico_gratis'])->default('credito');
            
            // Para créditos: valor em reais
            $table->decimal('valor_credito', 10, 2)->nullable();
            
            // Para cupons: código e percentual de desconto
            $table->string('codigo_cupom')->nullable()->unique();
            $table->decimal('percentual_desconto', 5, 2)->nullable();
            
            // Para serviço grátis: qual serviço
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('cascade');
            
            // Origem da recompensa
            $table->enum('origem', ['compra_produto', 'promocao', 'indicacao', 'aniversario'])
                ->default('compra_produto');
            
            // Referência à compra que gerou a recompensa
            $table->foreignId('comanda_produto_id')->nullable()
                ->constrained('comanda_produtos')->onDelete('set null');
            
            // Validade
            $table->date('validade');
            
            // Status
            $table->enum('status', ['ativo', 'usado', 'expirado'])->default('ativo');
            
            // Quando foi usado
            $table->timestamp('usado_em')->nullable();
            $table->foreignId('usado_em_comanda_id')->nullable()
                ->constrained('comandas')->onDelete('set null');
            
            // Descrição para o cliente
            $table->string('descricao')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index(['user_id', 'status']);
            $table->index(['codigo_cupom', 'status']);
            $table->index('validade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fidelidade_rewards');
    }
};

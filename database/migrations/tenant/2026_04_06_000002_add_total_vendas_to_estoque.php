<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adiciona contador de vendas para sistema de recomendação inteligente.
     * Produtos mais vendidos serão sugeridos automaticamente quando não houver
     * relacionamento manual configurado.
     */
    public function up(): void
    {
        Schema::table('estoque', function (Blueprint $table) {
            // Contador total de vendas do produto
            $table->integer('total_vendas')->default(0)->after('quantidade_atual');
            
            // Índice para otimizar consulta de produtos mais vendidos
            $table->index(['branch_id', 'total_vendas']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estoque', function (Blueprint $table) {
            $table->dropIndex(['branch_id', 'total_vendas']);
            $table->dropColumn('total_vendas');
        });
    }
};

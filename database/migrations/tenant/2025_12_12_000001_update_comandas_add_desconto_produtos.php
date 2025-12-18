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
        Schema::table('comandas', function (Blueprint $table) {
            // Renomear o campo desconto para desconto_servicos
            if (Schema::hasColumn('comandas', 'desconto')) {
                $table->renameColumn('desconto', 'desconto_servicos');
            }
            // Adicionar o campo desconto_produtos
            if (!Schema::hasColumn('comandas', 'desconto_produtos')) {
                $table->decimal('desconto_produtos', 10, 2)->default(0)->after('desconto_servicos');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comandas', function (Blueprint $table) {
            // Reverter o nome do campo
            if (Schema::hasColumn('comandas', 'desconto_servicos')) {
                $table->renameColumn('desconto_servicos', 'desconto');
            }
            // Remover o campo desconto_produtos
            if (Schema::hasColumn('comandas', 'desconto_produtos')) {
                $table->dropColumn('desconto_produtos');
            }
        });
    }
};

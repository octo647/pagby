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
        Schema::table('caixa', function (Blueprint $table) {
            // Remove o índice único antigo de 'data', se existir
            try {
                $table->dropUnique('caixa_data_unique');
            } catch (\Exception $e) {}

            // Cria o índice único composto branch_id+data
            $table->unique(['branch_id', 'data']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

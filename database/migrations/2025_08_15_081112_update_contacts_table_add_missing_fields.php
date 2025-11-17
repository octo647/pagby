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
        Schema::table('contacts', function (Blueprint $table) {
            // Adicionar campos que estão sendo usados pelo model mas não existem na migração original
            if (!Schema::hasColumn('contacts', 'owner_name')) {
                $table->string('owner_name')->nullable();
            }
            if (!Schema::hasColumn('contacts', 'tipo')) {
                $table->string('tipo')->nullable();
            }
            if (!Schema::hasColumn('contacts', 'tenant_name')) {
                $table->string('tenant_name')->nullable();
            }
            if (!Schema::hasColumn('contacts', 'neighborhood')) {
                $table->string('neighborhood')->nullable();
            }
            if (!Schema::hasColumn('contacts', 'cpf')) {
                $table->string('cpf')->nullable();
            }
            if (!Schema::hasColumn('contacts', 'employee_count')) {
                $table->integer('employee_count')->nullable();
            }
            
            // Renomear o campo 'salon' para 'tenant_name' se existir
            if (Schema::hasColumn('contacts', 'salon') && !Schema::hasColumn('contacts', 'tenant_name')) {
                $table->renameColumn('salon', 'tenant_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            // Reverter as alterações
            $table->dropColumn(['owner_name', 'tipo', 'neighborhood']);
            
            // Renomear de volta se necessário
            if (Schema::hasColumn('contacts', 'salon_name')) {
                $table->renameColumn('salon_name', 'salon');
            }
        });
    }
};

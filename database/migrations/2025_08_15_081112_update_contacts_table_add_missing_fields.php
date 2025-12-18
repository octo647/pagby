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
            // Adicionar campos se não existirem
            if (!Schema::hasColumn('contacts', 'owner_name')) {
                $table->string('owner_name')->nullable();
            }
            if (!Schema::hasColumn('contacts', 'tipo')) {
                $table->string('tipo')->nullable();
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
        });
        // Renomear o campo 'salon' para 'tenant_name' se apropriado (fora do closure para evitar problemas de transação)
        if (Schema::hasColumn('contacts', 'salon') && !Schema::hasColumn('contacts', 'tenant_name')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->renameColumn('salon', 'tenant_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            // Remover campos apenas se existirem
            if (Schema::hasColumn('contacts', 'owner_name')) {
                $table->dropColumn('owner_name');
            }
            if (Schema::hasColumn('contacts', 'tipo')) {
                $table->dropColumn('tipo');
            }
            if (Schema::hasColumn('contacts', 'neighborhood')) {
                $table->dropColumn('neighborhood');
            }
            if (Schema::hasColumn('contacts', 'cpf')) {
                $table->dropColumn('cpf');
            }
            if (Schema::hasColumn('contacts', 'employee_count')) {
                $table->dropColumn('employee_count');
            }
        });
        // Renomear de volta se necessário
        if (Schema::hasColumn('contacts', 'tenant_name') && !Schema::hasColumn('contacts', 'salon')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->renameColumn('tenant_name', 'salon');
            });
        }
    }
};

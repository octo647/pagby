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
        if (Schema::hasTable('contacts')) {
            // Se a tabela já existir, garantir que campos e renomeações estejam corretos
            if (Schema::hasColumn('contacts', 'salon') && !Schema::hasColumn('contacts', 'tenant_name')) {
                Schema::table('contacts', function (Blueprint $table) {
                    $table->renameColumn('salon', 'tenant_name');
                });
            }
            return;
        }
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('subscription_plan', 20)->nullable();
            $table->string('phone')->nullable();
            $table->text('notas')->nullable();
            $table->text('address')->nullable();
            $table->string('complement')->nullable();
            $table->string('cep')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('tipo')->nullable();
            $table->string('cpf')->nullable();
            $table->integer('employee_count')->nullable();
            $table->string('tenant_name')->nullable();
            $table->timestamp('contract_accepted_at')->nullable();
            $table->timestamps();
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

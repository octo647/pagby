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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->decimal('price', 8, 2);
            $table->integer('duration_days'); // duração do plano
            $table->json('services'); // ids dos serviços inclusos
            $table->json('additional_services'); // ids dos produtos inclusos
            $table->json('features'); // características do plano
            $table->boolean('active')->default(true); // se o plano está ativo
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade'); // referência ao salão
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // usuário que criou o plano
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null'); // usuário que atualizou o plano
            $table->softDeletes(); // para permitir exclusão suave
            $table->unique(['name', 'branch_id'], 'unique_plan_name_per_branch'); // garantir que o nome do plano seja único por salão
            $table->index(['branch_id', 'active'], 'index_plans_by_branch_and_active'); // índice para consultas por salão e status ativo
            $table->index(['created_by', 'updated_by'], 'index_plans_by_users'); // índice para consultas por usuários

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};

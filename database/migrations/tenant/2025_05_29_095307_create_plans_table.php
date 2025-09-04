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
            $table->json('features'); // características do plano
            $table->json('allowed_days')->nullable(); // dias permitidos (array/json)
            $table->boolean('active')->default(true); // se o plano está ativo
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // usuário que criou o plano
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null'); // usuário que atualizou o plano
            $table->softDeletes(); // para permitir exclusão suave
            $table->unique(['name'], 'unique_plan_name'); // garantir que o nome do plano seja único
            
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

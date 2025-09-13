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
        Schema::create('branch_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->decimal('price', 10, 2); // Preço específico da filial
            $table->integer('duration_minutes')->default(30); // Duração padrão em minutos
            $table->boolean('is_active')->default(true); // Se o serviço está ativo nesta filial
            $table->text('description')->nullable(); // Descrição específica da filial
            $table->timestamps();
            
            // Índice único para evitar duplicação service+branch
            $table->unique(['branch_id', 'service_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_services');
    }
};

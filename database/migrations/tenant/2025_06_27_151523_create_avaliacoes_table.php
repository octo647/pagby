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
        Schema::create('avaliacoes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->unsignedBigInteger('branch_id');
            $table->foreign('branch_id')
                ->references('id')
                ->on('branches')
                ->cascadeOnDelete();
            $table->date('data');
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->foreign('appointment_id')
                ->references('id')
                ->on('appointments')
                ->nullOnDelete();
            $table->unsignedTinyInteger('avaliacao')
                ->comment('Nota da avaliação, de 1 a 5')
                ->default('5')
                ->comment('Avaliação de 1 a 5');
            $table->text('comentario')->nullable()
                ->comment('Comentário da avaliação');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avaliacoes');
    }
};

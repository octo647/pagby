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
        Schema::create('caixa', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->decimal('total_entrada', 10, 2)->default(0);
            $table->decimal('total_saida', 10, 2)->default(0);
            $table->decimal('saldo_final', 10, 2)->default(0);
            $table->date('data');
            $table->unsignedBigInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->unique(['branch_id', 'data']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_caixa');
    }
};

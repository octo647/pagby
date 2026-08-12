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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('plan_id')->constrained();
            $table->string('mp_payment_id')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['Ativo', 'Expirado', 'Cancelado'])->default('Ativo');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // usuário que criou a assinatura
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null'); // usuário que atualizou a assinatura
            $table->softDeletes(); // para permitir exclusão suave
            $table->unique(['user_id', 'plan_id', 'status'], 'unique_user_plan_subscription'); // garantir que um usuário não tenha a mesma assinatura de plano mais de uma vez
            $table->index(['user_id', 'status'], 'index_subscriptions_by_user_and_status'); // índice para consultas por usuário e status
            $table->index(['plan_id', 'status'], 'index_subscriptions_by_plan_and_status'); // índice para consultas por plano e status
            $table->index([ 'status'], 'index_subscriptions_by_status'); // índice para consultas por status
            $table->index(['created_by', 'updated_by'], 'index_subscriptions_by_users'); // índice para consultas por usuários  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};

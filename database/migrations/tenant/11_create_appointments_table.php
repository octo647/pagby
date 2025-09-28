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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')
            ->references('id')
            ->on('users')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
            $table->unsignedBigInteger('branch_id');
            $table->foreign('branch_id')
            ->references('id')
            ->on('branches')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')
            ->references('id')
            ->on('users')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
            $table->string('services', 250);
            $table->decimal('total', 10, 2)->default(0);
            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['Pendente', 'Confirmado', 'Realizado', 'Cancelado', 'bloqueio'])->default('Pendente');
            $table->string('notes', 250)->nullable(); 
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')
            ->references('id')
            ->on('users')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')
            ->references('id')
            ->on('users')
            ->nullOnUpdate()
            ->nullOnDelete();
            $table->date('cancellation_date')->nullable();
            $table->time('cancellation_time')->nullable();
            $table->unsignedBigInteger('cancellation_by')->nullable();
            $table->foreign('cancellation_by')
            ->references('id')
            ->on('users')
            ->nullOnDelete()
            ->nullonUpdate();
            $table->string('cancellation_reason')->nullable();              
            
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['service_id']);
            $table->dropForeign(['schedule_id']);
            $table->dropForeign(['transaction_id']);
            $table->dropForeign(['payment_status']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['cancellation_reason']);        
        });
        Schema::dropIfExists('appointments');
    }
};

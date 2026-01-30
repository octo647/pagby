<?php
// database/migrations/xxxx_create_pag_by_payments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagby_payments', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->string('contact_id')->nullable();
            $table->string('mp_payment_id')->unique()->nullable();
            $table->string('asaas_payment_id')->nullable();
            $table->string('asaas_subscription_id')->nullable();
            $table->string('status'); // 'pending', 'approved', 'rejected'
            $table->decimal('amount', 8, 2);
            $table->integer('employee_count')->default(1);
            $table->string('plan')->nullable();
            $table->string('type')->default('subscription');
            $table->string('payment_method')->nullable();
            $table->json('mp_data')->nullable();
            $table->text('description')->nullable();
            $table->string('external_id')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagby_payments');
    }
};
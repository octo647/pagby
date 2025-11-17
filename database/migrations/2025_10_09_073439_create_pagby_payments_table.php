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
            $table->string('mp_payment_id')->unique()->nullable();
            $table->string('plan'); // 'basico' ou 'premium'
            $table->string('status'); // 'pending', 'approved', 'rejected'
            $table->decimal('amount', 8, 2);
            $table->string('payment_method')->nullable();
            $table->json('mp_data')->nullable();
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
<?php
// database/migrations/xxxx_add_mercadopago_fields_to_subscriptions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Só adicionar se não existirem
            if (!Schema::hasColumn('subscriptions', 'mp_payment_id')) {
                $table->string('mp_payment_id')->nullable()->unique();
            }
            if (!Schema::hasColumn('subscriptions', 'payment_method')) {
                $table->string('payment_method')->nullable();
            }
            if (!Schema::hasColumn('subscriptions', 'payment_status')) {
                $table->string('payment_status')->default('pending');
            }
            if (!Schema::hasColumn('subscriptions', 'mp_data')) {
                $table->json('mp_data')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['mp_payment_id', 'payment_method', 'payment_status', 'mp_data']);
        });
    }
};
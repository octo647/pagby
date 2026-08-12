<?php
// database/migrations/2026_01_17_000001_add_asaas_subscription_id_to_pagby_payments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagby_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('pagby_payments', 'asaas_subscription_id')) {
                $table->string('asaas_subscription_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pagby_payments', function (Blueprint $table) {
            if (Schema::hasColumn('pagby_payments', 'asaas_subscription_id')) {
                $table->dropColumn('asaas_subscription_id');
            }
        });
    }
};

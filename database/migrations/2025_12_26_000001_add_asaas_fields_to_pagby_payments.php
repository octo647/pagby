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
        Schema::table('pagby_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('pagby_payments', 'asaas_payment_id')) {
                $table->string('asaas_payment_id')->nullable()->after('mp_payment_id');
            }
            if (!Schema::hasColumn('pagby_payments', 'employee_count')) {
                $table->integer('employee_count')->default(1)->after('amount');
            }
            if (!Schema::hasColumn('pagby_payments', 'type')) {
                $table->string('type')->default('subscription')->after('status'); // subscription, renewal, etc
            }
            if (!Schema::hasColumn('pagby_payments', 'description')) {
                $table->text('description')->nullable()->after('mp_data');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagby_payments', function (Blueprint $table) {
            $table->dropColumn(['asaas_payment_id', 'employee_count', 'type', 'description']);
        });
    }
};

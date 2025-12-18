<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tenants_plans_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants_plans_payments', 'mp_payment_id')) {
                $table->string('mp_payment_id')->nullable()->after('external_id');
            }
        });
    }

    public function down()
    {
        Schema::table('tenants_plans_payments', function (Blueprint $table) {
            if (Schema::hasColumn('tenants_plans_payments', 'mp_payment_id')) {
                $table->dropColumn('mp_payment_id');
            }
        });
    }
};
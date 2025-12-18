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
       Schema::connection('mysql')->table('tenants_plans', function (Blueprint $table) {
        $table->unsignedBigInteger('plan_id')->nullable()->after('tenant_id');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->table('tenants_plans', function (Blueprint $table) {
            $table->dropColumn('plan_id');
        });
    }
};

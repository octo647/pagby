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
            $table->string('plan')->nullable()->after('employee_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagby_payments', function (Blueprint $table) {
            $table->dropColumn('plan');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrar dados: basico = 2 funcionários, premium = 5 funcionários
        DB::statement("UPDATE pagby_payments SET employee_count = 2 WHERE plan = 'basico' AND employee_count IS NULL");
        DB::statement("UPDATE pagby_payments SET employee_count = 5 WHERE plan = 'premium' AND employee_count IS NULL");
        
        // Remover campo plan (não é mais necessário)
        Schema::table('pagby_payments', function (Blueprint $table) {
            $table->dropColumn('plan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagby_payments', function (Blueprint $table) {
            $table->string('plan')->after('tenant_id');
        });
        
        // Reverter dados
        DB::statement("UPDATE pagby_payments SET plan = 'basico' WHERE employee_count <= 3");
        DB::statement("UPDATE pagby_payments SET plan = 'premium' WHERE employee_count >= 4");
    }
};

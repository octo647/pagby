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
        Schema::table('branches', function (Blueprint $table) {
           
            $table->boolean('require_commission')->default(false)->after('require_advance_payment');

            $table->decimal('commission', 5, 2)->default(0)->after('require_commission'); // Exemplo: 15.00 para 15%

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn('require_commission');
            $table->dropColumn('comission');
        });
    }
};

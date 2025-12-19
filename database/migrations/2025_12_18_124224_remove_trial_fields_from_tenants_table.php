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
        // Não remove mais os campos trial - mantemos para o período de teste
        // Schema::table('tenants', function (Blueprint $table) {
        //     $table->dropColumn(['trial_started_at', 'trial_ends_at']);
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não precisa restaurar pois não removemos
        // Schema::table('tenants', function (Blueprint $table) {
        //     $table->timestamp('trial_started_at')->nullable()->after('status');
        //     $table->timestamp('trial_ends_at')->nullable()->after('trial_started_at');
        // });
    }
};

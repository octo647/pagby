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
        // Mantém 'trial' no enum, apenas garante que está presente
        DB::statement("ALTER TABLE tenants MODIFY COLUMN subscription_status ENUM('trial', 'active', 'expired', 'suspended') DEFAULT 'trial'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Sem alteração necessária no rollback
        DB::statement("ALTER TABLE tenants MODIFY COLUMN subscription_status ENUM('trial', 'active', 'expired', 'suspended') DEFAULT 'trial'");
    }
};

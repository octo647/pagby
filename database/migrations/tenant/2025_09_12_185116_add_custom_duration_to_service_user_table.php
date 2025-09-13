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
        Schema::table('service_user', function (Blueprint $table) {
            $table->integer('custom_duration_minutes')->nullable()->after('service_id');
            $table->boolean('is_active')->default(true)->after('custom_duration_minutes');
            $table->text('notes')->nullable()->after('is_active'); // Notas específicas do funcionário para este serviço
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_user', function (Blueprint $table) {
            $table->dropColumn(['custom_duration_minutes', 'is_active', 'notes']);
        });
    }
};

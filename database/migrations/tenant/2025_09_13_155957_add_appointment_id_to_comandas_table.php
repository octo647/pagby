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
        Schema::table('comandas', function (Blueprint $table) {
            $table->foreignId('appointment_id')
                  ->nullable()
                  ->after('branch_id')
                  ->constrained('appointments')
                  ->onDelete('set null')
                  ->comment('Agendamento associado à comanda (opcional)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comandas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('appointment_id');
        });
    }
};

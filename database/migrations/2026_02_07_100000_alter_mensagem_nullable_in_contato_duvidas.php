<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contato_duvidas', function (Blueprint $table) {
            $table->text('mensagem')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('contato_duvidas', function (Blueprint $table) {
            $table->text('mensagem')->nullable(false)->change();
        });
    }
};

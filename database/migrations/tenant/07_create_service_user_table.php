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
        Schema::create('service_user', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->cascadeOnDelete();
            $table->unsignedBigInteger('service_id');
            $table->foreign('service_id')
            ->references('id')
            ->on('services')
            ->cascadeOnDelete();
            $table->integer('custom_duration_minutes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_user');
    }
};

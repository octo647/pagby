<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('booksy_prospects', function (Blueprint $table) {
            $table->id();
            $table->string('owner_name');
            $table->string('salon_name');
            $table->string('salon_type')->nullable();
            $table->unsignedInteger('employee_count')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('converted')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('booksy_prospects');
    }
};
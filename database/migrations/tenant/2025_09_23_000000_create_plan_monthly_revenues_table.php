<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('plan_monthly_revenues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->unsignedBigInteger('branch_id');
            $table->date('month'); // Armazena o primeiro dia do mês
            $table->decimal('revenue', 12, 2);
            $table->timestamps();

            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->unique(['plan_id', 'branch_id', 'month']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('plan_monthly_revenues');
    }
};

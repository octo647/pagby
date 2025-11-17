<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->enum('type', ['barbearia', 'salao_beleza', 'clinica'])->default('barbearia'); // Assuming 'barbearia' is the default type
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('instagram')->nullable();            
            $table->string('google_client_id')->nullable();
            $table->string('google_client_secret')->nullable();
            $table->string('facebook_client_id')->nullable();
            $table->string('facebook_client_secret')->nullable();
            $table->boolean('social_login_enabled')->default(false);
            $table->string('name')->nullable();
            $table->string('cnpj')->nullable();
            $table->string('fantasy_name')->nullable();
            $table->string('slug')->unique()->nullable();
            $table->string('address')->nullable();
            $table->integer('number')->nullable();
            $table->string('complement')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('cep')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('logo')->nullable();
            $table->string('plan')->nullable(); // Assuming this is a reference to a plan, adjust as necessary
            $table->string('status')->default('Ativo'); // Example status field, adjust as necessary   

            // your custom columns may go here
            $table->json('data')->nullable(); // Example of a JSON column for additional data

            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
}

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
            $table->string('type')->default('barbearia');
            $table->string('template')->default('default');
            $table->integer('employee_count')->default(1);
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
            $table->string('status')->default('Ativo');
            $table->timestamp('trial_started_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->enum('subscription_status', ['trial', 'active', 'expired', 'suspended'])->default('trial');
            $table->timestamp('subscription_started_at')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();
            $table->boolean('is_blocked')->default(false);
            $table->string('asaas_wallet_id')->nullable()->comment('ID da subconta Asaas para split de pagamentos');
            $table->text('asaas_account_data')->nullable()->comment('Dados completos da subconta Asaas (JSON)');
            $table->json('data')->nullable();
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

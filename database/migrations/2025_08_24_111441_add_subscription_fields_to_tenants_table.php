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
        Schema::table('tenants', function (Blueprint $table) {
            $table->timestamp('trial_started_at')->nullable()->after('status');
            $table->timestamp('trial_ends_at')->nullable()->after('trial_started_at');
            $table->enum('subscription_status', ['trial', 'active', 'expired', 'suspended'])->default('trial')->after('trial_ends_at');
            $table->string('current_plan')->nullable()->after('subscription_status');
            $table->timestamp('subscription_started_at')->nullable()->after('current_plan');
            $table->timestamp('subscription_ends_at')->nullable()->after('subscription_started_at');
            $table->boolean('is_blocked')->default(false)->after('subscription_ends_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'trial_started_at',
                'trial_ends_at', 
                'subscription_status',
                'current_plan',
                'subscription_started_at',
                'subscription_ends_at',
                'is_blocked'
            ]);
        });
    }
};

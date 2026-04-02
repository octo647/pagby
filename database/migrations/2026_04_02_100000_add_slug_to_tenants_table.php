<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Adiciona o campo slug se não existir
        if (!Schema::hasColumn('tenants', 'slug')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->string('slug')->unique()->nullable()->after('fantasy_name');
            });
            
            // Popula o slug para tenants existentes que não têm
            DB::table('tenants')->whereNull('slug')->orWhere('slug', '')->get()->each(function ($tenant) {
                $slug = Str::slug($tenant->fantasy_name ?: $tenant->name ?: $tenant->id);
                
                // Garante que o slug é único
                $originalSlug = $slug;
                $counter = 1;
                while (DB::table('tenants')->where('slug', $slug)->where('id', '!=', $tenant->id)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
                
                DB::table('tenants')->where('id', $tenant->id)->update(['slug' => $slug]);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('tenants', 'slug')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->dropColumn('slug');
            });
        }
    }
};

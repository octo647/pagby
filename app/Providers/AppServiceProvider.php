<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('Admin', function (User $user) {
            return $user->hasRole('Admin');
        });
        Gate::define('Proprietário', function (User $user) {
            return $user->hasRole('Proprietário');
        });
        Gate::define('Funcionário', function (User $user) {
            
            return $user->hasRole('Funcionário');
        });
        Gate::define('Cliente', function (User $user) {
            return $user->hasRole('Cliente');
        });
    }
}

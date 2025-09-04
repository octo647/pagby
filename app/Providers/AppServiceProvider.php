<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rules\Password;

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
        // Configuração de regras de senha mais amigáveis
        Password::defaults(function () {
            return Password::min(8)
                ->letters()
                ->numbers();
        });

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
         View::composer('layouts.navigation', function ($view) {
        $view->with('employees', User::whereHas('roles', function ($query) {
            $query->where('role', 'Funcionário');
        })->orderBy('name')->get());
    });
    }
}

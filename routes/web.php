<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TenantAdminController;
use App\Http\Controllers\PlanAdminController;
use App\Http\Controllers\TenantRegistrationController;

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
        Route::get('/', function () {
            return view('home');
        });
Route::get('/register-tenant', [TenantRegistrationController::class, 'showForm'])->name('register-tenant');
Route::post('/register-tenant', [TenantRegistrationController::class, 'register']);
Route::get('/registration-success', [TenantRegistrationController::class, 'registrationSuccess'])->name('registration-success'); 

Route::prefix('admin')->middleware(['auth', 'can:Admin'])->group(function () {
    Route::get('/tenants', [TenantAdminController::class, 'index'])->name('admin.tenants');
    Route::get('/tenants/{tenant}', [TenantAdminController::class, 'show'])->name('admin.tenants.show');
    Route::get('/planos', [PlanAdminController::class, 'index'])->name('admin.planos');
    
    
    // ... outras rotas administrativas
});
Route::get('/funcionalidades', function () {
            return view('funcionalidades');
        })->name('funcionalidades');
        

        Route::middleware('auth')->group(function () {
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
            Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
        });

        require __DIR__.'/auth.php';
    });
}

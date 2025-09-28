<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('universal', []);
        $middleware->alias([
            'checkTenantSubscription' => \App\Http\Middleware\CheckTenantSubscription::class,
            'scopeSessions' => \Stancl\Tenancy\Middleware\ScopeSessions::class,
            'handleSessionErrors' => \App\Http\Middleware\HandleSessionErrors::class,
        ]);
        
        // Adicionar o middleware de tratamento de erros de sessão globalmente
        $middleware->web([
            \App\Http\Middleware\HandleSessionErrors::class,
        ]);
    })
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        // Limpeza automática de sessões expiradas a cada hora
        $schedule->command('sessions:clean --force')->hourly();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

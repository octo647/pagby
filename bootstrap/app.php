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
        $middleware->validateCsrfTokens(except: [
            'stripe/*',
            'http://www.pagby.com.br/pagby-subscription/*',
            'tenant-assinatura/*',
            'tenant-assinatura/webhook',
            'pagby-subscription/*',
            'pagby-subscription/webhook',
            'http://www.pagby.com.br/tenant-assinatura/webhook',
            'http://www.pagby.com.br/pagby-subscription/webhook',
            'subscription/select',
            'api/subconta-webhook',
            
        ]);

        // Adicionar o middleware de tratamento de erros de sessão globalmente
        $middleware->web([
            \App\Http\Middleware\HandleSessionErrors::class,
        ]);
    })
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        // Limpeza automática de sessões expiradas a cada hora
        $schedule->command('sessions:clean --force')->hourly();
        
        // Verifica assinaturas expirando em 3 dias - executa todo dia às 09h
        $schedule->command('subscriptions:check-expiring --days=3')
                 ->dailyAt('09:00')
                 ->timezone('America/Sao_Paulo');
        
        // Verifica assinaturas expirando amanhã - executa todo dia às 10h
        $schedule->command('subscriptions:check-expiring --days=1')
                 ->dailyAt('10:00')
                 ->timezone('America/Sao_Paulo');
        
        // Lembretes de agendamento - 24h antes (às 18h)
        $schedule->command('appointments:send-reminders --hours=24')
                 ->dailyAt('18:00')
                 ->timezone('America/Sao_Paulo');
        
        // Lembretes de agendamento - 2h antes (a cada hora)
        $schedule->command('appointments:send-reminders --hours=2')
                 ->hourly()
                 ->between('8:00', '20:00')
                 ->timezone('America/Sao_Paulo');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

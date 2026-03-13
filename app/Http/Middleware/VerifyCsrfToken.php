<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'tenant-assinatura/webhook',
        'webhook/mercado-pago',
        'http://www.pagby.com.br/pagby-subscription/*',
        'http://www.pagby.com.br/tenant-assinatura/*',
        '/pagby-subscription/webhook',
        '/api/subconta-webhook',
    ];
}

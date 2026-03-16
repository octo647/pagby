#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Inicializar tenancy para o tenant especificado
$tenantId = $argv[1] ?? 'barbearia-teste12';

$tenant = \App\Models\Tenant::where('id', $tenantId)->first();

if (!$tenant) {
    echo "Tenant não encontrado: {$tenantId}\n";
    exit(1);
}

tenancy()->initialize($tenant);

// Buscar serviços
$services = \App\Models\Service::select('id', 'service', 'price', 'time', 'photo')
    ->take(10)
    ->get();

echo "Serviços do tenant {$tenantId}:\n";
echo "Total: " . $services->count() . "\n\n";

foreach ($services as $service) {
    echo "ID: {$service->id}\n";
    echo "Nome: {$service->service}\n";
    echo "Preço: R$ " . number_format($service->price, 2, ',', '.') . "\n";
    echo "Duração: {$service->time} min\n";
    echo "Foto: " . ($service->photo ?? 'N/A') . "\n";
    echo "---\n";
}

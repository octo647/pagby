#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tenantId = $argv[1] ?? 'ghedim';

$tenant = \App\Models\Tenant::where('id', $tenantId)->first();

if (!$tenant) {
    echo "Tenant não encontrado: {$tenantId}\n";
    exit(1);
}

echo "Tenant: {$tenant->id}\n";
echo "Nome: {$tenant->fantasy_name}\n";
echo "Logo DB: " . ($tenant->logo ?? 'NULL') . "\n";
echo "Logo existe fisicamente: ";

if ($tenant->logo) {
    // Tenta diferentes caminhos
    $paths = [
        public_path($tenant->logo),
        public_path(ltrim($tenant->logo, '/')),
    ];
    
    $found = false;
    foreach ($paths as $path) {
        if (file_exists($path)) {
            echo "SIM - $path\n";
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        echo "NÃO - tentou:\n";
        foreach ($paths as $path) {
            echo "  - $path\n";
        }
    }
} else {
    echo "N/A (sem logo no DB)\n";
}

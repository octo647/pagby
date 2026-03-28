<?php

/**
 * Script para resetar senha de usuário no tenant teste13
 * Executar no servidor: php reset-password-teste13.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== Reset de Senha - Tenant teste13 ===" . PHP_EOL . PHP_EOL;

$tenant = Tenant::where('id', 'teste13')->first();

if (!$tenant) {
    echo "❌ Tenant teste13 não encontrado" . PHP_EOL;
    exit(1);
}

echo "✓ Tenant encontrado: {$tenant->id}" . PHP_EOL;

tenancy()->initialize($tenant);

$user = User::where('email', 'teste15@algo.com')->first();

if (!$user) {
    echo "❌ Usuário teste15@algo.com não encontrado no tenant teste13" . PHP_EOL;
    
    echo PHP_EOL . "Usuários disponíveis:" . PHP_EOL;
    $users = User::all(['email', 'name']);
    foreach ($users as $u) {
        echo "  - {$u->email} ({$u->name})" . PHP_EOL;
    }
    
    tenancy()->end();
    exit(1);
}

echo "✓ Usuário encontrado: {$user->email}" . PHP_EOL;
echo "  Nome: {$user->name}" . PHP_EOL;
echo "  Status: {$user->status}" . PHP_EOL;

// Reseta a senha
$user->password = Hash::make('123456');
$user->save();

echo PHP_EOL . "✅ Senha resetada com sucesso para: 123456" . PHP_EOL;

tenancy()->end();

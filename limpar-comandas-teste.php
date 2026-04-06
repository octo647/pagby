<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Inicializa o tenant labelle
$tenant = \App\Models\Tenant::find('labelle');

if (!$tenant) {
    echo "Tenant labelle não encontrado!\n";
    exit(1);
}

echo "Inicializando tenant: {$tenant->id}\n";
tenancy()->initialize($tenant);

use App\Models\Comanda;
use Illuminate\Support\Facades\DB;

echo "\n=== Limpando comandas criadas pelo script anterior ===\n\n";

// Remove apenas comandas que começam com 'CMD-' (criadas pelo script)
$comandas = Comanda::where('numero_comanda', 'LIKE', 'CMD-%')->get();

echo "Encontradas " . $comandas->count() . " comandas para remover.\n";

foreach ($comandas as $comanda) {
    // Remove serviços e produtos associados (cascade deve fazer isso, mas vamos garantir)
    DB::table('comanda_servicos')->where('comanda_id', $comanda->id)->delete();
    DB::table('comanda_produtos')->where('comanda_id', $comanda->id)->delete();
    $comanda->delete();
}

echo "\n✅ Comandas limpas com sucesso!\n";
echo "Total de comandas restantes: " . Comanda::count() . "\n";

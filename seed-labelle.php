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

echo "Limpando banco de dados do tenant...\n";
// Desabilita foreign keys temporariamente
DB::statement('SET FOREIGN_KEY_CHECKS=0;');

// Lista todas as tabelas
$tables = DB::select('SHOW TABLES');
$dbName = DB::getDatabaseName();
$tableKey = "Tables_in_{$dbName}";

foreach ($tables as $table) {
    $tableName = $table->$tableKey;
    if ($tableName !== 'migrations') {
        echo "  Truncando tabela: {$tableName}\n";
        DB::table($tableName)->truncate();
    }
}

// Re-habilita foreign keys
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "\n=== Executando Seeders (sem dependências de pagamento) ===\n\n";

// 1. Cria os roles
echo "→ Criando roles...\n";
(new \Database\Seeders\RolesTableSeeder())->run();

// 2. Cria usuários
echo "→ Criando 100 usuários...\n";
\App\Models\User::factory(100)->create();

// 3. Cria filiais
echo "→ Criando 3 filiais...\n";
\App\Models\Branch::factory()->count(3)->create();

// 4. Cria serviços
echo "→ Criando serviços...\n";
(new \Database\Seeders\ServiceTableSeeder())->run();

// 5. Associa roles aos usuários (role_user)
echo "→ Associando roles aos usuários...\n";
(new \Database\Seeders\RoleUserTableSeeder())->run();

// 6. Associa serviços aos funcionários (service_user)
echo "→ Associando serviços aos funcionários...\n";
(new \Database\Seeders\ServiceUserTableSeeder())->run();

// 7. Cria horários dos funcionários
echo "→ Criando schedules...\n";
(new \Database\Seeders\SchedulesTableSeeder())->run();

// 8. Cria estoque
echo "→ Criando estoque...\n";
(new \Database\Seeders\EstoqueSeeder())->run();

// 9. Cria agendamentos
echo "→ Criando agendamentos...\n";
(new \Database\Seeders\AppointmentsTableSeeder())->run();

// 10. Cria comandas
echo "→ Criando comandas...\n";
(new \Database\Seeders\ComandasSeeder())->run();

// 11. Cria caixas
echo "→ Criando caixas...\n";
(new \Database\Seeders\CaixaSeeder())->run();

echo "\n✓ Seed concluído com sucesso!\n";
echo "\nResumo:\n";
echo "- Usuários: " . \App\Models\User::count() . "\n";
echo "- Filiais: " . \App\Models\Branch::count() . "\n";
echo "- Serviços: " . \App\Models\Service::count() . "\n";
echo "- Agendamentos: " . \App\Models\Appointment::count() . "\n";
echo "- Comandas: " . \App\Models\Comanda::count() . "\n";
echo "- Estoque: " . \App\Models\Estoque::count() . "\n";

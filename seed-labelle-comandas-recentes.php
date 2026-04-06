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

echo "\n=== Criando Comandas Recentes para o Tenant Labelle ===\n\n";

use App\Models\Branch;
use App\Models\User;
use App\Models\Service;
use App\Models\Estoque;
use App\Models\Comanda;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

$branches = Branch::all();
if ($branches->isEmpty()) {
    echo "❌ Nenhuma filial encontrada. Execute o seed completo primeiro.\n";
    exit(1);
}

// Pega funcionários com a role 'Funcionário'
$funcionarios = User::whereHas('roles', function($q) {
    $q->where('role', 'Funcionário');
})->get();

if ($funcionarios->isEmpty()) {
    echo "❌ Nenhum funcionário encontrado. Execute o seed completo primeiro.\n";
    exit(1);
}

// Pega clientes
$clientes = User::whereHas('roles', function($q) {
    $q->where('role', 'Cliente');
})->get();

if ($clientes->isEmpty()) {
    echo "❌ Nenhum cliente encontrado. Execute o seed completo primeiro.\n";
    exit(1);
}

$hoje = Carbon::today();

// Define os períodos usando a MESMA LÓGICA do componente ControlePagamento
$periodos = [
    'semanal' => [
        'inicio' => $hoje->copy()->startOfWeek(),
        'fim' => $hoje->copy()->endOfWeek(),
        'quantidade' => 20,
        'descricao' => 'Período semanal (semana atual)',
    ],
    'quinzenal' => [
        'inicio' => $hoje->copy()->subDays($hoje->day <= 15 ? $hoje->day - 1 : $hoje->day - 16),
        'fim' => $hoje->copy()->subDays($hoje->day <= 15 ? $hoje->day - 1 : $hoje->day - 16)->addDays(14),
        'quantidade' => 15,
        'descricao' => 'Período quinzenal (quinzena atual)',
    ],
    'mensal' => [
        'inicio' => $hoje->copy()->startOfMonth(),
        'fim' => $hoje->copy()->endOfMonth(),
        'quantidade' => 25,
        'descricao' => 'Período mensal (mês atual)',
    ],
];

$comandaCounter = Comanda::max('id') ?? 0;
$totalCriadas = 0;

foreach ($periodos as $nomePeriodo => $config) {
    echo "\n→ Criando comandas para {$config['descricao']}...\n";
    echo "  Período: {$config['inicio']->format('d/m/Y')} a {$config['fim']->format('d/m/Y')}\n";
    echo "  Quantidade: {$config['quantidade']} comandas\n";
    
    for ($i = 0; $i < $config['quantidade']; $i++) {
        $comandaCounter++;
        
        // Escolhe uma data aleatória dentro do período
        $diasDiferenca = $config['inicio']->diffInDays($config['fim']);
        $dataAbertura = $config['inicio']->copy()->addDays(rand(0, max(0, $diasDiferenca)));
        $dataFechamento = $dataAbertura->copy()->addHours(rand(1, 4));
        
        // Escolhe filial, funcionário e cliente aleatórios
        $branch = $branches->random();
        $funcionario = $funcionarios->random();
        $cliente = $clientes->random();
        
        // Pega serviços disponíveis (services não tem branch_id, são globais)
        $services = Service::inRandomOrder()->take(rand(1, 3))->get();
        
        if ($services->isEmpty()) {
            echo "  ⚠ Pulando comanda {$i} - sem serviços disponíveis\n";
            continue;
        }
        
        $subtotal_servicos = $services->sum('price');
        
        // Cria a comanda
        $comanda = Comanda::create([
            'appointment_id' => null,
            'branch_id' => $branch->id,
            'numero_comanda' => 'CMD-' . $dataAbertura->format('Ymd') . '-' . str_pad($comandaCounter, 4, '0', STR_PAD_LEFT),
            'cliente_nome' => $cliente->name,
            'cliente_telefone' => $cliente->phone ?? '(00) 00000-0000',
            'funcionario_id' => $funcionario->id,
            'data_abertura' => $dataAbertura,
            'status' => 'Finalizada',
            'data_fechamento' => $dataFechamento,
            'subtotal_servicos' => $subtotal_servicos,
            'subtotal_produtos' => 0,
            'desconto_servicos' => 0,
            'desconto_produtos' => 0,
            'total_geral' => $subtotal_servicos,
            'created_at' => $dataAbertura,
            'updated_at' => $dataFechamento,
        ]);
        
        // Adiciona os serviços à comanda
        foreach ($services as $service) {
            DB::table('comanda_servicos')->insert([
                'comanda_id' => $comanda->id,
                'service_id' => $service->id,
                'funcionario_id' => $funcionario->id,
                'quantidade' => 1,
                'preco_unitario' => $service->price,
                'subtotal' => $service->price,
                'status_servico' => 'Concluído',
                'created_at' => $dataAbertura,
                'updated_at' => $dataFechamento,
            ]);
        }
        
        // Adiciona produtos aleatórios (50% de chance)
        if (rand(0, 1) === 1) {
            $produtos = Estoque::where('branch_id', $branch->id)
                ->inRandomOrder()
                ->take(rand(1, 2))
                ->get();
            
            $total_produtos = 0;
            foreach ($produtos as $produto) {
                $quantidade = rand(1, 3);
                $subtotal = $produto->preco_unitario * $quantidade;
                
                DB::table('comanda_produtos')->insert([
                    'comanda_id' => $comanda->id,
                    'estoque_id' => $produto->id,
                    'funcionario_id' => $funcionario->id,
                    'quantidade' => $quantidade,
                    'preco_unitario' => $produto->preco_unitario,
                    'subtotal' => $subtotal,
                    'percentual_produtos' => $produto->percentual_produtos ?? 10,
                    'created_at' => $dataAbertura,
                    'updated_at' => $dataFechamento,
                ]);
                
                $total_produtos += $subtotal;
            }
            
            // Atualiza totais da comanda
            $comanda->subtotal_produtos = $total_produtos;
            $comanda->total_geral = $comanda->subtotal_servicos + $total_produtos;
            $comanda->save();
        }
        
        $totalCriadas++;
    }
    
    echo "  ✓ {$config['quantidade']} comandas criadas\n";
}

echo "\n✅ Seed de comandas recentes concluído com sucesso!\n";
echo "\nResumo:\n";
echo "- Total de comandas criadas: {$totalCriadas}\n";
echo "- Total de comandas na base: " . Comanda::count() . "\n";
echo "- Comandas finalizadas na última semana: " . Comanda::where('status', 'Finalizada')
    ->whereBetween('data_fechamento', [$hoje->copy()->subDays(6), $hoje])
    ->count() . "\n";
echo "- Comandas finalizadas nas últimas 2 semanas: " . Comanda::where('status', 'Finalizada')
    ->whereBetween('data_fechamento', [$hoje->copy()->subDays(14), $hoje])
    ->count() . "\n";
echo "- Comandas finalizadas no último mês: " . Comanda::where('status', 'Finalizada')
    ->whereBetween('data_fechamento', [$hoje->copy()->subDays(30), $hoje])
    ->count() . "\n";

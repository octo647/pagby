# Guia: Usando Tinker com Multi-Tenancy

## 🎯 O Problema

No Pagby, cada tenant (salão/barbearia) tem seu próprio banco de dados (`tenant{uuid}`). Quando você abre o Tinker, **por padrão** ele está conectado ao banco **central**, não ao banco de um tenant específico.

```
Banco Central: mysql (conexão padrão)
├── tenants
├── domains  
├── users (centrais)
└── pagby_payments

Banco Tenant: tenant12345-abcd-efgh...
├── users (do salão)
├── estoque
├── comandas
├── fidelidade_rewards
└── appointments
```

---

## ✅ Solução: Inicializar Tenant

### **Método 1: Inicialização Manual (Recomendado)**

```bash
php artisan tinker
```

```php
// 1. Listar tenants disponíveis
$tenants = \App\Models\Tenant::all(['id', 'name']);
foreach($tenants as $t) {
    echo "{$t->id} - {$t->name}\n";
}

// 2. Escolher um tenant (copie o ID da lista acima)
$tenant = \App\Models\Tenant::find('c7c720cc-8');
// OU pelo domínio:
$tenant = \App\Models\Tenant::whereHas('domains', function($q) {
    $q->where('domain', 'meusalao.pagby.com.br');
})->first();

// 3. Inicializar contexto do tenant
tenancy()->initialize($tenant);

// 4. PRONTO! Agora todos os comandos usam o banco do tenant
echo "✅ Conectado a: " . \DB::connection()->getDatabaseName();

// 5. Executar comandos normalmente
$produtos = \App\Models\Estoque::all();
$comandas = \App\Models\Comanda::all();
$recompensas = \App\Models\FidelidadeReward::all();
```

---

### **Método 2: Helper Rápido**

Crie uma função helper para facilitar:

```php
// Cole isso no Tinker uma vez
function t($tenantIdOrDomain = null) {
    if (!$tenantIdOrDomain) {
        $tenant = \App\Models\Tenant::first();
    } elseif (strpos($tenantIdOrDomain, '.') !== false) {
        // É um domínio
        $tenant = \App\Models\Tenant::whereHas('domains', function($q) use ($tenantIdOrDomain) {
            $q->where('domain', $tenantIdOrDomain);
        })->first();
    } else {
        // É um ID
        $tenant = \App\Models\Tenant::find($tenantIdOrDomain);
    }
    
    if (!$tenant) {
        throw new Exception("Tenant não encontrado!");
    }
    
    tenancy()->initialize($tenant);
    
    echo "✅ Tenant: {$tenant->name} ({$tenant->id})\n";
    echo "📊 DB: " . \DB::connection()->getDatabaseName() . "\n";
    
    return $tenant;
}

// Uso:
t(); // Conecta ao primeiro tenant
t('c7c720cc-8'); // Por ID
t('meusalao.pagby.com.br'); // Por domínio
```

---

### **Método 3: Script Automatizado**

Use o script de teste com tenant específico:

```bash
php artisan tinker
```

```php
// Edite $tenantId no arquivo test-fidelidade.php e execute:
include 'test-fidelidade.php';
```

---

## 🧪 Exemplos Práticos

### **Teste 1: Criar Produto e Vender**

```php
// 1. Inicializar tenant
$tenant = \App\Models\Tenant::first();
tenancy()->initialize($tenant);

// 2. Criar produto
$produto = \App\Models\Estoque::create([
    'branch_id' => \App\Models\Branch::first()->id,
    'produto_nome' => 'Pomada Modeladora Premium',
    'preco_unitario' => 50.00,
    'quantidade_atual' => 20,
    'quantidade_minima' => 5,
    'total_vendas' => 0,
]);

echo "✅ Produto criado: {$produto->produto_nome} (ID: {$produto->id})\n";

// 3. Criar comanda
$cliente = \App\Models\User::whereHas('roles', function($q) {
    $q->where('role', 'Cliente');
})->first();

$comanda = \App\Models\Comanda::create([
    'numero_comanda' => \App\Models\Comanda::gerarNumeroComanda($produto->branch_id),
    'branch_id' => $produto->branch_id,
    'client_id' => $cliente?->id,
    'cliente_nome' => $cliente?->name ?? 'Cliente Teste',
    'funcionario_id' => \App\Models\User::whereHas('roles', function($q) {
        $q->where('role', 'Funcionário');
    })->first()?->id ?? 1,
    'status' => 'Aberta',
    'data_abertura' => now(),
    'subtotal_produtos' => 0,
    'total_geral' => 0,
]);

echo "✅ Comanda criada: #{$comanda->numero_comanda}\n";

// 4. Vender produto (OBSERVER VAI CRIAR RECOMPENSA!)
$venda = \App\Models\ComandaProduto::create([
    'comanda_id' => $comanda->id,
    'estoque_id' => $produto->id,
    'quantidade' => 1,
    'preco_unitario' => $produto->preco_unitario,
    'subtotal' => $produto->preco_unitario,
]);

echo "✅ Venda registrada!\n";

// 5. Verificar recompensa criada automaticamente
$recompensa = \App\Models\FidelidadeReward::latest()->first();
if ($recompensa) {
    echo "🎁 Recompensa gerada automaticamente!\n";
    echo "   Cliente ID: {$recompensa->user_id}\n";
    echo "   Tipo: {$recompensa->tipo}\n";
    echo "   Valor: R$ {$recompensa->valor_credito}\n";
    echo "   Válida até: {$recompensa->validade->format('d/m/Y')}\n";
} else {
    echo "⚠️  Nenhuma recompensa criada (observer pode não estar ativo)\n";
}

// 6. Verificar total_vendas incrementado
$produto->refresh();
echo "📊 Total vendas do produto: {$produto->total_vendas}\n";
```

---

### **Teste 2: Vincular Produto a Serviço**

```php
// 1. Inicializar
$tenant = \App\Models\Tenant::first();
tenancy()->initialize($tenant);

// 2. Pegar serviço e produto
$service = \App\Models\Service::first();
$produto = \App\Models\Estoque::first();

if (!$service || !$produto) {
    echo "⚠️  Crie serviços e produtos primeiro\n";
    exit;
}

// 3. Vincular produto ao serviço
\DB::table('service_estoque')->insert([
    'service_id' => $service->id,
    'estoque_id' => $produto->id,
    'priority' => 1,
    'discount_percentage' => 10, // 10% de desconto
    'is_active' => true,
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "✅ Produto '{$produto->produto_nome}' vinculado ao serviço '{$service->service}'\n";

// 4. Testar sugestões hierárquicas
$branch = \App\Models\Branch::first();
$sugeridos = $service->getProdutosSugeridos($branch->id, 3);

echo "\n📦 Produtos sugeridos para '{$service->service}':\n";
foreach ($sugeridos as $p) {
    $desconto = $p->pivot->discount_percentage ?? 0;
    echo "   {$p->produto_nome} - R$ {$p->preco_unitario}";
    if ($desconto > 0) {
        echo " ({$desconto}% OFF)";
    }
    echo "\n";
}
```

---

### **Teste 3: Consultar Recompensas de Cliente**

```php
// 1. Inicializar
$tenant = \App\Models\Tenant::first();
tenancy()->initialize($tenant);

// 2. Buscar cliente
$cliente = \App\Models\User::whereHas('roles', function($q) {
    $q->where('name', 'Cliente');
})->first();

if (!$cliente) {
    echo "⚠️  Nenhum cliente encontrado\n";
    exit;
}

// 3. Ver recompensas ativas
$recompensas = \App\Models\FidelidadeReward::where('user_id', $cliente->id)
    ->ativos()
    ->get();

echo "🎁 Recompensas de {$cliente->name}:\n";
foreach ($recompensas as $r) {
    echo "   {$r->descricao_formatada} - Válido até {$r->validade->format('d/m/Y')}\n";
}

// 4. Saldo total em créditos
$saldo = \App\Models\FidelidadeReward::saldoCreditosCliente($cliente->id);
echo "\n💰 Saldo total: R$ " . number_format($saldo, 2, ',', '.') . "\n";
```

---

## 🔍 Verificações Úteis

### **Qual banco estou usando?**

```php
echo \DB::connection()->getDatabaseName();
// Central: mysql
// Tenant: tenant12345-abcd-...
```

### **Estou no contexto de um tenant?**

```php
$tenant = tenant();
if ($tenant) {
    echo "✅ Tenant ativo: {$tenant->name}\n";
} else {
    echo "❌ Contexto central (sem tenant)\n";
}
```

### **Ver todas as conexões disponíveis:**

```php
print_r(config('database.connections'));
```

### **Reiniciar contexto (voltar ao central):**

```php
tenancy()->end();
echo "Voltou ao banco central\n";
```

---

## ⚠️ Erros Comuns

### **Erro: Table 'mysql.estoque' doesn't exist**

**Causa:** Você está no banco central, não no tenant.

**Solução:**
```php
$tenant = \App\Models\Tenant::first();
tenancy()->initialize($tenant);
```

---

### **Erro: Class 'tenant' not found**

**Causa:** Helper `tenant()` não disponível no contexto.

**Solução:**
```php
$tenant = tenancy()->tenant; // Alternativa
```

---

### **Observer não está funcionando**

**Verificar:**
```php
// 1. Verificar se está registrado
app(\App\Providers\AppServiceProvider::class)->boot();

// 2. Limpar cache
Artisan::call('config:clear');
Artisan::call('cache:clear');

// 3. Testar manualmente
$venda = \App\Models\ComandaProduto::create([...]);
\App\Models\FidelidadeReward::latest()->first(); // Deve existir
```

---

## 📝 Comandos Úteis para Desenvolvimento

```bash
# Limpar tudo e recomeçar
php artisan config:clear
php artisan cache:clear  
php artisan route:clear
php artisan view:clear

# Ver rotas do tenant
php artisan route:list | grep tenant

# Executar migrations em todos os tenants
php artisan tenants:migrate

# Executar migration em tenant específico
php artisan tenants:migrate --tenants=c7c720cc-8
```

---

## 🎓 Resumo

**SEMPRE que usar Tinker para testar funcionalidades de tenant:**

```php
// 1️⃣ Inicializar
$tenant = \App\Models\Tenant::first();
tenancy()->initialize($tenant);

// 2️⃣ Confirmar
echo \DB::connection()->getDatabaseName(); // Deve ser tenant{uuid}

// 3️⃣ Trabalhar normalmente
$dados = \App\Models\Estoque::all();
```

Sem isso, você ficará no banco **central** e terá erros de tabelas não encontradas! 🚨

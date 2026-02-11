# Implementação do Percentual de Produtos para Pagamento de Funcionários

## Resumo
O campo `percentual_produtos` da tabela `estoque` agora é corretamente utilizado para registrar o percentual da venda de cada produto que deve ser destinado ao pagamento do funcionário que realizou a venda.

## Problema Identificado
Embora o campo `percentual_produtos` existisse na tabela `estoque`, ele não estava sendo armazenado no momento da venda. O sistema buscava o percentual diretamente do estoque durante o cálculo de pagamentos, o que causava dois problemas:

1. **Histórico incorreto**: Se o percentual fosse alterado no estoque após a venda, o cálculo usaria o valor atual, não o vigente no momento da venda
2. **Produtos deletados**: Se um produto fosse removido do estoque, não seria possível calcular o percentual devido

## Solução Implementada

### 1. Migration
**Arquivo**: `database/migrations/tenant/2026_02_10_000001_add_percentual_produtos_to_comanda_produtos.php`

Adiciona o campo `percentual_produtos` (decimal 5,2, padrão 0) na tabela `comanda_produtos`, logo após o campo `subtotal`.

```bash
php artisan tenants:migrate --path=database/migrations/tenant/2026_02_10_000001_add_percentual_produtos_to_comanda_produtos.php
```

### 2. Modelo ComandaProduto
**Arquivo**: `app/Models/ComandaProduto.php`

Adicionado `percentual_produtos` ao array `$fillable`, permitindo atribuição em massa.

### 3. Método adicionarProduto (Comanda)
**Arquivo**: `app/Models/Comanda.php`

Modificado para copiar o `percentual_produtos` do estoque para o registro de `comanda_produtos` no momento da venda:

```php
ComandaProduto::create([
    'comanda_id' => $this->id,
    'estoque_id' => $estoqueId,
    'quantidade' => $quantidade,
    'preco_unitario' => $preco,
    'subtotal' => $quantidade * $preco,
    'percentual_produtos' => $estoque->percentual_produtos ?? 0, // ← NOVO
    'observacoes' => $observacoes
]);
```

### 4. ControlePagamento
**Arquivo**: `app/Livewire/Proprietario/ControlePagamento.php`

Alterado para usar o percentual salvo em `comanda_produtos` ao invés de buscar do estoque:

**Antes:**
```php
foreach ($comanda->comandaProdutos as $cp) {
    $estoque = Estoque::find($cp->estoque_id);
    if ($estoque) {
        $produtos_vendidos[] = $estoque->percentual_produtos;
    }
}
```

**Depois:**
```php
foreach ($comanda->comandaProdutos as $cp) {
    // Usa o percentual salvo no momento da venda
    $produtos_vendidos[] = $cp->percentual_produtos ?? 0;
}
```

### 5. Factory
**Arquivo**: `database/factories/ComandaProdutoFactory.php`

Atualizado para incluir `percentual_produtos` ao gerar dados de teste, copiando do estoque quando disponível ou gerando valor aleatório entre 5 e 30.

### 6. Comando de População
**Arquivo**: `app/Console/Commands/PopularPercentualProdutos.php`

Criado comando para popular o campo em registros históricos existentes:

```bash
# Popula apenas registros com percentual 0 ou nulo
php artisan tenants:run comanda-produtos:popular-percentual

# Força atualização de todos os registros
php artisan tenants:run comanda-produtos:popular-percentual --force
```

O comando:
- Busca todos os `comanda_produtos` sem percentual definido
- Copia o percentual atual do estoque relacionado
- Exibe progresso com barra de carregamento
- Mostra relatório final com totais processados

## Funcionamento

### Fluxo de Venda
1. Cliente realiza compra de produto na comanda
2. Sistema chama `$comanda->adicionarProduto($estoqueId, $quantidade)`
3. Método busca o produto no estoque
4. **Copia o `percentual_produtos` do estoque para `comanda_produtos`**
5. Salva o registro com o percentual "congelado" daquele momento

### Cálculo de Pagamento
1. Proprietário acessa "Controle de Pagamento"
2. Sistema lista comandas finalizadas do período
3. Para cada produto vendido, **usa o percentual salvo em `comanda_produtos`**
4. Calcula média dos percentuais de todos os produtos vendidos pelo funcionário
5. Aplica percentual sobre total de produtos: `valor_produtos = total_produtos * (percentual_produtos / 100)`

### Consideração do Profissional
O profissional que realizou a venda é identificado como o funcionário associado à comanda (`funcionario_id` na tabela `comandas`). Este é normalmente o profissional que realizou o serviço principal da comanda.

## Dados Históricos
Registros antigos de `comanda_produtos` (criados antes desta implementação) terão `percentual_produtos = 0` até que:
- O comando `comanda-produtos:popular-percentual` seja executado, OU
- Sejam recalculados manualmente

**Nota**: O comando de população usa o percentual ATUAL do estoque, não o histórico (que não estava sendo salvo anteriormente).

## Benefícios
✅ **Histórico preciso**: Percentuais salvos no momento da venda  
✅ **Produtos deletados**: Sistema continua funcionando mesmo se produto for removido do estoque  
✅ **Alterações futuras**: Mudanças de percentual não afetam vendas anteriores  
✅ **Rastreabilidade**: Cada venda mantém seu percentual original  

## Arquivos Modificados
- `database/migrations/tenant/2026_02_10_000001_add_percentual_produtos_to_comanda_produtos.php` (novo)
- `app/Models/ComandaProduto.php`
- `app/Models/Comanda.php`
- `app/Livewire/Proprietario/ControlePagamento.php`
- `database/factories/ComandaProdutoFactory.php`
- `app/Console/Commands/PopularPercentualProdutos.php` (novo)

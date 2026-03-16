# Sistema de Criação de Tenants com Template Padrão

## Visão Geral

Este documento descreve o sistema de criação de tenants utilizando um template padrão neutro e o serviço TenantCreationService. A criação de tenants continua sendo feita manualmente pelo administrador através do painel Admin, mas agora com um processo mais simplificado e com templates neutros que podem ser customizados posteriormente pelos proprietários.

## Fluxo de Criação

### Fluxo Atual
1. Proprietário preenche formulário de contato
2. Admin acessa painel de Salões
3. Admin seleciona contato do proprietário
4. Admin escolhe logo (template "Padrao" é usado automaticamente)
5. Admin clica em "Criar" para gerar o tenant
6. Tenant é criado com template neutro e profissional
7. Proprietário recebe acesso ao sistema
8. Proprietário pode customizar sua home posteriormente

**Melhorias Implementadas**:
- ✅ Template neutro e profissional como padrão
- ✅ TenantCreationService simplifica e padroniza criação
- ✅ Código mais limpo e manutenível
- ✅ Base preparada para customização futura pelos proprietários
- ✅ Admin mantém controle do processo de criação

## Componentes Implementados

### 1. Template Padrão "Padrao"

**Localização**: `resources/Templates/{Categoria}/Padrao/home.blade.php`

**Categorias Disponíveis**:
- Barbearias
- Salões
- Spas
- Esteticistas
- PetShops
- Veterinárias

**Características do Template**:
- Design neutro e profissional
- Paleta de cores suave (azul, cinza, branco)
- Totalmente responsivo
- Seções modulares preparadas para customização:
  - **Hero**: Área de destaque inicial
  - **Serviços**: Grid de serviços (editável)
  - **Sobre**: Informações do estabelecimento
  - **Galeria**: Fotos dos trabalhos (editável)
  - **Ambiente**: Fotos do espaço físico (editável)
  - **Equipe**: Fotos e informações dos profissionais (editável)
  - **Contato**: Formulário e informações de contato

### 2. TenantCreationService

**Localização**: `app/Services/TenantCreationService.php`

**Responsabilidades**:
- Validar dados do tenant
- Criar registro no banco central
- Criar domínio (slug.pagby.com.br)
- Criar estrutura de diretórios
- **Criar link/cópia do template**: Padrao = cópia editável, outros = symlink
- Executar seeders (roles)
- Criar conta do proprietário
- Fazer upload da logo (se fornecida)
- Atualizar dados do Contact

**Métodos Principais**:

```php
// Criar tenant com dados customizados
createTenant(
    array $tenantData,
    ?UploadedFile $logoFile = null,
    ?string $templateType = 'Barbearias',
    ?string $template = 'Padrao'
): Tenant

// Criar tenant a partir de um Contact
createTenantFromContact(
    Contact $contact,
    ?UploadedFile $logoFile = null
): Tenant
```

### 3. Webhook do Asaas Modificado

**Localização**: `app/Http/Controllers/AsaasSubscriptionController.php`

**Métodos Modificados**:

#### `hPainel Admin Refatorado

**Localização**: `app/Livewire/Admin/Saloes.php`

**Método Principal**: `saveNewSalon()`

**Processo**:
1. Valida dados do formulário
2. Usa `TenantCreationService::createTenant()` para criar o tenant
3. Admin pode escolher qualquer template (Padrao, Clean, Moderna, etc.)
4. **Template Padrao = cópia editável, outros = symlink compartilhado**
5. Todos os detalhes de criação são gerenciados pelo serviço
6. Código mais limpo e fácil de manter
    participant F as Formulário
    participant A as Asaas
    participant W as Webhook
    participant T as TenantCreationService
    participant DB as Banco de Dados

    P->>F: Preenche dados do sManual

```mermaid
sequenceDiagram
    participant P as Proprietário
    participant F as Formulário
    participant Admin as Admin
    participant Panel as Painel Admin
    participant T as TenantCreationService
    participant DB as Banco de Dados

    P->>F: Preenche dados do salão
    F->>DB: Cria Contact
    Admin->>Panel: Acessa painel de Salões
    Panel->>DB: Lista Contacts disponíveis
    Admin->>Panel: Seleciona Contact
    Panel->>Panel: Preenche dados automaticamente
    Admin->>Panel: Escolhe logo (opcional)
    Admin->>Panel: Clica em "Criar"
    Panel->>T: createTenant(data, logo, templateType, 'Padrao')
    T->>DB: Cria Tenant registro
    T->>DB: Cria Domain
    T-->>T: Cria estrutura de diretórios
    T-->>T: Copia template Padrao (editável)
    T->>DB: Executa seeders (roles)
    T->>DB: Cria User proprietário
    T->>DB: Upload logo (se fornecida)
    T->>DB: Atualiza Contact data
    T->>Panel: Retorna Tenant criado
    Panel->>Admin: Exibe mensagem de sucesso
    Admin->>P: Notifica proprietário (email/whatsapp)o proprietário
- phone
- tenant_name        // Nome do salão
- employee_count
- address, city, state, etc.
- tipo               // Tipo: Barbearia, Salão de Beleza, etc.
- subscription_plan  // Plano escolhido: Básico, Premium
- asaas_customer_id
```

### PagByPayment (Banco Central)
```php
- id
- tenant_id          // temp_{contact_id} → depois {tenant_slug}
- asaas_payment_id
- amount
- status             // pending → received
- asaas_data         // JSON com dados do webhook
```

### Tenant (Banco Central)
```php
- id                 // Slug do salão
- slug               // Slug do salão
- fantasy_name       // Nome fantasia
- email, phone, address, etc.
- type               // Tipo do estabelecimento
- template           // Template usado (default: 'Padrao')
- employee_count
- trial_started_at
- trial_ends_at
- subscription_ends_at
- is_blocked
```

### Subscription (Banco do Tenant)
```php
- id
- user_id            // ID do proprietário
- plan_id            // ID do plano
- start_date
- end_date
- status             // Ativo, Cancelado, etc.
```

### SubscriptionPayment (Banco do Tenant)
```php
- id
- subscription_id
- asaas_payment_id
- amount
- billing_type
- due_date
- status             // received, pending, overdue
- payment_date
- received_at
- confirmed_at
- asaas_data         // JSON com dados do webhook
```

## Testes e Validação

### Como Testar

1. **Criar um Contact de teste**:
```bash
php artisan tinker
```
```php
$contact = App\Models\Contact::create([
    'owner_name' => 'João Silva' (ou usar formulário web):
```bash
php artisan tinker
```
```php
$contact = App\Models\Contact::create([
    'owner_name' => 'João Silva',
    'cpf' => '123.456.789-00',
    'email' => 'joao@teste.com',
    'phone' => '11999999999',
    'tenant_name' => 'Barbearia do João',
    'employee_count' => 2,
    'address' => 'Rua Exemplo, 123',
    'city' => 'São Paulo',
    'state' => 'SP',
    'tipo' => 'Barbearia',
    'subscription_plan' => 'Básico',
]);
```

2. **Acessar painel Admin**:
   - Login no domínio central
   - Navegar para "Salões"
   - Clicar em "Novo Salão"

3. **Criar tenant**:
   - Selecionar Contact da lista
   - Fazer upload da logo (opcional)
   - Confirmar dados preenchidos automaticamente
   - Clicar em "Criar"

4. **Verificar criação**:
```bash
# Verificar se tenant foi criado
php artisan tinker
App\Models\Tenant::where('email', 'joao@teste.com')->first();

# Verificar estrutura de diretórios
ls -la resources/views/tenants/
ls -la public/tenants/
ls -la storage/tenant*/

# Acessar home do tenant
curl https://barbearia-do-joao.pagby.com.brvice funciona corretamente
- [ ] Webhook cria tenant automaticamente
- [ ] Estrutura de diretórios está correta
- [ ] Symlink do template funciona
- [ ] Roles criadas no tenant
- [ ] Usuário proprietário criado
- [ ] Assinatura ativada no tenant
- [ ] Pagamento registrado no tenant
- [ ] PagByPayment atualizado com tenant_id real
- [ ] Tenant é acessível via subdomínio

## Próximos Passos: Customização da Home

### Fase 2: Interface de Customização (A IMPLEMENTAR)

**Objetivo**: Permitir que proprietários customizem sua home sem tocar em código

**Funcionalidades Planejadas**:

1. **Seção Serviços**
   - Listar serviços cadastrados no sistema
   - Exibir nome, descrição, preço e foto
   - Arrastar e reordenar serviços
   - Ativar/desativar serviços na home

2. **Seção Galeria**
   - Upload de fotos dos trabalhos
   - Arrastar e reordenar fotos
   - Excluir fotos
   - Limite: 12 fotos

3. **Seção Ambiente**
   - Upload de fotos do espaço físico
   - Adicionar título a cada foto (ex: "Recepção", "Área VIP")
   - Limite: 6 fotos

4. **Seção Equipe**
   - Listar funcionários cadastrados
   - Exibir foto, nome e função
   - Selecionar quais funcionários aparecem na home
   - Reordenar funcionários

5. **Personalização de Cores**
   - Escolher cor primária
   - Escolher cor secundária
   - Pré-visualizar mudanças em tempo real

### Implementação Sugerida

**Criar componente Livewire**:
`app/Livewire/Proprietario/CustomizarHome.php`

**Criar view**:
`resources/views/livewire/proprietario/customizar-home.blade.php`

**Adicionar rota no tenant**:
```php
Route::middleware(['auth', 'role:Proprietário'])->group(function () {
    Route::get('/configuracoes/home', CustomizarHome::class)
        ->name('configuracoes.home');
});
```

**Estrutura de Dados para Customização**:
Adicionar campo `home_customization` (JSON) na tabela `tenants`:
```php
{
  "colors": {
    "primary": "#2c3e50",
    "secondary": "#3498db"
  },
  "sections": {
    "servicos": {
      "enabled": true,
      "order": [1, 5, 3, 2],
      "title": "Nossos Serviços"
    },
    "galeria": {
      "enabled": true,
      "images": [
        "/storage/tenants/{id}/gallery/img1.jpg",
        "/storage/tenants/{id}/gallery/img2.jpg"
      ]
    },
    "ambiente": {
      "enabled": true,
      "images": [
        {
          "path": "/storage/tenants/{id}/ambiente/img1.jpg",
          "title": "Recepção"
        }
      ]
    },
    "equipe": {
      "enabled": true,
      "employees": [2, 5, 1],
      "title": "Nossa Equipe"
    }
  }
}
```

**Modificar template para ler customização**:
```blade
@php
$customization = json_decode(tenant()->home_customization, true) ?? [];
$colors = $customization['colors'] ?? [
    'primary' => '#2c3e50',
    'secondary' => '#3498db'
];
@endphp

<style>
:root {
    --cor-primaria: {{ $colors['primary'] }};
    --cor-secundaria: {{ $colors['secondary'] }};
}
</style>

<!-- Seção Serviços -->
@if($customization['sections']['servicos']['enabled'] ?? true)
<section class="servicos">
    <h2>{{ $customization['sections']['servicos']['title'] ?? 'Nossos Serviços' }}</h2>
    @foreach(getOrderedServices($customization['sections']['servicos']['order'] ?? []) as $service)
        <!-- Card de serviço -->
    @endforeach
</section>
@endif
```

## Manutenção e Troubleshooting

### Logs Importantes

**Criação de Tenant**:
```bash
grep "Iniciando criação de tenant" storage/logs/laravel.log
grep "Tenant criado com sucesso" storage/logs/laravel.log
```

**Webhook**:
```bash
grep "Webhook Asaas Recebido" storage/logs/laravel.log
grep "Tenant criado automaticamente via webhook" storage/logs/laravel.log
```

**Erros**:
```bash
grep "ERROR" storage/logs/laravel.log | grep -i tenant
```

### Problemas Comuns
Erro ao criar tenant no painel Admin**
- Verificar se Contact existe e tem dados válidos
- Verificar permissões de escrita nos diretórios
- Verificar logs: `storage/logs/laravel.log`
- Confirmar que template "Padrao" existe para a categoria
- Confirmar que PagByPayment tem tenant_id no formato `temp_{contact_id}`

**2. Template não aparece ou erro 404**
- Verificar se arquivo existe: `ls -la resources/views/tenants/{tenant_id}/home.blade.php`
- Se for symlink, verificar se destino existe: `ls -la $(readlink resources/views/tenants/{tenant_id}/home.blade.php)`
- Verificar se template existe em `resources/Templates/{Tipo}/{Template}/home.blade.php`
- Executar `php artisan view:clear`

**3. Edições na home não aparecem (template Padrao)**
- Verificar se é realmente uma cópia: `file resources/views/tenants/{tenant_id}/home.blade.php`
- Se for symlink, edições afetarão todos os tenants (não é o esperado para Padrao)
- Verificar se está editando o arquivo correto: `resources/views/tenants/{tenant_id}/home.blade.php`
- Executar `php artisan view:clear`
- Verificar cache do navegador (Ctrl+Shift+R)

**3b. Tentando editar template não-Padrao (symlink)**
- Templates específicos (Clean, Moderna, etc.) não devem ser editados individualmente
- Use template Padrao se precisa customização
- Ou edite o template original (afetará todos os tenants que o usam)

**4. Proprietário não consegue fazer login**
- Verificar se usuário foi criado: `SELECT * FROM users WHERE email='...'` (no banco do tenant)
- Verificar se role "Proprietário" foi associada
- Senha padrão é sempre `123456`

**4. Estrutura de diretórios incompleta**
- Verificar permissões de escrita
- Verificar logs de criação
- Recriar manualmente se necessário

## Arquivos Modificados

### Novos Arquivos
- `app/Services/TenantCreationService.php`
- `resources/Templates/Barbearias/Padrao/home.blade.php`
- `resources/Templates/Salões/Padrao/home.blade.php`
- `resources/Templates/Spas/Padrao/home.blade.php`
- `resources/Templates/Esteticistas/Padrao/home.blade.php`
- `resources/Templates/PetShops/Padrao/home.blade.php`
- `resources/Templates/Veterinárias/Padrao/home.blade.php`
- `FLUXO_CRIACAO_AUTOMATICA_TENANTS.md` (este arquivo)

### Arquivos Modificados

## Conclusão

O sistema de criação de tenants com template padrão está totalmente implementado. O processo continua sendo gerenciado pelo administrador através do painel Admin, mas agora é mais simples, padronizado e preparado para futuras customizações pelos proprietários.

**Status Atual**: ✅ Implementado e pronto para uso
**Próxima Fase**: Interface de customização da home pelos proprietários (a ser implementada)

## Nota Importante sobre Templates: Symlink vs Cópia

### Estratégia Híbrida

**Decisão Técnica**: O sistema usa **estratégia híbrida** baseada no template escolhido:

#### Template "Padrao"
- ✅ **CÓPIA** do arquivo para o diretório do tenant
- **Editável** individualmente por cada proprietário
- Mudanças não afetam outros tenants
- Permite customização completa via interface

#### Outros Templates (Clean, Moderna, etc.)
- 🔗 **SYMLINK** para o arquivo original
- **Não editável** (compartilhado entre tenants)
- Mudanças no template original afetam todos os tenants que o usam
- Design consistente e centralizado

**Razão desta Abordagem**:
- Templates específicos têm design elaborado que deve ser preservado
- Template Padrao é neutro e feito para ser customizado
- Melhor dos dois mundos: design profissional + flexibilidade de customização

**Como Funciona**:
```php
if ($template === 'Padrao') {
    copy($templateHome, $tenantHome);  // Cópia editável
} else {
    symlink($templateHome, $tenantHome);  // Symlink não editável
}
```

**Localização dos Arquivos**:
- Template Padrao: `resources/views/tenants/{tenant_id}/home.blade.php` (arquivo real)
- Outros templates: `resources/views/tenants/{tenant_id}/home.blade.php` (symlink)

## Nota Importante

A criação de tenants é **manual** e controlada pelo administrador. Não há criação automática via webhook de pagamento. Isso permite:
- Maior controle sobre o processo
- Verificação dos dados antes da criação
- Flexibilidade para ajustes caso necessário
- Monitoramento direto de cada novo tenant
**Próxima Fase**: Interface de customização da home (a ser implementada)

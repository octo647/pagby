# Sistema de Onboarding para Tenants

## Visão Geral

Sistema de onboarding interativo para guiar proprietários de salões/barbearias na configuração inicial da plataforma Pagby.

## Componentes Criados

### 1. Migration
**Arquivo**: `database/migrations/2026_04_02_082343_add_onboarding_to_tenants_table.php`

Adiciona campos à tabela `tenants`:
- `onboarding_completed` (boolean): Indica se o onboarding foi concluído
- `onboarding_progress` (json): Armazena o progresso de cada passo

### 2. Model Tenant Atualizado
**Arquivo**: `app/Models/Tenant.php`

Campos adicionados aos arrays:
- `getCustomColumns()`
- `$fillable`
- `$casts`

### 3. Componente Livewire
**Arquivo**: `app/Livewire/Proprietario/Onboarding.php`

Funcionalidades:
- Verifica automaticamente o progresso de cada passo
- Atualiza status no banco de dados
- Redirecionamento para áreas específicas de configuração
- Marcação de onboarding completo

### 4. View do Onboarding
**Arquivo**: `resources/views/livewire/proprietario/onboarding.blade.php`

Interface com:
- Cards visuais para cada passo
- Barra de progresso
- Indicadores visuais (ícones, cores)
- Botão de atualização de progresso
- Mensagem de conclusão

### 5. Layout
**Arquivo**: `resources/views/layouts/onboarding.blade.php`

Layout simples incluindo:
- Navegação
- Componente Livewire
- Estilos Tailwind

### 6. Rota
**Arquivo**: `routes/tenant.php`

Rota adicionada:
```php
Route::get('/onboarding', function () {
    return view('layouts.onboarding');
})->middleware(['auth', 'verified'])->name('onboarding');
```

### 7. Link no Menu
**Arquivo**: `resources/views/layouts/navigation.blade.php`

Link destacado no menu lateral:
- Aparece apenas para proprietários
- Visível apenas se `onboarding_completed = false`
- Design chamativo (gradiente indigo/purple)

## Passos do Onboarding

### Passo 1: Criar Filial 🏢
**Verificação**: `Branch::count() > 0`
**Rota**: Dashboard → Cadastros → Filiais

### Passo 2: Cadastrar Funcionários 👥
**Verificação**: Existe usuário com role "Funcionário"
**Rota**: Dashboard → Cadastros → Usuários

### Passo 3: Atribuir Filiais aos Funcionários 🔗
**Verificação**: `DB::table('branch_user')->count() > 0`
**Rota**: Dashboard → Cadastros → Funcionários

### Passo 4: Criar Serviços ✂️
**Verificação**: `Service::count() > 0`
**Rota**: Dashboard → Cadastros → Serviços

### Passo 5: Atribuir Serviços aos Funcionários 🎯
**Verificação**: `DB::table('service_user')->count() > 0`
**Rota**: Dashboard → Cadastros → Funcionários x Serviços

### Passo 6: Definir Horários de Trabalho ⏰
**Verificação**: `Schedule::count() > 0`
**Rota**: Dashboard → Cadastros → Horários

### Passo 7: Customizar a Home 🎨
**Verificação**: `$tenant->data['home_customized']` existe
**Rota**: Dashboard → Customizar Home

## Uso

### Acessar Onboarding
- URL: `/onboarding` (dentro do domínio do tenant)
- Ou clicar no card destacado no menu lateral

### Fluxo de Uso
1. Proprietário faz login no tenant
2. Visualiza o card de onboarding no menu lateral (se não concluído)
3. Acessa a página de onboarding
4. Visualiza os 7 passos e o progresso atual
5. Clica em "Configurar Agora" em cada passo pendente
6. É redirecionado para a página específica de configuração
7. Completa a configuração
8. Retorna ao onboarding e clica em "Atualizar Progresso"
9. Repete até completar todos os passos
10. Clica em "Ir para o Dashboard" ao finalizar

### Atualização Automática
O sistema verifica automaticamente:
- Existência de filiais
- Usuários com role funcionário
- Relacionamentos branch_user
- Serviços cadastrados
- Relacionamentos service_user
- Horários cadastrados
- Flag de customização da home

## Design

### Cores e Visual
- **Gradientes**: Blue/Indigo/Purple para destaque
- **Indicadores**: 
  - Verde: Passo concluído
  - Cinza: Passo pendente
- **Ícones**: Emojis temáticos para cada passo
- **Barra de Progresso**: Animada com gradiente

### Responsividade
- Layout adaptativo (grid 1 ou 2 colunas)
- Mobile-friendly
- Cards com hover effects

## Melhorias Futuras (Opcionais)

1. **Tooltips**: Dicas adicionais em cada passo
2. **Tutorial Interativo**: Guias passo a passo in-line
3. **Notificações**: Lembrete para completar onboarding
4. **Gamificação**: Badges ou recompensas ao completar
5. **Analytics**: Tracking de conclusão e tempo médio
6. **Resumo em Vídeo**: Link para tutorial em vídeo
7. **Chat de Suporte**: Botão de ajuda contextual

## Manutenção

### Adicionar Novo Passo
1. Editar `app/Livewire/Proprietario/Onboarding.php`
2. Adicionar novo item ao array `$steps` no método `checkProgress()`
3. Definir lógica de verificação (`'completed' => ...`)
4. Atualizar `$totalSteps` se necessário

### Personalizar Verificações
Editar as condições de `'completed'` no método `checkProgress()` do componente Livewire.

### Alterar Visual
Editar `resources/views/livewire/proprietario/onboarding.blade.php` com classes Tailwind.

## Testes Recomendados

1. Criar tenant novo sem dados
2. Verificar que todos os passos aparecem como pendentes
3. Completar cada passo e atualizar progresso
4. Verificar mudança de status visual
5. Completar todos e testar redirecionamento final
6. Verificar que card do menu desaparece após conclusão

## Arquivos Modificados

- `app/Models/Tenant.php`
- `routes/tenant.php`
- `resources/views/layouts/navigation.blade.php`

## Arquivos Criados

- `database/migrations/2026_04_02_082343_add_onboarding_to_tenants_table.php`
- `app/Livewire/Proprietario/Onboarding.php`
- `resources/views/livewire/proprietario/onboarding.blade.php`
- `resources/views/layouts/onboarding.blade.php`
- `ONBOARDING_SYSTEM.md` (este arquivo)

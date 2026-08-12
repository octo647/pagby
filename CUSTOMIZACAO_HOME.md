# Sistema de Customização da Home

## Visão Geral

Sistema que permite aos proprietários customizarem a página inicial do seu estabelecimento, **mas apenas se estiverem usando o Template Padrao**.

## Estratégia de Edição

### Template Padrao (Editável)
- ✅ **Arquivo real** copiado para o diretório do tenant
- **Pode ser editado** via interface de customização
- Mudanças não afetam outros tenants
- Localizado em: `resources/views/tenants/{tenant_id}/home.blade.php`

### Outros Templates (Não Editáveis)
- 🔗 **Symlink** para o arquivo original
- **Não pode ser editado** (design compartilhado)
- Mantém consistência profissional
- Mudanças no original afetam todos os tenants que o usam

## Como Funciona

### 1. Verificação Automática

O componente `CustomizarHome` verifica automaticamente:

```php
$homeFile = resource_path("views/tenants/$tenantId/home.blade.php");
$isSymlink = is_link($homeFile);

if ($isSymlink) {
    // Template específico → NÃO editável
    $this->canEdit = false;
} else {
    // Template Padrao → EDITÁVEL
    $this->canEdit = true;
}
```

### 2. Interface Baseada no Tipo

**Se não pode editar:**
- Exibe aviso explicativo
- Informa qual template está sendo usado
- Explica por que não pode editar
- Sugere contatar suporte para migrar para Template Padrao

**Se pode editar:**
- Interface completa de customização
- Abas para diferentes aspectos da home
- Preview em tempo real
- Botão para salvar alterações

## Funcionalidades de Customização

### 🎨 Cores
Personaliza a paleta de cores do site:
- **Cor Primária**: Usada em títulos e menus
- **Cor Secundária**: Usada em botões e links
- **Cor de Destaque**: Usada para elementos de ênfase

Usa variáveis CSS no `:root`:
```css
:root {
    --cor-primaria: #2c3e50;
    --cor-secundaria: #3498db;
    --cor-destaque: #e74c3c;
}
```

### 🚀 Seção Hero
Edita o conteúdo da seção principal (topo):
- **Título Principal**: Título grande que aparece primeiro
- **Subtítulo/Descrição**: Texto descritivo abaixo do título

### 👁️ Preview
- Visualização em tempo real das alterações
- Mostra como ficará o gradient de fundo
- Botão de exemplo com cor de destaque
- Link para abrir página real em nova aba

## Arquivos do Sistema

### Componente Livewire
**Arquivo**: `app/Livewire/Proprietario/CustomizarHome.php`

**Responsabilidades**:
- Verificar se o tenant pode editar (`verificarEditabilidade()`)
- Carregar conteúdo atual do arquivo home.blade.php
- Extrair variáveis CSS e textos
- Salvar customizações no arquivo
- Limpar cache de views após salvar

**Métodos Principais**:
```php
mount()                      // Inicializa e verifica editabilidade
verificarEditabilidade()     // Checa se é symlink ou arquivo
carregarConteudo()          // Lê arquivo home.blade.php
extrairVariaveis()          // Extrai cores e textos via regex
salvarCustomizacoes()       // Salva alterações no arquivo
```

### View
**Arquivo**: `resources/views/livewire/proprietario/customizar-home.blade.php`

**Seções**:
- Cabeçalho com título e descrição
- Aviso de template não editável (quando aplicável)
- Informação do template em uso
- Abas de navegação (Cores, Hero, Preview)
- Formulários de edição
- Preview em tempo real
- Botões de ação (Cancelar, Salvar)

### Integração

**Dashboard**: `resources/views/dashboard.blade.php`
```php
@elseif($tabelaAtiva === 'customizar-home')
    @livewire('proprietario.customizar-home')
```

**Menu**: `resources/views/layouts/navigation-back.blade.php`
```php
['tabelaAtiva' => 'customizar-home', 'label' => 'Customizar Home', 'icon' => '...']
```

## Fluxo de Uso

### 1. Acesso pelo Menu
Proprietário clica em **"Customizar Home"** no menu lateral

### 2. Verificação Automática
Sistema verifica se o template é editável

### 3a. Template NÃO Editável
- Mostra aviso explicativo
- Informa tipo de template
- Sugere contato com suporte

### 3b. Template Editável
- Carrega dados atuais
- Exibe interface de edição
- Permite customizar cores e textos

### 4. Edição
Proprietário edita:
- Escolhe cores com color picker
- Altera textos do Hero
- Visualiza preview

### 5. Salvamento
- Sistema atualiza arquivo home.blade.php
- Usa regex para substituir valores
- Limpa cache de views (`php artisan view:clear`)
- Exibe mensagem de sucesso

### 6. Visualização
- Mudanças aparecem imediatamente na home
- Cores aplicadas via variáveis CSS
- Textos substituídos no HTML

## Segurança

### Validações
- ✅ Verifica se arquivo existe antes de editar
- ✅ Valida se não é symlink (proteção contra edição de templates compartilhados)
- ✅ Usa tenant() helper para garantir contexto correto
- ✅ Log de todas as operações

### Logs
```php
Log::info('Verificação de editabilidade', [
    'tenant_id' => $tenantId,
    'can_edit' => $this->canEdit,
    'is_symlink' => $this->isSymlink,
    'template_type' => $this->templateType,
]);

Log::info('Home customizada salva', [
    'tenant_id' => $tenantId,
    'cores' => [...],
]);
```

## Limitações Atuais

### O que já funciona:
- ✅ Verificação automática de editabilidade
- ✅ Edição de cores CSS
- ✅ Edição de textos do Hero
- ✅ Preview das alterações
- ✅ Salvamento no arquivo
- ✅ Limpeza de cache

### Para implementação futura:
- 📋 Edição da seção Serviços
- 📋 Edição da seção Galeria
- 📋 Edição da seção Ambiente
- 📋 Edição da seção Equipe
- 📋 Upload de imagens via interface
- 📋 Editor visual WYSIWYG
- 📋 Histórico de versões
- 📋 Botão de reset para padrão

## Próximos Passos

### 1. Expandir Customizações
Adicionar edição de mais seções:
- Upload de imagens para galeria
- Gerenciamento de cards de serviços
- Edição de informações da equipe

### 2. Editor Visual
Implementar editor WYSIWYG para:
- Arrastar e soltar seções
- Edição inline de textos
- Preview em tempo real lado a lado

### 3. Templates Adicionais
Permitir migração entre templates:
- Interface para trocar de template
- Backup automático antes da migração
- Conversão de customizações existentes

### 4. Versionamento
Sistema de histórico:
- Salvar versões anteriores
- Botão de desfazer/refazer
- Restaurar versão anterior

## Testes

### Testar Funcionamento

1. **Criar tenant com Template Padrao**:
```bash
# Via admin panel
# Selecionar Template: Padrao
# Criar tenant normalmente
```

2. **Verificar arquivo**:
```bash
# Deve ser arquivo real, não symlink
file resources/views/tenants/{tenant_id}/home.blade.php
# Saída esperada: "ASCII text" (não "symbolic link")
```

3. **Acessar customização**:
- Login como proprietário
- Menu → "Customizar Home"
- Deve aparecer interface de edição completa

4. **Testar com Template Específico**:
- Criar tenant com template "Clean", "Moderna", etc.
- Login como proprietário
- Menu → "Customizar Home"
- Deve aparecer apenas aviso de não editável

### Validar Salvamento

1. Mudar cores
2. Alterar textos
3. Salvar
4. Abrir home em nova aba
5. Verificar se mudanças apareceram

## Troubleshooting

### Home não atualiza após salvar
```bash
php artisan view:clear
php artisan cache:clear
# Limpar cache do navegador (Ctrl+Shift+R)
```

### Erro "não pode editar"
```bash
# Verificar se é symlink
ls -la resources/views/tenants/{tenant_id}/home.blade.php
# Se for symlink (l---------), precisa converter para cópia
```

### Converter de symlink para cópia
```bash
# Backup do symlink atual
TENANT_ID="seu-tenant-id"
rm resources/views/tenants/$TENANT_ID/home.blade.php
# Copiar template Padrao
cp resources/Templates/{Tipo}/Padrao/home.blade.php resources/views/tenants/$TENANT_ID/home.blade.php
```

## Referências

- [FLUXO_CRIACAO_AUTOMATICA_TENANTS.md](FLUXO_CRIACAO_AUTOMATICA_TENANTS.md) - Documentação sobre criação de tenants e templates
- [app/Services/TenantCreationService.php](app/Services/TenantCreationService.php) - Lógica de criação de templates
- [resources/Templates/](resources/Templates/) - Templates disponíveis

<?php

namespace App\Livewire\Proprietario;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class CustomizarHome extends Component
{
    use WithFileUploads;
    public $canEdit = false;
    public $isSymlink = false;
    public $templateType = '';
    public $homeContent = '';
    
    // Variáveis CSS customizáveis
    public $corPrimaria = '#2c3e50';
    public $corSecundaria = '#3498db';
    public $corDestaque = '#e74c3c';
    
    // Conteúdos das seções
    public $heroTituloTipo = 'nome_salao'; // 'nome_salao' ou 'personalizado'
    public $heroTituloPersonalizado = '';
    public $heroSubtituloTipo = 'endereco_salao'; // 'endereco_salao' ou 'personalizado'
    public $heroSubtituloPersonalizado = '';
    
    // Imagem do Hero
    public $heroImagem; // Upload temporário
    public $heroImagemAtual = ''; // Nome do arquivo da imagem atual
    
    // Abas
    public $activeTab = 'verificacao';
    
    public function mount()
    {
        $this->verificarEditabilidade();
        
        if ($this->canEdit) {
            $this->carregarConteudo();
            
            // Se detectou uma imagem no CSS mas ela não existe fisicamente, limpar
            if ($this->heroImagemAtual === '' && $this->temReferenciaImagemNoCSS()) {
                $this->limparImagemDoCSS();
            }
        }
    }
    
    /**
     * Verifica se o tenant pode editar a home
     * - Se é symlink: NÃO pode editar (template compartilhado)
     * - Se é arquivo real: PODE editar (template Padrao copiado)
     */
    protected function verificarEditabilidade()
    {
        $tenantId = tenant('id');
        $homeFile = resource_path("views/tenants/$tenantId/home.blade.php");
        
        if (!file_exists($homeFile)) {
            $this->canEdit = false;
            $this->templateType = 'não encontrado';
            return;
        }
        
        $this->isSymlink = is_link($homeFile);
        
        if ($this->isSymlink) {
            // É symlink → Template específico (não editável)
            $this->canEdit = false;
            $target = readlink($homeFile);
            
            // Extrai nome do template do caminho
            if (preg_match('#/Templates/([^/]+)/([^/]+)/#', $target, $matches)) {
                $this->templateType = "{$matches[1]} / {$matches[2]}";
            } else {
                $this->templateType = 'Template Específico';
            }
        } else {
            // É arquivo real → Template Padrao (editável)
            $this->canEdit = true;
            $this->templateType = 'Padrao (Editável)';
        }
        
        Log::info('Verificação de editabilidade da home', [
            'tenant_id' => $tenantId,
            'can_edit' => $this->canEdit,
            'is_symlink' => $this->isSymlink,
            'template_type' => $this->templateType,
        ]);
    }
    
    /**
     * Carrega o conteúdo atual do arquivo home.blade.php
     */
    protected function carregarConteudo()
    {
        $tenantId = tenant('id');
        $homeFile = resource_path("views/tenants/$tenantId/home.blade.php");
        
        if (file_exists($homeFile)) {
            $this->homeContent = file_get_contents($homeFile);
            $this->extrairVariaveis();
        }
    }
    
    /**
     * Extrai variáveis CSS e conteúdos do arquivo
     */
    protected function extrairVariaveis()
    {
        // Extrai cores do CSS
        if (preg_match('/--cor-primaria:\s*([^;]+);/', $this->homeContent, $matches)) {
            $this->corPrimaria = trim($matches[1]);
        }
        if (preg_match('/--cor-secundaria:\s*([^;]+);/', $this->homeContent, $matches)) {
            $this->corSecundaria = trim($matches[1]);
        }
        if (preg_match('/--cor-destaque:\s*([^;]+);/', $this->homeContent, $matches)) {
            $this->corDestaque = trim($matches[1]);
        }
        
        // Extrai textos do Hero
        if (preg_match('/<h1[^>]*>(.*?)<\/h1>/s', $this->homeContent, $matches)) {
            $tituloCompleto = $matches[1];
            
            // Verifica se usa blade variable para nome do salão
            if (preg_match('/\{\{\s*tenant\(\)->fantasy_name.*?\}\}/', $tituloCompleto)) {
                $this->heroTituloTipo = 'nome_salao';
            } else {
                $this->heroTituloTipo = 'personalizado';
                $this->heroTituloPersonalizado = strip_tags($tituloCompleto);
            }
        }
        
        if (preg_match('/<div class="hero-content">.*?<p[^>]*>(.*?)<\/p>/s', $this->homeContent, $matches)) {
            $subtituloCompleto = $matches[1];
            
            // Verifica se usa blade variable para endereço do salão
            if (preg_match('/\{\{\s*tenant\(\)->address.*?\}\}/', $subtituloCompleto)) {
                $this->heroSubtituloTipo = 'endereco_salao';
            } else {
                $this->heroSubtituloTipo = 'personalizado';
                $this->heroSubtituloPersonalizado = strip_tags($subtituloCompleto);
            }
        }
        
        // Extrai caminho da imagem do Hero (se houver url() no background)
        if (preg_match('/\\.hero\\s*\\{[^}]*background:[^}]*url\\([\\\'\\"]?([^\\\'\\"\\)]+)[\\\'\\"]?\\)/s', $this->homeContent, $matches)) {
            $fullPath = $matches[1];
            // Extrai apenas o nome do arquivo de /tenants/{id}/hero/arquivo.jpg
            $tenantId = tenant('id');
            if (preg_match("#/tenants/{$tenantId}/hero/(.+)$#", $fullPath, $fileMatch)) {
                $filename = $fileMatch[1];
                // Verifica se o arquivo realmente existe em public/tenants/{id}/hero/
                $publicPath = public_path("tenants/{$tenantId}/hero/{$filename}");
                if (file_exists($publicPath)) {
                    $this->heroImagemAtual = $filename;
                } else {
                    // Arquivo não existe, limpar referência
                    Log::info('Imagem do Hero não encontrada, limpando referência', [
                        'tenant_id' => $tenantId,
                        'expected_path' => $publicPath,
                    ]);
                }
            }
        }
    }
    
    /**
     * Restaura as cores para os valores padrão
     */
    public function restaurarCoresPadrao()
    {
        $this->corPrimaria = '#2c3e50';
        $this->corSecundaria = '#3498db';
        $this->corDestaque = '#e74c3c';
        
        session()->flash('message', 'Cores restauradas para o padrão.');
    }
    
    /**
     * Remove a imagem do Hero
     */
    public function removerImagemHero()
    {
        if (!$this->canEdit) {
            session()->flash('error', 'Este template não pode ser editado.');
            return;
        }
        
        $tenantId = tenant('id');
        $publicPath = public_path("tenants/{$tenantId}/hero/{$this->heroImagemAtual}");
        
        // Remove arquivo físico se existir
        if ($this->heroImagemAtual && file_exists($publicPath)) {
            unlink($publicPath);
        }
        
        $this->heroImagemAtual = '';
        $this->heroImagem = null;
        
        // Limpa a referência do CSS também
        $this->limparImagemDoCSS();
        
        session()->flash('message', 'Imagem removida com sucesso.');
    }
    
    /**
     * Limpa referências de imagem antigas do CSS
     */
    public function limparImagemDoCSS()
    {
        if (!$this->canEdit) {
            return;
        }
        
        $tenantId = tenant('id');
        $homeFile = resource_path("views/tenants/$tenantId/home.blade.php");
        
        if (!file_exists($homeFile)) {
            return;
        }
        
        $content = file_get_contents($homeFile);
        
        // Remove qualquer url() do background da classe .hero (com ou sem quebras de linha)
        $content = preg_replace(
            '/(\.hero\s*\{\s*min-height:[^}]*background:\s*)linear-gradient\([^)]+\),\s*url\([^)]+\);?\s*(background-size:[^;]+;)?\s*(background-position:[^;]+;)?/s',
            "$1linear-gradient(135deg, var(--cor-primaria) 0%, var(--cor-secundaria) 100%);\n            ",
            $content
        );
        
        file_put_contents($homeFile, $content);
        
        // Recarrega conteúdo
        $this->homeContent = $content;
        $this->heroImagemAtual = '';
        
        Log::info('Referências de imagem antigas limpas do CSS', [
            'tenant_id' => $tenantId,
        ]);
    }
    
    /**
     * Verifica se há uma referência de imagem no CSS do hero
     */
    protected function temReferenciaImagemNoCSS()
    {
        return preg_match('/\.hero\s*\{[^}]*background:[^}]*url\(/s', $this->homeContent);
    }
    
    /**
     * Listener para quando uma imagem é selecionada
     */
    public function updatedHeroImagem()
    {
        $this->validate([
            'heroImagem' => 'image|max:5120', // máx 5MB
        ]);
    }
    
    /**
     * Salva as customizações no arquivo
     */
    public function salvarCustomizacoes()
    {
        if (!$this->canEdit) {
            session()->flash('error', 'Este template não pode ser editado.');
            return;
        }
        
        $tenantId = tenant('id');
        $homeFile = resource_path("views/tenants/$tenantId/home.blade.php");
        
        // Atualiza as variáveis CSS
        $conteudoAtualizado = $this->homeContent;
        
        $conteudoAtualizado = preg_replace(
            '/--cor-primaria:\s*[^;]+;/',
            "--cor-primaria: {$this->corPrimaria};",
            $conteudoAtualizado
        );
        $conteudoAtualizado = preg_replace(
            '/--cor-secundaria:\s*[^;]+;/',
            "--cor-secundaria: {$this->corSecundaria};",
            $conteudoAtualizado
        );
        $conteudoAtualizado = preg_replace(
            '/--cor-destaque:\s*[^;]+;/',
            "--cor-destaque: {$this->corDestaque};",
            $conteudoAtualizado
        );
        
        // Atualiza título do Hero
        if ($this->heroTituloTipo === 'nome_salao') {
            // Usa variável Blade para nome do salão
            $novoTitulo = "{{ tenant()->fantasy_name ?? 'Bem-vindo' }}";
        } else {
            // Usa texto personalizado
            $novoTitulo = htmlspecialchars($this->heroTituloPersonalizado, ENT_QUOTES, 'UTF-8');
        }
        
        $conteudoAtualizado = preg_replace(
            '/<h1[^>]*>.*?<\/h1>/s',
            "<h1>{$novoTitulo}</h1>",
            $conteudoAtualizado
        );
        
        // Atualiza subtítulo do Hero
        if ($this->heroSubtituloTipo === 'endereco_salao') {
            // Usa variável Blade para endereço do salão
            $novoSubtitulo = "{{ tenant()->address ?? '' }}{{ tenant()->number ? ', ' . tenant()->number : '' }}";
        } else {
            // Usa texto personalizado
            $novoSubtitulo = htmlspecialchars($this->heroSubtituloPersonalizado, ENT_QUOTES, 'UTF-8');
        }
        
        $conteudoAtualizado = preg_replace(
            '/(<div class="hero-content">.*?<p[^>]*>).*?(<\/p>)/s',
            "$1{$novoSubtitulo}$2",
            $conteudoAtualizado
        );
        
        // Processa upload de nova imagem do Hero
        if ($this->heroImagem) {
            $tenantId = tenant('id');
            
            // Cria diretório em public/tenants/{id}/hero/ se não existir
            $directory = public_path("tenants/{$tenantId}/hero");
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Remove imagem antiga se existir
            if ($this->heroImagemAtual) {
                $oldFile = public_path("tenants/{$tenantId}/hero/{$this->heroImagemAtual}");
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }
            
            // Salva nova imagem copiando o conteúdo (evita problemas de permissão)
            $filename = 'hero-' . time() . '.' . $this->heroImagem->getClientOriginalExtension();
            $filePath = public_path("tenants/{$tenantId}/hero/{$filename}");
            
            // Lê o conteúdo do arquivo temporário e salva no destino
            $content = file_get_contents($this->heroImagem->getRealPath());
            file_put_contents($filePath, $content);
            
            // Define permissões corretas
            chmod($filePath, 0644);
            
            // Salva apenas o nome do arquivo
            $this->heroImagemAtual = $filename;
            
            Log::info('Imagem do Hero salva', [
                'tenant_id' => $tenantId,
                'filename' => $filename,
                'directory' => $directory,
            ]);
        }
        
        // Atualiza background da seção .hero no CSS
        if ($this->heroImagemAtual) {
            $tenantId = tenant('id');
            // URL pública: /tenants/{id}/hero/arquivo.jpg
            $imageUrl = "/tenants/{$tenantId}/hero/{$this->heroImagemAtual}";
            
            // Procura pela classe .hero e atualiza o background
            // Mantém overlay gradient sobre a imagem
            $conteudoAtualizado = preg_replace(
                '/(\.hero\s*\{[^}]*background:\s*)([^;]+)(;[^}]*\})/s',
                "$1linear-gradient(135deg, rgba(44, 62, 80, 0.7), rgba(52, 152, 219, 0.7)), url('{$imageUrl}');\n            background-size: cover;\n            background-position: center$3",
                $conteudoAtualizado
            );
            
            Log::info('Background do Hero atualizado', [
                'tenant_id' => $tenantId,
                'imageUrl' => $imageUrl,
            ]);
        } else {
            // Remove imagem do background, mantém apenas gradient
            $conteudoAtualizado = preg_replace(
                '/(\.hero\s*\{[^}]*background:\s*)linear-gradient\([^)]+\),\s*url\([^)]+\);\s*background-size:[^;]+;\s*background-position:[^;]+/s',
                '$1linear-gradient(135deg, var(--cor-primaria) 0%, var(--cor-secundaria) 100%)',
                $conteudoAtualizado
            );
        }
        
        // Salva o arquivo
        if (file_put_contents($homeFile, $conteudoAtualizado)) {
            $this->homeContent = $conteudoAtualizado;
            
            // Limpa cache de views
            Artisan::call('view:clear');
            
            session()->flash('message', 'Customizações salvas com sucesso! A página foi atualizada.');
            
            Log::info('Home customizada salva', [
                'tenant_id' => $tenantId,
                'cores' => [
                    'primaria' => $this->corPrimaria,
                    'secundaria' => $this->corSecundaria,
                    'destaque' => $this->corDestaque,
                ],
            ]);
        } else {
            session()->flash('error', 'Erro ao salvar as customizações.');
            
            Log::error('Erro ao salvar home customizada', [
                'tenant_id' => $tenantId,
            ]);
        }
    }
    
    public function render()
    {
        return view('livewire.proprietario.customizar-home');
    }
}

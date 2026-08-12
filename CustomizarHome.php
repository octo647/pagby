<?php

namespace App\Livewire\Proprietario;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class CustomizarHome extends Component
{
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
    public $heroSubtitulo = '';
    
    // Abas
    public $activeTab = 'verificacao';
    
    public function mount()
    {
        $this->verificarEditabilidade();
        
        if ($this->canEdit) {
            $this->carregarConteudo();
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
            $this->heroSubtitulo = strip_tags($matches[1]);
        }
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
            "--cor-seítulo do Hero
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
        if (!empty($this->heroSubtitulo)) {
            $subtituloEscapado = htmlspecialchars($this->heroSubtitulo, ENT_QUOTES, 'UTF-8');
            $conteudoAtualizado = preg_replace(
                '/(<div class="hero-content">.*?<p[^>]*>).*?(<\/p>)/s',
                "$1{$subtituloEscapadreplace(
                '/<h1[^>]*>.*?<\/h1>/s',
                "<h1>{$this->heroTitulo}</h1>",
                $conteudoAtualizado
            );
        }
        if (!empty($this->heroSubtitulo)) {
            $conteudoAtualizado = preg_replace(
                '/(<div class="hero-content">.*?<p[^>]*>).*?(<\/p>)/s',
                "$1{$this->heroSubtitulo}$2",
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

<?php

namespace App\Livewire\Proprietario;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use App\Models\Service;
use App\Models\User;

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
    
    // Logo
    public $logoImagem; // Upload temporário
    public $logoImagemAtual = ''; // Nome do arquivo da logo atual
    
    // Serviços
    public $servicosDisponiveis = []; // Todos os serviços do tenant
    public $servicosSelecionados = []; // IDs dos serviços escolhidos para exibir
    public $maxServicos = 6; // Máximo de serviços a exibir na home
    
    // Sobre
    public $sobreTitulo = '';
    public $sobreParagrafo1 = '';
    public $sobreParagrafo2 = '';
    public $sobreParagrafo3 = '';
    public $sobreImagem; // Upload temporário
    public $sobreImagemAtual = ''; // Nome do arquivo atual
    
    // Galeria (múltiplas imagens)
    public $galeriaImagens = []; // Uploads temporários (array)
    public $galeriaImagensAtuais = []; // Arquivos atuais (array de nomes)
    
    // Ambiente (imagens com legendas)
    public $ambienteImagens = []; // Uploads temporários
    public $ambienteImagensAtuais = []; // Arquivos atuais
    public $ambienteLegendas = ['Recepção', 'Área de Atendimento', 'Área VIP']; // Legendas editáveis
    
    // Equipe (permitir selecionar funcionários do sistema)
    public $equipeFuncionarios = []; // IDs dos funcionários selecionados
    public $equipeFuncionariosDisponiveis = []; // Todos funcionários disponíveis
    
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
            $this->carregarServiços();
            $this->carregarLogo();
            $this->carregarSobre();
            $this->carregarGaleria();
            $this->carregarAmbiente();
            $this->carregarEquipe();
        }
    }
    
    /**
     * Carrega os funcionários disponíveis e selecionados
     */
    protected function carregarEquipe()
    {
        // Carrega todos os funcionários com papel de Funcionário ou Proprietário
        $funcionarios = User::whereHas('roles', function($query) {
            $query->whereIn('role', ['Funcionário', 'Proprietário']);
        })->get();
        
        $this->equipeFuncionariosDisponiveis = $funcionarios->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->roles->first()->role ?? 'Funcionário',
                'photo' => $user->photo ? "/storage/tenant" . tenant('id') . "/{$user->photo}" : null,
            ];
        })->toArray();
        
        // Extrai IDs dos funcionários que aparecem na seção equipe
        if (preg_match('/<section[^>]*class="[^"]*equipe[^"]*"[^>]*>(.*?)<\/section>/s', $this->homeContent, $matches)) {
            $equipeContent = $matches[1];
            
            // Procura por data-user-id ou extrai nomes para tentar mapear
            preg_match_all('/data-user-id="(\d+)"/s', $equipeContent, $matchesIds);
            if (isset($matchesIds[1])) {
                $this->equipeFuncionarios = array_map('intval', $matchesIds[1]);
            }
        }
    }
    
    /**
     * Carrega as imagens e legendas da seção ambiente
     */
    protected function carregarAmbiente()
    {
        $tenantId = tenant('id');
        $ambienteDir = public_path("tenants/{$tenantId}/ambiente");
        
        // Inicializa os arrays vazios
        $this->ambienteImagensAtuais = [];
        $this->ambienteLegendas = ['Recepção', 'Área de Atendimento', 'Área VIP'];
        
        // Primeiro tenta extrair do template
        if (preg_match('/<section[^>]*class="[^"]*ambiente[^"]*"[^>]*>(.*?)<\/section>/s', $this->homeContent, $matches)) {
            $ambienteContent = $matches[1];
            
            // Extrai todas as imagens
            preg_match_all('/<img[^>]*src="([^"]+)"/s', $ambienteContent, $matchesImagens);
            if (isset($matchesImagens[1])) {
                foreach ($matchesImagens[1] as $imagePath) {
                    $filename = basename($imagePath);
                    // Verifica se o arquivo realmente existe antes de adicionar
                    if (file_exists(public_path("tenants/{$tenantId}/ambiente/{$filename}"))) {
                        $this->ambienteImagensAtuais[] = $filename;
                    }
                }
            }
            
            // Extrai legendas dos h3
            preg_match_all('/<h3[^>]*>(.*?)<\/h3>/s', $ambienteContent, $matchesLegendas);
            if (isset($matchesLegendas[1])) {
                $this->ambienteLegendas = [];
                foreach ($matchesLegendas[1] as $legenda) {
                    $this->ambienteLegendas[] = trim(strip_tags($legenda));
                }
            }
        } else {
            // Se não encontrou no template, verifica se há arquivos no diretório
            // Mas só carrega se existir pelo menos um arquivo válido
            if (is_dir($ambienteDir)) {
                $files = scandir($ambienteDir);
                $validFiles = [];
                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..' && preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {
                        // Verifica se o arquivo realmente existe e tem conteúdo
                        $filePath = $ambienteDir . '/' . $file;
                        if (file_exists($filePath) && filesize($filePath) > 0) {
                            $validFiles[] = $file;
                        }
                    }
                }
                
                if (!empty($validFiles)) {
                    sort($validFiles);
                    $this->ambienteImagensAtuais = $validFiles;
                    
                    // Define legendas padrão se não tiver
                    if (empty($this->ambienteLegendas)) {
                        $this->ambienteLegendas = ['Recepção', 'Área de Atendimento', 'Área VIP'];
                    }
                }
            }
        }
    }
    
    /**
     * Carrega as imagens da galeria
     */
    protected function carregarGaleria()
    {
        $tenantId = tenant('id');
        $galeriaDir = public_path("tenants/{$tenantId}/galeria");
        
        // Inicializa o array vazio
        $this->galeriaImagensAtuais = [];
        
        // Primeiro tenta carregar do template
        if (preg_match('/<section[^>]*class="[^"]*galeria[^"]*"[^>]*>(.*?)<\/section>/s', $this->homeContent, $matches)) {
            $galeriaContent = $matches[1];
            
            // Extrai todas as imagens da galeria
            preg_match_all('/<img[^>]*src="([^"]+)"/s', $galeriaContent, $matchesImagens);
            
            if (isset($matchesImagens[1])) {
                foreach ($matchesImagens[1] as $imagePath) {
                    $filename = basename($imagePath);
                    // Verifica se o arquivo realmente existe antes de adicionar
                    if (file_exists(public_path("tenants/{$tenantId}/galeria/{$filename}"))) {
                        $this->galeriaImagensAtuais[] = $filename;
                    }
                }
            }
        } else {
            // Se não encontrou no template, verifica se há arquivos no diretório
            // Mas só carrega se existir pelo menos um arquivo válido
            if (is_dir($galeriaDir)) {
                $files = scandir($galeriaDir);
                $validFiles = [];
                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..' && preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {
                        // Verifica se o arquivo realmente existe e tem conteúdo
                        $filePath = $galeriaDir . '/' . $file;
                        if (file_exists($filePath) && filesize($filePath) > 0) {
                            $validFiles[] = $file;
                        }
                    }
                }
                
                if (!empty($validFiles)) {
                    sort($validFiles);
                    $this->galeriaImagensAtuais = $validFiles;
                }
            }
        }
    }
    
    /**
     * Carrega o conteúdo da seção Sobre
     */
    protected function carregarSobre()
    {
        // Extrai título da seção sobre (pode ser do section-title ou do sobre-text h2)
        if (preg_match('/<section[^>]*class="[^"]*sobre[^"]*"[^>]*>.*?<h2[^>]*>(.*?)<\/h2>/s', $this->homeContent, $matches)) {
            $this->sobreTitulo = trim(strip_tags($matches[1]));
        }
        
        // Extrai imagem da seção sobre
        if (preg_match('/<div[^>]*class="[^"]*sobre-img[^"]*"[^>]*>.*?<img[^>]*src="([^"]+)"/s', $this->homeContent, $matches)) {
            $imagePath = $matches[1];
            $this->sobreImagemAtual = basename($imagePath);
        }
        
        // Extrai parágrafos da seção sobre
        if (preg_match('/<div[^>]*class="[^"]*sobre-text[^"]*"[^>]*>(.*?)<\/div>/s', $this->homeContent, $matchesDiv)) {
            $sobreTextContent = $matchesDiv[1];
            
            // Extrai todos os parágrafos
            preg_match_all('/<p[^>]*>(.*?)<\/p>/s', $sobreTextContent, $matchesParagrafos);
            
            if (isset($matchesParagrafos[1][0])) {
                $this->sobreParagrafo1 = trim(strip_tags($matchesParagrafos[1][0]));
            }
            if (isset($matchesParagrafos[1][1])) {
                $this->sobreParagrafo2 = trim(strip_tags($matchesParagrafos[1][1]));
            }
            if (isset($matchesParagrafos[1][2])) {
                $this->sobreParagrafo3 = trim(strip_tags($matchesParagrafos[1][2]));
            }
        }
    }
    
    /**
     * Carrega a logo atual do tenant
     */
    protected function carregarLogo()
    {
        $tenant = tenant();
        if ($tenant && $tenant->logo) {
            // Extrai o nome do arquivo da logo
            $this->logoImagemAtual = basename($tenant->logo);
        }
    }
    
    /**
     * Carrega os serviços do tenant
     */
    protected function carregarServiços()
    {
        // Carrega todos os serviços do tenant
        $this->servicosDisponiveis = Service::orderBy('service')
            ->get()
            ->toArray();
        
        // Extrai IDs dos serviços já exibidos na home (se houver)
        $this->servicosSelecionados = [];
        
        if (preg_match_all('/<div class="servico-card" data-service-id="(\d+)">/', $this->homeContent, $matches)) {
            $this->servicosSelecionados = array_map('intval', $matches[1]);
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
     * Gera o HTML dos cards de serviços para a home
     */
    protected function gerarHTMLServicos()
    {
        $html = '';
        $tenantId = tenant('id');
        
        foreach ($this->servicosSelecionados as $servicoId) {
            $servico = collect($this->servicosDisponiveis)->firstWhere('id', $servicoId);
            
            if ($servico) {
                $nome = htmlspecialchars($servico['service'], ENT_QUOTES, 'UTF-8');
                $preco = number_format($servico['price'], 2, ',', '.');
                $duracao = $servico['time'] ?? 30;
                
                // Verifica onde a foto está localizada
                $imagemUrl = '/images/placeholder-servico.jpg'; // Padrão
                if (!empty($servico['photo'])) {
                    // Primeiro verifica em public/tenants/{id}/ (fotos estáticas)
                    $imagePath = public_path("tenants/{$tenantId}/{$servico['photo']}");
                    if (file_exists($imagePath)) {
                        $imagemUrl = "/tenants/{$tenantId}/{$servico['photo']}";
                    } else {
                        // Se não encontrou, verifica em storage (fotos enviadas pelo sistema)
                        $storageImagePath = public_path("storage/tenant{$tenantId}/services/{$servico['photo']}");
                        if (file_exists($storageImagePath)) {
                            $imagemUrl = "/storage/tenant{$tenantId}/services/{$servico['photo']}";
                        }
                    }
                }
                
                $servicoId = $servico['id'];
                $html .= <<<HTML
<div class="servico-card" data-service-id="{$servicoId}">
                    <div class="servico-img" style="background-image: url('{$imagemUrl}');"></div>
                    <div class="servico-content">
                        <h3>{$nome}</h3>
                        <p>Duração: {$duracao} minutos</p>
                        <div class="servico-preco">R$ {$preco}</div>
                    </div>
                </div>
                
HTML;
            }
        }
        
        return $html;
    }
    
    /**
     * Salva as customizações no arquivo
     */
    public function salvarCustomizacoes()
    {
        try {
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
            
            // Atualiza background apenas com a imagem (sem overlay)
            $conteudoAtualizado = preg_replace(
                '/(\.hero\s*\{[^}]*background:\s*)([^;]+)(;[^}]*\})/s',
                "$1url('{$imageUrl}');\n            background-size: cover;\n            background-position: center$3",
                $conteudoAtualizado
            );
            
            Log::info('Background do Hero atualizado', [
                'tenant_id' => $tenantId,
                'imageUrl' => $imageUrl,
            ]);
        } else {
            // Remove imagem do background, mantém apenas gradient
            $conteudoAtualizado = preg_replace(
                '/(\.hero\s*\{[^}]*background:\s*)url\([^)]+\);\s*background-size:[^;]+;\s*background-position:[^;]+/s',
                '$1linear-gradient(135deg, var(--cor-primaria) 0%, var(--cor-secundaria) 100%)',
                $conteudoAtualizado
            );
        }
        
        // Processa upload de nova logo
        if ($this->logoImagem) {
            $tenantId = tenant('id');
            
            // Cria diretório em public/tenants/{id}/ se não existir
            $directory = public_path("tenants/{$tenantId}");
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Remove logo antiga se existir
            if ($this->logoImagemAtual) {
                $oldFile = public_path("tenants/{$tenantId}/{$this->logoImagemAtual}");
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }
            
            // Salva nova logo copiando o conteúdo
            $filename = 'logo.' . $this->logoImagem->getClientOriginalExtension();
            $filePath = public_path("tenants/{$tenantId}/{$filename}");
            
            // Lê o conteúdo do arquivo temporário e salva no destino
            $content = file_get_contents($this->logoImagem->getRealPath());
            file_put_contents($filePath, $content);
            
            // Define permissões corretas
            chmod($filePath, 0644);
            
            // Atualiza o campo logo no banco de dados do tenant (SEM barra inicial)
            $tenant = tenant();
            $tenant->logo = "tenants/{$tenantId}/{$filename}";
            $tenant->save();
            
            // Salva apenas o nome do arquivo
            $this->logoImagemAtual = $filename;
            
            Log::info('Logo salva', [
                'tenant_id' => $tenantId,
                'filename' => $filename,
                'path' => $tenant->logo,
            ]);
        }
        
        // Atualiza seção de serviços
        if (count($this->servicosSelecionados) > 0) {
            $servicosHTML = $this->gerarHTMLServicos();
            $conteudoAtualizado = preg_replace(
                '/(<div class="servicos-grid">).*?(<\/div>\s*<\/div>\s*<\/section>)/s',
                "$1\n                $servicosHTML\n            $2",
                $conteudoAtualizado
            );
            
            Log::info('Serviços atualizados na home', [
                'total' => count($this->servicosSelecionados),
                'ids' => $this->servicosSelecionados,
            ]);
        } else {
            // Remove todos os serviços quando nenhum está selecionado
            $conteudoAtualizado = preg_replace(
                '/(<div class="servicos-grid">).*?(<\/div>\s*<\/div>\s*<\/section>)/s',
                "$1\n                \n            $2",
                $conteudoAtualizado
            );
            
            Log::info('Serviços removidos da home (nenhum selecionado)');
        }
        
        // Processa upload de nova imagem da seção Sobre
        if ($this->sobreImagem) {
            $tenantId = tenant('id');
            
            // Cria diretório em public/tenants/{id}/sobre/ se não existir
            $directory = public_path("tenants/{$tenantId}/sobre");
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Remove imagem antiga se existir
            if ($this->sobreImagemAtual) {
                $oldFile = public_path("tenants/{$tenantId}/sobre/{$this->sobreImagemAtual}");
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }
            
            // Salva nova imagem copiando o conteúdo
            $filename = 'sobre-' . time() . '.' . $this->sobreImagem->getClientOriginalExtension();
            $filePath = public_path("tenants/{$tenantId}/sobre/{$filename}");
            
            // Lê o conteúdo do arquivo temporário e salva no destino
            $content = file_get_contents($this->sobreImagem->getRealPath());
            file_put_contents($filePath, $content);
            
            // Define permissões corretas
            chmod($filePath, 0644);
            
            // Salva apenas o nome do arquivo
            $this->sobreImagemAtual = $filename;
            
            Log::info('Imagem da seção Sobre salva', [
                'tenant_id' => $tenantId,
                'filename' => $filename,
            ]);
        }
        
        // Atualiza seção Sobre
        if ($this->sobreTitulo || $this->sobreParagrafo1 || $this->sobreParagrafo2 || $this->sobreParagrafo3 || $this->sobreImagemAtual) {
            $tenantId = tenant('id');
            
            // Gera o HTML da seção sobre
            $titulo = htmlspecialchars($this->sobreTitulo ?: 'Sobre Nós', ENT_QUOTES, 'UTF-8');
            $p1 = htmlspecialchars($this->sobreParagrafo1 ?: 'Somos uma empresa dedicada a oferecer os melhores serviços para nossos clientes. Com anos de experiência no mercado, garantimos qualidade e satisfação em cada atendimento.', ENT_QUOTES, 'UTF-8');
            $p2 = htmlspecialchars($this->sobreParagrafo2 ?: 'Nossa equipe é formada por profissionais qualificados e apaixonados pelo que fazem, sempre buscando as melhores técnicas e tendências do mercado.', ENT_QUOTES, 'UTF-8');
            $p3 = htmlspecialchars($this->sobreParagrafo3 ?: 'Venha nos conhecer e experimente um atendimento diferenciado!', ENT_QUOTES, 'UTF-8');
            
            // HTML da imagem (só inclui se houver imagem)
            $imagemHTML = '';
            if ($this->sobreImagemAtual) {
                $sobreImagemUrl = "/tenants/{$tenantId}/sobre/{$this->sobreImagemAtual}";
                $imagemHTML = <<<HTML
<div class="sobre-img">
                        <img src="{$sobreImagemUrl}" alt="Sobre {$titulo}">
                    </div>
                    
HTML;
            }
            
            $sobreHTML = <<<HTML
<section class="sobre" id="sobre">
                <div class="section-title">
                    <h2>{$titulo}</h2>
                </div>
                <div class="sobre-content">
                    {$imagemHTML}
                    <div class="sobre-text">
                        <h2>{{ tenant()->fantasy_name }}</h2>
                        <p>{$p1}</p>
                        <p>{$p2}</p>
                        <p>{$p3}</p>
                    </div>
                </div>
            </section>
HTML;
            
            // Substitui a seção sobre completa
            $conteudoAtualizado = preg_replace(
                '/<section[^>]*class="[^"]*sobre[^"]*"[^>]*>.*?<\/section>/s',
                $sobreHTML,
                $conteudoAtualizado
            );
            
            Log::info('Seção Sobre atualizada na home', [
                'titulo' => $this->sobreTitulo,
                'imagem' => $this->sobreImagemAtual,
            ]);
        }
        
        // Processa uploads de novas imagens da Galeria
        if (!empty($this->galeriaImagens)) {
            $tenantId = tenant('id');
            
            // Cria diretório em public/tenants/{id}/galeria/ se não existir
            $directory = public_path("tenants/{$tenantId}/galeria");
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            foreach ($this->galeriaImagens as $index => $imagem) {
                if ($imagem) {
                    // Remove imagem antiga deste índice se existir
                    if (isset($this->galeriaImagensAtuais[$index])) {
                        $oldFile = public_path("tenants/{$tenantId}/galeria/{$this->galeriaImagensAtuais[$index]}");
                        if (file_exists($oldFile)) {
                            unlink($oldFile);
                        }
                    }
                    
                    // Salva nova imagem
                    $filename = 'galeria-' . ($index + 1) . '-' . time() . '.' . $imagem->getClientOriginalExtension();
                    $filePath = public_path("tenants/{$tenantId}/galeria/{$filename}");
                    
                    $content = file_get_contents($imagem->getRealPath());
                    file_put_contents($filePath, $content);
                    chmod($filePath, 0644);
                    
                    // Atualiza array
                    $this->galeriaImagensAtuais[$index] = $filename;
                    
                    Log::info('Imagem da galeria salva', [
                        'tenant_id' => $tenantId,
                        'index' => $index,
                        'filename' => $filename,
                    ]);
                }
            }
        }
        
        // Atualiza seção Galeria
        if (!empty($this->galeriaImagensAtuais)) {
            $tenantId = tenant('id');
            
            // Gera HTML dos itens da galeria
            $galeriaItensHTML = '';
            foreach ($this->galeriaImagensAtuais as $index => $filename) {
                $imageUrl = "/tenants/{$tenantId}/galeria/{$filename}";
                $galeriaItensHTML .= <<<HTML

                    <div class="galeria-item">
                        <img src="{$imageUrl}" alt="Trabalho {{ $index + 1 }}">
                    </div>
HTML;
            }
            
            $galeriaHTML = <<<HTML
<section class="galeria" id="galeria">
                <div class="section-title">
                    <h2>Galeria</h2>
                </div>
                <div class="galeria-grid">{$galeriaItensHTML}
                </div>
            </section>
HTML;
            
            // Substitui a seção galeria completa
            $conteudoAtualizado = preg_replace(
                '/<section[^>]*class="[^"]*galeria[^"]*"[^>]*>.*?<\/section>/s',
                $galeriaHTML,
                $conteudoAtualizado
            );
            
            Log::info('Seção Galeria atualizada na home', [
                'total_imagens' => count($this->galeriaImagensAtuais),
            ]);
        } else {
            // Remove a seção galeria quando não há imagens
            $conteudoAtualizado = preg_replace(
                '/<section[^>]*class="[^"]*galeria[^"]*"[^>]*>.*?<\/section>\s*/s',
                '',
                $conteudoAtualizado
            );
            
            Log::info('Seção Galeria removida da home (nenhuma imagem)');
        }
        
        // Processa uploads de novas imagens do Ambiente
        if (!empty($this->ambienteImagens)) {
            $tenantId = tenant('id');
            
            // Cria diretório em public/tenants/{id}/ambiente/ se não existir
            $directory = public_path("tenants/{$tenantId}/ambiente");
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            foreach ($this->ambienteImagens as $index => $imagem) {
                if ($imagem) {
                    // Remove imagem antiga deste índice se existir
                    if (isset($this->ambienteImagensAtuais[$index])) {
                        $oldFile = public_path("tenants/{$tenantId}/ambiente/{$this->ambienteImagensAtuais[$index]}");
                        if (file_exists($oldFile)) {
                            unlink($oldFile);
                        }
                    }
                    
                    // Salva nova imagem
                    $filename = 'ambiente-' . ($index + 1) . '-' . time() . '.' . $imagem->getClientOriginalExtension();
                    $filePath = public_path("tenants/{$tenantId}/ambiente/{$filename}");
                    
                    $content = file_get_contents($imagem->getRealPath());
                    file_put_contents($filePath, $content);
                    chmod($filePath, 0644);
                    
                    // Atualiza array
                    $this->ambienteImagensAtuais[$index] = $filename;
                    
                    Log::info('Imagem do ambiente salva', [
                        'tenant_id' => $tenantId,
                        'index' => $index,
                        'filename' => $filename,
                    ]);
                }
            }
        }
        
        // Atualiza seção Ambiente
        if (!empty($this->ambienteImagensAtuais)) {
            $tenantId = tenant('id');
            
            // Gera HTML dos itens do ambiente
            $ambienteItensHTML = '';
            foreach ($this->ambienteImagensAtuais as $index => $filename) {
                $imageUrl = "/tenants/{$tenantId}/ambiente/{$filename}";
                $legenda = isset($this->ambienteLegendas[$index]) ? htmlspecialchars($this->ambienteLegendas[$index], ENT_QUOTES, 'UTF-8') : "Área " . ($index + 1);
                
                $ambienteItensHTML .= <<<HTML

                    <div class="ambiente-item">
                        <img src="{$imageUrl}" alt="{$legenda}">
                        <div class="ambiente-overlay">
                            <h3>{$legenda}</h3>
                        </div>
                    </div>
HTML;
            }
            
            $ambienteHTML = <<<HTML
<section class="ambiente" id="ambiente">
                <div class="section-title">
                    <h2>Nosso Ambiente</h2>
                </div>
                <div class="ambiente-grid">{$ambienteItensHTML}
                </div>
            </section>
HTML;
            
            // Substitui a seção ambiente completa
            $conteudoAtualizado = preg_replace(
                '/<section[^>]*class="[^"]*ambiente[^"]*"[^>]*>.*?<\/section>/s',
                $ambienteHTML,
                $conteudoAtualizado
            );
            
            Log::info('Seção Ambiente atualizada na home', [
                'total_imagens' => count($this->ambienteImagensAtuais),
            ]);
        } else {
            // Remove a seção ambiente quando não há imagens
            $conteudoAtualizado = preg_replace(
                '/<section[^>]*class="[^"]*ambiente[^"]*"[^>]*>.*?<\/section>\s*/s',
                '',
                $conteudoAtualizado
            );
            
            Log::info('Seção Ambiente removida da home (nenhuma imagem)');
        }
        
        // Atualiza seção Equipe
        if (!empty($this->equipeFuncionarios)) {
            // Busca os dados dos funcionários selecionados
            $funcionarios = User::whereIn('id', $this->equipeFuncionarios)
                ->with('roles')
                ->get();
            
            $tenantId = tenant('id');
            
            // Gera HTML dos cards da equipe
            $equipeCardsHTML = '';
            foreach ($funcionarios as $funcionario) {
                $nome = htmlspecialchars($funcionario->name, ENT_QUOTES, 'UTF-8');
                $cargo = $funcionario->roles->first()->role ?? 'Funcionário';
                $foto = $funcionario->photo 
                    ? "/storage/tenant{$tenantId}/{$funcionario->photo}" 
                    : '/images/default-user.png';
                
                $equipeCardsHTML .= <<<HTML

                    <div class="equipe-card" data-user-id="{$funcionario->id}">
                        <div class="equipe-foto">
                            <img src="{$foto}" alt="{$nome}">
                        </div>
                        <div class="equipe-info">
                            <h3>{$nome}</h3>
                            <p>{$cargo}</p>
                        </div>
                    </div>
HTML;
            }
            
            $equipeHTML = <<<HTML

    <section class="equipe" id="equipe">
                <div class="section-title">
                    <h2>Nossa Equipe</h2>
                </div>
                <div class="equipe-grid">{$equipeCardsHTML}
                </div>
            </section>
HTML;
            
            // Tenta substituir a seção equipe se ela já existir
            $conteudoAntes = $conteudoAtualizado;
            $conteudoAtualizado = preg_replace(
                '/<section[^>]*class="[^"]*equipe[^"]*"[^>]*>.*?<\/section>/s',
                $equipeHTML,
                $conteudoAtualizado
            );
            
            // Se não houve substituição (seção não existe), insere após a seção de serviços
            if ($conteudoAtualizado === $conteudoAntes) {
                $conteudoAtualizado = preg_replace(
                    '/(<section[^>]*class="[^"]*servicos[^"]*"[^>]*>.*?<\/section>)/s',
                    '$1' . $equipeHTML,
                    $conteudoAtualizado
                );
                Log::info('Seção Equipe inserida na home após serviços', [
                    'total_funcionarios' => count($this->equipeFuncionarios),
                ]);
            } else {
                Log::info('Seção Equipe atualizada na home', [
                    'total_funcionarios' => count($this->equipeFuncionarios),
                ]);
            }
        } else {
            // Remove a seção equipe quando não há funcionários selecionados
            $conteudoAtualizado = preg_replace(
                '/<section[^>]*class="[^"]*equipe[^"]*"[^>]*>.*?<\/section>\s*/s',
                '',
                $conteudoAtualizado
            );
            
            Log::info('Seção Equipe removida da home (nenhum funcionário)');
        }
        
        // Salva o arquivo
        if (file_put_contents($homeFile, $conteudoAtualizado)) {
            $this->homeContent = $conteudoAtualizado;
            
            // Limpa cache de views
            Artisan::call('view:clear');
            
            // Recarrega as propriedades para refletir o estado atualizado
            $this->carregarServiços();
            $this->carregarLogo();
            $this->carregarSobre();
            $this->carregarGaleria();
            $this->carregarAmbiente();
            $this->carregarEquipe();
            
            // Limpa os uploads temporários
            $this->heroImagem = null;
            $this->logoImagem = null;
            $this->sobreImagem = null;
            $this->galeriaImagens = [];
            $this->ambienteImagens = [];
            
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
        } catch (\Exception $e) {
            Log::error('=== EXCEÇÃO NO SALVAMENTO ===', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            session()->flash('error', 'Erro ao processar: ' . $e->getMessage());
        }
    }
    
    /**
     * Remove a logo atual
     */
    public function removerLogo()
    {
        if ($this->logoImagemAtual) {
            $tenantId = tenant('id');
            $filePath = public_path("tenants/{$tenantId}/{$this->logoImagemAtual}");
            
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            // Remove do banco de dados
            $tenant = tenant();
            $tenant->logo = null;
            $tenant->save();
            
            $this->logoImagemAtual = '';
            $this->logoImagem = null;
            
            session()->flash('message', 'Logo removida com sucesso!');
            
            Log::info('Logo removida', ['tenant_id' => $tenantId]);
        }
    }
    
    public function removerSobreImagem()
    {
        if ($this->sobreImagemAtual) {
            $tenantId = tenant('id');
            $filePath = public_path("tenants/{$tenantId}/sobre/{$this->sobreImagemAtual}");
            
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            $this->sobreImagemAtual = '';
            $this->sobreImagem = null;
            
            session()->flash('message', 'Imagem da seção Sobre removida com sucesso!');
            
            Log::info('Imagem da seção Sobre removida', ['tenant_id' => $tenantId]);
        }
    }
    
    public function removerGaleriaImagem($index)
    {
        if (isset($this->galeriaImagensAtuais[$index])) {
            $tenantId = tenant('id');
            $filePath = public_path("tenants/{$tenantId}/galeria/{$this->galeriaImagensAtuais[$index]}");
            
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            // Remove do array
            unset($this->galeriaImagensAtuais[$index]);
            $this->galeriaImagensAtuais = array_values($this->galeriaImagensAtuais); // Reindexar
            
            session()->flash('message', 'Imagem da galeria removida com sucesso!');
            
            Log::info('Imagem da galeria removida', ['tenant_id' => $tenantId, 'index' => $index]);
        }
    }
    
    public function removerAmbienteImagem($index)
    {
        if (isset($this->ambienteImagensAtuais[$index])) {
            $tenantId = tenant('id');
            $filePath = public_path("tenants/{$tenantId}/ambiente/{$this->ambienteImagensAtuais[$index]}");
            
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            // Remove do array
            unset($this->ambienteImagensAtuais[$index]);
            $this->ambienteImagensAtuais = array_values($this->ambienteImagensAtuais); // Reindexar
            
            session()->flash('message', 'Imagem do ambiente removida com sucesso!');
            
            Log::info('Imagem do ambiente removida', ['tenant_id' => $tenantId, 'index' => $index]);
        }
    }
    
    public function render()
    {
        return view('livewire.proprietario.customizar-home');
    }
}

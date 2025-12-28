<?php


namespace App\Livewire\Admin;


use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Tenant;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class Saloes extends Component
{
    public $templateList = [];
    use WithFileUploads;

    public function mount()
    {
        $this->loadSaloes();
        $this->loadTemplateList();
    }
        /**
         * Carrega todos os templates disponíveis em resources/Templates
         * e monta a lista para o modal, incluindo o caminho da miniatura.
         */
        public function loadTemplateList()
        {
            $templateDir = resource_path('Templates');
            $publicImagesDir = public_path('images');
            $this->templateList = [];
            if (is_dir($templateDir)) {
                foreach (scandir($templateDir) as $entry) {
                    if ($entry === '.' || $entry === '..') continue;
                    $templatePath = $templateDir . DIRECTORY_SEPARATOR . $entry;
                    if (is_dir($templatePath)) {
                        // Tenta encontrar uma miniatura padrão ou alternativa
                        // Busca arquivos photo.*, ambiente.*, hero.*
                        $thumbnail = null;
                        $patterns = ['photo.', 'ambiente.', 'hero.'];
                        if (is_dir("$publicImagesDir/$entry")) {
                            $files = scandir("$publicImagesDir/$entry");
                            foreach ($patterns as $pattern) {
                                foreach ($files as $file) {
                                    if (strpos($file, $pattern) === 0) {
                                        $thumbPath = "/images/$entry/$file";
                                        if (file_exists(public_path($thumbPath))) {
                                            $thumbnail = $thumbPath;
                                            break 2;
                                        }
                                    }
                                }
                            }
                        }
                        // Se não encontrar, usa uma imagem placeholder
                        if (!$thumbnail) {
                            $thumbnail = '/images/placeholder-template.png';
                        }
                        $this->templateList[] = [
                            'name' => $entry,
                            'thumbnail' => $thumbnail,
                        ];
                    }
                }
            }
        }
    public function loadSaloes()
    {
        $this->saloes = Tenant::all()->map(function($salon) {
            return $salon->toArray();
        })->toArray();
    }
    use WithFileUploads;
    public $logoFile;
    public $saloes = [];
    public $showCreateSalonPanel = false;
    public $newSalon = [];
    public $editedSalonIndex = null;
    public $editedSalonField = null;
    public $contact;

    // Novas propriedades para contatos
    public $contacts = [];
    public $contactsPage = 1;
    public $contactsTotalPages = 1;
    public $selectedContactId = null;

    public function loadContacts($page = 1)
    {
        $perPage = 10;
        $query = \App\Models\Contact::orderByDesc('created_at');
        $total = $query->count();
        $this->contactsTotalPages = (int) ceil($total / $perPage);
        $this->contacts = $query->skip(($page-1)*$perPage)->take($perPage)->get();
        $this->contactsPage = $page;
    }

    public function selectContact($contactId)
    {
        $contact = \App\Models\Contact::find($contactId);
        if ($contact) {
            $this->selectedContactId = $contactId;
            $this->newSalon['email'] = $contact->email;
            $this->newSalon['phone'] = $contact->phone;
            $this->newSalon['name'] = $contact->tenant_name;
            $this->newSalon['address'] = $contact->address;
            $this->newSalon['cep'] = $contact->cep;
            $this->newSalon['neighborhood'] = $contact->neighborhood;
            $this->newSalon['city'] = $contact->city;
            $this->newSalon['state'] = $contact->state;
            $this->newSalon['whatsapp'] = $contact->phone;
            $this->newSalon['fantasy_name'] = $contact->tenant_name;
            $this->newSalon['cnpj'] = $contact->cnpj;
            $this->newSalon['slug'] = Str::slug($contact->tenant_name) ?? '';
            $this->newSalon['employee_count'] = $contact->employee_count ?? 1;
            $this->newSalon['type'] = $contact->tipo ?? 'barbearia'; // Default type
            $this->newSalon['plan'] = $contact->subscription_plan ?? 'mensal'; // Default plan
        }
    }
    
    private function resetNewSalon()
    {
        $this->newSalon = [
            'id' => '',
            'type' => 'barbearia', // Default type
            'employee_count' => 1, // Default employee count
            'email' => '',
            'phone' => '', 
            'whatsapp' => '',  
            'status' => '',
            'instagram' => '',
            'facebook_client_id' => '',
            'facebook_client_secret' => '',
            'google_client_id' => '',
            'google_client_secret' => '',
            'social_login_enabled' => 0, // Corrigido para inteiro
            'name' => '',
            'cnpj' => '',
            'fantasy_name' => '',
            'slug' => '',
            'address' => '',
            'number' => null,
            'complement' => '',
            'neighborhood' => '',
            'cep' => '',
            'city' => '',
            'state' => '',
            'logo' => '',
            'plan' => '',
            'status' => '',
            'trial_started_at' => null,
            'trial_ends_at' => null,
            'subscription_ends_at' => null,
            'is_blocked' => false,
            'data' => null, // Assuming this is a JSON field, adjust as necessary
            'created_at' => null,
            'updated_at' => null
        ];
    }
    
    public function createSalon()
    {
        $this->showCreateSalonPanel = true;
        $this->resetNewSalon();
        $this->loadContacts(1);
        // Garante que o campo logo está limpo ao criar
        $this->newSalon['logo'] = '';
        session()->flash('message', 'Painel de criação aberto.');
    }
    
    public function editSalon($salonIndex)
    {
        $this->editedSalonIndex = $salonIndex;
    }
    
    public function saveSalon($salonIndex)
    {  
        $salon = $this->saloes[$salonIndex] ?? null;

        if (!is_null($salon)) {
            $salonModel = Tenant::find($salon['id']);
            Log::info('Modelo recuperado no painel Editar', [
                'id' => $salon['id'] ?? null,
                'model' => $salonModel ? $salonModel->toArray() : null
            ]);
            if (!$salonModel) {
                session()->flash('error', 'Salão não encontrado.');
                return;
            }

            // Validação do arquivo
            $this->validate([
                'logoFile' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // Upload da nova logo, se enviada
            if ($this->logoFile) {
                Log::info('LogoFile recebido no painel Editar', [
                    'originalName' => $this->logoFile->getClientOriginalName(),
                    'size' => $this->logoFile->getSize(),
                    'mime' => $this->logoFile->getMimeType(),
                ]);
                $slugForm = $salon['slug'] ?? null;
                $slugModel = $salonModel->slug ?? null;
                Log::info('Valores de slug', ['slugForm' => $slugForm, 'slugModel' => $slugModel]);
                $slug = $slugForm ?: $slugModel;
                Log::info('Slug FINAL utilizado para upload', ['slug' => $slug]);
                if (empty($slug)) {
                    Log::error('Slug está vazio, upload abortado');
                } else {
                    $ext = $this->logoFile->getClientOriginalExtension();
                    $path = "images/tenants/$slug/logo.$ext";
                    // Garante que o diretório existe
                    $storageDir = storage_path("app/public/images/tenants/$slug");
                    if (!is_dir($storageDir)) {
                        mkdir($storageDir, 0755, true);
                    }
                    // Tenta salvar no storage/app/public/images/{slug}/logo.{ext} com tratamento de erro
                    try {
                        $result = $this->logoFile->storeAs("images/tenants/$slug", "logo.$ext", 'public');
                        Log::info('Resultado do storeAs', ['result' => $result]);
                    } catch (\Exception $e) {
                        Log::error('Erro ao salvar logoFile no storage', [
                            'exception' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }
                    Log::info('LogoFile salvo em storage', [
                        'storagePath' => storage_path("app/public/images/tenants/$slug/logo.$ext"),
                        'exists' => file_exists(storage_path("app/public/images/tenants/$slug/logo.$ext")),
                    ]);
                    // Copia para public/images/{slug}/logo.{ext}
                    $storagePath = storage_path("app/public/images/tenants/$slug/logo.$ext");
                    $publicPath = public_path("images/tenants/$slug/logo.$ext");
                    if (file_exists($storagePath)) {
                        copy($storagePath, $publicPath);
                        Log::info('LogoFile copiado para public', [
                            'publicPath' => $publicPath,
                            'exists' => file_exists($publicPath),
                        ]);
                    }
                    $salon['logo'] = $path;
                }
            } else {
                Log::info('LogoFile NÃO recebido no painel Editar');
            }

            $salonModel->update([
                'type' => $salon['type'] ?? null,
                'email' => $salon['email'] ?? null,
                'phone' => $salon['phone'] ?? null,                
                'whatsapp' => $salon['whatsapp'] ?? null,
                'instagram' => $salon['instagram'] ?? null,
                'facebook_client_id' => $salon['facebook_client_id'] ?? null,
                'facebook_client_secret' => $salon['facebook_client_secret'] ?? null,
                'google_client_id' => $salon['google_client_id'] ?? null,
                'google_client_secret' => $salon['google_client_secret'] ?? null,
                'social_login_enabled' => $salon['social_login_enabled'] ?? null,
                'name' => $salon['name'] ?? null,
                'cnpj' => $salon['cnpj'] ?? null,
                'fantasy_name' => $salon['fantasy_name'] ?? null,
                'slug' => $salon['slug'] ?? null,
                'address' => $salon['address'] ?? null,
                'number' => $salon['number'] ?? null,
                'complement' => $salon['complement'] ?? null,                
                'neighborhood' => $salon['neighborhood'] ?? null,
                'cep' => $salon['cep'] ?? null,
                'city' => $salon['city'] ?? null,
                'state' => $salon['state'] ?? null,
                'logo' => $salon['logo'] ?? null,
                'plan' => $salon['plan'] ?? null,
                'status' => $salon['status'] ?? null,
                'trial_started_at' => $salon['trial_started_at'] ?? null,
                'trial_ends_at' => $salon['trial_ends_at'] ?? null,
                'subscription_started_at' => $salon['subscription_started_at'] ?? null,
                'subscription_ends_at' => $salon['subscription_ends_at'] ?? null,
                'is_blocked' => $salon['is_blocked'] ?? false,
                'data' => $salon['data'] ?? null,
                'created_at' => $salon['created_at'] ?? null,
                'updated_at' => $salon['updated_at'] ?? null          
                
               
            ]);

            $this->editedSalonIndex = null;
            $this->loadSaloes(); // Recarrega a lista
            $this->dispatch('salonLogoUpdated');
            session()->flash('message', 'Salão atualizado com sucesso.');
        } else {
            session()->flash('error', 'Salão não encontrado.');
        }
    }

    public function databaseExists($dbName)
    {
        $result = DB::select("SHOW DATABASES LIKE ?", [$dbName]);
        return !empty($result);
    }
    
    public function saveNewSalon()
    {
        $this->newSalon['id'] = $this->newSalon['slug'];
        // Deleta a base de dados do tenant, se já existir (MySQL)
        // Usa apenas o id do salão como nome do banco de dados
        $dbName = $this->newSalon['id'];
        
        try {
            if ($this->databaseExists($dbName)) {
                DB::statement("DROP DATABASE IF EXISTS `$dbName`");
            }
        } catch (\Exception $e) {
            Log::error('Erro ao tentar deletar base de dados existente do tenant: ' . $e->getMessage());
        }

        // Isso vai parar a execução e mostrar a mensagem
        // O id do salão será igual ao slug
        
        $this->validate([
            'newSalon.slug' => 'required',
            'logoFile' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        // Domínio baseado no ambiente
        $domainSuffix = env('TENANT_DOMAIN_SUFFIX', '.localhost');
        // Garante que social_login_enabled seja inteiro
        $this->newSalon['social_login_enabled'] = (int) ($this->newSalon['social_login_enabled'] ?? 0);
        // Cria o novo tenant se não existir, removendo o campo logo para não sobrescrever depois
        $newSalonData = $this->newSalon;
        if (isset($newSalonData['logo'])) {
            unset($newSalonData['logo']);
        }
        $tenant = Tenant::firstOrCreate(['id' => $this->newSalon['id']], $newSalonData);

        // Cria o domínio para o salão
        $tenant->createDomain(['domain' => $this->newSalon['id'] . $domainSuffix]);

        // Inicia automaticamente o período de teste de 30 dias
        $tenant->startTrial();

        // Cria conta do proprietário no tenant
        $this->createOwnerAccount($tenant);

        // Executa seeders do tenant
        $this->seedTenantDatabase($tenant);

        //Criar a estrutura de diretórios
        $this->createTenantDirectoryStructure($this->newSalon['id'], $this->newSalon['type'] ?? 'barbearia');

        // Salvar logo se enviada (em public/tenants/$tenantId/logo.{ext})
        if ($this->logoFile) {
            $slug = $this->newSalon['slug'];
            $originalName = $this->logoFile->getClientOriginalName();
            $ext = $this->logoFile->getClientOriginalExtension();
            Log::info('Upload de logo recebido', [
                'original_name' => $originalName,
                'extension' => $ext,
                'mime' => $this->logoFile->getMimeType(),
            ]);
            $logoDir = public_path("images/tenants/$slug");
            if (!is_dir($logoDir)) {
                mkdir($logoDir, 0755, true);
            }
            $logoPath = $logoDir . "/logo.$ext";
            try {
                $this->logoFile->storeAs("tmp", "logo.$ext", 'local');
                $tmpPath = storage_path("app/tmp/logo.$ext");
                copy($tmpPath, $logoPath);
                unlink($tmpPath);
                Log::info('LogoFile salvo em ' . $logoPath);
            } catch (\Exception $e) {
                Log::error('Erro ao salvar logoFile em public/tenants', [
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
            // Atualiza o campo logo no banco e no array
            $logoRelPath = "images/tenants/$slug/logo.$ext";
            $tenant->logo = $logoRelPath;
            $tenant->save();
            Log::info('Logo salvo no banco', ['tenant_id' => $tenant->id, 'logo' => $tenant->logo]);
            $this->newSalon['logo'] = $logoRelPath;
        } else {
            // Se não houver upload, não sobrescreve o campo logo existente
            if (!empty($this->newSalon['logo'])) {
                $tenant->logo = $this->newSalon['logo'];
                $tenant->save();
                Log::info('Logo salvo no banco (sem upload)', ['tenant_id' => $tenant->id, 'logo' => $tenant->logo]);
            }
        }
        // Atualiza o nome temporário do salão na tabela pagbypayments
        $contact = \App\Models\Contact::where('email', $this->newSalon['email'])->first();
        if ($contact) {
            \App\Models\PagByPayment::where('tenant_id', 'temp_' . $contact->id)->update(['tenant_id' => $tenant->id]);
            // Transfere o employee_count do contact para o tenant se não foi definido
            if (!isset($this->newSalon['employee_count']) || $this->newSalon['employee_count'] <= 0) {
                $tenant->employee_count = $contact->employee_count ?? 1;
                // Salva apenas employee_count, não sobrescreve logo
                $tenant->save();
            }
        }
        
        $this->dispatch('salonLogoUpdated');

        // Limpa o cache de views para garantir que novos symlinks/templates sejam reconhecidos
        \Artisan::call('view:clear');

        $this->showCreateSalonPanel = false;
        $this->loadSaloes(); // Recarrega a lista
        $this->resetNewSalon();
        session()->flash('message', 'Salão criado com sucesso.');
    }
    /**
     * Cria a estrutura de diretórios para o tenant
     */
    private function createTenantDirectoryStructure($tenantId, $tenantType)
    {
        $basePath = env('HOSTGATOR_PUBLIC_PATH', public_path());
        $storagePath = env('HOSTGATOR_STORAGE_PATH', storage_path());

        // Diretórios principais
        $directories = [
            $basePath . "/tenants/$tenantId",
            storage_path("app/public/images/$tenantId"),
            resource_path("views/tenants/$tenantId"),
            $storagePath . "/app/public/profile-photos",
            $storagePath . "/app/public/services",
            $storagePath . "/app/public/gallery",
            $storagePath . "/framework/cache",
        ];

        // Subdiretórios padrões para a home do tenant
        $viewSubdirs = [
            resource_path("views/tenants/$tenantId/partials"),
            resource_path("views/tenants/$tenantId/components"),
        ];
        $directories = array_merge($directories, $viewSubdirs);

        // Criar todos os diretórios
        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
        }


        // Cria symlink para o diretório de imagens do template

        // Usa diretamente o nome informado em $tenantType (ex: SalaoBeleza1, Barbearia1)
        $templateType = trim($tenantType);
        $templateImages = public_path("images/$templateType");
        $tenantImages = public_path("images/tenants/$tenantId");
        Log::info('Tentando criar symlink de imagens', ['templateImages' => $templateImages, 'tenantImages' => $tenantImages]);
        // Remove qualquer arquivo, diretório ou symlink existente antes de criar o symlink
        // Remover qualquer coisa existente
        if (file_exists($tenantImages) || is_link($tenantImages)) {
            @unlink($tenantImages);
            if (file_exists($tenantImages)) {
                \Illuminate\Support\Facades\File::deleteDirectory($tenantImages);
            }
        }
        // Só cria se não existir mais nada
        if (!file_exists($tenantImages) && !is_link($tenantImages) && file_exists($templateImages)) {
            symlink($templateImages, $tenantImages);
            Log::info('Symlink de imagens criado', ['from' => $templateImages, 'to' => $tenantImages]);
        } else if (file_exists($tenantImages) || is_link($tenantImages)) {
            Log::error('Falha ao criar symlink: destino ainda existe', ['tenantImages' => $tenantImages]);
        } else {
            Log::error('Diretório de template de imagens não encontrado', ['templateImages' => $templateImages]);
        }

        // Cria symlink para o template home.blade.php
        $templateHome = resource_path("Templates/$templateType/home.blade.php");
        $tenantHome = resource_path("views/tenants/$tenantId/home.blade.php");
            Log::info('Tentando criar symlink do home.blade.php', ['templateHome' => $templateHome, 'tenantHome' => $tenantHome]);
        if (!is_link($tenantHome)) {
            if (file_exists($tenantHome)) {
                unlink($tenantHome);
            }
            if (file_exists($templateHome)) {
                symlink($templateHome, $tenantHome);
                Log::info('Symlink do home.blade.php criado', ['from' => $templateHome, 'to' => $tenantHome]);
                    Log::info('Symlink do home.blade.php criado', ['from' => $templateHome, 'to' => $tenantHome]);
            } else {
                    Log::error('Arquivo de template home.blade.php não encontrado', ['templateHome' => $templateHome]);
            }
        }

        // Criar link simbólico para storage público
        $this->createTenantStorageLink($tenantId);
    }
    /**
     * Copia imagens padrão para o diretório do tenant
     */
    private function copyImages($tenantId, $tenantType)
    {
        
        // Não faz mais cópia, apenas symlink criado em createTenantDirectoryStructure
    }

   /**
     * Cria o arquivo home.blade.php personalizado para o tenant
     */
    private function createTenantHomeView($tenantId, $tenantType)
    {
        $homeViewPath = resource_path("views/tenants/$tenantId/home.blade.php");
        $homeContent = file_get_contents(resource_path("Templates/$tenantType/home.blade.php"));
        
               
        file_put_contents($homeViewPath, $homeContent);
    }



/**
 * Cria link simbólico para o storage público do tenant
 */
private function createTenantStorageLink($tenantId)
{
    $storagePath = storage_path("{$tenantId}/app/public");
    $publicPath = public_path("storage/{$tenantId}");

    // Log para diagnóstico
    Log::info('Tentando criar symlink', [
        'storagePath' => $storagePath,
        'publicPath' => $publicPath,
        'storage_exists' => is_dir($storagePath),
        'public_exists' => is_dir(dirname($publicPath)),
    ]);

    // Garante que o diretório de origem existe
    if (!is_dir($storagePath)) {
        mkdir($storagePath, 0755, true);
        Log::info('Diretório de origem criado', ['storagePath' => $storagePath]);
    }

    // Garante que o diretório pai do destino existe
    if (!is_dir(dirname($publicPath))) {
        mkdir(dirname($publicPath), 0755, true);
        Log::info('Diretório pai do symlink criado', ['publicPath' => dirname($publicPath)]);
    }

    if (!is_link($publicPath) && is_dir($storagePath)) {
        symlink($storagePath, $publicPath);
        Log::info('Symlink criado', [
            'from' => $storagePath,
            'to' => $publicPath
        ]);
    }
}

    /**
     * Cria conta do proprietário no tenant recém-criado
     */
    private function createOwnerAccount($tenant)
    {
        // Busca os dados do contato correspondente
        $contact = \App\Models\Contact::where('email', $this->newSalon['email'])->first();
        if (!$contact) {
            // Se não encontrar o contato, tenta buscar pelo nome do salão ou outros dados
            $contact = \App\Models\Contact::where('tenant_name', $this->newSalon['name'])->first();
        }

        // Inicializa tenancy para o tenant específico
        tenancy()->initialize($tenant);
        
        try {
            // Cria o usuário proprietário no banco do tenant
            $user = \App\Models\User::create([
                'name' => $contact ? $contact->owner_name : ($this->newSalon['name'] ?? 'Proprietário'),
                'email' => $this->newSalon['email'] ?? ($contact ? $contact->email : 'admin@' . $this->newSalon['id'] . '.com'),
                'phone' => $contact ? $contact->phone : null,
                'password' => Hash::make('123456'), // Senha padrão que deve ser alterada no primeiro login
                'email_verified_at' => now(),
                'status' => 'Ativo',
                'origin' => 'system',
            ]);

            // Busca ou cria a role de proprietário
            $ownerRole = \App\Models\Role::firstOrCreate(['role' => 'Proprietário']);

            // Associa o usuário à role de proprietário
            $user->roles()->attach($ownerRole->id);

            Log::info("Usuário proprietário criado para tenant {$tenant->id}: {$user->email}");
            
        } catch (\Exception $e) {
            Log::error("Erro ao criar usuário proprietário para tenant {$tenant->id}: " . $e->getMessage());
        } finally {
            // Finaliza tenancy para voltar ao contexto central
            tenancy()->end();
        }
    }

    /**
     * Executa seeders do tenant
     */
    private function seedTenantDatabase($tenant)
    {
        // Inicializa tenancy para o tenant específico
        tenancy()->initialize($tenant);
        
        try {
            // Executa o seeder do tenant
            \Illuminate\Support\Facades\Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\TenantDatabaseSeeder'
            ]);
            
            Log::info("Seeders executados para tenant {$tenant->id}");
            
        } catch (\Exception $e) {
            Log::error("Erro ao executar seeders para tenant {$tenant->id}: " . $e->getMessage());
        } finally {
            // Finaliza tenancy para voltar ao contexto central
            tenancy()->end();
        }
    }
    
    public function deleteSalon($id)
    {
        $salon = Tenant::find($id);
        if ($salon) {
            $slug = $salon->slug;
            if (empty($slug) || $slug === 'tenants' || $slug === '/') {
                Log::error("Tentativa de exclusão perigosa: slug inválido", ['id' => $id, 'slug' => $slug]);
                session()->flash('error', 'Slug do tenant inválido. Operação abortada.');
                return;
            }
            $dbName = $salon->tenancy_db_name ?? "tenant{$id}";
            try {
                // Exclui o registro do tenant
                $salon->delete();
                Log::info("Registro do tenant {$id} excluído.");

                // Exclui a base de dados do salão
                $dbPath = database_path("{$dbName}.sqlite");
                if (file_exists($dbPath)) {
                    unlink($dbPath);
                    Log::info("Base de dados {$dbName} excluída.");
                } else {
                    Log::warning("Base de dados {$dbName} não encontrada para exclusão.");
                }

                // Remove diretórios criados (apenas do tenant específico)
                $dirs = [
                    public_path("images/$slug"),
                    storage_path("app/public/images/$slug"),
                    resource_path("views/tenants/$slug"),
                    storage_path("$slug/app/public/profile-photos"),
                    storage_path("$slug/app/public/services"),
                    storage_path("$slug/app/public/gallery"),
                    storage_path("$slug/framework/cache"),
                    storage_path("$slug"),
                ];

                foreach ($dirs as $dir) {
                    // Protege contra exclusão da pasta raiz de tenants
                    if (realpath($dir) === realpath(resource_path('views/tenants')) || $dir === resource_path('views/tenants')) {
                        Log::error("Tentativa de exclusão da pasta raiz de tenants bloqueada", ['dir' => $dir]);
                        continue;
                    }
                    if (is_link($dir)) {
                        unlink($dir);
                        Log::info("Symlink removido: $dir");
                    } elseif (is_dir($dir)) {
                        \Illuminate\Support\Facades\File::deleteDirectory($dir);
                        Log::info("Diretório excluído: $dir");
                    }
                }

                // Remove link simbólico do storage público do tenant
                $publicLink = public_path("storage/tenant{$id}");
                if (is_link($publicLink)) {
                    unlink($publicLink);
                    Log::info("Link simbólico removido: $publicLink");
                }

                $this->loadSaloes(); // Recarrega a lista
                session()->flash('message', 'Salão deletado com sucesso.');
            } catch (\Exception $e) {
                Log::error("Erro ao excluir salão {$id}: " . $e->getMessage());
                session()->flash('error', 'Erro ao deletar salão. Consulte os logs.');
            }
        } else {
            session()->flash('error', 'Salão não encontrado.');
        }
    }
    
    public function render()
    {
        return view('livewire.admin.saloes');
    }
    
    public function testButton()
    {
        session()->flash('message', 'Livewire funcionando!');
    }
}
<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Tenant;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

class Saloes extends Component
{
    use WithFileUploads;
    public $logoFile;
    public $saloes = [];
    public $showCreateSalonPanel = false;
    public $newSalon = [];
    public $editedSalonIndex = null;
    public $editedSalonField = null;
    
    public function mount()
    {
        $this->loadSaloes();
        $this->resetNewSalon();
    }
    
    private function loadSaloes()
    {
        $this->saloes = Tenant::all()->map(function($salon) {
            return $salon->toArray();
        })->toArray();
       
    }
    
    private function resetNewSalon()
    {
        $this->newSalon = [
            'id' => '',
            'type' => 'barbearia', // Default type
            'email' => '',
            'phone' => '',
            'status' => '',
            'instagram' => '',
            'facebook' => '',
            'whatsapp' => '',            
            'name' => '',
            'cnpj' => '',
            'fantasy_name' => '',
            'slug' => '',
            'cep' => '',
            'neighborhood' => '',
            'state' => '',
            'logo' => '',
            'plan' => '',
            'data' => null, // Assuming this is a JSON field, adjust as necessary
            'address' => '',
            'number' => null,
            'complement' => '',
            'city' => '',
        ];
    }
    
    public function createSalon()
    {   
        
        $this->showCreateSalonPanel = true;
        $this->resetNewSalon();
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
                    $path = "images/$slug/logo.$ext";
                    // Garante que o diretório existe
                    $storageDir = storage_path("app/public/images/$slug");
                    if (!is_dir($storageDir)) {
                        mkdir($storageDir, 0755, true);
                    }
                    // Tenta salvar no storage/app/public/images/{slug}/logo.{ext} com tratamento de erro
                    try {
                        $result = $this->logoFile->storeAs("images/$slug", "logo.$ext", 'public');
                        Log::info('Resultado do storeAs', ['result' => $result]);
                    } catch (\Exception $e) {
                        Log::error('Erro ao salvar logoFile no storage', [
                            'exception' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }
                    Log::info('LogoFile salvo em storage', [
                        'storagePath' => storage_path("app/public/images/$slug/logo.$ext"),
                        'exists' => file_exists(storage_path("app/public/images/$slug/logo.$ext")),
                    ]);
                    // Copia para public/images/{slug}/logo.{ext}
                    $storagePath = storage_path("app/public/images/$slug/logo.$ext");
                    $publicPath = public_path("images/$slug/logo.$ext");
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
                'email' => $salon['email'] ?? null,
                'type' => $salon['type'] ?? null,
                'whatsapp' => $salon['whatsapp'] ?? null,
                'instagram' => $salon['instagram'] ?? null,
                'facebook' => $salon['facebook'] ?? null,
                'name' => $salon['name'] ?? null,
                'cnpj' => $salon['cnpj'] ?? null,
                'fantasy_name' => $salon['fantasy_name'] ?? null,
                'slug' => $salon['slug'] ?? null,
                'cep' => $salon['cep'] ?? null,
                'neighborhood' => $salon['neighborhood'] ?? null,
                'state' => $salon['state'] ?? null,
                'logo' => $salon['logo'] ?? null,
                'plan' => $salon['plan'] ?? null,
                'data' => $salon['data'] ?? null,
                'phone' => $salon['phone'] ?? null,
                'status' => $salon['status'] ?? null,
                'address' => $salon['address'] ?? null,
                'number' => $salon['number'] ?? null,
                'complement' => $salon['complement'] ?? null,
                'city' => $salon['city'] ?? null,
                'plan' => $salon['plan'] ?? null
            ]);

            $this->editedSalonIndex = null;
            $this->loadSaloes(); // Recarrega a lista
            $this->dispatch('salonLogoUpdated');
            session()->flash('message', 'Salão atualizado com sucesso.');
        } else {
            session()->flash('error', 'Salão não encontrado.');
        }
    }
    
    public function saveNewSalon()
    {
        
         // Isso vai parar a execução e mostrar a mensagem
        // O id do salão será igual ao slug
        $this->newSalon['id'] = $this->newSalon['slug'];
        $this->validate([
            'newSalon.slug' => 'required',
            'logoFile' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $tenant = Tenant::create($this->newSalon);

        // Cria o domínio para o salão
        $tenant->createDomain(['domain' => $this->newSalon['id'] . '.localhost']);

        // Inicia automaticamente o período de teste de 30 dias
        $tenant->startTrial();

        // Cria conta do proprietário no tenant
        $this->createOwnerAccount($tenant);

        // Executa seeders do tenant
        $this->seedTenantDatabase($tenant);

        //Criar a estrutura de diretórios
        $this->createTenantDirectoryStructure($this->newSalon['id'], $this->newSalon['type'] ?? 'barbearia');

        // Salvar logo se enviada (após copiar imagens padrão)
        if ($this->logoFile) {
            $slug = $this->newSalon['slug'];
            $ext = $this->logoFile->getClientOriginalExtension();
            $path = "images/$slug/logo.$ext";
            try {
                $result = $this->logoFile->storeAs("images/$slug", "logo.$ext", 'public');
                Log::info('Resultado do storeAs (criação)', ['result' => $result]);
            } catch (\Exception $e) {
                Log::error('Erro ao salvar logoFile no storage (criação)', [
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
            $storagePath = storage_path("app/public/images/$slug/logo.$ext");
            $publicPath = public_path("images/$slug/logo.$ext");
            Log::info('LogoFile salvo em storage (criação)', [
                'storagePath' => $storagePath,
                'exists' => file_exists($storagePath),
            ]);
            if (file_exists($storagePath)) {
                copy($storagePath, $publicPath);
                Log::info('LogoFile copiado para public (criação)', [
                    'publicPath' => $publicPath,
                    'exists' => file_exists($publicPath),
                ]);
            }
            // Atualiza o campo logo no banco
            $tenant->logo = $path;
            $tenant->save();
        }
        $this->dispatch('salonLogoUpdated');

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
        $basePath = public_path();
        $storagePath = storage_path();
        
        // Diretórios a serem criados
        $directories = [
            // Para imagens do tenant (logo, etc)
            $basePath . "/images/$tenantId",
            // Para uploads via Livewire/Laravel
            storage_path("app/public/images/$tenantId"),
            // Para views personalizadas
            resource_path("views/tenants/$tenantId"),
            // Para storage específico do tenant
            $storagePath . "/$tenantId/app/public/profile-photos",
            $storagePath . "/$tenantId/app/public/services",
            $storagePath . "/$tenantId/app/public/gallery",
            $storagePath . "/$tenantId/framework/cache",
        ];

        // Criar todos os diretórios
        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
        }
        //Copia as imagens em Barbearia ou Salao_de_beleza para o novo diretório
        $this->copyImages($tenantId, $tenantType);

        // Criar arquivo home.blade.php personalizado
        $this->createTenantHomeView($tenantId, $tenantType);
        
        // Criar link simbólico para storage público
        $this->createTenantStorageLink($tenantId);
    }
    /**
     * Copia imagens padrão para o diretório do tenant
     */
    private function copyImages($tenantId, $tenantType)
    {
        
        $source = public_path("images/$tenantType");
        $destination = public_path("images/$tenantId");

        // Copiar todos os arquivos de imagem do diretório padrão para o diretório do tenant
        File::copyDirectory($source, $destination);
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
    
    if (!is_link($publicPath) && is_dir($storagePath)) {
        symlink($storagePath, $publicPath);
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
            $contact = \App\Models\Contact::where('salon_name', $this->newSalon['name'])->first();
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

                // Remove diretórios criados
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
                    if (is_dir($dir)) {
                        File::deleteDirectory($dir);
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
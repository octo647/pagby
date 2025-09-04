<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Tenant;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class Saloes extends Component
{
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
            if (!$salonModel) {
                session()->flash('error', 'Salão não encontrado.');
                return;
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
            session()->flash('message', 'Salão atualizado com sucesso.');
        } else {
            session()->flash('error', 'Salão não encontrado.');
        }
    }
    
    public function saveNewSalon()
    {
        
         // Isso vai parar a execução e mostrar a mensagem
        $this->validate([
            'newSalon.id' => 'required',
           // 'newSalon.email' => 'required|email',
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

        $this->showCreateSalonPanel = false;
        $this->loadSaloes(); // Recarrega a lista
        $this->resetNewSalon();
        session()->flash('message', 'Salão criado com sucesso.');
    }
    /**
     * Cria a estrutura de diretórios para o tenant
     */
    private function createTenantDirectoryStructure($tenantId, $tenantType = 'salao_beleza')
    {
        $basePath = public_path();
        $storagePath = storage_path();
        
        // Diretórios a serem criados
        $directories = [
            // Para imagens do tenant (logo, etc)
            $basePath . "/images/$tenantId",
            
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
        if($tenantType == 'Salão de Beleza'){
        $source = public_path("images/Salao_beleza");
      } else {
          $source = public_path("images/Barbearia");
      }

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
        
        // Template baseado no tipo do estabelecimento
        if ($tenantType === 'barbearia') {
            $homeContent = $this->getBarbeariaHomeTemplate($tenantId);
        } else {
            $homeContent = $this->getSalaoHomeTemplate($tenantId);
        }
        
        file_put_contents($homeViewPath, $homeContent);
    }
    /**
 * Template para barbearia
 */
private function getBarbeariaHomeTemplate($tenantId)
{
    return <<<'BLADE'
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Bem-vindo ao Salão {{tenant()->id}}</title>
        <link rel="stylesheet" href="/css/app.css">
    </head>
    <body class="bg-gradient-to-br from-gray-900 to-gray-700 min-h-screen flex flex-col">
        <header class="w-full py-6 bg-gray-900 shadow">
            <div class="container mx-auto flex items-center justify-between px-4">
                <div class="flex items-center gap-3">
                    <img src={{ $tenant->data['logo'] ?? tenant()->logo }} alt="Logo do Salão {{tenant()->id}}" class="w-12 h-12 rounded-full object-cover border-2 border-yellow-600 shadow">
                    <span class="text-2xl font-extrabold text-yellow-600 tracking-wide">Barbearia {{tenant()->id}}</span>
                </div>
                <div class="flex items-center gap-4">
                <a href="{{ route('login') }}" class="bg-yellow-600 text-gray-900 px-4 py-2 rounded-full text-sm font-bold shadow hover:bg-yellow-700 transition"">Entrar</a>
                <a href="{{ route('register') }}" class="bg-yellow-600 text-gray-900 px-4 py-2 rounded-full text-sm font-bold shadow hover:bg-yellow-700 transition">Registrar</a>
                </div>
            </div>
        </header>

        <main class="flex-1 flex flex-col items-center justify-center text-center px-4">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-yellow-500 mb-4 drop-shadow">Estilo e atitude para homens</h1>
            <p class="text-lg sm:text-2xl text-gray-200 mb-8">Agende seu horário e viva a experiência de um verdadeiro salão masculino!</p>
            <a href="{{ route('agendamento') }}" class="bg-yellow-600 text-gray-900 px-8 py-3 rounded-full text-lg font-bold shadow hover:bg-yellow-700 transition mb-10">Agendar agora</a>
            <div class="flex gap-8 mt-6 justify-center flex-wrap">
                <div class="flex flex-col items-center">
                    <img src="/images/{{tenant()->id}}/corte.jpg" class="w-14 h-14 mb-2 rounded shadow-lg border border-gray-800" alt="Corte">
                    <span class="text-yellow-400 font-medium">Corte</span>
                </div>
                <div class="flex flex-col items-center">
                    <img src="/images/{{tenant()->id}}/barba.jpg" class="w-14 h-14 mb-2 rounded shadow-lg border border-gray-800" alt="Barba">
                    <span class="text-yellow-400 font-medium">Barba</span>
                </div>
                <div class="flex flex-col items-center">
                    <img src="/images/{{tenant()->id}}/manicure.jpg" class="w-14 h-14 mb-2 rounded shadow-lg border border-gray-800" alt="Manicure">
                    <span class="text-yellow-400 font-medium">Manicure</span>
                </div>
                <div class="flex flex-col items-center">
                    <img src="/images/{{tenant()->id}}/coloracao.jpg" class="w-14 h-14 mb-2 rounded shadow-lg border border-gray-800" alt="Coloração">
                    <span class="text-yellow-400 font-medium">Coloração</span>
                </div>

                <!-- Adicione mais serviços se quiser -->
            </div>
        </main>

        <footer class="w-full py-4 bg-gray-900 text-center text-yellow-600 text-sm shadow mt-8">
            &copy; {{ date('Y') }} Barbearia {{tenant()->id}}. Todos os direitos reservados.
        </footer>
    </body>
    </html>

BLADE;
}

/**
 * Template para salão de beleza
 */
private function getSalaoHomeTemplate($tenantId)
{
    return <<<'BLADE'
<x-app-layout>
<div class="hero-section bg-pink-500 text-white py-20">
    <div class="container mx-auto text-center">
        <h1 class="text-5xl font-bold mb-4">Bem-vinda ao Nosso Salão</h1>
        <p class="text-xl mb-8">Beleza e cuidados especiais para realçar sua autoestima</p>
        <a href="#agendamento" class="bg-white text-pink-500 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100">
            Agendar Horário
        </a>
    </div>
</div>

<div class="services-section py-16">
    <div class="container mx-auto">
        <h2 class="text-3xl font-bold text-center mb-12">Nossos Serviços</h2>
        <div class="grid md:grid-cols-4 gap-6">
            <div class="service-card text-center p-6 border rounded-lg">
                <h3 class="text-xl font-semibold mb-3">Corte</h3>
                <p>Cortes modernos e personalizados</p>
            </div>
            <div class="service-card text-center p-6 border rounded-lg">
                <h3 class="text-xl font-semibold mb-3">Escova</h3>
                <p>Escova e modelagem profissional</p>
            </div>
            <div class="service-card text-center p-6 border rounded-lg">
                <h3 class="text-xl font-semibold mb-3">Coloração</h3>
                <p>Cores vibrantes e naturais</p>
            </div>
            <div class="service-card text-center p-6 border rounded-lg">
                <h3 class="text-xl font-semibold mb-3">Manicure</h3>
                <p>Cuidados completos para suas unhas</p>
            </div>
        </div>
    </div>
</div>

<div class="gallery-section py-16 bg-gray-50">
    <div class="container mx-auto">
        <h2 class="text-3xl font-bold text-center mb-12">Nossa Galeria</h2>
        <div class="grid md:grid-cols-3 gap-6">
            <!-- Aqui serão carregadas as imagens dos trabalhos -->
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="h-48 bg-gray-200 rounded-lg mb-4">
                    <img src="images/{{tenant()->id}}/escova.jpeg" class="w-full h-full object-cover rounded-lg" alt="Escova">
                </div>
                <p class="text-center">Corte e Escova</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="h-48 bg-gray-200 rounded-lg mb-4">
                    <img src="images/{{tenant()->id}}/coloracao.jpeg" class="w-full h-full object-cover rounded-lg" alt="Coloração">
                </div>
                <p class="text-center">Coloração</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="h-48 bg-gray-200 rounded-lg mb-4">
                    <img src="images/{{tenant()->id}}/manicure.jpeg" class="w-full h-full object-cover rounded-lg" alt="Manicure">
                </div>
                <p class="text-center">Manicure</p>
            </div>
        </div>
    </div>
</div>

<div class="contact-section bg-pink-100 py-16">
    <div class="container mx-auto text-center">
        <h2 class="text-3xl font-bold mb-8">Entre em Contato</h2>
        <p class="text-lg mb-4">Agende seu horário e venha cuidar da sua beleza</p>
        <div class="flex justify-center space-x-8">
            <div>
                <strong>Telefone:</strong> (11) 99999-9999
            </div>
            <div>
                <strong>Endereço:</strong> Rua Example, 123
            </div>
        </div>
    </div>
</div>
</x-app-layout>
BLADE;
}

/**
 * Cria link simbólico para o storage público do tenant
 */
private function createTenantStorageLink($tenantId)
{
    $storagePath = storage_path("tenant{$tenantId}/app/public");
    $publicPath = public_path("storage/tenant{$tenantId}");
    
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
            $salon->delete();
            $this->loadSaloes(); // Recarrega a lista
            session()->flash('message', 'Salão deletado com sucesso.');
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
<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\Contact;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

/**
 * Serviço centralizado para criação de tenants
 * 
 * Este serviço encapsula toda a lógica de criação de um novo tenant,
 * incluindo:
 * - Criação do registro do tenant
 * - Criação do domínio
 * - Criação da estrutura de diretórios
 * - Execução dos seeders
 * - Criação da conta do proprietário
 * - Upload da logo (opcional)
 */
class TenantCreationService
{
    /**
     * Cria um novo tenant completo
     * 
     * @param array $tenantData Dados do tenant (name, email, phone, slug, etc)
     * @param UploadedFile|null $logoFile Arquivo da logo (opcional)
     * @param string|null $templateType Tipo de template (Barbearias, Salões, etc)
     * @param string|null $template Nome do template (default: 'Padrao')
     * @return Tenant
     * @throws \Exception
     */
    public function createTenant(
        array $tenantData,
        ?UploadedFile $logoFile = null,
        ?string $templateType = 'Barbearias',
        ?string $template = 'Padrao'
    ): Tenant {
        Log::info('=== Iniciando criação de tenant ===', [
            'slug' => $tenantData['slug'] ?? 'não definido',
            'email' => $tenantData['email'] ?? 'não definido',
        ]);

        try {
            // 1. Validar dados obrigatórios
            $this->validateTenantData($tenantData);

            // 2. Preparar ID do tenant (usa slug como ID)
            $tenantId = $tenantData['slug'];
            
            // 3. Deletar banco de dados existente se necessário
            $this->dropExistingDatabase($tenantId);

            // 4. Criar registro do tenant
            $tenant = $this->createTenantRecord($tenantData, $tenantId);

            // 5. Criar domínio
            $this->createTenantDomain($tenant, $tenantId);

            // 6. Iniciar período de trial
            $tenant->startTrial();

            // 7. Criar estrutura de diretórios
            $this->createTenantDirectoryStructure($tenantId, $templateType, $template);

            // 8. Executar seeders do tenant
            $this->seedTenantDatabase($tenant);

            // 9. Criar conta do proprietário
            $this->createOwnerAccount($tenant, $tenantData);

            // 10. Fazer upload da logo se fornecida
            if ($logoFile) {
                $this->uploadLogo($tenant, $logoFile, $tenantId);
            }

            // 11. Atualizar dados do Contact se existir
            $this->updateContactData($tenant, $tenantData);

            // 12. Limpar cache de views
            Artisan::call('view:clear');

            Log::info('=== Tenant criado com sucesso ===', [
                'tenant_id' => $tenant->id,
                'slug' => $tenant->slug,
            ]);

            return $tenant;

        } catch (\Exception $e) {
            Log::error('=== Erro ao criar tenant ===', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $tenantData,
            ]);
            throw $e;
        }
    }

    /**
     * Valida dados obrigatórios do tenant
     */
    protected function validateTenantData(array $data): void
    {
        if (empty($data['slug'])) {
            throw new \Exception('Slug é obrigatório para criar um tenant');
        }

        if (empty($data['email'])) {
            throw new \Exception('Email é obrigatório para criar um tenant');
        }

        if (empty($data['fantasy_name'])) {
            throw new \Exception('Nome fantasia é obrigatório para criar um tenant');
        }
    }

    /**
     * Deleta banco de dados existente
     */
    protected function dropExistingDatabase(string $tenantId): void
    {
        try {
            $result = DB::select("SHOW DATABASES LIKE ?", [$tenantId]);
            if (!empty($result)) {
                DB::statement("DROP DATABASE IF EXISTS `$tenantId`");
                Log::info("Banco de dados existente deletado: $tenantId");
            }
        } catch (\Exception $e) {
            Log::warning('Erro ao tentar deletar banco de dados existente', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Cria registro do tenant no banco central
     */
    protected function createTenantRecord(array $data, string $tenantId): Tenant
    {
        // Garante que social_login_enabled seja inteiro
        $data['social_login_enabled'] = (int) ($data['social_login_enabled'] ?? 0);

        // Remove logo do array de criação (será salvo depois)
        $tenantData = $data;
        unset($tenantData['logo']);
        $tenantData['id'] = $tenantId;

        $tenant = Tenant::firstOrCreate(['id' => $tenantId], $tenantData);

        Log::info('Registro do tenant criado', ['tenant_id' => $tenant->id]);

        return $tenant;
    }

    /**
     * Cria domínio para o tenant
     */
    protected function createTenantDomain(Tenant $tenant, string $tenantId): void
    {
        $domainSuffix = config('app.tenant_domain_suffix', '.localhost');
        $domain = $tenantId . $domainSuffix;
        
        $tenant->createDomain(['domain' => $domain]);
        
        Log::info('Domínio criado para tenant', [
            'tenant_id' => $tenant->id,
            'domain' => $domain,
        ]);
    }

    /**
     * Cria estrutura de diretórios para o tenant
     */
    protected function createTenantDirectoryStructure(
        string $tenantId,
        string $templateType,
        string $template
    ): void {
        // Diretórios principais
        $directories = [
            public_path("/tenants/$tenantId"),
            storage_path("/tenant$tenantId"),
            resource_path("views/tenants/$tenantId"),
            storage_path("/tenant$tenantId/profile-photos"),
            storage_path("/tenant$tenantId/services"),
            storage_path("/tenant$tenantId/gallery"),
            storage_path("/tenant$tenantId/cache"),
            storage_path("/tenant$tenantId/framework/cache"),
            resource_path("views/tenants/$tenantId/partials"),
            resource_path("views/tenants/$tenantId/components"),
        ];

        // Criar todos os diretórios
        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                if (!mkdir($directory, 0775, true)) {
                    throw new \Exception("Não foi possível criar o diretório: $directory");
                }
                Log::info("Diretório criado", ['directory' => $directory]);
            }
        }

        // Criar link/cópia do template home.blade.php
        // Padrao = cópia editável, outros = symlink compartilhado
        $this->createHomeTemplateSymlink($tenantId, $templateType, $template);

        // Criar link simbólico para storage público
        $this->createTenantStorageLink($tenantId);
    }

    /**
     * Cria link ou copia o template home.blade.php para o diretório do tenant
     * - Template "Padrao": CÓPIA (editável individualmente)
     * - Outros templates: SYMLINK (compartilhado, não editável)
     */
    protected function createHomeTemplateSymlink(
        string $tenantId,
        string $templateType,
        string $template
    ): void {
        $templateHome = resource_path("Templates/$templateType/$template/home.blade.php");
        $tenantHome = resource_path("views/tenants/$tenantId/home.blade.php");

        if (!file_exists($templateHome)) {
            Log::error('Template home.blade.php não encontrado', [
                'template_path' => $templateHome,
            ]);
            throw new \Exception("Template não encontrado: $templateHome");
        }

        // Remove arquivo ou symlink existente
        if (is_link($tenantHome)) {
            unlink($tenantHome);
        } elseif (file_exists($tenantHome)) {
            unlink($tenantHome);
        }

        // Template "Padrao" é COPIADO (para permitir edição individual)
        // Outros templates usam SYMLINK (compartilhado, não editável)
        if ($template === 'Padrao') {
            if (!copy($templateHome, $tenantHome)) {
                throw new \Exception("Erro ao copiar template home.blade.php para o tenant");
            }
            
            Log::info('Template Padrao copiado para o tenant (editável)', [
                'from' => $templateHome,
                'to' => $tenantHome,
                'editable' => true,
            ]);
        } else {
            if (!symlink($templateHome, $tenantHome)) {
                throw new \Exception("Erro ao criar symlink do template home.blade.php");
            }
            
            Log::info('Symlink do template criado para o tenant (não editável)', [
                'from' => $templateHome,
                'to' => $tenantHome,
                'editable' => false,
            ]);
        }
    }

    /**
     * Cria link simbólico para o storage público do tenant
     */
    protected function createTenantStorageLink(string $tenantId): void
    {
        $storagePath = storage_path("tenants/$tenantId/app/public");
        $publicPath = public_path("storage/tenants/$tenantId");

        // Garante que o diretório de origem existe
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0775, true);
        }

        // Garante que o diretório pai do destino existe
        if (!is_dir(dirname($publicPath))) {
            mkdir(dirname($publicPath), 0775, true);
        }

        // Remove symlink antigo se existir
        if (is_link($publicPath)) {
            @unlink($publicPath);
        }

        if (@symlink($storagePath, $publicPath)) {
            Log::info('Symlink de storage criado', [
                'from' => $storagePath,
                'to' => $publicPath,
            ]);
        } else {
            Log::warning('Não foi possível criar symlink de storage', [
                'from' => $storagePath,
                'to' => $publicPath,
            ]);
        }
    }

    /**
     * Executa seeders do tenant (cria roles básicas)
     */
    protected function seedTenantDatabase(Tenant $tenant): void
    {
        tenancy()->initialize($tenant);
        
        try {
            // Cria as roles básicas
            $roles = [
                ['role' => 'Proprietário'],
                ['role' => 'Funcionário'],
                ['role' => 'Cliente'],
            ];

            foreach ($roles as $roleData) {
                \App\Models\Role::firstOrCreate($roleData);
            }
            
            Log::info("Roles criadas para tenant", ['tenant_id' => $tenant->id]);
            
        } catch (\Exception $e) {
            Log::error("Erro ao criar roles para tenant", [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } finally {
            tenancy()->end();
        }
    }

    /**
     * Cria conta do proprietário no tenant
     */
    protected function createOwnerAccount(Tenant $tenant, array $tenantData): void
    {
        // Busca dados do contato se existir
        $contact = Contact::where('email', $tenantData['email'])->first();

        tenancy()->initialize($tenant);
        
        try {
            // Cria o usuário proprietário
            $user = \App\Models\User::create([
                'name' => $contact ? $contact->owner_name : ($tenantData['name'] ?? 'Proprietário'),
                'email' => $tenantData['email'],
                'phone' => $contact ? $contact->phone : ($tenantData['phone'] ?? null),
                'password' => Hash::make('123456'), // Senha padrão
                'email_verified_at' => now(),
                'status' => 'Ativo',
                'origin' => 'system',
            ]);

            // Busca a role de proprietário
            $ownerRole = \App\Models\Role::where('role', 'Proprietário')->first();

            // Associa o usuário à role de proprietário
            if ($ownerRole) {
                $user->roles()->attach($ownerRole->id);
            }

            Log::info("Usuário proprietário criado", [
                'tenant_id' => $tenant->id,
                'user_email' => $user->email,
            ]);
            
        } catch (\Exception $e) {
            Log::error("Erro ao criar usuário proprietário", [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } finally {
            tenancy()->end();
        }
        
        // Enviar e-mail com credenciais após finalizar o tenancy context
        if ($contact) {
            try {
                \Mail::to($contact->email)->send(
                    new \App\Mail\TenantOwnerCredentialsMail($contact, $tenant, '123456')
                );
                
                Log::info("📧 E-mail de credenciais enviado com sucesso", [
                    'tenant_id' => $tenant->id,
                    'email' => $contact->email,
                ]);
            } catch (\Exception $e) {
                Log::error("❌ Erro ao enviar e-mail de credenciais", [
                    'tenant_id' => $tenant->id,
                    'email' => $contact->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Faz upload da logo do tenant
     */
    protected function uploadLogo(Tenant $tenant, UploadedFile $logoFile, string $tenantId): void
    {
        $ext = $logoFile->getClientOriginalExtension();
        $logoDir = public_path("tenants/$tenantId");
        
        if (!is_dir($logoDir)) {
            mkdir($logoDir, 0775, true);
        }

        $logoPath = $logoDir . "/logo.$ext";
        
        try {
            // Salva temporariamente
            $logoFile->storeAs("tmp", "logo.$ext", 'local');
            $tmpPath = storage_path("app/tmp/logo.$ext");
            
            // Copia para o destino final
            copy($tmpPath, $logoPath);
            unlink($tmpPath);
            
            // Atualiza o campo logo no banco
            $logoRelPath = "tenants/$tenantId/logo.$ext";
            $tenant->logo = $logoRelPath;
            $tenant->save();
            
            Log::info('Logo salvo', [
                'tenant_id' => $tenant->id,
                'logo_path' => $logoRelPath,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao salvar logo', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);
            // Não lança exceção para não interromper a criação do tenant
        }
    }

    /**
     * Atualiza dados do Contact relacionado
     */
    protected function updateContactData(Tenant $tenant, array $tenantData): void
    {
        $contact = Contact::where('email', $tenantData['email'])->first();
        
        if ($contact) {
            // Atualiza pagamentos temporários
            \App\Models\PagByPayment::where('tenant_id', 'temp_' . $contact->id)
                ->update(['tenant_id' => $tenant->id]);

            // Transfere employee_count do contact para o tenant se necessário
            if (empty($tenant->employee_count) || $tenant->employee_count <= 0) {
                $tenant->employee_count = $contact->employee_count ?? 1;
                $tenant->save();
            }

            Log::info('Dados do contact atualizados', [
                'tenant_id' => $tenant->id,
                'contact_id' => $contact->id,
            ]);
        }
    }

    /**
     * Cria um tenant a partir de um Contact
     * 
     * @param Contact $contact Contato com os dados do tenant
     * @param UploadedFile|null $logoFile Arquivo da logo (opcional)
     * @return Tenant
     */
    public function createTenantFromContact(Contact $contact, ?UploadedFile $logoFile = null): Tenant
    {
        // Mapear tipo do contact para templateType
        $typeMap = [
            'Barbearia' => 'Barbearias',
            'Salão de Beleza' => 'Salões',
            'Spa' => 'Spas',
            'Estetica' => 'Esteticistas',
            'PetShop' => 'PetShops',
            'Veterinaria' => 'Veterinárias',
        ];

        $templateType = $typeMap[$contact->tipo ?? 'Barbearia'] ?? 'Barbearias';

        $tenantData = [
            'slug' => Str::slug($contact->tenant_name),
            'fantasy_name' => $contact->tenant_name,
            'name' => $contact->owner_name,
            'email' => $contact->email,
            'phone' => $contact->phone,
            'whatsapp' => $contact->phone,
            'address' => $contact->address,
            'number' => $contact->number,
            'complement' => $contact->complement,
            'cep' => $contact->cep,
            'neighborhood' => $contact->neighborhood,
            'city' => $contact->city,
            'state' => $contact->state,
            'cnpj' => $contact->cnpj ?? null,
            'employee_count' => $contact->employee_count ?? 1,
            'type' => $contact->tipo ?? 'Barbearia',
            'template' => 'Padrao',
            'plan' => $contact->subscription_plan ?? 'mensal',
        ];

        return $this->createTenant($tenantData, $logoFile, $templateType, 'Padrao');
    }
}

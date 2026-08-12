<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Tenant;

class ActivateWhatsApp extends Command
{
    protected $signature = 'whatsapp:activate {phone} {tenant?}';
    protected $description = 'Ativa WhatsApp para um número específico em um tenant';

    public function handle()
    {
        $phone = $this->argument('phone');
        $tenantId = $this->argument('tenant');
        
        // Remove todos os caracteres não numéricos
        $normalizedPhone = preg_replace('/\D/', '', $phone);
        
        $this->info("🔍 Procurando número: {$normalizedPhone}");
        
        // Gera variações do número
        $variations = $this->generatePhoneVariations($normalizedPhone);
        
        $this->info("📱 Variações testadas: " . implode(', ', $variations));
        
        // Se especificou um tenant
        if ($tenantId) {
            $tenant = Tenant::where('id', $tenantId)
                ->orWhere('subdomain', $tenantId)
                ->first();
            
            if (!$tenant) {
                $this->error("❌ Tenant '{$tenantId}' não encontrado!");
                return 1;
            }
            
            $this->info("🏢 Verificando no tenant: {$tenant->subdomain}");
            
            tenancy()->initialize($tenant);
            
            return $this->activateInCurrentTenant($variations);
        }
        
        // Tenta em todos os tenants
        $this->info("🔄 Buscando em todos os tenants...\n");
        
        $tenants = Tenant::all();
        $found = false;
        
        foreach ($tenants as $tenant) {
            tenancy()->initialize($tenant);
            
            $user = $this->findUserByPhone($variations);
            
            if ($user) {
                $this->info("✅ Encontrado no tenant: {$tenant->subdomain}");
                $this->info("   👤 Usuário: {$user->name}");
                $this->info("   📞 Telefone: {$user->phone}");
                $this->info("   📧 Email: {$user->email}");
                
                $user->whatsapp_activated = true;
                $user->save();
                
                $this->info("   ✅ WhatsApp ATIVADO!\n");
                $found = true;
            }
            
            tenancy()->end();
        }
        
        if (!$found) {
            $this->error("❌ Número não encontrado em nenhum tenant!");
            $this->info("\n💡 Dica: Certifique-se de que:");
            $this->info("   • O usuário está cadastrado no sistema");
            $this->info("   • O telefone está correto no perfil");
            $this->info("   • O campo 'whatsapp' está marcado como true");
            return 1;
        }
        
        return 0;
    }
    
    private function activateInCurrentTenant($variations)
    {
        $user = $this->findUserByPhone($variations);
        
        if (!$user) {
            $this->error("❌ Número não encontrado neste tenant!");
            
            // Lista todos os usuários para debug
            $this->info("\n📋 Usuários cadastrados neste tenant:");
            $users = User::select('id', 'name', 'phone', 'whatsapp', 'whatsapp_activated')->get();
            
            foreach ($users as $u) {
                $this->line("   • {$u->name} - {$u->phone} (WhatsApp: " . 
                    ($u->whatsapp ? 'Sim' : 'Não') . ", Ativado: " . 
                    ($u->whatsapp_activated ? 'Sim' : 'Não') . ")");
            }
            
            return 1;
        }
        
        $this->info("✅ Usuário encontrado!");
        $this->info("   👤 Nome: {$user->name}");
        $this->info("   📞 Telefone: {$user->phone}");
        $this->info("   📧 Email: {$user->email}");
        $this->info("   ✓ WhatsApp: " . ($user->whatsapp ? 'Sim' : 'Não'));
        $this->info("   ✓ Ativado: " . ($user->whatsapp_activated ? 'Sim' : 'Não'));
        
        if ($user->whatsapp_activated) {
            $this->warn("\n⚠️  WhatsApp já estava ativado!");
            return 0;
        }
        
        $user->whatsapp_activated = true;
        $user->save();
        
        $this->info("\n✅ WhatsApp ATIVADO com sucesso!");
        
        return 0;
    }
    
    private function findUserByPhone($variations)
    {
        return User::where(function($query) use ($variations) {
            foreach ($variations as $variant) {
                $query->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '(', ''), ')', '') = ?", [$variant]);
            }
        })->first();
    }
    
    private function generatePhoneVariations($phone)
    {
        $variations = [$phone];
        
        // Com zero na frente
        if (!str_starts_with($phone, '0')) {
            $variations[] = '0' . $phone;
        }
        
        // Remove código do país se presente
        if (str_starts_with($phone, '55')) {
            $withoutCountry = substr($phone, 2);
            $variations[] = $withoutCountry;
            $variations[] = '0' . $withoutCountry;
        }
        
        // Se tem 10 dígitos (DDD + número sem 9), adiciona o 9
        if (strlen($phone) == 10) {
            $with9 = substr($phone, 0, 2) . '9' . substr($phone, 2);
            $variations[] = $with9;
            $variations[] = '0' . $with9;
        }
        
        // Se tem 11 dígitos (DDD + 9 + número), remove o 9
        if (strlen($phone) == 11 && substr($phone, 2, 1) == '9') {
            $without9 = substr($phone, 0, 2) . substr($phone, 3);
            $variations[] = $without9;
            $variations[] = '0' . $without9;
        }
        
        return array_unique($variations);
    }
}

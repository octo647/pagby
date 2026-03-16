<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class LimparHeroTenant extends Command
{
    protected $signature = 'tenant:limpar-hero {tenantId}';
    protected $description = 'Limpa referências antigas de imagem do Hero de um tenant';

    public function handle()
    {
        $tenantId = $this->argument('tenantId');
        $homeFile = resource_path("views/tenants/$tenantId/home.blade.php");
        
        if (!file_exists($homeFile)) {
            $this->error("Arquivo home.blade.php não encontrado para o tenant {$tenantId}");
            return 1;
        }
        
        $content = file_get_contents($homeFile);
        
        // Remover , url(...);
        $content = preg_replace(
            '/,\s*url\([^)]+\);/',
            ';',
            $content
        );
        
        // Substituir cores fixas pelas variáveis
        $content = str_replace(
            'linear-gradient(135deg, rgba(44, 62, 80, 0.7), rgba(52, 152, 219, 0.7))',
            'linear-gradient(135deg, var(--cor-primaria) 0%, var(--cor-secundaria) 100%)',
            $content
        );
        
        $result = file_put_contents($homeFile, $content);
        
        if ($result === false) {
            $this->error("Erro ao salvar o arquivo! Verifique as permissões.");
            return 1;
        }
        
        $this->info("CSS do Hero limpo com sucesso para o tenant {$tenantId}!");
        $this->info("Bytes escritos: {$result}");
        return 0;
    }
}

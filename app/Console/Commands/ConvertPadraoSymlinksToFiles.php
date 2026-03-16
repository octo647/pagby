<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ConvertPadraoSymlinksToFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:convert-padrao-symlinks 
                            {--tenant= : ID do tenant específico para converter}
                            {--dry-run : Apenas mostrar o que seria feito, sem executar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Converte symlinks de Template Padrao para arquivos copiados (editáveis)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $specificTenant = $this->option('tenant');
        
        $this->info('🔍 Procurando tenants com Template Padrao usando symlinks...');
        $this->newLine();
        
        $tenantsDir = resource_path('views/tenants');
        
        if (!is_dir($tenantsDir)) {
            $this->error("Diretório de tenants não encontrado: $tenantsDir");
            return 1;
        }
        
        $tenantDirs = File::directories($tenantsDir);
        $converted = 0;
        $skipped = 0;
        $errors = 0;
        
        foreach ($tenantDirs as $tenantDir) {
            $tenantId = basename($tenantDir);
            
            // Se especificou tenant, processar apenas ele
            if ($specificTenant && $tenantId !== $specificTenant) {
                continue;
            }
            
            $homeFile = "$tenantDir/home.blade.php";
            
            // Verifica se arquivo existe
            if (!file_exists($homeFile)) {
                $this->warn("  ⚠️  $tenantId: home.blade.php não encontrado");
                $skipped++;
                continue;
            }
            
            // Verifica se é symlink
            if (!is_link($homeFile)) {
                if ($specificTenant) {
                    $this->info("  ℹ️  $tenantId: Já é arquivo (não é symlink) ✓");
                }
                $skipped++;
                continue;
            }
            
            // É symlink, verifica se aponta para Template Padrao
            $linkTarget = readlink($homeFile);
            
            if (!preg_match('#/Templates/[^/]+/Padrao/home\.blade\.php$#', $linkTarget)) {
                $this->line("  ⏭️  $tenantId: Symlink para template específico (mantido)");
                $skipped++;
                continue;
            }
            
            // Encontrou symlink de Template Padrao, converter!
            $this->warn("  🔄 $tenantId: Template Padrao com symlink → convertendo...");
            
            if ($dryRun) {
                $this->line("     [DRY RUN] Removeria symlink e copiaria: $linkTarget");
                $converted++;
                continue;
            }
            
            // Verifica se o arquivo de origem existe
            if (!file_exists($linkTarget)) {
                $this->error("     ❌ Erro: Arquivo de origem não existe: $linkTarget");
                $errors++;
                continue;
            }
            
            // Backup do symlink (salva o target)
            $this->line("     📋 Target: $linkTarget");
            
            // Remove o symlink
            if (!unlink($homeFile)) {
                $this->error("     ❌ Erro ao remover symlink");
                $errors++;
                continue;
            }
            
            // Copia o arquivo
            if (!copy($linkTarget, $homeFile)) {
                $this->error("     ❌ Erro ao copiar arquivo");
                // Tenta recriar o symlink
                symlink($linkTarget, $homeFile);
                $errors++;
                continue;
            }
            
            $this->info("     ✅ Convertido com sucesso! Agora é editável.");
            $converted++;
        }
        
        // Resumo
        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📊 Resumo da Operação:');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        if ($dryRun) {
            $this->warn('⚠️  MODO DRY-RUN (nenhuma alteração foi feita)');
        }
        
        $this->line("✅ Convertidos: $converted");
        $this->line("⏭️  Ignorados: $skipped");
        
        if ($errors > 0) {
            $this->error("❌ Erros: $errors");
        }
        
        if ($converted > 0 && !$dryRun) {
            $this->newLine();
            $this->info('🔄 Limpando cache de views...');
            $this->call('view:clear');
            $this->info('✅ Cache limpo!');
        }
        
        $this->newLine();
        
        if ($dryRun && $converted > 0) {
            $this->warn('💡 Para executar a conversão de verdade, rode sem --dry-run');
        }
        
        return 0;
    }
}

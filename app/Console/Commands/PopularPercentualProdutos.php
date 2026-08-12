<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ComandaProduto;
use App\Models\Estoque;

class PopularPercentualProdutos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comanda-produtos:popular-percentual {--force : Força a atualização mesmo de registros que já têm percentual}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Popula o campo percentual_produtos em comanda_produtos existentes baseado no estoque atual';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');
        
        $this->info('Iniciando população do percentual_produtos...');
        
        // Buscar todos os comanda_produtos que não têm percentual definido (ou todos se --force)
        $query = ComandaProduto::query();
        
        if (!$force) {
            $query->where(function($q) {
                $q->whereNull('percentual_produtos')
                  ->orWhere('percentual_produtos', 0);
            });
        }
        
        $comandaProdutos = $query->with('estoque')->get();
        
        $total = $comandaProdutos->count();
        $atualizados = 0;
        $erros = 0;
        
        $this->info("Total de registros a processar: {$total}");
        
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        
        foreach ($comandaProdutos as $comandaProduto) {
            try {
                if ($comandaProduto->estoque) {
                    $comandaProduto->percentual_produtos = $comandaProduto->estoque->percentual_produtos ?? 0;
                    $comandaProduto->save();
                    $atualizados++;
                } else {
                    // Se o produto do estoque foi deletado, mantém 0
                    $comandaProduto->percentual_produtos = 0;
                    $comandaProduto->save();
                    $erros++;
                }
            } catch (\Exception $e) {
                $this->error("\nErro ao processar comanda_produto ID {$comandaProduto->id}: " . $e->getMessage());
                $erros++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        
        $this->newLine(2);
        $this->info("Processo concluído!");
        $this->info("Total processado: {$total}");
        $this->info("Atualizados com sucesso: {$atualizados}");
        
        if ($erros > 0) {
            $this->warn("Registros com erro/produto não encontrado: {$erros}");
        }
        
        return Command::SUCCESS;
    }
}

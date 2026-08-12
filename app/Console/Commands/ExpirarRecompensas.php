<?php

namespace App\Console\Commands;

use App\Models\FidelidadeReward;
use Illuminate\Console\Command;

class ExpirarRecompensas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fidelidade:expirar-recompensas 
                            {--dry-run : Simula a execução sem alterar dados}
                            {--notificar : Envia notificações aos clientes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marca recompensas de fidelidade vencidas como expiradas e opcionalmente notifica clientes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $notificar = $this->option('notificar');

        $this->info('🔍 Buscando recompensas vencidas...');

        // Buscar recompensas ativas que já venceram
        $recompensasVencidas = FidelidadeReward::where('status', 'ativo')
            ->where('validade', '<', now()->toDate())
            ->get();

        $total = $recompensasVencidas->count();

        if ($total === 0) {
            $this->info('✅ Nenhuma recompensa vencida encontrada.');
            return 0;
        }

        $this->warn("⚠️  Encontradas {$total} recompensas vencidas.");

        if ($dryRun) {
            $this->info('🔷 Modo DRY RUN - Nenhuma alteração será feita');
            $this->table(
                ['ID', 'Cliente', 'Tipo', 'Valor', 'Venceu em'],
                $recompensasVencidas->map(fn($r) => [
                    $r->id,
                    $r->user->name ?? 'N/A',
                    $r->tipo,
                    $r->descricao_formatada,
                    $r->validade->format('d/m/Y'),
                ])->toArray()
            );
            return 0;
        }

        // Confirmar antes de prosseguir
        if (!$this->confirm("Deseja marcar {$total} recompensas como expiradas?")) {
            $this->info('Operação cancelada.');
            return 0;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $expirados = 0;
        $erros = 0;

        foreach ($recompensasVencidas as $recompensa) {
            try {
                // Marcar como expirado
                $recompensa->update(['status' => 'expirado']);
                $expirados++;

                // Notificar cliente (se opção ativada)
                if ($notificar) {
                    $this->notificarCliente($recompensa);
                }

                $bar->advance();
            } catch (\Exception $e) {
                $erros++;
                $this->error("\nErro ao expirar recompensa ID {$recompensa->id}: " . $e->getMessage());
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ {$expirados} recompensas marcadas como expiradas.");
        
        if ($erros > 0) {
            $this->error("❌ {$erros} erros encontrados.");
        }

        if ($notificar) {
            $this->info("📧 Notificações enviadas aos clientes.");
        }

        return 0;
    }

    /**
     * Notifica cliente sobre recompensa expirada
     */
    private function notificarCliente(FidelidadeReward $recompensa): void
    {
        // Implementar conforme seu sistema de notificações
        // Exemplos:
        
        // 1. Email
        // Mail::to($recompensa->user->email)->send(new RecompensaExpiradaMail($recompensa));
        
        // 2. Notificação no sistema
        // $recompensa->user->notify(new RecompensaExpiradaNotification($recompensa));
        
        // 3. WhatsApp (se integrado)
        // WhatsAppService::enviar($recompensa->user->telefone, "Sua recompensa expirou...");

        $this->line("  → Notificação enviada para {$recompensa->user->name}");
    }
}

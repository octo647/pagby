<?php

namespace App\Console\Commands;

use App\Models\FidelidadeReward;
use Illuminate\Console\Command;

class NotificarRecompensasExpirando extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fidelidade:notificar-expirando 
                            {--dias=7 : Dias antes do vencimento para notificar}
                            {--dry-run : Simula a execução sem enviar notificações}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifica clientes sobre recompensas que estão prestes a expirar';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $diasAntes = (int) $this->option('dias');
        $dryRun = $this->option('dry-run');

        $dataLimite = now()->addDays($diasAntes)->toDate();

        $this->info("🔍 Buscando recompensas que expiram até {$dataLimite->format('d/m/Y')}...");

        // Buscar recompensas ativas que expiram nos próximos X dias
        $recompensasExpirando = FidelidadeReward::where('status', 'ativo')
            ->where('validade', '>', now()->toDate())
            ->where('validade', '<=', $dataLimite)
            ->with('user')
            ->get();

        $total = $recompensasExpirando->count();

        if ($total === 0) {
            $this->info('✅ Nenhuma recompensa expirando nos próximos dias.');
            return 0;
        }

        // Agrupar por cliente
        $porCliente = $recompensasExpirando->groupBy('user_id');

        $this->warn("⚠️  {$total} recompensas de {$porCliente->count()} clientes expiram em breve.");

        if ($dryRun) {
            $this->info('🔷 Modo DRY RUN - Nenhuma notificação será enviada');
            $this->table(
                ['Cliente', 'Qtd Recompensas', 'Expira em'],
                $porCliente->map(function($recompensas, $userId) {
                    $cliente = $recompensas->first()->user;
                    $proximaExpiracao = $recompensas->min('validade');
                    return [
                        $cliente->name ?? "ID: {$userId}",
                        $recompensas->count(),
                        \Carbon\Carbon::parse($proximaExpiracao)->format('d/m/Y') . 
                        ' (' . \Carbon\Carbon::parse($proximaExpiracao)->diffForHumans() . ')',
                    ];
                })->values()->toArray()
            );
            return 0;
        }

        if (!$this->confirm("Deseja enviar notificações para {$porCliente->count()} clientes?")) {
            $this->info('Operação cancelada.');
            return 0;
        }

        $bar = $this->output->createProgressBar($porCliente->count());
        $bar->start();

        $enviados = 0;
        $erros = 0;

        foreach ($porCliente as $userId => $recompensas) {
            try {
                $cliente = $recompensas->first()->user;
                
                if (!$cliente) {
                    $this->error("\nCliente ID {$userId} não encontrado");
                    $erros++;
                    continue;
                }

                $this->enviarNotificacao($cliente, $recompensas);
                $enviados++;

                $bar->advance();
            } catch (\Exception $e) {
                $erros++;
                $this->error("\nErro ao notificar cliente ID {$userId}: " . $e->getMessage());
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ {$enviados} notificações enviadas com sucesso.");
        
        if ($erros > 0) {
            $this->error("❌ {$erros} erros encontrados.");
        }

        return 0;
    }

    /**
     * Envia notificação para o cliente
     */
    private function enviarNotificacao($cliente, $recompensas): void
    {
        // Montar mensagem
        $totalCreditos = $recompensas->where('tipo', 'credito')->sum('valor_credito');
        $cupons = $recompensas->where('tipo', 'cupom');
        $servicosGratis = $recompensas->where('tipo', 'servico_gratis');

        $mensagem = "🎁 Olá {$cliente->name}!\n\n";
        $mensagem .= "Você tem recompensas que expiram em breve:\n\n";

        if ($totalCreditos > 0) {
            $mensagem .= "💰 R$ " . number_format($totalCreditos, 2, ',', '.') . " em créditos\n";
        }

        if ($cupons->isNotEmpty()) {
            foreach ($cupons as $cupom) {
                $mensagem .= "🏷️ Cupom {$cupom->codigo_cupom} - {$cupom->percentual_desconto}% OFF\n";
            }
        }

        if ($servicosGratis->isNotEmpty()) {
            foreach ($servicosGratis as $servico) {
                $servicoNome = $servico->service->service ?? 'Serviço';
                $mensagem .= "✂️ {$servicoNome} grátis\n";
            }
        }

        $proximaExpiracao = $recompensas->min('validade');
        $diasRestantes = \Carbon\Carbon::parse($proximaExpiracao)->diffInDays(now());
        
        $mensagem .= "\n⏰ Expira em {$diasRestantes} dias (" . 
                    \Carbon\Carbon::parse($proximaExpiracao)->format('d/m/Y') . ")\n\n";
        $mensagem .= "Agende agora e aproveite! 📅";

        // Implementar envio conforme seu sistema
        
        // 1. Email
        // Mail::to($cliente->email)->send(new RecompensasExpirandoMail($cliente, $recompensas, $mensagem));
        
        // 2. WhatsApp
        // WhatsAppService::enviar($cliente->telefone, $mensagem);
        
        // 3. Notificação in-app
        // $cliente->notify(new RecompensasExpirandoNotification($recompensas, $mensagem));

        // Por enquanto, apenas log
        $this->line("  → Notificação preparada para {$cliente->name} ({$cliente->email})");
        
        // Salvar log de notificação sent (opcional)
        \Illuminate\Support\Facades\Log::info("Notificação de recompensas expirando", [
            'cliente_id' => $cliente->id,
            'total_recompensas' => $recompensas->count(),
            'mensagem' => $mensagem,
        ]);
    }
}

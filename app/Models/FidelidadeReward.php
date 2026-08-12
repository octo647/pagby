<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class FidelidadeReward extends Model
{
    use HasFactory;

    protected $table = 'fidelidade_rewards';

    protected $fillable = [
        'user_id',
        'tipo',
        'valor_credito',
        'codigo_cupom',
        'percentual_desconto',
        'service_id',
        'origem',
        'comanda_produto_id',
        'validade',
        'status',
        'usado_em',
        'usado_em_comanda_id',
        'descricao',
    ];

    protected $casts = [
        'valor_credito' => 'decimal:2',
        'percentual_desconto' => 'decimal:2',
        'validade' => 'date',
        'usado_em' => 'datetime',
    ];

    // Relacionamentos
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function comandaProduto(): BelongsTo
    {
        return $this->belongsTo(ComandaProduto::class);
    }

    public function comandaOndeUsado(): BelongsTo
    {
        return $this->belongsTo(Comanda::class, 'usado_em_comanda_id');
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('status', 'ativo')
                    ->where('validade', '>=', now()->toDate());
    }

    public function scopeDoCliente($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // Métodos auxiliares

    /**
     * Cria recompensa de crédito para cliente
     */
    public static function criarCredito(
        int $userId,
        float $valor,
        string $origem = 'compra_produto',
        ?int $comandaProdutoId = null,
        int $diasValidade = 30
    ): self {
        return self::create([
            'user_id' => $userId,
            'tipo' => 'credito',
            'valor_credito' => $valor,
            'origem' => $origem,
            'comanda_produto_id' => $comandaProdutoId,
            'validade' => now()->addDays($diasValidade)->toDate(),
            'descricao' => "Crédito de R$ " . number_format($valor, 2, ',', '.') . " para usar em serviços",
        ]);
    }

    /**
     * Cria cupom de desconto para cliente
     */
    public static function criarCupom(
        int $userId,
        float $percentualDesconto,
        string $origem = 'compra_produto',
        ?int $comandaProdutoId = null,
        int $diasValidade = 30
    ): self {
        $codigo = self::gerarCodigoCupom();

        return self::create([
            'user_id' => $userId,
            'tipo' => 'cupom',
            'codigo_cupom' => $codigo,
            'percentual_desconto' => $percentualDesconto,
            'origem' => $origem,
            'comanda_produto_id' => $comandaProdutoId,
            'validade' => now()->addDays($diasValidade)->toDate(),
            'descricao' => $percentualDesconto . "% de desconto com o cupom " . $codigo,
        ]);
    }

    /**
     * Cria recompensa de serviço grátis
     */
    public static function criarServicoGratis(
        int $userId,
        int $serviceId,
        string $origem = 'compra_produto',
        ?int $comandaProdutoId = null,
        int $diasValidade = 60
    ): self {
        $service = Service::find($serviceId);

        return self::create([
            'user_id' => $userId,
            'tipo' => 'servico_gratis',
            'service_id' => $serviceId,
            'origem' => $origem,
            'comanda_produto_id' => $comandaProdutoId,
            'validade' => now()->addDays($diasValidade)->toDate(),
            'descricao' => "Serviço grátis: " . ($service->service ?? 'Serviço'),
        ]);
    }

    /**
     * Marca recompensa como usada
     */
    public function marcarComoUsado(int $comandaId): bool
    {
        if ($this->status !== 'ativo') {
            return false;
        }

        $this->update([
            'status' => 'usado',
            'usado_em' => now(),
            'usado_em_comanda_id' => $comandaId,
        ]);

        return true;
    }

    /**
     * Restaura recompensa quando agendamento é cancelado
     */
    public function restaurar(): bool
    {
        if ($this->status !== 'usado') {
            return false;
        }

        // Verifica se ainda está dentro da validade
        if ($this->validade < now()->toDate()) {
            // Se expirou, marca como expirado ao invés de ativo
            $this->update([
                'status' => 'expirado',
                'usado_em' => null,
                'usado_em_comanda_id' => null,
            ]);
            return false;
        }

        $this->update([
            'status' => 'ativo',
            'usado_em' => null,
            'usado_em_comanda_id' => null,
        ]);

        return true;
    }

    /**
     * Restaura todas as recompensas usadas em uma comanda
     * (usado quando agendamento é cancelado)
     */
    public static function restaurarPorComanda(int $comandaId): int
    {
        $recompensas = self::where('usado_em_comanda_id', $comandaId)
                          ->where('status', 'usado')
                          ->get();

        $restaurados = 0;
        foreach ($recompensas as $recompensa) {
            if ($recompensa->restaurar()) {
                $restaurados++;
            }
        }

        \Log::info('Recompensas restauradas ao cancelar agendamento', [
            'comanda_id' => $comandaId,
            'total_recompensas' => $recompensas->count(),
            'restaurados' => $restaurados,
        ]);

        return $restaurados;
    }

    /**
     * Cancela/remove recompensas geradas por produtos de uma comanda
     * (usado quando agendamento é cancelado e venda de produtos também)
     */
    public static function cancelarRecompensasGeradasPorComanda(int $comandaId): int
    {
        // Buscar IDs dos produtos da comanda
        $comandaProdutoIds = \App\Models\ComandaProduto::where('comanda_id', $comandaId)
            ->pluck('id')
            ->toArray();

        if (empty($comandaProdutoIds)) {
            return 0;
        }

        // Buscar recompensas geradas por esses produtos
        $recompensas = self::whereIn('comanda_produto_id', $comandaProdutoIds)
            ->whereIn('status', ['ativo', 'usado'])
            ->get();

        $cancelados = 0;
        foreach ($recompensas as $recompensa) {
            // Deletar a recompensa (pois a venda foi cancelada)
            $recompensa->delete();
            $cancelados++;
        }

        \Log::info('Recompensas geradas por produtos canceladas', [
            'comanda_id' => $comandaId,
            'comanda_produto_ids' => $comandaProdutoIds,
            'total_canceladas' => $cancelados,
        ]);

        return $cancelados;
    }

    /**
     * Verifica se a recompensa está válida
     */
    public function isValido(): bool
    {
        return $this->status === 'ativo' && 
               $this->validade >= now()->toDate();
    }

    /**
     * Verifica se a recompensa expirou e atualiza status
     */
    public function verificarExpiracao(): void
    {
        if ($this->status === 'ativo' && $this->validade < now()->toDate()) {
            $this->update(['status' => 'expirado']);
        }
    }

    /**
     * Gera código único para cupom
     */
    protected static function gerarCodigoCupom(): string
    {
        do {
            $codigo = 'PROD' . strtoupper(Str::random(6));
        } while (self::where('codigo_cupom', $codigo)->exists());

        return $codigo;
    }

    /**
     * Obter saldo total de créditos ativos do cliente
     */
    public static function saldoCreditosCliente(int $userId): float
    {
        return self::where('user_id', $userId)
            ->where('tipo', 'credito')
            ->where('status', 'ativo')
            ->where('validade', '>=', now()->toDate())
            ->sum('valor_credito');
    }

    /**
     * Formata descrição legível para o cliente
     */
    public function getDescricaoFormatadaAttribute(): string
    {
        if ($this->descricao) {
            return $this->descricao;
        }

        return match($this->tipo) {
            'credito' => "R$ " . number_format($this->valor_credito, 2, ',', '.'),
            'cupom' => "Cupom {$this->codigo_cupom} - {$this->percentual_desconto}% OFF",
            'servico_gratis' => $this->service ? $this->service->service . " grátis" : "Serviço grátis",
            default => "Recompensa disponível",
        };
    }
}

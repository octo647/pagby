<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComandaProduto extends Model
{
    use HasFactory;

    protected $table = 'comanda_produtos';

    protected $fillable = [
        'comanda_id',
        'estoque_id',
        'quantidade',
        'preco_unitario',
        'subtotal',
        'percentual_produtos',
        'observacoes'
    ];

    protected $casts = [
        'preco_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    // Relacionamentos
    public function comanda(): BelongsTo
    {
        return $this->belongsTo(Comanda::class);
    }

    public function estoque(): BelongsTo
    {
        return $this->belongsTo(Estoque::class);
    }

    // Atributos calculados
    public function getValorTotalAttribute(): float
    {
        return $this->quantidade * $this->preco_unitario;
    }

    public function getProdutoNomeAttribute(): string
    {
        return $this->estoque->produto_nome ?? 'Produto não encontrado';
    }
}
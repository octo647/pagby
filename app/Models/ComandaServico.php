<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComandaServico extends Model
{
    use HasFactory;

    protected $table = 'comanda_servicos';

    protected $fillable = [
        'comanda_id',
        'service_id',
        'funcionario_id',
        'quantidade',
        'preco_unitario',
        'subtotal',
        'status_servico',
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

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'funcionario_id');
    }

    // Métodos auxiliares
    public function marcarComoEmAndamento(): void
    {
        $this->update(['status_servico' => 'Em Andamento']);
    }

    public function marcarComoConcluido(): void
    {
        $this->update(['status_servico' => 'Concluído']);
    }

    // Atributos calculados
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status_servico) {
            'Aguardando' => '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Aguardando</span>',
            'Em Andamento' => '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Em Andamento</span>',
            'Concluído' => '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Concluído</span>',
            default => '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Desconhecido</span>'
        };
    }
}
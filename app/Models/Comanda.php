<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Comanda extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'numero_comanda',
        'cliente_nome',
        'cliente_telefone',
        'funcionario_id',
        'status',
        'data_abertura',
        'data_fechamento',
        'subtotal_servicos',
        'subtotal_produtos',
        'desconto',
        'total_geral',
        'observacoes'
    ];

    protected $casts = [
        'data_abertura' => 'datetime',
        'data_fechamento' => 'datetime',
        'subtotal_servicos' => 'decimal:2',
        'subtotal_produtos' => 'decimal:2',
        'desconto' => 'decimal:2',
        'total_geral' => 'decimal:2'
    ];

    // Relacionamentos
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'funcionario_id');
    }

    public function comandaServicos(): HasMany
    {
        return $this->hasMany(ComandaServico::class);
    }

    public function comandaProdutos(): HasMany
    {
        return $this->hasMany(ComandaProduto::class);
    }

    // Scopes
    public function scopeAbertas($query)
    {
        return $query->where('status', 'Aberta');
    }

    public function scopeFinalizadas($query)
    {
        return $query->where('status', 'Finalizada');
    }

    public function scopeParaBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeHoje($query)
    {
        return $query->whereDate('data_abertura', today());
    }

    // Métodos auxiliares
    public function recalcularTotais(): void
    {
        // Recalcular subtotal de serviços
        $this->subtotal_servicos = $this->comandaServicos()->sum('subtotal');
        
        // Recalcular subtotal de produtos
        $this->subtotal_produtos = $this->comandaProdutos()->sum('subtotal');
        
        // Calcular total geral
        $this->total_geral = $this->subtotal_servicos + $this->subtotal_produtos - $this->desconto;
        
        $this->save();
    }

    public function adicionarServico($serviceId, $funcionarioId, $quantidade = 1, $precoUnitario = null, $observacoes = null): void
    {
        $service = Service::find($serviceId);
        $preco = $precoUnitario ?? $service->getPriceForBranch($this->branch_id);
        
        ComandaServico::create([
            'comanda_id' => $this->id,
            'service_id' => $serviceId,
            'funcionario_id' => $funcionarioId,
            'quantidade' => $quantidade,
            'preco_unitario' => $preco,
            'subtotal' => $quantidade * $preco,
            'observacoes' => $observacoes
        ]);

        $this->recalcularTotais();
    }

    public function adicionarProduto($estoqueId, $quantidade, $precoUnitario = null, $observacoes = null): void
    {
        $estoque = Estoque::find($estoqueId);
        
        // Verificar se há estoque suficiente
        if ($estoque->quantidade_atual < $quantidade) {
            throw new \Exception("Estoque insuficiente. Disponível: {$estoque->quantidade_atual}");
        }

        $preco = $precoUnitario ?? $estoque->preco_unitario;
        
        ComandaProduto::create([
            'comanda_id' => $this->id,
            'estoque_id' => $estoqueId,
            'quantidade' => $quantidade,
            'preco_unitario' => $preco,
            'subtotal' => $quantidade * $preco,
            'observacoes' => $observacoes
        ]);

        // Reduzir quantidade do estoque (ao finalizar a comanda)
        if ($this->status === 'Finalizada') {
            $estoque->decrement('quantidade_atual', $quantidade);
        }

        $this->recalcularTotais();
    }

    public function finalizar(): void
    {
        // Reduzir estoque dos produtos vendidos
        foreach ($this->comandaProdutos as $comandaProduto) {
            $comandaProduto->estoque->decrement('quantidade_atual', $comandaProduto->quantidade);
        }

        $this->update([
            'status' => 'Finalizada',
            'data_fechamento' => now()
        ]);
    }

    public function cancelar(): void
    {
        $this->update([
            'status' => 'Cancelada',
            'data_fechamento' => now()
        ]);
    }

    // Geração automática do número da comanda
    public static function gerarNumeroComanda($branchId): string
    {
        $hoje = today()->format('Ymd');
        $ultimaComanda = self::where('branch_id', $branchId)
            ->where('numero_comanda', 'like', "{$branchId}-{$hoje}-%")
            ->orderBy('numero_comanda', 'desc')
            ->first();

        if ($ultimaComanda) {
            $ultimoNumero = (int) substr($ultimaComanda->numero_comanda, -3);
            $proximoNumero = str_pad($ultimoNumero + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $proximoNumero = '001';
        }

        return "{$branchId}-{$hoje}-{$proximoNumero}";
    }

    // Atributos calculados
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'Aberta' => '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Aberta</span>',
            'Finalizada' => '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Finalizada</span>',
            'Cancelada' => '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Cancelada</span>',
            default => '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Desconhecido</span>'
        };
    }

    public function getTempoAbertoAttribute(): string
    {
        if ($this->status === 'Aberta') {
            $diff = $this->data_abertura->diffForHumans(now(), true);
            return "há {$diff}";
        }
        
        if ($this->data_fechamento) {
            $diff = $this->data_abertura->diffForHumans($this->data_fechamento, true);
            return $diff;
        }

        return '-';
    }
}
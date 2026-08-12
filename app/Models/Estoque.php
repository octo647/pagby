<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estoque extends Model
{
    use HasFactory;

    protected $table = 'estoque';

    protected $fillable = [
        'branch_id',
        'produto_nome',
        'categoria',
        'quantidade_atual',
        'total_vendas',
        'quantidade_minima',
        'preco_unitario',
        'percentual_produtos',
        'fornecedor',
        'data_validade',
        'observacoes',
    ];

    protected $casts = [
        'preco_unitario' => 'decimal:2',
        'data_validade' => 'date',
    ];

    /**
     * Relacionamento com Branch
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Relacionamento many-to-many com Services (produtos recomendados para serviços)
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_estoque')
            ->withPivot('priority', 'discount_percentage', 'is_active', 'observacoes')
            ->withTimestamps()
            ->orderBy('priority');
    }

    /**
     * Relacionamento com comandas onde foi vendido
     */
    public function comandaProdutos()
    {
        return $this->hasMany(ComandaProduto::class);
    }

    /**
     * Verifica se o produto está em estoque baixo
     */
    public function isEstoqueBaixo()
    {
        return $this->quantidade_atual <= $this->quantidade_minima;
    }

    /**
     * Verifica se o produto está vencido
     */
    public function isVencido()
    {
        return $this->data_validade && $this->data_validade < now()->toDate();
    }

    /**
     * Verifica se o produto vence em breve (próximos 30 dias)
     */
    public function venceEmBreve()
    {
        return $this->data_validade && 
               $this->data_validade >= now()->toDate() && 
               $this->data_validade <= now()->addDays(30)->toDate();
    }

    /**
     * Calcula o valor total do estoque do produto
     */
    public function getValorTotalAttribute()
    {
        return $this->quantidade_atual * ($this->preco_unitario ?? 0);
    }

    /**
     * Scope para filtrar por filial
     */
    public function scopePorFilial($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope para filtrar por categoria
     */
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Scope para produtos com estoque baixo
     */
    public function scopeEstoqueBaixo($query)
    {
        return $query->whereRaw('quantidade_atual <= quantidade_minima');
    }

    /**
     * Scope para produtos vencidos
     */
    public function scopeVencidos($query)
    {
        return $query->where('data_validade', '<', now()->toDate());
    }

    /**
     * Scope para produtos que vencem em breve
     */
    public function scopeVencendoEmBreve($query)
    {
        return $query->where('data_validade', '>=', now()->toDate())
                    ->where('data_validade', '<=', now()->addDays(30)->toDate());
    }

    /**
     * Scope para produtos mais vendidos (ordenados por total_vendas)
     */
    public function scopeMaisVendidos($query, $limit = 10)
    {
        return $query->where('quantidade_atual', '>', 0)
                    ->orderBy('total_vendas', 'desc')
                    ->limit($limit);
    }

    /**
     * Incrementa contador de vendas
     */
    public function incrementarVendas(int $quantidade = 1): void
    {
        $this->increment('total_vendas', $quantidade);
    }

    /**
     * Registra venda e atualiza estoque
     */
    public function registrarVenda(int $quantidade): bool
    {
        if ($this->quantidade_atual < $quantidade) {
            return false;
        }

        $this->decrement('quantidade_atual', $quantidade);
        $this->incrementarVendas($quantidade);
        
        return true;
    }

    /**
     * Verifica se produto está disponível para venda
     */
    public function isDisponivelParaVenda(): bool
    {
        return $this->quantidade_atual > 0 && !$this->isVencido();
    }
}
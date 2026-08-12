<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['created_at','updated_at','photo','salon_id', 'service', 'price', 'time'];
    
    public function employees(){
        return $this->belongsToMany(User::class, 'service_user', 
        'service_id', 'user_id')->withPivot('custom_duration_minutes', 'is_active', 'notes');
    }
    
    /**
     * Alias para employees (para consistência com outros relacionamentos)
     */
    public function users()
    {
        return $this->employees();
    }
    
    /**
     * Relacionamento com configurações por filial
     */
    public function branchServices()
    {
        return $this->hasMany(BranchService::class);
    }

    /**
     * Relacionamento many-to-many com Produtos (estoque)
     * Produtos recomendados manualmente para este serviço
     */
    public function produtosRecomendados()
    {
        return $this->belongsToMany(Estoque::class, 'service_estoque')
            ->withPivot('priority', 'discount_percentage', 'is_active', 'observacoes')
            ->withTimestamps()
            ->wherePivot('is_active', true)
            ->orderBy('priority');
    }
    
    /**
     * Obter configuração específica para uma filial
     */
    public function forBranch($branchId)
    {
        return $this->branchServices()->where('branch_id', $branchId)->first();
    }
    
    /**
     * Verificar se está ativo em uma filial específica
     */
    public function isActiveInBranch($branchId)
    {
        $branchService = $this->forBranch($branchId);
        return $branchService ? $branchService->is_active : false;
    }
    
    /**
     * Obter preço para uma filial específica
     * Primeiro verifica se há sobrescrita na branch_services, senão usa o preço padrão
     */
    public function getPriceForBranch($branchId)
    {
        $branchService = $this->forBranch($branchId);
        
        // Se existe configuração específica da filial, usa esse preço
        if ($branchService && $branchService->price) {
            return $branchService->price;
        }
        
        // Senão, usa o preço padrão do serviço
        return $this->price ?? 0;
    }
    
    /**
     * Obter duração para uma filial e funcionário específicos
     * Hierarquia: ServiceUser (personalizado) -> BranchService (filial) -> Service (padrão)
     */
    public function getDurationForEmployee($branchId, $userId)
    {
        // 1º Prioridade: Tempo personalizado do funcionário
        $employee = $this->employees()->where('user_id', $userId)->first();
        if ($employee && $employee->pivot->custom_duration_minutes) {
            return $employee->pivot->custom_duration_minutes;
        }
        
        // 2º Prioridade: Tempo específico da filial
        $branchService = $this->forBranch($branchId);
        if ($branchService && $branchService->duration_minutes) {
            return $branchService->duration_minutes;
        }
        
        // 3º Prioridade: Tempo padrão do serviço
        return $this->time ?? 30;
    }
    
    /**
     * Obter duração padrão para uma filial específica (sem considerar funcionário)
     * Hierarquia: BranchService (filial) -> Service (padrão)
     */
    public function getDurationForBranch($branchId)
    {
        // Se existe configuração específica da filial, usa essa duração
        $branchService = $this->forBranch($branchId);
        if ($branchService && $branchService->duration_minutes) {
            return $branchService->duration_minutes;
        }
        
        // Senão, usa a duração padrão do serviço
        return $this->time ?? 30;
    }

    /**
     * LÓGICA HIERÁRQUICA DE RECOMENDAÇÃO DE PRODUTOS
     * 
     * Prioridade 1: Produtos relacionados manualmente (tabela pivot service_estoque)
     * Prioridade 2: Se não houver relacionamento manual, retorna produtos mais vendidos
     * 
     * @param int $branchId - Filial para filtrar produtos disponíveis
     * @param int $limit - Quantidade de produtos a retornar (default 3)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProdutosSugeridos(int $branchId, int $limit = 3)
    {
        // PRIORIDADE 1: Produtos relacionados manualmente
        $produtosManuais = $this->produtosRecomendados()
            ->where('branch_id', $branchId)
            ->where('quantidade_atual', '>', 0) // Apenas produtos em estoque
            ->limit($limit)
            ->get();

        // Se encontrou produtos relacionados manualmente, retorna eles
        if ($produtosManuais->isNotEmpty()) {
            return $produtosManuais;
        }

        // PRIORIDADE 2: Produtos mais vendidos da filial (fallback automático)
        return Estoque::where('branch_id', $branchId)
            ->maisVendidos($limit)
            ->get();
    }

    /**
     * Verifica se o serviço tem produtos recomendados configurados
     */
    public function temProdutosRecomendadosManuais(int $branchId): bool
    {
        return $this->produtosRecomendados()
            ->where('branch_id', $branchId)
            ->exists();
    }
    
    use HasFactory;
}

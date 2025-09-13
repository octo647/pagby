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
    
    use HasFactory;
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Branch extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_name',
        'cnpj',
        'require_advance_payment',
        'address',
        'complement',
        'phone',
        'whatsapp',
        'email',
        'city',
        'state',
        'logo',
        'require_comission',
        'commission'
    ];
    public function users():BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
    
    /**
     * Relacionamento com configurações de serviços da filial
     */
    public function branchServices()
    {
        return $this->hasMany(BranchService::class);
    }
    
    /**
     * Serviços ativos desta filial
     */
    public function activeServices()
    {
        return $this->branchServices()->active()->with('service');
    }
    
    /**
     * Verificar se um serviço está disponível nesta filial
     */
    public function hasService($serviceId)
    {
        return $this->branchServices()->where('service_id', $serviceId)->where('is_active', true)->exists();
    }
}

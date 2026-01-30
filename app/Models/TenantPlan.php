<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantPlan extends Model
{
    protected $table = 'tenants_plans';

    protected $fillable = [
        'tenant_id',
        'plan_id',
        'name',
        'price',
        'duration_days',
        'active',
    ];

    // Se quiser, relacione com o tenant central
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}

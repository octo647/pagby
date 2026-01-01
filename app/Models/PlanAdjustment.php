<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanAdjustment extends Model
{
    protected $connection = 'mysql'; // Central database
    
    protected $fillable = [
        'tenant_id',
        'type',
        'amount',
        'employee_count_before',
        'employee_count_after',
        'plan_period',
        'days_remaining',
        'percentage_remaining',
        'status',
        'asaas_payment_id',
        'asaas_invoice_url',
        'applied_at',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'percentage_remaining' => 'decimal:2',
        'applied_at' => 'datetime',
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento com Tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Obter créditos pendentes de um tenant
     */
    public static function getPendingCredits($tenantId)
    {
        return self::where('tenant_id', $tenantId)
            ->where('type', 'credit')
            ->where('status', 'pending')
            ->sum('amount');
    }

    /**
     * Aplicar créditos pendentes
     */
    public static function applyPendingCredits($tenantId)
    {
        $credits = self::where('tenant_id', $tenantId)
            ->where('type', 'credit')
            ->where('status', 'pending')
            ->get();

        foreach ($credits as $credit) {
            $credit->status = 'applied';
            $credit->applied_at = now();
            $credit->save();
        }

        return $credits->sum('amount');
    }

    /**
     * Marcar débito como pago
     */
    public function markAsPaid()
    {
        $this->status = 'paid';
        $this->paid_at = now();
        $this->save();
    }
}

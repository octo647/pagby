<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Payment extends Model
{
    use HasFactory;

    // Especificar a tabela explicitamente
    protected $table = 'payments';

    // Especificar conexão central explicitamente
    protected $connection = 'mysql';

    // IMPORTANTE: Adicionar todos os campos necessários
    protected $fillable = [
        'external_id',
        'preference_id', 
        'contact_id',
        'tenant_id',
        'plan',
        'amount',
        'currency',
        'status',
        'status_detail',
        'payment_method',
        'payment_type',
        'payer_data',
        'mercadopago_data',
        'approved_at',
        'expires_at',
    ];

    protected $casts = [
        'payer_data' => 'array',
        'mercadopago_data' => 'array',
        'approved_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // Relacionamentos
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByPlan($query, $plan)
    {
        return $query->where('plan', $plan);
    }

    // Accessors
    public function getIsApprovedAttribute()
    {
        return $this->status === 'approved';
    }

    public function getIsPendingAttribute()
    {
        return $this->status === 'pending';
    }

    public function getFormattedAmountAttribute()
    {
        return 'R$ ' . number_format($this->amount, 2, ',', '.');
    }

    public function getPlanNameAttribute()
    {
        return $this->plan === 'basico' ? 'Básico' : 'Premium';
    }
}
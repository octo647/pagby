<?php
// app/Models/PagByPayment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagByPayment extends Model
{
    use HasFactory;
    protected $table = 'pagby_payments';

    protected $fillable = [
        'id',
        'tenant_id',
        'contact_id',        
        'mp_payment_id',
        'asaas_payment_id',
        'external_id',
        'plan',
        'employee_count',
        'status',
        'type',
        'amount',
        'payment_method',
        'mp_data',
        'description',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'mp_data' => 'array',
        'amount' => 'decimal:2'
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function getPlanNameAttribute(): string
    {
        return match($this->plan) {
            'basico' => 'Básico',
            'premium' => 'Premium',
            default => 'Desconhecido'
        };
    }

    public function getStatusNameAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pendente',
            'approved' => 'Aprovado',
            'rejected' => 'Rejeitado',
            'cancelled' => 'Cancelado',
            default => 'Desconhecido'
        };
    }

    public function getPaymentMethodNameAttribute(): string
    {
        return match($this->payment_method) {
            'pix' => 'PIX',
            'credit_card' => 'Cartão de Crédito',
            'debit_card' => 'Cartão de Débito',
            default => 'Outro'
        };
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Pagamentos avulsos via Asaas (TENANT)
 * Modelo SEM split - subconta do salão
 * Cliente paga serviço único (não assinatura)
 */
class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'comanda_id',
        'appointment_id',
        'customer_id',
        'asaas_payment_id',
        'asaas_customer_id',
        'asaas_invoice_url',
        'asaas_invoice_number',
        'amount',
        'net_value',
        'billing_type',
        'description',
        'due_date',
        'payment_date',
        'estimated_credit_date',
        'status',
        'asaas_data',
        'external_reference',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'net_value' => 'decimal:2',
        'due_date' => 'date',
        'payment_date' => 'date',
        'estimated_credit_date' => 'date',
        'paid_at' => 'datetime',
        'asaas_data' => 'array',
    ];

    // Relacionamentos
    public function comanda(): BelongsTo
    {
        return $this->belongsTo(Comanda::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    // Métodos auxiliares
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isReceived(): bool
    {
        return in_array($this->status, ['received', 'received_in_cash']);
    }

    public function isOverdue(): bool
    {
        return $this->status === 'overdue';
    }

    public function markAsReceived(array $additionalData = []): void
    {
        $this->update(array_merge([
            'status' => 'received',
            'payment_date' => now(),
            'paid_at' => now(),
        ], $additionalData));
    }

    public function markAsOverdue(): void
    {
        $this->update(['status' => 'overdue']);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeReceived($query)
    {
        return $query->whereIn('status', ['received', 'received_in_cash']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    // Accessors
    public function getFormattedAmountAttribute(): string
    {
        return 'R$ ' . number_format($this->amount, 2, ',', '.');
    }

    public function getStatusNameAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pendente',
            'confirmed' => 'Confirmado',
            'received' => 'Recebido',
            'received_in_cash' => 'Recebido em Dinheiro',
            'overdue' => 'Vencido',
            'refunded' => 'Estornado',
            'deleted' => 'Deletado',
            'cancelled' => 'Cancelado',
            default => 'Desconhecido'
        };
    }

    public function getBillingTypeNameAttribute(): string
    {
        return match($this->billing_type) {
            'PIX' => 'PIX',
            'BOLETO' => 'Boleto',
            'CREDIT_CARD' => 'Cartão de Crédito',
            'DEBIT_CARD' => 'Cartão de Débito',
            'UNDEFINED' => 'Não Definido',
            default => $this->billing_type
        };
    }
}
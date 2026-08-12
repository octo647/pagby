<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPayment extends Model
{
    use HasFactory;

    protected $table = 'subscriptions_payments';

    protected $fillable = [
        'subscription_id',
        'asaas_payment_id',
        'asaas_invoice_url',
        'amount',
        'net_value',
        'billing_type',
        'due_date',
        'payment_date',
        'status',
        'asaas_data',
        'confirmed_at',
        'received_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'net_value' => 'decimal:2',
        'due_date' => 'date',
        'payment_date' => 'date',
        'confirmed_at' => 'datetime',
        'received_at' => 'datetime',
        'asaas_data' => 'array',
    ];

    // Relacionamentos
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    // Métodos auxiliares
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isReceived(): bool
    {
        return $this->status === 'received';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'overdue';
    }

    public function markAsReceived(): void
    {
        $this->update([
            'status' => 'received',
            'payment_date' => now(),
            'received_at' => now(),
        ]);
    }

    public function markAsOverdue(): void
    {
        $this->update(['status' => 'overdue']);
    }
}

<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    
    use HasDatabase, HasDomains;

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'type',
            'email',
            'phone',
            'instagram',
            'facebook',
            'whatsapp',
            'name',
            'status',
            'address',
            'complement',
            'city',
            'logo',
            'name',
            'cnpj',
            'fantasy_name',
            'slug',
            'neighborhood',
            'cep',
            'state',
            'plan',
            'trial_started_at',
            'trial_ends_at',
            'subscription_status',
            'current_plan',
            'subscription_started_at',
            'subscription_ends_at',
            'is_blocked',
        ];
    }

    protected $casts = [
        'trial_started_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'subscription_started_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'is_blocked' => 'boolean',
    ];

    /**
     * Verifica se está no período de teste
     */
    public function isInTrial(): bool
    {
        return $this->subscription_status === 'trial' && 
               $this->trial_ends_at && 
               $this->trial_ends_at->isFuture();
    }

    /**
     * Verifica se o período de teste expirou
     */
    public function isTrialExpired(): bool
    {
        return $this->subscription_status === 'trial' && 
               $this->trial_ends_at && 
               $this->trial_ends_at->isPast();
    }

    /**
     * Verifica se tem assinatura ativa
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscription_status === 'active' && 
               $this->subscription_ends_at && 
               $this->subscription_ends_at->isFuture();
    }

    /**
     * Verifica se a assinatura expirou
     */
    public function isSubscriptionExpired(): bool
    {
        return $this->subscription_status === 'active' && 
               $this->subscription_ends_at && 
               $this->subscription_ends_at->isPast();
    }

    /**
     * Verifica se o tenant deve ser bloqueado
     */
    public function shouldBeBlocked(): bool
    {
        return $this->is_blocked || 
               $this->isTrialExpired() || 
               $this->isSubscriptionExpired() ||
               $this->subscription_status === 'suspended';
    }

    /**
     * Inicia o período de teste
     */
    public function startTrial(): void
    {
        $this->trial_started_at = now();
        $this->trial_ends_at = now()->addDays(30);
        $this->subscription_status = 'trial';
        $this->is_blocked = false;
        $this->save();
    }

    /**
     * Ativa uma assinatura paga
     */
    public function activateSubscription(string $planName, int $durationDays = 30): void
    {
        $this->current_plan = $planName;
        $this->subscription_started_at = now();
        $this->subscription_ends_at = now()->addDays($durationDays);
        $this->subscription_status = 'active';
        $this->is_blocked = false;
        $this->save();
    }

    /**
     * Bloqueia o tenant
     */
    public function block(): void
    {
        $this->is_blocked = true;
        $this->subscription_status = 'suspended';
        $this->save();
    }

    /**
     * Desbloqueia o tenant
     */
    public function unblock(): void
    {
        $this->is_blocked = false;
        if ($this->hasActiveSubscription()) {
            $this->subscription_status = 'active';
        }
        $this->save();
    }


}

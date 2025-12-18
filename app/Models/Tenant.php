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
            'employee_count',
            'email',
            'phone',
            'whatsapp',
            'instagram',
            'facebook_client_id',
            'facebook_client_secret',
            'google_client_id',
            'google_client_secret',
            'social_login_enabled',            
            'name', 
            'cnpj',
            'fantasy_name',
            'slug', 
            'address',
            'number',
            'complement',
            'neighborhood',
            'cep',
            'city',
            'state',
            'logo',
            'plan',
            'status',            
            'trial_started_at',
            'trial_ends_at',
            'subscription_status',
            'current_plan',
            'subscription_started_at',
            'subscription_ends_at',
            'is_blocked',
            'data',
            'created_at',
            'updated_at',
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
        // Bloqueia se estiver bloqueado manualmente
        if ($this->is_blocked) {
            return true;
        }
        // Bloqueia se o trial expirou e NÃO tem assinatura ativa
        if ($this->isTrialExpired() && !$this->hasActiveSubscription()) {
            return true;
        }
        // Bloqueia se a assinatura paga expirou
        if ($this->isSubscriptionExpired()) {
            return true;
        }
        // Bloqueia se o status está suspenso
        if ($this->subscription_status === 'suspended') {
            return true;
        }
        return false;
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
    public function pagByPayments()
    {
        return $this->hasMany(PagByPayment::class, 'tenant_id', 'id');
    }

    public function latestPagByPayment()
    {
        return $this->hasOne(PagByPayment::class, 'tenant_id', 'id')->latest();
    }

    public function hasApprovedPagByPayments(): bool
    {
        return $this->pagByPayments()->whereIn('status', ['approved', 'authorized'])->exists();
    }
    /**
     * Retorna o nome legível do plano atual
     */
    public function getPlanDisplayName(): string
    {
        return match($this->current_plan) {
            'basico' => 'Básico',
            'premium' => 'Premium',
            default => 'Trial'
        };
    }

    /**
     * Retorna o status legível da assinatura
     */
    public function getSubscriptionStatusDisplay(): string
    {
        if ($this->isInTrial()) {
            return 'Trial ativo até ' . $this->trial_ends_at->format('d/m/Y');
        }
        
        if ($this->hasActiveSubscription()) {
            return 'Assinatura ativa até ' . $this->subscription_ends_at->format('d/m/Y');
        }
        
        if ($this->shouldBeBlocked()) {
            return 'Bloqueado';
        }
        
        return 'Sem assinatura';
    }

    /**
     * Verifica se pode fazer upgrade/downgrade
     */
    public function canChangePlan(): bool
    {
        return $this->hasActiveSubscription() && !$this->shouldBeBlocked();
    }


}

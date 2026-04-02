<?php

namespace App\Models;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    /**
     * Retorna o preço por funcionário considerando promoção do primeiro ano
     */
    public function getCurrentPricePerEmployee(): float
    {
        $basePrice = config('pricing.base_price_per_employee', 60.00);
        return $basePrice;
    }

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'type',
            'template',
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
            'asaas_wallet_id',
            'asaas_account_id',
            'asaas_account_data',
            'asaas_api_key',
            'asaas_account_status',
            'asaas_account_activated_at',
            'subscription_started_at',
            'subscription_ends_at', 
            'is_blocked',
            'onboarding_completed',
            'onboarding_progress',
            'data'
        ];

    }

    protected $fillable = [
        'id',
        'type',
        'template',
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
        'asaas_wallet_id',
        'asaas_account_id',
        'asaas_account_data',
        'asaas_api_key',
        'asaas_account_status',
        'asaas_account_activated_at',
        'subscription_started_at',
        'subscription_ends_at', 
        'is_blocked',
        'onboarding_completed',
        'onboarding_progress',
        'data',
        'crated_at',
        'updated_at',
        
    ];

    protected $hidden = [
        'asaas_api_key', // Nunca expor API key em JSON/APIs
    ];

    protected $casts = [
        'trial_started_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'subscription_started_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'asaas_account_activated_at' => 'datetime',
        'is_blocked' => 'boolean',
        'onboarding_completed' => 'boolean',
        'onboarding_progress' => 'array',
        'asaas_account_data' => 'array',
    ];

    /**
     * Verifica se está no período de trial
     */
    public function isInTrial(): bool
    {
        return $this->subscription_status === 'trial' && 
               $this->trial_ends_at && 
               $this->trial_ends_at->isFuture();
    }

    /**
     * Verifica se o trial expirou
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
        \Log::info('[shouldBeBlocked] Tenant', [
            'id' => $this->id,
            'subscription_status' => $this->subscription_status,
            'trial_ends_at' => $this->trial_ends_at,
            'subscription_ends_at' => $this->subscription_ends_at,
            'is_blocked' => $this->is_blocked,
        ]);
        // Bloqueia se estiver bloqueado manualmente
        if ($this->is_blocked) {
            \Log::info('[shouldBeBlocked] Bloqueado manualmente');
            return true;
        }
        // Bloqueia se trial expirou e não tem assinatura ativa
        if ($this->isTrialExpired() && !$this->hasActiveSubscription()) {
            \Log::info('[shouldBeBlocked] Trial expirado e sem assinatura ativa');
            return true;
        }
        // Bloqueia se a assinatura paga expirou
        if ($this->isSubscriptionExpired()) {
            \Log::info('[shouldBeBlocked] Assinatura paga expirada');
            return true;
        }
        // Bloqueia se o status está suspenso
        if ($this->subscription_status === 'suspended') {
            \Log::info('[shouldBeBlocked] Status suspenso');
            return true;
        }
        \Log::info('[shouldBeBlocked] NÃO bloqueado');
        return false;
    }

    /**
     * Inicia período de trial
     */
    public function startTrial(): void
    {
        $this->trial_started_at = now();
        $this->trial_ends_at = now()->addDays(config('pricing.trial.duration_days', 30));
        $this->subscription_status = 'trial';
        $this->employee_count = 1; // Começa com 1, pode aumentar até 5 no trial
        $this->is_blocked = false;
        $this->save();
    }

    /**
     * Ativa uma assinatura paga
     */
    public function activateSubscription(int $employeeCount, int $durationDays = 30): void
    {
        $this->employee_count = $employeeCount;
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
     * Retorna o preço mensal baseado no número de funcionários
     */
    public function getMonthlyPrice(): float
    {
        // Trial não paga
        if ($this->isInTrial()) {
            return 0;
        }
        $pricePerEmployee = $this->getCurrentPricePerEmployee();
        return $this->employee_count * $pricePerEmployee;
    }

    /**
     * Retorna o status legível da assinatura
     */
    public function getSubscriptionStatusDisplay(): string
    {
        if ($this->isInTrial()) {
            $daysLeft = now()->diffInDays($this->trial_ends_at, false);
            $daysLeft = max(0, ceil($daysLeft)); // Arredondar para cima e não permitir negativo
            return "Trial ativo ({$daysLeft} dias restantes) - até " . $this->trial_ends_at->format('d/m/Y');
        }
        
        if ($this->hasActiveSubscription()) {
            $employees = $this->employee_count;
            $price = $this->getMonthlyPrice();
            return "Plano Ativo: {$employees} funcionário" . ($employees > 1 ? 's' : '') . " - R$ " . number_format($price, 2, ',', '.') . "/mês até " . $this->subscription_ends_at->format('d/m/Y');
        }
        
        if ($this->isTrialExpired()) {
            return 'Trial expirado - Escolha um plano para continuar';
        }
        
        if ($this->shouldBeBlocked()) {
            return 'Bloqueado';
        }
        
        return 'Sem assinatura';
    }

    /**
     * Verifica se pode alterar número de funcionários
     */
    public function canChangeEmployeeCount(): bool
    {
        return !$this->shouldBeBlocked();
    }

    // ============================================================
    // MÉTODOS PARA SUBCONTAS ASAAS (MODELO SEM SPLIT)
    // ============================================================

    /**
     * Obtém API key descriptografada da subconta.
     * 
     * IMPORTANTE: Usar apenas quando necessário processar pagamentos.
     * Nunca expor em APIs ou logs.
     * 
     * @return string|null
     */
    public function getAsaasApiKeyDecryptedAttribute()
    {
        if (!$this->asaas_api_key) {
            return null;
        }

        try {
            return \Illuminate\Support\Facades\Crypt::decryptString($this->asaas_api_key);
        } catch (\Exception $e) {
            \Log::error('[Tenant] Erro ao descriptografar API key', [
                'tenant_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Verifica se a subconta está ativa e pode receber pagamentos.
     * 
     * @return bool
     */
    public function canReceivePayments(): bool
    {
        return $this->asaas_account_status === 'active' 
            && !empty($this->asaas_api_key);
    }

    /**
     * Verifica se precisa criar subconta Asaas.
     * 
     * @return bool
     */
    public function needsAsaasSubaccount(): bool
    {
        return empty($this->asaas_account_id) 
            || empty($this->asaas_api_key);
    }

    /**
     * Verifica se a subconta está pendente de aprovação.
     * 
     * @return bool
     */
    public function isSubaccountPending(): bool
    {
        return $this->asaas_account_status === 'pending';
    }

    /**
     * Verifica se a subconta foi rejeitada.
     * 
     * @return bool
     */
    public function isSubaccountRejected(): bool
    {
        return $this->asaas_account_status === 'rejected';
    }

    /**
     * Ativa a subconta após aprovação do Asaas.
     * 
     * @return void
     */
    public function activateSubaccount(): void
    {
        $this->asaas_account_status = 'active';
        $this->asaas_account_activated_at = now();
        $this->save();

        \Log::info('[Tenant] Subconta ativada', [
            'tenant_id' => $this->id,
            'account_id' => $this->asaas_account_id
        ]);
    }

    /**
     * Retorna status legível da subconta.
     * 
     * @return string
     */
    public function getSubaccountStatusDisplay(): string
    {
        if (!$this->asaas_account_id) {
            return 'Subconta não criada';
        }

        return match($this->asaas_account_status) {
            'pending' => '⏳ Aguardando aprovação Asaas',
            'active' => '✅ Ativa - pode receber pagamentos',
            'rejected' => '❌ Rejeitada pelo Asaas',
            'disabled' => '⛔ Desabilitada',
            default => '❓ Status desconhecido'
        };
    }


}

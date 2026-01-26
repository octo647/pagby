<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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

        // Cancelar assinatura Asaas vigente (se houver)
        $tenant = $this->tenant;
        if ($tenant) {
            $asaasService = app(\App\Services\AsaasService::class);
            $contact = null;
            $pagamento = $tenant->pagByPayments()->whereNotNull('asaas_payment_id')->latest()->first();
            if ($pagamento) {
                $contact = $pagamento->contact;
            }
            // Buscar subscription_id no último pagamento recorrente
            $pagamentoRecorrente = $tenant->pagByPayments()->where('type', 'recorrente')->whereNotNull('external_id')->latest()->first();
            if ($pagamentoRecorrente && $pagamentoRecorrente->external_id) {
                try {
                    $asaasService->cancelarAssinatura($pagamentoRecorrente->external_id);
                } catch (\Exception $e) {
                    Log::error('Erro ao cancelar assinatura Asaas após ajuste de plano', [
                        'tenant_id' => $tenant->id,
                        'subscription_id' => $pagamentoRecorrente->external_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Agendar nova cobrança para o próximo período (mantém lógica anterior)
            $nextDueDate = $tenant->subscription_ends_at ? $tenant->subscription_ends_at->copy()->addDay() : now()->addMonth();
            if ($contact) {
                $customerData = [
                    'name' => $contact->owner_name ?? 'Cliente',
                    'email' => $contact->email,
                    'cpfCnpj' => $contact->cpf ?? '',
                    'phone' => $contact->phone ?? '',
                ];
                $plan = $pagamento ? $pagamento->plan : 'basico';
                $employeeCount = $this->employee_count_after;
                $pricePerEmployee = $tenant->getCurrentPricePerEmployee();
                $value = $employeeCount * $pricePerEmployee;
                $paymentData = [
                    'billingType' => 'UNDEFINED',
                    'value' => $value,
                    'dueDate' => $nextDueDate->format('Y-m-d'),
                    'description' => "Renovação de assinatura PagBy: plano {$plan}, {$employeeCount} funcionário(s)",
                ];
                try {
                    $result = $asaasService->criarCobranca($customerData, $paymentData);
                    if ($result['success']) {
                        \App\Models\PagByPayment::create([
                            'tenant_id' => $tenant->id,
                            'contact_id' => $contact->id,
                            'asaas_payment_id' => $result['data']['id'] ?? null,
                            'plan' => $plan,
                            'employee_count' => $employeeCount,
                            'status' => 'pending',
                            'type' => 'recorrente',
                            'amount' => $value,
                            'payment_method' => 'asaas',
                            'description' => $paymentData['description'],
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Erro ao agendar nova cobrança Asaas após ajuste de plano', [
                        'tenant_id' => $tenant->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }
}

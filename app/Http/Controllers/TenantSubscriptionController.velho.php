<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;

class TenantSubscriptionController extends Controller
{
    /**
     * Mostra a página de planos disponíveis
     */
    public function showPlans()
    {
        $tenant = tenant();
        
        $plans = [
            [
                'name' => 'Básico',
                'price' => 29.90,
                'features' => [
                    'Até 1 profissional',
                    'Agendamento online',
                    'Controle financeiro básico',
                    'Relatórios simples',
                    'Suporte via email'
                ],
                'duration_days' => 30
            ],
            [
                'name' => 'Intermediário',
                'price' => 59.90,
                'features' => [
                    'Até 3 profissionais',
                    'Agendamento online',
                    'Controle financeiro avançado',
                    'Relatórios detalhados',
                    'Gestão de estoque básica',
                    'Suporte via chat'
                ],
                'duration_days' => 30
            ],
            [
                'name' => 'Avançado',
                'price' => 99.90,
                'features' => [
                    'Profissionais ilimitados',
                    'Agendamento online',
                    'Controle financeiro completo',
                    'Relatórios avançados',
                    'Gestão de estoque completa',
                    'Sistema de fidelidade',
                    'Múltiplas filiais',
                    'Suporte prioritário'
                ],
                'duration_days' => 30
            ]
        ];

        return view('tenant.subscription.plans', compact('tenant', 'plans'));
    }

    /**
     * Ativa uma assinatura para o tenant
     */
    public function selectPlan(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:Básico,Intermediário,Avançado'
        ]);

        $tenant = tenant();
        
        // Aqui você integraria com o gateway de pagamento
        // Por enquanto, vamos simular a ativação da assinatura
        
        $durationDays = 30; // Por padrão 30 dias
        
        $tenant->activateSubscription($request->plan, $durationDays);

        return redirect()->route('tenant.subscription.success')
            ->with('success', "Plano {$request->plan} ativado com sucesso!");
    }

    /**
     * Página de sucesso após ativação
     */
    public function success()
    {
        $tenant = tenant();
        return view('tenant.subscription.success', compact('tenant'));
    }

    /**
     * Página mostrada quando o tenant está bloqueado
     */
    public function blocked()
    {
        $tenant = tenant();
        
        $plans = [
            [
                'name' => 'Básico',
                'price' => 29.90,
                'features' => ['Até 1 profissional', 'Funcionalidades básicas', 'Suporte via email']
            ],
            [
                'name' => 'Intermediário', 
                'price' => 59.90,
                'features' => ['Até 3 profissionais', 'Funcionalidades avançadas', 'Suporte via chat']
            ],
            [
                'name' => 'Avançado',
                'price' => 99.90,
                'features' => ['Profissionais ilimitados', 'Todas as funcionalidades', 'Suporte prioritário']
            ]
        ];

        return view('tenant.subscription.blocked', compact('tenant', 'plans'));
    }

    /**
     * Inicia período de teste para novo tenant
     */
    public function startTrial()
    {
        $tenant = tenant();
        
        if (!$tenant->trial_started_at) {
            $tenant->startTrial();
            return redirect()->back()->with('success', 'Período de teste de 30 dias iniciado!');
        }
        
        return redirect()->back()->with('error', 'Período de teste já foi utilizado.');
    }
}

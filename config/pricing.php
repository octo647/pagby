<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Pricing Configuration
    |--------------------------------------------------------------------------
    |
    | Sistema simplificado: R$ 30 por funcionário
    |
    */

    'trial' => [
        'duration_days' => 30,
        'max_employees' => 5, // Durante trial pode testar com até 5 funcionários
        'name' => 'Período de Teste',
        'description' => 'Teste grátis por 30 dias com até 5 funcionários',
    ],

    'base_price_per_employee' => 30.00, // R$ 30 por funcionário

    'features' => [
        'Agendamentos ilimitados',
        'Controle de caixa completo',
        'Relatórios avançados',
        'Múltiplas filiais',
        'Gestão de estoque',
        'Planos de assinatura para clientes',
        'Integração WhatsApp',
        'Suporte prioritário',
        'Dashboard em tempo real',
    ],

    /*
    |--------------------------------------------------------------------------
    | Billing Configuration
    |--------------------------------------------------------------------------
    */

    'billing' => [
        'default_duration_days' => 30,
        'grace_period_days' => 3, // Dias de tolerância após vencimento
        'suspension_days' => 10, // Dias até suspender após vencimento
        'cancellation_days' => 30, // Dias até cancelar após vencimento
    ],

    /*
    |--------------------------------------------------------------------------
    | Marketplace Configuration
    |--------------------------------------------------------------------------
    */

    'marketplace' => [
        'commission_percentage' => 5.0, // 5% de comissão da Pagby
        'tenant_percentage' => 95.0, // 95% para o tenant
    ],

];

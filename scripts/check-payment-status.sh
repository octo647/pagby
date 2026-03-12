#!/bin/bash

# Script para verificar status de pagamento no banco de dados

echo "🔍 Verificando status de pagamentos..."
echo ""

# Verificar se o payment_id foi passado como argumento
if [ -z "$1" ]; then
    echo "📋 Últimos 5 pagamentos criados:"
    php artisan tinker --execute="
        \$payments = \App\Models\TenantsPlansPayment::on('mysql')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get(['id', 'tenant_id', 'plan', 'amount', 'status', 'asaas_subscription_id', 'created_at']);
        
        foreach (\$payments as \$p) {
            echo \"━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\";
            echo \"ID: {\$p->id}\n\";
            echo \"Tenant: {\$p->tenant_id}\n\";
            echo \"Plano: {\$p->plan}\n\";
            echo \"Valor: R$ \" . number_format(\$p->amount, 2, ',', '.') . \"\n\";
            echo \"Status: {\$p->status}\n\";
            echo \"Assinatura Asaas: {\$p->asaas_subscription_id}\n\";
            echo \"Data: {\$p->created_at->format('d/m/Y H:i')}\n\";
        }
    "
else
    PAYMENT_ID=$1
    echo "🔍 Detalhes do Payment ID: $PAYMENT_ID"
    echo ""
    php artisan tinker --execute="
        \$payment = \App\Models\TenantsPlansPayment::on('mysql')->find($PAYMENT_ID);
        
        if (\$payment) {
            echo \"━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\";
            echo \"ID: {\$payment->id}\n\";
            echo \"Tenant: {\$payment->tenant_id}\n\";
            echo \"Plano: {\$payment->plan}\n\";
            echo \"Valor: R$ \" . number_format(\$payment->amount, 2, ',', '.') . \"\n\";
            echo \"Status: {\$payment->status}\n\";
            echo \"Assinatura Asaas: {\$payment->asaas_subscription_id}\n\";
            echo \"Data Criação: {\$payment->created_at->format('d/m/Y H:i:s')}\n\";
            echo \"Última Atualização: {\$payment->updated_at->format('d/m/Y H:i:s')}\n\";
            echo \"━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\";
            
            if (\$payment->asaas_data) {
                echo \"\n📦 Dados Asaas:\n\";
                \$data = json_decode(\$payment->asaas_data, true);
                echo json_encode(\$data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . \"\n\";
            }
            
            // Verificar status do tenant
            echo \"\n🏢 Status do Tenant:\n\";
            \$tenant = \Stancl\Tenancy\Database\Models\Tenant::find(\$payment->tenant_id);
            if (\$tenant) {
                echo \"Subscription Status: {\$tenant->subscription_status}\n\";
                echo \"Bloqueado: \" . (\$tenant->is_blocked ? 'Sim ❌' : 'Não ✅') . \"\n\";
            }
        } else {
            echo \"❌ Payment não encontrado!\n\";
        }
    "
fi

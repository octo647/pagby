#!/bin/bash

###############################################################################
# Script de Migração para Asaas
# Automatiza o processo de migração do MercadoPago para Asaas
###############################################################################

set -e  # Sair em caso de erro

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

echo "╔════════════════════════════════════════════════════╗"
echo "║   Migração do Sistema de Assinaturas → Asaas      ║"
echo "╚════════════════════════════════════════════════════╝"
echo ""

###############################################################################
# Funções auxiliares
###############################################################################

check_env_var() {
    local var_name=$1
    local var_value=$(grep "^${var_name}=" "${PROJECT_ROOT}/.env" 2>/dev/null | cut -d '=' -f2)
    
    if [ -z "$var_value" ] || [ "$var_value" == "your_key_here" ]; then
        return 1
    fi
    return 0
}

prompt_continue() {
    read -p "Continuar? (s/N): " response
    if [[ ! "$response" =~ ^[Ss]$ ]]; then
        echo "❌ Operação cancelada."
        exit 1
    fi
}

###############################################################################
# Passo 1: Verificar ambiente
###############################################################################

echo "📋 Passo 1/6: Verificando ambiente..."
echo ""

# Verificar se está no diretório correto
if [ ! -f "${PROJECT_ROOT}/artisan" ]; then
    echo "❌ Erro: Execute este script da pasta scripts/ do projeto Laravel"
    exit 1
fi

echo "✅ Diretório do projeto localizado"

# Verificar credenciais Asaas
echo ""
echo "🔑 Verificando credenciais Asaas..."

if check_env_var "ASAAS_API_KEY"; then
    echo "✅ ASAAS_API_KEY configurada"
else
    echo "⚠️  ASAAS_API_KEY não configurada"
    echo ""
    read -p "Digite sua API Key do Asaas: " api_key
    echo "ASAAS_API_KEY=${api_key}" >> "${PROJECT_ROOT}/.env"
    echo "✅ API Key adicionada ao .env"
fi

if check_env_var "ASAAS_API_URL"; then
    echo "✅ ASAAS_API_URL configurada"
else
    echo "⚠️  ASAAS_API_URL não configurada"
    echo ""
    echo "Escolha o ambiente:"
    echo "  1) Sandbox (testes)"
    echo "  2) Produção"
    read -p "Opção: " env_option
    
    if [ "$env_option" == "1" ]; then
        api_url="https://sandbox.asaas.com/api/v3"
    else
        api_url="https://www.asaas.com/api/v3"
    fi
    
    echo "ASAAS_API_URL=${api_url}" >> "${PROJECT_ROOT}/.env"
    echo "✅ API URL adicionada ao .env"
fi

echo ""
echo "✅ Ambiente verificado!"
echo ""
prompt_continue

###############################################################################
# Passo 2: Executar Migration
###############################################################################

echo ""
echo "📦 Passo 2/6: Executando migrations..."
echo ""

cd "$PROJECT_ROOT"

php artisan migrate --force

echo ""
echo "✅ Migrations executadas!"
echo ""
prompt_continue

###############################################################################
# Passo 3: Limpar cache
###############################################################################

echo ""
echo "🧹 Passo 3/6: Limpando cache..."
echo ""

php artisan config:clear
php artisan cache:clear
php artisan route:clear

echo ""
echo "✅ Cache limpo!"
echo ""
prompt_continue

###############################################################################
# Passo 4: Criar subcontas Asaas
###############################################################################

echo ""
echo "💳 Passo 4/6: Criando subcontas Asaas para tenants..."
echo ""

# Contar tenants
tenant_count=$(php artisan tinker --execute="echo App\Models\Tenant::on('mysql')->count();" 2>/dev/null || echo "0")

echo "📊 Total de tenants encontrados: ${tenant_count}"
echo ""

if [ "$tenant_count" -gt 0 ]; then
    echo "⚠️  Este processo criará subcontas no Asaas para todos os tenants."
    echo "   Certifique-se de que os tenants possuem:"
    echo "   • owner_email"
    echo "   • owner_cpf_cnpj (ou será solicitado)"
    echo "   • owner_phone (ou será solicitado)"
    echo ""
    prompt_continue
    
    php artisan tenants:create-asaas-accounts
    
    echo ""
    echo "✅ Subcontas criadas!"
else
    echo "⚠️  Nenhum tenant encontrado. Pulando criação de subcontas."
fi

echo ""
prompt_continue

###############################################################################
# Passo 5: Atualizar rotas
###############################################################################

echo ""
echo "🛣️  Passo 5/6: Configurando rotas..."
echo ""

WEB_ROUTES="${PROJECT_ROOT}/routes/web.php"

if grep -q "require __DIR__.'/asaas.php';" "$WEB_ROUTES"; then
    echo "✅ Rotas Asaas já incluídas em routes/web.php"
else
    echo "⚠️  Rotas Asaas não encontradas em routes/web.php"
    echo ""
    echo "Deseja adicionar automaticamente? (recomendado)"
    prompt_continue
    
    echo "" >> "$WEB_ROUTES"
    echo "// Rotas Asaas - Sistema de assinaturas com split" >> "$WEB_ROUTES"
    echo "require __DIR__.'/asaas.php';" >> "$WEB_ROUTES"
    
    echo "✅ Rotas adicionadas!"
fi

echo ""
prompt_continue

###############################################################################
# Passo 6: Verificações finais
###############################################################################

echo ""
echo "🔍 Passo 6/6: Verificações finais..."
echo ""

# Verificar se campos foram adicionados
echo "Verificando estrutura do banco..."

has_wallet=$(php artisan tinker --execute="
use Illuminate\Support\Facades\Schema;
echo Schema::connection('mysql')->hasColumn('tenants', 'asaas_wallet_id') ? 'yes' : 'no';
" 2>/dev/null)

has_subscription=$(php artisan tinker --execute="
use Illuminate\Support\Facades\Schema;
echo Schema::connection('mysql')->hasColumn('tenants_plans_payments', 'asaas_subscription_id') ? 'yes' : 'no';
" 2>/dev/null)

if [ "$has_wallet" == "yes" ]; then
    echo "✅ Campo asaas_wallet_id existe em tenants"
else
    echo "❌ Campo asaas_wallet_id NÃO existe em tenants"
fi

if [ "$has_subscription" == "yes" ]; then
    echo "✅ Campo asaas_subscription_id existe em tenants_plans_payments"
else
    echo "❌ Campo asaas_subscription_id NÃO existe em tenants_plans_payments"
fi

# Contar subcontas criadas
wallets_count=$(php artisan tinker --execute="
echo App\Models\Tenant::on('mysql')->whereNotNull('asaas_wallet_id')->count();
" 2>/dev/null || echo "0")

echo ""
echo "📊 Subcontas Asaas criadas: ${wallets_count}/${tenant_count}"

###############################################################################
# Resumo final
###############################################################################

echo ""
echo "╔════════════════════════════════════════════════════╗"
echo "║              Migração Concluída! ✅                ║"
echo "╚════════════════════════════════════════════════════╝"
echo ""
echo "📋 Próximos Passos:"
echo ""
echo "1. Configurar webhook no painel Asaas:"
echo "   URL: https://seu-dominio.com.br/asaas-assinatura/webhook"
echo ""
echo "2. Testar criação de assinatura:"
echo "   • Acessar página de planos em um tenant"
echo "   • Selecionar plano"
echo "   • Verificar se redireciona corretamente"
echo ""
echo "3. Monitorar logs:"
echo "   tail -f storage/logs/laravel.log | grep -i asaas"
echo ""
echo "4. Ler documentação completa:"
echo "   • MIGRACAO_ASAAS.md - Guia completo"
echo "   • ASAAS_QUICKSTART.md - Início rápido"
echo "   • COMPARACAO_MERCADOPAGO_ASAAS.md - Análise comparativa"
echo ""
echo "5. Decisão sobre assinaturas existentes:"
echo "   • Manter no MercadoPago (recomendado inicialmente)"
echo "   • OU migrar para Asaas (requer cancelamento/recriação)"
echo ""
echo "📞 Suporte Asaas: suporte@asaas.com | (11) 4950-2209"
echo ""
echo "✅ Sistema pronto para uso!"
echo ""

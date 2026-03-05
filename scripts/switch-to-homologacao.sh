#!/bin/bash
# Script para trocar o .env do VPS para usar chave de HOMOLOGAÇÃO

echo "🔧 Atualizando .env no VPS para HOMOLOGAÇÃO..."

REMOTE_HOST="helder@69.6.222.77"
REMOTE_PATH="/var/www/pagby"

# Conectar e atualizar via SSH
ssh -p 22022 $REMOTE_HOST << 'ENDSSH'
cd /var/www/pagby

# Backup do .env atual
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)

# Atualizar chave para homologação
sed -i 's/ASAAS_API_KEY=\$aact_prod_/ASAAS_API_KEY=$aact_hmlg_/' .env

# Verificar se funcionou
echo ""
echo "✅ .env atualizado! Verificando..."
grep "ASAAS_API" .env | head -2

echo ""
echo "🧹 Limpando cache..."
php artisan config:clear
php artisan cache:clear

echo ""
echo "✅ Pronto! Agora pode executar o teste:"
echo "   php artisan asaas:test-subaccount-invoice --save-evidence"
ENDSSH

echo ""
echo "✅ Configuração atualizada no VPS!"

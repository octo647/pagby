#!/bin/bash

# Script para sincronizar WhatsApp Bot do Pagby com o VPS
# Sincroniza apenas a pasta scripts/whatsapp-bot

cd "$(dirname "$0")/.."

echo "🤖 Sincronizando WhatsApp Bot Pagby com VPS"
echo "==========================================="
echo ""

# Configurações do servidor
REMOTE_HOST="helder@69.6.222.77"
REMOTE_PATH="/var/www/pagby/"
SSH_PORT="22022"

# Verifica se a pasta existe
if [ ! -d "scripts/whatsapp-bot" ]; then
    echo "❌ Pasta scripts/whatsapp-bot não encontrada!"
    exit 1
fi

echo "📤 Sincronizando pasta whatsapp-bot..."
echo ""

# Sincroniza a pasta do bot
rsync -avz --no-perms --progress -e "ssh -p $SSH_PORT" \
    --exclude='node_modules/' \
    --exclude='auth_info_baileys/' \
    --exclude='leads.json' \
    --exclude='.gitignore' \
    scripts/whatsapp-bot/ $REMOTE_HOST:${REMOTE_PATH}scripts/whatsapp-bot/

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Sincronização concluída!"
    echo ""
    echo "📋 Próximos passos no VPS:"
    echo "  ssh -p $SSH_PORT $REMOTE_HOST"
    echo "  cd ${REMOTE_PATH}scripts/whatsapp-bot"
    echo "  npm install"
    echo "  ./start-pagby-bot.sh"
    echo ""
    echo "💡 Dica: O bot precisa ser reiniciado no VPS se já estiver rodando"
    echo "  pm2 restart pagby-whatsapp-bot"
    echo ""
else
    echo ""
    echo "❌ Erro na sincronização!"
    exit 1
fi

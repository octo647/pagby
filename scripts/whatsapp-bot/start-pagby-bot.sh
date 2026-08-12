#!/bin/bash

# Script para iniciar o WhatsApp Bot do Pagby
# Usa PM2 para manter o bot rodando 24/7

cd "$(dirname "$0")"

BOT_NAME="pagby-whatsapp-bot"
BOT_SCRIPT="index.js"

echo "🚀 Iniciando WhatsApp Bot Pagby"
echo "================================"
echo ""

# Verifica se node_modules existe
if [ ! -d "node_modules" ]; then
    echo "📦 Instalando dependências..."
    npm install
    echo ""
fi

# Verifica se PM2 está instalado
if ! command -v pm2 &> /dev/null; then
    echo "⚠️  PM2 não encontrado!"
    echo "Instale com: npm install -g pm2"
    echo ""
    echo "Por enquanto, iniciando em modo normal..."
    echo ""
    node $BOT_SCRIPT
    exit 0
fi

# Para o bot se já estiver rodando
echo "🔍 Verificando se o bot já está rodando..."
if pm2 describe $BOT_NAME &> /dev/null; then
    echo "⚠️  Bot já está rodando. Reiniciando..."
    pm2 restart $BOT_NAME
else
    echo "▶️  Iniciando bot pela primeira vez..."
    pm2 start $BOT_SCRIPT --name $BOT_NAME
fi

echo ""
echo "✅ Bot iniciado!"
echo ""
echo "📱 Novo número Pagby: 32 998621569"
echo ""
echo "📋 Comandos úteis:"
echo "  pm2 logs $BOT_NAME        - Ver logs do bot"
echo "  pm2 monit                 - Monitorar em tempo real"
echo "  pm2 restart $BOT_NAME     - Reiniciar bot"
echo "  pm2 stop $BOT_NAME        - Parar bot"
echo "  pm2 delete $BOT_NAME      - Remover bot do PM2"
echo ""
echo "💡 Dica: Use 'pm2 logs $BOT_NAME' para ver o QR code se for primeira vez"
echo ""

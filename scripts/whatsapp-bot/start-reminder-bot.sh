#!/bin/bash

# Script para iniciar o bot de lembretes do WhatsApp
# Usage: ./start-reminder-bot.sh

cd "$(dirname "$0")"

echo "🚀 Iniciando PagBy Reminder Bot..."

# Verifica se node_modules existe
if [ ! -d "node_modules" ]; then
    echo "📦 Instalando dependências..."
    npm install
fi

# Inicia o bot
echo "🤖 Bot iniciando..."
node reminder-bot.js

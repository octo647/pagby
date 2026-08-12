#!/bin/bash

# Script para resetar autenticação do WhatsApp Bot Pagby
# Use quando quiser conectar um novo número de WhatsApp

cd "$(dirname "$0")"

echo "🔄 Reset de Autenticação - WhatsApp Bot Pagby"
echo "=============================================="
echo ""
echo "⚠️  ATENÇÃO: Este script irá remover a autenticação atual!"
echo "Você precisará escanear um novo QR code para reconectar."
echo ""
read -p "Deseja continuar? (s/N): " confirm

if [[ ! "$confirm" =~ ^[sS]$ ]]; then
    echo "❌ Operação cancelada."
    exit 0
fi

# Remove autenticação antiga
if [ -d "auth_info_baileys" ]; then
    echo "🗑️  Removendo autenticação antiga..."
    rm -rf auth_info_baileys
    echo "✅ Autenticação removida!"
else
    echo "ℹ️  Nenhuma autenticação anterior encontrada."
fi

# Limpa arquivo de leads (opcional)
read -p "Deseja também limpar o arquivo de leads? (s/N): " clear_leads

if [[ "$clear_leads" =~ ^[sS]$ ]]; then
    if [ -f "leads.json" ]; then
        echo "🗑️  Removendo leads.json..."
        rm -f leads.json
        echo "✅ Arquivo de leads removido!"
    fi
fi

echo ""
echo "✅ Reset concluído!"
echo ""
echo "📱 Novo número do Pagby: 32 998621569 (5532998621569)"
echo ""
echo "Próximos passos:"
echo "1. Execute: npm start"
echo "2. Escaneie o QR code com o WhatsApp do novo número"
echo "3. Aguarde a mensagem '✅ Bot conectado ao WhatsApp!'"
echo ""

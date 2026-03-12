#!/bin/bash

# Script para verificar logs de webhook do Asaas

echo "🔍 Verificando logs de webhook na produção..."
echo ""
echo "🔻 Últimas 50 linhas de logs de webhook:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Comando para ver logs (ajuste o caminho se necessário)
tail -50 storage/logs/laravel.log | grep -A 10 "Webhook Asaas"


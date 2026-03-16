#!/bin/bash

# Script para testar se o template home é copiado (não symlink)
# após criar um tenant

echo "🧪 Teste: Verificar se home.blade.php é cópia e não symlink"
echo "============================================================"
echo ""

# Busca o primeiro tenant criado
TENANT_DIR=$(ls -d resources/views/tenants/*/ 2>/dev/null | head -n 1)

if [ -z "$TENANT_DIR" ]; then
    echo "❌ Nenhum tenant encontrado em resources/views/tenants/"
    echo "   Crie um tenant primeiro através do painel Admin"
    exit 1
fi

TENANT_ID=$(basename "$TENANT_DIR")
HOME_FILE="resources/views/tenants/$TENANT_ID/home.blade.php"

echo "📁 Tenant encontrado: $TENANT_ID"
echo "📄 Arquivo: $HOME_FILE"
echo ""

if [ ! -e "$HOME_FILE" ]; then
    echo "❌ Arquivo home.blade.php não existe!"
    exit 1
fi

if [ -L "$HOME_FILE" ]; then
    echo "❌ ERRO: Arquivo é um SYMLINK!"
    echo "   Link para: $(readlink -f "$HOME_FILE")"
    echo ""
    echo "   ⚠️  Tenants criados com symlink não podem ser editados individualmente."
    echo "   Execute: rm '$HOME_FILE' e recrie o tenant."
    exit 1
else
    echo "✅ Arquivo é uma CÓPIA (não symlink)"
    echo "   O tenant pode editar sua home individualmente!"
    echo ""
    
    # Verifica tamanho do arquivo
    FILE_SIZE=$(stat -f%z "$HOME_FILE" 2>/dev/null || stat -c%s "$HOME_FILE" 2>/dev/null)
    echo "📊 Tamanho: $FILE_SIZE bytes"
    
    # Verifica permissões
    PERMS=$(ls -l "$HOME_FILE" | awk '{print $1}')
    echo "🔒 Permissões: $PERMS"
    
    # Verifica se é editável
    if [ -w "$HOME_FILE" ]; then
        echo "✏️  Arquivo é EDITÁVEL"
    else
        echo "⚠️  Arquivo NÃO é editável (problema de permissões)"
    fi
    
    echo ""
    echo "✅ Teste PASSOU: Template foi copiado corretamente!"
fi

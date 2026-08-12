#!/bin/bash

# Script para renomear diretórios de tenants adicionando prefixo "tenant"
# Este script deve ser executado no servidor de produção

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

STORAGE_PATH="/var/www/pagby/storage"

echo -e "${YELLOW}=== Script de Renomeação de Diretórios de Tenants ===${NC}"
echo ""
echo "Este script irá renomear diretórios no formato '{slug}' para 'tenant{slug}'"
echo "Diretório alvo: $STORAGE_PATH"
echo ""

# Verifica se o diretório existe
if [ ! -d "$STORAGE_PATH" ]; then
    echo -e "${RED}Erro: Diretório $STORAGE_PATH não encontrado!${NC}"
    exit 1
fi

# Lista de diretórios a ignorar (não são tenants)
IGNORE_DIRS=("app" "framework" "logs" ".")

# Função para verificar se um diretório deve ser ignorado
should_ignore() {
    local dir_name="$1"
    for ignore in "${IGNORE_DIRS[@]}"; do
        if [ "$dir_name" = "$ignore" ]; then
            return 0
        fi
    done
    return 1
}

# Conta quantos diretórios serão renomeados
count=0
for dir in "$STORAGE_PATH"/*; do
    if [ -d "$dir" ]; then
        dir_name=$(basename "$dir")
        
        # Ignora diretórios que já começam com "tenant"
        if [[ $dir_name == tenant* ]]; then
            continue
        fi
        
        # Ignora diretórios do sistema
        if should_ignore "$dir_name"; then
            continue
        fi
        
        ((count++))
    fi
done

echo -e "Foram encontrados ${GREEN}$count${NC} diretórios para renomear."
echo ""

if [ $count -eq 0 ]; then
    echo "Nenhum diretório para renomear. Saindo..."
    exit 0
fi

# Confirma com o usuário
read -p "Deseja continuar? (s/N): " confirm
if [[ ! $confirm =~ ^[Ss]$ ]]; then
    echo "Operação cancelada."
    exit 0
fi

echo ""
echo -e "${YELLOW}Iniciando renomeação...${NC}"
echo ""

# Renomeia os diretórios
renamed=0
failed=0

for dir in "$STORAGE_PATH"/*; do
    if [ -d "$dir" ]; then
        dir_name=$(basename "$dir")
        
        # Ignora diretórios que já começam com "tenant"
        if [[ $dir_name == tenant* ]]; then
            continue
        fi
        
        # Ignora diretórios do sistema
        if should_ignore "$dir_name"; then
            continue
        fi
        
        new_name="tenant$dir_name"
        new_path="$STORAGE_PATH/$new_name"
        
        # Verifica se o novo nome já existe
        if [ -e "$new_path" ]; then
            echo -e "${RED}✗ Pulado: $dir_name (destino já existe)${NC}"
            ((failed++))
            continue
        fi
        
        # Tenta renomear
        if mv "$dir" "$new_path"; then
            echo -e "${GREEN}✓ Renomeado: $dir_name → $new_name${NC}"
            ((renamed++))
        else
            echo -e "${RED}✗ Falha ao renomear: $dir_name${NC}"
            ((failed++))
        fi
    fi
done

echo ""
echo -e "${YELLOW}=== Resumo ===${NC}"
echo -e "Total renomeados: ${GREEN}$renamed${NC}"
if [ $failed -gt 0 ]; then
    echo -e "Total com falha: ${RED}$failed${NC}"
fi
echo ""
echo -e "${YELLOW}IMPORTANTE:${NC} Execute os seguintes comandos no servidor:"
echo "  cd /var/www/pagby"
echo "  php artisan config:cache"
echo "  php artisan cache:clear"
echo ""

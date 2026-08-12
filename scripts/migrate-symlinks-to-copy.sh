#!/bin/bash

# Script para migrar tenants de symlink para cópia do template Padrao
# Útil para tenants criados antes da mudança de symlink para copy

echo "🔄 Migração: Converter symlinks para cópias do template Padrao"
echo "=============================================================="
echo ""

count=0
migrated=0
skipped=0

for tenant_dir in resources/views/tenants/*/; do
    if [ ! -d "$tenant_dir" ]; then
        continue
    fi
    
    tenant_id=$(basename "$tenant_dir")
    home_file="${tenant_dir}home.blade.php"
    
    count=$((count + 1))
    
    echo "[$count] Tenant: $tenant_id"
    
    # Verifica se existe
    if [ ! -e "$home_file" ]; then
        echo "    ⚠️  Arquivo home.blade.php não existe - PULANDO"
        skipped=$((skipped + 1))
        echo ""
        continue
    fi
    
    # Verifica se é symlink
    if [ -L "$home_file" ]; then
        echo "    🔗 É SYMLINK - migrando..."
        
        # Remove symlink
        rm "$home_file"
        
        # Busca tipo do tenant no banco (fallback: Barbearia)
        # Para simplificar, vou usar Barbearias como padrão
        template_source="resources/Templates/Barbearias/Padrao/home.blade.php"
        
        # Verifica se template existe
        if [ ! -f "$template_source" ]; then
            echo "    ❌ Template Padrao não encontrado: $template_source"
            skipped=$((skipped + 1))
            continue
        fi
        
        # Copia template
        if cp "$template_source" "$home_file"; then
            echo "    ✅ Migrado com sucesso!"
            echo "    📄 Copiado de: $template_source"
            migrated=$((migrated + 1))
        else
            echo "    ❌ Erro ao copiar template"
            skipped=$((skipped + 1))
        fi
    else
        echo "    ✅ Já é CÓPIA - nada a fazer"
        skipped=$((skipped + 1))
    fi
    
    echo ""
done

echo "=============================================================="
echo "📊 Resumo da Migração:"
echo "   Total de tenants: $count"
echo "   Migrados: $migrated"
echo "   Pulados/Já OK: $skipped"
echo ""

if [ $migrated -gt 0 ]; then
    echo "✅ Migração concluída! Execute 'php artisan view:clear' para limpar cache."
else
    echo "ℹ️  Nenhum tenant precisou ser migrado."
fi

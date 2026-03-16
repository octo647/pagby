#!/bin/bash

# Deploy rápido dos arquivos de customização da home

echo "🚀 Deploy: Sistema de Customização da Home"
echo ""

# Sincronizar arquivos
echo "📤 Sincronizando arquivos modificados..."
rsync -avz --no-perms -e 'ssh -p 22022' \
    app/Livewire/Proprietario/CustomizarHome.php \
    app/Console/Commands/ConvertPadraoSymlinksToFiles.php \
    resources/views/livewire/proprietario/customizar-home.blade.php \
    resources/views/layouts/navigation-back.blade.php \
    resources/views/dashboard.blade.php \
    helder@69.6.222.77:/var/www/pagby/

echo ""
echo "✅ Arquivos sincronizados!"
echo ""
echo "🔄 Agora execute no VPS:"
echo "  ssh -p 22022 helder@69.6.222.77"
echo "  cd /var/www/pagby"
echo "  php artisan view:clear"
echo "  php artisan config:clear"
echo ""

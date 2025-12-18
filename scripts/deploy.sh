#!/bin/bash
echo "🚀 Deploying to HostGator..."

# Verifica se estamos usando configuração local
if grep -q "APP_ENV=local" .env; then
    echo "✅ Usando configuração local, okay para deploy"
else
    echo "⚠️  ATENÇÃO: .env não está em modo local!"
    read -p "Continuar? (y/N): " confirm
    if [[ ! $confirm =~ ^[Yy]$ ]]; then
        exit 1
    fi
fi

# Compila assets
echo "📦 Compilando assets..."
npm run build

# Configurações do rsync
REMOTE_HOST="helder@69.6.222.77"
REMOTE_PATH="/var/www/pagby/"


# Sincronizar projeto completo (sem sobrescrever/apagar tenants do servidor)
echo "📤 Sincronizando projeto com HostGator (rsync)..."
rsync -avz --no-perms --progress --delete-after -e 'ssh -p 22022' \
    --exclude='.git/' \
    --exclude='node_modules/' \
    --exclude='vendor/' \
    --exclude='storage/' \
    --exclude='bootstrap/cache/*' \
    --exclude='.env*' \
    --exclude='config/tenancy.php' \
    --exclude='resources/views/tenants/' \
    --exclude='public/tenants/' \
    --exclude='public/images/tenants/' \
    ./ $REMOTE_HOST:$REMOTE_PATH

# Enviar DIRETAMENTE o .env.production como .env
echo "📄 Enviando configuração de produção..."
rsync -avz --no-perms -e 'ssh -p 22022' .env.production $REMOTE_HOST:$REMOTE_PATH/.env


# Comandos no servidor via SSH

echo "🔧 Executando comandos no servidor..."
ssh -p 22022 helder@69.6.222.77 << 'ENDSSH'
cd /var/www/pagby/

# Instala dependências de produção (evita problemas com Collision)
composer install --no-dev --optimize-autoloader

# Pequeno delay para garantir gravação do .env
sleep 2

# Limpa todos os caches do Laravel
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Recria o cache de configuração
php artisan config:cache
php artisan config:clear
echo "Permissões e dependências ajustadas."
ENDSSH

echo "✅ Instalação bem sucedida!"
echo "🔍 Verifique: https://pagby.com.br"

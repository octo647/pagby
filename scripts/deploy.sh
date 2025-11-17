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
REMOTE_HOST="helde663@pagby.com.br"
REMOTE_PATH="/home4/helde663/pagby/"

# Sincronizar projeto completo (excluindo arquivos desnecessários)
echo "📤 Sincronizando projeto com HostGator (rsync)..."
rsync -avz --progress --delete-after -e 'ssh -p 2222' \
    --exclude='.git/' \
    --exclude='node_modules/' \
    --exclude='vendor/' \
    --exclude='storage/logs/' \
    --exclude='storage/framework/cache/' \
    --exclude='storage/framework/sessions/' \
    --exclude='storage/framework/views/' \
    --exclude='.env*' \
    --exclude='config/tenancy.php' \
    ./ $REMOTE_HOST:$REMOTE_PATH

# Enviar DIRETAMENTE o .env.production como .env
echo "📄 Enviando configuração de produção..."
rsync -avz -e 'ssh -p 2222' .env.production $REMOTE_HOST:$REMOTE_PATH/.env

# Comandos no servidor via SSH
echo "🔧 Executando comandos no servidor..."
ssh -p 2222 helde663@pagby.com.br << 'ENDSSH'
cd /home4/helde663/pagby
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 755 public/images/
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
ENDSSH

echo "✅ Deploy completed successfully!"
echo "🔍 Verifique: https://pagby.com.br"
echo "📊 Próximos deploys serão muito mais rápidos!"
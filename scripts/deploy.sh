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

# Backup do .env local e substitui pelo de produção
echo "💾 Substituindo .env local pelo de produção..."
cp .env .env.local.backup
cp .env_production .env

# Configurações do rsync
REMOTE_HOST="helder@69.6.222.77"
REMOTE_PATH="/var/www/pagby/"

# Sincronizar projeto (SEM --delete para não apagar arquivos do servidor)
echo "📤 Sincronizando projeto com HostGator (rsync)..."
rsync -avz --no-perms --progress -e 'ssh -p 22022' \
    --exclude='.git/' \
    --exclude='node_modules/' \
    --exclude='vendor/' \
    --exclude='storage/' \
    --exclude='bootstrap/cache/*' \
    --exclude='.env.local.backup' \
    --exclude='.env_production' \
    --exclude='.env.example' \
    --exclude='.env.backup*' \
    --exclude='.env_backup*' \
    --exclude='resources/views/tenants/' \
    --exclude='public/tenants/' \
    --exclude='public/images/tenants/' \
    --exclude='scripts/whatsapp-bot/' \
    ./ $REMOTE_HOST:$REMOTE_PATH

# Restaurar .env local
echo "🔄 Restaurando .env local..."
mv .env.local.backup .env

# Comandos no servidor via SSH
echo "🔧 Executando comandos no servidor..."
ssh -p 22022 helder@69.6.222.77 << 'ENDSSH'
cd /var/www/pagby/

# Remove arquivo hot do Vite (modo dev)
rm -f public/hot

# Garantir que diretórios de cache existam com permissões corretas
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p bootstrap/cache
chmod -R 777 storage bootstrap/cache

# Criar e ajustar permissões dos diretórios de tenant para permitir criação pelo Apache
mkdir -p public/tenants public/storage public/images/tenants resources/views/tenants
chmod 775 public/tenants 2>/dev/null || true
chmod 775 public/storage 2>/dev/null || true
chmod 775 public/images/tenants 2>/dev/null || true
chmod 775 resources/views/tenants 2>/dev/null || true

# Instala/atualiza dependências (sem remover vendor para evitar downtime)
composer install --no-dev --optimize-autoloader --no-scripts 2>&1 | grep -v "Please provide a valid cache path" || true

# Regenerar autoload OTIMIZADO (crítico para performance e evitar erros)
composer dump-autoload -o

# Limpar TODOS os caches do Laravel
php artisan optimize:clear 2>/dev/null || true

# Recria caches otimizados
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

# Limpar OPCache (crítico para recarregar classes alteradas)
php -r "if (function_exists('opcache_reset')) { opcache_reset(); echo '✓ OPCache limpo\n'; } else { echo '✗ OPCache não disponível\n'; }"

echo "✅ Deploy concluído com sucesso!"
ENDSSH

# Verificar se o site está respondendo
echo "🔍 Verificando se o site está online..."
if curl -sf -o /dev/null https://pagby.com.br; then
    echo "✅ Site está online e respondendo!"
else
    echo "⚠️  AVISO: Site pode não estar respondendo corretamente"
    echo "🔍 Verifique manualmente: https://pagby.com.br"
fi

echo "✅ Deploy finalizado!"
echo "📊 Próximos passos:"
echo "   - Acesse: https://pagby.com.br"
echo "   - Verifique logs: ssh -p 22022 helder@69.6.222.77 'tail -f /var/www/pagby/storage/logs/laravel.log'"

# 🚀 Modo de Produção - Pagby

**Data:** 16/03/2026  
**Branch:** `modo-producao`  
**Status:** ✅ CONFIGURADO

---

## 📋 Mudanças Aplicadas

### Local (.env)
```env
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=warning
LOG_STACK=daily
LOG_DAILY_DAYS=7
```

### VPS (.env_production)
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://pagby.com.br
LOG_LEVEL=warning
LOG_STACK=daily
LOG_DAILY_DAYS=14
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
CACHE_STORE=file
CACHE_PREFIX=pagby_
```

---

## 🎯 Otimizações de Produção

### 1. Logging
- ✅ **Nível:** `warning` (ao invés de `debug`)
- ✅ **Driver:** `daily` (rotação automática de logs)
- ✅ **Retenção:** 14 dias no VPS, 7 dias local

### 2. Cache
- ✅ **Driver:** `file` (melhor performance que database)
- ✅ **Prefixo:** `pagby_` (evita conflitos)
- ✅ **Config cacheado:** `config:cache` executado

### 3. Segurança
- ✅ **Debug:** desabilitado (não expõe informações sensíveis)
- ✅ **HTTPS:** URLs com `https://`
- ✅ **Cookies seguros:** `SESSION_SECURE_COOKIE=true`
- ✅ **SameSite:** `lax` (proteção CSRF)

### 4. Performance
- ✅ Views compiladas: `view:cache`
- ✅ Configurações cacheadas: `config:cache`
- ✅ Autoload otimizado: `composer dump-autoload -o`

---

## 🚀 Deploy para VPS

### Passo 1: Commitar mudanças localmente

```bash
git add .
git commit -m "Implementa modo de produção com otimizações"
git push origin modo-producao
```

### Passo 2: Executar Deploy

```bash
./scripts/deploy.sh
```

**O script deploy.sh automaticamente:**
1. ✅ Compila assets (`npm run build`)
2. ✅ Sincroniza código via rsync
3. ✅ **NÃO sobrescreve `.env`** no servidor
4. ✅ Instala dependências: `composer install --no-dev --optimize-autoloader`
5. ✅ Limpa caches: `optimize:clear`
6. ✅ Recria caches otimizados: `config:cache`, `route:cache`, `view:cache`
7. ✅ Limpa OPCache

### Passo 3: Verificar no VPS

```bash
# Conectar via SSH
ssh -p 22022 helder@69.6.222.77

# Navegar para o diretório
cd /var/www/pagby

# Verificar configurações
php artisan config:show app.env    # Deve mostrar: production
php artisan config:show app.debug  # Deve mostrar: false

# Verificar logs
tail -f storage/logs/laravel-$(date +%Y-%m-%d).log
```

---

## 📊 Comandos Úteis de Produção

### Limpar TODOS os caches
```bash
php artisan optimize:clear
```

### Recriar caches otimizados
```bash
php artisan config:cache
php artisan view:cache
php artisan route:cache  # ⚠️  Tem conflito de rotas atualmente
```

### Verificar status do sistema
```bash
php artisan about
```

### Monitorar logs em tempo real
```bash
# Local
tail -f storage/logs/laravel-$(date +%Y-%m-%d).log

# VPS
ssh -p 22022 helder@69.6.222.77 'tail -f /var/www/pagby/storage/logs/laravel-$(date +%Y-%m-%d).log'
```

---

## ⚠️  Problemas Conhecidos

### 1. Route Cache - Conflito de nomes
```
LogicException: Unable to prepare route [/] for serialization. 
Another route has already been assigned name [home].
```

**Status:** Não crítico  
**Impacto:** Cache de rotas desabilitado  
**Solução futura:** Renomear uma das rotas 'home' (web.php ou tenant.php)

---

## 🔒 Segurança em Produção

### Variáveis Críticas (.env)
- ✅ `APP_KEY`: Única e segura
- ✅ `DB_PASSWORD`: Senha forte
- ✅ `ASAAS_API_KEY`: Chave de produção (quando ativar)
- ❌ **NÃO versionar `.env`** no Git

### Headers de Segurança (futuro)
Adicionar no `.htaccess` ou configuração do servidor:
```apache
Header always set X-Frame-Options "SAMEORIGIN"
Header always set X-Content-Type-Options "nosniff"
Header always set X-XSS-Protection "1; mode=block"
Header always set Referrer-Policy "strict-origin-when-cross-origin"
```

---

## 📈 Monitoramento

### Logs a Observar
- `storage/logs/laravel-YYYY-MM-DD.log`
- Níveis: `error`, `warning` (críticos em produção)

### Métricas Importantes
- Taxa de erro HTTP 500
- Tempo de resposta das páginas
- Uso de memória/CPU
- Tamanho dos logs

---

## 🔄 Rollback Rápido

Se algo der errado após deploy:

```bash
# Conectar ao VPS
ssh -p 22022 helder@69.6.222.77
cd /var/www/pagby

# Limpar todos os caches
php artisan optimize:clear

# Verificar .env
cat .env | grep APP_ENV

# Restaurar backup se necessário
# cp .env.backup .env

# Recriar caches
php artisan config:cache
php artisan view:cache
```

---

## ✅ Checklist Pré-Deploy

- [x] `.env_production` configurado com valores corretos
- [x] `APP_DEBUG=false`
- [x] `LOG_LEVEL=warning`
- [x] Assets compilados (`npm run build`)
- [x] Código commitado e pushed
- [ ] Backup do banco de dados feito
- [ ] Teste em ambiente de staging (se disponível)
- [ ] Avisar usuários sobre manutenção (se necessário)

---

## 🎯 Próximos Passos

1. **Corrigir conflito de rotas** para habilitar `route:cache`
2. **Implementar monitoramento** (Laravel Telescope ou similar)
3. **Configurar alerts** para erros críticos
4. **Backup automático** do banco de dados
5. **CDN** para assets estáticos
6. **Redis** para cache (performance adicional)

---

**Desenvolvido para Pagby SaaS**  
*Última atualização: 16/03/2026*

# Deploy do Sistema de Customização da Home

## 🚀 Passos para Deploy no VPS

### 1. Fazer o Deploy Completo

```bash
# Execute o deploy completo
./scripts/deploy.sh
```

**OU** se quiser deploy apenas dos arquivos da customização:

```bash
# Execute o deploy específico
./scripts/deploy-customizacao.sh
```

### 2. Conectar ao VPS e Limpar Cache

```bash
# Conectar ao VPS
ssh -p 22022 helder@69.6.222.77

# Ir para o diretório do projeto
cd /var/www/pagby

# Limpar caches
php artisan view:clear
php artisan config:clear
php artisan cache:clear

# Sair
exit
```

### 3. Testar no Navegador

1. Acesse o tenant no VPS
2. Login como Proprietário
3. Verifique se o menu "Customizar Home" apareceu
4. Teste a funcionalidade

---

## 🔍 Verificar se Arquivos Foram Enviados

Se o menu ainda não aparecer, verifique se os arquivos foram enviados:

```bash
ssh -p 22022 helder@69.6.222.77 "ls -la /var/www/pagby/app/Livewire/Proprietario/CustomizarHome.php"
ssh -p 22022 helder@69.6.222.77 "grep -n 'customizar-home' /var/www/pagby/resources/views/layouts/navigation-back.blade.php"
```

---

## ⚡ Script Rápido (Tudo em um comando)

```bash
# Deploy + Limpar cache remoto automaticamente
./scripts/deploy.sh && ssh -p 22022 helder@69.6.222.77 "cd /var/www/pagby && php artisan view:clear && php artisan config:clear && php artisan cache:clear"
```

---

## 📋 Checklist de Verificação

- [ ] Deploy executado com sucesso
- [ ] Cache limpo no VPS (view, config, cache)
- [ ] Arquivo `CustomizarHome.php` existe no servidor
- [ ] Arquivo `navigation-back.blade.php` atualizado no servidor
- [ ] Menu "Customizar Home" aparece para Proprietário
- [ ] Tenant criado com Template Padrao tem arquivo (não symlink)

---

## 🐛 Troubleshooting

### Menu não aparece mesmo após deploy

1. **Verificar se é Proprietário:**
   - Usuário precisa ter role "Proprietário"

2. **Verificar cache:**
   ```bash
   ssh -p 22022 helder@69.6.222.77 "cd /var/www/pagby && php artisan optimize:clear"
   ```

3. **Verificar erro nos logs:**
   ```bash
   ssh -p 22022 helder@69.6.222.77 "tail -50 /var/www/pagby/storage/logs/laravel.log"
   ```

4. **Forçar recarregar página:**
   - Ctrl+Shift+R no navegador

### "Customizar Home" mostra "não editável"

- Verificar se o tenant foi criado com Template Padrao
- Verificar se home.blade.php é arquivo (não symlink):
  ```bash
  ssh -p 22022 helder@69.6.222.77 "file /var/www/pagby/resources/views/tenants/{TENANT_ID}/home.blade.php"
  ```

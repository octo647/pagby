# Sistema de Lembretes WhatsApp - PagBy

Sistema automatizado de lembretes de vencimento de assinaturas via WhatsApp.

## 📋 Arquitetura

```
Laravel (PHP)                JSON Bridge              Node.js Bot
─────────────               ─────────────            ─────────────
CheckExpiringSubscriptions  subscription_reminders   reminder-bot.js
     (Command)        →         .json          →    (Baileys WhatsApp)
                                                            ↓
                                                        Customer
```

## 🚀 Como Funcionar

### 1. Configurar o Bot WhatsApp

```bash
cd scripts/whatsapp-bot
npm install
./start-reminder-bot.sh
```

Na primeira execução, escaneie o QR code com seu WhatsApp Business.

### 2. Agendar Verificação de Assinaturas

O comando já está agendado em `bootstrap/app.php`:

```php
// 3 dias antes - 09h
$schedule->command('subscriptions:check-expiring --days=3')->dailyAt('09:00');

// 1 dia antes - 10h
$schedule->command('subscriptions:check-expiring --days=1')->dailyAt('10:00');
```

Para testar manualmente:

```bash
php artisan subscriptions:check-expiring --days=3
```

### 3. Ativar Cron (Produção)

No servidor, adicione ao crontab:

```bash
crontab -e
```

Adicione:

```
* * * * * cd /path/to/pagby && php artisan schedule:run >> /dev/null 2>&1
```

## 📱 Fluxo Completo

1. **09h**: Laravel verifica assinaturas vencendo em 3 dias
2. **Cria entrada** em `storage/app/subscription_reminders.json`
3. **A cada 5 minutos**: Bot WhatsApp lê o arquivo JSON
4. **Filtra lembretes** não enviados (`sent: false`)
5. **Formata mensagem** personalizada em português
6. **Envia via WhatsApp** para o cliente
7. **Marca como enviado** (`sent: true`) no JSON

## 📄 Formato JSON

```json
{
  "123_2024-03-15": {
    "payment_id": 123,
    "tenant_id": "abc123",
    "tenant_name": "Salão Magic Club",
    "plan_name": "Plano Premium",
    "amount": 49.00,
    "due_date": "2024-03-15",
    "days_until_due": 3,
    "customer_name": "Maria Silva",
    "customer_phone": "(11) 98765-4321",
    "customer_email": "maria@example.com",
    "payment_url": "https://sandbox.asaas.com/i/abc123",
    "created_at": "2024-03-12T09:00:00.000000Z",
    "sent": false,
    "sent_at": null
  }
}
```

## 💬 Mensagem Enviada

```
🔔 *Lembrete de Vencimento - Salão Magic Club*

Olá, *Maria Silva*! 👋

Seu plano *Plano Premium* vence *em 3 dias* (15/03/2024).

💰 Valor: *R$ 49,00*

🔗 Link para renovação:
https://sandbox.asaas.com/i/abc123

📲 Renove agora para continuar aproveitando todos os benefícios!

_Mensagem automática do PagBy_
```

## 🔧 Arquivos Criados

| Arquivo | Descrição |
|---------|-----------|
| `app/Console/Commands/CheckExpiringSubscriptions.php` | Comando Laravel que verifica BD e gera JSON |
| `scripts/whatsapp-bot/reminder-bot.js` | Bot Node.js que lê JSON e envia mensagens |
| `scripts/whatsapp-bot/start-reminder-bot.sh` | Script para iniciar o bot |
| `storage/app/subscription_reminders.json` | Bridge de dados entre Laravel e Node.js |

## 🛠️ Comandos Úteis

```bash
# Testar verificação de assinaturas
php artisan subscriptions:check-expiring --days=3

# Ver JSON de lembretes
cat storage/app/subscription_reminders.json | jq

# Iniciar bot WhatsApp
cd scripts/whatsapp-bot && ./start-reminder-bot.sh

# Ver logs do bot
cd scripts/whatsapp-bot && pm2 logs reminder-bot

# Reiniciar bot com PM2
pm2 restart reminder-bot
```

## 🐛 Troubleshooting

### Bot não conecta

```bash
cd scripts/whatsapp-bot
rm -rf auth_info_baileys/
node reminder-bot.js  # Escaneie QR novamente
```

### Lembretes não sendo enviados

1. Verifique se o arquivo JSON existe:
   ```bash
   ls -la storage/app/subscription_reminders.json
   ```

2. Verifique formato dos telefones (deve ter +55):
   ```bash
   cat storage/app/subscription_reminders.json | jq '.[].customer_phone'
   ```

3. Verifique logs do bot no console

### Comando não agenda

```bash
# Limpar cache do Laravel
php artisan config:clear
php artisan cache:clear

# Testar schedule
php artisan schedule:list
```

## 📈 Melhorias Futuras

- [ ] Dashboard para ver histórico de lembretes enviados
- [ ] Múltiplos templates de mensagem (3 dias, 1 dia, vencido)
- [ ] Retry automático para falhas de envio
- [ ] Webhook para cancelar lembretes quando plano é renovado
- [ ] Notificações in-app além do WhatsApp
- [ ] Relatório de engajamento (quantos renovaram após lembrete)

## 🔒 Segurança

- O bot usa autenticação multi-file do Baileys (segura)
- Telefones são formatados e validados antes do envio
- Números inválidos são logados e pulados
- JSON contém apenas dados necessários (sem senhas/tokens)

## 📞 Suporte

Em caso de problemas, verifique:
1. Laravel logs: `storage/logs/laravel.log`
2. Bot console output
3. Cron logs: `/var/log/cron` ou `/var/log/syslog`

# Sistema de Lembretes de Agendamento - WhatsApp Bot

Sistema integrado para enviar lembretes automáticos de agendamentos via WhatsApp.

## 🎯 Como Funciona

O sistema usa o bot WhatsApp já existente (`index-with-commands.js`) para processar lembretes de agendamentos:

```
Laravel Command          JSON Bridge            WhatsApp Bot
──────────────          ─────────────          ─────────────
SendAppointment    →    whatsapp_commands → index-with-commands.js
Reminders.php               .json                  ↓
                                              Cliente recebe
                                              lembrete
```

## 📋 Características

- ✅ **Integrado ao bot existente** - Usa mesma infraestrutura de comandos
- ✅ **Lembretes automáticos** - 24h antes (às 18h) e 2h antes (a cada hora)
- ✅ **Multi-tenant** - Processa todos os salões automaticamente
- ✅ **Controle de envio** - Campo `reminder_sent_at` evita duplicatas
- ✅ **Retry automático** - Até 3 tentativas em caso de falha
- ✅ **WhatsApp ativado** - Só envia para clientes que ativaram WhatsApp

## 🚀 Instalação

### 1. Rodar migration

```bash
php artisan migrate
```

Isso adiciona o campo `reminder_sent_at` na tabela `appointments`.

### 2. Atualizar bot WhatsApp

```bash
# Upload dos arquivos atualizados
scp -P 22022 scripts/whatsapp-bot/index-with-commands.js helder@69.6.222.77:/var/www/pagby/scripts/whatsapp-bot/

# Reiniciar o bot
ssh -p 22022 helder@69.6.222.77 "pm2 restart whatsapp-bot"
```

### 3. Verificar agendamento

```bash
php artisan schedule:list
```

Deve mostrar:
- `appointments:send-reminders --hours=24` - Diariamente às 18h
- `appointments:send-reminders --hours=2` - A cada hora (8h-20h)

## 💬 Mensagem Enviada

```
🔔 *Lembrete de Agendamento*

Olá, *Maria Silva*! 👋

📅 Você tem um horário marcado:

🕐 *Data e hora:* 15/01/2026 às 14:00
💈 *Profissional:* João Silva
✂️ *Serviço:* Corte + Barba
📍 *Local:* Salão Magic Club - Centro

💰 *Valor:* R$ 75,00
⚠️ Pagamento pendente

Nos vemos em breve! 😊

_Mensagem automática de Salão Magic Club_
```

## 🔧 Comandos Disponíveis

### Testar manualmente

```bash
# Lembretes para agendamentos nas próximas 24h
php artisan appointments:send-reminders --hours=24

# Lembretes para agendamentos nas próximas 2h
php artisan appointments:send-reminders --hours=2
```

### Ver logs do bot

```bash
ssh -p 22022 helder@69.6.222.77
pm2 logs whatsapp-bot
```

### Ver comandos na fila

```bash
cat storage/app/whatsapp_commands.json | jq
```

## 📊 Fluxo Completo

1. **18h diariamente**: Laravel busca agendamentos do dia seguinte
2. **Cria comando JSON**: Tipo `appointment_reminder` com dados do agendamento
3. **Bot processa a cada 30s**: Lê `whatsapp_commands.json`
4. **Formata mensagem**: Personalizada com nome, horário, profissional, etc.
5. **Envia WhatsApp**: Para clientes com `whatsapp_activated=true`
6. **Marca como enviado**: Campo `reminder_sent_at` preenchido
7. **Remove da fila**: Comando executado é removido do JSON

## 🎨 Personalização

### Alterar horários de envio

Edite [bootstrap/app.php](bootstrap/app.php):

```php
// Lembrete de 24h às 18h
$schedule->command('appointments:send-reminders --hours=24')
         ->dailyAt('18:00');

// Lembrete de 2h a cada hora (8h-20h)
$schedule->command('appointments:send-reminders --hours=2')
         ->hourly()
         ->between('8:00', '20:00');
```

### Alterar mensagem

Edite a função `sendAppointmentReminder` em [scripts/whatsapp-bot/index-with-commands.js](scripts/whatsapp-bot/index-with-commands.js).

### Adicionar novos tipos de lembrete

1. Adicione novo tipo no comando: `type: 'custom_reminder'`
2. Crie função no bot: `async function sendCustomReminder(sock, cmd)`
3. Processe no loop: `else if (cmd.type === 'custom_reminder')`

## 🛡️ Proteções

- ✅ **Não envia duplicatas**: Campo `reminder_sent_at` controla
- ✅ **Só clientes ativos**: Verifica `whatsapp_activated=true`
- ✅ **Validação de telefone**: Normaliza e valida antes de enviar
- ✅ **Retry limitado**: Máximo 3 tentativas em caso de falha
- ✅ **Status do agendamento**: Só envia para status `confirmed`

## 📈 Monitoramento

### Ver lembretes enviados

```sql
SELECT 
    customer_id,
    appointment_date,
    start_time,
    reminder_sent_at
FROM appointments
WHERE reminder_sent_at IS NOT NULL
ORDER BY reminder_sent_at DESC
LIMIT 10;
```

### Ver taxa de sucesso

```bash
# No servidor
pm2 logs whatsapp-bot | grep "Lembrete de agendamento enviado"
```

## 🐛 Troubleshooting

### Lembretes não estão sendo enviados

1. Verificar se bot está rodando:
   ```bash
   ssh -p 22022 helder@69.6.222.77 "pm2 list"
   ```

2. Verificar se comandos estão sendo gerados:
   ```bash
   cat storage/app/whatsapp_commands.json
   ```

3. Verificar logs do comando:
   ```bash
   php artisan appointments:send-reminders --hours=24
   ```

### Cliente não recebeu lembrete

Verificar:
- ✅ Cliente tem telefone cadastrado
- ✅ Campo `whatsapp_activated` = true
- ✅ Agendamento tem status `confirmed`
- ✅ Agendamento não tem `reminder_sent_at` preenchido
- ✅ Horário do agendamento está no período correto

### Resetar lembretes

```sql
-- Para reenviar lembretes (usar com cuidado!)
UPDATE appointments 
SET reminder_sent_at = NULL 
WHERE appointment_date >= CURDATE();
```

## 🔄 Deploy

Ao fazer deploy, lembre de:

1. **Rodar migration**: `php artisan migrate`
2. **Atualizar bot**: Upload + restart PM2
3. **Limpar cache**: `php artisan config:cache`
4. **Verificar cron**: Deve estar ativo no servidor

## 📝 Tipos de Comando Suportados

| Tipo | Descrição | Campos Obrigatórios |
|------|-----------|-------------------|
| `send_message` | Mensagem genérica | `to`, `message` |
| `appointment_reminder` | Lembrete de agendamento | `customer_phone`, `customer_name`, `appointment_date`, etc. |
| `payment_reminder` | Lembrete de pagamento | `customer_phone`, `customer_name`, `amount`, `due_date` |

## 🎯 Próximos Passos

- [ ] Dashboard para gerenciar lembretes
- [ ] Configurações por tenant (horários personalizados)
- [ ] Confirmação de agendamento via WhatsApp
- [ ] Cancelamento via WhatsApp
- [ ] Estatísticas de engajamento
- [ ] A/B testing de mensagens

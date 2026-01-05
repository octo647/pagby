# Sistema de Comandos WhatsApp - PagBy

Sistema de integração entre Laravel e bot WhatsApp usando fila de comandos via arquivo JSON.

## 🏗️ Arquitetura

```
Laravel Command                WhatsApp Bot
─────────────                 ─────────────
CheckExpiringSubscriptions     index.js
         ↓                          ↓
  Gera comandos              Lê comandos
         ↓                          ↓
whatsapp_commands.json  ←  Processa a cada 30s
                                   ↓
                            Envia WhatsApp
                                   ↓
                            Remove da fila
```

## 📁 Arquivo de Comandos

**Localização:** `storage/app/whatsapp_commands.json`

**Estrutura:**
```json
[
  {
    "type": "send_message",
    "to": "(11) 98765-4321",
    "message": "🔔 *Lembrete de Vencimento*\n\nOlá, Maria!...",
    "created_at": "2026-01-02T09:00:00-03:00",
    "metadata": {
      "payment_id": 123,
      "tenant_id": "abc123",
      "due_date": "2026-01-05"
    }
  }
]
```

## 🚀 Como Funciona

### 1. Laravel Cria Comandos

O comando `subscriptions:check-expiring` verifica assinaturas vencendo e adiciona comandos à fila:

```bash
php artisan subscriptions:check-expiring --days=3
```

**O que acontece:**
1. Consulta banco de dados (tenants_plans_payments)
2. Filtra por status='RECEIVED' e próxima data de vencimento
3. Para cada assinatura expirando:
   - Extrai dados do cliente (nome, telefone)
   - Formata mensagem personalizada
   - Adiciona comando ao JSON
   - Evita duplicatas

### 2. Bot WhatsApp Processa Comandos

O bot (`index.js`) lê o arquivo JSON **a cada 30 segundos**:

```javascript
// Verifica comandos pendentes
setInterval(() => {
  // 1. Lê whatsapp_commands.json
  // 2. Para cada comando:
  //    - Formata telefone (adiciona +55)
  //    - Envia mensagem via Baileys
  //    - Remove da fila se sucesso
  //    - Mantém na fila se erro
}, 30000)
```

**Vantagens:**
- ✅ Usa bot existente (sem conflito de sessão)
- ✅ Desacoplado (Laravel e Bot independentes)
- ✅ Retry automático (comandos com erro permanecem na fila)
- ✅ Simples e robusto

## 📋 Tipos de Comandos

### send_message

Envia mensagem de texto para um número.

**Estrutura:**
```json
{
  "type": "send_message",
  "to": "11987654321",
  "message": "Texto da mensagem"
}
```

**Processamento de Telefone:**
- Remove caracteres não-numéricos
- Remove zero inicial se houver
- Adiciona código do Brasil (55) se necessário
- Formata para WhatsApp: `5511987654321@s.whatsapp.net`

## 🛠️ Comandos Úteis

### Gerar lembretes manualmente
```bash
php artisan subscriptions:check-expiring --days=3
php artisan subscriptions:check-expiring --days=1
```

### Ver comandos na fila
```bash
cat storage/app/whatsapp_commands.json | jq
```

### Limpar fila de comandos
```bash
echo "[]" > storage/app/whatsapp_commands.json
```

### Ver logs do bot
```bash
cd scripts/whatsapp-bot
tail -f ../../storage/logs/laravel.log
```

## 🔧 Configuração

### 1. Iniciar Bot WhatsApp

```bash
cd scripts/whatsapp-bot
./start-bot.sh  # ou node index.js
```

### 2. Agendar Verificação (Laravel Scheduler)

Já configurado em `bootstrap/app.php`:
```php
$schedule->command('subscriptions:check-expiring --days=3')
         ->dailyAt('09:00')
         ->timezone('America/Sao_Paulo');

$schedule->command('subscriptions:check-expiring --days=1')
         ->dailyAt('10:00')
         ->timezone('America/Sao_Paulo');
```

### 3. Ativar Cron (Produção)

```bash
crontab -e
```

Adicione:
```
* * * * * cd /var/www/pagby && php artisan schedule:run >> /dev/null 2>&1
```

## 📱 Exemplo de Fluxo Completo

**Dia 02/01 às 09h:**
1. Cron executa: `php artisan subscriptions:check-expiring --days=3`
2. Laravel encontra assinatura de Maria vencendo dia 05/01
3. Cria comando em `whatsapp_commands.json`:
   ```json
   {
     "type": "send_message",
     "to": "11987654321",
     "message": "🔔 Lembrete...",
     "created_at": "2026-01-02T09:00:00-03:00"
   }
   ```

**Dentro de 30 segundos:**
4. Bot lê arquivo JSON
5. Processa comando
6. Envia mensagem WhatsApp para Maria
7. Remove comando do arquivo
8. Arquivo fica: `[]`

## 🐛 Troubleshooting

### Bot não processa comandos

**Verifique:**
```bash
# 1. Bot está rodando?
ps aux | grep "node index.js"

# 2. Arquivo existe?
ls -la storage/app/whatsapp_commands.json

# 3. Formato JSON válido?
cat storage/app/whatsapp_commands.json | jq
```

### Comandos não são criados

**Verifique:**
```bash
# 1. Há assinaturas expirando?
php artisan subscriptions:check-expiring --days=3

# 2. Logs do Laravel
tail -f storage/logs/laravel.log

# 3. Permissões do arquivo
chmod 664 storage/app/whatsapp_commands.json
```

### Mensagens não são enviadas

**Verifique:**
```bash
# 1. Bot conectado ao WhatsApp?
# Deve mostrar: "✅ Bot conectado ao WhatsApp!"

# 2. Telefone tem formato correto?
# Deve ser: 5511987654321 (com código do país)

# 3. Sessão WhatsApp ativa?
ls -la scripts/whatsapp-bot/auth_info_baileys/
```

## 🔒 Segurança

- ✅ Arquivo JSON local (não exposto via web)
- ✅ Validação de formato de telefone
- ✅ Sanitização de dados antes do envio
- ✅ Logs detalhados para auditoria
- ✅ Retry automático em caso de falha

## 📊 Monitoramento

**Métricas importantes:**
- Quantos comandos na fila: `cat storage/app/whatsapp_commands.json | jq length`
- Comandos executados hoje: Verificar logs do bot
- Taxa de sucesso: Comandos criados vs comandos restantes

**Alertas sugeridos:**
- Fila com mais de 100 comandos (possível problema no bot)
- Bot desconectado por mais de 5 minutos
- Muitos comandos com erro (problema de formato)

## 🚀 Melhorias Futuras

- [ ] Suporte a outros tipos de comando (send_image, send_document)
- [ ] Dashboard web para monitorar fila
- [ ] Webhook para receber respostas dos clientes
- [ ] Histórico de mensagens enviadas (banco de dados)
- [ ] Múltiplos bots (load balancing)
- [ ] Prioridade de comandos (urgentes primeiro)

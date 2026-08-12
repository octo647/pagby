# WhatsApp Bot - Pagby

Bot automatizado para:
- 📱 Lembretes de vencimento de planos e assinaturas
- 🤖 Captação de leads de campanhas do Instagram/Facebook
- 📝 Perguntas qualificadoras e agendamento de demonstrações
- ✅ Ativação de notificações via WhatsApp

## 📱 Número Atual do Bot

**Número:** 32 998621569  
**WhatsApp:** 5532998621569  
**Status:** ✅ Ativo

_(Número anterior 32 98294948 migrado para Zapby)_

## Funcionalidades

- ✅ Responde automaticamente a mensagens recebidas
- ✅ Fluxo de perguntas para qualificar leads
- ✅ Salva informações em `leads.json`
- ✅ Personaliza mensagens com nome do cliente
- ✅ Funciona 24/7 uma vez ativado

## Perguntas do fluxo

1. Nome do interessado
2. Nome do salão/barbearia
3. WhatsApp para contato
4. Sistema atual de agendamento
5. Principal desafio na gestão
6. Melhor dia/horário para demonstração

## Como usar

### Primeira Ativação (novo número)

1. Instale as dependências:

```bash
cd scripts/whatsapp-bot
npm install
```

2. Inicie o bot:

```bash
# Opção 1: Usando script automatizado com PM2
./start-pagby-bot.sh

# Opção 2: Modo normal (sem PM2)
npm start
```

3. Escaneie o QR code com o WhatsApp do número **32 998621569** (apenas na primeira vez).

4. O bot ficará rodando e responderá automaticamente a qualquer mensagem recebida.

### Resetar Autenticação (trocar de número)

Se precisar conectar outro número posteriormente:

```bash
./reset-auth.sh
```

### Scripts Disponíveis

- `start-pagby-bot.sh` - Inicia o bot com PM2 (recomendado)
- `reset-auth.sh` - Remove autenticação e permite conectar novo número
- `start-reminder-bot.sh` - Inicia bot específico de lembretes

📖 **Documentação completa:** [ATIVACAO_NOVO_NUMERO.md](ATIVACAO_NOVO_NUMERO.md)

## Visualizar leads capturados

Os leads são salvos em `leads.json` no formato:

```json
[
  {
    "jid": "5599999999999@s.whatsapp.net",
    "timestamp": "2025-12-17T...",
    "nome": "João Silva",
    "salao": "Barbearia do João",
    "telefone": "55 99 99999-9999",
    "sistema_atual": "Agenda no papel",
    "desafio": "Controle de comissões",
    "horario": "Terça 14h"
  }
]
```

## Para campanha Instagram/Facebook

Use este link no anúncio para abrir conversa diretamente:

```
https://wa.me/5532998621569?text=Oi!%20Vi%20a%20propaganda%20do%20Pagby
```

**Link completo:** https://wa.me/5532998621569

## Manter bot rodando 24/7

Para manter o bot ativo no servidor, use PM2:

```bash
npm install -g pm2
pm2 start index.js --name whatsapp-bot
pm2 save
pm2 startup
```

## Observações

- Use um chip dedicado para evitar bloqueios no número pessoal
- Não envie spam ou mensagens não solicitadas
- O bot precisa estar rodando para responder mensagens
- Autenticação fica salva em `auth_info_baileys/`


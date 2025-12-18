# WhatsApp Bot - Captação de Leads Pagby

Bot automatizado para captar leads de campanhas do Instagram/Facebook, fazer perguntas qualificadoras e agendar demonstrações.

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

1. Instale as dependências:

```bash
cd scripts/whatsapp-bot
npm install
```

2. Rode o bot:

```bash
npm start
```

3. Escaneie o QR code com o WhatsApp dedicado (apenas na primeira vez).

4. O bot ficará rodando e responderá automaticamente a qualquer mensagem recebida.

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
https://wa.me/5599999999999?text=Oi!%20Vi%20a%20propaganda%20do%20Pagby
```

Substitua `5599999999999` pelo número do WhatsApp do bot.

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


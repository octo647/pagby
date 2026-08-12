# Ativação WhatsApp Bot Pagby - Novo Número

## 📱 Informações do Novo Número

**Número:** 32 998621569  
**Com DDD:** 5532998621569  
**Operadora:** _(confirmar qual operadora do chip)_

## ✅ Status Atual

- ✅ Autenticação antiga removida (número 32 98294948 agora no Zapby)
- ✅ Scripts de inicialização criados
- ⏳ Aguardando: QR Code scan com novo número

---

## 🚀 Passo a Passo para Ativar

### 1. Preparar o Chip

- Insira o chip no celular
- Instale o WhatsApp com o número **32 998621569**
- Configure o perfil com nome: **PagBy** ou **PagBy Atendimento**
- ⚠️ **IMPORTANTE:** Use um celular que ficará dedicado para o bot

### 2. Conectar no Servidor VPS

```bash
ssh usuario@seu-servidor-vps
cd /home/helder/projetos/pagby/scripts/whatsapp-bot
```

### 3. Verificar Dependências

```bash
# Instalar PM2 globalmente (se ainda não tiver)
npm install -g pm2

# Instalar dependências do bot
npm install
```

### 4. Iniciar o Bot

```bash
# Usando o script de inicialização
./start-pagby-bot.sh

# OU diretamente com PM2
pm2 start index.js --name pagby-whatsapp-bot
```

### 5. Ver o QR Code

```bash
# Ver os logs para visualizar o QR code
pm2 logs pagby-whatsapp-bot

# OU se iniciou sem PM2
# O QR code aparecerá automaticamente no terminal
```

### 6. Escanear QR Code

1. Abra o WhatsApp no celular com o número **32 998621569**
2. Vá em: **Configurações** → **Aparelhos conectados** → **Conectar aparelho**
3. Escaneie o QR code que apareceu no terminal
4. Aguarde a mensagem: **✅ Bot conectado ao WhatsApp!**
5. Confirme que apareceu: **📱 Número WhatsApp conectado: 5532998621569**

---

## 🔧 Comandos Úteis

### Gerenciar o Bot

```bash
# Ver logs em tempo real
pm2 logs pagby-whatsapp-bot

# Monitorar status
pm2 monit

# Reiniciar bot
pm2 restart pagby-whatsapp-bot

# Parar bot
pm2 stop pagby-whatsapp-bot

# Remover bot do PM2
pm2 delete pagby-whatsapp-bot

# Ver lista de processos
pm2 list
```

### Salvar Configuração PM2

```bash
# Salvar lista de processos
pm2 save

# Configurar PM2 para iniciar no boot
pm2 startup
# Siga as instruções que aparecerem
```

### Reset de Autenticação (se necessário)

```bash
# Se precisar conectar outro número
./reset-auth.sh
```

---

## 📋 Funcionalidades do Bot

### Bot Principal (index.js)

O bot responde aos seguintes comandos:

1. **ATIVAR** - Ativa lembretes de WhatsApp para o usuário
2. **VINCULAR [telefone]** - Vincula telefone ao WhatsApp ID
3. **MENU** ou **AJUDA** - Mostra comandos disponíveis

### Fluxo de Ativação

Quando um cliente envia "ATIVAR":
- Bot verifica se o número está cadastrado no sistema
- Se encontrar: ativa lembretes automaticamente
- Se não encontrar: solicita telefone cadastrado para vincular
- Após vincular: ativa os lembretes

### Integração com Laravel

O bot integra com o Laravel através de:
- `storage/app/whatsapp_commands.json` - Comandos a processar
- `storage/app/whatsapp_phone_map.json` - Mapeamento telefone → WhatsApp ID
- Consulta direta aos bancos de dados dos tenants

---

## 🧪 Testando o Bot

### 1. Teste de Conexão

Após conectar, envie uma mensagem do seu número pessoal para **32 998621569**:

```
Olá
```

Você deve receber alguma resposta do bot (ou ver a mensagem nos logs).

### 2. Teste do Comando ATIVAR

Envie:

```
ATIVAR
```

O bot deve:
- Buscar seu número nos tenants
- Ativar lembretes OU solicitar vinculação

### 3. Verificar Logs

```bash
pm2 logs pagby-whatsapp-bot --lines 50
```

Você deve ver:
- `📩 Mensagem de [número]: [texto]`
- `✅ Bot conectado ao WhatsApp!`
- Sem erros de conexão

---

## ⚠️ Troubleshooting

### QR Code não aparece

```bash
# Reinicie o bot
pm2 restart pagby-whatsapp-bot
pm2 logs pagby-whatsapp-bot
```

### Bot desconecta frequentemente

- Verifique a conexão de internet do servidor
- Confirme que não há outro dispositivo conectado no mesmo número
- Certifique-se de que o chip tem sinal e dados móveis

### Erro: "Módulo não encontrado"

```bash
cd /home/helder/projetos/pagby/scripts/whatsapp-bot
npm install
```

### Bot não responde mensagens

```bash
# Verifique se está rodando
pm2 list

# Veja os logs
pm2 logs pagby-whatsapp-bot --err

# Verifique permissões nos arquivos
ls -la ../../storage/app/
```

---

## 📊 Monitoramento

### Arquivo de Mapeamento

```bash
# Ver mapeamento de telefones
cat ../../storage/app/whatsapp_phone_map.json
```

### Leads Capturados

```bash
# Ver leads (se usar fluxo de captação)
cat leads.json
```

---

## 🔐 Segurança

1. **Não compartilhe** arquivos da pasta `auth_info_baileys/`
2. **Backup**: Os arquivos de autenticação são salvos automaticamente
3. **Chip dedicado**: Use um chip exclusivo para o bot
4. **Não envie spam**: Respeite as políticas do WhatsApp

---

## 📞 Informações de Contato

**Número do Bot:** 32 998621569  
**Finalidade:** Lembretes automáticos e atendimento Pagby  
**Status:** ⏳ Aguardando ativação

---

## ✅ Checklist de Ativação

- [ ] Chip inserido e WhatsApp configurado
- [ ] Conectado ao VPS
- [ ] PM2 instalado
- [ ] Dependências instaladas (`npm install`)
- [ ] Bot iniciado (`./start-pagby-bot.sh`)
- [ ] QR Code escaneado
- [ ] Mensagem de confirmação apareceu
- [ ] Teste de mensagem enviado e respondido
- [ ] PM2 save executado
- [ ] PM2 startup configurado

---

**Data de configuração:** 28 de março de 2026  
**Número anterior:** 32 98294948 (migrado para Zapby)  
**Número atual:** 32 998621569

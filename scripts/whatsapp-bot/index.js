import makeWASocket, { useMultiFileAuthState, fetchLatestBaileysVersion } from '@whiskeysockets/baileys'
import qrcode from 'qrcode-terminal'
import fs from 'fs'
import path from 'path'
import { fileURLToPath } from 'url'
import fetch from 'node-fetch'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

// Arquivo de comandos do Laravel
const COMMANDS_FILE = path.join(__dirname, '../../storage/app/whatsapp_commands.json')

// Arquivo de mapeamento telefone → WhatsApp ID
const PHONE_MAP_FILE = path.join(__dirname, '../../storage/app/whatsapp_phone_map.json')

const MAX_RECONNECT_DELAY = 60 * 1000
const SUPPORT_URL = 'https://wa.me/5532998548620'
const CENTRAL_API_URL = 'https://pagby.com.br'

// Evita que uma falha isolada (ex: envio durante reconexão) derrube o processo inteiro
process.on('unhandledRejection', (reason) => {
  console.error('⚠️ Unhandled rejection (bot continua rodando):', reason)
})
process.on('uncaughtException', (error) => {
  console.error('⚠️ Uncaught exception (bot continua rodando):', error)
})

// Lista de tenants usada como fallback até a primeira atualização via API central
let tenantSubdomains = ['magic-club', 'dumont', 'villebelle', 'labelle', 'bicholegal', 'dudu', 'bar', 'salao-cowboy', 'barba-e-cabelo', 'pets-cia']

async function refreshTenantSubdomains() {
  try {
    const response = await fetch(`${CENTRAL_API_URL}/api/tenants/domains`)
    const data = await response.json()

    if (Array.isArray(data.tenants) && data.tenants.length > 0) {
      tenantSubdomains = data.tenants
      console.log(`🔄 Lista de tenants atualizada (${tenantSubdomains.length})`)
    }
  } catch (error) {
    console.log(`⚠️ Erro ao atualizar lista de tenants: ${error.message}`)
  }
}

refreshTenantSubdomains()
setInterval(refreshTenantSubdomains, 10 * 60 * 1000)
let reconnectAttempts = 0
let reconnectTimer = null
let commandProcessorTimer = null
let currentSocket = null

// Armazena o estado de cada conversa: { jid: { step: 0, data: {} } }
const conversations = {}

// Carrega mapeamento de telefones
let phoneMap = {}
try {
  if (fs.existsSync(PHONE_MAP_FILE)) {
    phoneMap = JSON.parse(fs.readFileSync(PHONE_MAP_FILE, 'utf8'))
  }
} catch (error) {
  console.log('⚠️  Erro ao carregar mapeamento de telefones:', error.message)
}

async function main() {
  try {
    const { state, saveCreds } = await useMultiFileAuthState('auth_info_baileys')
    const { version } = await fetchLatestBaileysVersion()
    const sock = makeWASocket({
      version,
      auth: state,
    })

    currentSocket = sock

    sock.ev.on('creds.update', saveCreds)

    sock.ev.on('connection.update', (update) => {
      const { connection, lastDisconnect, qr } = update
      if (qr) {
        qrcode.generate(qr, { small: true })
        console.log('Escaneie o QR code acima com o WhatsApp.')
      }
      if (connection === 'close') {
        const statusCode = lastDisconnect?.error?.output?.statusCode
        currentSocket = null

        if (statusCode === 401) {
          console.error('❌ Sessão WhatsApp desconectada. Novo pareamento necessário.')
          return
        }

        scheduleReconnect(`conexão encerrada${statusCode ? ` (status ${statusCode})` : ''}`)
      }
      if (connection === 'open') {
        reconnectAttempts = 0
        console.log('✅ Bot conectado ao WhatsApp!')

        const connectedNumber = sock.user?.id || 'Número não identificado'
        console.log(`📱 Número WhatsApp conectado: ${connectedNumber}`)

        startCommandProcessor()
      }
    })

    // Escuta mensagens recebidas
    sock.ev.on('messages.upsert', async ({ messages }) => {
      for (const msg of messages) {
        if (!msg.message || msg.key.fromMe) continue

        const jid = msg.key.remoteJid
        const text = msg.message.conversation || msg.message.extendedTextMessage?.text || ''

        console.log(`📩 Mensagem de ${jid}: ${text}`)

        try {
          await savePhoneMapping(jid)
          await handleConversation(sock, jid, text)
        } catch (error) {
          console.error(`❌ Erro ao processar mensagem de ${jid}:`, error.message)
        }
      }
    })
  } catch (error) {
    currentSocket = null
    scheduleReconnect(`falha ao iniciar conexão: ${error.message}`)
  }
}

function scheduleReconnect(reason) {
  if (reconnectTimer) return

  reconnectAttempts += 1
  const delay = Math.min(5000 * 2 ** (reconnectAttempts - 1), MAX_RECONNECT_DELAY)
  console.error(`⚠️ ${reason}. Nova tentativa em ${Math.ceil(delay / 1000)}s.`)

  reconnectTimer = setTimeout(async () => {
    reconnectTimer = null
    await main()
  }, delay)
}

async function handleConversation(sock, jid, text) {
  // Inicializa conversa se não existir
  if (!conversations[jid]) {
    conversations[jid] = { step: 0, data: {} }
  }

  const conv = conversations[jid]
  const lowerText = text.toLowerCase().trim()

  // COMANDO: ATIVAR WhatsApp
  if (lowerText === 'ativar') {
    const phone = jid.split('@')[0]
    let cleaned = phone
    
    console.log(`\n🔵 ===== COMANDO ATIVAR RECEBIDO =====`)
    console.log(`📱 JID original: ${jid}`)
    console.log(`📱 Número extraído: ${phone}`)
    
    // Se começa com 55, remove código do país
    if (cleaned.startsWith('55')) {
      console.log(`🇧🇷 Removendo código do país (55)`)
      cleaned = cleaned.substring(2)
    }
    
    console.log(`📱 Número final enviado para API: ${cleaned}`)
    console.log(`� JID completo enviado: ${jid}`)
    console.log(`🔵 =====================================\n`)
    
    // Tenta ativar em todos os tenants (enviando JID completo também)
    const activated = await markUserWhatsAppActivated(cleaned, jid)
    
    if (activated) {
      await sock.sendMessage(jid, {
        text: '✅ *Lembretes ativados com sucesso!*\n\nVocê receberá avisos sobre:\n• Vencimento de planos\n• Atualizações importantes\n\nPara desativar, entre em contato pelo perfil.'
      })
    } else {
      // Não encontrou - iniciar fluxo de vinculação automática
      conversations[jid] = { step: 'awaiting_phone', jid: jid }
      await sock.sendMessage(jid, {
        text: '📱 *Para ativar os lembretes*\n\nPor favor, envie seu número de telefone cadastrado.\n\n*Exemplo:* 32998448612\n\n(apenas números, sem espaços ou traços)'
      })
    }
    return
  }

  // Fluxo de vinculação: Usuário aguardando enviar telefone
  if (conv && conv.step === 'awaiting_phone') {
    const phoneInput = text.trim().replace(/\D/g, '') // Remove tudo que não é número
    
    if (!phoneInput || phoneInput.length < 10) {
      await sock.sendMessage(jid, {
        text: '⚠️ *Número inválido*\n\nEnvie apenas os números do seu telefone.\n\n*Exemplo:* 32998448612'
      })
      return
    }
    
    console.log(`\n🔗 ===== VINCULANDO TELEFONE AUTOMATICAMENTE =====`)
    console.log(`📱 Telefone informado: ${phoneInput}`)
    console.log(`📱 JID: ${jid}`)
    
    const linked = await linkPhoneToJid(phoneInput, jid)
    
    if (linked) {
      // Agora ativa
      const activated = await markUserWhatsAppActivated(phoneInput, jid)
      delete conversations[jid] // Limpa estado
      
      await sock.sendMessage(jid, {
        text: '✅ *Lembretes ativados com sucesso!*\n\nSeu telefone foi vinculado e os lembretes foram ativados.\n\nVocê receberá avisos sobre:\n• Vencimento de planos\n• Atualizações importantes'
      })
    } else {
      delete conversations[jid]
      await sock.sendMessage(jid, {
        text: '⚠️ *Telefone não encontrado*\n\nEste número não está cadastrado em nenhum salão.\n\nVerifique se o número está correto ou entre em contato com seu salão.'
      })
    }
    return
  }

  // COMANDO: VINCULAR telefone ao JID (para contas Business)
  if (lowerText.startsWith('vincular ')) {
    const phoneToLink = lowerText.replace('vincular ', '').trim().replace(/\D/g, '')
    
    if (!phoneToLink || phoneToLink.length < 10) {
      await sock.sendMessage(jid, {
        text: '⚠️ *Formato inválido*\n\nUse: VINCULAR seguido do seu telefone\n\nExemplo:\nVINCULAR 32998448612'
      })
      return
    }
    
    console.log(`\n🔗 ===== VINCULAÇÃO DE TELEFONE =====`)
    console.log(`📱 JID: ${jid}`)
    console.log(`📞 Telefone: ${phoneToLink}`)
    
    // Tenta vincular em todos os tenants
    const linked = await linkPhoneToJid(phoneToLink, jid)
    
    if (linked) {
      await sock.sendMessage(jid, {
        text: `✅ *Telefone vinculado com sucesso!*\n\nSeu número ${phoneToLink} foi vinculado a esta conta WhatsApp.\n\nAgora você pode usar o comando ATIVAR normalmente.`
      })
    } else {
      await sock.sendMessage(jid, {
        text: '⚠️ *Telefone não encontrado*\n\nEste número não está cadastrado em nenhum salão.\n\nVerifique se o número está correto.'
      })
    }
    return
  }
  
  // Comandos especiais
  if (lowerText === 'menu' || lowerText === 'ajuda') {
    await sendStatusAwareMenu(sock, jid)
    return
  }

  await sendStatusAwareMenu(sock, jid)
}

async function sendStatusAwareMenu(sock, jid) {
  const phone = jid.split('@')[0]
  const status = await getWhatsAppStatus(phone, jid)

  if (status.activated) {
    await sock.sendMessage(jid, {
      text: `*Lembrete PagBy*\n\nSeu telefone está ativado para receber lembretes!\n\nSe precisar falar com o suporte, envie sua dúvida para ${SUPPORT_URL}, que retornaremos em breve!`
    })
    return
  }

  await sock.sendMessage(jid, {
    text: `*Lembrete PagBy*\n\nComandos disponíveis:\n• ATIVAR - Ativar lembretes via WhatsApp\n• VINCULAR [número] - Vincular seu telefone\n\nEste bot processa comandos automáticos de lembretes.\n\nPara falar com o suporte, envie sua dúvida para ${SUPPORT_URL}, que retornaremos em breve!`
  })
}

async function getWhatsAppStatus(phone, jid) {
  const tenants = tenantSubdomains

  for (const tenant of tenants) {
    try {
      const response = await fetch(`https://${tenant}.pagby.com.br/api/whatsapp/status`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ phone, jid })
      })
      const data = await response.json()

      if (data.found) {
        return { activated: Boolean(data.activated) }
      }
    } catch (error) {
      console.log(`⚠️ Erro ao consultar status no tenant ${tenant}: ${error.message}`)
    }
  }

  return { activated: false }
}

/**
 * Processa comandos do Laravel a cada 30 segundos
 */
function startCommandProcessor() {
  if (commandProcessorTimer) return

  console.log('🔔 Processador de comandos Laravel iniciado')
  
  commandProcessorTimer = setInterval(async () => {
    const sock = currentSocket
    if (!sock) return

    try {
      if (!fs.existsSync(COMMANDS_FILE)) {
        return
      }

      const data = fs.readFileSync(COMMANDS_FILE, 'utf8')
      const commands = JSON.parse(data)
      
      if (!Array.isArray(commands) || commands.length === 0) {
        return
      }

      console.log(`📋 Processando ${commands.length} comandos...`)
      
      const executed = []
      const failed = []
      
      for (const cmd of commands) {
        try {
          if (cmd.type === 'send_message') {
            await sendMessageCommand(sock, cmd)
            executed.push(cmd)
            console.log(`✅ Mensagem enviada para ${cmd.to}`)
          } else if (cmd.type === 'appointment_reminder') {
            await sendAppointmentReminder(sock, cmd)
            executed.push(cmd)
            console.log(`✅ Lembrete de agendamento enviado para ${cmd.customer_name}`)
          }
        } catch (error) {
          console.error(`❌ Erro ao processar comando ${cmd.type} para ${cmd.to || cmd.customer_phone}:`, error.message)
          failed.push({ ...cmd, error: error.message, retries: (cmd.retries || 0) + 1 })
        }
      }
      
      // Atualiza arquivo: mantém apenas os que falharam (com limite de 3 tentativas)
      const retry = failed.filter(cmd => cmd.retries < 3)
      fs.writeFileSync(COMMANDS_FILE, JSON.stringify(retry, null, 2))
      
      if (executed.length > 0) {
        console.log(`✅ ${executed.length} comandos executados com sucesso`)
      }
      if (failed.length > 0) {
        console.log(`⚠️  ${failed.length} comandos falharam`)
      }
      
    } catch (error) {
      console.error('❌ Erro ao processar comandos:', error.message)
    }
  }, 30 * 1000) // 30 segundos
  
  console.log('✅ Verificação ativa: comandos serão processados a cada 30s')
}

/**
 * Salva mapeamento de telefone → WhatsApp ID
 */
async function savePhoneMapping(jid) {
  try {
    // Salva o JID original
    let key = jid.split('@')[0]
    
    // Para JIDs do tipo @lid (contas business/linked), não tenta extrair número
    // Esses JIDs não contêm o número de telefone
    if (jid.includes('@lid')) {
      console.log(`ℹ️  JID tipo @lid detectado: ${jid}`)
      console.log(`💡 Para ativar lembretes, use o comando ATIVAR`)
      
      // Apenas procura se já existe mapeamento
      let brazilianPhone = null
      for (const [phone, mappedJid] of Object.entries(phoneMap)) {
        if (mappedJid === jid && phone.length >= 10 && phone.length <= 11 && !phone.includes('@')) {
          brazilianPhone = phone
          break
        }
      }
      
      if (brazilianPhone) {
        console.log(`📱 Número já mapeado: ${brazilianPhone}`)
      }
      
      return
    }
    
    // Se não mudou, não precisa salvar novamente
    if (phoneMap[key] === jid) {
      return
    }
    
    phoneMap[key] = jid
    
    // Se é um número brasileiro (começa com 55), extrai e salva também
    if (key.startsWith('55')) {
      const phone = key.substring(2) // Remove código do país
      phoneMap[phone] = jid
      console.log(`💾 Mapeamento salvo: ${phone} → ${jid}`)
      
      // Marca usuário como whatsapp_activated no banco
      await markUserWhatsAppActivated(phone)
    } else {
      console.log(`💾 Mapeamento salvo: ${key} → ${jid}`)
    }
    
    fs.writeFileSync(PHONE_MAP_FILE, JSON.stringify(phoneMap, null, 2))
  } catch (error) {
    console.error('❌ Erro ao salvar mapeamento:', error.message)
  }
}

/**
 * Marca usuário como whatsapp_activated no banco via API
 * Retorna true se ativou com sucesso, false se não encontrou
 */
async function markUserWhatsAppActivated(phone, jid = null) {
  try {
    // Valida se o número parece válido (10-11 dígitos)
    const cleanPhone = phone.replace(/\D/g, '')
    if (cleanPhone.length < 10 || cleanPhone.length > 11) {
      console.log(`⚠️  Número inválido (${phone}), pulando busca em tenants`)
      return false
    }
    
    console.log(`\n🟢 ===== INICIANDO BUSCA EM TENANTS =====`)
    console.log(`📱 Buscando número: ${phone}`)
    if (jid) console.log(`📱 JID: ${jid}\n`)
    else console.log()
    
    // Descobre qual tenant tem esse número
    const tenants = tenantSubdomains
    
    for (const tenant of tenants) {
      const url = `https://${tenant}.pagby.com.br/api/whatsapp/activate`
      
      console.log(`🔍 Tentando tenant: ${tenant}...`)
      
      try {
        const payload = { phone }
        if (jid) payload.jid = jid
        
        const response = await fetch(url, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        })
        
        const data = await response.json()
        
        console.log(`   📊 Resposta: success=${data.success}, updated=${data.updated}`)
        if (data.tried) console.log(`   🔄 Variações testadas: ${data.tried.join(', ')}`)
        
        // Aceita success=true OU updated > 0 (compatibilidade com ambas as respostas)
        if (data.success && (data.updated > 0 || data.message)) {
          console.log(`\n✅ ===== SUCESSO! =====`)
          console.log(`✅ WhatsApp ativado para ${phone} no tenant ${tenant}`)
          if (data.user) console.log(`✅ Usuário: ${data.user}`)
          console.log(`✅ ====================\n`)
          return true // Sucesso!
        }
      } catch (err) {
        console.log(`   ⚠️  Erro ao chamar API: ${err.message}`)
        // Ignora erros de tenant específico e continua
      }
    }
    
    console.log(`\n❌ ===== FALHA =====`)
    console.log(`❌ Número ${phone} não encontrado em nenhum tenant`)
    console.log(`❌ =================\n`)
    return false // Não encontrou em nenhum tenant
  } catch (error) {
    console.error(`❌ Erro ao ativar WhatsApp: ${error.message}`)
    return false
  }
}

/**
 * Vincula um telefone a um JID do WhatsApp (útil para contas Business)
 * Retorna true se encontrou e vinculou, false se não encontrou
 */
async function linkPhoneToJid(phone, jid) {
  try {
    console.log(`\n🔗 ===== VINCULANDO TELEFONE AO JID =====`)
    console.log(`📱 Telefone: ${phone}`)
    console.log(`📱 JID: ${jid}\n`)
    
    const tenants = tenantSubdomains
    
    for (const tenant of tenants) {
      const url = `https://${tenant}.pagby.com.br/api/whatsapp/link-jid`
      
      console.log(`🔍 Tentando tenant: ${tenant}...`)
      
      try {
        const response = await fetch(url, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ phone, jid })
        })
        
        const data = await response.json()
        
        console.log(`   📊 Resposta: success=${data.success}`)
        
        if (data.success) {
          console.log(`\n✅ ===== SUCESSO! =====`)
          console.log(`✅ Telefone ${phone} vinculado ao JID no tenant ${tenant}`)
          if (data.user) console.log(`✅ Usuário: ${data.user}`)
          console.log(`✅ ====================\n`)
          return true
        }
      } catch (err) {
        console.log(`   ⚠️  Erro ao chamar API: ${err.message}`)
      }
    }
    
    console.log(`\n❌ ===== FALHA =====`)
    console.log(`❌ Telefone ${phone} não encontrado em nenhum tenant`)
    console.log(`❌ =================\n`)
    return false
  } catch (error) {
    console.error(`❌ Erro ao vincular telefone: ${error.message}`)
    return false
  }
}

/**
 * Busca WhatsApp ID por telefone
 */
function findWhatsAppId(phone) {
  if (!phone) return null
  
  // Remove caracteres não numéricos
  let cleaned = phone.replace(/\D/g, '')
  
  // Remove código do país se tiver
  if (cleaned.startsWith('55')) {
    cleaned = cleaned.substring(2)
  }
  
  // Remove 0 inicial
  if (cleaned.startsWith('0')) {
    cleaned = cleaned.substring(1)
  }
  
  // Tenta encontrar mapeamento direto
  if (phoneMap[cleaned]) {
    return phoneMap[cleaned]
  }
  
  // Se tem 11 dígitos, tenta sem o 9
  if (cleaned.length === 11) {
    const without9 = cleaned.substring(0, 2) + cleaned.substring(3)
    if (phoneMap[without9]) {
      return phoneMap[without9]
    }
  }
  
  // Se tem 10 dígitos, tenta com o 9
  if (cleaned.length === 10) {
    const with9 = cleaned.substring(0, 2) + '9' + cleaned.substring(2)
    if (phoneMap[with9]) {
      return phoneMap[with9]
    }
  }
  
  return null
}

/**
 * Busca em todos os tenants o whatsapp_jid salvo para um telefone.
 * Necessário para contas @lid (vinculadas/business), onde o formato
 * "55+número@s.whatsapp.net" adivinhado não alcança o destinatário real.
 */
async function resolveWhatsAppTarget(phone) {
  const localId = findWhatsAppId(phone)
  if (localId) return localId

  for (const tenant of tenantSubdomains) {
    try {
      const response = await fetch(`https://${tenant}.pagby.com.br/api/whatsapp/status`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ phone })
      })
      const data = await response.json()

      if (data.found && data.jid) {
        return data.jid
      }
    } catch (error) {
      console.log(`⚠️ Erro ao resolver JID no tenant ${tenant}: ${error.message}`)
    }
  }

  return null
}

/**
 * Envia mensagem via comando
 */
async function sendMessageCommand(sock, cmd) {
  const { to, message } = cmd
  
  // Se já tem @ (é um ID do WhatsApp), usa direto
  if (to.includes('@')) {
    await sock.sendMessage(to, { text: message })
    return
  }
  
  // Tenta o JID real (mapeamento local ou whatsapp_jid salvo no tenant)
  const resolvedId = await resolveWhatsAppTarget(to)
  if (resolvedId) {
    console.log(`📱 Usando JID resolvido: ${to} → ${resolvedId}`)
    await sock.sendMessage(resolvedId, { text: message })
    return
  }
  
  // Se não encontrou, tenta formatos padrão
  console.log(`⚠️  ID não encontrado para ${to}, tentando formatos padrão...`)
  const phones = formatPhoneNumber(to)
  if (!phones || phones.length === 0) {
    throw new Error(`Telefone inválido: ${to}`)
  }
  
  // Tenta enviar com cada formato até conseguir
  let lastError = null
  for (const phone of phones) {
    try {
      await sock.sendMessage(phone, { text: message })
      console.log(`✅ Enviado com sucesso usando: ${phone}`)
      return // Sucesso!
    } catch (error) {
      lastError = error
      console.log(`❌ Falhou com ${phone}: ${error.message}`)
      // Continua tentando outros formatos
    }
  }
  
  // Se nenhum formato funcionou, lança erro
  throw lastError || new Error(`Não foi possível enviar para ${to}`)
}

async function sendAppointmentReminder(sock, cmd) {
  const target = (await resolveWhatsAppTarget(cmd.customer_phone)) || formatPhoneNumber(cmd.customer_phone)[0]

  if (!target) {
    throw new Error(`Telefone inválido: ${cmd.customer_phone}`)
  }

  const pendingPayment = cmd.has_pending_payment
    ? `💰 *Valor:* R$ ${Number(cmd.total_price || 0).toFixed(2).replace('.', ',')}\n⚠️ Pagamento pendente\n\n`
    : ''
  const observation = cmd.observation
    ? `📝 *Observação:* ${cmd.observation}\n\n`
    : ''

  const message = `🔔 *Lembrete de Agendamento*

Olá, *${cmd.customer_name}*! 👋

📅 Você tem um horário marcado:

🕐 *Data e hora:* ${cmd.appointment_date} às ${cmd.appointment_time}
💈 *Profissional:* ${cmd.employee_name}
✂️ *Serviço:* ${cmd.service_names}
📍 *Local:* ${cmd.tenant_name} - ${cmd.branch_name}

${observation}${pendingPayment}Nos vemos em breve! 😊

_Mensagem automática de ${cmd.tenant_name}_`

  await sock.sendMessage(target, { text: message })
}

/**
 * Formata número para WhatsApp - retorna array com possíveis formatos
 */
function formatPhoneNumber(phone) {
  if (!phone) return []
  
  let cleaned = phone.replace(/\D/g, '')
  
  if (cleaned.startsWith('0')) {
    cleaned = cleaned.substring(1)
  }
  
  // Remove código do país se já tiver
  if (cleaned.startsWith('55')) {
    cleaned = cleaned.substring(2)
  }
  
  const formats = []
  
  // Se tem 11 dígitos (DDD + 9 + número), tenta ambos os formatos
  if (cleaned.length === 11) {
    // Formato 1: com o 9 (número novo)
    formats.push(`55${cleaned}@s.whatsapp.net`)
    
    // Formato 2: sem o 9 (número antigo convertido)
    const without9 = cleaned.substring(0, 2) + cleaned.substring(3)
    formats.push(`55${without9}@s.whatsapp.net`)
  } 
  // Se tem 10 dígitos (DDD + número sem 9)
  else if (cleaned.length === 10) {
    // Formato 1: como está
    formats.push(`55${cleaned}@s.whatsapp.net`)
    
    // Formato 2: adiciona o 9 (tenta formato novo)
    const with9 = cleaned.substring(0, 2) + '9' + cleaned.substring(2)
    formats.push(`55${with9}@s.whatsapp.net`)
  }
  // Outros tamanhos
  else {
    formats.push(`55${cleaned}@s.whatsapp.net`)
  }
  
  return formats
}

// Salva conversas periodicamente (opcional)
setInterval(() => {
  const leadsFile = path.join(__dirname, 'leads.json')
  try {
    // Limpar conversas com mais de 30 dias
    const now = Date.now()
    for (const [key, data] of Object.entries(conversations)) {
      if (now - data.lastMessage > 30 * 24 * 60 * 60 * 1000) {
        delete conversations[key]
      }
    }
    
    // Salvar dados (máximo 10MB)
    const data = JSON.stringify(conversations, null, 2)
    if (Buffer.byteLength(data) > 10 * 1024 * 1024) {
      console.warn('⚠️ leads.json ultrapassou 10MB, limpando conversas antigas...')
      // Manter apenas últimas 500 conversas
      const sortedConvs = Object.entries(conversations)
        .sort((a, b) => b[1].lastMessage - a[1].lastMessage)
        .slice(0, 500)
      Object.assign(conversations, Object.fromEntries(sortedConvs))
    }
    
    fs.writeFileSync(leadsFile, JSON.stringify(conversations, null, 2))
  } catch (err) {
    if (err.code === 'ENOSPC') {
      console.error('❌ DISCO CHEIO! Limpe espaço em disco do VPS')
    } else {
      console.error('⚠️ Erro ao salvar leads:', err.message)
    }
  }
}, 60000)

// Inicia o bot
main().catch(err => {
  console.error('❌ Erro fatal:', err)
  process.exit(1)
})

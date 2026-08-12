import makeWASocket, { useMultiFileAuthState, fetchLatestBaileysVersion } from '@whiskeysockets/baileys'
import qrcode from 'qrcode-terminal'
import fs from 'fs'
import path from 'path'
import { fileURLToPath } from 'url'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

// Caminho para o arquivo de lembretes do Laravel
const REMINDERS_FILE = path.join(__dirname, '../../storage/app/subscription_reminders.json')

// Estado das conversas
const conversations = {}

async function main() {
  const { state, saveCreds } = await useMultiFileAuthState('auth_info_baileys')
  const { version } = await fetchLatestBaileysVersion()
  const sock = makeWASocket({
    version,
    auth: state,
  })

  sock.ev.on('creds.update', saveCreds)

  sock.ev.on('connection.update', (update) => {
    const { connection, lastDisconnect, qr } = update
    if (qr) {
      qrcode.generate(qr, { small: true })
      console.log('🔐 Escaneie o QR code acima com o WhatsApp.')
    }
    if (connection === 'close') {
      const shouldReconnect = lastDisconnect?.error?.output?.statusCode !== 401
      if (shouldReconnect) {
        console.log('🔄 Reconectando...')
        main()
      }
    }
    if (connection === 'open') {
      console.log('✅ Bot PagBy conectado ao WhatsApp!')
      console.log('📅 Sistema de lembretes ativo')
      
      // Inicia verificação periódica de lembretes
      startReminderChecker(sock)
    }
  })

  // Escuta mensagens recebidas
  sock.ev.on('messages.upsert', async ({ messages }) => {
    for (const msg of messages) {
      if (!msg.message || msg.key.fromMe) continue
      
      const jid = msg.key.remoteJid
      const text = msg.message.conversation || msg.message.extendedTextMessage?.text || ''
      
      console.log(`📩 Mensagem de ${jid}: ${text}`)
      
      await handleConversation(sock, jid, text)
    }
  })
}

async function handleConversation(sock, jid, text) {
  // Inicializa conversa se não existir
  if (!conversations[jid]) {
    conversations[jid] = { step: 0, data: {} }
  }

  const conv = conversations[jid]
  const lowerText = text.toLowerCase().trim()

  // Comandos especiais
  if (lowerText === 'menu' || lowerText === 'ajuda') {
    await sendMenu(sock, jid)
    return
  }

  // Resto da lógica de agendamento existente pode continuar aqui...
}

async function sendMenu(sock, jid) {
  const menu = `
🤖 *Bot PagBy - Menu de Ajuda*

📅 *Comandos disponíveis:*
• Digite *menu* ou *ajuda* para ver este menu
• O bot enviará lembretes automáticos de vencimento de planos

💡 Caso tenha dúvidas, entre em contato com o suporte.
  `.trim()
  
  await sock.sendMessage(jid, { text: menu })
}

/**
 * Verifica periodicamente o arquivo de lembretes e envia mensagens
 */
function startReminderChecker(sock) {
  console.log('🔔 Iniciando verificador de lembretes...')
  
  // Verifica a cada 5 minutos
  setInterval(async () => {
    try {
      if (!fs.existsSync(REMINDERS_FILE)) {
        return
      }

      const data = fs.readFileSync(REMINDERS_FILE, 'utf8')
      const reminders = JSON.parse(data)
      
      for (const [key, reminder] of Object.entries(reminders)) {
        // Pula lembretes já enviados
        if (reminder.sent) continue
        
        // Envia apenas se tiver telefone
        if (!reminder.customer_phone) {
          console.log(`⚠️  Sem telefone para ${reminder.customer_name}`)
          continue
        }
        
        // Formata número para WhatsApp
        const phone = formatPhoneNumber(reminder.customer_phone)
        if (!phone) {
          console.log(`⚠️  Telefone inválido: ${reminder.customer_phone}`)
          continue
        }
        
        // Envia lembrete
        await sendSubscriptionReminder(sock, phone, reminder)
        
        // Marca como enviado
        reminders[key].sent = true
        reminders[key].sent_at = new Date().toISOString()
        
        console.log(`✅ Lembrete enviado para ${reminder.customer_name} (${phone})`)
      }
      
      // Salva estado atualizado
      fs.writeFileSync(REMINDERS_FILE, JSON.stringify(reminders, null, 2))
      
    } catch (error) {
      console.error('❌ Erro ao processar lembretes:', error.message)
    }
  }, 5 * 60 * 1000) // 5 minutos
  
  // Executa uma verificação imediata
  console.log('🔍 Executando primeira verificação...')
  setTimeout(async () => {
    try {
      if (!fs.existsSync(REMINDERS_FILE)) {
        console.log('📝 Arquivo de lembretes ainda não criado')
        return
      }

      const data = fs.readFileSync(REMINDERS_FILE, 'utf8')
      const reminders = JSON.parse(data)
      
      const pending = Object.values(reminders).filter(r => !r.sent).length
      console.log(`📊 ${pending} lembretes pendentes`)
      
    } catch (error) {
      console.error('❌ Erro na verificação inicial:', error.message)
    }
  }, 5000)
}

/**
 * Envia lembrete de assinatura via WhatsApp
 */
async function sendSubscriptionReminder(sock, phone, reminder) {
  const daysText = reminder.days_until_due === 1 ? 'amanhã' : `em ${reminder.days_until_due} dias`
  
  const message = `
🔔 *Lembrete de Vencimento - ${reminder.tenant_name}*

Olá, *${reminder.customer_name}*! 👋

Seu plano *${reminder.plan_name}* vence *${daysText}* (${formatDate(reminder.due_date)}).

💰 Valor: *R$ ${formatMoney(reminder.amount)}*

${reminder.payment_url ? `🔗 Link para renovação:\n${reminder.payment_url}\n\n` : ''}📲 Renove agora para continuar aproveitando todos os benefícios!

_Mensagem automática do PagBy_
  `.trim()
  
  try {
    await sock.sendMessage(phone, { text: message })
  } catch (error) {
    console.error(`❌ Erro ao enviar para ${phone}:`, error.message)
    throw error
  }
}

/**
 * Formata número de telefone para WhatsApp
 */
function formatPhoneNumber(phone) {
  if (!phone) return null
  
  // Remove tudo exceto números
  let cleaned = phone.replace(/\D/g, '')
  
  // Se começar com 0, remove
  if (cleaned.startsWith('0')) {
    cleaned = cleaned.substring(1)
  }
  
  // Se não começar com 55 (código do Brasil), adiciona
  if (!cleaned.startsWith('55')) {
    cleaned = '55' + cleaned
  }
  
  // Formato WhatsApp: numero@s.whatsapp.net
  return cleaned + '@s.whatsapp.net'
}

/**
 * Formata data
 */
function formatDate(dateString) {
  const date = new Date(dateString)
  return date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' })
}

/**
 * Formata valor monetário
 */
function formatMoney(value) {
  return parseFloat(value).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

// Inicia o bot
main().catch(err => {
  console.error('❌ Erro fatal:', err)
  process.exit(1)
})

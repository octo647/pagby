import makeWASocket, { useMultiFileAuthState, fetchLatestBaileysVersion } from '@whiskeysockets/baileys'
import qrcode from 'qrcode-terminal'
import fs from 'fs'

// Armazena o estado de cada conversa: { jid: { step: 0, data: {} } }
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
      console.log('Escaneie o QR code acima com o WhatsApp.')
    }
    if (connection === 'close') {
      const shouldReconnect = lastDisconnect?.error?.output?.statusCode !== 401
      if (shouldReconnect) main()
    }
    if (connection === 'open') {
      console.log('✅ Bot conectado ao WhatsApp - aguardando mensagens...')
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
  
  // Fluxo de perguntas (sem key 'inicio', começando direto)
  const questions = [
    '👋 Olá! Seja bem-vindo ao *Pagby*!\n\nVi que você veio da nossa campanha. Vou fazer algumas perguntinhas rápidas para te ajudar melhor! 😊\n\n📝 Qual é o seu *nome*?',
    'Prazer, [NOME]! 🎉\n\n🏢 Qual é o nome do seu *salão ou barbearia*?',
    'Ótimo! E qual é o seu *WhatsApp* para contato?\n(Exemplo: 55 99 99999-9999)',
    'Perfeito! Agora me conta:\n\n🤔 Você já usa algum *sistema de agendamento* hoje? Se sim, qual?',
    'Entendi! E qual o principal *desafio* que você enfrenta na gestão do seu salão?\n(Agendamentos, controle financeiro, comissões, etc.)',
    'Show! Última pergunta:\n\n📅 Qual o melhor *dia e horário* para uma demonstração rápida do Pagby? (10-15 min)'
  ]
  
  const dataKeys = ['nome', 'salao', 'telefone', 'sistema_atual', 'desafio', 'horario']
  
  // Primeira mensagem: envia boas-vindas
  if (conv.step === 0) {
    await sendMessage(sock, jid, questions[0])
    conv.step++
    return
  }
  
  // Armazena resposta da pergunta anterior
  const dataIndex = conv.step - 1
  if (dataIndex < dataKeys.length) {
    conv.data[dataKeys[dataIndex]] = text
  }
  
  // Envia próxima pergunta ou finaliza
  if (conv.step < questions.length) {
    let nextMsg = questions[conv.step]
    // Substitui placeholders com os dados já coletados
    nextMsg = nextMsg.replace('[NOME]', conv.data.nome || '')
    
    await sendMessage(sock, jid, nextMsg)
    conv.step++
  } else {
    // Finaliza conversa e salva lead
    await sendMessage(sock, jid, 
      `🎊 Pronto, ${conv.data.nome}!\n\n` +
      `Muito obrigado pelas informações. Em breve nossa equipe entrará em contato no horário que você indicou para agendar a demonstração.\n\n` +
      `💼 Enquanto isso, conheça mais sobre o Pagby em: *https://pagby.com.br*\n\n` +
      `Até logo! 👋`
    )
    
    // Salva lead em arquivo JSON
    saveLead(jid, conv.data)
    
    // Reinicia conversa
    delete conversations[jid]
    
    console.log(`✅ Lead capturado: ${conv.data.nome} - ${conv.data.salao}`)
  }
}

async function sendMessage(sock, jid, msg) {
  await sock.sendMessage(jid, { text: msg })
}

function saveLead(jid, data) {
  const lead = {
    jid,
    timestamp: new Date().toISOString(),
    ...data
  }
  
  // Salva em arquivo leads.json
  let leads = []
  if (fs.existsSync('leads.json')) {
    leads = JSON.parse(fs.readFileSync('leads.json', 'utf8'))
  }
  leads.push(lead)
  fs.writeFileSync('leads.json', JSON.stringify(leads, null, 2))
  
  console.log('💾 Lead salvo em leads.json')
}

main()

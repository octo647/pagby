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
const PHONE_MAP_FILE = path.join(__dirname, '../../storage/app/whatsapp_phone_map.json')

// Armazena o estado de cada conversa: { jid: { step: 0, data: {} } }
const conversations = {}

// Carrega mapeamento de telefones
let phoneMap = {}
if (fs.existsSync(PHONE_MAP_FILE)) {
  try {
    phoneMap = JSON.parse(fs.readFileSync(PHONE_MAP_FILE, 'utf8'))
  } catch (e) {
    console.warn('⚠️  Erro ao carregar mapeamento de telefones:', e.message)
  }
}

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
      console.log('✅ Bot conectado ao WhatsApp!')
      
      // Inicia processador de comandos Laravel
      startCommandProcessor(sock)
    }
  })

  // Escuta mensagens recebidas
  sock.ev.on('messages.upsert', async ({ messages }) => {
    for (const msg of messages) {
      if (!msg.message || msg.key.fromMe) continue
      
      const jid = msg.key.remoteJid
      const text = msg.message.conversation || msg.message.extendedTextMessage?.text || ''
      
      console.log(`📩 Mensagem de ${jid}: ${text}`)
      
      // Salva/atualiza mapeamento de telefone e ativa WhatsApp automaticamente
      const isExistingUser = await savePhoneMapping(jid)
      
      // Distingue entre usuário de tenant e lead comercial
      await handleConversation(sock, jid, text, isExistingUser)
    }
  })
}

async function handleConversation(sock, jid, text, isExistingUser) {
  const lowerText = text.toLowerCase().trim()

  // ========== USUÁRIOS DE TENANTS (já cadastrados) ==========
  if (isExistingUser) {
    // Resposta simples para usuários que só querem notificações
    if (!conversations[jid] || conversations[jid].step === 0) {
      await sock.sendMessage(jid, {
        text: '✅ *Notificações ativadas com sucesso!*\n\n' +
              'Você receberá lembretes importantes sobre:\n' +
              '• Vencimento de planos\n' +
              '• Renovações e pagamentos\n' +
              '• Outras notificações do sistema\n\n' +
              '_Seus lembretes estão ativos!_ 🔔'
      })
      
      if (!conversations[jid]) {
        conversations[jid] = { step: 1, data: {}, userType: 'tenant' }
      } else {
        conversations[jid].step = 1
        conversations[jid].userType = 'tenant'
      }
      return
    }
    
    // Mensagens subsequentes de usuários de tenant
    if (lowerText === 'menu' || lowerText === 'ajuda') {
      await sock.sendMessage(jid, {
        text: '📋 *Menu de Opções*\n\n' +
              '✅ Suas notificações estão ativas\n' +
              '🔔 Você receberá lembretes automáticos\n\n' +
              '💬 Para suporte, entre em contato com seu salão.'
      })
      return
    }
    
    await sock.sendMessage(jid, {
      text: '👋 Obrigado pela mensagem!\n\n' +
            'Seus lembretes estão ativos. Para dúvidas sobre agendamentos ou serviços, ' +
            'entre em contato diretamente com seu salão.'
    })
    return
  }

  // ========== LEADS COMERCIAIS (novos prospects) ==========
  
  // Inicializa conversa se não existir
  if (!conversations[jid]) {
    conversations[jid] = { step: 0, data: {}, userType: 'lead' }
  }

  const conv = conversations[jid]
  conv.userType = 'lead' // Marca como lead comercial

  // ========== COMANDOS ESPECIAIS ==========
  
  if (lowerText === 'menu' || lowerText === 'ajuda') {
    await sock.sendMessage(jid, {
      text: '*🤖 Bot PagBy - Menu*\n\n' +
            '📋 Opções disponíveis:\n\n' +
            '1️⃣ *cadastro* - Cadastrar seu salão\n' +
            '2️⃣ *info* - Informações sobre o PagBy\n' +
            '3️⃣ *demo* - Agendar demonstração\n' +
            '4️⃣ *planos* - Ver planos e preços\n' +
            '5️⃣ *suporte* - Falar com suporte\n\n' +
            '✅ *Lembretes ativados* para notificações automáticas!'
    })
    return
  }

  if (lowerText === 'info' || lowerText === 'informações') {
    await sock.sendMessage(jid, {
      text: '*💼 PagBy - Sistema de Gestão para Salões*\n\n' +
            '✅ Agendamento online\n' +
            '✅ Controle financeiro\n' +
            '✅ Gestão de funcionários\n' +
            '✅ Relatórios e comandas\n' +
            '✅ WhatsApp integrado\n\n' +
            '✅ Site personalizado para seu salão!\n' +           
            '🎁 7 dias grátis para testar!\n\n' +
            'Digite *cadastro* para começar!'
    })
    return
  }

  if (lowerText === 'planos' || lowerText === 'precos' || lowerText === 'preços') {
    await sock.sendMessage(jid, {
      text: `*💎 Planos PagBy*\n\n* Preços para 1 profissional:*\n\n*Mensal: R\$ 60,00*\n*Trimestral: 144,00*\n *Semestral: 252,00*\n *Anual: 432,00*\n\n🎁 *7 dias grátis em qualquer plano!*\n\nDigite *cadastro* para começar!`
    })
    return
  }

  if (lowerText === 'cancelar' || lowerText === 'sair' || lowerText === 'parar') {
    conversations[jid] = { step: 0, data: {} }
    await sock.sendMessage(jid, {
      text: '❌ Cadastro cancelado.\n\nDigite *menu* para ver outras opções!'
    })
    return
  }

  // ========== FLUXO DE CADASTRO DE LEAD ==========
  
  if (lowerText === 'cadastro' || lowerText === 'cadastrar' || lowerText === 'demo' || lowerText === 'demonstração' || lowerText === 'demonstracao') {
    conversations[jid] = { 
      step: 1, 
      data: { 
        started_at: new Date().toISOString(),
        tipo: lowerText === 'demo' || lowerText === 'demonstração' || lowerText === 'demonstracao' ? 'demo' : 'cadastro'
      } 
    }
    await sock.sendMessage(jid, {
      text: '🎉 *Ótimo! Vamos começar seu cadastro no PagBy!*\n\n' +
            'Primeiro, qual é o *nome do seu salão/barbearia*?\n\n' +
            '_(Digite "cancelar" a qualquer momento para sair)_'
    })
    return
  }

  // ========== ETAPAS DO CADASTRO ==========

  switch (conv.step) {
    case 0:
      // Primeira mensagem do usuário - boas-vindas
      await sock.sendMessage(jid, {
        text: '👋 *Olá! Bem-vindo ao PagBy!*\n\n' +
              '🏆 *O melhor sistema para gestão de salões e barbearias*\n\n' +
              '✨ O que você gostaria de fazer?\n\n' +
              '• Digite *cadastro* - Cadastrar seu salão\n' +
              '• Digite *demo* - Agendar demonstração\n' +
              '• Digite *info* - Saber mais sobre o PagBy\n' +
              '• Digite *planos* - Ver planos e preços\n' +
              '• Digite *menu* - Ver todas as opções\n\n' +
              '✅ Lembretes ativados para você!'
      })
      conv.step = 0.5 // Aguardando escolha
      break

    case 0.5:
      // Aguardando comando inicial
      await sock.sendMessage(jid, {
        text: '🤔 Não entendi...\n\n' +
              'Digite *cadastro* para cadastrar seu salão\n' +
              'ou *menu* para ver todas as opções!'
      })
      break

    case 1:
      // Recebeu nome do salão
      conv.data.nome_salao = text
      conv.step = 2
      await sock.sendMessage(jid, {
        text: `✅ *${text}* - que nome legal!\n\n` +
              'Agora me diga: *qual é o seu nome?*\n' +
              '_(Nome do proprietário/responsável)_'
      })
      break

    case 2:
      // Recebeu nome do proprietário
      conv.data.nome_proprietario = text
      conv.step = 3
      await sock.sendMessage(jid, {
        text: `Prazer, *${text}*! 😊\n\n` +
              'Qual é o *telefone de contato* do salão?\n' +
              '_(Pode ser celular ou WhatsApp)_'
      })
      break

    case 3:
      // Recebeu telefone
      conv.data.telefone = text
      conv.step = 4
      await sock.sendMessage(jid, {
        text: '📍 Perfeito!\n\n' +
              'Em qual *cidade* está localizado o salão?'
      })
      break

    case 4:
      // Recebeu cidade
      conv.data.cidade = text
      conv.step = 5
      await sock.sendMessage(jid, {
        text: '💻 Ótimo!\n\n' +
              'Vocês já utilizam algum *sistema de gestão* atualmente?\n\n' +
              '_(Ex: Excel, agenda de papel, outro app, etc.)_'
      })
      break

    case 5:
      // Recebeu sistema atual
      conv.data.sistema_atual = text
      conv.step = 6
      await sock.sendMessage(jid, {
        text: '👥 Entendi!\n\n' +
              'Quantos *funcionários/profissionais* trabalham no salão?\n' +
              '_(Apenas um número)_'
      })
      break

    case 6:
      // Recebeu número de funcionários
      conv.data.num_funcionarios = text
      conv.step = 7
      await sock.sendMessage(jid, {
        text: '🎯 Última pergunta!\n\n' +
              'Qual é o *principal desafio* que vocês enfrentam hoje na gestão?\n\n' +
              '_(Ex: controle financeiro, agendamentos, gestão de equipe, etc.)_'
      })
      break

    case 7:
      // Recebeu desafio - FINALIZA CADASTRO
      conv.data.desafio = text
      conv.data.completed_at = new Date().toISOString()
      
      // Salva no leads.json
      saveLead(jid, conv.data)
      
      // Calcula preço estimado
      const numFunc = parseInt(conv.data.num_funcionarios) || 1
      const precoBasico = (config('pricing.basic_per_employee') || 60.00)
      const precoMensal = precoBasico * (1 + 0.3 * (numFunc - 1))
      const precoTrimestral = precoMensal * 0.8 * 3
      const precoSemestral = precoMensal * 0.7 * 6
      const precoAnual = precoMensal * 0.6 * 12

      await sock.sendMessage(jid, {
        text: '🎉 *Cadastro concluído com sucesso!*\n\n' +
              '📋 *Resumo:*\n' +
              `• Salão: ${conv.data.nome_salao}\n` +
              `• Responsável: ${conv.data.nome_proprietario}\n` +
              `• Cidade: ${conv.data.cidade}\n` +
              `• Funcionários: ${conv.data.num_funcionarios}\n\n` +
              '💰 *Valores para seu salão:*\n' +
              `• Plano Mensal: R$ ${precoMensal.toFixed(2)}/mês\n` +
              `• Plano Trimestral: R$ ${precoTrimestral.toFixed(2)} (equivalente a R$ ${(precoTrimestral/3).toFixed(2)}/mês)\n\n` +
              `• Plano Semestral: R$ ${precoSemestral.toFixed(2)} (equivalente a R$ ${(precoSemestral/6).toFixed(2)}/mês)\n` +
              `• Plano Anual: R$ ${precoAnual.toFixed(2)} (equivalente a R$ ${(precoAnual/12).toFixed(2)}/mês)\n\n` +
              
              '_(Teste por 7 dias sem compromisso)_\n\n' +
              '🚀 Nossa equipe entrará em contato em breve para:\n' +
              '✅ Agendar uma demonstração\n' +
              '✅ Tirar suas dúvidas\n' +
              '✅ Configurar seu sistema\n\n' +
              '📱 *Acesse agora:* pagby.com.br\n\n' +
              '_Obrigado pelo interesse no PagBy!_'
      })
      
      // Reseta conversa
      conversations[jid] = { step: 0, data: {} }
      
      // Notifica admin (opcional)
      console.log('🎯 NOVO LEAD CADASTRADO:', conv.data)
      break

    default:
      // Qualquer outro estado
      await sock.sendMessage(jid, {
        text: '🤔 Desculpe, algo deu errado.\n\n' +
              'Digite *menu* para começar novamente!'
      })
      conversations[jid] = { step: 0, data: {} }
  }
}

/**
 * Salva lead no arquivo leads.json
 */
function saveLead(jid, data) {
  try {
    const leadsFile = path.join(__dirname, 'leads.json')
    let leads = []
    
    if (fs.existsSync(leadsFile)) {
      leads = JSON.parse(fs.readFileSync(leadsFile, 'utf8'))
    }
    
    leads.push({
      jid: jid,
      timestamp: new Date().toISOString(),
      ...data,
      status: 'novo'
    })
    
    fs.writeFileSync(leadsFile, JSON.stringify(leads, null, 2))
    console.log('✅ Lead salvo com sucesso!')
  } catch (error) {
    console.error('❌ Erro ao salvar lead:', error.message)
  }
}

/**
 * Processa comandos do Laravel a cada 30 segundos
 */
function startCommandProcessor(sock) {
  console.log('🔔 Processador de comandos Laravel iniciado')
  
  setInterval(async () => {
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
          } else if (cmd.type === 'payment_reminder') {
            await sendPaymentReminder(sock, cmd)
            executed.push(cmd)
            console.log(`✅ Lembrete de pagamento enviado para ${cmd.customer_name}`)
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
 * Envia mensagem via comando
 */
async function sendMessageCommand(sock, cmd) {
  const { to, message } = cmd
  
  // Formata telefone
  const phone = formatPhoneNumber(to)
  if (!phone) {
    throw new Error(`Telefone inválido: ${to}`)
  }
  
  await sock.sendMessage(phone, { text: message })
}

/**
 * Envia lembrete de agendamento
 */
async function sendAppointmentReminder(sock, cmd) {
  const phone = formatPhoneNumber(cmd.customer_phone)
  if (!phone) {
    throw new Error(`Telefone inválido: ${cmd.customer_phone}`)
  }
  
  const message = `🔔 *Lembrete de Agendamento*

Olá, *${cmd.customer_name}*! 👋

📅 Você tem um horário marcado:

🕐 *Data e hora:* ${cmd.appointment_date} às ${cmd.appointment_time}
💈 *Profissional:* ${cmd.employee_name}
✂️ *Serviço:* ${cmd.service_names}
📍 *Local:* ${cmd.branch_name}

${cmd.observation ? `📝 *Observação:* ${cmd.observation}\n\n` : ''}${cmd.has_pending_payment ? `💰 *Valor:* R$ ${cmd.total_price}\n⚠️ Pagamento pendente\n\n` : ''}Nos vemos em breve! 😊

_Mensagem automática de ${cmd.tenant_name}_`

  await sock.sendMessage(phone, { text: message })
}

/**
 * Envia lembrete de pagamento/renovação
 */
async function sendPaymentReminder(sock, cmd) {
  const phone = formatPhoneNumber(cmd.customer_phone)
  if (!phone) {
    throw new Error(`Telefone inválido: ${cmd.customer_phone}`)
  }
  
  const message = `🔔 *Lembrete de Vencimento*

Olá, *${cmd.customer_name}*! 👋

Seu plano *${cmd.plan_name}* vence ${cmd.days_until_due === 0 ? '*hoje*' : `*em ${cmd.days_until_due} ${cmd.days_until_due === 1 ? 'dia' : 'dias'}*`} (${cmd.due_date}).

💰 Valor: *R$ ${cmd.amount.toFixed(2)}*

🔗 Clique para renovar:
${cmd.payment_url}

📲 Renove agora para continuar aproveitando todos os benefícios!

_Mensagem automática de ${cmd.tenant_name}_`

  await sock.sendMessage(phone, { text: message })
}

/**
 * Formata número para WhatsApp
 */
function formatPhoneNumber(phone) {
  if (!phone) return null
  
  let cleaned = phone.replace(/\D/g, '')
  
  if (cleaned.startsWith('0')) {
    cleaned = cleaned.substring(1)
  }
  
  if (!cleaned.startsWith('55')) {
    cleaned = '55' + cleaned
  }
  
  return cleaned + '@s.whatsapp.net'
}

// Salva conversas periodicamente (opcional)
setInterval(() => {
  const leadsFile = 'leads.json'
  fs.writeFileSync(leadsFile, JSON.stringify(conversations, null, 2))
}, 60000)

/**
 * Salva mapeamento de telefone para WhatsApp ID
 * Retorna true se o usuário já existe em algum tenant
 */
/**
 * Normaliza número de telefone para apenas dígitos
 * Remove +, espaços, hífens, parênteses, etc.
 */
function normalizePhone(phone) {
  if (!phone) return ''
  return phone.toString().replace(/\D/g, '') // Remove tudo que não é dígito
}

async function savePhoneMapping(jid) {
  try {
    // Extrai número sem @s.whatsapp.net ou @lid
    const rawNumber = jid.split('@')[0]
    const normalizedRaw = normalizePhone(rawNumber)
    
    let isExistingUser = false
    
    // Se já está mapeado, verifica se precisa ativar
    if (phoneMap[normalizedRaw]) {
      // Se já mapeado, busca número brasileiro no map e tenta ativar
      for (const [phone, mappedJid] of Object.entries(phoneMap)) {
        const normalizedPhone = normalizePhone(phone)
        if (mappedJid === jid && normalizedPhone.length >= 10 && normalizedPhone.length <= 11) {
          const activated = await markUserWhatsAppActivated(normalizedPhone)
          if (activated) {
            isExistingUser = true
          }
          break
        }
      }
      return isExistingUser
    }
    
    // Salva o mapeamento com número normalizado
    phoneMap[normalizedRaw] = jid
    
    // Se for número brasileiro (10-12 dígitos), salva também sem 55
    if (normalizedRaw.startsWith('55') && normalizedRaw.length >= 12) {
      const phoneWithout55 = normalizedRaw.substring(2)
      phoneMap[phoneWithout55] = jid
      console.log(`📱 Número encontrado no mapeamento: ${phoneWithout55} (normalizado de ${rawNumber})`)
      
      // Tenta ativar no sistema com múltiplas variações
      let activated = await markUserWhatsAppActivated(phoneWithout55)
      
      // Se não encontrou, tenta com 9 dígito removido (números antigos)
      if (!activated && phoneWithout55.length === 11 && phoneWithout55[2] === '9') {
        const phoneWithout9 = phoneWithout55.substring(0, 2) + phoneWithout55.substring(3)
        console.log(`📱 Tentando variação sem 9º dígito: ${phoneWithout9}`)
        activated = await markUserWhatsAppActivated(phoneWithout9)
      }
      
      if (activated) {
        isExistingUser = true
      }
    }
    
    // Salva o arquivo
    fs.writeFileSync(PHONE_MAP_FILE, JSON.stringify(phoneMap, null, 2))
    
    return isExistingUser
  } catch (error) {
    console.error('❌ Erro ao salvar mapeamento:', error.message)
    return false
  }
}

/**
 * Marca usuário como ativado para WhatsApp em todos os tenants
 * Retorna true se encontrou e ativou o usuário
 */
async function markUserWhatsAppActivated(phone) {
  const tenants = [
    'magic-club',
    'dumont',
    'villebelle',
    'labelle',
    'salao-cowboy',
    'bar',
    'barbearia-vilomar',
    'bicholegal',
    'dudu',
    'pets-cia',
    'barba-e-cabelo'
  ]
  
  for (const tenant of tenants) {
    try {
      const response = await fetch(`https://${tenant}.pagby.com.br/api/whatsapp/activate`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ phone })
      })
      
      const data = await response.json()
      
      if (data.success && data.updated > 0) {
        console.log(`✅ WhatsApp ativado para ${phone} no tenant ${tenant}`)
        return true // Usuário encontrado e ativado
      }
    } catch (error) {
      // Ignora erros silenciosamente (tenant pode não existir)
    }
  }
  
  console.log(`ℹ️  Número ${phone} não encontrado em nenhum tenant (possível lead comercial)`)
  return false // Não é usuário de tenant
}

// Inicia o bot
main().catch(err => {
  console.error('❌ Erro fatal:', err)
  process.exit(1)
})

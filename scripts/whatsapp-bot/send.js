import makeWASocket, { useMultiFileAuthState, fetchLatestBaileysVersion } from '@whiskeysockets/baileys';
import process from 'process';

async function main() {
  const { state, saveCreds } = await useMultiFileAuthState('auth_info_baileys');
  const { version } = await fetchLatestBaileysVersion();
  const sock = makeWASocket({
    version,
    auth: state,
    printQRInTerminal: false,
    connectTimeoutMs: 60000,
    defaultQueryTimeoutMs: undefined,
    keepAliveIntervalMs: 30000,
    markOnlineOnConnect: true,
  });

  sock.ev.on('creds.update', saveCreds);

  const phone = process.argv[2];
  const message = process.argv[3] || 'Mensagem de teste';
  if (!phone) {
    console.error('Uso: node send.js <numero_completo> "Mensagem"');
    process.exit(1);
  }
  const jid = phone.replace(/\D/g, '') + '@s.whatsapp.net';
  await sock.sendMessage(jid, { text: message });
  console.log('Mensagem enviada para', jid);
  process.exit(0);
}

main();
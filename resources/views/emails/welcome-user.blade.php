<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%); padding: 40px 30px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;">
                                Bem-vindo ao {{ $tenantName }}! 👋
                            </h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #333333;">
                                Olá, <strong>{{ $user->name }}</strong>!
                            </p>

                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #333333;">
                                Sua conta foi criada com sucesso! Estamos muito felizes em ter você conosco.
                            </p>

                            <div style="background-color: #f9fafb; border-left: 4px solid #ec4899; padding: 20px; margin: 30px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 15px 0; font-size: 18px; color: #ec4899;">
                                    🔔 Ative os Lembretes via WhatsApp
                                </h2>
                                <p style="margin: 0 0 15px 0; font-size: 14px; line-height: 1.6; color: #666666;">
                                    Receba avisos de vencimento de planos e outras notificações importantes diretamente no seu WhatsApp!
                                </p>
                                
                                <p style="margin: 0 0 15px 0; font-size: 14px; line-height: 1.6; color: #666666;">
                                    <strong>Como ativar:</strong>
                                </p>
                                
                                <ol style="margin: 0 0 15px 0; padding-left: 20px; font-size: 14px; line-height: 1.8; color: #666666;">
                                    <li>Clique no botão abaixo</li>
                                    <li>Envie qualquer mensagem para nosso WhatsApp</li>
                                    <li>Pronto! Os lembretes serão ativados automaticamente</li>
                                </ol>

                                <div style="text-align: center; margin: 25px 0;">
                                    <a href="https://wa.me/{{ $whatsappNumber }}?text=ATIVAR" 
                                       style="display: inline-block; background-color: #25D366; color: #ffffff; text-decoration: none; padding: 15px 40px; border-radius: 25px; font-size: 16px; font-weight: bold; box-shadow: 0 2px 4px rgba(37, 211, 102, 0.3);">
                                        📱 Ativar Lembretes WhatsApp
                                    </a>
                                </div>

                                <p style="margin: 15px 0 0 0; font-size: 12px; text-align: center; color: #999999;">
                                    Nosso WhatsApp: <strong>(32) 99961-2957</strong>
                                </p>
                            </div>

                            <div style="margin: 30px 0; padding: 20px; background-color: #eff6ff; border-radius: 8px;">
                                <h3 style="margin: 0 0 10px 0; font-size: 16px; color: #1e40af;">
                                    💡 Dicas para começar:
                                </h3>
                                <ul style="margin: 0; padding-left: 20px; font-size: 14px; line-height: 1.8; color: #666666;">
                                    <li>Complete seu perfil com foto e informações de contato</li>
                                    <li>Explore os serviços disponíveis</li>
                                    <li>Agende seus horários com facilidade</li>
                                    @if($user->phone)
                                    <li>Ative os lembretes via WhatsApp para não perder nenhum vencimento</li>
                                    @endif
                                </ul>
                            </div>

                            <p style="margin: 30px 0 0 0; font-size: 14px; line-height: 1.6; color: #666666;">
                                Se tiver alguma dúvida, estamos sempre à disposição para ajudar!
                            </p>

                            <p style="margin: 20px 0 0 0; font-size: 14px; line-height: 1.6; color: #666666;">
                                Atenciosamente,<br>
                                <strong>Equipe {{ $tenantName }}</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 20px 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; font-size: 12px; color: #999999;">
                                © {{ date('Y') }} {{ $tenantName }} - Powered by PagBy
                            </p>
                            <p style="margin: 10px 0 0 0; font-size: 12px; color: #999999;">
                                Este é um email automático, por favor não responda.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

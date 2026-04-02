<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credenciais de Acesso</title>
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
                                🎉 Parabéns, {{ $contact->owner_name }}!
                            </h1>
                            <p style="margin: 10px 0 0 0; color: #ffffff; font-size: 16px;">
                                Seu salão <strong>{{ $tenant->fantasy_name }}</strong> está no ar!
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #333333;">
                                Olá, <strong>{{ $contact->owner_name }}</strong>!
                            </p>

                            <p style="margin: 0 0 30px 0; font-size: 16px; line-height: 1.6; color: #333333;">
                                Seu sistema de gerenciamento foi criado com sucesso! Use as credenciais abaixo para fazer seu primeiro acesso:
                            </p>

                            <!-- Credenciais Box -->
                            <div style="background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%); border: 2px solid #818cf8; border-radius: 12px; padding: 30px; margin: 30px 0;">
                                <h2 style="margin: 0 0 20px 0; font-size: 20px; color: #4338ca; text-align: center;">
                                    🔑 Suas Credenciais de Acesso
                                </h2>
                                
                                <table width="100%" cellpadding="10" cellspacing="0">
                                    <tr>
                                        <td style="width: 100px; font-weight: bold; color: #4338ca; font-size: 14px; vertical-align: top;">
                                            📧 E-mail:
                                        </td>
                                        <td style="font-size: 16px; color: #1e293b; word-break: break-all;">
                                            <strong>{{ $contact->email }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 100px; font-weight: bold; color: #4338ca; font-size: 14px; vertical-align: top;">
                                            🔐 Senha:
                                        </td>
                                        <td style="font-size: 20px; color: #1e293b; letter-spacing: 2px; font-family: 'Courier New', monospace;">
                                            <strong>{{ $password }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 100px; font-weight: bold; color: #4338ca; font-size: 14px; vertical-align: top;">
                                            🌐 URL:
                                        </td>
                                        <td style="font-size: 14px; color: #1e293b; word-break: break-all;">
                                            <a href="{{ $loginUrl }}" style="color: #4338ca; text-decoration: none;">
                                                {{ $loginUrl }}
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- CTA Button -->
                            <div style="text-align: center; margin: 35px 0;">
                                <a href="{{ $loginUrl }}" 
                                   style="display: inline-block; background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%); color: #ffffff; text-decoration: none; padding: 16px 50px; border-radius: 30px; font-size: 18px; font-weight: bold; box-shadow: 0 4px 6px rgba(236, 72, 153, 0.3);">
                                    🚀 Acessar Meu Painel
                                </a>
                            </div>

                            <!-- Security Warning -->
                            <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 30px 0; border-radius: 4px;">
                                <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #92400e;">
                                    <strong>⚠️ Importante:</strong> Por motivos de segurança, recomendamos que você altere sua senha no primeiro acesso. 
                                    Vá em <strong>Perfil → Alterar Senha</strong> após fazer login.
                                </p>
                            </div>

                            <!-- Getting Started -->
                            <div style="margin: 30px 0; padding: 20px; background-color: #f0fdf4; border-radius: 8px; border: 1px solid #86efac;">
                                <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #15803d;">
                                    🎯 Primeiros Passos
                                </h3>
                                <ol style="margin: 0; padding-left: 20px; font-size: 14px; line-height: 2; color: #166534;">
                                    <li>Acesse o sistema com suas credenciais</li>
                                    <li>Complete o onboarding guiado (7 passos simples)</li>
                                    <li>Cadastre seus funcionários e serviços</li>
                                    <li>Configure os horários de atendimento</li>
                                    <li>Personalize a página inicial do seu salão</li>
                                </ol>
                            </div>

                            <!-- Support -->
                            <div style="margin: 30px 0; padding: 20px; background-color: #eff6ff; border-radius: 8px;">
                                <h3 style="margin: 0 0 10px 0; font-size: 16px; color: #1e40af;">
                                    💬 Precisa de Ajuda?
                                </h3>
                                <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #1e3a8a;">
                                    Estamos aqui para ajudá-lo! Entre em contato conosco:
                                </p>
                                <p style="margin: 10px 0 0 0; font-size: 14px; color: #1e3a8a;">
                                    📱 WhatsApp: <strong>(32) 9 9829-4948</strong><br>
                                    📧 E-mail: <strong>suportepagby@gmail.com</strong>
                                </p>
                            </div>

                            <p style="margin: 30px 0 0 0; font-size: 14px; line-height: 1.6; color: #666666; text-align: center;">
                                Bem-vindo à família PagBy! 🎊
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 20px 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; font-size: 12px; color: #9ca3af;">
                                © {{ date('Y') }} PagBy - Sistema de Gerenciamento para Salões e Barbearias
                            </p>
                            <p style="margin: 10px 0 0 0; font-size: 12px; color: #9ca3af;">
                                Este é um e-mail automático, por favor não responda.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

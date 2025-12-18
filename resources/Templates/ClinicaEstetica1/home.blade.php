<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ tenant()->fantasy_name ?? 'Clínica de Estética' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Montserrat', sans-serif; margin: 0; background: #f7f6f9; color: #333; }
        header { background: #fff; box-shadow: 0 2px 8px #eee; position: fixed; width: 100%; z-index: 100; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 20px 0; }
        .logo { font-size: 2rem; color: #b48ec7; font-weight: bold; text-decoration: none; }
        .nav-links { display: flex; gap: 30px; list-style: none; }
        .nav-links a { color: #333; text-decoration: none; font-weight: 500; }
        .nav-links a:hover { color: #b48ec7; }
        .btn-agendar { background: #b48ec7; color: #fff; padding: 10px 25px; border-radius: 25px; text-decoration: none; font-weight: 600; }
        .btn-agendar:hover { background: #8e6bb4; }
        .hero { background: linear-gradient(rgba(180,142,199,0.3),rgba(180,142,199,0.3)), url('/images/ClinicaEstetica/ambiente.jpeg') center/cover; color: #fff; height: 70vh; display: flex; align-items: center; justify-content: center; text-align: center; }
        .hero-content { max-width: 600px; }
        .hero h1 { font-size: 3rem; margin-bottom: 20px; }
        .hero p { font-size: 1.2rem; margin-bottom: 30px; }
        .section { padding: 80px 0; }
        .section-title { text-align: center; margin-bottom: 50px; }
        .section-title h2 { color: #b48ec7; font-size: 2.2rem; }
        .servicos-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; }
        .servico-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 8px #eee; padding: 30px; text-align: center; }
        .servico-card img { width: 80px; height: 80px; object-fit: cover; border-radius: 50%; margin-bottom: 15px; }
        .servico-card h3 { color: #b48ec7; margin-bottom: 10px; }
        .sobre { display: flex; flex-wrap: wrap; gap: 40px; align-items: center; }
        .sobre-img { flex: 1; min-width: 300px; }
        .sobre-img img { width: 100%; border-radius: 10px; }
        .sobre-text { flex: 2; }
        .contato-form { background: #fff; max-width: 400px; margin: 0 auto; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px #eee; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px; }
        .btn-enviar { background: #b48ec7; color: #fff; border: none; padding: 12px 30px; border-radius: 25px; font-weight: 600; cursor: pointer; }
        .btn-enviar:hover { background: #8e6bb4; }
        footer { background: #b48ec7; color: #fff; text-align: center; padding: 40px 0 20px; margin-top: 60px; }
        footer { background: #b48ec7; color: #fff; text-align: center; padding: 40px 0 20px; margin-top: 60px; font-size: 1rem; }
        .footer-content { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 40px; max-width: 1200px; margin: 0 auto 30px; }
        .footer-col { flex: 1 1 220px; min-width: 200px; margin-bottom: 20px; }
        .footer-col h4 { color: #fff; margin-bottom: 18px; font-size: 1.15rem; letter-spacing: 1px; }
        .footer-col ul { list-style: none; padding: 0; margin: 0; }
        .footer-col ul li { margin-bottom: 10px; }
        .footer-col ul li a { color: #e0d6ec; text-decoration: none; transition: color 0.2s; }
        .footer-col ul li a:hover { color: #fff; text-decoration: underline; }
        .footer-col p { color: #e0d6ec; font-size: 0.97rem; margin-bottom: 18px; }
        .social-links { margin-top: 10px; }
        .social-links a { color: #fff; margin: 0 8px; font-size: 1.3rem; transition: color 0.2s; }
        .social-links a:hover { color: #f7c873; }
        .footer-bottom { border-top: 1px solid #d1bfe3; padding-top: 18px; margin-top: 18px; font-size: 0.95rem; color: #e0d6ec; }
        @media (max-width: 900px) { .footer-content { flex-direction: column; align-items: center; gap: 0; } .footer-col { min-width: 0; text-align: center; } }
        @media (max-width: 600px) { footer { padding: 30px 0 10px; font-size: 0.97rem; } .footer-content { gap: 0; } }

        .footer-address { display: flex; align-items: center; justify-content: center; gap: 8px; color: #e0d6ec; font-size: 0.98rem; margin-top: 10px; }
        .footer-address i { color: #f7c873; font-size: 1.1rem; margin-right: 6px; }
        .footer-phone { display: flex; align-items: center; justify-content: center; gap: 8px; color: #e0d6ec; font-size: 0.98rem; margin-top: 6px; }
        .footer-phone i { color: #f7c873; font-size: 1.1rem; margin-right: 6px; }
        @media (max-width: 900px) { .sobre { flex-direction: column; } }
        @media (max-width: 600px) { .hero h1 { font-size: 2rem; } .section { padding: 40px 0; } }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="#" class="logo">{{ tenant()->fantasy_name ?? 'Clínica de Estética' }}</a>
                <ul class="nav-links">
                    <li><a href="#inicio">Início</a></li>
                    <li><a href="#servicos">Serviços</a></li>
                    <li><a href="#sobre">Sobre</a></li>
                    <li><a href="#contato">Contato</a></li>
                </ul>
                <a href="/login" class="btn-agendar">Agendar</a>
            </nav>
        </div>
    </header>

    <section class="hero" id="inicio">
        <div class="hero-content">
            <h1>Beleza, Bem-estar e Autoestima</h1>
            <p>Tratamentos faciais, corporais e terapias para realçar sua beleza natural em um ambiente acolhedor e moderno.</p>
            <a href="/login" class="btn-agendar">Agende sua avaliação</a>
        </div>
    </section>

    <section class="section" id="servicos">
        <div class="container">
            <div class="section-title">
                <h2>Nossos Serviços</h2>
                <p>Conheça as especialidades da nossa clínica</p>
            </div>
            <div class="servicos-grid">
                <div class="servico-card">
                    <img src="/images/ClinicaEstetica/pele.jpeg" alt="Limpeza de Pele">
                    <h3>Limpeza de Pele</h3>
                    <p>Remoção de impurezas, hidratação e revitalização para uma pele saudável e luminosa.</p>
                </div>
                <div class="servico-card">
                    <img src="/images/ClinicaEstetica/massagem.jpeg" alt="Massagem Relaxante">
                    <h3>Massagem Relaxante</h3>
                    <p>Alívio do estresse e relaxamento muscular com técnicas especializadas.</p>
                </div>
                <div class="servico-card">
                    <img src="/images/ClinicaEstetica/depilacao.jpeg" alt="Depilação">
                    <h3>Depilação a laser</h3>
                    <p>Procedimentos seguros e confortáveis para uma pele lisa e macia.</p>
                </div>
                <div class="servico-card">
                    <img src="/images/ClinicaEstetica/facial.jpeg" alt="Tratamentos Faciais">
                    <h3>Tratamentos Faciais</h3>
                    <p>Protocolos personalizados para rejuvenescimento e cuidados com a pele.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="sobre">
        <div class="container sobre">
            <div class="sobre-img">
                <img src="/images/ClinicaEstetica/ambiente.jpeg" alt="Nossa Clínica">
            </div>
            <div class="sobre-text">
                <h2>Sobre a Clínica</h2>
                <p>Com profissionais qualificados e equipamentos modernos, oferecemos um atendimento humanizado e resultados de excelência. Nosso objetivo é proporcionar bem-estar, autoestima e satisfação a cada cliente.</p>
                <p>Venha conhecer nosso espaço e descubra como podemos cuidar de você!</p>
            </div>
        </div>
    </section>

    <section class="section" id="contato">
        <div class="container">
            <div class="section-title">
                <h2>Agende seu horário</h2>
                <p>Registre-se para usar nosso serviço de agendamento online.</p>
            </div>
            <div class="contato-form" style="display: flex; justify-content: center; align-items: center;">
                <a href="/register" class="btn-agendar" style="margin: 0 auto;">Registrar</a>
            </div>
        </div>
    </section>

   
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-col">
                    <h4>{{ tenant()->fantasy_name ?? 'Clínica de Estética' }}</h4>
                    <p>O ambiente acolhedor e profissional que você merece.</p>
                    <div class="footer-address">
                        <i class="fas fa-map-marker-alt"></i>
                        {{ tenant()->address ?? '' }}
                        <br>
                        {{ tenant()->neighborhood ?? '' }}
                        <br>
                        {{ tenant()->city }} {{ tenant()->state ?? 'Juiz de Fora, MG' }}
                    </div>
                    <div class="footer-phone">
                        <i class="fas fa-phone"></i>
                        {{ tenant()->phone ?? 'Telefone não cadastrado' }}
                    </div>
                    <div class="social-links">
                        @if(tenant()->instagram)
                        <a href="{{ tenant()->instagram }}" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a>
                        @endif
                        @if(tenant()->tiktok)
                        <a href="{{ tenant()->tiktok }}" target="_blank" rel="noopener"><i class="fab fa-tiktok"></i></a>
                        @endif
                        @if(tenant()->whatsapp)
                        <a href="{{ tenant()->whatsapp }}" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i></a>
                        @endif
                    </div>
                </div>
                
                <div class="footer-col">
                    <h4>LINKS RÁPIDOS</h4>
                    <ul>
                        <li><a href="#inicio">INÍCIO</a></li>
                        <li><a href="#servicos">SERVIÇOS</a></li>
                        <li><a href="#sobre">SOBRE</a></li>
                        <li><a href="#contato">CONTATO</a></li>
                    </ul>
                </div>
                
                
                
                <div class="footer-col">
                    <h4>HORÁRIOS</h4>
                    <ul>
                        <li>SEGUNDA A SEXTA: 9H ÀS 21H</li>
                        <li>SÁBADO: 9H ÀS 18H</li>
                        <li>DOMINGO: 10H ÀS 14H</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} {{ tenant()->fantasy_name ?? 'Clínica de Estética' }}. FEITO COM ❤️ PELA EQUIPE QUE ENTENDE DE BELEZA.</p>
            </div>
        </div>
    </footer>
</body>
</html>
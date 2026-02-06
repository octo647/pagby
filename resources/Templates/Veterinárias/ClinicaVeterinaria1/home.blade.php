<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <link rel="icon" type="image/png" sizes="192x192" href="{{ tenant()->logo }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ tenant()->logo }}">
    <link rel="apple-touch-icon" href="{{ tenant()->logo }}">
    <link rel="manifest" href="/manifest.json">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ tenant()->fantasy_name ?? 'Clínica Veterinária' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
                        @media (max-width: 800px) {
                            .btn-agendar-header {
                                display: none !important;
                            }
                        }
                .hamburger {
                    display: none;
                    flex-direction: column;
                    justify-content: center;
                    width: 32px;
                    height: 32px;
                    cursor: pointer;
                    z-index: 200;
                }
                .hamburger span {
                    height: 4px;
                    width: 100%;
                    background: #4dbb8b;
                    margin: 4px 0;
                    border-radius: 2px;
                    transition: 0.3s;
                }
                @media (max-width: 800px) {
                    .nav-links {
                        display: none !important;
                        position: absolute;
                        top: 70px;
                        left: 0;
                        width: 100vw;
                        background: #fff;
                        flex-direction: column;
                        align-items: center;
                        gap: 0;
                        box-shadow: 0 2px 8px #e0f2e9;
                        padding: 0;
                        margin: 0;
                        z-index: 150;
                    }
                    .nav-links.open {
                        display: flex !important;
                    }
                    .hamburger { display: flex; }
                }
        body { font-family: 'Montserrat', sans-serif; margin: 0; background: #f6faf7; color: #333; }
        header { background: #fff; box-shadow: 0 2px 8px #e0f2e9; position: fixed; width: 100%; z-index: 100; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 20px 0; }
        .logo { font-size: 2rem; color: #4dbb8b; font-weight: bold; text-decoration: none; }
        .nav-links { display: flex; gap: 30px; list-style: none; }
        .nav-links a { color: #333; text-decoration: none; font-weight: 500; }
        .nav-links a:hover { color: #4dbb8b; }
        .btn-agendar { background: #4dbb8b; color: #fff; padding: 10px 25px; border-radius: 25px; text-decoration: none; font-weight: 600; }
        .btn-agendar:hover { background: #368c65; }
        .hero { background: linear-gradient(rgba(40,40,30,0.45),rgba(230,220,200,0.7)), url('/images/ClinicaVeterinaria/hero.jpg') center/cover; color: #fff; height: 70vh; display: flex; align-items: center; justify-content: center; text-align: center; }
        .hero h1, .hero p { color: #fff; text-shadow: 0 2px 12px rgba(0,0,0,0.55); }
        .hero-content { max-width: 600px; }
        .hero h1 { font-size: 3rem; margin-bottom: 20px; }
        .hero p { font-size: 1.2rem; margin-bottom: 30px; }
        .section { padding: 80px 0; }
        .section-title { text-align: center; margin-bottom: 50px; }
        .section-title h2 { color: #4dbb8b; font-size: 2.2rem; }
        .servicos-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; }
        .servico-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 8px #e0f2e9; padding: 30px; text-align: center; }
        .servico-card img { width: 80px; height: 80px; object-fit: cover; border-radius: 50%; margin-bottom: 15px; }
        .servico-card h3 { color: #4dbb8b; margin-bottom: 10px; }
        .sobre { display: flex; flex-wrap: wrap; gap: 40px; align-items: center; }
        .sobre-img { flex: 1; min-width: 300px; }
        .sobre-img img { width: 100%; border-radius: 10px; }
        .sobre-text { flex: 2; }
        .contato-form { background: #fff; max-width: 400px; margin: 0 auto; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px #e0f2e9; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px; }
        .btn-enviar { background: #4dbb8b; color: #fff; border: none; padding: 12px 30px; border-radius: 25px; font-weight: 600; cursor: pointer; }
        .btn-enviar:hover { background: #368c65; }
        footer { background: #4dbb8b; color: #fff; text-align: center; padding: 40px 0 20px; margin-top: 60px; font-size: 1rem; }
        .footer-content { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 40px; max-width: 1200px; margin: 0 auto 30px; }
        .footer-col { flex: 1 1 220px; min-width: 200px; margin-bottom: 20px; }
        .footer-col h4 { color: #fff; margin-bottom: 18px; font-size: 1.15rem; letter-spacing: 1px; }
        .footer-col ul { list-style: none; padding: 0; margin: 0; }
        .footer-col ul li { margin-bottom: 10px; }
        .footer-col ul li a { color: #d6f5e7; text-decoration: none; transition: color 0.2s; }
        .footer-col ul li a:hover { color: #fff; text-decoration: underline; }
        .footer-col p { color: #d6f5e7; font-size: 0.97rem; margin-bottom: 18px; }
        .footer-address, .footer-phone { display: flex; align-items: center; justify-content: center; gap: 8px; color: #d6f5e7; font-size: 0.98rem; margin-top: 10px; }
        .footer-address i, .footer-phone i { color: #fff176; font-size: 1.1rem; margin-right: 6px; }
        .social-links { margin-top: 10px; }
        .social-links a { color: #fff; margin: 0 8px; font-size: 1.3rem; transition: color 0.2s; }
        .social-links a:hover { color: #fff176; }
        .footer-bottom { border-top: 1px solid #b2e5c7; padding-top: 18px; margin-top: 18px; font-size: 0.95rem; color: #d6f5e7; }
        @media (max-width: 900px) { .footer-content { flex-direction: column; align-items: center; gap: 0; } .footer-col { min-width: 0; text-align: center; } }
        @media (max-width: 600px) { footer { padding: 30px 0 10px; font-size: 0.97rem; } .footer-content { gap: 0; } }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="#" class="logo">{{ tenant()->fantasy_name ?? 'Clínica Veterinária' }}</a>
                <div class="hamburger" onclick="document.querySelector('.nav-links').classList.toggle('open')">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <ul class="nav-links">
                    <li><a href="#inicio">Início</a></li>
                    <li><a href="#servicos">Serviços</a></li>
                    <li><a href="#sobre">Sobre</a></li>
                    <li><a href="#contato">Contato</a></li>
                </ul>
                <a href="/login" class="btn-agendar btn-agendar-header">Agendar</a>
            </nav>
        </div>
    </header>

    <section class="hero" id="inicio">
        <div class="hero-content">
            <h1>Cuidado e carinho para seu pet</h1>
            <p>Consultas, vacinas, exames e cirurgias com equipe especializada e estrutura moderna.</p>
            <a href="/login" class="btn-agendar">Agende uma consulta</a>
        </div>
    </section>

    <section class="section" id="servicos">
        <div class="container">
            <div class="section-title">
                <h2>Nossos Serviços</h2>
                <p>Saúde, prevenção e bem-estar animal</p>
            </div>
            <div class="servicos-grid">
                <div class="servico-card">
                    <img src="/images/ClinicaVeterinaria/consulta.jpg" alt="Consultas">
                    <h3>Consultas</h3>
                    <p>Atendimento clínico para cães, gatos e outros pets, com diagnóstico preciso e orientação.</p>
                </div>
                <div class="servico-card">
                    <img src="/images/ClinicaVeterinaria/vacina.jpg" alt="Vacinação">
                    <h3>Vacinação</h3>
                    <p>Vacinas essenciais para proteger seu pet contra doenças comuns e graves.</p>
                </div>
                <div class="servico-card">
                    <img src="/images/ClinicaVeterinaria/exame.jpg" alt="Exames">
                    <h3>Exames Laboratoriais</h3>
                    <p>Exames completos para monitorar a saúde do seu animal e garantir tratamentos eficazes.</p>
                </div>
                <div class="servico-card">
                    <img src="/images/ClinicaVeterinaria/cirurgia.jpg" alt="Cirurgias">
                    <h3>Cirurgias</h3>
                    <p>Procedimentos cirúrgicos com segurança, tecnologia e acompanhamento pós-operatório.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="sobre">
        <div class="container sobre">
            <div class="sobre-img">
                <img src="/images/ClinicaVeterinaria/clinica.jpg" alt="Nossa Clínica">
            </div>
            <div class="sobre-text">
                <h2>Sobre a Clínica</h2>
                <p>Equipe apaixonada por animais, estrutura moderna e atendimento humanizado. Aqui, seu pet é tratado com respeito, carinho e profissionalismo.</p>
                <p>Venha conhecer nosso espaço e proporcione o melhor para seu melhor amigo!</p>
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
                    <h4>{{ tenant()->fantasy_name ?? 'Clínica Veterinária' }}</h4>
                    <p>Seu pet em boas mãos, sempre!</p>
                    <div class="footer-address">
                        <i class="fas fa-map-marker-alt"></i>
                        {{ tenant()->address ?? 'Endereço não cadastrado' }}
                    </div>
                    <div class="footer-phone">
                        <i class="fas fa-phone"></i>
                        {{ tenant()->phone ?? 'Telefone não cadastrado' }}
                    </div>
                    <div class="social-links">
                        @if(tenant()->instagram)
                        <a href="{{ tenant()->instagram }}" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a>
                        @endif
                        @if(tenant()->facebook)
                        <a href="{{ tenant()->facebook }}" target="_blank" rel="noopener"><i class="fab fa-facebook"></i></a>
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
                        <li>SEGUNDA A SEXTA: 8H ÀS 20H</li>
                        <li>SÁBADO: 8H ÀS 16H</li>
                        <li>DOMINGO: FECHADO</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2023 {{ tenant()->fantasy_name ?? 'Clínica Veterinária' }}. FEITO COM ❤️ POR QUEM AMA ANIMAIS.</p>
            </div>
        </div>
    </footer>
</body>
</html>

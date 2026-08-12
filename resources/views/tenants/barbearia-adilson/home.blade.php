<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <link rel="icon" type="image/png" sizes="192x192" href="{{ tenant()->logo }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ tenant()->logo }}">
    <link rel="apple-touch-icon" href="{{ tenant()->logo }}">
    <link rel="manifest" href="/manifest.json">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ tenant()->fantasy_name ?? 'Barbearia' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset e configurações básicas */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            color: #333;
            line-height: 1.6;
            overflow-x: hidden;
            background-color: #ffffff;
        }

        :root {
            --cor-primaria: #2c3e50;
            --cor-secundaria: #3498db;
            --cor-destaque: #e74c3c;
            --cinza-claro: #f8f9fa;
            --branco: #ffffff;
        }
        
        /* Cabeçalho */
        header {
            background-color: var(--branco);
            position: fixed;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }
        
        .logo img {
            max-height: 60px;
            width: auto;
            object-fit: contain;
        }
        
        .nav-links {
            display: flex;
            list-style: none;
            gap: 30px;
        }
        
        .nav-links a {
            text-decoration: none;
            color: var(--cor-primaria);
            font-weight: 500;
            font-size: 14px;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
            color: var(--cor-secundaria);
        }
        
        .btn-agendar {
            background-color: var(--cor-secundaria);
            color: var(--branco);
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .btn-agendar:hover {
            background-color: var(--cor-primaria);
            transform: translateY(-2px);
        }
        
        .menu-toggle {
            display: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--cor-primaria);
        }
        
        /* Seção Hero */
        .hero {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--cor-primaria) 0%, var(--cor-secundaria) 100%);
            display: flex;
            align-items: center;
            color: var(--branco);
            padding-top: 80px;
        }
        
        .hero-content {
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            font-weight: 700;
        }
        
        .hero p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        
        .btn-hero {
            background-color: var(--branco);
            color: var(--cor-primaria);
            padding: 15px 40px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .btn-hero:hover {
            background-color: var(--cor-destaque);
            color: var(--branco);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        /* Seção comum para todas as sections */
        section {
            padding: 80px 0;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .section-title h2 {
            font-size: 2.5rem;
            color: var(--cor-primaria);
            margin-bottom: 10px;
            font-weight: 600;
        }

        .section-title h2::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background-color: var(--cor-secundaria);
            margin: 15px auto 0;
        }

        .section-title p {
            color: #666;
            font-size: 1.1rem;
        }
        
        /* SEÇÃO SERVIÇOS - Editável pelo proprietário */
        .servicos {
            background-color: var(--cinza-claro);
        }
        
        .servicos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }
        
        .servico-card {
            background-color: var(--branco);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .servico-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .servico-img {
            height: 200px;
            background-size: cover;
            background-position: center;
            background-color: #ddd;
        }
        
        .servico-content {
            padding: 25px;
            text-align: center;
        }
        
        .servico-content h3 {
            font-size: 1.3rem;
            margin-bottom: 10px;
            color: var(--cor-primaria);
        }

        .servico-content p {
            color: #666;
            margin-bottom: 15px;
            font-size: 0.95rem;
        }
        
        .servico-preco {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--cor-secundaria);
        }
        
        /* SEÇÃO SOBRE */
        .sobre-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }
        
        .sobre-img img {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .sobre-text h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: var(--cor-primaria);
        }

        .sobre-text p {
            margin-bottom: 15px;
            color: #555;
            line-height: 1.8;
        }
        
        /* SEÇÃO GALERIA - Editável pelo proprietário */
        .galeria {
            background-color: var(--cinza-claro);
        }
        
        .galeria-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .galeria-item {
            height: 300px;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .galeria-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s;
        }
        
        .galeria-item:hover img {
            transform: scale(1.1);
        }
        
        /* SEÇÃO AMBIENTE - Editável pelo proprietário */
        .ambiente-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .ambiente-item {
            position: relative;
            height: 350px;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .ambiente-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s;
        }

        .ambiente-item:hover img {
            transform: scale(1.05);
        }

        .ambiente-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            color: white;
            padding: 20px;
        }

        .ambiente-overlay h3 {
            font-size: 1.2rem;
        }
        
        /* SEÇÃO EQUIPE - Editável pelo proprietário */
        .equipe {
            background-color: var(--cinza-claro);
        }

        .equipe-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .equipe-card {
            background-color: var(--branco);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s;
        }

        .equipe-card:hover {
            transform: translateY(-5px);
        }
        
        .equipe-foto {
            height: 280px;
            background-size: cover;
            background-position: center;
            background-color: #ddd;
        }
        
        .equipe-info {
            padding: 20px;
        }

        .equipe-info h3 {
            font-size: 1.3rem;
            color: var(--cor-primaria);
            margin-bottom: 5px;
        }

        .equipe-info p {
            color: #666;
            font-size: 0.95rem;
        }
        
        /* SEÇÃO CONTATO */
        .contato {
            background-color: var(--branco);
        }

        .contato-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
        }
        
        .contato-info h3 {
            font-size: 1.8rem;
            margin-bottom: 30px;
            color: var(--cor-primaria);
        }
        
        .contato-item {
            display: flex;
            margin-bottom: 25px;
            align-items: flex-start;
        }
        
        .contato-icon {
            width: 40px;
            color: var(--cor-secundaria);
            font-size: 24px;
        }

        .contato-item-text h4 {
            color: var(--cor-primaria);
            margin-bottom: 5px;
        }

        .contato-item-text p {
            color: #666;
        }
        
        .contato-form {
            background-color: var(--cinza-claro);
            padding: 40px;
            border-radius: 10px;
        }

        .contato-form h3 {
            font-size: 1.8rem;
            margin-bottom: 25px;
            color: var(--cor-primaria);
        }
        
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--cor-primaria);
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--cor-secundaria);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        .btn-submit {
            background-color: var(--cor-secundaria);
            color: var(--branco);
            padding: 12px 40px;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            background-color: var(--cor-primaria);
            transform: translateY(-2px);
        }
        
        /* Rodapé */
        footer {
            background-color: var(--cor-primaria);
            color: var(--branco);
            padding: 60px 0 20px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .footer-col h4 {
            color: var(--branco);
            margin-bottom: 20px;
            font-size: 1.2rem;
        }

        .footer-col p, .footer-col ul {
            color: rgba(255,255,255,0.8);
            line-height: 2;
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col ul li a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-col ul li a:hover {
            color: var(--branco);
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255,255,255,0.8);
            transition: all 0.3s;
        }
        
        .social-links a:hover {
            border-color: var(--branco);
            color: var(--branco);
            background-color: var(--cor-secundaria);
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            margin-top: 40px;
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 14px;
            color: rgba(255,255,255,0.6);
        }
        
        /* Responsividade */
        @media (max-width: 768px) {
            .hero h1 { font-size: 2rem; }
            .nav-links {
                position: fixed;
                top: 80px;
                left: -100%;
                width: 100%;
                height: calc(100vh - 80px);
                background: var(--branco);
                flex-direction: column;
                align-items: center;
                padding-top: 30px;
                transition: 0.4s;
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            }
            .nav-links.active { left: 0; }
            .nav-links li { margin: 10px 0; }
            .menu-toggle { display: block; }
            .sobre-content,
            .contato-grid {
                grid-template-columns: 1fr;
                gap: 30px;
                padding: 0 20px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="#" class="logo">
                    @if(tenant()->logo)
                        <img src="{{ url(tenant()->logo) }}" alt="{{ tenant()->fantasy_name }}">
                    @else
                        <span style="font-size: 24px; font-weight: 700; color: var(--cor-primaria);">{{ tenant()->fantasy_name ?? 'Barbearia' }}</span>
                    @endif
                </a>
                
                <ul class="nav-links">
                    <li><a href="#inicio">Início</a></li>
                    <li><a href="#servicos">Serviços</a></li>
                    <li><a href="#sobre">Sobre</a></li>
                    <li><a href="#galeria">Galeria</a></li>
                    <li><a href="#ambiente">Ambiente</a></li>
                    <li><a href="#equipe">Equipe</a></li>
                    <li><a href="#contato">Contato</a></li>
                </ul>
                
                <a href="/login" class="btn-agendar">Agendar</a>
                
                <div class="menu-toggle" onclick="toggleMenu()">
                    <i class="fas fa-bars"></i>
                </div>
            </nav>
        </div>
    </header>

    <!-- SEÇÃO HERO -->
    <section class="hero" id="inicio">
        <div class="container">
            <div class="hero-content">
                <h1>{{ tenant()->fantasy_name ?? 'Bem-vindo' }}</h1>
                
                <p>{{ tenant()->address ?? '' }}{{ tenant()->number ? ', ' . tenant()->number : 'Serviços de qualidade para você' }}</p>
                <a href="/login" class="btn-hero">AGENDAR AGORA</a>
            </div>
        </div>
    </section>

    <!-- SEÇÃO SERVIÇOS -->
    <section class="servicos" id="servicos">
        <div class="container">
            <div class="section-title">
                <h2>Nossos Serviços</h2>
                <p>Conheça os serviços que oferecemos</p>
            </div>
            <div class="servicos-grid">
                <!-- Card exemplo - será substituído por serviços reais do salão -->
                <div class="servico-card">
                    <div class="servico-img" style="background-image: url('/images/placeholder-servico.jpg');"></div>
                    <div class="servico-content">
                        <h3>Corte de Cabelo</h3>
                        <p>Corte moderno e personalizado</p>
                        <div class="servico-preco">R$ 40,00</div>
                    </div>
                </div>
                <div class="servico-card">
                    <div class="servico-img" style="background-image: url('/images/placeholder-servico.jpg');"></div>
                    <div class="servico-content">
                        <h3>Barba</h3>
                        <p>Barba bem feita e alinhada</p>
                        <div class="servico-preco">R$ 30,00</div>
                    </div>
                </div>
                <div class="servico-card">
                    <div class="servico-img" style="background-image: url('/images/placeholder-servico.jpg');"></div>
                    <div class="servico-content">
                        <h3>Combo Completo</h3>
                        <p>Cabelo + Barba + Sobrancelha</p>
                        <div class="servico-preco">R$ 80,00</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SEÇÃO SOBRE -->
    <section class="sobre" id="sobre">
        <div class="container">
            <div class="section-title">
                <h2>Sobre Nós</h2>
            </div>
            <div class="sobre-content">
                <div class="sobre-img">
                    <img src="/images/placeholder-sobre.jpg" alt="Sobre nós">
                </div>
                <div class="sobre-text">
                    <h2>{{ tenant()->fantasy_name ?? 'Nossa História' }}</h2>
                    <p>Somos uma empresa dedicada a oferecer os melhores serviços para nossos clientes. Com anos de experiência no mercado, garantimos qualidade e satisfação em cada atendimento.</p>
                    <p>Nossa equipe é formada por profissionais qualificados e apaixonados pelo que fazem, sempre buscando as melhores técnicas e tendências do mercado.</p>
                    <p>Venha nos conhecer e experimente um atendimento diferenciado!</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SEÇÃO GALERIA -->
    <section class="galeria" id="galeria">
        <div class="container">
            <div class="section-title">
                <h2>Galeria</h2>
                <p>Confira alguns dos nossos trabalhos</p>
            </div>
            <div class="galeria-grid">
                <!-- Imagens exemplo - serão substituídas por imagens reais do salão -->
                <div class="galeria-item">
                    <img src="/images/placeholder-galeria.jpg" alt="Trabalho 1">
                </div>
                <div class="galeria-item">
                    <img src="/images/placeholder-galeria.jpg" alt="Trabalho 2">
                </div>
                <div class="galeria-item">
                    <img src="/images/placeholder-galeria.jpg" alt="Trabalho 3">
                </div>
                <div class="galeria-item">
                    <img src="/images/placeholder-galeria.jpg" alt="Trabalho 4">
                </div>
            </div>
        </div>
    </section>

    <!-- SEÇÃO AMBIENTE -->
    <section class="ambiente" id="ambiente">
        <div class="container">
            <div class="section-title">
                <h2>Nosso Ambiente</h2>
                <p>Conheça nosso espaço</p>
            </div>
            <div class="ambiente-grid">
                <!-- Imagens do ambiente - editáveis pelo proprietário -->
                <div class="ambiente-item">
                    <img src="/images/placeholder-ambiente.jpg" alt="Ambiente 1">
                    <div class="ambiente-overlay">
                        <h3>Recepção</h3>
                    </div>
                </div>
                <div class="ambiente-item">
                    <img src="/images/placeholder-ambiente.jpg" alt="Ambiente 2">
                    <div class="ambiente-overlay">
                        <h3>Área de Atendimento</h3>
                    </div>
                </div>
                <div class="ambiente-item">
                    <img src="/images/placeholder-ambiente.jpg" alt="Ambiente 3">
                    <div class="ambiente-overlay">
                        <h3>Área VIP</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SEÇÃO EQUIPE -->
    <section class="equipe" id="equipe">
        <div class="container">
            <div class="section-title">
                <h2>Nossa Equipe</h2>
                <p>Conheça nossos profissionais</p>
            </div>
            <div class="equipe-grid">
                <!-- Cards de equipe - serão preenchidos com profissionais reais -->
                <div class="equipe-card">
                    <div class="equipe-foto" style="background-image: url('/images/placeholder-equipe.jpg');"></div>
                    <div class="equipe-info">
                        <h3>João Silva</h3>
                        <p>Barbeiro Profissional</p>
                    </div>
                </div>
                <div class="equipe-card">
                    <div class="equipe-foto" style="background-image: url('/images/placeholder-equipe.jpg');"></div>
                    <div class="equipe-info">
                        <h3>Maria Santos</h3>
                        <p>Cabeleireira</p>
                    </div>
                </div>
                <div class="equipe-card">
                    <div class="equipe-foto" style="background-image: url('/images/placeholder-equipe.jpg');"></div>
                    <div class="equipe-info">
                        <h3>Pedro Costa</h3>
                        <p>Especialista em Barba</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SEÇÃO CONTATO -->
    <section class="contato" id="contato">
        <div class="container">
            <div class="section-title">
                <h2>Entre em Contato</h2>
            </div>
            <div class="contato-grid">
                <div class="contato-info">
                    <h3>Informações de Contato</h3>
                    <div class="contato-item">
                        <div class="contato-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contato-item-text">
                            <h4>Endereço</h4>
                            <p>{{ tenant()->address ?? 'Rua Exemplo, 123' }}{{ tenant()->number ? ', ' . tenant()->number : '' }}<br>
                            {{ tenant()->neighborhood ?? 'Bairro' }} - {{ tenant()->city ?? 'Cidade' }}/{{ tenant()->state ?? 'UF' }}</p>
                        </div>
                    </div>
                    <div class="contato-item">
                        <div class="contato-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contato-item-text">
                            <h4>Telefone</h4>
                            <p>{{ tenant()->phone ?? '(00) 0000-0000' }}</p>
                        </div>
                    </div>
                    <div class="contato-item">
                        <div class="contato-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contato-item-text">
                            <h4>E-mail</h4>
                            <p>{{ tenant()->email ?? 'contato@exemplo.com' }}</p>
                        </div>
                    </div>
                    <div class="social-links">
                        @if(tenant()->instagram)
                            <a href="{{ tenant()->instagram }}" target="_blank"><i class="fab fa-instagram"></i></a>
                        @endif
                        @if(tenant()->facebook)
                            <a href="{{ tenant()->facebook }}" target="_blank"><i class="fab fa-facebook"></i></a>
                        @endif
                        @if(tenant()->whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', tenant()->whatsapp) }}" target="_blank"><i class="fab fa-whatsapp"></i></a>
                        @endif
                    </div>
                </div>
                <div class="contato-form">
                    <h3>Envie sua Mensagem</h3>
                    <form>
                        <div class="form-group">
                            <label>Nome</label>
                            <input type="text" class="form-control" placeholder="Seu nome">
                        </div>
                        <div class="form-group">
                            <label>E-mail</label>
                            <input type="email" class="form-control" placeholder="Seu e-mail">
                        </div>
                        <div class="form-group">
                            <label>Telefone</label>
                            <input type="tel" class="form-control" placeholder="Seu telefone">
                        </div>
                        <div class="form-group">
                            <label>Mensagem</label>
                            <textarea class="form-control" placeholder="Sua mensagem"></textarea>
                        </div>
                        <button type="submit" class="btn-submit">Enviar Mensagem</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- RODAPÉ -->
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>{{ tenant()->fantasy_name ?? 'Sobre' }}</h4>
                    <p>Oferecemos os melhores serviços com qualidade e profissionalismo.</p>
                    <div class="social-links">
                        @if(tenant()->instagram)
                            <a href="{{ tenant()->instagram }}" target="_blank"><i class="fab fa-instagram"></i></a>
                        @endif
                        @if(tenant()->facebook)
                            <a href="{{ tenant()->facebook }}" target="_blank"><i class="fab fa-facebook"></i></a>
                        @endif
                        @if(tenant()->whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', tenant()->whatsapp) }}" target="_blank"><i class="fab fa-whatsapp"></i></a>
                        @endif
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Links Rápidos</h4>
                    <ul>
                        <li><a href="#inicio">Início</a></li>
                        <li><a href="#servicos">Serviços</a></li>
                        <li><a href="#sobre">Sobre</a></li>
                        <li><a href="#contato">Contato</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Horário de Funcionamento</h4>
                    <p>Segunda a Sexta: 9h - 19h<br>
                    Sábado: 9h - 17h<br>
                    Domingo: Fechado</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} {{ tenant()->fantasy_name ?? 'Barbearia' }}. Todos os direitos reservados. | Powered by Pagby</p>
            </div>
        </div>
    </footer>

    <script>
        function toggleMenu() {
            document.querySelector('.nav-links').classList.toggle('active');
        }

        // Fechar menu ao clicar em um link
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', () => {
                document.querySelector('.nav-links').classList.remove('active');
            });
        });

        // Scroll suave
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>

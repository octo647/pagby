<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <link rel="icon" type="image/png" sizes="192x192" href="{{ tenant()->logo }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ tenant()->logo }}">
    <link rel="apple-touch-icon" href="{{ tenant()->logo }}">
    <link rel="manifest" href="/manifest.json">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ tenant()->fantasy_name ?? 'Barbearia Minimal | Estilo Essencial' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset e Configurações de Cores: Branco, Cinza, Preto e Dourado */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', 'Helvetica Neue', Arial, sans-serif;
        }
        
        body {
            color: #333333;
            line-height: 1.6;
            overflow-x: hidden;
            background-color: #ffffff;
        }

        /* Variáveis de Paleta */
        :root {
            --ouro: #C5A059;
            --preto: #111111;
            --cinza-claro: #F4F4F4;
            --branco: #FFFFFF;
        }
        
        /* Cabeçalho */
        header {
            background-color: var(--branco);
            position: fixed;
            width: 100%;
            z-index: 1000;
            border-bottom: 1px solid #e5e5e5;
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
            padding: 20px 0;
        }
        
        .logo {
            font-size: 24px;
            font-weight: 700;
            color: var(--preto);
            text-decoration: none;
            letter-spacing: 2px;
        }
        
        .logo img {
            max-height: 80px;
            width: auto;
            object-fit: contain;
        }
        
        .nav-links {
            display: flex;
            list-style: none;
            gap: 45px;
        }
        
        .nav-links li {
            margin-left: 0;
        }
        
        .nav-links a {
            text-decoration: none;
            color: var(--preto);
            font-weight: 500;
            font-size: 13px;
            letter-spacing: 1px;
            transition: color 0.3s;
            position: relative;
        }
        
        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 0;
            background-color: var(--ouro);
            transition: width 0.3s;
        }
        
        .nav-links a:hover {
            color: var(--ouro);
        }
        
        .nav-links a:hover::after {
            width: 100%;
        }
        
        .btn-agendar {
            background-color: var(--preto);
            color: var(--branco);
            padding: 12px 28px;
            border: 1px solid var(--preto);
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            letter-spacing: 1px;
            transition: all 0.3s;
        }
        
        .btn-agendar:hover {
            background-color: var(--ouro);
            border-color: var(--ouro);
            color: var(--branco);
        }
        
        .menu-toggle {
            display: none;
            font-size: 22px;
            cursor: pointer;
            color: var(--preto);
        }
        
        /* Seção Hero */
        .hero {
            height: 100vh;
            background-color: var(--cinza-claro);
            display: flex;
            align-items: center;
            color: var(--preto);
            position: relative;
            overflow: hidden;
            padding-top: 80px;
        }
        
        .hero-content {
            max-width: 630px;
            z-index: 2;
            position: relative;
            background-color: var(--cinza-claro);
            padding-right: 40px;
        }
        
        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 25px;
            font-weight: 300;
            line-height: 1.1;
            letter-spacing: -1px;
        }
        
        .hero h1 span {
            color: var(--ouro);
            font-weight: 600;
        }
        
        .hero p {
            font-size: 1.1rem;
            margin-bottom: 40px;
            color: #555555;
            max-width: 500px;
        }
        
        .btn-hero {
            background-color: var(--preto);
            color: var(--branco);
            padding: 18px 45px;
            border: 1px solid var(--preto);
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 2px;
            display: inline-block;
            transition: all 0.4s;
        }
        
        .btn-hero:hover {
            background-color: transparent;
            color: var(--preto);
            border-color: var(--ouro);
        }
        
        .hero-image {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 45%;
            background-image: url('images/Templates/Barbearias/Clean/hero.avif');
            background-size: cover;
            background-position: center;
            filter: grayscale(100%) contrast(1.1);
            z-index: 1;
        }
        
        /* Seção Serviços */
        .servicos {
            padding: 120px 0;
            background-color: var(--branco);
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 80px;
        }
        
        .section-title h2 {
            font-size: 2.2rem;
            color: var(--preto);
            margin-bottom: 15px;
            font-weight: 300;
            letter-spacing: 1px;
        }

        .section-title h2::after {
            content: '';
            display: block;
            width: 60px;
            height: 3px;
            background-color: var(--ouro);
            margin: 15px auto 0;
        }
        
        .servicos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }
        
        .servico-card {
            background-color: var(--branco);
            border: 1px solid #eeeeee;
            transition: all 0.3s;
        }
        
        .servico-card:hover {
            border-color: var(--ouro);
            transform: translateY(-5px);
        }
        
        .servico-img {
            height: 250px;
            background-size: cover;
            background-position: center;
            filter: grayscale(100%);
            transition: filter 0.5s;
        }
        
        .servico-card:hover .servico-img {
            filter: grayscale(0%);
        }
        
        .servico-content {
            padding: 35px;
            text-align: center;
        }
        
        .servico-content h3 {
            font-size: 1.2rem;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }
        
        .servico-preco {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--ouro);
            margin-top: 15px;
        }
        
        /* Seção Sobre */
        .sobre {
            padding: 120px 0;
            background-color: var(--cinza-claro);
        }
        
        .sobre-content {
            display: flex;
            align-items: center;
            gap: 80px;
        }
        
        .sobre-img {
            flex: 1;
            position: relative;
        }
        
        .sobre-img::before {
            content: '';
            position: absolute;
            top: -15px;
            left: -15px;
            right: 15px;
            bottom: 15px;
            border: 2px solid var(--ouro);
            z-index: 1;
        }
        
        .sobre-img img {
            width: 100%;
            z-index: 2;
            filter: grayscale(100%);
            position: relative;
        }
        
        .sobre-text h2 {
            font-size: 2.2rem;
            margin-bottom: 25px;
            font-weight: 300;
        }
        
        /* Seção Galeria */
        .galeria {
            padding: 120px 0;
        }
        
        .galeria-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 15px;
        }
        
        .galeria-item {
            height: 350px;
            overflow: hidden;
        }
        
        .galeria-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: grayscale(100%);
            transition: all 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        
        .galeria-item:hover img {
            transform: scale(1.1);
            filter: grayscale(0%);
        }
        
        /* Seção Contato */
        .contato {
            padding: 120px 0;
            background-color: var(--cinza-claro);
        }
        
        .contato-info h3 {
            font-size: 1.8rem;
            margin-bottom: 35px;
            font-weight: 300;
        }
        
        .contato-item {
            display: flex;
            margin-bottom: 30px;
        }
        
        .contato-icon {
            width: 30px;
            color: var(--ouro);
            font-size: 20px;
        }
        
        .contato-form {
            background-color: var(--branco);
            padding: 50px;
            border-top: 4px solid var(--ouro);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 0;
            border: none;
            border-bottom: 1px solid #dddddd;
            margin-bottom: 20px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--ouro);
        }
        
        /* Rodapé */
        footer {
            background-color: var(--preto);
            color: var(--branco);
            padding: 80px 0 30px;
        }
        
        .footer-col h4 {
            color: var(--ouro);
            margin-bottom: 25px;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 2px;
        }
        
        .social-links a {
            width: 40px;
            height: 40px;
            border: 1px solid #333;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #999;
            margin-right: 10px;
            transition: all 0.3s;
        }
        
        .social-links a:hover {
            border-color: var(--ouro);
            color: var(--ouro);
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 40px;
            margin-top: 60px;
            border-top: 1px solid #222;
            font-size: 12px;
            color: #666;
        }
        
        /* Responsividade */
        @media (max-width: 768px) {
            .hero h1 { font-size: 2.5rem; }
            .hero-image { display: none; }
            .nav-links {
                position: fixed;
                top: 100px;
                left: -100%;
                width: 100%;
                height: calc(100vh - 100px);
                background: var(--branco);
                flex-direction: column;
                align-items: center;
                padding-top: 30px;
                transition: 0.4s;
            }
            .nav-links.active { left: 0; }
            .nav-links li { margin: 8px 0; }
            .menu-toggle { display: block; }
            .sobre-content { flex-direction: column; }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="#" class="logo"><img src="{{ url(tenant()->logo) }}" alt="{{ tenant()->fantasy_name ?? 'BARBEARIA' }}"></a>
                
                <ul class="nav-links">
                    <li><a href="#inicio">INÍCIO</a></li>
                    <li><a href="#servicos">SERVIÇOS</a></li>
                    <li><a href="#sobre">SOBRE</a></li>
                    <li><a href="#galeria">GALERIA</a></li>
                    <li><a href="#contato">CONTATO</a></li>
                </ul>
                
                <a href="/login" class="btn-agendar">AGENDAR</a>
                
                <div class="menu-toggle">
                    <i class="fas fa-bars"></i>
                </div>
            </nav>
        </div>
    </header>

    <section class="hero" id="inicio">
        <div class="container">
            <div class="hero-content">
                <h1>ESTILO, <span>QUALIDADE</span> E EXPERIÊNCIA</h1>
                <p>Uma barbearia voltada para você cliente, buscamos entregar o melhor resultado e experiência.</p>
                <a href="/login" class="btn-hero">RESERVAR AGORA</a>
            </div>
        </div>
        <div class="hero-image"></div>
    </section>

    <section class="servicos" id="servicos">
        <div class="container">
            <div class="section-title">
                <h2>SERVIÇOS</h2>
                <p>O equilíbrio perfeito entre a tradição e o estilo moderno.</p>
            </div>
            
            <div class="servicos-grid">
                <div class="servico-card">
                    <div class="servico-img" style="background-image: url({{ url('images/Templates/Barbearias/Clean/corte1.jpeg') }})"></div>
                    <div class="servico-content">
                        <h3>CORTE DE CABELO</h3>
                        <p>Técnicas avançadas para um acabamento impecável.</p>
                        <div class="servico-preco">R$ 40,00</div>
                    </div>
                </div>
                
                <div class="servico-card">
                    <div class="servico-img" style="background-image: url({{ url('images/Templates/Barbearias/Clean/barba1.jpeg') }})"></div>
                    <div class="servico-content">
                        <h3>BARBA CLASSIC</h3>
                        <p>Desenho, toalha quente e relaxamento profundo.</p>
                        <div class="servico-preco">R$ 30,00</div>
                    </div>
                </div>
                
                <div class="servico-card">
                    <div class="servico-img" style="background-image: url({{ url('images/Templates/Barbearias/Clean/homem-com-barba.jpg') }})"></div>
                    <div class="servico-content">
                        <h3>O ESSENCIAL</h3>
                        <p>Corte, barba e consultoria de imagem completa.</p>
                        <div class="servico-preco">R$ 60,00</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="sobre" id="sobre">
        <div class="container">
            <div class="sobre-content">
                <div class="sobre-img">
                    <img src="{{ url('images/Templates/Barbearias/Clean/photo.avif') }}" alt="Espaço Premium">
                </div>
                <div class="sobre-text">
                    <h2>SOBRE O CONCEITO</h2>
                    <p>A {{ tenant()->fantasy_name ?? 'Barbearia Essencial' }} redefine o cuidado masculino através de uma paleta de luxo e precisão técnica.</p>
                    <p>Acreditamos que o ambiente influencia o bem-estar. Por isso, criamos um espaço monocromático com detalhes em ouro para que você foque apenas no seu momento.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="galeria" id="galeria">
        <div class="container">
            <div class="section-title">
                <h2>GALERIA</h2>
            </div>
            <div class="galeria-grid">
                <div class="galeria-item"><img src="{{ url('images/Templates/Barbearias/Clean/corte-moderno.jpeg') }}"></div>
                <div class="galeria-item"><img src="{{ url('images/Templates/Barbearias/Clean/barba2.jpeg') }}"></div>
                <div class="galeria-item"><img src="{{ url('images/Templates/Barbearias/Clean/produtos.jpeg') }}"></div>
                <div class="galeria-item"><img src="{{ url('images/Templates/Barbearias/Clean/corte2.jpeg') }}"></div>
            </div>
        </div>
    </section>

    <section class="contato" id="contato">
        <div class="container">
            <div class="contato-content">
                <div class="contato-info">
                    <h3>CONTATO</h3>
                    <div class="contato-item">
                        <i class="fas fa-map-marker-alt contato-icon"></i>
                        <div>
                            <h4>LOCALIZAÇÃO</h4>
                            <p>{{ tenant()->address ?? '' }}@if(tenant()->number), {{ tenant()->number }}@endif</p>
                            <p>{{ tenant()->neighborhood ?? '' }}</p>
                            <p>{{ tenant()->city ?? '' }}@if(tenant()->city && tenant()->state) - @endif{{ tenant()->state ?? '' }}</p>
                            
                        </div>
                    </div>
                    <div class="contato-item">
                        <i class="fas fa-phone contato-icon"></i>
                        <div>
                            <h4>TELEFONE</h4>
                            <p>{{ tenant()->phone ?? '(11) 3456-7890' }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="contato-form">
                    <h3>AGENDAR SESSÃO</h3>
                    <p>Clique abaixo para escolher seu profissional e horário.</p>
                    <br>
                    <a href="/login" class="btn-agendar" style="display:inline-block">AGENDAR AGORA</a>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-content" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 40px;">
                <div class="footer-col">
                    <h4>{{ tenant()->name ?? 'ESSENCIAL' }}</h4>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>HORÁRIOS</h4>
                    <p style="font-size: 13px; color: #999;">Seg - Sex: 09h às 20h<br>Sáb: 09h às 18h</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 {{ tenant()->fantasy_name ?? 'BARBEARIA ESSENCIAL' }}.</p>
            </div>
        </div>
    </footer>

    <script>
        const menuToggle = document.querySelector('.menu-toggle');
        const navLinks = document.querySelector('.nav-links');
        const navItems = document.querySelectorAll('.nav-links a');
        
        menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });
        
        navItems.forEach(item => {
            item.addEventListener('click', () => {
                navLinks.classList.remove('active');
            });
        });
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{tenant()->fantasy_name ?? 'Funk Barber - Estilo da Quebrada'}}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Poppins:wght@300;400;600;700;800&family=Bebas+Neue&family=Anton&display=swap" rel="stylesheet">
    <style>
        :root {
            --funk-pink: #FF00FF;
            --funk-green: #00FF00;
            --funk-blue: #00FFFF;
            --funk-yellow: #FFFF00;
            --funk-purple: #9D00FF;
            --funk-black: #0A0A0A;
            --funk-white: #F0F0F0;
            --funk-neon: #FF3131;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--funk-black);
            color: var(--funk-white);
            overflow-x: hidden;
            line-height: 1.6;
        }
        
        h1, h2, h3, h4 {
            font-family: 'Anton', sans-serif;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        
        /* Efeito de grade/glitch */
        .grid-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(10, 10, 10, 0.9), rgba(10, 10, 10, 0.9)),
                url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect width="100" height="100" fill="none" stroke="%23FF00FF" stroke-width="0.5" opacity="0.1"/></svg>');
            opacity: 0.8;
            pointer-events: none;
            z-index: -2;
        }
        
        /* Pulsação de cores */
        @keyframes pulse {
            0%, 100% { opacity: 0.7; }
            50% { opacity: 1; }
        }
        
        /* Header Funk */
        header {
            background-color: rgba(10, 10, 10, 0.95);
            padding: 20px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            z-index: 1000;
            border-bottom: 3px solid var(--funk-pink);
            box-shadow: 0 5px 20px rgba(255, 0, 255, 0.3);
            backdrop-filter: blur(10px);
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo-icon {
            font-size: 2.8rem;
            color: var(--funk-pink);
            text-shadow: 
                0 0 10px var(--funk-pink),
                0 0 20px var(--funk-pink);
            animation: pulse 2s infinite;
        }
        
        .logo-text {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            font-weight: 900;
            background: linear-gradient(45deg, var(--funk-pink), var(--funk-blue), var(--funk-green));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: 2px;
            text-shadow: 0 0 10px rgba(255, 0, 255, 0.5);
        }
        
        /* Navegação Funk */
        nav ul {
            display: flex;
            list-style: none;
            gap: 30px;
        }
        
        nav a {
            color: var(--funk-white);
            text-decoration: none;
            font-weight: 700;
            font-size: 1.2rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 10px 20px;
            border-radius: 30px;
            background: rgba(255, 0, 255, 0.1);
            transition: all 0.3s;
            border: 2px solid transparent;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        nav a:hover {
            background: linear-gradient(45deg, var(--funk-pink), var(--funk-purple));
            color: var(--funk-black);
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(255, 0, 255, 0.5);
            border: 2px solid var(--funk-green);
        }
        
        .nav-icon {
            font-size: 1.3rem;
        }
        
        .header-btn {
            background: linear-gradient(45deg, var(--funk-pink), var(--funk-blue));
            color: var(--funk-black);
            padding: 12px 30px;
            border: none;
            border-radius: 30px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 0 15px rgba(255, 0, 255, 0.5);
            font-family: 'Orbitron', sans-serif;
        }
        
        .header-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 0 25px rgba(0, 255, 255, 0.7);
            background: linear-gradient(45deg, var(--funk-green), var(--funk-yellow));
        }
        
        /* Hero Section Funk */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 180px 5% 50px 5%;
            position: relative;
            overflow: hidden;
            background: 
                radial-gradient(circle at 20% 50%, rgba(255, 0, 255, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(0, 255, 255, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(0, 255, 0, 0.15) 0%, transparent 50%);
        }

        @media (max-width: 1100px) {
            .hero {
                padding-top: 200px;
            }
        }
        @media (max-width: 768px) {
            .hero {
                padding-top: 230px;
            }
        }
        @media (max-width: 480px) {
            .hero {
                padding-top: 260px;
            }
        }
        }

        @media (max-width: 1100px) {
            .hero {
                padding-top: 140px;
            }
        }
        @media (max-width: 768px) {
            .hero {
                padding-top: 170px;
            }
        }
        @media (max-width: 480px) {
            .hero {
                padding-top: 200px;
            }
        }
        }
        
        .hero-content {
            max-width: 800px;
            z-index: 2;
            position: relative;
        }
        
        .hero h1 {
            font-size: 5rem;
            line-height: 1;
            margin-bottom: 20px;
            background: linear-gradient(45deg, var(--funk-pink), var(--funk-yellow), var(--funk-blue));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 0 20px rgba(255, 0, 255, 0.3);
        }
        
        .hero h1 span {
            display: block;
            font-size: 3.5rem;
            color: var(--funk-green);
            text-shadow: 0 0 15px var(--funk-green);
        }
        
        .hero-subtitle {
            font-family: 'Orbitron', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--funk-blue);
            margin-bottom: 30px;
            text-shadow: 0 0 10px var(--funk-blue);
            animation: pulse 3s infinite alternate;
        }
        
        .hero p {
            font-size: 1.4rem;
            margin-bottom: 40px;
            color: #DDD;
            max-width: 700px;
            background-color: rgba(0, 0, 0, 0.6);
            padding: 25px;
            border-radius: 15px;
            border-left: 5px solid var(--funk-pink);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }
        
        /* Elementos de som e equalizador */
        .equalizer {
            position: absolute;
            right: 5%;
            bottom: 20%;
            width: 100px;
            height: 200px;
            display: flex;
            align-items: flex-end;
            gap: 8px;
        }
        
        .bar {
            width: 15px;
            background: linear-gradient(to top, var(--funk-pink), var(--funk-blue));
            border-radius: 5px 5px 0 0;
            animation: equalize 1.5s infinite ease-in-out;
        }
        
        .bar:nth-child(1) { height: 40%; animation-delay: 0s; }
        .bar:nth-child(2) { height: 70%; animation-delay: 0.2s; }
        .bar:nth-child(3) { height: 90%; animation-delay: 0.4s; }
        .bar:nth-child(4) { height: 60%; animation-delay: 0.6s; }
        .bar:nth-child(5) { height: 80%; animation-delay: 0.8s; }
        .bar:nth-child(6) { height: 50%; animation-delay: 1s; }
        
        @keyframes equalize {
            0%, 100% { height: 40%; }
            50% { height: 90%; }
        }
        
        /* Botões Funk */
        .hero-buttons {
            display: flex;
            gap: 25px;
            flex-wrap: wrap;
            margin-top: 40px;
        }
        
        .btn-whatsapp {
            background: linear-gradient(45deg, #25D366, #00FF00);
            color: black;
            padding: 20px 40px;
            border: none;
            border-radius: 50px;
            font-size: 1.3rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 5px 20px rgba(37, 211, 102, 0.5);
            font-family: 'Orbitron', sans-serif;
        }
        
        .btn-whatsapp:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 10px 30px rgba(0, 255, 0, 0.7);
        }
        
        .btn-instagram {
            background: linear-gradient(45deg, #E4405F, #FF00FF);
            color: white;
            padding: 20px 40px;
            border: none;
            border-radius: 50px;
            font-size: 1.3rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 5px 20px rgba(228, 64, 95, 0.5);
            font-family: 'Orbitron', sans-serif;
        }
        
        .btn-instagram:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 10px 30px rgba(255, 0, 255, 0.7);
        }
        
        /* Seção de Cortes */
        .cuts {
            padding: 120px 5%;
            background-color: var(--funk-black);
            position: relative;
            overflow: hidden;
        }
        
        .section-title {
            text-align: center;
            font-size: 4rem;
            margin-bottom: 80px;
            position: relative;
            background: linear-gradient(45deg, var(--funk-pink), var(--funk-blue));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            width: 200px;
            height: 5px;
            background: linear-gradient(to right, var(--funk-pink), var(--funk-blue), var(--funk-green));
            bottom: -20px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 5px;
            box-shadow: 0 0 10px var(--funk-pink);
        }
        
        .cuts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .cut-card {
            background: rgba(20, 20, 20, 0.8);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 
                0 10px 20px rgba(0, 0, 0, 0.5),
                inset 0 0 20px rgba(255, 0, 255, 0.1);
            transition: all 0.4s;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }
        
        .cut-card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, var(--funk-pink), var(--funk-blue), var(--funk-green));
            z-index: -1;
            border-radius: 22px;
            opacity: 0;
            transition: opacity 0.4s;
        }
        
        .cut-card:hover::before {
            opacity: 1;
        }
        
        .cut-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 20px 40px rgba(255, 0, 255, 0.3);
            border: 2px solid var(--funk-yellow);
        }
        
        .cut-icon {
            font-size: 3.5rem;
            margin-bottom: 25px;
            background: linear-gradient(45deg, var(--funk-pink), var(--funk-blue));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .cut-card h3 {
            font-size: 2.2rem;
            margin-bottom: 20px;
            color: var(--funk-white);
        }
        
        .cut-card p {
            color: #AAA;
            margin-bottom: 25px;
        }
        
        .cut-price {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.8rem;
            font-weight: 900;
            color: var(--funk-green);
            text-shadow: 0 0 10px var(--funk-green);
        }
        
        /* Seção de Galeria Funk */
        .gallery {
            padding: 120px 5%;
            background: linear-gradient(45deg, rgba(255, 0, 255, 0.05), rgba(0, 255, 255, 0.05));
        }
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 15px;
            cursor: pointer;
            height: 300px;
            border: 3px solid transparent;
            transition: all 0.4s;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }
        
        .gallery-item:hover {
            transform: scale(1.05) rotate(2deg);
            border: 3px solid var(--funk-pink);
            box-shadow: 
                0 20px 40px rgba(255, 0, 255, 0.4),
                0 0 30px rgba(0, 255, 255, 0.3);
        }
        
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
            filter: brightness(0.9);
        }
        
        .gallery-item:hover img {
            transform: scale(1.1);
            filter: brightness(1.1) saturate(1.5);
        }
        
        .gallery-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 20px;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.9), transparent);
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.4s;
        }
        
        .gallery-item:hover .gallery-overlay {
            transform: translateY(0);
            opacity: 1;
        }
        
        /* Seção de Eventos */
        .events {
            padding: 120px 5%;
            background-color: var(--funk-black);
            text-align: center;
        }
        
        .event-card {
            max-width: 800px;
            margin: 0 auto;
            background: linear-gradient(45deg, rgba(255, 0, 255, 0.1), rgba(0, 255, 255, 0.1));
            border-radius: 20px;
            padding: 50px;
            border: 3px solid var(--funk-green);
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.5),
                0 0 30px rgba(0, 255, 0, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .event-card h3 {
            font-size: 3rem;
            margin-bottom: 20px;
            color: var(--funk-yellow);
            text-shadow: 0 0 10px var(--funk-yellow);
        }
        
        .event-card p {
            font-size: 1.5rem;
            margin-bottom: 30px;
            color: var(--funk-white);
        }
        
        .event-date {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            color: var(--funk-pink);
            text-shadow: 0 0 15px var(--funk-pink);
            margin-bottom: 30px;
        }
        
        /* Footer Funk */
        footer {
            background: linear-gradient(45deg, #1a1a1a, #0a0a0a);
            padding: 80px 5% 30px;
            border-top: 3px solid var(--funk-pink);
            position: relative;
            overflow: hidden;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 50px;
            max-width: 1200px;
            margin: 0 auto 50px;
            position: relative;
            z-index: 2;
        }
        
        .footer-section h3 {
            font-size: 2rem;
            margin-bottom: 25px;
            background: linear-gradient(45deg, var(--funk-pink), var(--funk-blue));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .footer-section p {
            color: #AAA;
            margin-bottom: 20px;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            color: var(--funk-white);
            font-size: 1.5rem;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        
        .social-link:hover {
            background: linear-gradient(45deg, var(--funk-pink), var(--funk-blue));
            color: var(--funk-black);
            transform: translateY(-5px) rotate(10deg);
            border: 2px solid var(--funk-green);
            box-shadow: 0 10px 20px rgba(255, 0, 255, 0.5);
        }
        
        .contact-info p {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            color: #CCC;
        }
        
        .contact-info i {
            margin-right: 15px;
            color: var(--funk-blue);
            font-size: 1.3rem;
            width: 25px;
        }
        
        .copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 0, 255, 0.2);
            color: #888;
            font-size: 0.9rem;
            font-family: 'Orbitron', sans-serif;
            position: relative;
            z-index: 2;
        }
        
        /* Elementos flutuantes */
        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 1;
        }
        
        .floating-element {
            position: absolute;
            color: var(--funk-pink);
            opacity: 0.1;
            font-size: 5rem;
            animation: float 15s infinite linear;
        }
        
        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(100px, -100px) rotate(360deg); }
        }
        
        /* Responsividade */
        @media (max-width: 1100px) {
            .hero h1 {
                font-size: 4rem;
            }
            
            .equalizer {
                display: none;
            }
        }
        
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 20px;
                padding: 15px 5%;
            }
            
            nav ul {
                gap: 10px;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            nav a {
                padding: 8px 15px;
                font-size: 1rem;
            }
            
            .hero h1 {
                font-size: 3rem;
            }
            
            .hero h1 span {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.5rem;
            }
            
            .hero p {
                font-size: 1.1rem;
            }
            
            .section-title {
                font-size: 3rem;
            }
            
            .btn-whatsapp, .btn-instagram {
                padding: 15px 30px;
                font-size: 1.1rem;
            }
        }
        
        @media (max-width: 480px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .hero h1 span {
                font-size: 2rem;
            }
            
            .section-title {
                font-size: 2.5rem;
            }
            
            .cut-price {
                font-size: 2.2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Overlay de grade -->
    <div class="grid-overlay"></div>
    
    <!-- Elementos flutuantes -->
    <div class="floating-elements">
        <div class="floating-element" style="top: 10%; left: 5%;">🔥</div>
        <div class="floating-element" style="top: 20%; right: 10%;">💈</div>
        <div class="floating-element" style="bottom: 30%; left: 15%;">✂️</div>
        <div class="floating-element" style="bottom: 20%; right: 5%;">🎶</div>
    </div>

    <!-- Header -->
    <header>
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-cut"></i>
            </div>
            <div class="logo-text">{{ tenant()->fantasy_name ?? 'FUNK BARBER' }}</div>
        </div>
        
        <nav>
            <ul>
                <li><a href="#home"><i class="fas fa-home nav-icon"></i> INÍCIO</a></li>
                <li><a href="#cuts"><i class="fas fa-scissors nav-icon"></i> CORTES</a></li>
                <li><a href="#gallery"><i class="fas fa-images nav-icon"></i> FOTOS</a></li>
                <li><a href="#events"><i class="fas fa-music nav-icon"></i> EVENTOS</a></li>
                <li><a href="{{ route('login') }}" class="header-btn"><i class="fas fa-sign-in-alt nav-icon"></i> AGENDAR</a></li>
            </ul>
        </nav>

    </header>

    <!-- Seção Hero -->
    <section class="hero" id="home">
        <div class="hero-content">
            <h1>{{tenant()->fantasy_name ?? 'BARBEARIA DA<br><span>QUEBRADA TOP'}}'</span></h1>
            <div class="hero-subtitle">🎵 CORTE NO ESTILO, NA REGUAÇÃO 🎵</div>
            <p>Aqui o corte é na régua, o som é no talo e o estilo é da favela! Mais que barbearia, é point da galera. Venha dar um tapa no visual com a gente!</p>

            <div class="hero-buttons">
                <button class="btn-whatsapp" onclick="openWhatsApp({{tenant()->whatsapp}})">
                    <i class="fab fa-whatsapp"></i> CHAMA NO ZAP
                </button>
                <button class="btn-instagram" onclick="openInstagram({{tenant()->instagram}})">
                    <i class="fab fa-instagram"></i> SEGUE A GENTE
                </button>
                <button class="btn-funk" id="play-funk-btn" style="background: linear-gradient(45deg, #FF00FF, #00FF00); color: #0A0A0A; font-weight: bold; padding: 18px 36px; border-radius: 50px; margin-left: 10px; border: none; font-size: 1.2rem; display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <i class="fas fa-music" id="funk-btn-icon"></i> <span id="funk-btn-text">TOCAR FUNK</span>
                </button>
                <audio id="funk-audio" src="images/Templates/Barbearias/Funk/funk.mp3"></audio>
            </div>
        </div>
        
        <!-- Equalizador visual -->
        <div class="equalizer">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </div>
    </section>

    <!-- Seção de Cortes -->
    <section class="cuts" id="cuts">
        <h2 class="section-title">CORTES NA REGUAÇÃO</h2>
        
        <div class="cuts-grid">
            <div class="cut-card">
                <div class="cut-icon">
                    <i class="fas fa-fire"></i>
                </div>
                <h3>DEGRADÊ NA REGUA</h3>
                <p>Degradê perfeito, na régua mesmo. Transição suave que faz sucesso na quebrada toda.</p>
                <div class="cut-price">R$ 25</div>
            </div>
            
            <div class="cut-card">
                <div class="cut-icon">
                    <i class="fas fa-crown"></i>
                </div>
                <h3>MOHAWK BRAVO</h3>
                <p>Estilo ousado pra quem é brabo mesmo. Deixa a galera olhando duas vezes.</p>
                <div class="cut-price">R$ 40</div>
            </div>
            
            <div class="cut-card">
                <div class="cut-icon">
                    <i class="fas fa-skull"></i>
                </div>
                <h3>DESENHO NA NAV</h3>
                <p>Desenho personalizado na navalha. De símbolo da quebrada ao nome da cremosa.</p>
                <div class="cut-price">R$ 30</div>
            </div>
            
            <div class="cut-card">
                <div class="cut-icon">
                    <i class="fas fa-gem"></i>
                </div>
                <h3>BARBA DE PRÓ</h3>
                <p>Barba feita com produtos top e acabamento que dura a semana toda.</p>
                <div class="cut-price">R$ 20</div>
            </div>
        </div>
    </section>

    <!-- Seção Galeria -->
    <section class="gallery" id="gallery">
        <h2 class="section-title">CLIMA DA QUEBRADA</h2>
        
        <div class="gallery-grid">
            <div class="gallery-item">
                <img src="images/Templates/Barbearias/Funk/corte1.jpeg" alt="Corte no estilo">
                <div class="gallery-overlay">
                    <h3>ESTILO DA FAVELA</h3>
                </div>
            </div>
            
            <div class="gallery-item">
                <img src="images/Templates/Barbearias/Funk/corte2.jpeg" alt="Barbearia movimentada">
                <div class="gallery-overlay">
                    <h3>POINT DA GALERA</h3>
                </div>
            </div>
            
            <div class="gallery-item">
                <img src="images/Templates/Barbearias/Funk/corte3.jpeg" alt="Desenho na navalha">
                <div class="gallery-overlay">
                    <h3>ARTE NA CABEÇA</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção de Eventos -->
    <section class="events" id="events">
        <h2 class="section-title">ROLÊ NA BARBEARIA</h2>
        
        <div class="event-card">
            <h3>SEXTA FUNK</h3>
            <p>Toda sexta é festa na barbearia! DJ tocando os hits da quebrada, cerveja gelada e corte com desconto.</p>
            <div class="event-date">TODA SEXTA | 18H ÀS 22H</div>
            <button class="btn-instagram" onclick="openInstagram({{tenant()->instagram}})">
                <i class="fas fa-calendar-alt"></i> CONFIRMA PRESENÇA
            </button>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact">
        <div class="footer-content">
            <div class="footer-section">
                <h3>{{ tenant()->fantasy_name ?? 'FUNK BARBER' }}</h3>
                <p>A barbearia que é point da quebrada! Corte na régua, som no talo e clima de família. Aqui a gente valoriza quem é da quebrada!</p>
                
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-tiktok"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-spotify"></i></a>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>FALA COM A GENTE</h3>
                <div class="contact-info">
                    <p><i class="fas fa-map-marker-alt"></i> {!! tenant()->address.','.tenant()->number.'<br>'.tenant()->complement. ' '. tenant()->neighborhood ?? 'Rua do Funk, 150 - Favela Top'!!}</p>
                    <p><i class="fas fa-phone"></i> {{ tenant()->phone ?? '(11) 9 8888-7777' }}</p>
                    <p><i class="fas fa-clock"></i> Ter-Sex: 10h-20h | Sáb: 9h-19h</p>
                    <p><i class="fas fa-music"></i> Sexta Funk: 18h-22h</p>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>PROMO DA QUEBRADA</h3>
                <p>• Leve 2 amigos, pague 2 cortes</p>
                <p>• Aniversariante: corte free</p>
                <p>• Primeira vez: 20% off</p>
                <p>• Estudante: 15% de desconto</p>
                <p>• Motoboy: corta pagando menos</p>
            </div>
        </div>
        
        <div class="copyright">
            <p>© 2023 {{ tenant()->fantasy_name ?? 'FUNK BARBER' }} | Orgulho da quebrada! 🎵 "Aqui o pobre é rico e o playboy não entra" 🎵</p>
        </div>
    </footer>

    <script>
        // Função para abrir WhatsApp (usada apenas no botão 'Chama no Zap')
        function openWhatsApp(phone = null) {
            if (!phone) {
                phone = "{{ tenant()->whatsapp ?? tenant()->phone ?? '5511988887777' }}";
            }
            const message = "Salve! Quero marcar um horário na Funk Barber! 🎵✂️";
            window.open(`https://wa.me/${phone}?text=${encodeURIComponent(message)}`, '_blank');
        }
        
        // Função para abrir Instagram
        function openInstagram() {
            window.open("{{ tenant()->instagram ?? 'https://instagram.com/funkbarber' }}", '_blank');
        }
        
        // Rolagem suave
        document.querySelectorAll('nav a').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                if(targetId && targetId.startsWith('#')) {
                    e.preventDefault();
                    document.querySelector(targetId).scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        // Efeito de header ao rolar
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header');
            if (window.scrollY > 100) {
                header.style.boxShadow = '0 10px 30px rgba(255, 0, 255, 0.5)';
                header.style.background = 'rgba(10, 10, 10, 0.98)';
            } else {
                header.style.boxShadow = '0 5px 20px rgba(255, 0, 255, 0.3)';
                header.style.background = 'rgba(10, 10, 10, 0.95)';
            }
        });
        
        // Efeito de digitação no subtítulo
        const subtitle = document.querySelector('.hero-subtitle');
        const originalText = subtitle.textContent;
        subtitle.textContent = '';
        let i = 0;
        
        function typeWriter() {
            if (i < originalText.length) {
                subtitle.textContent += originalText.charAt(i);
                i++;
                setTimeout(typeWriter, 50);
            }
        }
        
        // Inicia efeito de digitação
        setTimeout(typeWriter, 1000);
        
        // Efeito de cores pulsantes nos títulos
        const titles = document.querySelectorAll('.section-title, .hero h1');
        let hue = 0;
        
        function pulseColors() {
            hue = (hue + 1) % 360;
            titles.forEach(title => {
                title.style.background = `linear-gradient(45deg, hsl(${hue}, 100%, 50%), hsl(${(hue + 60) % 360}, 100%, 50%), hsl(${(hue + 120) % 360}, 100%, 50%))`;
                title.style.webkitBackgroundClip = 'text';
                title.style.backgroundClip = 'text';
            });
            requestAnimationFrame(pulseColors);
        }
        
        pulseColors();
        
        // Efeito de brilho nos cards ao passar mouse
        document.querySelectorAll('.cut-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.boxShadow = '0 25px 50px rgba(255, 0, 255, 0.5)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.boxShadow = '0 10px 20px rgba(0, 0, 0, 0.5), inset 0 0 20px rgba(255, 0, 255, 0.1)';
            });
        });
        
        // Efeito de som ao clicar nos botões
        document.querySelectorAll('button').forEach(button => {
            button.addEventListener('click', function() {
                // Efeito visual de clique
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 200);
            });
        });
        
        // Player de Funk: botão visível para tocar música
        document.addEventListener('DOMContentLoaded', function() {
            const playBtn = document.getElementById('play-funk-btn');
            const audio = document.getElementById('funk-audio');
            const btnText = document.getElementById('funk-btn-text');
            const btnIcon = document.getElementById('funk-btn-icon');
            let isPlaying = false;
            if (playBtn && audio) {
                playBtn.addEventListener('click', function() {
                    if (!isPlaying) {
                        audio.volume = 0.3;
                        audio.currentTime = 0;
                        audio.play();
                    } else {
                        audio.pause();
                        audio.currentTime = 0;
                    }
                });
                audio.addEventListener('play', function() {
                    isPlaying = true;
                    btnText.textContent = 'PARAR FUNK';
                    btnIcon.classList.remove('fa-music');
                    btnIcon.classList.add('fa-stop');
                });
                audio.addEventListener('pause', function() {
                    isPlaying = false;
                    btnText.textContent = 'TOCAR FUNK';
                    btnIcon.classList.remove('fa-stop');
                    btnIcon.classList.add('fa-music');
                });
                audio.addEventListener('ended', function() {
                    isPlaying = false;
                    btnText.textContent = 'TOCAR FUNK';
                    btnIcon.classList.remove('fa-stop');
                    btnIcon.classList.add('fa-music');
                });
            }
        });
    </script>
</body>
</html>

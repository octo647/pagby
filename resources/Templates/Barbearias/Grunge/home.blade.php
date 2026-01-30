<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ tenant()->fantasy_name ?? 'Razor Edge' }} - Barbearia Grunge</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Roboto+Condensed:wght@300;400;700&family=Cinzel+Decorative:wght@700;900&family=Special+Elite&display=swap" rel="stylesheet">
    <style>
        :root {
            --grunge-dark: #0A0A0A;
            --grunge-red: #8B0000;
            --grunge-rust: #B7410E;
            --grunge-metal: #71797E;
            --grunge-yellow: #DAA520;
            --grunge-texture: #1A1A1A;
            --grunge-light: #C0C0C0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto Condensed', sans-serif;
            background-color: var(--grunge-dark);
            color: var(--grunge-light);
            overflow-x: hidden;
            line-height: 1.6;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(139, 0, 0, 0.05) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(183, 65, 14, 0.05) 0%, transparent 20%);
        }
        
        h1, h2, h3, h4 {
            font-family: 'Oswald', sans-serif;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        
        /* Efeito de textura grunge */
        .grunge-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('images/Templates/Barbearias/Grunge/alt-dark.png');
            opacity: 0.15;
            pointer-events: none;
            z-index: -1;
        }
        
        /* Header Grunge */
        header {
            background-color: rgba(10, 10, 10, 0.95);
            padding: 20px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            z-index: 1000;
            border-bottom: 3px solid var(--grunge-red);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.8);
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo-icon {
            font-size: 2.8rem;
            color: var(--grunge-red);
            filter: drop-shadow(0 0 3px rgba(139, 0, 0, 0.5));
        }
        
        .logo-text {
            font-family: 'Cinzel Decorative', cursive;
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--grunge-yellow);
            text-shadow: 
                2px 2px 0 var(--grunge-red),
                4px 4px 0 rgba(0, 0, 0, 0.8);
            letter-spacing: 3px;
        }
        
        /* Navegação estilo industrial */
        nav ul {
            display: flex;
            list-style: none;
            gap: 40px;
        }
        
        nav a {
            color: var(--grunge-light);
            text-decoration: none;
            font-weight: 600;
            font-size: 1.2rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 10px 0;
            position: relative;
            transition: color 0.3s;
        }
        
        nav a::before {
            content: '//';
            color: var(--grunge-red);
            margin-right: 8px;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        nav a:hover::before {
            opacity: 1;
        }
        
        nav a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(to right, var(--grunge-red), var(--grunge-yellow));
            transition: width 0.3s ease;
        }
        
        nav a:hover {
            color: var(--grunge-yellow);
        }
        
        nav a:hover::after {
            width: 100%;
        }
        
        /* Botão estilo industrial */
        .header-btn {
            background: transparent;
            color: var(--grunge-light);
            padding: 12px 30px;
            border: 2px solid var(--grunge-metal);
            border-radius: 0;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            box-shadow: 3px 3px 0 rgba(0, 0, 0, 0.5);
        }
        
        .header-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(139, 0, 0, 0.3), transparent);
            transition: left 0.5s;
        }
        
        .header-btn:hover {
            background-color: rgba(139, 0, 0, 0.2);
            border-color: var(--grunge-red);
            color: var(--grunge-yellow);
            transform: translateY(-3px);
            box-shadow: 5px 5px 0 rgba(0, 0, 0, 0.7);
        }
        
        .header-btn:hover::before {
            left: 100%;
        }
        
        /* Hero Section Grunge */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 0 5%;
            position: relative;
            overflow: hidden;
            background-image: 
                linear-gradient(rgba(10, 10, 10, 0.9), rgba(10, 10, 10, 0.8)),
                url('images/Templates/Barbearias/Grunge/ambiente.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        
        .hero-content {
            max-width: 800px;
            z-index: 2;
            position: relative;
        }
        
        .hero h1 {
            font-size: 5rem;
            line-height: 1;
            margin-bottom: 30px;
            color: var(--grunge-light);
            text-shadow: 
                3px 3px 0 var(--grunge-red),
                6px 6px 0 rgba(0, 0, 0, 0.8);
            letter-spacing: 5px;
            font-family: 'Special Elite', cursive;
        }
        
        .hero h1 span {
            color: var(--grunge-yellow);
            display: block;
            font-size: 3.5rem;
            margin-top: 10px;
        }
        
        .hero p {
            font-size: 1.4rem;
            margin-bottom: 40px;
            color: #AAAAAA;
            max-width: 700px;
            border-left: 4px solid var(--grunge-red);
            padding-left: 20px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
        }
        
        /* Elementos de rasgado/grunge */
        .grunge-border {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none"><path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" fill="%230A0A0A"/></svg>');
            background-size: cover;
            background-repeat: no-repeat;
        }
        
        /* Seção Sobre */
        .about {
            padding: 120px 5%;
            background-color: var(--grunge-texture);
            position: relative;
        }
        
        .section-title {
            text-align: center;
            font-size: 3.5rem;
            margin-bottom: 80px;
            position: relative;
            color: var(--grunge-light);
        }
        
        .section-title::before, .section-title::after {
            content: '///';
            color: var(--grunge-red);
            margin: 0 20px;
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
        
        .section-title::after {
            content: '///';
        }
        
        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            max-width: 1200px;
            margin: 0 auto;
            align-items: center;
        }
        
        .about-text h3 {
            font-size: 2.2rem;
            color: var(--grunge-yellow);
            margin-bottom: 30px;
            border-bottom: 2px solid var(--grunge-metal);
            padding-bottom: 15px;
        }
        
        .about-text p {
            font-size: 1.2rem;
            margin-bottom: 25px;
            color: #BBBBBB;
        }
        
        .grunge-list {
            list-style: none;
            margin-top: 30px;
        }
        
        .grunge-list li {
            margin-bottom: 15px;
            padding-left: 30px;
            position: relative;
            color: var(--grunge-light);
        }
        
        .grunge-list li::before {
            content: '>';
            color: var(--grunge-red);
            font-weight: bold;
            position: absolute;
            left: 0;
        }
        
        .about-image {
            position: relative;
            border: 15px solid transparent;
            border-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect width="100" height="100" fill="none" stroke="%238B0000" stroke-width="10" stroke-dasharray="10,5"/></svg>') 30 stretch;
            box-shadow: 10px 10px 0 rgba(0, 0, 0, 0.5);
        }
        
        .about-image img {
            width: 100%;
            height: auto;
            display: block;
            filter: grayscale(30%) contrast(110%);
        }
        
        /* Seção Serviços */
        .services {
            padding: 120px 5%;
            background-color: var(--grunge-dark);
            position: relative;
        }
        
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .service-card {
            background: linear-gradient(145deg, #151515, #0A0A0A);
            padding: 40px 30px;
            border-left: 5px solid var(--grunge-red);
            box-shadow: 
                8px 8px 0 rgba(139, 0, 0, 0.2),
                inset 0 0 20px rgba(0, 0, 0, 0.5);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(to right, var(--grunge-red), var(--grunge-yellow));
        }
        
        .service-card:hover {
            transform: translateY(-10px) rotate(1deg);
            box-shadow: 
                15px 15px 0 rgba(139, 0, 0, 0.3),
                inset 0 0 30px rgba(139, 0, 0, 0.2);
            border-left-color: var(--grunge-yellow);
        }
        
        .service-icon {
            font-size: 3rem;
            color: var(--grunge-red);
            margin-bottom: 25px;
            text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.5);
        }
        
        .service-card h3 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: var(--grunge-yellow);
        }
        
        .service-card p {
            color: #AAAAAA;
        }
        
        /* Seção Galeria Grunge */
        .gallery {
            padding: 120px 5%;
            background-color: var(--grunge-texture);
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
            cursor: pointer;
            border: 8px solid transparent;
            border-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect width="100" height="100" fill="none" stroke="%2371797E" stroke-width="8" stroke-dasharray="15,10,5,10"/></svg>') 20 stretch;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.7);
            transition: all 0.4s;
        }
        
        .gallery-item:hover {
            transform: scale(1.03) rotate(-1deg);
            border-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect width="100" height="100" fill="none" stroke="%23DAA520" stroke-width="8" stroke-dasharray="15,10,5,10"/></svg>') 20 stretch;
            box-shadow: 10px 10px 20px rgba(0, 0, 0, 0.9);
        }
        
        .gallery-item img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            filter: sepia(30%) contrast(120%);
            transition: transform 0.5s, filter 0.5s;
        }
        
        .gallery-item:hover img {
            transform: scale(1.1);
            filter: sepia(0%) contrast(130%);
        }
        
        .gallery-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 20px;
            background: linear-gradient(to top, rgba(10, 10, 10, 0.9), transparent);
            transform: translateY(100%);
            transition: transform 0.4s;
        }
        
        .gallery-item:hover .gallery-overlay {
            transform: translateY(0);
        }
        
        /* Seção CTA */
        .cta {
            padding: 120px 5%;
            text-align: center;
            background: 
                linear-gradient(rgba(10, 10, 10, 0.9), rgba(10, 10, 10, 0.9)),
                url('images/Templates/Barbearias/Grunge/corte1.jpeg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: relative;
        }
        
        .cta h2 {
            font-size: 3.5rem;
            margin-bottom: 30px;
            color: var(--grunge-light);
            text-shadow: 2px 2px 0 var(--grunge-red);
        }
        
        .cta p {
            font-size: 1.3rem;
            margin-bottom: 50px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            color: #BBBBBB;
        }
        
        .cta-btn {
            background: transparent;
            color: var(--grunge-light);
            padding: 20px 60px;
            border: 3px solid var(--grunge-red);
            border-radius: 0;
            font-size: 1.5rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 3px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            box-shadow: 5px 5px 0 rgba(0, 0, 0, 0.7);
            font-family: 'Oswald', sans-serif;
        }
        
        .cta-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(218, 165, 32, 0.4), transparent);
            transition: left 0.7s;
        }
        
        .cta-btn:hover {
            background-color: rgba(139, 0, 0, 0.3);
            border-color: var(--grunge-yellow);
            color: var(--grunge-yellow);
            transform: translateY(-5px);
            box-shadow: 10px 10px 0 rgba(0, 0, 0, 0.9);
            letter-spacing: 5px;
        }
        
        .cta-btn:hover::before {
            left: 100%;
        }
        
        /* Footer Grunge */
        footer {
            background-color: var(--grunge-dark);
            padding: 80px 5% 30px;
            border-top: 3px solid var(--grunge-metal);
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 50px;
            max-width: 1200px;
            margin: 0 auto 50px;
        }
        
        .footer-section h3 {
            font-size: 1.8rem;
            margin-bottom: 25px;
            color: var(--grunge-yellow);
            border-bottom: 2px solid var(--grunge-red);
            padding-bottom: 10px;
        }
        
        .footer-section p {
            color: #AAAAAA;
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
            width: 50px;
            height: 50px;
            background: rgba(113, 121, 126, 0.2);
            border-radius: 0;
            color: var(--grunge-light);
            font-size: 1.3rem;
            transition: all 0.3s;
            border: 1px solid var(--grunge-metal);
        }
        
        .social-link:hover {
            background: var(--grunge-red);
            color: var(--grunge-dark);
            transform: rotate(45deg);
            border-color: var(--grunge-yellow);
        }
        
        .contact-info p {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .contact-info i {
            margin-right: 15px;
            color: var(--grunge-red);
            font-size: 1.2rem;
        }
        
        .copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(113, 121, 126, 0.3);
            color: #777;
            font-size: 0.9rem;
            font-family: 'Courier New', monospace;
        }
        
        /* Responsividade */
        @media (max-width: 1100px) {
            .hero h1 {
                font-size: 4rem;
            }
            
            .section-title {
                font-size: 3rem;
            }
            
            .about-content {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 20px;
                padding: 15px 5%;
            }
            
            nav ul {
                gap: 20px;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .hero h1 {
                font-size: 3rem;
            }
            
            .hero h1 span {
                font-size: 2.5rem;
            }
            
            .hero p {
                font-size: 1.1rem;
            }
            
            .section-title {
                font-size: 2.5rem;
            }
            
            .cta h2 {
                font-size: 2.8rem;
            }
        }
        
        @media (max-width: 480px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .hero h1 span {
                font-size: 2rem;
            }
            
            .header-btn, .cta-btn {
                padding: 15px 30px;
                font-size: 1rem;
            }
            
            .section-title {
                font-size: 2.2rem;
            }
            
            .cta h2 {
                font-size: 2.2rem;
            }
            
            .cta-btn {
                padding: 18px 40px;
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Overlay de textura grunge -->
    <div class="grunge-overlay"></div>

    <!-- Header Grunge -->
    <header>
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-skull-crossbones"></i>
            </div>
            <div class="logo-text">{{ tenant()->fantasy_name ?? 'RAZOR EDGE' }}</div>
        </div>
        
        <nav>
            <ul>
                <li><a href="#home">Inferno</a></li>
                <li><a href="#about">Sobre</a></li>
                <li><a href="#services">Cortes</a></li>
                <li><a href="#gallery">Galeria</a></li>
            </ul>
        </nav>
        
        <button class="header-btn" onclick="window.location.href='{{ route('login') }}'">ENTRAR NA MATILHA</button>
    </header>

    <!-- Seção Hero -->
    <section class="hero" id="home">
        <div class="hero-content">
            <h1>BARBEARIA<br><span>GRUNGE</span></h1>
            <p>Onde a tradição encontra a rebeldia. Não somos apenas uma barbearia, somos um refúgio para quem não se encaixa no molde. Cortes brutais, ambiente underground e atitude.</p>
            
            <button class="header-btn" onclick="scrollToAbout()" style="margin-top: 20px; font-size: 1.3rem; padding: 15px 40px;">
                <i class="fas fa-arrow-down" style="margin-right: 10px;"></i>EXPLORAR
            </button>
        </div>
        
        <div class="grunge-border"></div>
    </section>

    <!-- Seção Sobre -->
    <section class="about" id="about">
        <h2 class="section-title">SOBRE A MATILHA</h2>
        
        <div class="about-content">
            <div class="about-text">
                <h3>BARBEARIA PARA REBELDES</h3>
                <p>Fundada em 2010 por um grupo de motociclistas e artistas de rua, a Razor Edge nasceu da necessidade de um espaço onde a autenticidade fosse valorizada acima de tudo.</p>
                
                <p>Não seguimos modas, criamos identidades. Cada corte é uma declaração, cada barba uma expressão de personalidade.</p>
                
                <ul class="grunge-list">
                    <li>Ambiente 100% underground e autêntico</li>
                    <li>Música ao vivo de bandas locais às sextas</li>
                    <li>Produtos artesanais e de pequenos produtores</li>
                    <li>Eventos de cultura alternativa mensais</li>
                    <li>Tatuadores residentes para os mais corajosos</li>
                </ul>
            </div>
            
            <div class="about-image">
                <img src="images/Templates/Barbearias/Grunge/corte2.jpeg" alt="Interior barbearia grunge">
            </div>
        </div>
    </section>

    <!-- Seção Serviços -->
    <section class="services" id="services">
        <h2 class="section-title">CORTES BRUTAIS</h2>
        
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-fire"></i>
                </div>
                <h3>CORTE UNDERGROUND</h3>
                <p>Estilos que desafiam o convencional. De mohawks a undercuts, criamos o visual que expressa sua atitude rebelde.</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-knife"></i>
                </div>
                <h3>BARBA DE MOTOCICLISTA</h3>
                <p>Modelagem e tratamento para barbas que contam histórias. Óleos artesanais e acabamento com navalha clássica.</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-skull"></i>
                </div>
                <h3>TATUAGEM EXPRESS</h3>
                <p>Nossos tatuadores residentes criam arte permanente enquanto você espera. Flash tattoos e designs personalizados.</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-beer"></i>
                </div>
                <h3>EXPERIÊNCIA COMPLETA</h3>
                <p>Cerveja artesanal, rock'n'roll alto e conversas reais. Mais que um corte, uma experiência de irmandade.</p>
            </div>
        </div>
    </section>

    <!-- Seção Galeria -->
    <section class="gallery" id="gallery">
        <h2 class="section-title">CENÁRIO BRUTAL</h2>
        
        <div class="gallery-grid">
            <div class="gallery-item">
                <img src="images/Templates/Barbearias/Grunge/interior.jpeg" alt="Corte grunge">
                <div class="gallery-overlay">
                    <h3>UNDERCUT REBELDE</h3>
                </div>
            </div>
            
            <div class="gallery-item">
                <img src="images/Templates/Barbearias/Grunge/barba.jpg" alt="Barba estilo motoclube">
                <div class="gallery-overlay">
                    <h3>BARBA DE ESTRADA</h3>
                </div>
            </div>
            
            <div class="gallery-item">
                <img src="images/Templates/Barbearias/Grunge/corte1.jpeg" alt="Ambiente underground">
                <div class="gallery-overlay">
                    <h3>NOSSO TERRITÓRIO</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção CTA -->
    <section class="cta" id="cta">
        <h2>PRONTO PARA SE TORNAR LENDA?</h2>
        <p>Agende seu horário e entre para a irmandade. Primeira visita inclui uma cerveja artesanal e uma dose de atitude.</p>
        <button class="cta-btn" onclick="window.location.href='{{ route('login') }}'">AGENDAR NO BATIMENTO</button>
    </section>

    <!-- Footer -->
    <footer id="contact">
        <div class="footer-content">
            <div class="footer-section">
                <h3>RAZOR EDGE</h3>
                <p>Barbearia grunge para quem não tem medo de ser autêntico. Corte, atitude e cultura alternativa.</p>
                
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i>{{ tenant()->instagram }}</a>
                    <a href="#" class="social-link"><i class="fab fa-spotify"></i>{{ tenant()->spotify }}</a>
                    <a href="#" class="social-link"><i class="fab fa-bandcamp"></i>{{ tenant()->bandcamp }}</a>
                    <a href="#" class="social-link"><i class="fab fa-youtube"></i>{{ tenant()->youtube }}</a>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>ENCONTRE A MATILHA</h3>
                <div class="contact-info">
                    <p><i class="fas fa-map-marker-alt"></i> {{ tenant()->address ?? 'Rua do Underground, 666 - Centro' }}</p>
                    <p><i class="fas fa-phone"></i> {{ tenant()->phone ?? '(11) 9-6666-6666' }}</p>
                    <p><i class="fas fa-envelope"></i> {{ tenant()->email ?? 'sangue@razoredge.com.br' }}</p>
                    <p><i class="fas fa-clock"></i> {{ tenant()->hours ?? 'Seg-Sex: 12h-22h | Sáb: 10h-20h' }}  </p>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>REGRA DA CASA</h3>
                <p>1. Respeite todos os irmãos.<br>
                   2. Não reclame do volume.<br>
                   3. Traga histórias para contar.<br>
                   4. Saia melhor do que entrou.</p>
            </div>
        </div>
        
        <div class="copyright">
            <p>© 2023 {{ tenant()->fantasy_name ?? 'RAZOR EDGE BARBER' }} | Nenhum direito reservado - roube este estilo se tiver coragem.</p>
        </div>
    </footer>

    <script>
        // Funções de rolagem suave
        function scrollToCTA() {
            document.getElementById('cta').scrollIntoView({
                behavior: 'smooth'
            });
        }
        
        function scrollToAbout() {
            document.getElementById('about').scrollIntoView({
                behavior: 'smooth'
            });
        }
        
        // Adiciona efeito de rolagem suave para todos os links
        document.querySelectorAll('nav a').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if(targetId.startsWith('#')) {
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
                header.style.backgroundColor = 'rgba(10, 10, 10, 0.98)';
                header.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.9)';
            } else {
                header.style.backgroundColor = 'rgba(10, 10, 10, 0.95)';
                header.style.boxShadow = '0 5px 20px rgba(0, 0, 0, 0.8)';
            }
        });
        
        // Modal de agendamento estilo grunge
        function openBookingModal() {
            const modalHTML = `
                <div id="grunge-modal" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.9); z-index:2000; display:flex; justify-content:center; align-items:center; font-family:'Oswald', sans-serif;">
                    <div style="background:#0A0A0A; border:5px solid #8B0000; padding:40px; max-width:500px; text-align:center; box-shadow:15px 15px 0 rgba(0,0,0,0.7); position:relative;">
                        <button onclick="closeModal()" style="position:absolute; top:15px; right:15px; background:none; border:none; color:#DAA520; font-size:2rem; cursor:pointer;">X</button>
                        <h2 style="color:#DAA520; margin-bottom:20px; font-size:2.5rem; text-transform:uppercase;">AGENDAR</h2>
                        <p style="color:#C0C0C0; margin-bottom:30px;">Ligue direto para a matilha ou apareça sem aviso. Aceitamos os corajosos.</p>
                        <div style="background:#8B0000; padding:20px; margin:20px 0; border:3px solid #71797E;">
                            <h3 style="color:#0A0A0A; font-size:2rem;">(11) 9-6666-6666</h3>
                        </div>
                        <p style="color:#AAAAAA; font-size:0.9rem; margin-top:30px;">Rua do Underground, 666 - Centro<br>Seg-Sex: 12h-22h | Sáb: 10h-20h</p>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }
        
        function closeModal() {
            const modal = document.getElementById('grunge-modal');
            if (modal) modal.remove();
        }
        
        // Efeito de digitação no título
        const title = document.querySelector('.hero h1');
        const originalHTML = title.innerHTML;
        
        // Reset para efeito visual
        setTimeout(() => {
            title.innerHTML = originalHTML;
        }, 100);
        
        // Efeito de glitch aleatório
        setInterval(() => {
            if (Math.random() > 0.7) {
                title.style.textShadow = `3px 3px 0 ${Math.random() > 0.5 ? '#DAA520' : '#8B0000'}, 6px 6px 0 rgba(0, 0, 0, 0.8)`;
                
                setTimeout(() => {
                    title.style.textShadow = '3px 3px 0 #8B0000, 6px 6px 0 rgba(0, 0, 0, 0.8)';
                }, 100);
            }
        }, 3000);
        
        // Efeito de preto e branco nas imagens ao passar o mouse
        document.querySelectorAll('.gallery-item img').forEach(img => {
            img.addEventListener('mouseenter', function() {
                this.style.filter = 'grayscale(0%) contrast(130%)';
            });
            
            img.addEventListener('mouseleave', function() {
                this.style.filter = 'sepia(30%) contrast(120%)';
            });
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ tenant()->fantasy_name ?? 'Barbearia do Zé' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&family=Nunito:wght@400;600;800&family=Bebas+Neue&display=swap" rel="stylesheet">
    <style>
        :root {
            --favela-yellow: #FFD700;
            --favela-green: #32CD32;
            --favela-red: #FF0000;
            --favela-blue: #1E90FF;
            --favela-purple: #9370DB;
            --favela-dark: #222222;
            --favela-light: #F8F8F8;
            --favela-concrete: #A9A9A9;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--favela-light);
            color: var(--favela-dark);
            overflow-x: hidden;
            line-height: 1.6;
        }
        
        h1, h2, h3, h4 {
            font-family: 'Bebas Neue', cursive;
            font-weight: 700;
            letter-spacing: 1px;
        }
        
        /* Efeito de textura de muro */
        .wall-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(248, 248, 248, 0.95), rgba(248, 248, 248, 0.95)),
                url('images/Templates/Barbearias/Comunidade/concrete-wall.png');
            opacity: 0.6;
            pointer-events: none;
            z-index: -1;
        }
        
        /* Header da Quebrada */
        header {
            background: linear-gradient(135deg, var(--favela-yellow), var(--favela-green));
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            border-bottom: 5px solid var(--favela-red);
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo-icon {
            font-size: 2.5rem;
            color: var(--favela-red);
            text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.2);
        }
        
        .logo-text {
            font-family: 'Bebas Neue', cursive;
            font-size: 2.2rem;
            font-weight: 900;
            color: var(--favela-dark);
            text-shadow: 2px 2px 0 var(--favela-yellow);
            letter-spacing: 2px;
        }
        
        .logo-subtext {
            font-family: 'Nunito', sans-serif;
            font-size: 0.9rem;
            color: var(--favela-dark);
            font-weight: 800;
            margin-top: -5px;
        }
        
        /* Navegação estilo comunidade */
        nav ul {
            display: flex;
            list-style: none;
            gap: 25px;
        }
        
        nav a {
            color: var(--favela-dark);
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            padding: 8px 15px;
            border-radius: 30px;
            background-color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 3px 5px rgba(0, 0, 0, 0.1);
        }
        
        nav a:hover {
            background-color: var(--favela-red);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        }
        
        .nav-icon {
            font-size: 1.2rem;
        }
        
        /* Botão WhatsApp fixo */
        .whatsapp-float {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: #25D366;
            color: white;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            transition: all 0.3s;
            text-decoration: none;
        }
        
        .whatsapp-float:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        }
        
        /* Hero Section da Favela */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 100px 5% 50px;
            position: relative;
            overflow: hidden;
            background: linear-gradient(rgba(255, 215, 0, 0.1), rgba(50, 205, 50, 0.1));
        }
        
        .hero-content {
            max-width: 800px;
            z-index: 2;
            position: relative;
        }
        
        .hero h1 {
            font-size: 4.5rem;
            line-height: 1;
            margin-bottom: 20px;
            color: var(--favela-dark);
            text-shadow: 3px 3px 0 var(--favela-yellow);
        }
        
        .hero h1 span {
            color: var(--favela-red);
            display: block;
            font-size: 3.5rem;
            margin-top: 10px;
        }
        
        .hero-subtitle {
            font-family: 'Nunito', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            color: var(--favela-green);
            margin-bottom: 30px;
            text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.1);
        }
        
        .hero p {
            font-size: 1.4rem;
            margin-bottom: 40px;
            color: #333;
            max-width: 700px;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            border-left: 5px solid var(--favela-blue);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .hero-buttons {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .btn-whatsapp {
            background-color: #25D366;
            color: white;
            padding: 18px 35px;
            border: none;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(37, 211, 102, 0.3);
        }
        
        .btn-whatsapp:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(37, 211, 102, 0.5);
        }
        
        .btn-call {
            background-color: var(--favela-blue);
            color: white;
            padding: 18px 35px;
            border: none;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(30, 144, 255, 0.3);
        }
        
        .btn-call:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(30, 144, 255, 0.5);
            background-color: var(--favela-red);
        }
        
        /* Elementos visuais de comunidade */
        .community-elements {
            position: absolute;
            right: 5%;
            top: 50%;
            transform: translateY(-50%);
            max-width: 500px;
        }
        
        .community-card {
            background: linear-gradient(135deg, var(--favela-yellow), var(--favela-green));
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            border: 5px solid var(--favela-red);
            text-align: center;
        }
        
        .community-card h3 {
            font-size: 2.2rem;
            color: var(--favela-dark);
            margin-bottom: 15px;
        }
        
        .community-card p {
            font-size: 1.2rem;
            color: var(--favela-dark);
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .price {
            font-size: 3.5rem;
            font-weight: 900;
            color: var(--favela-red);
            text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.1);
        }
        
        .price span {
            font-size: 1.5rem;
            color: var(--favela-dark);
        }
        
        /* Seção de Preços Acessíveis */
        .prices {
            padding: 100px 5%;
            background-color: var(--favela-light);
        }
        
        .section-title {
            text-align: center;
            font-size: 3.5rem;
            margin-bottom: 60px;
            position: relative;
            color: var(--favela-dark);
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            width: 150px;
            height: 5px;
            background: linear-gradient(to right, var(--favela-yellow), var(--favela-green));
            bottom: -20px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 5px;
        }
        
        .prices-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .price-card {
            background-color: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            border-top: 5px solid var(--favela-yellow);
            text-align: center;
        }
        
        .price-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            border-top-color: var(--favela-red);
        }
        
        .price-card h3 {
            font-size: 2rem;
            color: var(--favela-dark);
            margin-bottom: 20px;
        }
        
        .price-value {
            font-size: 3rem;
            font-weight: 900;
            color: var(--favela-green);
            margin-bottom: 20px;
        }
        
        .price-value span {
            font-size: 1.5rem;
            color: var(--favela-dark);
        }
        
        .price-features {
            list-style: none;
            margin-bottom: 25px;
        }
        
        .price-features li {
            padding: 10px 0;
            border-bottom: 1px dashed #DDD;
            color: #555;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .price-features i {
            color: var(--favela-green);
        }
        
        /* Seção de Localização */
        .location {
            padding: 100px 5%;
            background: linear-gradient(135deg, var(--favela-yellow), var(--favela-green));
        }
        
        .location-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            max-width: 1200px;
            margin: 0 auto;
            align-items: center;
        }
        
        .location-info h3 {
            font-size: 2.5rem;
            color: var(--favela-dark);
            margin-bottom: 30px;
        }
        
        .location-details {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .location-details p {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--favela-dark);
        }
        
        .location-details i {
            color: var(--favela-red);
            font-size: 1.5rem;
            width: 30px;
        }
        
        .map-placeholder {
            background-color: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            text-align: center;
            height: 300px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .map-placeholder i {
            font-size: 4rem;
            color: var(--favela-red);
            margin-bottom: 20px;
        }
        
        /* Seção de Depoimentos */
        .testimonials {
            padding: 100px 5%;
            background-color: var(--favela-light);
        }
        
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .testimonial-card {
            background-color: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        
        .testimonial-card::before {
            content: '"';
            position: absolute;
            top: 10px;
            left: 20px;
            font-size: 5rem;
            color: var(--favela-yellow);
            font-family: 'Bebas Neue', cursive;
            opacity: 0.3;
        }
        
        .testimonial-text {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 20px;
            font-style: italic;
        }
        
        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .author-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: var(--favela-green);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
            font-weight: bold;
        }
        
        .author-info h4 {
            color: var(--favela-dark);
            font-size: 1.3rem;
            margin-bottom: 5px;
        }
        
        .author-info p {
            color: #777;
            font-size: 0.9rem;
        }
        
        /* Footer da Comunidade */
        footer {
            background-color: var(--favela-dark);
            padding: 60px 5% 30px;
            color: white;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto 40px;
        }
        
        .footer-section h3 {
            font-size: 1.8rem;
            margin-bottom: 25px;
            color: var(--favela-yellow);
        }
        
        .footer-section p {
            color: #CCCCCC;
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
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            color: white;
            font-size: 1.3rem;
            transition: all 0.3s;
        }
        
        .social-link:hover {
            background-color: var(--favela-yellow);
            color: var(--favela-dark);
            transform: translateY(-5px);
        }
        
        .contact-info p {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            color: #CCCCCC;
        }
        
        .contact-info i {
            margin-right: 15px;
            color: var(--favela-yellow);
            font-size: 1.2rem;
            width: 20px;
        }
        
        .copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #999;
            font-size: 0.9rem;
        }
        
        /* Responsividade */
        @media (max-width: 1100px) {
            .hero h1 {
                font-size: 3.5rem;
            }
            
            .community-elements {
                position: relative;
                top: auto;
                right: auto;
                transform: none;
                max-width: 100%;
                margin-top: 50px;
            }
            
            .location-content {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 15px;
                padding: 15px 5%;
            }
            
            nav ul {
                gap: 10px;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            nav a {
                padding: 8px 12px;
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
                font-size: 2.8rem;
            }
            
            .btn-whatsapp, .btn-call {
                padding: 15px 25px;
                font-size: 1rem;
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
                font-size: 2.2rem;
            }
            
            .price-value, .price {
                font-size: 2.5rem;
            }
            
            .whatsapp-float {
                width: 60px;
                height: 60px;
                font-size: 2rem;
                bottom: 20px;
                right: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Overlay de textura de muro -->
    <div class="wall-overlay"></div>
    
    <!-- Botão WhatsApp fixo -->
    <a href="https://wa.me/{{ tenant()->phone ?? '5511999999999' }}?text=Olá! Gostaria de agendar um horário na barbearia" class="whatsapp-float" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Header -->
    <header>
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-cut"></i>
            </div>
            <div>
                <div class="logo-text">{{ tenant()->fantasy_name ?? 'BARBEARIA DO ZÉ' }}</div>
                <div class="logo-subtext">CORTE BOM E BARATO | DESDE 2010</div>
            </div>
        </div>
        
        <nav>
            <ul>
                <li><a href="#home"><i class="fas fa-home nav-icon"></i> Início</a></li>
                <li><a href="#prices"><i class="fas fa-tag nav-icon"></i> Preços</a></li>
                <li><a href="#location"><i class="fas fa-map-marker-alt nav-icon"></i> Onde fica</a></li>
                <li><a href="#testimonials"><i class="fas fa-comment-alt nav-icon"></i> Clientes</a></li>
            </ul>
        </nav>
    </header>

    <!-- Seção Hero -->
    <section class="hero" id="home">
        <div class="hero-content">
            <h1>{!! tenant()->fantasy_name ?? 'BARBEARIA DA <span>COMUNIDADE</span>' !!}</h1>
            <div class="hero-subtitle">CORTE TOP, PREÇO JUSTO E RESPEITO</div>
            <p>Há mais de 10 anos cuidando do visual da galera da comunidade. Aqui o corte é bom, o preço cabe no bolso e a conversa é sempre sincera. Venha fazer parte da família!</p>
            
            <div class="hero-buttons">
                <button class="btn-call" type="button">
                   <a href="{{route('login')}}"> <i class="fas fa-sign-in-alt"></i> AGENDAR</a>
                </button>
                
            </div>
        </div>
        
        <div class="community-elements">
            <div class="community-card">
                <h3>PROMOÇÃO DA SEMANA</h3>
                <p>CORTE + BARBA COMPLETA</p>
                <div class="price">R$ 35<span>,00</span></div>
                <p style="font-size: 1rem; margin-top: 10px;">Válido até sábado</p>
            </div>
        </div>
    </section>

    <!-- Seção de Preços -->
    <section class="prices" id="prices">
        <h2 class="section-title">PREÇOS QUE CABEM NO BOLSO</h2>
        
        <div class="prices-grid">
            <div class="price-card">
                <h3>CORTE SIMPLES</h3>
                <div class="price-value">R$ 20<span>,00</span></div>
                <ul class="price-features">
                    <li><i class="fas fa-check"></i> Corte à máquina</li>
                    <li><i class="fas fa-check"></i> Degradê ou social</li>
                    <li><i class="fas fa-check"></i> Acabamento na navalha</li>
                    <li><i class="fas fa-check"></i> Lavagem incluída</li>
                </ul>
                <button class="btn-whatsapp" style="width: 100%; justify-content: center;"><a href="{{route('login')}}">AGENDAR</a></button>
            </div>
            
            <div class="price-card">
                <h3>CORTE + BARBA</h3>
                <div class="price-value">R$ 35<span>,00</span></div>
                <ul class="price-features">
                    <li><i class="fas fa-check"></i> Corte completo</li>
                    <li><i class="fas fa-check"></i> Barba feita</li>
                    <li><i class="fas fa-check"></i> Toalha quente</li>
                    <li><i class="fas fa-check"></i> Produtos premium</li>
                </ul>
                <button class="btn-whatsapp" style="width: 100%; justify-content: center; background-color: var(--favela-red);"><a href="{{route('login')}}">PROMOÇÃO</a></button>
            </div>
            
            <div class="price-card">
                <h3>SOBRANCELHA</h3>
                <div class="price-value">R$ 10<span>,00</span></div>
                <ul class="price-features">
                    <li><i class="fas fa-check"></i> Design completo</li>
                    <li><i class="fas fa-check"></i> Aparação cuidadosa</li>
                    <li><i class="fas fa-check"></i> Hena opcional (+R$5)</li>
                    <li><i class="fas fa-check"></i> Limpeza da pele</li>
                </ul>
                <button class="btn-whatsapp" style="width: 100%; justify-content: center;"><a href="{{route('login')}}">AGENDAR</a></button>
            </div>
        </div>
    </section>

    <!-- Seção Localização -->
    <section class="location" id="location">
        <h2 class="section-title" style="color: var(--favela-dark);">ONDE A GENTE TÁ</h2>
        
        <div class="location-content">
            <div class="location-info">
                <h3>FÁCIL DE CHEGAR, IMPOSSÍVEL DE ESQUECER</h3>
                <div class="location-details">
                    <p><i class="fas fa-map-marker-alt"></i> {{ tenant()->address.', '. tenant()->number. ' - '. tenant()->neighborhood ?? 'Rua da Amizade, 123 - Vila Esperança'}}</p>
                    <p><i class="fas fa-phone"></i> {{ tenant()->phone ?? '(11) 9 9999-9999' }}</p>
                    <p><i class="fas fa-clock"></i> Segunda a Sábado: 9h às 20h</p>
                    <p><i class="fas fa-clock"></i> Domingo: 9h às 14h</p>
                    <p><i class="fas fa-bus"></i> Ponto de ônibus na esquina</p>
                    <p><i class="fas fa-motorcycle"></i> Estacionamento para moto na frente</p>
                </div>
            </div>
            
            <div class="map-placeholder">
                <i class="fas fa-map-marked-alt"></i>
                <h3>PERTO DE VOCÊ</h3>
                <p>Do lado da padaria do seu Manuel<br>Em frente ao campo de futebol</p>
                <button class="btn-call" style="margin-top: 20px;" onclick="openMaps()">
                    <i class="fas fa-directions"></i> COMO CHEGAR
                </button>
            </div>
        </div>
    </section>

    <!-- Seção Depoimentos -->
    <section class="testimonials" id="testimonials">
        <h2 class="section-title">FALA DA GALERA</h2>
        
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="testimonial-text">
                    "Desde que o Zé abriu a barbearia aqui na comunidade, nunca mais precisei ir pro centro pra cortar cabelo. O trabalho dele é impecável e o preço é justo!"
                </div>
                <div class="testimonial-author">
                    <div class="author-avatar">C</div>
                    <div class="author-info">
                        <h4>Carlinhos</h4>
                        <p>Cliente há 5 anos</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="testimonial-text">
                    "Melhor barbearia da região! Levo meus dois filhos sempre, o Zé tem paciência com as crianças e faz um trabalho excelente. Preço acessível e qualidade top!"
                </div>
                <div class="testimonial-author">
                    <div class="author-avatar">M</div>
                    <div class="author-info">
                        <h4>Mariana</h4>
                        <p>Mãe de clientes</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="testimonial-text">
                    "Sempre fui atendido com respeito e profissionalismo. O Zé não só corta cabelo, como dá conselho, ouve a gente e cria um ambiente muito bom. Mais que barbearia, é ponto de encontro!"
                </div>
                <div class="testimonial-author">
                    <div class="author-avatar">J</div>
                    <div class="author-info">
                        <h4>Jorge</h4>
                        <p>Cliente há 8 anos</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact">
        <div class="footer-content">
            <div class="footer-section">
                <h3>{{ tenant()->fanatasy_name ?? 'BARBEARIA DO ZÉ' }}</h3>
                <p>Cuidando do visual da comunidade desde 2010. Corte bom, preço justo e um papo cabeça. Venha fazer parte da nossa família!</p>
                
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-tiktok"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>ATENDIMENTO</h3>
                <div class="contact-info">
                    <p><i class="fas fa-map-marker-alt"></i> {{ tenant()->address.', '. tenant()->number. ' - '. tenant()->neighborhood ?? 'Rua da Amizade, 123 - Vila Esperança'}}</p>
                    <p><i class="fas fa-phone"></i> {{ tenant()->phone ?? '(11) 9 9999-9999' }}</p>
                    <p><i class="fas fa-clock"></i> Seg-Sáb: 9h-20h | Dom: 9h-14h</p>
                    <p><i class="fas fa-percentage"></i> Desconto em dinheiro: 10% off</p>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>VALORES</h3>
                <p>• Respeito acima de tudo</p>
                <p>• Preço justo pra comunidade</p>
                <p>• Qualidade não precisa ser cara</p>
                <p>• Atendimento humanizado</p>
                <p>• Apoio aos jovens da quebrada</p>
            </div>
        </div>
        
        <div class="copyright">
            <p>© 2023 Barbearia do Zé - Orgulho da comunidade! | "De quebrada pra quebrada, sempre evoluindo"</p>
        </div>
    </footer>

    <script>
        // Função para abrir WhatsApp
        function openWhatsApp() {
            const phone = {{ tenant()->phone ?? "5511999999999" }};
            const message = "Olá! Gostaria de agendar um horário na barbearia";
            window.open(`https://wa.me/${phone}?text=${encodeURIComponent(message)}`, '_blank');
        }
        
        // Função para simular ligação
        function callBarber() {
            if(confirm("Ligar para a {{ tenant()->fantasy_name ?? 'Barbearia do Zé' }}?\n{{ tenant()->phone ?? '(11) 9 9999-9999' }}")) {
                window.location.href = "tel:+5511999999999";
            }
        }
        
        // Função para abrir mapa (simulação)
        function openMaps() {
            alert("Para chegar na {{ tenant()->fantasy_name ?? 'Barbearia do Zé' }}:\n\n{{tenant()->address ?? 'Rua da Amizade, 123 - Vila Esperança'}}\n\nReferências:\n• Do lado da padaria do seu Manuel\n• Em frente ao campo de futebol\n• Próximo ao ponto de ônibus da linha 123");
        }
        
        // Rolagem suave para links internos
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
                header.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.3)';
            } else {
                header.style.boxShadow = '0 5px 20px rgba(0, 0, 0, 0.2)';
            }
        });
        
        // Animar cards de preço ao aparecer na tela
        const observerOptions = {
            threshold: 0.2
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = "1";
                    entry.target.style.transform = "translateY(0)";
                }
            });
        }, observerOptions);
        
        // Aplicar animação aos cards
        document.querySelectorAll('.price-card').forEach(card => {
            card.style.opacity = "0";
            card.style.transform = "translateY(30px)";
            card.style.transition = "opacity 0.5s, transform 0.5s";
            observer.observe(card);
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
        
        // Inicia efeito de digitação após carregar a página
        window.addEventListener('load', () => {
            setTimeout(typeWriter, 1000

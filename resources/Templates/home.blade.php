<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barbearia Vibe - Estilo Moderno</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #FF6B35;
            --secondary: #00A8E8;
            --dark: #121212;
            --light: #F8F9FA;
            --gray: #2D2D2D;
            --accent: #FFD166;
            --gradient: linear-gradient(135deg, #FF6B35, #00A8E8);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--dark);
            color: var(--light);
            overflow-x: hidden;
            line-height: 1.6;
        }
        
        h1, h2, h3, h4 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
        }
        
        /* Header Moderno */
        header {
            background-color: rgba(18, 18, 18, 0.95);
            backdrop-filter: blur(10px);
            padding: 25px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            z-index: 1000;
            border-bottom: 2px solid var(--primary);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo-icon {
            font-size: 2.8rem;
            background: var(--gradient);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .logo-text {
            font-size: 2.2rem;
            font-weight: 800;
            background: var(--gradient);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: -1px;
        }
        
        /* Menu moderno */
        nav ul {
            display: flex;
            list-style: none;
            gap: 40px;
        }
        
        nav a {
            color: var(--light);
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 10px 0;
            position: relative;
            transition: color 0.3s;
        }
        
        nav a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 3px;
            background: var(--gradient);
            transition: width 0.3s ease;
        }
        
        nav a:hover {
            color: var(--primary);
        }
        
        nav a:hover::after {
            width: 100%;
        }
        
        .cta-header {
            background: var(--gradient);
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: none;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4);
        }
        
        .cta-header:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 107, 53, 0.6);
        }
        
        /* Hero Section Moderna */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 0 5%;
            position: relative;
            overflow: hidden;
        }
        
        .hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(255, 107, 53, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(0, 168, 232, 0.15) 0%, transparent 50%),
                var(--dark);
            z-index: -2;
        }
        
        .hero-content {
            max-width: 700px;
            z-index: 2;
        }
        
        .hero h1 {
            font-size: 4.5rem;
            line-height: 1.1;
            margin-bottom: 30px;
            background: linear-gradient(to right, #FF6B35, #00A8E8, #FFD166);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: -1px;
        }
        
        .hero p {
            font-size: 1.4rem;
            margin-bottom: 40px;
            color: #CCCCCC;
            max-width: 600px;
        }
        
        .hero-buttons {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .btn-primary {
            background: var(--gradient);
            color: white;
            padding: 18px 45px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 8px 20px rgba(255, 107, 53, 0.4);
        }
        
        .btn-secondary {
            background: transparent;
            color: var(--light);
            padding: 18px 45px;
            border: 2px solid var(--primary);
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(255, 107, 53, 0.6);
        }
        
        .btn-secondary:hover {
            background: var(--primary);
            color: var(--dark);
        }
        
        /* Elementos visuais modernos */
        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: -1;
            overflow: hidden;
        }
        
        .floating-element {
            position: absolute;
            border-radius: 50%;
            background: var(--gradient);
            opacity: 0.1;
            animation: float 15s infinite linear;
        }
        
        .floating-element:nth-child(1) {
            width: 300px;
            height: 300px;
            top: 10%;
            right: 5%;
            animation-delay: 0s;
        }
        
        .floating-element:nth-child(2) {
            width: 150px;
            height: 150px;
            top: 60%;
            right: 15%;
            animation-delay: -5s;
        }
        
        .floating-element:nth-child(3) {
            width: 200px;
            height: 200px;
            top: 30%;
            right: 25%;
            animation-delay: -10s;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-30px) rotate(180deg);
            }
        }
        
        /* Seção de Destaques */
        .highlights {
            padding: 120px 5%;
            background-color: var(--gray);
        }
        
        .section-title {
            text-align: center;
            font-size: 3.2rem;
            margin-bottom: 80px;
            position: relative;
        }
        
        .section-title span {
            background: var(--gradient);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            width: 100px;
            height: 5px;
            background: var(--gradient);
            bottom: -20px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 5px;
        }
        
        .highlights-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .highlight-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            transition: transform 0.3s, background 0.3s;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .highlight-card:hover {
            transform: translateY(-15px);
            background: rgba(255, 107, 53, 0.1);
        }
        
        .highlight-icon {
            font-size: 3.5rem;
            margin-bottom: 25px;
            background: var(--gradient);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .highlight-card h3 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: var(--light);
        }
        
        .highlight-card p {
            color: #AAAAAA;
        }
        
        /* Seção de Galeria */
        .gallery {
            padding: 120px 5%;
            background-color: var(--dark);
        }
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .gallery-item {
            border-radius: 15px;
            overflow: hidden;
            height: 300px;
            position: relative;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .gallery-item:hover {
            transform: scale(1.03);
        }
        
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .gallery-item:hover img {
            transform: scale(1.1);
        }
        
        .gallery-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 20px;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            color: white;
            transform: translateY(10px);
            opacity: 0;
            transition: all 0.3s;
        }
        
        .gallery-item:hover .gallery-overlay {
            transform: translateY(0);
            opacity: 1;
        }
        
        /* Seção CTA Final */
        .final-cta {
            padding: 120px 5%;
            text-align: center;
            background: var(--gradient);
            position: relative;
            overflow: hidden;
        }
        
        .final-cta h2 {
            font-size: 3.5rem;
            margin-bottom: 30px;
            color: white;
        }
        
        .final-cta p {
            font-size: 1.3rem;
            margin-bottom: 50px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .cta-big {
            background: var(--dark);
            color: white;
            padding: 22px 60px;
            border: none;
            border-radius: 50px;
            font-size: 1.3rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            cursor: pointer;
            transition: transform 0.3s, background 0.3s;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .cta-big:hover {
            transform: translateY(-5px) scale(1.05);
            background: #000;
        }
        
        /* Footer Moderno */
        footer {
            background-color: var(--dark);
            padding: 80px 5% 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
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
            background: var(--gradient);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
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
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            color: var(--light);
            font-size: 1.3rem;
            transition: all 0.3s;
        }
        
        .social-link:hover {
            background: var(--gradient);
            transform: translateY(-5px);
        }
        
        .contact-info p {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .contact-info i {
            margin-right: 15px;
            color: var(--primary);
            font-size: 1.2rem;
        }
        
        .copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            color: #777;
            font-size: 0.9rem;
        }
        
        /* Responsividade */
        @media (max-width: 1100px) {
            .hero h1 {
                font-size: 3.8rem;
            }
            
            .section-title {
                font-size: 2.8rem;
            }
        }
        
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 20px;
                padding: 20px 5%;
            }
            
            nav ul {
                gap: 20px;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .hero h1 {
                font-size: 3rem;
            }
            
            .hero p {
                font-size: 1.2rem;
            }
            
            .hero-buttons {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .section-title {
                font-size: 2.5rem;
            }
            
            .final-cta h2 {
                font-size: 2.8rem;
            }
        }
        
        @media (max-width: 480px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .btn-primary, .btn-secondary {
                padding: 15px 35px;
                font-size: 1rem;
            }
            
            .section-title {
                font-size: 2.2rem;
            }
            
            .final-cta h2 {
                font-size: 2.2rem;
            }
            
            .cta-big {
                padding: 18px 40px;
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header Moderno -->
    <header>
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-cut"></i>
            </div>
            <div class="logo-text">VIBE BARBER</div>
        </div>
        
        <nav>
            <ul>
                <li><a href="#home">Início</a></li>
                <li><a href="#highlights">Destaques</a></li>
                <li><a href="#gallery">Galeria</a></li>
                <li><a href="#contact">Contato</a></li>
            </ul>
        </nav>
        
        <button class="cta-header" onclick="scrollToCTA()">Agendar</button>
    </header>

    <!-- Seção Hero -->
    <section class="hero" id="home">
        <div class="hero-bg"></div>
        
        <div class="floating-elements">
            <div class="floating-element"></div>
            <div class="floating-element"></div>
            <div class="floating-element"></div>
        </div>
        
        <div class="hero-content">
            <h1>ESTILO MODERNO PARA O HOMEM CONTEMPORÂNEO</h1>
            <p>Na Vibe Barber, combinamos técnicas tradicionais com as últimas tendências para criar looks únicos que expressam sua personalidade. Experiência premium em um ambiente descontraído.</p>
            
            <div class="hero-buttons">
                <button class="btn-primary" onclick="scrollToCTA()">AGENDAR HORÁRIO</button>
                <button class="btn-secondary" onclick="scrollToHighlights()">CONHECER SERVIÇOS</button>
            </div>
        </div>
    </section>

    <!-- Seção de Destaques -->
    <section class="highlights" id="highlights">
        <h2 class="section-title">POR QUE <span>ESCOLHER-NOS</span></h2>
        
        <div class="highlights-grid">
            <div class="highlight-card">
                <div class="highlight-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3>ATENDIMENTO RÁPIDO</h3>
                <p>Agilidade sem abrir mão da qualidade. Seu tempo é valioso.</p>
            </div>
            
            <div class="highlight-card">
                <div class="highlight-icon">
                    <i class="fas fa-star"></i>
                </div>
                <h3>PRODUTOS PREMIUM</h3>
                <p>Utilizamos apenas marcas de alta qualidade para cuidar do seu visual.</p>
            </div>
            
            <div class="highlight-card">
                <div class="highlight-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>BARBEIROS ESPECIALIZADOS</h3>
                <p>Profissionais constantemente atualizados com as últimas técnicas.</p>
            </div>
            
            <div class="highlight-card">
                <div class="highlight-icon">
                    <i class="fas fa-couch"></i>
                </div>
                <h3>AMBIENTE CONFORTAVEL</h3>
                <p>Design moderno, música boa e bebidas geladas para sua experiência.</p>
            </div>
        </div>
    </section>

    <!-- Seção de Galeria -->
    <section class="gallery" id="gallery">
        <h2 class="section-title">NOSSO <span>TRABALHO</span></h2>
        
        <div class="gallery-grid">
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1599351431202-1e0f0137899a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Corte moderno">
                <div class="gallery-overlay">
                    <h3>CORTE MODERNO</h3>
                </div>
            </div>
            
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1621605815971-fbc98d665033?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Barba estilo">
                <div class="gallery-overlay">
                    <h3>BARBA ESTILIZADA</h3>
                </div>
            </div>
            
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1580618672591-eb180b1a973f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Ambiente moderno">
                <div class="gallery-overlay">
                    <h3>AMBIENTE PREMIUM</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção CTA Final -->
    <section class="final-cta" id="cta">
        <h2>PRONTO PARA TRANSFORMAR SEU VISUAL?</h2>
        <p>Agende seu horário agora mesmo e experimente o padrão Vibe Barber de qualidade e estilo. Sua primeira visita inclui uma bebida cortesia.</p>
        <button class="cta-big" onclick="openBookingModal()">AGENDAR AGORA</button>
    </section>

    <!-- Footer Moderno -->
    <footer id="contact">
        <div class="footer-content">
            <div class="footer-section">
                <h3>VIBE BARBER</h3>
                <p>Reinventando a experiência de barbearia para o homem moderno. Técnica, estilo e inovação em um só lugar.</p>
                
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-tiktok"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>CONTATO</h3>
                <div class="contact-info">
                    <p><i class="fas fa-map-marker-alt"></i> Av. Modernidade, 456 - Centro</p>
                    <p><i class="fas fa-phone"></i> (11) 98765-4321</p>
                    <p><i class="fas fa-envelope"></i> contato@vibebarber.com.br</p>
                    <p><i class="fas fa-clock"></i> Seg-Sex: 9h-21h | Sáb: 9h-18h</p>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>NEWSLETTER</h3>
                <p>Receba nossas novidades e promoções exclusivas.</p>
                <div style="margin-top: 20px;">
                    <input type="email" placeholder="Seu e-mail" style="padding: 12px 15px; border-radius: 50px; border: none; width: 100%; background: rgba(255,255,255,0.1); color: white;">
                    <button style="margin-top: 10px; padding: 12px 25px; border-radius: 50px; border: none; background: var(--gradient); color: white; font-weight: bold; cursor: pointer;">INSCREVER</button>
                </div>
            </div>
        </div>
        
        <div class="copyright">
            <p>&copy; 2023 Vibe Barber. Todos os direitos reservados. | Design moderno e vibrante para uma nova geração.</p>
        </div>
    </footer>

    <script>
        // Funções de rolagem suave
        function scrollToCTA() {
            document.getElementById('cta').scrollIntoView({
                behavior: 'smooth'
            });
        }
        
        function scrollToHighlights() {
            document.getElementById('highlights').scrollIntoView({
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
        
        // Efeito de mudança no header ao rolar
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header');
            if (window.scrollY > 100) {
                header.style.backgroundColor = 'rgba(18, 18, 18, 0.98)';
                header.style.backdropFilter = 'blur(15px)';
                header.style.boxShadow = '0 10px 40px rgba(0, 0, 0, 0.4)';
            } else {
                header.style.backgroundColor = 'rgba(18, 18, 18, 0.95)';
                header.style.backdropFilter = 'blur(10px)';
                header.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.3)';
            }
        });
        
        // Simulação de modal de agendamento
        function openBookingModal() {
            alert("Sistema de agendamento online em breve!\nPor enquanto, entre em contato pelo WhatsApp: (11) 98765-4321\nOu nos visite na Av. Modernidade, 456 - Centro");
        }
        
        // Animação dos elementos flutuantes
        document.addEventListener('DOMContentLoaded', function() {
            const floatElements = document.querySelectorAll('.floating-element');
            floatElements.forEach(el => {
                // Posições aleatórias para os elementos
                const randomX = Math.random() * 80 + 10;
                const randomY = Math.random() * 80 + 10;
                const randomSize = Math.random() * 200 + 100;
                
                el.style.left = `${randomX}%`;
                el.style.top = `${randomY}%`;
                el.style.width = `${randomSize}px`;
                el.style.height = `${randomSize}px`;
                
                // Atraso de animação aleatório
                const randomDelay = Math.random() * 10;
                el.style.animationDelay = `-${randomDelay}s`;
            });
        });
        
        // Efeito de digitação no título (opcional)
        let typedIndex = 0;
        const typedText = "ESTILO MODERNO PARA O HOMEM CONTEMPORÂNEO";
        const titleElement = document.querySelector('.hero h1');
        
        function typeWriter() {
            if (typedIndex < typedText.length) {
                titleElement.innerHTML = typedText.substring(0, typedIndex+1) + '<span style="background: var(--gradient); -webkit-background-clip: text; background-clip: text; color: transparent;">|</span>';
                typedIndex++;
                setTimeout(typeWriter, 50);
            } else {
                titleElement.innerHTML = typedText;
            }
        }
        
        // Inicia a animação de digitação após um breve delay
        setTimeout(typeWriter, 500);
    </script>
</body>
</html>

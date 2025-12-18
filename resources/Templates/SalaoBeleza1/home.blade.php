<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ tenant()->fantasy_name ?? 'Belle Éclat' }} - Salão de Beleza</title>
    <!-- Google Fonts: Dancing Script -->
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <style>
        /* Reset e configurações básicas */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        /* Header e Navegação */
        header {
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
        }


        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #d4af37;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .salon-name-navbar {
            font-size: 2.3rem;
            font-family: 'Dancing Script', cursive;
            font-weight: 700;
            color: #b8941f;
            margin-left: 18px;
            letter-spacing: 1px;
            text-shadow: 0 2px 8px rgba(212,175,55,0.10);
            line-height: 1;
            white-space: nowrap;
            transition: color 0.3s;
        }

        .nav-links {
            display: flex;
            list-style: none;
        }

        .nav-links li {
            margin-left: 30px;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #d4af37;
        }

        .menu-toggle {
            display: none;
            cursor: pointer;
            font-size: 24px;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('images/SalaoBeleza1/hero.jpeg') no-repeat center center/cover;
            background-size: cover;
            background-position: center;
            height: 80vh;
            display: flex;
            align-items: center;
            text-align: center;
            color: white;
            margin-top: 70px;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 20px;
            margin-bottom: 30px;
        }

        .btn {
            display: inline-block;
            background-color: #d4af37;
            color: white;
            padding: 12px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #b8941f;
        }

        /* Serviços */
        .services {
            padding: 80px 0;
            background-color: white;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-size: 36px;
            color: #333;
            margin-bottom: 15px;
        }

        .section-title p {
            color: #777;
            max-width: 600px;
            margin: 0 auto;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .service-card {
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .service-card:hover {
            transform: translateY(-10px);
        }

        .service-img {
            height: 200px;
            overflow: hidden;
        }

        .service-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .service-card:hover .service-img img {
            transform: scale(1.1);
        }

        .service-info {
            padding: 20px;
        }

        .service-info h3 {
            font-size: 22px;
            margin-bottom: 10px;
            color: #333;
        }

        .service-info p {
            color: #777;
            margin-bottom: 15px;
        }

        /* Sobre */
        .about {
            padding: 80px 0;
            background-color: #f9f9f9;
        }

        .about-content {
            display: flex;
            align-items: center;
            gap: 50px;
        }

        .about-img {
            flex: 1;
            border-radius: 10px;
            overflow: hidden;
        }

        .about-img img {
            width: 100%;
            height: auto;
            display: block;
        }

        .about-text {
            flex: 1;
        }

        .about-text h2 {
            font-size: 36px;
            margin-bottom: 20px;
            color: #333;
        }

        .about-text p {
            margin-bottom: 20px;
            color: #555;
        }

        /* Depoimentos */
        .testimonials {
            padding: 80px 0;
            background-color: white;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .testimonial-card {
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .testimonial-text {
            font-style: italic;
            margin-bottom: 20px;
            color: #555;
        }

        .client-info {
            display: flex;
            align-items: center;
        }

        .client-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 15px;
        }

        .client-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .client-name {
            font-weight: bold;
            color: #333;
        }

        /* Contato */
        .contact {
            padding: 80px 0;
            background-color: #f9f9f9;
        }

        .contact-content {
            display: flex;
            gap: 50px;
        }

        .contact-info {
            flex: 1;
        }

        .contact-info h3 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        .contact-details {
            margin-bottom: 30px;
        }

        .contact-details p {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .contact-details i {
            margin-right: 10px;
            color: #d4af37;
        }

        .contact-form {
            flex: 1;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-group textarea {
            height: 150px;
            resize: vertical;
        }

        /* Footer */
        footer {
            background-color: #333;
            color: white;
            padding: 50px 0 20px;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .footer-column {
            flex: 1;
        }

        .footer-column h3 {
            font-size: 20px;
            margin-bottom: 20px;
            color: #d4af37;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: #bbb;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: #d4af37;
        }

        .social-links {
            display: flex;
            gap: 15px;
        }

        .social-links a {
            color: white;
            font-size: 20px;
            transition: color 0.3s;
        }

        .social-links a:hover {
            color: #d4af37;
        }

        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #444;
            color: #bbb;
            font-size: 14px;
        }

        /* Responsividade */
        @media (max-width: 992px) {
            .about-content, .contact-content {
                flex-direction: column;
            }
            
            .about-img, .about-text, .contact-info, .contact-form {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 15px 0;
            }
            
            .nav-links {
                display: none;
                position: absolute;
                top: 70px;
                left: 0;
                width: 100%;
                background-color: white;
                flex-direction: column;
                padding: 20px;
                box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            }
            
            .nav-links.active {
                display: flex;
            }
            
            .nav-links li {
                margin: 10px 0;
            }
            
            .menu-toggle {
                display: block;
            }
            
            .hero h1 {
                font-size: 36px;
            }
            
            .hero p {
                font-size: 18px;
            }
            
            .footer-content {
                flex-direction: column;
                gap: 30px;
            }
        }

        @media (max-width: 576px) {
            .hero {
                height: 60vh;
            }
            
            .hero h1 {
                font-size: 28px;
            }
            
            .hero p {
                font-size: 16px;
            }
            
            .section-title h2 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <nav class="navbar">
                <div class="logo">
                    <img src="{{ tenant()->logo ?? '' }}" alt="{{ tenant()->fantasy_name ?? 'Belle Éclat' }}" style="width:54px; height:54px; object-fit:cover; border-radius:50%; box-shadow:0 2px 8px #d4af37a0;">
                    <span class="salon-name-navbar">{{ tenant()->fantasy_name ?? 'Belle Éclat' }}</span>
                </div>
                <ul class="nav-links">
                    <li><a href="#home">Home</a></li>
                    <li><a href="#services">Serviços</a></li>
                    <li><a href="#about">Sobre</a></li>
                    <li><a href="#testimonials">Depoimentos</a></li>
                    <li><a href="#contact">Contato</a></li>
                    <li><a href="login" ">Agendar Horário</a></li>
                    <li><a href="register" >Registrar</a></li>
                </ul>
                <div class="menu-toggle">☰</div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container">
            <div class="hero-content">
                <h1>Realce sua beleza natural</h1>
                <p>No {{ tenant()->fantasy_name ?? 'Belle Éclat' }}, oferecemos tratamentos exclusivos para realçar sua beleza única com técnicas modernas e produtos de alta qualidade.</p>
                <a href="/login" class="btn">Agende seu horário</a>
            </div>
        </div>
    </section>

    <!-- Serviços -->
    <section class="services" id="services">
        <div class="container">
            <div class="section-title">
                <h2>Nossos Serviços</h2>
                <p>Descubra nossa gama completa de serviços de beleza para cuidar de você do início ao fim.</p>
            </div>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-img">
                        <img src="https://images.unsplash.com/photo-1562322140-8baeececf3df?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Corte de Cabelo">
                    </div>
                    <div class="service-info">
                        <h3>Cortes e Penteados</h3>
                        <p>Cortes modernos e penteados exclusivos para todas as ocasiões, realizados por nossos especialistas.</p>
                       
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-img">
                        <img src="https://images.unsplash.com/photo-1512496015851-a90fb38ba796?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Coloração">
                    </div>
                    <div class="service-info">
                        <h3>Coloração</h3>
                        <p>Desde mechas sutis a transformações completas, trabalhamos com as melhores marcas do mercado.</p>
                       
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-img">
                        <img src="https://images.unsplash.com/photo-1596462502278-27bfdc403348?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Manicure e Pedicure">
                    </div>
                    <div class="service-info">
                        <h3>Manicure e Pedicure</h3>
                        <p>Cuidados completos para suas mãos e pés, com técnicas de spa e os melhores esmaltes.</p>
                        
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-img">
                        <img src="https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Tratamentos Faciais">
                    </div>
                    <div class="service-info">
                        <h3>Tratamentos Faciais</h3>
                        <p>Limpeza de pele, hidratação e tratamentos antienvelhecimento para uma pele radiante.</p>
                        
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-img">
                        <img src="/images/SalaoBeleza1/maquiagem.jpeg" 
                        alt="Maquiagem">
                        
                    </div>
                    <div class="service-info">
                        <h3>Maquiagem</h3>
                        <p>Maquiagem profissional para eventos especiais ou para o dia a dia, realçando sua beleza natural.</p>
                        
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-img">
                        <img src="https://images.unsplash.com/photo-1600948836101-f9ffda59d250?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Depilação">
                    </div>
                    <div class="service-info">
                        <h3>Depilação</h3>
                        <p>Técnicas modernas de depilação para resultados duradouros e confortáveis.</p>
                       
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sobre -->
    <section class="about" id="about">
        <div class="container">
            <div class="about-content">
                <div class="about-img">
                    <img src="https://images.unsplash.com/photo-1560448204-603b3fc33ddc?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Sobre o Salão">
                </div>
                <div class="about-text">
                    <h2>Sobre o {{ tenant()->fantasy_name ?? 'Belle Éclat' }}</h2>
                    <p>Há mais de 10 anos no mercado, o {{ tenant()->fantasy_name ?? 'Belle Éclat' }} se consolidou como referência em beleza e bem-estar. Nossa missão é proporcionar uma experiência única e personalizada para cada cliente.</p>
                    <p>Contamos com uma equipe de profissionais altamente qualificados e em constante atualização, sempre buscando as melhores técnicas e produtos para oferecer serviços de excelência.</p>
                    <p>No {{ tenant()->fantasy_name ?? 'Belle Éclat' }}, acreditamos que a beleza vai além da estética - é sobre autoestima, confiança e bem-estar. Por isso, nos dedicamos a criar um ambiente acolhedor onde você possa relaxar e se sentir especial.</p>
                    <a href="#contact" class="btn">Conheça nossa equipe</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Depoimentos -->
    <section class="testimonials" id="testimonials">
        <div class="container">
            <div class="section-title">
                <h2>O que Nossas Clientes Dizem</h2>
                <p>Confira alguns depoimentos de clientes satisfeitas com nossos serviços.</p>
            </div>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        "Adorei a experiência no {{ tenant()->fantasy_name ?? 'Belle Éclat' }}! A equipe é super atenciosa e o resultado do meu corte ficou incrível. Com certeza voltarei!"
                    </div>
                    <div class="client-info">
                        <div class="client-avatar">
                            <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="Maria Silva">
                        </div>
                        <div class="client-name">Maria Silva</div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        "Fiz a coloração dos meus sonhos! A profissional foi incrível, entendeu exatamente o que eu queria. O ambiente é muito aconchegante."
                    </div>
                    <div class="client-info">
                        <div class="client-avatar">
                            <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Ana Costa">
                        </div>
                        <div class="client-name">Ana Costa</div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        "Sou cliente há anos e nunca me decepcionei. Os tratamentos faciais são maravilhosos e deixam minha pele radiante. Recomendo!"
                    </div>
                    <div class="client-info">
                        <div class="client-avatar">
                            <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Juliana Santos">
                        </div>
                        <div class="client-name">Juliana Santos</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contato -->
    <section class="contact" id="contact">
        <div class="container">
            <div class="section-title">
                <h2>Entre em Contato</h2>
                <p>Agende seu horário ou tire suas dúvidas conosco.</p>
            </div>
            <div class="contact-content">
                <div class="contact-info">
                    <h3>Informações de Contato</h3>
                    <div class="contact-details">
                        <p>📍 {{ tenant()->address ?? 'Rua das Flores, 123 - Centro, São Paulo - SP' }}</p>
                        <p>📞 {{ tenant()->phone ?? '(11) 3456-7890' }}</p>
                        <p>📱 {{ tenant()->whatsapp ?? '(11) 98765-4321' }}</p>
                        <p>✉️ {{ tenant()->email ?? 'contato@belleeclat.com.br' }}</p>
                    </div>
                    <h3>Horário de Funcionamento</h3>
                    <div class="contact-details">
                        <p>Segunda a Sexta: 9h às 20h</p>
                        <p>Sábado: 9h às 18h</p>
                        <p>Domingo: Fechado</p>
                    </div>
                </div>
                
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>{{ tenant()->fantasy_name ?? 'Belle Éclat' }}</h3>
                    <p>Há mais de 10 anos realçando a beleza natural de nossas clientes com serviços de alta qualidade e atendimento personalizado.</p>
                    <div class="social-links">
                        <a href="#">📘</a>
                        <a href="#">📷</a>
                        <a href="#">🐦</a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>Links Rápidos</h3>
                    <ul class="footer-links">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#services">Serviços</a></li>
                        <li><a href="#about">Sobre</a></li>
                        <li><a href="#testimonials">Depoimentos</a></li>
                        <li><a href="#contact">Contato</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Serviços</h3>
                    <ul class="footer-links">
                        <li><a href="#">Cortes e Penteados</a></li>
                        <li><a href="#">Coloração</a></li>
                        <li><a href="#">Manicure e Pedicure</a></li>
                        <li><a href="#">Tratamentos Faciais</a></li>
                        <li><a href="#">Maquiagem</a></li>
                        <li><a href="#">Depilação</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2023 {{ tenant()->fantasy_name ?? 'Belle Éclat' }} - Todos os direitos reservados</p>
            </div>
        </div>
    </footer>

    <script>
        // Menu toggle para dispositivos móveis
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.nav-links').classList.toggle('active');
        });

        // Fechar menu ao clicar em um link
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', () => {
                document.querySelector('.nav-links').classList.remove('active');
            });
        });

        // Smooth scroll para âncoras
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if(targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if(targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 70,
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>
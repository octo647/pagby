<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barber Shop - Barbearia Americana</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #0d0d0d;
            color: #f1f1f1;
            overflow-x: hidden;
        }
        
        /* Estilo do cabeçalho */
        header {
            background-color: rgba(13, 13, 13, 0.95);
            padding: 20px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
        }
        
        .logo {
            display: flex;
            align-items: center;
        }
        
        .logo-icon {
            color: #c9a769;
            font-size: 2.5rem;
            margin-right: 10px;
        }
        
        .logo-text {
            font-family: 'Times New Roman', serif;
            font-size: 1.8rem;
            font-weight: bold;
            color: #c9a769;
            letter-spacing: 1px;
        }
        
        .logo-text span {
            color: #b22222;
        }
        
        nav ul {
            display: flex;
            list-style: none;
        }
        
        nav li {
            margin-left: 30px;
        }
        
        nav a {
            color: #f1f1f1;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: color 0.3s;
        }
        
        nav a:hover {
            color: #c9a769;
        }
        
        /* Seção principal */
        .hero {
            height: 100vh;
            background-image: linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1585747860715-2ba37e788b70?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1474&q=80');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 0 5%;
            position: relative;
        }
        
        .hero-content {
            max-width: 900px;
            z-index: 2;
        }
        
        .hero h1 {
            font-family: 'Times New Roman', serif;
            font-size: 4.5rem;
            color: #c9a769;
            margin-bottom: 20px;
            text-shadow: 3px 3px 5px rgba(0, 0, 0, 0.8);
            letter-spacing: 2px;
        }
        
        .hero p {
            font-size: 1.5rem;
            margin-bottom: 40px;
            line-height: 1.6;
            color: #e0e0e0;
        }
        
        .btn {
            background-color: #b22222;
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 30px;
            font-size: 1.2rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .btn:hover {
            background-color: #c9a769;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        }
        
        /* Elementos decorativos da barbearia */
        .barber-pole {
            position: absolute;
            right: 10%;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 200px;
            background: linear-gradient(to bottom, #b22222 33%, white 33%, white 66%, #0d47a1 66%);
            border-radius: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            animation: spin 4s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: translateY(-50%) rotate(0deg); }
            100% { transform: translateY(-50%) rotate(360deg); }
        }
        
        /* Seção de serviços */
        .services {
            padding: 100px 5%;
            background-color: #1a1a1a;
        }
        
        .section-title {
            text-align: center;
            font-family: 'Times New Roman', serif;
            font-size: 3rem;
            color: #c9a769;
            margin-bottom: 60px;
            position: relative;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            width: 100px;
            height: 3px;
            background-color: #b22222;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .service-card {
            background-color: #262626;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s;
        }
        
        .service-card:hover {
            transform: translateY(-10px);
        }
        
        .service-icon {
            background-color: #b22222;
            height: 80px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 2.5rem;
            color: white;
        }
        
        .service-content {
            padding: 30px;
        }
        
        .service-content h3 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: #c9a769;
        }
        
        .service-content p {
            color: #b0b0b0;
            line-height: 1.6;
        }
        
        /* Rodapé */
        footer {
            background-color: #0d0d0d;
            padding: 60px 5% 30px;
            border-top: 2px solid #c9a769;
        }
        
        .footer-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto 40px;
        }
        
        .footer-section {
            flex: 1;
            min-width: 300px;
            margin-bottom: 30px;
        }
        
        .footer-section h3 {
            font-size: 1.5rem;
            color: #c9a769;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer-section h3:after {
            content: '';
            position: absolute;
            width: 50px;
            height: 2px;
            background-color: #b22222;
            bottom: 0;
            left: 0;
        }
        
        .contact-info p {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .contact-info i {
            margin-right: 10px;
            color: #c9a769;
        }
        
        .social-icons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-icons a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: #262626;
            border-radius: 50%;
            color: #c9a769;
            font-size: 1.2rem;
            transition: all 0.3s;
        }
        
        .social-icons a:hover {
            background-color: #b22222;
            color: white;
            transform: translateY(-3px);
        }
        
        .copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid #333;
            color: #888;
            font-size: 0.9rem;
        }
        
        /* Responsividade */
        @media (max-width: 992px) {
            .hero h1 {
                font-size: 3.5rem;
            }
            
            .hero p {
                font-size: 1.3rem;
            }
            
            .barber-pole {
                display: none;
            }
        }
        
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                padding: 15px 5%;
            }
            
            .logo {
                margin-bottom: 15px;
            }
            
            nav ul {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            nav li {
                margin: 5px 15px;
            }
            
            .hero h1 {
                font-size: 2.8rem;
            }
            
            .hero p {
                font-size: 1.1rem;
            }
            
            .section-title {
                font-size: 2.5rem;
            }
        }
        
        @media (max-width: 480px) {
            .hero h1 {
                font-size: 2.2rem;
            }
            
            .btn {
                padding: 12px 30px;
                font-size: 1rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .service-content h3 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Cabeçalho com navegação -->
    <header>
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-cut"></i>
            </div>
            <div class="logo-text">OLD SCHOOL<span>BARBER</span></div>
        </div>
        <nav>
            <ul>
                <li><a href="#home">Início</a></li>
                <li><a href="#services">Serviços</a></li>
                <li><a href="#about">Sobre</a></li>
                <li><a href="#contact">Contato</a></li>
            </ul>
        </nav>
    </header>

    <!-- Seção principal -->
    <section class="hero" id="home">
        <div class="barber-pole"></div>
        <div class="hero-content">
            <h1>BARBER SHOP AMERICANA</h1>
            <p>Desde 1950, oferecendo cortes clássicos e cuidados masculinos com a tradição das barbearias americanas. Um lugar onde estilo, história e profissionalismo se encontram.</p>
            <button class="btn" onclick="scrollToServices()">AGENDE SEU HORÁRIO</button>
        </div>
    </section>

    <!-- Seção de serviços -->
    <section class="services" id="services">
        <h2 class="section-title">NOSSOS SERVIÇOS</h2>
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-scissors"></i>
                </div>
                <div class="service-content">
                    <h3>CORTE TRADICIONAL</h3>
                    <p>Cortes de cabelo clássicos e modernos, realizados com técnicas tradicionais de barbearia americana. Inclui acabamento com navalha.</p>
                </div>
            </div>
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-air-freshener"></i>
                </div>
                <div class="service-content">
                    <h3>BARBA COMPLETA</h3>
                    <p>Tratamento completo para barba com toalha quente, óleos especiais e acabamento com navalha para um visual impecável.</p>
                </div>
            </div>
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-spa"></i>
                </div>
                <div class="service-content">
                    <h3>TRATAMENTOS</h3>
                    <p>Relaxe com nossos tratamentos faciais masculinos, que incluem limpeza profunda e hidratação para uma pele saudável.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Rodapé -->
    <footer id="contact">
        <div class="footer-content">
            <div class="footer-section">
                <h3>OLD SCHOOL BARBER</h3>
                <p>Uma barbearia que mantém viva a tradição das clássicas barbearias americanas, combinando técnica, estilo e um ambiente exclusivamente masculino.</p>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h3>CONTATO</h3>
                <div class="contact-info">
                    <p><i class="fas fa-map-marker-alt"></i> Rua da Tradição, 123 - Centro</p>
                    <p><i class="fas fa-phone"></i> (11) 3456-7890</p>
                    <p><i class="fas fa-envelope"></i> contato@oldschoolbarber.com</p>
                    <p><i class="fas fa-clock"></i> Seg-Sex: 9h-19h | Sáb: 9h-17h</p>
                </div>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2023 Old School Barber. Todos os direitos reservados. | Desenvolvido com o estilo clássico das barbearias americanas.</p>
        </div>
    </footer>

    <script>
        // Função para rolar suavemente até a seção de serviços
        function scrollToServices() {
            document.getElementById('services').scrollIntoView({
                behavior: 'smooth'
            });
        }
        
        // Adiciona efeito de rolagem suave para todos os links internos
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
        
        // Adiciona efeito de mudança de cor no header ao rolar
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header');
            if (window.scrollY > 50) {
                header.style.backgroundColor = 'rgba(13, 13, 13, 0.98)';
                header.style.boxShadow = '0 5px 20px rgba(0, 0, 0, 0.7)';
            } else {
                header.style.backgroundColor = 'rgba(13, 13, 13, 0.95)';
                header.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.5)';
            }
        });
    </script>
</body>
</html>

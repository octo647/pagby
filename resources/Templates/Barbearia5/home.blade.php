<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ tenant()->fantasy_name ?? 'Barbearia Urbana' }} | Estilo Jovem</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Reset e configurações gerais */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary: #6C63FF;
            --secondary: #FF6584;
            --accent: #00D2A2;
            --dark: #1A1A2E;
            --light: #F8F9FA;
            --gray: #6C757D;
        }
        
        body {
            color: var(--dark);
            line-height: 1.6;
            overflow-x: hidden;
            background-color: var(--light);
            font-family: 'Poppins', sans-serif;
        }
        
        /* Cabeçalho */
        header {
            background-color: rgba(255, 255, 255, 0.95);
            position: fixed;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
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
            font-size: 28px;
            font-weight: 800;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .logo span {
            color: var(--secondary);
        }
        
        .logo i {
            margin-right: 10px;
            font-size: 32px;
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
            color: var(--dark);
            font-weight: 600;
            transition: all 0.3s;
            font-size: 16px;
            position: relative;
        }
        
        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 3px;
            bottom: -5px;
            left: 0;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transition: width 0.3s;
            border-radius: 2px;
        }
        
        .nav-links a:hover {
            color: var(--primary);
        }
        
        .nav-links a:hover::after {
            width: 100%;
        }
        
        .btn-agendar {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            color: white;
            padding: 12px 25px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(108, 99, 255, 0.3);
            border: none;
            cursor: pointer;
            font-size: 15px;
        }
        
        .btn-agendar:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(108, 99, 255, 0.4);
        }
        
        .menu-toggle {
            display: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--primary);
        }
        
        /* Seção Hero */
        .hero {
            height: 100vh;
            background: linear-gradient(135deg, rgba(108, 99, 255, 0.2), rgba(255, 101, 132, 0.2)), url('/images/Barbearia5/barber.webp') no-repeat center center/cover;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .hero-content {
            max-width: 700px;
            z-index: 2;
        }
        
        .hero h1 {
            font-size: 4rem;
            margin-bottom: 20px;
            font-weight: 800;
            line-height: 1.1;
        }
        
        .hero p {
            font-size: 1.3rem;
            margin-bottom: 40px;
            opacity: 0.9;
            max-width: 600px;
        }
        
        .btn-hero {
            background: white;
            color: var(--primary);
            padding: 16px 40px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            display: inline-block;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .btn-hero:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        
        .hero-shapes {
            position: absolute;
            top: 0;
            right: 0;
            width: 50%;
            height: 100%;
            overflow: hidden;
        }
        
        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
        }
        
        .shape-1 {
            width: 300px;
            height: 300px;
            background: white;
            top: -100px;
            right: -50px;
        }
        
        .shape-2 {
            width: 200px;
            height: 200px;
            background: var(--accent);
            bottom: 100px;
            right: 200px;
        }
        
        .shape-3 {
            width: 150px;
            height: 150px;
            background: var(--secondary);
            top: 200px;
            right: 400px;
        }
        
        /* Seção Serviços */
        .servicos {
            padding: 120px 0;
            background-color: white;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 80px;
        }
        
        .section-title h2 {
            font-size: 3rem;
            color: var(--dark);
            margin-bottom: 15px;
            font-weight: 800;
            position: relative;
            display: inline-block;
        }
        
        .section-title h2::after {
            content: '';
            position: absolute;
            width: 80px;
            height: 5px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 5px;
        }
        
        .section-title p {
            color: var(--gray);
            max-width: 600px;
            margin: 0 auto;
            font-size: 1.1rem;
        }
        
        .servicos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .servico-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            position: relative;
            border: 1px solid #f0f0f0;
        }
        
        .servico-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }
        
        .servico-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 25px;
            color: white;
            font-size: 28px;
        }
        
        .servico-content {
            padding: 0 25px 25px;
        }
        
        .servico-content h3 {
            font-size: 1.5rem;
            margin-bottom: 12px;
            color: var(--dark);
            font-weight: 700;
        }
        
        .servico-content p {
            color: var(--gray);
            margin-bottom: 20px;
            font-size: 15px;
        }
        
        .servico-preco {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .servico-btn {
            background: var(--accent);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .servico-btn:hover {
            background: var(--primary);
            transform: scale(1.05);
        }
        
        /* Seção Destaque */
        .destaque {
            padding: 100px 0;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            text-align: center;
        }
        
        .destaque h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            font-weight: 800;
        }
        
        .destaque p {
            font-size: 1.2rem;
            margin-bottom: 40px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .btn-destaque {
            background: white;
            color: var(--primary);
            padding: 15px 35px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            display: inline-block;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .btn-destaque:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        
        /* Seção Sobre */
        .sobre {
            padding: 120px 0;
            background-color: var(--light);
        }
        
        .sobre-content {
            display: flex;
            align-items: center;
            gap: 60px;
        }
        
        .sobre-img {
            flex: 1;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        
        .sobre-img::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 20px;
            right: -20px;
            bottom: -20px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            z-index: -1;
            border-radius: 15px;
            opacity: 0.2;
        }
        
        .sobre-img img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        .sobre-text {
            flex: 1;
        }
        
        .sobre-text h2 {
            font-size: 2.8rem;
            margin-bottom: 25px;
            color: var(--dark);
            font-weight: 800;
        }
        
        .sobre-text p {
            margin-bottom: 20px;
            color: var(--gray);
            font-size: 1.1rem;
        }
        
        .sobre-stats {
            display: flex;
            gap: 30px;
            margin-top: 40px;
        }
        
        .stat {
            text-align: center;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 5px;
        }
        
        .stat-text {
            color: var(--gray);
            font-weight: 600;
        }
        
        /* Seção Galeria */
        .galeria {
            padding: 120px 0;
            background-color: white;
        }
        
        .galeria-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .galeria-item {
            height: 280px;
            border-radius: 15px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .galeria-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .galeria-item:hover img {
            transform: scale(1.1);
        }
        
        .galeria-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
            display: flex;
            align-items: flex-end;
            padding: 20px;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .galeria-item:hover .galeria-overlay {
            opacity: 1;
        }
        
        .galeria-overlay h3 {
            color: white;
            font-size: 1.2rem;
        }
        
        /* Seção Contato */
        .contato {
            padding: 120px 0;
            background-color: var(--light);
        }
        
        .contato-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 60px;
        }
        
        .contato-info h3 {
            font-size: 2rem;
            margin-bottom: 30px;
            color: var(--dark);
            font-weight: 800;
        }
        
        .contato-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 25px;
        }
        
        .contato-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        
        .contato-text h4 {
            font-size: 1.1rem;
            margin-bottom: 5px;
            color: var(--dark);
            font-weight: 700;
        }
        
        .contato-text p {
            color: var(--gray);
        }
        
        .contato-form {
            background-color: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark);
        }
        
        .form-control {
            width: 100%;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
            font-family: 'Poppins', sans-serif;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.1);
        }
        
        .btn-enviar {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 15px 35px;
            border-radius: 30px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1rem;
            width: 100%;
            box-shadow: 0 4px 15px rgba(108, 99, 255, 0.3);
        }
        
        .btn-enviar:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(108, 99, 255, 0.4);
        }
        
        /* Rodapé */
        footer {
            background-color: var(--dark);
            color: white;
            padding: 80px 0 30px;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 60px;
        }
        
        .footer-col h4 {
            font-size: 1.3rem;
            margin-bottom: 25px;
            font-weight: 700;
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer-col h4::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border-radius: 3px;
        }
        
        .footer-col ul {
            list-style: none;
        }
        
        .footer-col ul li {
            margin-bottom: 12px;
        }
        
        .footer-col ul li a {
            color: #b0b0b0;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-col ul li a:hover {
            color: white;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
        }
        
        .social-links a {
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: all 0.3s;
        }
        
        .social-links a:hover {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            transform: translateY(-3px);
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #b0b0b0;
            font-size: 0.9rem;
        }
        
        /* Responsividade */
        @media (max-width: 992px) {
            .sobre-content {
                flex-direction: column;
            }
            
            .sobre-img, .sobre-text {
                flex: none;
                width: 100%;
            }
            
            .hero h1 {
                font-size: 3rem;
            }
        }
        
        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }
            
            .nav-links {
                position: fixed;
                top: 80px;
                left: -100%;
                width: 100%;
                height: calc(100vh - 80px);
                background-color: white;
                flex-direction: column;
                align-items: center;
                justify-content: flex-start;
                padding-top: 50px;
                transition: left 0.3s;
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            }
            
            .nav-links.active {
                left: 0;
            }
            
            .nav-links li {
                margin: 20px 0;
            }
            
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .section-title h2 {
                font-size: 2.2rem;
            }
            
            .sobre-stats {
                flex-direction: column;
                gap: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Cabeçalho -->
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="#" class="logo">
                    <i class="fas fa-cut"></i>
                   {!! tenant()->fantasy_name ?? 'BARBEARIA <span>URBANA</span>' !!} 
                </a>
                
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

    <!-- Seção Hero -->
    <section class="hero" id="inicio">
        <div class="container">
            <div class="hero-content">
                <h1>ESTILO URBANO PARA A NOVA GERAÇÃO</h1>
                <p>Descubra o lugar onde a cultura jovem encontra os melhores cortes e cuidados masculinos. Tecnologia, estilo e atitude em um só lugar.</p>
                <a href="/login" class="btn-hero">AGENDAR HORÁRIO</a>
            </div>
        </div>
        <div class="hero-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
    </section>

    <!-- Seção Serviços -->
    <section class="servicos" id="servicos">
        <div class="container">
            <div class="section-title">
                <h2>NOSSOS SERVIÇOS</h2>
                <p>Cortes modernos, técnicas avançadas e produtos premium para você se destacar.</p>
            </div>
            
            <div class="servicos-grid">
                <div class="servico-card">
                    <div class="servico-icon">
                        <i class="fas fa-cut"></i>
                    </div>
                    <div class="servico-content">
                        <h3>CORTE URBANO</h3>
                        <p>Estilo personalizado com as últimas tendências. Degradê, undercut ou o que sua vibe pedir.</p>
                        <div class="servico-preco">
                            R$ 35,00
                            <a href="/login" class="servico-btn">AGENDAR</a>
                        </div>
                    </div>
                </div>
                
                <div class="servico-card">
                    <div class="servico-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="servico-content">
                        <h3>BARBA STREET</h3>
                        <p>Modelagem perfeita com produtos que cuidam da sua pele. Do designer à barba cheia.</p>
                        <div class="servico-preco">
                            R$ 25,00
                            <a href="/login" class="servico-btn">AGENDAR</a>
                        </div>
                    </div>
                </div>
                
                <div class="servico-card">
                    <div class="servico-icon">
                        <i class="fas fa-crown"></i>
                    </div>
                    <div class="servico-content">
                        <h3>COMBO VIP</h3>
                        <p>Experiência completa: corte, barba, hidratação e cuidados especiais. O pacote premium.</p>
                        <div class="servico-preco">
                            R$ 55,00
                            <a href="/login" class="servico-btn">AGENDAR</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção Destaque -->
    <section class="destaque">
        <div class="container">
            <h2>PRIMEIRA VEZ NA BARBEARIA URBANA?</h2>
            <p>Novos clientes ganham 20% de desconto no primeiro agendamento! Venha fazer parte da nossa comunidade.</p>
            <a href="/login" class="btn-destaque">GARANTIR DESCONTO</a>
        </div>
    </section>

    <!-- Seção Sobre -->
    <section class="sobre" id="sobre">
        <div class="container">
            <div class="sobre-content">
                <div class="sobre-img">
                    <img src="/images/Barbearia5/photo.avif" alt="Ambiente da barbearia">
                </div>
                <div class="sobre-text">
                    <h2>MAIS DO QUE UMA BARBEARIA, UM PONTO DE ENCONTRO</h2>
                    <p>A {{ tenant()->fantasy_name ?? 'Barbearia Urbana' }} nasceu para ser o lugar onde a galera se encontra, troca ideia e sai ainda mais estilosa. Combinamos técnicas tradicionais com o que há de mais moderno no universo masculino.</p>
                    <p>Nossa equipe é formada por jovens talentos que entendem de tendências, sabem ouvir e estão sempre atualizados com as novidades do mundo da beleza masculina.</p>
                    
                    <div class="sobre-stats">
                        <div class="stat">
                            <div class="stat-number">500+</div>
                            <div class="stat-text">Clientes Satisfeitos</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">3</div>
                            <div class="stat-text">Anos no Mercado</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">4.9</div>
                            <div class="stat-text">Avaliação no Google</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção Galeria -->
    <section class="galeria" id="galeria">
        <div class="container">
            <div class="section-title">
                <h2>NOSSO ESPAÇO</h2>
                <p>Ambiente descontraído, música boa e a energia que a galera curte.</p>
            </div>
            
            <div class="galeria-grid">
                <div class="galeria-item">
                    <img src="/images/Barbearia5/corte1.jpeg" alt="Galeria 1">
                    <div class="galeria-overlay">
                        <h3>Corte Moderno</h3>
                    </div>
                </div>
                <div class="galeria-item">
                    <img src="/images/Barbearia5/barber2.webp" alt="Galeria 2">
                    <div class="galeria-overlay">
                        <h3>Ambiente Descontraído</h3>
                    </div>
                </div>
                <div class="galeria-item">
                    <img src="/images/Barbearia5/homem-com-barba.jpg" alt="Galeria 3">
                    <div class="galeria-overlay">
                        <h3>Equipe Profissional</h3>
                    </div>
                </div>
                <div class="galeria-item">
                    <img src="/images/Barbearia5/corte2.jpeg" alt="Galeria 4">
                    <div class="galeria-overlay">
                        <h3>Produtos Premium</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção Contato -->
    <section class="contato" id="contato">
        <div class="container">
            <div class="section-title">
                <h2>FALE COM A GENTE</h2>
                <p>Agende seu horário ou tire suas dúvidas. Respondemos rapidinho!</p>
            </div>
            
            <div class="contato-content">
                <div class="contato-info">
                    <h3>VEM TOMAR UM CAFÉ COM A GENTE</h3>
                    
                    <div class="contato-item">
                        <div class="contato-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contato-text">
                            <h4>ONDE ESTAMOS</h4>
                            <p>{{ tenant()->address ?? 'Rua da Juventude, 123 - Centro<br>São Paulo, SP' }}</p>
                        </div>
                    </div>
                    
                    <div class="contato-item">
                        <div class="contato-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contato-text">
                            <h4>TELEFONE / WHATSAPP</h4>
                            <p>{{ tenant()->phone ?? '(11) 99999-9999' }}</p>
                        </div>
                    </div>
                    
                    <div class="contato-item">
                        <div class="contato-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contato-text">
                            <h4>E-MAIL</h4>
                            <p>{{ tenant()->email ?? 'contato@barbeariaurbana.com' }}</p>
                        </div>
                    </div>
                    
                    <div class="contato-item">
                        <div class="contato-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contato-text">
                            <h4>HORÁRIOS</h4>
                            <p>Segunda a Sexta: 9h às 21h<br>Sábado: 9h às 18h<br>Domingo: 10h às 14h</p>
                        </div>
                    </div>
                </div>
                
                <div class="contato-form" id="agendar">
                    <h3>Registre-se e use nosso serviço de agendamento online!</h3>
                    <br>
                    <br>
                    <div style="text-align: center;">
                    <a href="/login" class="btn-agendar">Agendar Horário</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Rodapé -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-col">
                    <h4>{{ tenant()->fantasy_name ?? 'BARBEARIA URBANA' }}</h4>
                    <p style="color: #b0b0b0; line-height: 1.6;">O point da galera que curte estilo, qualidade e um ambiente descontraído. Aqui, cada corte tem atitude!</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                        <a href="#"><i class="fab fa-spotify"></i></a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h4>LINKS RÁPIDOS</h4>
                    <ul>
                        <li><a href="#inicio">INÍCIO</a></li>
                        <li><a href="#servicos">SERVIÇOS</a></li>
                        <li><a href="#sobre">SOBRE</a></li>
                        <li><a href="#galeria">GALERIA</a></li>
                        <li><a href="#contato">CONTATO</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>SERVIÇOS</h4>
                    <ul>
                        <li><a href="#">CORTE URBANO</a></li>
                        <li><a href="#">BARBA STREET</a></li>
                        <li><a href="#">COMBO VIP</a></li>
                        <li><a href="#">CONSULTORIA DE ESTILO</a></li>
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
                <p>&copy; 2023 {{ tenant()->fantasy_name ?? 'BARBEARIA URBANA' }}. FEITO COM ❤️ PELA GALERA QUE ENTENDE DE ESTILO.</p>
            </div>
        </div>
    </footer>

    <script>
        // Menu responsivo
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.nav-links').classList.toggle('active');
        });
        
        // Fechar menu ao clicar em um link
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', () => {
                document.querySelector('.nav-links').classList.remove('active');
            });
        });
        
        // Formulário de agendamento
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Agendamento enviado! Te mandaremos uma confirmação pelo WhatsApp.');
            this.reset();
        });
        
        // Animação simples para os números de estatística
        const stats = document.querySelectorAll('.stat-number');
        stats.forEach(stat => {
            const target = parseInt(stat.textContent);
            let current = 0;
            const increment = target / 50;
            
            const updateStat = () => {
                if (current < target) {
                    current += increment;
                    stat.textContent = Math.ceil(current) + (stat.textContent.includes('.') ? '.' + stat.textContent.split('.')[1] : '');
                    setTimeout(updateStat, 30);
                } else {
                    stat.textContent = stat.textContent.includes('.') ? target.toFixed(1) : target;
                }
            };
            
            // Iniciar animação quando a seção estiver visível
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        updateStat();
                        observer.unobserve(entry.target);
                    }
                });
            });
            
            observer.observe(stat.closest('.sobre'));
        });
    </script>
</body>
</html>
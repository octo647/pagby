<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ tenant()->fantasy_name ?? 'Barbearia Cowboy ' }} | Estilo Western</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Alfa+Slab+One&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Reset e configurações gerais */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            color: #3A2C1E;
            line-height: 1.6;
            overflow-x: hidden;
            background-color: #F8F4E9;
            font-family: 'Open Sans', sans-serif;
        }
        
        h1, h2, h3, h4 {
            font-family: 'Alfa Slab One', cursive;
            font-weight: normal;
            letter-spacing: 1px;
        }
        
        /* Cabeçalho */
        header {
            background-color: rgba(58, 44, 30, 0.95);
            position: fixed;
            width: 100%;
            z-index: 1000;
            border-bottom: 3px solid #8B4513;
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
            font-weight: bold;
            color: #D4A76A;
            text-decoration: none;
            font-family: 'Alfa Slab One', cursive;
            text-shadow: 2px 2px 0 #8B4513;
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
            color: #D4A76A;
            font-weight: 600;
            transition: color 0.3s;
            font-size: 16px;
            position: relative;
        }
        
        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 0;
            background-color: #D4A76A;
            transition: width 0.3s;
        }
        
        .nav-links a:hover {
            color: #F8F4E9;
        }
        
        .nav-links a:hover::after {
            width: 100%;
        }
        
        .btn-agendar {
            background-color: #8B4513;
            color: #F8F4E9;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s;
            border: 2px solid #D4A76A;
            font-size: 15px;
        }
        
        .btn-agendar:hover {
            background-color: #D4A76A;
            color: #3A2C1E;
        }
        
        .menu-toggle {
            display: none;
            font-size: 24px;
            cursor: pointer;
            color: #D4A76A;
        }
        
        /* Seção Hero */
        .hero {
            height: 100vh;
            background: linear-gradient(rgba(58, 44, 30, 0.3), rgba(58, 44, 30, 0.3)), url('/images/Templates/Barbearias/Cowboy/photo.avif');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            color: #F8F4E9;
            text-align: center;
            position: relative;
        }
        
            
        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            z-index: 2;
        }
        
        .hero h1 {
            font-size: 4rem;
            margin-bottom: 20px;
            text-shadow: 3px 3px 0 #8B4513;
            line-height: 1.1;
        }
        
        .hero p {
            font-size: 1.3rem;
            margin-bottom: 40px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .btn-hero {
            background-color: #8B4513;
            color: #F8F4E9;
            padding: 15px 40px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            display: inline-block;
            transition: all 0.3s;
            border: 2px solid #D4A76A;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-hero:hover {
            background-color: #D4A76A;
            color: #3A2C1E;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        /* Seção Serviços */
        .servicos {
            padding: 100px 0;
            background-color: #F8F4E9;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .section-title h2 {
            font-size: 2.8rem;
            color: #3A2C1E;
            margin-bottom: 15px;
            text-shadow: 2px 2px 0 #D4A76A;
        }
        
        .section-title p {
            color: #5C4A36;
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
            background-color: #FFFFFF;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            border: 2px solid #8B4513;
            position: relative;
        }
        
        .servico-card:hover {
            transform: translateY(-10px);
        }
        
        .servico-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background-color: #8B4513;
        }
        
        .servico-img {
            height: 200px;
            background-size: cover;
            background-position: center;
        }
        
        .servico-content {
            padding: 25px;
        }
        
        .servico-content h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #3A2C1E;
        }
        
        .servico-content p {
            color: #5C4A36;
            margin-bottom: 20px;
        }
        
        .servico-preco {
            font-size: 1.5rem;
            font-weight: 700;
            color: #8B4513;
        }
        
        /* Seção Sobre */
        .sobre {
            padding: 100px 0;
            background-color: #3A2C1E;
            color: #F8F4E9;
        }
        
        .sobre-content {
            display: flex;
            align-items: center;
            gap: 50px;
        }
        
        .sobre-img {
            flex: 1;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border: 5px solid #8B4513;
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
            margin-bottom: 20px;
            color: #D4A76A;
            text-shadow: 2px 2px 0 #8B4513;
        }
        
        .sobre-text p {
            margin-bottom: 20px;
            font-size: 1.1rem;
        }
        
        /* Seção Galeria */
        .galeria {
            padding: 100px 0;
            background-color: #F8F4E9;
        }
        
        .galeria-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .galeria-item {
            height: 250px;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
            border: 3px solid #8B4513;
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
        
        /* Seção Contato */
        .contato {
            padding: 100px 0;
            background-color: #3A2C1E;
            color: #F8F4E9;
        }
        
        .contato-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 50px;
        }
        
        .contato-info h3 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #D4A76A;
            text-shadow: 2px 2px 0 #8B4513;
        }
        
        .contato-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .contato-icon {
            width: 50px;
            height: 50px;
            background-color: #5C4A36;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: #D4A76A;
            font-size: 1.2rem;
        }
        
        .contato-text h4 {
            font-size: 1.1rem;
            margin-bottom: 5px;
            color: #D4A76A;
        }
        
        .contato-text p {
            color: #F8F4E9;
        }
        
        .contato-form {
            background-color: #5C4A36;
            padding: 30px;
            border-radius: 10px;
            border: 2px solid #D4A76A;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #D4A76A;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #8B4513;
            border-radius: 5px;
            font-size: 1rem;
            background-color: #F8F4E9;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #D4A76A;
        }
        
        .btn-enviar {
            background-color: #8B4513;
        }
        
        .btn-enviar:hover {
            background-color: #D4A76A;
            color: #3A2C1E;
        }
        
        /* Rodapé */
        footer {
            background-color: #1A130B;
            color: #D4A76A;
            padding: 70px 0 20px;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 50px;
        }
        
        .footer-col h4 {
            font-size: 1.3rem;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
            color: #F8F4E9;
            font-family: 'Alfa Slab One', cursive;
        }
        
        .footer-col h4::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 2px;
            background-color: #8B4513;
        }
        
        .footer-col ul {
            list-style: none;
        }
        
        .footer-col ul li {
            margin-bottom: 10px;
        }
        
        .footer-col ul li a {
            color: #D4A76A;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-col ul li a:hover {
            color: #F8F4E9;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
        }
        
        .social-links a {
            width: 40px;
            height: 40px;
            background-color: #3A2C1E;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #D4A76A;
            transition: all 0.3s;
        }
        
        .social-links a:hover {
            background-color: #8B4513;
            transform: translateY(-3px);
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #3A2C1E;
            color: #8B4513;
            font-size: 0.9rem;
        }
        
        /* Elementos decorativos western */
        .western-divider {
            height: 30px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none"><path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25" fill="%238B4513"/><path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5" fill="%238B4513"/><path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" fill="%238B4513"/></svg>');
            background-size: cover;
            margin: 40px 0;
        }
        
        .cowboy-hat {
            display: inline-block;
            margin: 0 10px;
            font-size: 24px;
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
        }
        
        @media (max-width: 768px) {
            .btn-agendar {
                display: none;
            }
            .contato-form .btn-agendar {
                display: inline-block;
            }
            .menu-toggle {
                display: block;
            }
            
            .nav-links {
                position: fixed;
                top: 80px;
                left: -100%;
                width: 100%;
                height: calc(100vh - 80px);
                background-color: #3A2C1E;
                flex-direction: column;
                align-items: center;
                justify-content: flex-start;
                padding-top: 50px;
                transition: left 0.3s;
                border-top: 3px solid #8B4513;
            }
            
            .nav-links.active {
                left: 0;
            }
            
            .nav-links li {
                margin: 15px 0;
            }
            
            .hero h1 {
                font-size: 2.8rem;
            }
            
            .section-title h2 {
                font-size: 2.2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Cabeçalho -->
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="/login" class="logo">{{ mb_strtoupper(tenant()->fantasy_name ?? 'BARBEARIA COWBOY')}}</a>
                
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
                <h1>{{ mb_strtoupper(tenant()->fantasy_name ?? 'BARBEARIA COWBOY') }} <i class="fas fa-hat-cowboy cowboy-hat"></i></h1>
                <p>Onde a tradição western encontra o estilo masculino moderno. Corte de cabelo e barba com autenticidade e qualidade.</p>
                <a href="/login" class="btn-hero">Agendar Horário</a>
            </div>
        </div>
    </section>

    <div class="western-divider"></div>

    <!-- Seção Serviços -->
    <section class="servicos" id="servicos">
        <div class="container">
            <div class="section-title">
                <h2>NOSSOS SERVIÇOS</h2>
                <p>Oferecemos serviços de qualidade com a autenticidade do velho oeste.</p>
            </div>
            
            <div class="servicos-grid">
                <div class="servico-card">
                    <div class="servico-img" style="background-image: url('images/Templates/Barbearias/Cowboy/corte1.jpeg');"></div>
                    <div class="servico-content">
                        <h3>CORTE DE CAVALHEIRO</h3>
                        <p>Corte tradicional com técnicas clássicas e acabamento impecável para o homem moderno.</p>
                        <div class="servico-preco">R$ 35,00</div>
                    </div>
                </div>
                
                <div class="servico-card">
                    <div class="servico-img" style="background-image: url('images/Templates/Barbearias/Cowboy/barba1.jpeg');"></div>
                    <div class="servico-content">
                        <h3>BARBA RANCHER</h3>
                        <p>Aparar, modelar e definir a barba com produtos premium e toalhas quentes.</p>
                        <div class="servico-preco">R$ 25,00</div>
                    </div>
                </div>
                
                <div class="servico-card">
                    <div class="servico-img" style="background-image: url('images/Templates/Barbearias/Cowboy/combo.jpg');"></div>
                    <div class="servico-content">
                        <h3>COMBO XERIFE</h3>
                        <p>Pacote completo: corte, barba e cuidados com a pele. O xodó dos nossos clientes.</p>
                        <div class="servico-preco">R$ 50,00</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção Sobre -->
    <section class="sobre" id="sobre">
        <div class="container">
            <div class="sobre-content">
                <div class="sobre-img">
                    <img src="images/Templates/Barbearias/Cowboy/interior.jpeg" alt="Interior da barbearia">
                </div>
                <div class="sobre-text">
                    <h2>SOBRE A {{ mb_strtoupper(tenant()->fantasy_name ?? 'BARBEARIA COWBOY') }}</h2>
                    <p>Fundada em 2010, a {{ mb_strtoupper(tenant()->fantasy_name ?? 'Barbearia Cowboy') }} nasceu da paixão por tradições western e pelo cuidado masculino. Nosso espaço foi cuidadosamente decorado para transportar você para o velho oeste, com todo o conforto e qualidade dos tempos modernos.</p>
                    <p>Nossa equipe é formada por barbeiros especializados que dominam tanto as técnicas tradicionais quanto as tendências atuais. Cada profissional é treinado para oferecer não apenas um serviço, mas uma experiência completa.</p>
                    <p>Utilizamos apenas produtos de alta qualidade e técnicas comprovadas para garantir sua satisfação. Aqui, você não é apenas mais um cliente - é parte da nossa família cowboy.</p>
                </div>
            </div>
        </div>
    </section>

    <div class="western-divider"></div>

    <!-- Seção Galeria -->
    <section class="galeria" id="galeria">
        <div class="container">
            <div class="section-title">
                <h2>GALERIA</h2>
                <p>Conheça nosso ambiente e alguns dos trabalhos realizados.</p>
            </div>
            
            <div class="galeria-grid">
               <div class="galeria-grid">
                <div class="galeria-item">
                    <img src="{{ url('images/Templates/Barbearias/Cowboy/corte-moderno.jpeg') }}">
                </div>
                
                <div class="galeria-item">
                    <img src="{{ url('images/Templates/Barbearias/Cowboy/barba2.jpeg') }}" alt="Cuidados com a barba">
                </div>
                <div class="galeria-item">
                    <img src="{{ url('images/Templates/Barbearias/Cowboy/produtos.jpeg') }}" alt="Produtos de barbearia">
                </div>
                <div class="galeria-item">
                    <img src="{{ url('images/Templates/Barbearias/Cowboy/corte2.jpeg') }}" alt="Ambiente da barbearia">
                </div>
                <div class="galeria-item">
                    <img src="{{ url('images/Templates/Barbearias/Cowboy/corte3.jpeg') }}" alt="Ambiente da barbearia">
                </div>
                 <div class="galeria-item">
                    <img src="{{ url('images/Templates/Barbearias/Cowboy/corte4.jpeg') }}" alt="Ambiente da barbearia">
                </div>
                <div class="galeria-item">
                    <img src="{{ url('images/Templates/Barbearias/Cowboy/corte5.jpeg') }}" alt="Ambiente da barbearia">
                </div>
                <div class="galeria-item">
                    <img src="{{ url('images/Templates/Barbearias/Cowboy/corte6.jpeg') }}" alt="Ambiente da barbearia">
                </div>
            </div>
        </div>
    </section>

    <!-- Seção Contato -->
    <section class="contato" id="contato">
        <div class="container">
            <div class="section-title">
                <h2>CONTATO</h2>
                <p>Agende seu horário ou tire suas dúvidas conosco.</p>
            </div>
            
            <div class="contato-content">
                <div class="contato-info">
                    <h3>INFORMAÇÕES</h3>
                    
                    <div class="contato-item">
                        <div class="contato-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contato-text">
                            <h4>ENDEREÇO</h4>
                            <p>{!! tenant()->address ?? 'Rua do Oeste, 123 - Centro<br>Campo Grande, MS' !!}</p>
                        </div>
                    </div>
                    
                    <div class="contato-item">
                        <div class="contato-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contato-text">
                            <h4>TELEFONE</h4>
                            <p>{{ tenant()->phone ?? '(67) 9999-9999' }}</p>
                        </div>
                    </div>
                    
                    <div class="contato-item">
                        <div class="contato-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contato-text">
                            <h4>E-MAIL</h4>
                            <p>{{ tenant()->email ?? 'contato@barbeariacowboy.com' }}</p>
                        </div>
                    </div>
                    
                    <div class="contato-item">
                        <div class="contato-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contato-text">
                            <h4>HORÁRIO DE FUNCIONAMENTO</h4>
                            <p>Segunda a Sábado: 9h às 20h<br>Domingo: 10h às 16h</p>
                        </div>
                    </div>
                </div>
                
                <div class="contato-form" id="agendar">
                    
                    <h3 style="text-align: center;">Registre-se e use nosso serviço de agendamento online!</h3>
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
                    <h4>{{ mb_strtoupper(tenant()->fantasy_name ?? 'BARBEARIA COWBOY') }}</h4>
                    <p>Onde a tradição western encontra o estilo masculino moderno.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
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
                        <li><a href="#">CORTE DE CAVALHEIRO</a></li>
                        <li><a href="#">BARBA RANCHER</a></li>
                        <li><a href="#">COMBO XERIFE</a></li>
                        <li><a href="#">TRATAMENTOS CAPILARES</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>HORÁRIOS</h4>
                    <ul>
                        <li>SEGUNDA A SÁBADO: 9H ÀS 20H</li>
                        <li>DOMINGO: 10H ÀS 16H</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2023 {{ mb_strtoupper(tenant()->fantasy_name ?? 'BARBEARIA COWBOY') }}. TODOS OS DIREITOS RESERVADOS.</p>
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
        
        
    </script>
</body>
</html>
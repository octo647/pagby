<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ tenant()->fantasy_name ?? 'Barbearia Minimal | Estilo Essencial' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset e configurações gerais */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', 'Helvetica Neue', Arial, sans-serif;
        }
        
        body {
            color: #1a1a1a;
            line-height: 1.6;
            overflow-x: hidden;
            background-color: #ffffff;
        }
        
        /* Cabeçalho */
        header {
            background-color: #ffffff;
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
            padding: 25px 0;
        }
        
        .logo {
            font-size: 24px;
            font-weight: 700;
            color: #000000;
            text-decoration: none;
            letter-spacing: 1px;
        }
        
        .nav-links {
            display: flex;
            list-style: none;
        }
        
        .nav-links li {
            margin-left: 35px;
        }
        
        .nav-links a {
            text-decoration: none;
            color: #333333;
            font-weight: 400;
            font-size: 15px;
            letter-spacing: 0.5px;
            transition: color 0.3s;
            position: relative;
        }
        
        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 1px;
            bottom: -5px;
            left: 0;
            background-color: #000000;
            transition: width 0.3s;
        }
        
        .nav-links a:hover {
            color: #000000;
        }
        
        .nav-links a:hover::after {
            width: 100%;
        }
        
        .btn-agendar {
            background-color: #000000;
            color: #ffffff;
            padding: 12px 25px;
            border: 1px solid #000000;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            letter-spacing: 1px;
            transition: all 0.3s;
        }
        
        .btn-agendar:hover {
            background-color: #ffffff;
            color: #000000;
        }
        
        .menu-toggle {
            display: none;
            font-size: 22px;
            cursor: pointer;
            color: #000000;
        }
        
        /* Seção Hero */
        .hero {
            height: 100vh;
            background-color: #f8f8f8;
            display: flex;
            align-items: center;
            color: #000000;
            position: relative;
            overflow: hidden;
        }
        
        .hero-content {
            max-width: 700px;
            z-index: 2;
        }
        
        .hero h1 {
            font-size: 3.8rem;
            margin-bottom: 25px;
            font-weight: 300;
            line-height: 1.1;
            letter-spacing: -1px;
        }
        
        .hero p {
            font-size: 1.1rem;
            margin-bottom: 40px;
            color: #555555;
            max-width: 500px;
            line-height: 1.7;
        }
        
        .btn-hero {
            background-color: #000000;
            color: #ffffff;
            padding: 16px 40px;
            border: 1px solid #000000;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            letter-spacing: 1.5px;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .btn-hero:hover {
            background-color: transparent;
            color: #000000;
        }
        
        .hero-image {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 50%;
            background-image: url('images/Templates/Barbearias/Clean/hero.avif');
            background-size: cover;
            background-position: center;
            filter: grayscale(100%);
        }
        
        /* Seção Serviços */
        .servicos {
            padding: 120px 0;
            background-color: #ffffff;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 80px;
        }
        
        .section-title h2 {
            font-size: 2.2rem;
            color: #000000;
            margin-bottom: 15px;
            font-weight: 300;
            letter-spacing: -0.5px;
        }
        
        .section-title p {
            color: #666666;
            max-width: 500px;
            margin: 0 auto;
            font-size: 15px;
        }
        
        .servicos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }
        
        .servico-card {
            background-color: #ffffff;
            border: 1px solid #e5e5e5;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .servico-card:hover {
            border-color: #000000;
        }
        
        .servico-img {
            height: 220px;
            background-size: cover;
            background-position: center;
            filter: grayscale(100%);
            transition: filter 0.5s;
        }
        
        .servico-card:hover .servico-img {
            filter: grayscale(0%);
        }
        
        .servico-content {
            padding: 30px;
        }
        
        .servico-content h3 {
            font-size: 1.3rem;
            margin-bottom: 12px;
            color: #000000;
            font-weight: 500;
        }
        
        .servico-content p {
            color: #666666;
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .servico-preco {
            font-size: 1.2rem;
            font-weight: 600;
            color: #000000;
        }
        
        /* Seção Sobre */
        .sobre {
            padding: 120px 0;
            background-color: #f8f8f8;
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
            top: -20px;
            left: -20px;
            right: 20px;
            bottom: 20px;
            border: 1px solid #000000;
            z-index: 1;
        }
        
        .sobre-img img {
            width: 100%;
            height: auto;
            display: block;
            position: relative;
            z-index: 2;
            filter: grayscale(100%);
        }
        
        .sobre-text {
            flex: 1;
        }
        
        .sobre-text h2 {
            font-size: 2.2rem;
            margin-bottom: 25px;
            color: #000000;
            font-weight: 300;
            letter-spacing: -0.5px;
        }
        
        .sobre-text p {
            margin-bottom: 20px;
            color: #555555;
            line-height: 1.7;
        }
        
        /* Seção Galeria */
        .galeria {
            padding: 120px 0;
            background-color: #ffffff;
        }
        
        .galeria-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .galeria-item {
            height: 300px;
            position: relative;
            overflow: hidden;
        }
        
        .galeria-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
            filter: grayscale(100%);
        }
        
        .galeria-item:hover img {
            transform: scale(1.05);
            filter: grayscale(0%);
        }
        
        /* Seção Contato */
        .contato {
            padding: 120px 0;
            background-color: #f8f8f8;
        }
        
        .contato-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 60px;
        }
        
        .contato-info h3 {
            font-size: 1.8rem;
            margin-bottom: 30px;
            color: #000000;
            font-weight: 300;
        }
        
        .contato-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 25px;
        }
        
        .contato-icon {
            width: 24px;
            margin-right: 15px;
            color: #000000;
            font-size: 18px;
        }
        
        .contato-text h4 {
            font-size: 1rem;
            margin-bottom: 5px;
            color: #000000;
            font-weight: 500;
        }
        
        .contato-text p {
            color: #555555;
            font-size: 15px;
        }
        
        .contato-form {
            background-color: #ffffff;
            padding: 40px;
            border: 1px solid #e5e5e5;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
            color: #000000;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 0;
            border: none;
            border-bottom: 1px solid #e5e5e5;
            font-size: 15px;
            background: transparent;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #000000;
        }
        
        .btn-enviar {
            background-color: #000000;
            color: #ffffff;
            border: 1px solid #000000;
            padding: 14px 35px;
            font-weight: 500;
            font-size: 14px;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-enviar:hover {
            background-color: transparent;
            color: #000000;
        }
        
        /* Rodapé */
        footer {
            background-color: #000000;
            color: #ffffff;
            padding: 80px 0 30px;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 60px;
        }
        
        .footer-col h4 {
            font-size: 1.2rem;
            margin-bottom: 25px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        
        .footer-col ul {
            list-style: none;
        }
        
        .footer-col ul li {
            margin-bottom: 12px;
        }
        
        .footer-col ul li a {
            color: #cccccc;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }
        
        .footer-col ul li a:hover {
            color: #ffffff;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
        }
        
        .social-links a {
            width: 38px;
            height: 38px;
            border: 1px solid #555555;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #cccccc;
            transition: all 0.3s;
        }
        
        .social-links a:hover {
            border-color: #ffffff;
            color: #ffffff;
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid #333333;
            color: #999999;
            font-size: 13px;
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
            
            .hero-image {
                width: 40%;
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
            
            .hero-image {
                display: none;
            }
            
            .section-title h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Cabeçalho -->
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="#" class="logo">{{ tenant()->fantasy_name ?? 'BARBEARIA ESSENCIAL' }}</a>
                
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
                <h1>ESTILO ESSENCIAL PARA HOMENS MODERNOS</h1>
                <p>Uma experiência de barbearia focada no essencial, onde cada detalhe é pensado para oferecer o máximo em qualidade e sofisticação.</p>
                <a href="/login" class="btn-hero">AGENDAR HORÁRIO</a>
            </div>
        </div>
        <div class="hero-image"></div>
    </section>

    <!-- Seção Serviços -->
    <section class="servicos" id="servicos">
        <div class="container">
            <div class="section-title">
                <h2>NOSSOS SERVIÇOS</h2>
                <p>Serviços especializados com atenção aos detalhes e máxima qualidade.</p>
            </div>
            
            <div class="servicos-grid">
                <div class="servico-card">
                    <div class="servico-img" style="background-image: url({{ url('images/Templates/Barbearias/Clean/corte1.jpeg') }})"></div>
                    <div class="servico-content">
                        <h3>CORTE DE CABELO</h3>
                        <p>Corte preciso e personalizado, executado com técnicas modernas e atenção aos detalhes.</p>
                        <div class="servico-preco">R$ 40,00</div>
                    </div>
                </div>
                
                <div class="servico-card">
                    <div class="servico-img" style="background-image: url({{ url('images/Templates/Barbearias/Clean/barba1.jpeg') }})"></div>
                    <div class="servico-content">
                        <h3>BARBA</h3>
                        <p>Modelagem e acabamento perfeitos para uma barba impecável e bem definida.</p>
                        <div class="servico-preco">R$ 30,00</div>
                    </div>
                </div>
                
                <div class="servico-card">
                    <div class="servico-img" style="background-image: url({{ url('images/Templates/Barbearias/Clean/homem-com-barba.jpg') }})"></div>
                    <div class="servico-content">
                        <h3>TRATAMENTO COMPLETO</h3>
                        <p>Pacote completo que inclui corte, barba e cuidados especiais com a pele.</p>
                        <div class="servico-preco">R$ 60,00</div>
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
                    <img src="{{ url('images/Templates/Barbearias/Clean/photo.avif') }}" alt="Interior da barbearia">
                </div>
                <div class="sobre-text">
                    <h2>SOBRE NÓS</h2>
                    <p>A {{ tenant()->fantasy_name ?? 'Barbearia Essencial' }} nasceu da ideia de que menos é mais. Em um mundo cheio de excessos, buscamos oferecer uma experiência essencial, focada no que realmente importa: qualidade, precisão e sofisticação.</p>
                    <p>Nossa equipe é formada por profissionais altamente qualificados, apaixonados por seu ofício e constantemente atualizados com as últimas técnicas e tendências.</p>
                    <p>Em nosso espaço, cada detalhe foi cuidadosamente pensado para criar um ambiente sereno onde você pode relaxar e desfrutar de um momento dedicado ao seu cuidado pessoal.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção Galeria -->
    <section class="galeria" id="galeria">
        <div class="container">
            <div class="section-title">
                <h2>GALERIA</h2>
                <p>Conheça nosso ambiente e trabalhos realizados.</p>
            </div>
            
            <div class="galeria-grid">
                <div class="galeria-item">
                    <img src="{{ url('images/Templates/Barbearias/Clean/corte-moderno.jpeg') }}">
                </div>
                
                <div class="galeria-item">
                    <img src="{{ url('images/Templates/Barbearias/Clean/barba2.jpeg') }}" alt="Cuidados com a barba">
                </div>
                <div class="galeria-item">
                    <img src="{{ url('images/Templates/Barbearias/Clean/produtos.jpeg') }}" alt="Produtos de barbearia">
                </div>
                <div class="galeria-item">
                    <img src="{{ url('images/Templates/Barbearias/Clean/corte2.jpeg') }}" alt="Ambiente da barbearia">
                </div>
                <div class="galeria-item">
                    <img src="{{ url('images/Templates/Barbearias/Clean/corte3.jpeg') }}" alt="Ambiente da barbearia">
                </div>
                 <div class="galeria-item">
                    <img src="{{ url('images/Templates/Barbearias/Clean/corte4.jpeg') }}" alt="Ambiente da barbearia">
                </div>
                <div class="galeria-item">
                    <img src="{{ url('images/Templates/Barbearias/Clean/corte5.jpeg') }}" alt="Ambiente da barbearia">
                </div>
                <div class="galeria-item">
                    <img src="{{ url('images/Templates/Barbearias/Clean/corte6.jpeg') }}" alt="Ambiente da barbearia">
                </div>
            </div>
        </div>
    </section>

    <!-- Seção Contato -->
    <section class="contato" id="contato">
        <div class="container">
            <div class="section-title">
                <h2>CONTATO</h2>
                <p>Entre em contato para agendar seu horário ou tirar dúvidas.</p>
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
                            <p>{!! tenant()->address ?? 'Av. Paulista, 1000 - Bela Vista<br>São Paulo, SP' !!}</p>
                        </div>
                    </div>
                    
                    <div class="contato-item">
                        <div class="contato-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contato-text">
                            <h4>TELEFONE</h4>
                            <p>{{ tenant()->phone ?? '(11) 3456-7890' }}</p>
                        </div>
                    </div>
                    
                    <div class="contato-item">
                        <div class="contato-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contato-text">
                            <h4>E-MAIL</h4>
                            <p>{{ tenant()->email ?? 'contato@essencial.com' }}</p>
                        </div>
                    </div>
                    
                    <div class="contato-item">
                        <div class="contato-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contato-text">
                            <h4>HORÁRIOS</h4>
                            <p>Segunda a Sexta: 9h às 20h<br>Sábado: 9h às 18h<br>Domingo: Fechado</p>
                        </div>
                    </div>
                </div>
                
                <div class="contato-form" id="agendar">
                    <h3>AGENDAR HORÁRIO</h3>
                    <p>Registre-se e use nosso serviço de agendamento online!</p>
                    <br>
                    <a href="/login" class="btn-agendar">Agendar Horário</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Rodapé -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-col">
                    <h4>{{ tenant()->name ?? 'BARBEARIA ESSENCIAL' }}</h4>
                    <p style="color: #cccccc; font-size: 14px; line-height: 1.6;">Estilo essencial para homens modernos. Menos é mais.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h4>LINKS</h4>
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
                        <li><a href="#">CORTE DE CABELO</a></li>
                        <li><a href="#">BARBA</a></li>
                        <li><a href="#">TRATAMENTO COMPLETO</a></li>
                        <li><a href="#">CONSULTORIA DE ESTILO</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>HORÁRIOS</h4>
                    <ul>
                        <li>SEGUNDA A SEXTA: 9H ÀS 20H</li>
                        <li>SÁBADO: 9H ÀS 18H</li>
                        <li>DOMINGO: FECHADO</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2023 {{ tenant()->fantasy_name ?? 'BARBEARIA ESSENCIAL' }}. TODOS OS DIREITOS RESERVADOS.</p>
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
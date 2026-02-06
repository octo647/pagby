<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <link rel="icon" type="image/png" sizes="192x192" href="{{ tenant()->logo }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ tenant()->logo }}">
    <link rel="apple-touch-icon" href="{{ tenant()->logo }}">
    <link rel="manifest" href="/manifest.json">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ tenant()->fantasy_name ?? 'Barbearia' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset e configurações gerais */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }
        
        body {
            color: #333;
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        /* Cabeçalho */
        header {
            background-color: rgba(255, 255, 255, 0.95);
            position: fixed;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
            font-weight: 700;
            color: #1a1a1a;
            text-decoration: none;
        }
        
        .logo span {
            color: #d4af37;
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
        
        .btn-agendar {
            background-color: #d4af37;
            color: white;
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        
        .btn-agendar:hover {
            background-color: #b8941f;
        }
        
        .menu-toggle {
            display: none;
            font-size: 24px;
            cursor: pointer;
        }
        
        /* Seção Hero */
        .hero {
            height: 100vh;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url({{ url('images/Barbearia2/photo.avif') }});
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            color: white;
            text-align: center;
        }
        
        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            font-weight: 700;
        }
        
        .hero p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        
        .btn-hero {
            background-color: #d4af37;
            color: white;
            padding: 15px 35px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .btn-hero:hover {
            background-color: #b8941f;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        /* Seção Serviços */
        .servicos {
            padding: 100px 0;
            background-color: #f9f9f9;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .section-title h2 {
            font-size: 2.5rem;
            color: #1a1a1a;
            margin-bottom: 15px;
        }
        
        .section-title p {
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .servicos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .servico-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }
        
        .servico-card:hover {
            transform: translateY(-10px);
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
            color: #1a1a1a;
        }
        
        .servico-content p {
            color: #666;
            margin-bottom: 20px;
        }
        
        .servico-preco {
            font-size: 1.3rem;
            font-weight: 700;
            color: #d4af37;
        }
        
        /* Seção Sobre */
        .sobre {
            padding: 100px 0;
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
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
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
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #1a1a1a;
        }
        
        .sobre-text p {
            margin-bottom: 20px;
            color: #666;
        }
        
        /* Seção Galeria */
        .galeria {
            padding: 100px 0;
            background-color: #f9f9f9;
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
        }
        
        .contato-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 50px;
        }
        
        .contato-info h3 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: #1a1a1a;
        }
        
        .contato-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .contato-icon {
            width: 50px;
            height: 50px;
            background-color: #f5f5f5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: #d4af37;
            font-size: 1.2rem;
        }
        
        .contato-text h4 {
            font-size: 1.1rem;
            margin-bottom: 5px;
        }
        
        .contato-text p {
            color: #666;
        }
        
        .contato-form {
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #d4af37;
        }
        
        .btn-enviar {
            background-color: #d4af37;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn-enviar:hover {
            background-color: #b8941f;
        }
        
        /* Rodapé */
        footer {
            background-color: #1a1a1a;
            color: white;
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
        }
        
        .footer-col h4::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 2px;
            background-color: #d4af37;
        }
        
        .footer-col ul {
            list-style: none;
        }
        
        .footer-col ul li {
            margin-bottom: 10px;
        }
        
        .footer-col ul li a {
            color: #bbb;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-col ul li a:hover {
            color: #d4af37;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
        }
        
        .social-links a {
            width: 40px;
            height: 40px;
            background-color: #333;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: background-color 0.3s;
        }
        
        .social-links a:hover {
            background-color: #d4af37;
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #333;
            color: #bbb;
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
                margin: 15px 0;
            }
            
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Cabeçalho -->
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="#" class="logo"><span>{{ tenant()->fantasy_name ?? 'Estilo' }}</span></a>
                
                <ul class="nav-links">
                    <li><a href="#inicio">Início</a></li>
                    <li><a href="#servicos">Serviços</a></li>
                    <li><a href="#sobre">Sobre</a></li>
                    <li><a href="#galeria">Galeria</a></li>
                    <li><a href="#contato">Contato</a></li>
                </ul>
                
                <a href="/login" class="btn-agendar">Agendar Horário</a>
                
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
                <h1>Estilo & Tradição em Cada Corte</h1>
                <p>Descubra a experiência única de uma barbearia que combina técnicas clássicas com um toque moderno.</p>
                <a href="/login" class="btn-hero">Agendar Horário</a>
            </div>
        </div>
    </section>

    <!-- Seção Serviços -->
    <section class="servicos" id="servicos">
        <div class="container">
            <div class="section-title">
                <h2>Nossos Serviços</h2>
                <p>Oferecemos uma variedade de serviços para cuidar da sua aparência com qualidade e precisão.</p>
            </div>
            
            <div class="servicos-grid">
                <div class="servico-card">
                 
                    <div class="servico-img" style="background-image: url({{ url('images/Barbearia2/corte1.jpeg') }})"></div>
                
                    
                    <div class="servico-content">
                        <h3>Corte de Cabelo</h3>
                        <p>Corte moderno e personalizado de acordo com seu estilo e preferências.</p>
                        <div class="servico-preco">R$ 35,00</div>
                    </div>
                </div>
                
                <div class="servico-card">
                    <div class="servico-img" style="background-image: url({{ url('images/Barbearia2/Barba.jpeg') }})"></div>
                    <div class="servico-content">
                        <h3>Barba</h3>
                        <p>Aparar, modelar e definir a barba com técnicas tradicionais e produtos premium.</p>
                        <div class="servico-preco">R$ 25,00</div>
                    </div>
                </div>
                
                <div class="servico-card">
                    <div class="servico-img" style="background-image: url({{ url('images/Barbearia2/homem-com-barba.jpg') }})"></div>
                    <div class="servico-content">
                        <h3>Combo Completo</h3>
                        <p>Corte de cabelo + barba + cuidados com a pele. O pacote completo de cuidados masculinos.</p>
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
                    <img src="{{ url('images/Barbearia2/photo.avif') }}" alt="Interior da barbearia">
                </div>
                <div class="sobre-text">
                    <h2>Sobre Nossa Barbearia</h2>
                    <p>Há mais de 10 anos no mercado, a {{ tenant()->fantasy_name ?? 'Barbearia Estilo' }} combina tradição e modernidade para oferecer o melhor serviço de cuidados masculinos.</p>
                    <p>Nossa equipe é formada por profissionais qualificados e apaixonados pelo que fazem, sempre atualizados com as últimas tendências e técnicas.</p>
                    <p>Utilizamos apenas produtos de alta qualidade e oferecemos um ambiente acolhedor onde você pode relaxar e desfrutar de uma experiência única.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção Galeria -->
    <section class="galeria" id="galeria">
        <div class="container">
            <div class="section-title">
                <h2>Nossa Galeria</h2>
                <p>Conheça um pouco do nosso ambiente e trabalhos realizados.</p>
            </div>
            
            <div class="galeria-grid">
                <div class="galeria-item">
                    <img src="{{ url('images/Barbearia2/corte-moderno.jpeg') }}">
                </div>
                
                <div class="galeria-item">
                    <img src="{{ url('images/Barbearia2/barba2.jpeg') }}" alt="Cuidados com a barba">
                </div>
                <div class="galeria-item">
                    <img src="{{ url('images/Barbearia2/produtos.jpeg') }}" alt="Produtos de barbearia">
                </div>
                <div class="galeria-item">
                    <img src="{{ url('images/Barbearia2/corte2.jpeg') }}" alt="Ambiente da barbearia">
                </div>
                <div class="galeria-item">
                    <img src="{{ url('images/Barbearia2/corte3.jpeg') }}" alt="Ambiente da barbearia">
                </div>
                 <div class="galeria-item">
                    <img src="{{ url('images/Barbearia2/corte4.jpeg') }}" alt="Ambiente da barbearia">
                </div>
                <div class="galeria-item">
                    <img src="{{ url('images/Barbearia2/corte5.jpeg') }}" alt="Ambiente da barbearia">
                </div>
                <div class="galeria-item">
                    <img src="{{ url('images/Barbearia2/corte6.jpeg') }}" alt="Ambiente da barbearia">
                </div>
            </div>
        </div>
    </section>

    <!-- Seção Contato -->
    <section class="contato" id="contato">
        <div class="container">
            <div class="section-title">
                <h2>Entre em Contato</h2>
                <p>Agende seu horário ou tire suas dúvidas conosco.</p>
            </div>
            
            <div class="contato-content">
                <div class="contato-info">
                    <h3>Informações de Contato</h3>
                    
                    <div class="contato-item">
                        <div class="contato-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contato-text">
                            <h4>Endereço</h4>
                            <p>{{ tenant()->address ?? 'São Paulo, SP' }}</p>
                        </div>
                    </div>
                    
                    <div class="contato-item">
                        <div class="contato-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contato-text">
                            <h4>Telefone</h4>
                            <p>{{ tenant()->phone ?? '(11) 9999-9999' }}</p>
                        </div>
                    </div>
                    
                    <div class="contato-item">
                        <div class="contato-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contato-text">
                            <h4>E-mail</h4>
                            <p>{{ tenant()->email ?? 'contato@barbeariaestilo.com' }}</p>
                        </div>
                    </div>
                    
                    <div class="contato-item">
                        <div class="contato-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contato-text">
                            <h4>Horário de Funcionamento</h4>
                            <p>Segunda a Sábado: 9h às 20h<br>Domingo: 10h às 16h</p>
                        </div>
                    </div>
                </div>
                
                <div class="contato-form" id="agendar">
                    <h3>Agendar Horário</h3>
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
                    <h4>{{ tenant()->fantasy_name ?? 'Barbearia Estilo' }}</h4>
                    <p>Há mais de 10 anos oferecendo os melhores serviços de cuidados masculinos com qualidade e tradição.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h4>Links Rápidos</h4>
                    <ul>
                        <li><a href="#inicio">Início</a></li>
                        <li><a href="#servicos">Serviços</a></li>
                        <li><a href="#sobre">Sobre</a></li>
                        <li><a href="#galeria">Galeria</a></li>
                        <li><a href="#contato">Contato</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Serviços</h4>
                    <ul>
                        <li><a href="#">Corte de Cabelo</a></li>
                        <li><a href="#">Barba</a></li>
                        <li><a href="#">Combo Completo</a></li>
                        <li><a href="#">Tratamentos Capilares</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Horários</h4>
                    <ul>
                        <li>Segunda a Sábado: 9h às 20h</li>
                        <li>Domingo: 10h às 16h</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2023 {{ tenant()->fantasy_name ?? 'Barbearia Estilo' }}. Todos os direitos reservados.</p>
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
            alert('Agendamento enviado com sucesso! Entraremos em contato para confirmar.');
            this.reset();
        });
    </script>
</body>
</html>
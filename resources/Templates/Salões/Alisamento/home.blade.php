<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studio Glow | Especialistas em Alisamento e Cuidados</title>
    <style>
        :root {
            --primary: #d4a373; /* Tom bronze/dourado */
            --dark: #1a1a1a;
            --light: #fefae0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            color: var(--dark);
            background-color: #fff;
        }

        /* Header */
        header {
            background: var(--dark);
            color: white;
            padding: 1rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-weight: 500;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                        url('images/Templates/Salões/Alisamento/ambiente.png') no-repeat center center;
            background-size: cover;
            height: 80vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
        }

        /* Serviços */
        .services {
            padding: 5rem 10%;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .service-card {
            border: 1px solid #eee;
            padding: 2rem;
            text-align: center;
            transition: 0.3s;
            border-radius: 8px;
        }

        .service-card:hover {
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            transform: translateY(-5px);
        }

        .btn-agendar {
            background: var(--primary);
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 50px;
            display: inline-block;
            margin-top: 20px;
        }

        /* WhatsApp Flutuante */
        .whatsapp-float {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #25d366;
            color: white;
            padding: 15px 20px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

<header>
    <h2>STUDIO GLOW</h2>
    <nav>
        <a href="#servicos">Serviços</a>
        <a href="#sobre">Sobre</a>
        <a href="https://wa.me/seu-numero">Contato</a>
    </nav>
</header>

<section class="hero">
    <h1>Realçando sua beleza com perfeição</h1>
    <p>Especialistas em Alisamentos, Cronograma Capilar e Manicure Express.</p>
    <a href="https://wa.me/seu-numero" class="btn-agendar">Agende seu horário</a>
</section>

<section id="servicos" class="services">
    <div class="service-card">
        <h3>Alisamento Premium</h3>
        <p>Técnicas avançadas para um liso natural, brilhante e sem frizz. Protocolos que preservam a saúde do fio.</p>
    </div>
    
    <div class="service-card">
        <h3>Manicure Express</h3>
        <p>Perfeito para quem tem pressa mas não abre mão da elegância. Cutilagem e esmaltação impecáveis em tempo recorde.</p>
    </div>

    <div class="service-card">
        <h3>Cronograma Capilar</h3>
        <p>Tratamento personalizado: Hidratação, Nutrição e Reconstrução. Devolvemos a vida aos seus cabelos.</p>
    </div>
</section>

<a href="https://wa.me/seu-numero" class="whatsapp-float">Agendar pelo WhatsApp 📱</a>

</body>
</html>
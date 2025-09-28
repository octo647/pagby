<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vinda ao Salão {{tenant()->id}}</title>
    <link rel="stylesheet" href="/css/app.css">
    <style>
        body {
            font-family: 'Nunito', 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #fff0f6 0%, #ffe4ec 100%);
        }
        .hero-section {
            background: linear-gradient(120deg, #f9a8d4 0%, #fbcfe8 100%);
            box-shadow: 0 4px 24px 0 #f9a8d4a0;
            min-height: 220px;
            padding-top: 3rem;
            padding-bottom: 3rem;
            width: 100%;
            margin: 0;
        }
        .services-flex {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        @media (min-width: 900px) {
            .services-flex {
                flex-direction: row;
                gap: 2rem;
            }
        }
        .service-card {
            background: #fff;
            border: 1px solid #fbcfe8;
            transition: box-shadow 0.2s;
            flex: 1;
            min-width: 220px;
        }
        .service-card:hover {
            box-shadow: 0 2px 16px 0 #f9a8d4a0;
            border-color: #f472b6;
        }
        .gallery-section {
            background: linear-gradient(120deg, #fff0f6 0%, #ffe4ec 100%);
        }
        .gallery-flex {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        @media (min-width: 900px) {
            .gallery-flex {
                flex-direction: row;
                gap: 2rem;
            }
        }
        .gallery-img {
            width: 220px;
            height: 180px;
            object-fit: cover;
            border-radius: 1rem;
            box-shadow: 0 2px 12px 0 #f9a8d4a0;
            background: linear-gradient(120deg, #fbcfe8 0%, #fff0f6 100%);
        }
        .contact-section {
            background: linear-gradient(120deg, #fbcfe8 0%, #fff0f6 100%);
        }
        .btn-agendar {
            background: #f472b6;
            color: #fff;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 2rem;
            font-weight: 600;
            box-shadow: 0 2px 8px 0 #f9a8d4a0;
            transition: background 0.2s;
        }
        .btn-agendar:hover {
            background: #ec4899;
        }
        h1, h2, h3 {
            font-family: 'Nunito', 'Segoe UI', Arial, sans-serif;
        }
        footer {
            background: #f9a8d4;
            color: #fff;
            letter-spacing: 1px;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
</head>
<body >
<header>
    <div class="hero-section">
        <div class="container mx-auto text-center">
            <h1 class="text-5xl font-bold mb-4" style="color:#ec4899;">Bem-vinda ao Nosso Salão</h1>
            <p class="text-xl mb-8" style="color:#a21caf;">Beleza e cuidados especiais para realçar sua autoestima</p>
        </div>
    </div>
</header>
<div class="w-full flex justify-center" style="margin-top:4rem; margin-bottom:2.5rem;">
    <a href="login" class="btn-agendar" style="display:inline-block;">Agendar Horário</a>
</div>
</header>
<main >

    <div class="services-section py-16">
        <div class="container mx-auto">
            <h2 class="text-3xl font-bold text-center mb-12" style="color:#ec4899;">Nossos Serviços</h2>
          <div class="services-flex">
                <div class="service-card text-center p-6 rounded-lg" style="flex:1; min-width:220px;">
                    <h3 class="text-xl font-semibold mb-3" style="color:#a21caf;">Corte</h3>
                    <p style="color:#d946ef;">Cortes modernos e personalizados</p>
                </div>
                <div class="service-card text-center p-6 rounded-lg" style="flex:1; min-width:220px;">
                    <h3 class="text-xl font-semibold mb-3" style="color:#a21caf;">Escova</h3>
                    <p style="color:#d946ef;">Escova e modelagem profissional</p>
                </div>
                <div class="service-card text-center p-6 rounded-lg" style="flex:1; min-width:220px;">
                    <h3 class="text-xl font-semibold mb-3" style="color:#a21caf;">Coloração</h3>
                    <p style="color:#d946ef;">Cores vibrantes e naturais</p>
                </div>
                <div class="service-card text-center p-6 rounded-lg" style="flex:1; min-width:220px;">
                    <h3 class="text-xl font-semibold mb-3" style="color:#a21caf;">Manicure</h3>
                    <p style="color:#d946ef;">Cuidados completos para suas unhas</p>
                </div>
            </div>
        </div>
    </div>

    <div class="gallery-section py-16">
        <div class="container mx-auto">
            <h2 class="text-3xl font-bold text-center mb-12" style="color:#ec4899;">Nossa Galeria</h2>
            <div class="gallery-flex">
                <!-- Aqui serão carregadas as imagens dos trabalhos -->
                <div class="bg-white p-4 rounded-lg shadow-lg flex-1" style="min-width:220px;">
                    <img src="/images/{{tenant()->id}}/escova.jpeg" class="gallery-img" alt="Escova">
                    <p class="text-center mt-2" style="color:#a21caf;">Corte e Escova</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-lg flex-1" style="min-width:220px;">
                    <img src="/images/{{tenant()->id}}/coloracao.jpeg" class="gallery-img" alt="Coloração">
                    <p class="text-center mt-2" style="color:#a21caf;">Coloração</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-lg flex-1" style="min-width:220px;">
                    <img src="/images/{{tenant()->id}}/manicure.jpeg" class="gallery-img" alt="Manicure">
                    <p class="text-center mt-2" style="color:#a21caf;">Manicure</p>
                </div>
            </div>
        </div>
    </div>

    <div class="contact-section py-16">
        <div class="container mx-auto text-center">
            <h2 class="text-3xl font-bold mb-8" style="color:#ec4899;">Entre em Contato</h2>
            <p class="text-lg mb-4" style="color:#a21caf;">Agende seu horário e venha cuidar da sua beleza</p>
            <div class="flex justify-center space-x-8">
                <div style="color:#d946ef;">
                    <strong>Telefone:</strong> (11) 99999-9991
                </div>
                <div style="color:#d946ef;">
                    <strong>Endereço:</strong> Rua Example, 123
                </div>
            </div>
        </div>
    </div>
    
</main>

    <footer class="w-full py-4 text-center text-sm shadow mt-8">
        &copy; {{ date('Y') }} Salão de Beleza {{tenant()->id}}. Todos os direitos reservados.
    </footer>
</body>
</html>
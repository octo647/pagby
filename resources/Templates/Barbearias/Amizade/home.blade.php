<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ tenant()->fantasy_name ?? 'Barbearia Amizade' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap');
        
        body {
            font-family: 'Montserrat', sans-serif;
            scroll-behavior: smooth;
        }
        
        .hero-overlay {
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.8));
        }
        
        .service-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }
        
        .testimonial-card {
            transition: transform 0.3s ease;
        }
        
        .testimonial-card:hover {
            transform: scale(1.03);
        }
        
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }
        
        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .sticky-nav {
            position: sticky;
            top: 0;
            z-index: 100;
            transition: all 0.3s ease;
        }
        
        .sticky-nav.scrolled {
            background-color: rgba(17, 24, 39, 0.95);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .promo-badge {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .gallery-item {
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        
        .gallery-item:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen flex flex-col">
    <!-- Navegação fixa -->
    <nav class="sticky-nav w-full py-4 bg-gray-900 shadow z-50">
        <div class="container mx-auto flex items-center justify-between px-4">
            <div class="flex items-center gap-3">
                <img src="{{ tenant()->logo ?? asset('images/default-user.png') }}" alt="Logo do Salão {{ tenant()->fantasy_name ?? 'Barbearia Salon Dumont' }}" class="w-12 h-12 rounded-full object-cover border-2 border-yellow-600 shadow">
                <span class="text-2xl font-extrabold text-yellow-600 tracking-wide">{{ tenant()->fantasy_name ?? 'Barbearia Salon Dumont' }}</span>
            </div>
            <div class="hidden md:flex items-center gap-6">
                <a href="#servicos" class="text-gray-300 hover:text-yellow-500 transition">Serviços</a>
                <a href="#sobre" class="text-gray-300 hover:text-yellow-500 transition">Sobre</a>
                <a href="#depoimentos" class="text-gray-300 hover:text-yellow-500 transition">Depoimentos</a>
                <a href="#galeria" class="text-gray-300 hover:text-yellow-500 transition">Galeria</a>
                <a href="#contato" class="text-gray-300 hover:text-yellow-500 transition">Contato</a>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('login') }}" class="bg-yellow-600 text-gray-900 px-4 py-2 rounded-full text-sm font-bold shadow hover:bg-yellow-700 transition">Entrar</a>
                <a href="{{ route('register') }}" class="bg-yellow-600 text-gray-900 px-4 py-2 rounded-full text-sm font-bold shadow hover:bg-yellow-700 transition">Registrar</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative w-full h-[80vh] md:h-[100vh] flex items-center justify-center mb-8 overflow-hidden">
        <div class="absolute inset-0 w-full h-full bg-cover bg-center" style="background-image: url('/images/Templates/Barbearias/Amizade/ambiente.jpeg');"></div>
        <div class="hero-overlay absolute inset-0 w-full h-full"></div>
        <div class="relative z-10 text-center px-4 max-w-4xl">
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-yellow-400 drop-shadow mb-6">DESDE 2020 CUIDANDO DO SEU VISUAL</h1>
            <p class="text-lg sm:text-xl text-gray-200 max-w-2xl mx-auto mb-8">SEJA QUAL FOR SEU ESTILO, VAMOS MANTÊ-LO IMPECÁVEL! VENHA PARA NOSSA BARBEARIA, AQUI CUIDAMOS DE VOCÊ.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#agendamento" class="bg-yellow-600 text-gray-900 px-6 py-3 rounded-full font-bold shadow hover:bg-yellow-700 transition transform hover:scale-105">Agendar Horário</a>
                <a href="#servicos" class="bg-transparent border-2 border-yellow-600 text-yellow-400 px-6 py-3 rounded-full font-bold shadow hover:bg-yellow-600 hover:text-gray-900 transition">Ver Serviços</a>
            </div>
        </div>
    </section>

    <main class="flex-1 flex flex-col items-center text-center px-4">
        <!-- Seção de Serviços -->
        <section id="servicos" class="mb-16 w-full max-w-6xl fade-in">
            <h2 class="text-3xl font-bold text-yellow-400 mb-4">NOSSOS SERVIÇOS</h2>
            <p class="text-gray-300 max-w-2xl mx-auto mb-10">Com profissionais qualificados e atendimento personalizado, nosso clube é ideal para quem valoriza praticidade e qualidade nos cuidados pessoais.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="service-card bg-gray-800 rounded-lg p-6 shadow-lg border border-gray-700">
                    <div class="flex flex-col items-center">
                        <div class="w-20 h-20 mb-4 rounded-full overflow-hidden border-2 border-yellow-600 shadow">
                            <img src="/images/Templates/Barbearias/Amizade/corte.jpg" class="w-full h-full object-cover" alt="Corte de Cabelo">
                        </div>
                        <h3 class="text-xl font-bold text-yellow-400 mb-2">Corte de Cabelo</h3>
                        <p class="text-gray-300 mb-4">Cortes modernos e clássicos, sempre seguindo as últimas tendências.</p>
                        <span class="text-yellow-500 font-bold">A partir de R$ 30</span>
                    </div>
                </div>
                
                <div class="service-card bg-gray-800 rounded-lg p-6 shadow-lg border border-gray-700">
                    <div class="flex flex-col items-center">
                        <div class="w-20 h-20 mb-4 rounded-full overflow-hidden border-2 border-yellow-600 shadow">
                            <img src="/images/Templates/Barbearias/Amizade/barba.jpg" class="w-full h-full object-cover" alt="Barba">
                        </div>
                        <h3 class="text-xl font-bold text-yellow-400 mb-2">Barba</h3>
                        <p class="text-gray-300 mb-4">Aparar, modelar e definir sua barba com técnicas profissionais.</p>
                        <span class="text-yellow-500 font-bold">A partir de R$ 25</span>
                    </div>
                </div>
                
                <div class="service-card bg-gray-800 rounded-lg p-6 shadow-lg border border-gray-700">
                    <div class="flex flex-col items-center">
                        <div class="w-20 h-20 mb-4 rounded-full overflow-hidden border-2 border-yellow-600 shadow">
                            <img src="/images/Templates/Barbearias/Amizade/manicure.jpg" class="w-full h-full object-cover" alt="Manicure">
                        </div>
                        <h3 class="text-xl font-bold text-yellow-400 mb-2">Manicure</h3>
                        <p class="text-gray-300 mb-4">Cuidados completos para suas mãos e unhas com produtos de qualidade.</p>
                        <span class="text-yellow-500 font-bold">A partir de R$ 20</span>
                    </div>
                </div>
                
                <div class="service-card bg-gray-800 rounded-lg p-6 shadow-lg border border-gray-700">
                    <div class="flex flex-col items-center">
                        <div class="w-20 h-20 mb-4 rounded-full overflow-hidden border-2 border-yellow-600 shadow">
                            <img src="/images/Templates/Barbearias/Amizade/coloracao.jpg" class="w-full h-full object-cover" alt="Coloração">
                        </div>
                        <h3 class="text-xl font-bold text-yellow-400 mb-2">Coloração</h3>
                        <p class="text-gray-300 mb-4">Mude seu visual com nossas colorações profissionais e produtos de alta qualidade.</p>
                        <span class="text-yellow-500 font-bold">A partir de R$ 50</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Seção Sobre -->
        <section id="sobre" class="mb-16 w-full max-w-4xl fade-in">
            <h2 class="text-3xl font-bold text-yellow-400 mb-4">SOBRE NÓS</h2>
            <p class="text-gray-300 max-w-2xl mx-auto mb-6">Desde 2020 a {{ tenant()->fantasy_name ?? 'Barbearia Tradição' }} traz para nossos clientes as mais novas tendências e técnicas de cortes e barbas. Oferecemos serviços de alta qualidade em um ambiente familiar.</p>
            <div class="bg-gray-800 rounded-lg p-6 shadow-lg border border-gray-700">
                <h3 class="text-xl font-bold text-yellow-400 mb-4">VOCÊ NÃO É APENAS UM CLIENTE, É UM MEMBRO DA FAMÍLIA</h3>
                <p class="text-gray-300 mb-4">Nossa missão é proporcionar uma experiência única, onde cada cliente se sinta especial e saia satisfeito com nossos serviços.</p>
                <div class="flex flex-wrap justify-center gap-6 mt-6">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 rounded-full bg-yellow-600 flex items-center justify-center mb-2">
                            <i class="fas fa-award text-xl text-gray-900"></i>
                        </div>
                        <span class="text-yellow-400 font-medium">Qualidade</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 rounded-full bg-yellow-600 flex items-center justify-center mb-2">
                            <i class="fas fa-users text-xl text-gray-900"></i>
                        </div>
                        <span class="text-yellow-400 font-medium">Experiência</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 rounded-full bg-yellow-600 flex items-center justify-center mb-2">
                            <i class="fas fa-heart text-xl text-gray-900"></i>
                        </div>
                        <span class="text-yellow-400 font-medium">Dedicação</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Seção de Depoimentos -->
        <section id="depoimentos" class="mb-16 w-full max-w-5xl fade-in">
            <h2 class="text-3xl font-bold text-yellow-400 mb-4">O QUE DIZEM NOSSOS CLIENTES</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="testimonial-card bg-gray-800 rounded-lg p-6 shadow-lg border border-gray-700">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-yellow-600 flex items-center justify-center mr-3">
                            <span class="text-gray-900 font-bold">JS</span>
                        </div>
                        <div>
                            <h4 class="text-yellow-500 font-bold">João Silva</h4>
                            <div class="flex text-yellow-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-200 italic">"Atendimento top, ambiente limpo e profissionais excelentes! Sempre saio satisfeito com o resultado."</p>
                </div>
                
                <div class="testimonial-card bg-gray-800 rounded-lg p-6 shadow-lg border border-gray-700">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-yellow-600 flex items-center justify-center mr-3">
                            <span class="text-gray-900 font-bold">CM</span>
                        </div>
                        <div>
                            <h4 class="text-yellow-500 font-bold">Carlos Mendes</h4>
                            <div class="flex text-yellow-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-200 italic">"Meu corte ficou perfeito, virei cliente fiel! O ambiente é muito aconchegante e os profissionais são muito atenciosos."</p>
                </div>
                
                <div class="testimonial-card bg-gray-800 rounded-lg p-6 shadow-lg border border-gray-700">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-yellow-600 flex items-center justify-center mr-3">
                            <span class="text-gray-900 font-bold">RM</span>
                        </div>
                        <div>
                            <h4 class="text-yellow-500 font-bold">Roberto Martins</h4>
                            <div class="flex text-yellow-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-200 italic">"Excelente barbearia! Serviço de qualidade, preço justo e sempre com um atendimento personalizado. Recomendo!"</p>
                </div>
            </div>
        </section>

        <!-- Seção Galeria -->
        <section id="galeria" class="mb-16 w-full max-w-6xl fade-in">
            <h2 class="text-3xl font-bold text-yellow-400 mb-4">NOSSO TRABALHO</h2>
            <p class="text-gray-300 max-w-2xl mx-auto mb-8">Confira alguns dos nossos trabalhos realizados para clientes satisfeitos.</p>
            
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div class="gallery-item rounded-lg overflow-hidden shadow-lg">
                    <img src="/images/Templates/Barbearias/Amizade/corte.jpg" alt="Corte de Cabelo" class="w-full h-48 object-cover">
                </div>
                <div class="gallery-item rounded-lg overflow-hidden shadow-lg">
                    <img src="/images/Templates/Barbearias/Amizade/barba.jpg" alt="Barba" class="w-full h-48 object-cover">
                </div>
                <div class="gallery-item rounded-lg overflow-hidden shadow-lg">
                    <img src="/images/Templates/Barbearias/Amizade/manicure.jpg" alt="Manicure" class="w-full h-48 object-cover">
                </div>
                <div class="gallery-item rounded-lg overflow-hidden shadow-lg">
                    <img src="/images/Templates/Barbearias/Amizade/coloracao.jpg" alt="Coloração" class="w-full h-48 object-cover">
                </div>
                <div class="gallery-item rounded-lg overflow-hidden shadow-lg">
                    <img src="/images/Templates/Barbearias/Amizade/ambiente.jpeg" alt="Ambiente" class="w-full h-48 object-cover">
                </div>
                <div class="gallery-item rounded-lg overflow-hidden shadow-lg">
                    <img src="/images/Templates/Barbearias/Amizade/ambiente2.jpeg" alt="Ambiente 2" class="w-full h-48 object-cover bg-gray-700 flex items-center justify-center">
                    <div class="w-full h-full flex items-center justify-center bg-gray-800">
                        <span class="text-gray-400">Mais fotos em breve</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Seção Promoção -->
        <section class="mb-16 w-full max-w-4xl fade-in">
            <div class="relative bg-gradient-to-r from-yellow-600 to-yellow-800 rounded-xl p-8 shadow-2xl overflow-hidden">
                <div class="absolute top-0 right-0 bg-red-600 text-white px-4 py-2 font-bold rounded-bl-lg promo-badge">
                    PROMOÇÃO
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">PROMOÇÃO DO MÊS</h2>
                <p class="text-gray-900 text-xl font-bold mb-6">Corte + Barba por apenas R$ 49,90!</p>
                <p class="text-gray-800 mb-6">Aproveite esta oferta especial por tempo limitado para novos clientes. Agende seu horário agora mesmo!</p>
                <a href="/login" class="bg-gray-900 text-yellow-400 px-6 py-3 rounded-full font-bold shadow hover:bg-gray-800 transition inline-block">Agendar Agora</a>
            </div>
        </section>

        <!-- Seção Agendamento -->
        <section id="agendamento" class="mb-16 w-full max-w-2xl fade-in">
            <h2 class="text-3xl font-bold text-yellow-400 mb-4">AGENDE SEU HORÁRIO</h2>
            <p class="text-gray-300 max-w-2xl mx-auto mb-8">Clique no botão abaixo e faça login para usar nosso sistema de agendamento online.</p>
            
            <div class="bg-gray-800 rounded-lg p-6 shadow-lg border border-gray-700 ">
            
               
                
                <a href='/login' class="w-full bg-yellow-600 text-gray-900 px-4 py-3 rounded font-bold shadow hover:bg-yellow-700 transition">Solicitar Agendamento</a>
            </div>
        </section>

        <!-- Seção Contato -->
        <section id="contato" class="mb-16 w-full max-w-4xl fade-in">
            <h2 class="text-3xl font-bold text-yellow-400 mb-4">CONTATO E LOCALIZAÇÃO</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-gray-800 rounded-lg p-6 shadow-lg border border-gray-700 text-left">
                    <h3 class="text-xl font-bold text-yellow-400 mb-4">Horário de Funcionamento</h3>
                    <ul class="text-gray-300 space-y-2">
                        <li class="flex justify-between">
                            <span>Segunda a Sexta:</span>
                            <span class="font-bold">9h - 20h</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Sábado:</span>
                            <span class="font-bold">9h - 16h</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Domingo:</span>
                            <span class="font-bold">Fechado</span>
                        </li>
                    </ul>
                    
                    <h3 class="text-xl font-bold text-yellow-400 mt-6 mb-4">Endereço</h3>
                    <p class="text-gray-300 mb-2">{{ tenant()->address ?? 'Rua Exemplo, 123 - Centro' }}</p>
                    
                    <h3 class="text-xl font-bold text-yellow-400 mt-6 mb-4">Telefone</h3>
                    <p class="text-gray-300">{{ tenant()->phone ?? '(11) 99999-9999' }}</p>
                </div>
                
                <div class="bg-gray-800 rounded-lg p-6 shadow-lg border border-gray-700">
                    <h3 class="text-xl font-bold text-yellow-400 mb-4">Siga-nos nas Redes Sociais</h3>
                    <p class="text-gray-300 mb-6">Acompanhe nosso trabalho e fique por dentro das novidades e promoções.</p>
                    
                    <div class="flex gap-4 justify-center">
                        <a href="{{ tenant()->instagram ?? '#' }}" target="_blank" class="bg-yellow-600 text-gray-900 w-12 h-12 rounded-full flex items-center justify-center hover:bg-yellow-700 transition transform hover:scale-110">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="{{ tenant()->facebook ?? '#' }}" target="_blank" class="bg-yellow-600 text-gray-900 w-12 h-12 rounded-full flex items-center justify-center hover:bg-yellow-700 transition transform hover:scale-110">
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                        <a href="https://wa.me/{{ tenant()->whatsapp ?? '5511999999999' }}" target="_blank" class="bg-yellow-600 text-gray-900 w-12 h-12 rounded-full flex items-center justify-center hover:bg-yellow-700 transition transform hover:scale-110">
                            <i class="fab fa-whatsapp text-xl"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="w-full py-8 bg-gray-900 mt-auto border-t border-gray-800">
        <div class="container mx-auto text-center">
            <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                <div class="flex items-center gap-3 mb-4 md:mb-0">
                    <img src="{{ tenant()->logo ?? asset('images/default-user.png') }}" alt="Logo do Salão {{ tenant()->fantasy_name ?? 'Barbearia Salon Dumont' }}" class="w-10 h-10 rounded-full object-cover border-2 border-yellow-600 shadow">
                    <span class="text-xl font-extrabold text-yellow-600 tracking-wide">{{ tenant()->fantasy_name ?? 'Barbearia Salon Dumont' }}</span>
                </div>
                
                <div class="flex gap-6">
                    <a href="#servicos" class="text-gray-400 hover:text-yellow-500 transition">Serviços</a>
                    <a href="#sobre" class="text-gray-400 hover:text-yellow-500 transition">Sobre</a>
                    <a href="#contato" class="text-gray-400 hover:text-yellow-500 transition">Contato</a>
                </div>
            </div>
            
            <div class="text-gray-400 text-sm">
                &copy; {{ date('Y') }} {{ tenant()->fantasy_name ?? 'Barbearia Salon Dumont' }}. Todos os direitos reservados.
            </div>
        </div>
    </footer>

    <script>
        // Animação de scroll para elementos com a classe fade-in
        document.addEventListener('DOMContentLoaded', function() {
            const fadeElements = document.querySelectorAll('.fade-in');
            
            const fadeInObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, { threshold: 0.1 });
            
            fadeElements.forEach(element => {
                fadeInObserver.observe(element);
            });
            
            // Navegação fixa com efeito de scroll
            const nav = document.querySelector('.sticky-nav');
            
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    nav.classList.add('scrolled');
                } else {
                    nav.classList.remove('scrolled');
                }
            });
            
            // Smooth scroll para links internos
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;
                    
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 80,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>

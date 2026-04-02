<!DOCTYPE html>
<html lang="pt-BR" prefix="og: https://ogp.me/ns#">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Open Graph / Facebook - DEVE VIR PRIMEIRO -->
    <meta property="fb:app_id" content="925036043626183">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="PagBy">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="PagBy - Gerencie seu Salão de Beleza ou Barbearia">
    <meta property="og:description" content="Organize sua agenda, confirme clientes automaticamente e evite faltas. Teste grátis agora!">
    <meta property="og:image" content="{{ secure_url('images/consultando_agenda5.png') }}">
    <meta property="og:image:secure_url" content="{{ secure_url('images/consultando_agenda5.png') }}">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="PagBy - Sistema de Gestão para Salões">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="PagBy - Gerencie seu Salão de Beleza ou Barbearia">
    <meta name="twitter:description" content="Organize sua agenda, confirme clientes automaticamente e evite faltas. Teste grátis agora!">
    <meta name="twitter:image" content="{{ secure_url('images/consultando_agenda5.png') }}">
    
    <title>PagBy - Plataforma para Salões de Beleza e Barbearias</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#e11d48">
    <link rel="icon" type="image/png" href="/images/logo2.png">
    <link rel="apple-touch-icon" href="/images/logo2.png">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s, transform 0.6s;
        }
        
        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        #mobile-menu {
            transition: opacity 0.3s;
        }
        .opacity-0 {
            opacity: 0;
            pointer-events: none;
        }
        .opacity-100 {
            opacity: 1;
            pointer-events: auto;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 to-gray-800 text-white">
    {{ $slot }}
    
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Fade-in
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

    // Service Worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js')
            .then(function(registration) {
                console.log('Service Worker registrado:', registration);
            })
            .catch(function(error) {
                console.log('Erro ao registrar Service Worker:', error);
            });
    }

    // Mobile menu toggle
    const menuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const closeBtn = document.getElementById('close-mobile-menu');

    if (menuBtn && mobileMenu && closeBtn) {
        menuBtn.addEventListener('click', function() {
            mobileMenu.classList.add('opacity-100', 'pointer-events-auto');
            mobileMenu.classList.remove('opacity-0', 'pointer-events-none');
        });

        closeBtn.addEventListener('click', function() {
            mobileMenu.classList.remove('opacity-100', 'pointer-events-auto');
            mobileMenu.classList.add('opacity-0', 'pointer-events-none');
        });

        // Fecha o menu ao clicar em qualquer link dentro do menu mobile
        mobileMenu.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', function() {
                mobileMenu.classList.remove('opacity-100', 'pointer-events-auto');
                mobileMenu.classList.add('opacity-0', 'pointer-events-none');
            });
        });
    }
});
</script>

</body>
</html>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PagBy - Plataforma para Salões de Beleza e Barbearias</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#e11d48">
    <link rel="icon" type="image/png" sizes="192x192" href="/images/pagby-192.png">
    <link rel="apple-touch-icon" href="/images/pagby-192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/images/pagby-512.png">
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
     <!-- Meta Pixel Code -->
        <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '1875461569828036');
        fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=1875461569828036&ev=PageView&noscript=1"
        /></noscript>
        <!-- End Meta Pixel Code -->
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
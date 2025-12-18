<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo ao {{tenant()->fantasy_name}}</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body class="bg-gradient-to-br from-gray-900 to-gray-700 min-h-screen flex flex-col">
    <header class="w-full py-6 bg-gray-900 shadow">
        <div class="container mx-auto flex items-center justify-between px-4">
            <div class="flex items-center gap-3">
                <img src="{{ !empty($tenant->logo) ? url($tenant->logo) : asset('images/default-user.png') }}" alt="Logo do Salão {{tenant()->fantasy_name}}" class="w-12 h-12 rounded-full object-cover border-2 border-yellow-600 shadow">
                <span class="text-2xl font-extrabold text-yellow-600 tracking-wide">{{tenant()->fantasy_name}}</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('login') }}" class="bg-yellow-600 text-gray-900 px-4 py-2 rounded-full text-sm font-bold shadow hover:bg-yellow-700 transition">Entrar</a>
                <a href="{{ route('register') }}" class="bg-yellow-600 text-gray-900 px-4 py-2 rounded-full text-sm font-bold shadow hover:bg-yellow-700 transition">Registrar</a>
            </div>
        </div>
    </header>

    <!-- Cover de fundo com overlay e texto -->
    <section class="relative w-screen left-1/2 right-1/2 -ml-[50vw] -mr-[50vw] h-[60vh] md:h-[80vh] flex items-center justify-center mb-8 overflow-hidden">
        <div class="absolute inset-0 w-full h-full bg-cover bg-center" style="background-image: url('/images/Barbearia1/ambiente.jpeg');"></div>
        <div class="absolute inset-0 w-full h-full bg-black opacity-60"></div>
        <div class="relative z-10 flex flex-col items-center justify-center w-full h-full">
            <h1 class="text-3xl sm:text-5xl font-extrabold text-yellow-400 drop-shadow mb-2">Ambiente moderno e acolhedor</h1>
            <p class="text-lg sm:text-2xl text-gray-100 max-w-2xl mx-auto">Venha conhecer nosso espaço pensado para o seu conforto e estilo!</p>
        </div>
    </section>

    <main class="flex-1 flex flex-col items-center justify-center text-center px-4">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-yellow-500 mb-4 drop-shadow">Estilo e atitude</h1>
        <p class="text-lg sm:text-2xl text-gray-200 mb-4">Agende seu horário!</p>
        <p class="text-gray-400 max-w-xl mx-auto mb-8">
            No {{ tenant()->fantasy_name }}, você encontra profissionais experientes, ambiente moderno e atendimento personalizado. Cuidamos do seu visual com excelência e atenção aos detalhes.
        </p>
        <a href="{{ route('agendamento') }}" class="bg-yellow-600 text-gray-900 px-8 py-3 rounded-full text-lg font-bold shadow hover:bg-yellow-700 transition mb-10">Agendar agora</a>
        <div class="flex gap-8 mt-6 justify-center flex-wrap">
            <div class="flex flex-col items-center">
                <img src="/images/Barbearia1/corte.jpg" class="w-14 h-14 mb-2 rounded shadow-lg border border-gray-800" alt="Corte">
                <span class="text-yellow-400 font-medium">Corte</span>
            </div>
            <div class="flex flex-col items-center">
                <img src="/images/Barbearia1/barba.jpg" class="w-14 h-14 mb-2 rounded shadow-lg border border-gray-800" alt="Barba">
                <span class="text-yellow-400 font-medium">Barba</span>
            </div>
            <div class="flex flex-col items-center">
                <img src="/images/Barbearia1/manicure.jpg" class="w-14 h-14 mb-2 rounded shadow-lg border border-gray-800" alt="Manicure">
                <span class="text-yellow-400 font-medium">Manicure</span>
            </div>
            <div class="flex flex-col items-center">
                <img src="/images/Barbearia1/coloracao.jpg" class="w-14 h-14 mb-2 rounded shadow-lg border border-gray-800" alt="Coloração">
                <span class="text-yellow-400 font-medium">Coloração</span>
            </div>
        </div>

        <hr class="my-10 border-yellow-700 w-1/2">

        <section class="mb-10">
            <h2 class="text-2xl font-bold text-yellow-400 mb-2">Horário de Funcionamento</h2>
            <p class="text-gray-300">Seg a Sex: 9h - 20h | Sáb: 9h - 16h</p>
            <p class="text-gray-300">Endereço: {{ tenant()->address ?? 'Rua Exemplo, 123 - Centro' }}</p>
        </section>

        <section class="mb-10">
            <h2 class="text-2xl font-bold text-yellow-400 mb-2">O que dizem nossos clientes</h2>
            <div class="flex flex-col sm:flex-row gap-6 justify-center">
                <div class="bg-gray-800 rounded p-4 shadow max-w-xs">
                    <p class="text-gray-200 italic">"Atendimento top, ambiente limpo e profissionais excelentes!"</p>
                    <span class="text-yellow-500 font-bold block mt-2">— João S.</span>
                </div>
                <div class="bg-gray-800 rounded p-4 shadow max-w-xs">
                    <p class="text-gray-200 italic">"Meu corte ficou perfeito, virei cliente fiel!"</p>
                    <span class="text-yellow-500 font-bold block mt-2">— Carlos M.</span>
                </div>
            </div>
        </section>

        <section class="mb-10">
            <h2 class="text-2xl font-bold text-yellow-400 mb-2">Siga nas redes sociais</h2>
            <div class="flex gap-4 justify-center">
                <a href="{{ tenant()->instagram ?? '#' }}" target="_blank" class="text-yellow-500 hover:text-yellow-300 text-2xl"><i class="fab fa-instagram"></i></a>
                <a href="{{ tenant()->facebook ?? '#' }}" target="_blank" class="text-yellow-500 hover:text-yellow-300 text-2xl"><i class="fab fa-facebook"></i></a>
                <a href="https://wa.me/{{ tenant()->whatsapp ?? '5511999999999' }}" target="_blank" class="text-yellow-500 hover:text-yellow-300 text-2xl"><i class="fab fa-whatsapp"></i></a>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-yellow-400 mb-2">Promoção do mês</h2>
            <div class="bg-yellow-700 text-gray-900 rounded p-4 font-bold max-w-md mx-auto">
                Corte + Barba por R$49,90! Aproveite e agende já.
            </div>
        </section>
    </main>

    <footer class="w-full py-4 bg-gray-900 text-center text-yellow-600 text-sm shadow mt-8">
        &copy; {{ date('Y') }} {{tenant()->fantasy_name}}. Todos os direitos reservados.
    </footer>
</body>
</html>

{{-- filepath: resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo ao Salão {{tenant()->id}}</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body class="bg-gradient-to-br from-gray-900 to-gray-700 min-h-screen flex flex-col">
    <header class="w-full py-6 bg-gray-900 shadow">
        <div class="container mx-auto flex items-center justify-between px-4">
            <div class="flex items-center gap-3">
                <img src="/{{ tenant()->logo }}" alt="Logo do Salão {{tenant()->id}}" class="w-12 h-12 rounded-full object-cover border-2 border-yellow-600 shadow">
                <span class="text-2xl font-extrabold text-yellow-600 tracking-wide">Barbearia {{tenant()->id}}</span>
            </div>
            <a href="{{ route('login') }}" class="text-yellow-400 font-semibold hover:underline">Entrar</a>
        </div>
    </header>

    <main class="flex-1 flex flex-col items-center justify-center text-center px-4">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-yellow-500 mb-4 drop-shadow">Estilo e atitude para homens</h1>
        <p class="text-lg sm:text-2xl text-gray-200 mb-8">Agende seu horário e viva a experiência de um verdadeiro salão masculino!</p>
        <a href="{{ route('agendamento') }}" class="bg-yellow-600 text-gray-900 px-8 py-3 rounded-full text-lg font-bold shadow hover:bg-yellow-700 transition mb-10">Agendar agora</a>
        <div class="flex gap-8 mt-6 justify-center flex-wrap">
            <div class="flex flex-col items-center">
                <img src="/images/{{tenant()->id}}/corte.jpg" class="w-14 h-14 mb-2 rounded shadow-lg border border-gray-800" alt="Corte">
                <span class="text-yellow-400 font-medium">Corte</span>
            </div>
            <div class="flex flex-col items-center">
                <img src="/images/{{tenant()->id}}/barba.jpg" class="w-14 h-14 mb-2 rounded shadow-lg border border-gray-800" alt="Barba">
                <span class="text-yellow-400 font-medium">Barba</span>
            </div>
            <div class="flex flex-col items-center">
                <img src="/images/{{tenant()->id}}/manicure.jpg" class="w-14 h-14 mb-2 rounded shadow-lg border border-gray-800" alt="Manicure">
                <span class="text-yellow-400 font-medium">Manicure</span>
            </div>
            <div class="flex flex-col items-center">
                <img src="/images/{{tenant()->id}}/coloracao.jpg" class="w-14 h-14 mb-2 rounded shadow-lg border border-gray-800" alt="Coloração">
                <span class="text-yellow-400 font-medium">Coloração</span>
            </div>

            <!-- Adicione mais serviços se quiser -->
        </div>
    </main>

    <footer class="w-full py-4 bg-gray-900 text-center text-yellow-600 text-sm shadow mt-8">
        &copy; {{ date('Y') }} Barbearia {{tenant()->id}}. Todos os direitos reservados.
    </footer>
</body>
</html>
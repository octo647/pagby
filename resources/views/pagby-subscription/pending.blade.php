<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pagamento Pendente - PagBy</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-gray-900 via-purple-900 to-pink-900 min-h-screen text-white">
    <main class="flex-1 flex flex-col items-center justify-center text-center px-4 py-10">
        <div class="max-w-md w-full bg-white/10 backdrop-blur-sm rounded-2xl p-8 shadow-2xl">
            <div class="w-16 h-16 rounded-full bg-yellow-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            
            <h1 class="text-2xl font-bold mb-4 text-yellow-400">Pagamento Pendente</h1>
            
            <p class="text-white/80 mb-6">
                O pagamento da sua assinatura está sendo processado. Você receberá uma confirmação em breve.
            </p>
            
            <div class="space-y-3">
                <a href="{{ route('home') }}" 
                   class="block w-full bg-pink-600 text-white py-3 rounded-lg font-semibold hover:bg-pink-700 transition">
                    Voltar ao Início
                </a>
            </div>
        </div>
    </main>
</body>
</html>

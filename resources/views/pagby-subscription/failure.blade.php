<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pagamento Rejeitado - PagBy</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-gray-900 via-purple-900 to-pink-900 min-h-screen text-white">
    <main class="flex-1 flex flex-col items-center justify-center text-center px-4 py-10">
        <div class="max-w-md w-full bg-white/10 backdrop-blur-sm rounded-2xl p-8 shadow-2xl">
            <div class="w-16 h-16 rounded-full bg-red-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            
            <h1 class="text-2xl font-bold mb-4 text-red-400">Pagamento Rejeitado</h1>
            
            <p class="text-white/80 mb-6">
            
                Não foi possível processar o pagamento da sua assinatura. Verifique os dados e tente novamente.
            </p>
            
            <div class="space-y-3">
                <a href="{{ route('home') }}#planos" 
                   class="block w-full bg-pink-600 text-white py-3 rounded-lg font-semibold hover:bg-pink-700 transition">
                    Tentar Novamente
                </a>
                              
                <a href="{{ route('home') }}" 
                   class="block w-full border border-white/20 text-white py-3 rounded-lg font-semibold hover:bg-white/10 transition">
                    Voltar ao Início
                </a>
                <a href="mailto:suporte@pagby.com.br?subject=Suporte%20PagBy%20-%20Ajuda%20com%20Assinatura&body=Olá%20equipe%20de%20suporte,%0A%0AEstou%20enfrentando%20um%20problema%20com%20minha%20assinatura%20do%20plano%20{{ $plan_name }}, identificação do pagamento%20{{$payment_id }}, do estabelecimento%20&quot;{{ $tenant_name }}&quot;, tendo recebido a seguinte mensagem do sistema: &quot;{{ $message }}&quot;%0APor%20favor,%20preciso%20de%20ajuda.%0A%0AObrigado!" 
                   class="block w-full border border-white/20 text-white py-3 rounded-lg font-semibold hover:bg-white/10 transition">
                    Entrar em Contato com o Suporte
                </a>
            </div>
        </div>
    </main>
</body>
</html>

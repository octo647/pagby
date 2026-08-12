<x-pagby-layout>
    <header class="w-full py-6 bg-gradient-to-r from-indigo-900 via-purple-900 to-pink-700 shadow">
        <div class="container mx-auto flex items-center justify-between px-4">
            <div class="flex items-center gap-3">     
                <img src="{{ asset('images/logo.png') }}" alt="Logo PagBy" class="w-12 h-12 rounded-full object-cover border-2 border-pink-400 shadow">          
                <span class="text-3xl font-extrabold text-white tracking-wide">PagBy</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('login') }}" class="bg-white text-pink-700 px-4 py-2 rounded-full text-sm font-bold shadow hover:bg-pink-100 transition">Entrar</a>
                <a href="{{ route('register') }}" class="bg-pink-600 text-white px-4 py-2 rounded-full text-sm font-bold shadow hover:bg-pink-700 transition">Registrar</a>
            </div>
        </div>   
    </header>
    <main class="flex-1 flex flex-col items-center justify-center text-center bg-gray-900 px-4 py-10">
        <h1 class="text-4xl font-extrabold mb-4 text-pink-600 drop-shadow">Planos Pixby </h1>   
        <p class="text-lg text-gray-300">Descubra todas as funcionalidades que o Pixby oferece para otimizar a gestão do seu negócio.</p>   
    </main>
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($plans as $plan)
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-bold mb-2">{{ $plan->name }}</h2>
                    <p class="text-gray-700 mb-4">Mensalidade: R$ {{ number_format($plan->price, 2, ',', '.') }}</p>
                    <p class="text-gray-700 mb-4">Duração: {{ $plan->duration_days }} dias</p>
                    <p class="text-gray-700 mb-4">Serviços: {{ implode(', ', $plan->services->pluck('service')->toArray()) }}</p>
                    <a href="{{ route('plans.show', $plan) }}" class="text-pink-600 hover:underline">Ver Detalhes</a>
                </div>
            @endforeach
        </div>
</x-pagby-layout>
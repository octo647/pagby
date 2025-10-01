<!-- filepath: resources/views/home.blade.php -->
<x-pagby-layout>
    <main class="flex-1 flex flex-col items-center justify-center text-center bg-gray-900 px-4 py-10">
        <h1 class="text-4xl font-extrabold mb-4 text-pink-600 drop-shadow">Bem-vindo ao Pagby!</h1>
        <p class="mb-6 text-lg text-white/90 max-w-2xl">
            A plataforma inteligente para gestão de <span class="font-semibold text-pink-300">salões de beleza</span>, <span class="font-semibold text-pink-300">barbearias</span> e negócios de estética.<br>
            Organize sua agenda, otimize pagamentos e conquiste seus clientes com tecnologia!
        </p>
        <a href="{{ route('register-tenant') }}" class="bg-gradient-to-r from-pink-600 to-indigo-600 text-white px-6 py-3 rounded-full text-lg font-bold shadow hover:from-pink-700 hover:to-indigo-700 transition mb-8">Quero meu negócio no Pagby</a>
        <div class="mt-10 bg-white/10 rounded-xl p-8 shadow max-w-2xl">
            <h2 class="text-2xl font-semibold mb-4 text-white">Funcionalidades</h2>
            <ul class="list-disc pl-6 text-left text-white/90 space-y-2">
                <li><span class="font-bold text-pink-300">Agenda online</span> para clientes e equipe</li>
                <li><span class="font-bold text-pink-300">Gestão de serviços</span>, profissionais e horários</li>
                <li><span class="font-bold text-pink-300">Pagamentos integrados</span> e controle financeiro</li>
                <li><span class="font-bold text-pink-300">Relatórios inteligentes</span> para decisões rápidas</li>
                <li><span class="font-bold text-pink-300">Planos flexíveis</span> para todo porte de negócio</li>
                <li><span class="font-bold text-pink-300">Acesso fácil</span> pelo computador ou celular</li>
            </ul>
            <button class="mt-4 bg-pink-600 text-white px-4 py-2 rounded-full text-sm font-bold shadow hover:bg-pink-700 transition">
                <a href="{{ route('funcionalidades') }}">Ver todas as funcionalidades</a>
            </button>
        </div>
        <div class="mt-4 bg-white/10 rounded-xl p-8 shadow max-w-2xl text-white/80">
            <h2 class="text-2xl font-semibold mb-4">Comece agora</h2>
            <p class="mb-4">Crie sua conta gratuita e descubra como o Pagby pode transformar seu negócio de beleza e bem-estar.</p>
            <a href="{{ route('register-tenant') }}" class="bg-gradient-to-r from-pink-600 to-indigo-600 text-white px-6 py-3 rounded-full text-lg font-bold shadow hover:from-pink-700 hover:to-indigo-700 transition">Criar conta gratuita</a>
        </div>
        <div class="mt-4 mt-6 bg-white/10 rounded-xl p-8 shadow max-w-2xl text-white/80">
            <h3 class="text-xl font-bold mb-2">Por que Pagby?</h3>
            <p class="max-w-xl mx-auto">
                Pagby nasceu para simplificar a rotina de quem vive de beleza e bem-estar. Com tecnologia de ponta, segurança e facilidade, você foca no que faz de melhor: encantar seus clientes!
            </p>
        </div>
    </main>

</x-pagby-layout>
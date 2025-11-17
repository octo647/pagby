<x-pagby-layout>
    <header class="py-4 px-6 flex justify-between items-center">
        <div class="flex items-center">
            <a href="/"><img src="{{ asset('images/logo.png') }}" alt="Logo PagBy" class="w-24 h-18 mr-3"></a>
            
        </div>
        <nav class="hidden md:flex space-x-6">
            <a href="/" class="hover:text-pink-300 transition">Principal</a>

            <a href="/#depoimentos" class="hover:text-pink-300 transition">Depoimentos</a>
            <a href="/#planos" class="hover:text-pink-300 transition">Planos</a>
            
        </nav>
        <button id="mobile-menu-btn" class="md:hidden text-white">      
            <i class="fas fa-bars text-xl"></i>
        </button>
        <div id="mobile-menu" class="fixed inset-0 bg-gray-900 bg-opacity-95 z-50 flex flex-col items-center justify-center space-y-8 text-xl font-bold text-white transition-all duration-300 opacity-0 pointer-events-none">
        <div class="ml-8 mb-11 flex items-center">
        <a href="/"><img src="{{ asset('images/logo.png') }}" alt="Logo PagBy" class="w-32 h-20 mr-3"></a>
        </div>
        <a href="/" class="hover:text-pink-300 transition">Principal</a>
        <a href="/#depoimentos" class="hover:text-pink-300 transition">Depoimentos</a>
        <a href="/#planos" class="hover:text-pink-300 transition">Planos</a>
        <button id="close-mobile-menu" class="mt-8 text-pink-400 text-2xl"><i class="fas fa-times"></i></button>
        </div>
    </header>
    
    <main class="flex-1 flex flex-col items-center justify-center text-center bg-gray-900 px-4 py-10">
        <h1 class="text-4xl font-extrabold mb-4 text-pink-600 ">Funcionalidades do PagBy</h1>
        
        <a href="#" class="flex flex-col items-center bg-white border border-gray-200 rounded-lg shadow-sm md:flex-row md:max-w-xl hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 mb-6">
            <img class="object-cover w-full h-32 rounded-t-lg md:h-auto md:w-48 md:rounded-none md:rounded-s-lg" src="{{ asset('images/agenda.jpg') }}" alt="">
            <div class="flex flex-col justify-between p-4 leading-normal">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Agenda online</h5>
                <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Ofereça aos seus clientes a possibilidade de agendar serviços de forma prática e rápida. A agenda online permite que os usuários visualizem a disponibilidade e reservem horários com facilidade.</p>
            </div>
        </a>
        
        <a href="#" class="flex flex-col items-center bg-white border border-gray-200 rounded-lg shadow-sm md:flex-row md:max-w-xl hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 mb-6">
            <img class="object-cover w-full h-32 rounded-t-lg md:h-auto md:w-48 md:rounded-none md:rounded-s-lg" src="{{ asset('images/gestao.png') }}" alt="">
            <div class="flex flex-col justify-between p-4 leading-normal">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Gestão de Serviços, Profissionais e Horários</h5>
                <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Gerencie facilmente seus serviços, profissionais e horários disponíveis. O sistema permite uma visão clara e organizada de todos os agendamentos, garantindo que nada seja esquecido.</p>
            </div>
        </a>
        
        <a href="#" class="flex flex-col items-center bg-white border border-gray-200 rounded-lg shadow-sm md:flex-row md:max-w-xl hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 mb-6">
            <img class="object-cover w-full h-32 rounded-t-lg md:h-auto md:w-48 md:rounded-none md:rounded-s-lg" src="{{ asset('images/financeiro.jpeg') }}" alt="">
            <div class="flex flex-col justify-between p-4 leading-normal">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Pagamentos Integrados e Controle Financeiro</h5>
                <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Mantenha o controle total sobre suas finanças com nosso sistema de pagamentos integrados. Acompanhe receitas, despesas e relatórios financeiros de forma simples e eficiente.</p>
            </div>
        </a>
        
        <a href="#" class="flex flex-col items-center bg-white border border-gray-200 rounded-lg shadow-sm md:flex-row md:max-w-xl hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 mb-6">
            <img class="object-cover w-full h-32 rounded-t-lg md:h-auto md:w-48 md:rounded-none md:rounded-s-lg" src="{{ asset('images/relatorios.jpeg') }}" alt="">
            <div class="flex flex-col justify-between p-4 leading-normal">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Relatórios Inteligentes</h5>
                <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Obtenha insights valiosos sobre o desempenho do seu negócio com nossos relatórios inteligentes. Analise dados de agendamentos, serviços, assiduidade de clientes e muito mais.</p>
            </div>
        </a>
        
        <a href="#" class="flex flex-col items-center bg-white border border-gray-200 rounded-lg shadow-sm md:flex-row md:max-w-xl hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 mb-6">
            <div class="flex-shrink-0 flex items-center justify-center w-full h-32 md:w-48 md:h-auto">
              <img class="object-cover w-full h-32 rounded-t-lg md:h-auto md:w-48 md:rounded-none md:rounded-s-lg" src="{{ asset('images/assinatura.jpg') }}" alt="">  
            </div>
            <div class="flex flex-col justify-between p-4 leading-normal">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Planos de Assinatura</h5>
                <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Ofereça diferentes opções de planos de assinatura de serviços para seus clientes, permitindo que escolham a melhor opção para suas necessidades. Gerencie facilmente os planos e suas características.</p>
            </div>
        </a>
    </main>

</x-pagby-layout>
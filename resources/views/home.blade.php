<x-pagby-layout>
    <!-- Header/Navigation -->
    <header class="py-4 px-6 flex justify-between items-center">
        <div class="flex items-center">
            <img src="{{ asset('images/logo.png') }}" alt="Logo PagBy" class="w-24 h-18 mr-3">
            
        </div>
        <nav class="hidden md:flex space-x-6">
            <a href="#funcionalidades" class="hover:text-pink-300 transition">Funcionalidades</a>
            <a href="#depoimentos" class="hover:text-pink-300 transition">Depoimentos</a>
            <a href="#planos" class="hover:text-pink-300 transition">Planos</a>
            
        </nav>
        <button id="mobile-menu-btn" class="md:hidden text-white">      
            <i class="fas fa-bars text-xl"></i>
        </button>
        <div id="mobile-menu" class="fixed inset-0 bg-gray-900 bg-opacity-95 z-50 flex flex-col items-center justify-center space-y-8 text-xl font-bold text-white transition-all duration-300 opacity-0 pointer-events-none">
        <div class="ml-8 mb-11 flex items-center">
        <img src="{{ asset('images/logo.png') }}" alt="Logo PagBy" class="w-32 h-20 mr-3">
        </div>
        <a href="#funcionalidades" class="hover:text-pink-300 transition">Funcionalidades</a>
        <a href="#depoimentos" class="hover:text-pink-300 transition">Depoimentos</a>
        <a href="#planos" class="hover:text-pink-300 transition">Planos</a>
        <button id="close-mobile-menu" class="mt-8 text-pink-400 text-2xl"><i class="fas fa-times"></i></button>
        </div>
    </header>

    <main class="flex-1 flex flex-col items-center justify-center text-center px-4 py-10">
        <!-- Hero Section -->
        <div class="fade-in max-w-3xl">
            <h1 class="text-4xl md:text-5xl font-extrabold mb-4 text-pink-600 ">Bem-vindo ao PagBy!</h1>
            <p class="mb-6 text-lg md:text-xl text-white/90 max-w-2xl mx-auto">
                A plataforma inteligente para gestão de <span class="font-semibold text-pink-300">salões de beleza</span>, 
                <span class="font-semibold text-pink-300">barbearias</span> e negócios de estética.<br>
                Organize sua agenda, otimize pagamentos e conquiste seus clientes com tecnologia!
            </p>
          
            <a href="#planos" 
               class="inline-block bg-gradient-to-r from-pink-600 to-indigo-600 text-white px-8 py-4 rounded-full text-lg font-bold shadow-lg hover:from-pink-700 hover:to-indigo-700 transition mb-8 pulse-animation">
                Quero meu negócio no PagBy
            </a>
        </div>
        
        <!-- App Preview -->
        <div class="fade-in mt-12 max-w-4xl w-full">
            <div class="bg-gray-800/50 rounded-2xl p-6 md:p-8 shadow-2xl">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="md:w-1/2 mb-8 md:mb-0 md:pr-8">
                        <h2 class="text-2xl md:text-3xl font-bold mb-4 text-white">Interface Intuitiva e Moderna</h2>
                        <p class="text-white/80 mb-6">Gerencie seu negócio de forma simples e eficiente com uma interface pensada para profissionais da beleza.</p>
                        <div class="flex space-x-4">
                            <div class="flex items-center">
                                <i class="fas fa-check text-pink-500 mr-2"></i>
                                <span>Fácil de usar</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-pink-500 mr-2"></i>
                                <span>Totalmente responsivo</span>
                            </div>
                        </div>
                    </div>
                    <div class="md:w-1/2 relative">
                        <div class="bg-gray-700 rounded-xl p-4 shadow-lg mx-auto max-w-xs">
                            <div class="bg-gray-800 rounded-lg p-4 mb-4">
                                <div class="flex justify-between items-center mb-4">
                                    <div class="text-sm font-semibold">Agenda</div>
                                    <div class="text-xs text-gray-400">Hoje</div>
                                </div>
                                <div class="space-y-3">
                                    <div class="bg-pink-900/30 rounded p-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm">Maria Silva</span>
                                            <span class="text-xs">09:00</span>
                                        </div>
                                        <div class="text-xs text-gray-400">Corte e escova</div>
                                    </div>
                                    <div class="bg-indigo-900/30 rounded p-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm">João Santos</span>
                                            <span class="text-xs">10:30</span>
                                        </div>
                                        <div class="text-xs text-gray-400">Barba</div>
                                    </div>
                                    <div class="bg-purple-900/30 rounded p-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm">Ana Costa</span>
                                            <span class="text-xs">14:00</span>
                                        </div>
                                        <div class="text-xs text-gray-400">Manicure</div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-around">
                                <div class="text-center">
                                    <i class="fas fa-calendar text-pink-500 mb-1"></i>
                                    <div class="text-xs">Agenda</div>
                                </div>
                                <div class="text-center">
                                    <i class="fas fa-users text-indigo-500 mb-1"></i>
                                    <div class="text-xs">Clientes</div>
                                </div>
                                <div class="text-center">
                                    <i class="fas fa-chart-bar text-purple-500 mb-1"></i>
                                    <div class="text-xs">Relatórios</div>
                                </div>
                                <div class="text-center">
                                    <i class="fas fa-cog text-gray-500 mb-1"></i>
                                    <div class="text-xs">Mais</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Features Section -->
        <div id="funcionalidades" class="fade-in mt-16 bg-white/10 rounded-2xl p-8 shadow max-w-4xl w-full">
            <h2 class="text-2xl md:text-3xl font-semibold mb-6 text-white">Funcionalidades</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $features = [
                        [
                            'icon' => 'calendar-alt',
                            'color' => 'pink',
                            'title' => 'Agenda Online',
                            'description' => 'Clientes agendam serviços 24/7 e sua equipe tem acesso centralizado.'
                        ],
                        [
                            'icon' => 'user-friends',
                            'color' => 'indigo',
                            'title' => 'Gestão de Equipe',
                            'description' => 'Organize profissionais, serviços e horários de forma eficiente.'
                        ],
                        [
                            'icon' => 'credit-card',
                            'color' => 'purple',
                            'title' => 'Pagamentos Integrados',
                            'description' => 'Aceite múltiplas formas de pagamento com segurança.'
                        ],
                        [
                            'icon' => 'chart-line',
                            'color' => 'green',
                            'title' => 'Relatórios Inteligentes',
                            'description' => 'Acompanhe métricas e tome decisões baseadas em dados.'
                        ],
                        [
                            'icon' => 'mobile-alt',
                            'color' => 'yellow',
                            'title' => 'Acesso Multiplataforma',
                            'description' => 'Funciona perfeitamente no computador, tablet e celular.'
                        ],
                        [
                            'icon' => 'bell',
                            'color' => 'blue',
                            'title' => 'Lembretes Automáticos',
                            'description' => 'Reduza faltas com notificações para seus clientes.'
                        ]
                    ];
                @endphp
                
                @foreach($features as $feature)
                <div class="feature-card bg-gray-800/50 rounded-xl p-6 transition-transform duration-300 hover:scale-105 hover:shadow-xl">
                    <div class="w-12 h-12 rounded-full bg-{{ $feature['color'] }}-600/20 flex items-center justify-center mb-4">
                        <i class="fas fa-{{ $feature['icon'] }} text-{{ $feature['color'] }}-400 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-{{ $feature['color'] }}-300">{{ $feature['title'] }}</h3>
                    <p class="text-white/80">{{ $feature['description'] }}</p>
                </div>
                @endforeach
            </div>
            
            <button class="mt-8 bg-pink-600 text-white px-6 py-3 rounded-full font-bold shadow hover:bg-pink-700 transition">
                <a href="{{ route('funcionalidades') }}">Ver todas as funcionalidades</a>
            </button>
        </div>
        
        <!-- Stats Section -->
        <div class="fade-in mt-16 max-w-4xl w-full">
            <div class="bg-gradient-to-r from-pink-900/30 to-indigo-900/30 rounded-2xl p-8">
                <h2 class="text-2xl md:text-3xl font-bold mb-8 text-center">O PagBy em Números</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    @php
                        $stats = [
                            ['value' => '500+', 'label' => 'Negócios Atendidos', 'color' => 'pink'],
                            ['value' => '50K+', 'label' => 'Agendamentos/Mês', 'color' => 'indigo'],
                            ['value' => '98%', 'label' => 'Satisfação dos Clientes', 'color' => 'purple'],
                            ['value' => '40%', 'label' => 'Aumento de Produtividade', 'color' => 'green']
                        ];
                    @endphp
                    
                    @foreach($stats as $stat)
                    <div class="text-center">
                        <div class="text-3xl md:text-4xl font-bold text-{{ $stat['color'] }}-400">{{ $stat['value'] }}</div>
                        <div class="text-white/80 mt-2">{{ $stat['label'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Testimonials -->
        <div id="depoimentos" class="fade-in mt-16 bg-white/10 rounded-2xl p-8 shadow max-w-4xl w-full">
            <h2 class="text-2xl md:text-3xl font-semibold mb-6 text-white">O que dizem nossos clientes</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @php
                    $testimonials = [
                        [
                            'initial' => 'L',
                            'name' => 'Larissa Mendes',
                            'text' => '"O PagBy transformou minha barbearia! Agora consigo gerenciar horários, pagamentos e clientes tudo em um só lugar. Meus clientes amam a facilidade de agendamento online."'
                        ],
                        [
                            'initial' => 'A',
                            'name' => 'Ana Paula Costa',
                            'text' => '"Minha produtividade aumentou 40% desde que comecei a usar o PagBy. Os lembretes automáticos reduziram as faltas pela metade. Recomendo para todos os salões!"'
                        ]
                    ];
                @endphp
                
                @foreach($testimonials as $testimonial)
                <div class="bg-gray-800/50 rounded-xl p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-pink-600 flex items-center justify-center text-white font-bold mr-4">
                            {{ $testimonial['initial'] }}
                        </div>
                        <div>
                            <h4 class="font-bold">{{ $testimonial['name'] }}</h4>
                            <div class="flex text-yellow-400">
                                @for($i = 0; $i < 5; $i++)
                                <i class="fas fa-star"></i>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <p class="text-white/80 italic">{{ $testimonial['text'] }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- SEÇÃO DE PLANOS PAGBY -->
        <div id="planos" class="fade-in mt-16 max-w-6xl w-full">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4 text-white">
                    Escolha Seu Plano
                </h2>
                <p class="text-xl text-white/80 max-w-2xl mx-auto">
                    Planos flexíveis para impulsionar seu negócio
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <!-- Plano Básico -->
                <div class="bg-white rounded-2xl shadow-xl p-8 border-2 border-transparent hover:border-purple-500 transition-all duration-300 transform hover:scale-105">
                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Básico</h3>
                        <div class="text-4xl font-bold text-purple-600 mb-1">
                            R$ 29,90
                            <span class="text-lg text-gray-500 font-normal">/mês</span>
                        </div>
                        <p class="mb-2 text-purple-600 font-semibold">(por profissional)</p>
                        <p class="text-gray-600">Ideal para começar</p>
                    </div>

                    <ul class="space-y-3 mb-8 text-gray-700">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Agendamentos online
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Pagamentos via PIX/Cartão
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Gestão de clientes
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Suporte a planos de assinatura
                        </li>


                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Suporte por email
                        </li>
                    </ul>
                <a href="{{ route('pagby-subscription.choose-plan', ['plan' => 'basico']) }}" 
                class="block w-full bg-purple-600 text-white py-3 rounded-lg font-semibold hover:bg-purple-700 transition-colors text-center">
                    Escolher Básico
                </a>

                </div>

                <!-- Plano Premium -->
                <div class="bg-white rounded-2xl shadow-xl p-8 border-2 border-pink-500 relative transform hover:scale-105 transition-all duration-300">
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                        <span class="bg-gradient-to-r from-pink-500 to-purple-500 text-white px-4 py-1 rounded-full text-sm font-semibold">
                            MAIS POPULAR
                        </span>
                    </div>

                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Premium</h3>
                        <div class="text-4xl font-bold text-pink-600 mb-1">
                            R$ 59,90
                            <span class="text-lg text-gray-500 font-normal">/mês</span>
                        </div>
                        <p class="mb-2 text-purple-600 font-semibold">(por profissional)</p>
                        <p class="text-gray-600">Recursos completos</p>
                    </div>

                    <ul class="space-y-3 mb-8 text-gray-700">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Todos recursos do Básico
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Relatórios avançados
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Múltiplas filiais
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Integração WhatsApp
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Suporte prioritário
                        </li>
                    </ul>
                <a href="{{ route('pagby-subscription.choose-plan', ['plan' => 'premium']) }}" 
                class="block w-full bg-gradient-to-r from-pink-600 to-purple-600 text-white py-3 rounded-lg font-semibold hover:from-pink-700 hover:to-purple-700 transition-all text-center">
                    Escolher Premium
                </a>
                    
                </div>
            </div>

            <!-- Garantia -->
            <div class="text-center mt-12">
                <p class="text-white/80 mb-4">
                    🛡️ Cancele quando quiser
                </p>
                <p class="text-sm text-white/60">
                    Pagamentos via PIX, cartão de crédito e débito
                </p>
            </div>
        </div>
        
        <!-- Why PagBy Section -->
        <div class="fade-in mt-16 bg-white/10 rounded-2xl p-8 shadow max-w-4xl w-full text-center">
            <h3 class="text-2xl font-bold mb-4">Por que PagBy?</h3>
            <p class="max-w-2xl mx-auto text-white/80">
                PagBy nasceu para simplificar a rotina de quem vive de beleza e bem-estar. Com tecnologia de ponta, segurança e facilidade, você foca no que faz de melhor: encantar seus clientes!
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                @php
                    $reasons = [
                        [
                            'icon' => 'shield-alt',
                            'color' => 'pink',
                            'title' => 'Segurança',
                            'description' => 'Seus dados e transações protegidos com criptografia de ponta.'
                        ],
                        [
                            'icon' => 'headset',
                            'color' => 'indigo',
                            'title' => 'Suporte Dedicado',
                            'description' => 'Equipe especializada pronta para ajudar quando precisar.'
                        ],
                        [
                            'icon' => 'sync',
                            'color' => 'purple',
                            'title' => 'Atualizações Constantes',
                            'description' => 'Sempre evoluindo com novas funcionalidades e melhorias.'
                        ]
                    ];
                @endphp
                
                @foreach($reasons as $reason)
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 rounded-full bg-{{ $reason['color'] }}-600/20 flex items-center justify-center mb-4">
                        <i class="fas fa-{{ $reason['icon'] }} text-{{ $reason['color'] }}-400 text-2xl"></i>
                    </div>
                    <h4 class="font-bold mb-2">{{ $reason['title'] }}</h4>
                    <p class="text-white/80 text-sm">{{ $reason['description'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="mt-16 py-8 px-6 border-t border-gray-700">
        <div class="max-w-4xl mx-auto flex flex-col md:flex-row justify-between items-center">
            <div class="flex items-center mb-4 md:mb-0">
                <img src="{{ asset('images/logo.png') }}" alt="Logo PagBy" class="w-24 h-18 mr-3">
                
            </div>
            <div class="text-center md:text-right text-white/70">
                <p>&copy; {{ date('Y') }} PagBy. Todos os direitos reservados.</p>
                <p class="text-sm mt-2">Feito com ❤️ para profissionais da beleza</p>
            </div>
        </div>
    </footer>
</x-pagby-layout>
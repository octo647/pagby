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
    <!--    <div class="fade-in mt-16 max-w-4xl w-full">
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
        -->
        
        <!-- Testimonials -->
     <!--   <div id="depoimentos" class="fade-in mt-16 bg-white/10 rounded-2xl p-8 shadow max-w-4xl w-full">
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
        -->

        <!-- SEÇÃO DE PLANOS PAGBY -->
        <div id="planos" class="fade-in mt-16 max-w-6xl w-full">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold mb-4 text-white">
                    Escolha seu plano Pagby
                </h2>
                <p class="text-xl text-white/80 max-w-2xl mx-auto">
                    Selecione a periodicidade e o número de funcionários para ver o valor do plano.
                </p>
            </div>

            <div class="max-w-3xl mx-auto bg-white rounded-3xl shadow-2xl p-8">
                <form id="form-plano" class="grid grid-cols-1 md:grid-cols-2 gap-8 items-end">
                    <div>
                        <label for="periodicidade" class="block font-bold mb-2 text-gray-800">Periodicidade</label>
                        <select id="periodicidade" name="periodicidade" class="w-40 px-2 py-2 rounded-md border border-gray-300 text-gray-800 bg-white focus:ring-pink-500 focus:border-pink-500 text-base">
                            <option value="mensal">Mensal</option>
                            <option value="trimestral">Trimestral</option>
                            <option value="semestral">Semestral</option>
                            <option value="anual">Anual</option>
                        </select>
                    </div>
                    <div>
                        <label for="numFuncionarios" class="block font-bold mb-2 text-gray-800">Nº de Funcionários</label>
                        <input type="number" id="numFuncionarios" name="numFuncionarios" min="1" max="7" value="1" class="w-32 px-2 py-2 rounded-md border border-gray-300 text-gray-800 bg-white focus:ring-pink-500 focus:border-pink-500 text-base" />
                    </div>
                </form>
                <div class="mt-8 text-center">
                                        <div id="avisoFuncionarios" class="hidden text-red-600 text-lg font-semibold mb-4">
                                            Para mais de 7 funcionários, consulte valores pelo WhatsApp <a href="https://wa.me/{{ config('pagby.whatsapp_number') }}" class="underline text-green-700" target="_blank">{{ config('pagby.whatsapp_display') }}</a>.
                                        </div>
                    <div id="equivalenteMensalLabel" class="text-3xl font-extrabold text-pink-600 mb-2">
                        Equivalente mensal:
                    </div>
                    <div id="valorPlano" class="text-5xl font-bold text-purple-700 mb-2">
                        R$ 60,00/mês
                    </div>
                    <div id="valorTotalPlano" class="text-lg text-gray-700 mb-4">
                        Valor total: R$ 60,00
                    </div>
                    <a id="btn-assinar" href="#" class="inline-block bg-gradient-to-r from-pink-600 to-purple-600 text-white px-10 py-4 rounded-full text-xl font-bold shadow-lg hover:from-pink-700 hover:to-purple-700 transition-all transform hover:scale-105">
                        Assinar este plano
                    </a>
                </div>
            </div>

            <div class="text-center mt-12 space-y-4">
                <div class="flex flex-col md:flex-row justify-center items-center gap-6 text-white/90">
                    <div class="flex items-center gap-2">
                        <svg class="w-6 h-6 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Dados 100% seguros</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-6 h-6 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Suporte dedicado</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-6 h-6 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Atualizações constantes</span>
                    </div>
                </div>
            </div>
        </div>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const valorBase = 60.00;
        const acrescimoFuncionario = 0.20;
        const descontos = {
            'mensal': 0,
            'trimestral': 0.20,
            'semestral': 0.30,
            'anual': 0.40
        };
        const meses = {
            'mensal': 1,
            'trimestral': 3,
            'semestral': 6,
            'anual': 12
        };

        function calcularPlano(numFuncionarios, periodicidade) {
            let valor = valorBase * Math.pow(1 + acrescimoFuncionario, numFuncionarios - 1);
            let desconto = descontos[periodicidade] || 0;
            let valorFinal = valor * (1 - desconto);
            let total = valorFinal * (meses[periodicidade] || 1);
            let equivalenteMensal = total / (meses[periodicidade] || 1);
            return {
                total: Number(total.toFixed(2)),
                mensal: Number(equivalenteMensal.toFixed(2))
            };
        }


        function atualizarValores() {
            const periodicidade = document.getElementById('periodicidade').value;
            const numFuncionariosInput = document.getElementById('numFuncionarios');
            let numFuncionarios = parseInt(numFuncionariosInput.value) || 1;
            const aviso = document.getElementById('avisoFuncionarios');
            const equivalenteMensalLabel = document.getElementById('equivalenteMensalLabel');
            const valorPlano = document.getElementById('valorPlano');
            const valorTotalPlano = document.getElementById('valorTotalPlano');

            if (numFuncionarios > 7) {
                aviso.classList.remove('hidden');
                equivalenteMensalLabel.style.display = 'none';
                valorPlano.style.display = 'none';
                valorTotalPlano.style.display = 'none';
                // Limita o valor do input a 7
                numFuncionariosInput.value = 7;
            } else {
                aviso.classList.add('hidden');
                valorPlano.style.display = '';
                if (periodicidade === 'mensal') {
                    equivalenteMensalLabel.style.display = 'none';
                    valorTotalPlano.style.display = 'none';
                    valorPlano.textContent = `R$ ${calcularPlano(numFuncionarios, periodicidade).mensal.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}/mês`;
                } else {
                    equivalenteMensalLabel.style.display = '';
                    valorTotalPlano.style.display = '';
                    const valores = calcularPlano(numFuncionarios, periodicidade);
                    valorPlano.textContent = `R$ ${valores.mensal.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}/mês`;
                    valorTotalPlano.textContent = `Valor total: R$ ${valores.total.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                }
            }
        }

        // Garante que o aviso apareça ao colar ou digitar manualmente
        document.getElementById('numFuncionarios').addEventListener('change', atualizarValores);

        document.getElementById('periodicidade').addEventListener('change', atualizarValores);
        document.getElementById('numFuncionarios').addEventListener('input', atualizarValores);
        atualizarValores();

        // Ativa o botão Assinar este plano
        document.getElementById('btn-assinar').addEventListener('click', function(e) {
            e.preventDefault();
            const periodicidade = document.getElementById('periodicidade').value;
            const numFuncionarios = parseInt(document.getElementById('numFuncionarios').value) || 1;
            // Só permite se até 7 funcionários
            if (numFuncionarios > 7) return;
            const url = `{{ route('register-tenant') }}?plan=${encodeURIComponent(periodicidade)}&employees=${numFuncionarios}`;
            window.location.href = url;
        });
    });
</script>
</x-pagby-layout>
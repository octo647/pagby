<x-pagby-layout>
    <!-- Header/Navigation -->
    <header class="py-4 px-6 flex justify-between items-center">
        <div class="flex items-center">
            <img src="{{ asset('images/logo.png') }}" alt="Logo PagBy" class="w-24 h-18 mr-3">
            
        </div>
        <nav class="hidden md:flex space-x-6">
            <a href="#funcionalidades" class="hover:text-pink-300 transition">Funcionalidades</a>            
            <a href="#planos" class="hover:text-pink-300 transition">Planos</a>
            <a href="#contato" class="hover:text-pink-300 transition">Contato</a>
        </nav>
        <button id="mobile-menu-btn" class="md:hidden text-white">      
            <i class="fas fa-bars text-xl"></i>
        </button>
        <div id="mobile-menu" class="fixed inset-0 bg-gray-900 bg-opacity-95 z-50 flex flex-col items-center justify-center space-y-8 text-xl font-bold text-white transition-all duration-300 opacity-0 pointer-events-none">
        <div class="ml-8 mb-11 flex items-center">
        <img src="{{ asset('images/logo.png') }}" alt="Logo PagBy" class="w-32 h-20 mr-3">
        </div>
        <a href="#funcionalidades" class="hover:text-pink-300 transition">Funcionalidades</a>        
        <a href="#planos" class="hover:text-pink-300 transition">Planos</a>        
        <a href="#contato" class="hover:text-pink-300 transition">Contato</a>
        <button id="close-mobile-menu" class="mt-8 text-pink-400 text-2xl"><i class="fas fa-times"></i></button>
        </div>
    </header>

    <main class="flex-1 flex flex-col items-center justify-center text-center px-4 py-4">
        <!-- Hero Section -->
        <div class="fade-in max-w-3xl">
            <h2 class="text-2xl md:text-5xl font-extrabold mb-2 text-pink-600 ">Clientes estão indo embora porque você demora a responder
            </h2>
            <p class="mb-6 text-lg md:text-xl text-white/90 max-w-2xl mx-auto">
                
               ✅  Organize sua agenda, confirme clientes automaticamente e evite faltas             
            </p>
            <!-- Inserir figura de um cliente satisfeito -->
            <div class="w-full max-w-4xl max-h-[600px] overflow-hidden rounded-2xl shadow-2xl mb-8">
                <img src="{{ asset('images/barbeiro_trabalhando.jpeg') }}" alt="Cliente Satisfeito" class="w-100 h-100 rounded shadow-lg object-cover">
           
            </div>
          
            <a href="#planos" 
               class="inline-block bg-gradient-to-r from-pink-600 to-indigo-600 text-white px-8 py-4 rounded-full text-lg font-bold shadow-lg hover:from-pink-700 hover:to-indigo-700 transition mb-8 pulse-animation">
                Testar Grátis Agora
            </a>  
            <div class="bg-gradient-to-r from-pink-900/30 to-indigo-900/30 rounded-xl p-4 mb-8">
               <p class="text-white/80">✅ Sem cartão • Comece em minutos</p>
        </div>
        <div class="w-full flex justify-center mt-8 mb-12">
            <div class="w-full max-w-4xl max-h-[600px] overflow-hidden rounded-2xl shadow-2xl">
                <img src="{{ asset('images/consultando_agenda5.png') }}" alt="Consultando Agenda" class="w-full h-full object-cover">
            </div>
        </div>
          <div class="bg-gradient-to-r from-pink-900/30 to-indigo-900/30 rounded-xl p-4 mb-8">
                <h3 class="text-xl font-bold mb-4 text-white">Com o PagBy você pode:</h3>
           <ul>
               <li>Confirmar clientes automaticamente</li>
               <li>Evitar faltas</li>
               <li>Agendar sem trocar mensagens</li>
            </ul>
            </div>
        <span class="inline-block mt-4 text-yellow-300 font-bold text-xl">✨ Sem compromisso • Sem cartão de crédito • Acesso imediato</span>

        <!-- Seção de Benefícios Rápidos -->
        <div class="fade-in mt-12 max-w-5xl w-full">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white/10 backdrop-blur rounded-2xl p-6 text-center hover:scale-105 transition-transform">
                    <div class="text-5xl mb-4">⏰</div>
                    <h4 class="text-xl font-bold text-white mb-2">Economize 10h/semana</h4>
                    <p class="text-white/80">Pare de responder mensagens e foque no que importa: seus clientes</p>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-2xl p-6 text-center hover:scale-105 transition-transform">
                    <div class="text-5xl mb-4">📈</div>
                    <h4 class="text-xl font-bold text-white mb-2">Reduza faltas em 70%</h4>
                    <p class="text-white/80">Lembretes automáticos garantem que seus clientes apareçam</p>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-2xl p-6 text-center hover:scale-105 transition-transform">
                    <div class="text-5xl mb-4">💰</div>
                    <h4 class="text-xl font-bold text-white mb-2">Aumente seu faturamento</h4>
                    <p class="text-white/80">Agenda otimizada = mais horários preenchidos = mais receita</p>
                </div>
            </div>
        </div>
        
        <!-- Transformation Section -->
        <div class="fade-in mt-16 max-w-6xl w-full">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 text-white">
                A transformação que sua agenda precisa
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- ANTES -->
                <div class="bg-red-900/20 border-2 border-red-500/50 rounded-2xl p-6 md:p-8 transition-transform hover:scale-105">
                    <div class="text-center mb-6">
                        <span class="inline-block bg-red-600 text-white px-6 py-2 rounded-full font-bold text-lg mb-4">
                            ❌ ANTES
                        </span>
                        <h3 class="text-2xl font-bold text-red-300 mb-4">Sem o PagBy</h3>
                    </div>
                    
                    <div class="mb-6 flex justify-center">
                        <img src="{{ asset('images/agenda_papel.jpeg') }}" alt="Agenda Desorganizada" class="rounded-xl shadow-lg w-48 h-auto">
                    </div>
                    
                    <ul class="space-y-3 text-white/90">
                        <li class="flex items-start">
                            <i class="fas fa-times-circle text-red-400 mr-3 mt-1"></i>
                            <span>Agenda bagunçada e difícil de ler</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-times-circle text-red-400 mr-3 mt-1"></i>
                            <span>Cliente indo embora pela demora</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-times-circle text-red-400 mr-3 mt-1"></i>
                            <span>Mensagens confusas e mal organizadas</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-times-circle text-red-400 mr-3 mt-1"></i>
                            <span>Perda de tempo respondendo cada cliente</span>
                        </li>
                    </ul>
                </div>
                
                <!-- DEPOIS -->
                <div class="bg-green-900/20 border-2 border-green-500/50 rounded-2xl p-6 md:p-8 transition-transform hover:scale-105">
                    <div class="text-center mb-6">
                        <span class="inline-block bg-green-600 text-white px-6 py-2 rounded-full font-bold text-lg mb-4">
                            ✅ DEPOIS
                        </span>
                        <h3 class="text-2xl font-bold text-green-300 mb-4">Com o PagBy</h3>
                    </div>
                    
                    <div class="mb-6 flex justify-center">
                        <img src="{{ asset('images/agenda1.png') }}" alt="Agenda Digital Organizada" class="rounded-xl shadow-lg w-48 h-auto">
                    </div>
                    
                    <ul class="space-y-3 text-white/90">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-400 mr-3 mt-1"></i>
                            <span>Agenda automática e sempre atualizada</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-400 mr-3 mt-1"></i>
                            <span>Clientes confirmados automaticamente</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-400 mr-3 mt-1"></i>
                            <span>Rotina organizada e mais produtiva</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-400 mr-3 mt-1"></i>
                            <span>Mais tempo para atender seus clientes</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="text-center mt-8">
                <a href="#planos" 
                   class="inline-block bg-gradient-to-r from-green-600 to-emerald-600 text-white px-8 py-4 rounded-full text-lg font-bold shadow-lg hover:from-green-700 hover:to-emerald-700 transition">
                    Testar grátis agora →
                </a>
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
                <a href="/funcionalidades">Ver todas as funcionalidades</a>
            </button>
        </div>

        <!-- Seção: Veja o PagBy em Ação -->
        <div class="fade-in mt-20 max-w-7xl w-full">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    Veja o PagBy em Ação
                </h2>
                <p class="text-xl text-white/80 max-w-2xl mx-auto">
                    Screenshots reais da plataforma mostrando como o PagBy pode transformar a gestão do seu salão
                </p>
            </div>

            <!-- Para Proprietários -->
            <div class="mb-16 bg-gradient-to-br from-purple-900/30 to-pink-900/30 rounded-3xl p-8 md:p-12">
                <div class="text-center mb-8">
                    <span class="inline-block bg-purple-600 text-white px-6 py-2 rounded-full font-bold text-lg mb-3">
                        👔 Para Proprietários
                    </span>
                    <h3 class="text-2xl md:text-3xl font-bold text-white mb-2">
                        Tenha controle total do seu negócio
                    </h3>
                    <p class="text-white/80 max-w-2xl mx-auto">
                        Acompanhe faturamento, gerencie equipe e tome decisões baseadas em dados reais
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white/10 backdrop-blur rounded-2xl p-4 hover:scale-105 transition-transform">
                        <img src="{{ asset('images/screenshots/proprietario/faturamento_mensal.png') }}" 
                             alt="Faturamento Mensal" 
                             class="rounded-xl shadow-2xl w-full h-auto border-2 border-purple-400/30">
                        <p class="text-white font-semibold mt-4 text-center">📊 Acompanhe o faturamento em tempo real</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-2xl p-4 hover:scale-105 transition-transform">
                        <img src="{{ asset('images/screenshots/proprietario/balanco_diario.png') }}" 
                             alt="Balanço Diário" 
                             class="rounded-xl shadow-2xl w-full h-auto border-2 border-purple-400/30">
                        <p class="text-white font-semibold mt-4 text-center">💰 Balanço diário detalhado por filial</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white/5 rounded-xl p-4 text-center">
                        <img src="{{ asset('images/screenshots/proprietario/controle_agendas.png') }}" 
                             alt="Controle de Agendas" 
                             class="rounded-lg shadow-lg w-full h-auto mb-3 border border-white/20">
                        <p class="text-white/90 text-sm">📅 Controle completo das agendas</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-4 text-center">
                        <img src="{{ asset('images/screenshots/proprietario/ranking_servicos.png') }}" 
                             alt="Ranking de Serviços" 
                             class="rounded-lg shadow-lg w-full h-auto mb-3 border border-white/20">
                        <p class="text-white/90 text-sm">🏆 Ranking dos serviços mais rentáveis</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-4 text-center">
                        <img src="{{ asset('images/screenshots/proprietario/funcionarios.png') }}" 
                             alt="Gestão de Funcionários" 
                             class="rounded-lg shadow-lg w-full h-auto mb-3 border border-white/20">
                        <p class="text-white/90 text-sm">👥 Gestão completa da equipe</p>
                    </div>
                </div>

                <div class="text-center mt-8">
                    <a href="#planos" 
                       class="inline-block bg-purple-600 hover:bg-purple-700 text-white px-8 py-4 rounded-full text-lg font-bold shadow-lg transition">
                        Começar agora →
                    </a>
                </div>
            </div>

            <!-- Para Profissionais -->
            <div class="mb-16 bg-gradient-to-br from-blue-900/30 to-indigo-900/30 rounded-3xl p-8 md:p-12">
                <div class="text-center mb-8">
                    <span class="inline-block bg-blue-600 text-white px-6 py-2 rounded-full font-bold text-lg mb-3">
                        ✂️ Para Profissionais
                    </span>
                    <h3 class="text-2xl md:text-3xl font-bold text-white mb-2">
                        Sua agenda sempre organizada
                    </h3>
                    <p class="text-white/80 max-w-2xl mx-auto">
                        Gerencie seus horários, serviços e honorários de forma simples e eficiente
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white/10 backdrop-blur rounded-2xl p-4 hover:scale-105 transition-transform">
                        <img src="{{ asset('images/screenshots/profissional/minha_agenda.png') }}" 
                             alt="Minha Agenda" 
                             class="rounded-xl shadow-2xl w-full h-auto border-2 border-blue-400/30">
                        <p class="text-white font-semibold mt-4 text-center">📱 Agenda pessoal sempre atualizada</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-2xl p-4 hover:scale-105 transition-transform">
                        <img src="{{ asset('images/screenshots/profissional/honorarios.png') }}" 
                             alt="Honorários" 
                             class="rounded-xl shadow-2xl w-full h-auto border-2 border-blue-400/30">
                        <p class="text-white font-semibold mt-4 text-center">💵 Acompanhe seus ganhos facilmente</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white/5 rounded-xl p-4 text-center">
                        <img src="{{ asset('images/screenshots/profissional/meus_servicos.png') }}" 
                             alt="Meus Serviços" 
                             class="rounded-lg shadow-lg w-full h-auto mb-3 border border-white/20">
                        <p class="text-white/90 text-sm">💼 Gerencie seus serviços</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-4 text-center">
                        <img src="{{ asset('images/screenshots/profissional/meus_horarios.png') }}" 
                             alt="Meus Horários" 
                             class="rounded-lg shadow-lg w-full h-auto mb-3 border border-white/20">
                        <p class="text-white/90 text-sm">⏰ Defina seus horários disponíveis</p>
                    </div>
                </div>

                <div class="text-center mt-8">
                    <a href="#planos" 
                       class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-full text-lg font-bold shadow-lg transition">
                        Experimentar grátis →
                    </a>
                </div>
            </div>

            <!-- Para Clientes -->
            <div class="mb-16 bg-gradient-to-br from-green-900/30 to-emerald-900/30 rounded-3xl p-8 md:p-12">
                <div class="text-center mb-8">
                    <span class="inline-block bg-green-600 text-white px-6 py-2 rounded-full font-bold text-lg mb-3">
                        😊 Para Clientes
                    </span>
                    <h3 class="text-2xl md:text-3xl font-bold text-white mb-2">
                        Agendar nunca foi tão fácil
                    </h3>
                    <p class="text-white/80 max-w-2xl mx-auto">
                        Seus clientes agendam online 24/7, sem precisar ligar ou mandar mensagem
                    </p>
                </div>

                <div class="flex justify-center">
                    <div class="max-w-md bg-white/10 backdrop-blur rounded-2xl p-6 hover:scale-105 transition-transform">
                        <img src="{{ asset('images/screenshots/cliente/historico.png') }}" 
                             alt="Agendamento Cliente" 
                             class="rounded-xl shadow-2xl w-full h-auto border-2 border-green-400/30">
                        <p class="text-white font-semibold mt-4 text-center">
                            ⚡ Agendamento instantâneo sem complicação
                        </p>
                        <p class="text-white/70 text-sm text-center mt-2">
                            Seus clientes escolhem profissional, serviço e horário em poucos cliques
                        </p>
                    </div>
                </div>

                <div class="text-center mt-8">
                    <div class="bg-green-500/20 border-2 border-green-400 rounded-xl p-4 max-w-2xl mx-auto mb-6">
                        <p class="text-green-200 font-bold">
                            ✨ Menos mensagens no WhatsApp = Mais tempo para atender
                        </p>
                    </div>
                    <a href="#planos" 
                       class="inline-block bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-full text-lg font-bold shadow-lg transition">
                        Quero facilitar minha vida →
                    </a>
                </div>
            </div>
        </div>

        <!-- Seção: Respondendo suas dúvidas -->
        <div class="fade-in mt-20 max-w-5xl w-full">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    "Mas será que funciona para mim?"
                </h2>
                <p class="text-xl text-white/80">
                    Eliminamos todas as desculpas para você não experimentar
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white/10 backdrop-blur rounded-2xl p-6 border-2 border-white/20">
                    <div class="flex items-start">
                        <div class="text-3xl mr-4">🤔</div>
                        <div>
                            <h4 class="text-xl font-bold text-white mb-2">"É difícil de usar?"</h4>
                            <p class="text-white/80">
                                Não! Interface super intuitiva. Se você usa Instagram, vai usar o PagBy facilmente. 
                                <span class="text-green-300 font-semibold">Configuração em 5 minutos.</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur rounded-2xl p-6 border-2 border-white/20">
                    <div class="flex items-start">
                        <div class="text-3xl mr-4">💳</div>
                        <div>
                            <h4 class="text-xl font-bold text-white mb-2">"E se eu não gostar?"</h4>
                            <p class="text-white/80">
                                Sem problemas! 30 dias grátis para testar tudo. 
                                <span class="text-green-300 font-semibold">Não pedimos cartão de crédito no teste.</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur rounded-2xl p-6 border-2 border-white/20">
                    <div class="flex items-start">
                        <div class="text-3xl mr-4">📱</div>
                        <div>
                            <h4 class="text-xl font-bold text-white mb-2">"Preciso trocar de celular?"</h4>
                            <p class="text-white/80">
                                Não! Funciona em qualquer celular ou computador. 
                                <span class="text-green-300 font-semibold">Acesse de onde estiver.</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur rounded-2xl p-6 border-2 border-white/20">
                    <div class="flex items-start">
                        <div class="text-3xl mr-4">🎓</div>
                        <div>
                            <h4 class="text-xl font-bold text-white mb-2">"Vou precisar de treinamento?"</h4>
                            <p class="text-white/80">
                                Sistema super simples + suporte dedicado via WhatsApp. 
                                <span class="text-green-300 font-semibold">Você nunca fica sem ajuda.</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur rounded-2xl p-6 border-2 border-white/20">
                    <div class="flex items-start">
                        <div class="text-3xl mr-4">💰</div>
                        <div>
                            <h4 class="text-xl font-bold text-white mb-2">"É caro demais?"</h4>
                            <p class="text-white/80">
                                R$ 30/mês por funcionário. Menos que um delivery. 
                                <span class="text-green-300 font-semibold">Se paga só evitando 1 falta/mês.</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur rounded-2xl p-6 border-2 border-white/20">
                    <div class="flex items-start">
                        <div class="text-3xl mr-4">🔒</div>
                        <div>
                            <h4 class="text-xl font-bold text-white mb-2">"Meus dados estão seguros?"</h4>
                            <p class="text-white/80">
                                100%! Criptografia de ponta e servidores no Brasil. 
                                <span class="text-green-300 font-semibold">LGPD compliant.</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-12">
                <div class="bg-gradient-to-r from-pink-900/30 to-purple-900/30 border-2 border-pink-500/50 rounded-2xl p-8 max-w-3xl mx-auto">
                    <h3 class="text-2xl font-bold text-white mb-4">
                        🎯 Ainda em dúvida? Fale com a gente!
                    </h3>
                    <p class="text-white/80 mb-6">
                        Nossa equipe está pronta para te ajudar via WhatsApp
                    </p>
                    <a href="https://wa.me/{{ config('pagby.whatsapp_number') }}" target="_blank"
                       class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-full text-lg font-bold shadow-lg transition">
                        <i class="fab fa-whatsapp text-2xl"></i>
                        Chamar no WhatsApp
                    </a>
                </div>
            </div>
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

        <!-- Seção de Urgência e Prova Social -->
        <div class="fade-in mt-20 max-w-4xl w-full">
            <div class="bg-gradient-to-r from-yellow-900/40 to-orange-900/40 border-2 border-yellow-500/50 rounded-3xl p-8 md:p-12 text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-yellow-300 mb-4">
                    ⚠️ Quanto você está perdendo agora?
                </h2>
                <p class="text-xl text-white/90 mb-6 max-w-2xl mx-auto">
                    Cada dia sem o PagBy significa clientes perdidos, faltas não evitadas e horas desperdiçadas respondendo mensagens
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-red-900/30 rounded-xl p-4">
                        <div class="text-3xl font-bold text-red-300 mb-2">R$ 500+</div>
                        <p class="text-white/80 text-sm">Perdidos por mês com faltas evitáveis</p>
                    </div>
                    <div class="bg-red-900/30 rounded-xl p-4">
                        <div class="text-3xl font-bold text-red-300 mb-2">10h+</div>
                        <p class="text-white/80 text-sm">Desperdiçadas agendando manualmente</p>
                    </div>
                    <div class="bg-red-900/30 rounded-xl p-4">
                        <div class="text-3xl font-bold text-red-300 mb-2">30%</div>
                        <p class="text-white/80 text-sm">Dos clientes desistem pela demora</p>
                    </div>
                </div>
                <div class="bg-green-600/20 border-2 border-green-400 rounded-xl p-6 mb-6">
                    <p class="text-2xl font-bold text-green-200 mb-2">
                        ✅ Teste GRÁTIS por 30 dias
                    </p>
                    <p class="text-white/90">
                        Sem cartão de crédito • Sem compromisso • Cancele quando quiser
                    </p>
                </div>
                <a href="#planos" 
                   class="inline-block bg-gradient-to-r from-green-600 to-emerald-600 text-white px-10 py-5 rounded-full text-xl font-bold shadow-2xl hover:from-green-700 hover:to-emerald-700 transition transform hover:scale-105">
                    Começar teste grátis agora →
                </a>
                <p class="text-white/60 text-sm mt-4">
                    ⚡ Configuração em menos de 5 minutos • Suporte via WhatsApp
                </p>
            </div>
        </div>

        <!-- SEÇÃO DE PLANOS PAGBY -->
        <div id="planos" class="fade-in mt-16 max-w-7xl w-full px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold mb-4 text-white">
                    Comece seu teste grátis de 30 dias
                </h2>
                <p class="text-xl text-white/80 max-w-2xl mx-auto mb-6">
                    Você terá 30 dias grátis para testar todas as funcionalidades sem compromisso!
                </p>
                <div class="bg-green-500/20 border-2 border-green-400 rounded-xl p-4 max-w-2xl mx-auto mb-6">
                    <p class="text-green-200 font-bold text-lg">
                        🎁 Durante o teste grátis você tem acesso a TODAS as funcionalidades premium!
                    </p>
                </div>
                
                <!-- Selector de Funcionários -->
                <div class="inline-flex flex-col items-center bg-white/10 backdrop-blur rounded-2xl p-6 mb-8">
                    <label for="numFuncionarios" class="block font-bold mb-3 text-white text-lg">
                        Quantos funcionários?
                    </label>
                    <input type="number" id="numFuncionarios" name="numFuncionarios" min="1" max="20" value="1" 
                           class="w-24 px-4 py-3 rounded-xl border-2 border-pink-500 text-gray-800 bg-white focus:ring-2 focus:ring-pink-500 focus:border-transparent text-xl font-bold text-center" />
                  
                </div>
                
                <div id="avisoFuncionarios" class="hidden text-yellow-300 text-lg font-semibold mb-4 bg-yellow-900/30 rounded-xl p-4 max-w-2xl mx-auto">
                    <i class="fas fa-info-circle mr-2"></i>
                    Para mais de 20 funcionários, consulte valores pelo WhatsApp 
                    <a href="https://wa.me/{{ config('pagby.whatsapp_number') }}" class="underline text-green-300 hover:text-green-200" target="_blank">
                        {{ config('pagby.whatsapp_display') }}
                    </a>
                </div>
            </div>

            <!-- Grid de Planos -->
            <div class="grid grid-cols-1 gap-6 mb-12 max-w-xl mx-auto">
                <!-- Plano Mensal -->
                <div class="plan-card bg-white rounded-2xl shadow-xl p-6 transition-all duration-300 hover:scale-105 hover:shadow-2xl relative overflow-hidden">
                    <div class="text-center">
                        <!--<h3 class="text-2xl font-bold text-gray-800 mb-2">Mensal</h3>-->
                        <p class="text-gray-600 text-sm mb-6">Pagamento mensal</p>
                        
                        <div class="mb-6">
                            
                            <div class="text-5xl font-bold text-pink-600" data-plan="mensal">
                                R$ <span class="valor-mensal">30</span>
                            </div>
                            <div class="text-gray-600 text-sm mt-1">/mês</div>
                        </div>
                        
                        <button onclick="selecionarPlano('mensal')" 
                                class="w-full bg-gradient-to-r from-pink-600 to-purple-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:from-pink-700 hover:to-purple-700 transition-all">
                            Começar Teste Grátis
                        </button>
                        <!--
                        <div class="mt-4 text-gray-700 text-sm">
                            <div class="font-semibold mb-2">Pagamento:</div>
                            <div class="valor-total" data-plan="mensal">R$ 30,00 no total</div>
                        </div>
                        -->
                    </div>
                </div>

                

                <!-- Plano Semestral -->
                <!--            
                <div class="plan-card bg-white rounded-2xl shadow-xl p-6 transition-all duration-300 hover:scale-105 hover:shadow-2xl relative overflow-hidden">
                    <div class="absolute top-4 right-4 bg-green-600 text-white text-xs font-bold px-3 py-1 rounded-full">
                        10% OFF
                    </div>
                    <div class="text-center">
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Semestral</h3>
                        <p class="text-gray-600 text-sm mb-6">6 meses</p>
                        
                        <div class="mb-6">
                            <div class="text-gray-500 text-sm mb-1">Equivalente a</div>
                            <div class="text-5xl font-bold text-pink-600" data-plan="semestral">
                                R$ <span class="valor-mensal">27</span>
                            </div>
                            <div class="text-gray-600 text-sm mt-1">/mês</div>
                        </div>
                        
                        <button onclick="selecionarPlano('semestral')" 
                                class="w-full bg-gradient-to-r from-pink-600 to-purple-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:from-pink-700 hover:to-purple-700 transition-all">
                            Começar Teste Grátis
                        </button>
                        
                        <div class="mt-4 text-gray-700 text-sm">
                            <div class="font-semibold mb-2">Pagamento semestral:</div>
                            <div class="valor-total" data-plan="semestral">R$ 54,00 no total</div>
                        </div>
                    </div>
                </div>
                -->

                <!-- Plano Anual - DESTAQUE 
                <div class="plan-card bg-gradient-to-br from-pink-600 to-purple-700 rounded-2xl shadow-2xl p-6 transition-all duration-300 hover:scale-105 relative overflow-hidden border-4 border-yellow-400">
                    <div class="absolute top-0 left-0 right-0 bg-yellow-400 text-gray-900 text-center text-sm font-bold py-2 flex items-center justify-center gap-2">
                        <i class="fas fa-crown text-gray-900"></i>
                        MELHOR OFERTA
                        <i class="fas fa-crown text-gray-900"></i>
                    </div>
                    <div class="absolute top-12 right-4 bg-yellow-400 text-gray-900 text-xs font-bold px-3 py-1 rounded-full">
                        15% OFF
                    </div>
                    <div class="text-center mt-8">
                        <h3 class="text-2xl font-bold text-white mb-2">Anual</h3>
                        <p class="text-white/90 text-sm mb-6">12 meses</p>
                        
                        <div class="mb-6">
                            <div class="text-white/80 text-sm mb-1">Equivalente a</div>
                            <div class="text-5xl font-bold text-white" data-plan="anual">
                                R$ <span class="valor-mensal">25,50</span>
                            </div>
                            <div class="text-white/90 text-sm mt-1">/mês</div>
                        </div>
                        
                        <button onclick="selecionarPlano('anual')" 
                                class="w-full bg-yellow-400 text-gray-900 px-6 py-3 rounded-xl font-bold shadow-lg hover:bg-yellow-300 transition-all">
                            Começar Teste Grátis
                        </button>
                        
                        <div class="mt-4 text-white text-sm">
                            <div class="font-semibold mb-2">Pagamento anual:</div>
                            <div class="valor-total" data-plan="anual">R$ 306,00 no total</div>
                            <div id="economia-anual" class="text-xs text-yellow-200 mt-2">
                                💰 Economize R$ 126,00 no ano!
                            </div>
                        </div>
                        
                    </div>
                </div>
                -->
            </div>

            <!-- Benefícios -->
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
                <p class="text-white/70 text-sm mt-4">
                    • Cancele quando quiser • Suporte via WhatsApp {{ config('pagby.whatsapp_display') }} • Sem taxas escondidas
                </p>
            </div>
        </div>

        <!-- Seção de Urgência e Prova Social -->
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
        <div id="contato" class="max-w-4xl mx-auto flex flex-col md:flex-row justify-between items-center">
            <div class="flex items-center mb-4 md:mb-0">
                <img src="{{ asset('images/logo.png') }}" alt="Logo PagBy" class="w-24 h-18 mr-3">
                
            </div>
            <div class="flex items-center mb-4 md:mb-0">
                <a href="https://wa.me/{{ config('pagby.whatsapp_number') }}" target="_blank" 
                   class="flex items-center text-white/80 hover:text-white transition mr-6">
                    <i class="fab fa-whatsapp text-2xl mr-2"></i>
                    <span>{{ config('pagby.whatsapp_display') }}</span>
                </a>
                <a href="mailto:{{ config('pagby.contact_email') }}" 
                   class="flex items-center text-white/80 hover:text-white transition">
                    <i class="fas fa-envelope text-2xl mr-2"></i>
                    <span>{{ config('pagby.contact_email') }}</span>
                </a>
                
            </div>
            <div class="text-center md:text-right text-white/70">
                <p>&copy; {{ date('Y') }} PagBy é uma marca registrada de HECO Softwares Ltda. Todos os direitos reservados.</p>
                <p class="text-sm mt-2">Feito com ❤️ para profissionais da beleza</p>
            </div>
        </div>
    </footer>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const valorBase = {{ config('pricing.base_price_per_employee') }};
        const acrescimoFuncionario = 1;
        const descontos = {
            'mensal': 0,
            'semestral': 0.10,
            'anual': 0.15
        };
        const meses = {
            'mensal': 1,
            'semestral': 6,
            'anual': 12
        };

        function calcularPlano(numFuncionarios, periodicidade) {
            let valor = valorBase * (1 + (numFuncionarios - 1) * acrescimoFuncionario);
            let desconto = descontos[periodicidade] || 0;
            let valorFinal = valor * (1 - desconto);
            let total = valorFinal * (meses[periodicidade] || 1);
            let equivalenteMensal = total / (meses[periodicidade] || 1);
            return {
                total: Number(total.toFixed(2)),
                mensal: Number(equivalenteMensal.toFixed(2))
            };
        }

        function atualizarValoresPlanos() {
            const numFuncionariosInput = document.getElementById('numFuncionarios');
            let numFuncionarios = parseInt(numFuncionariosInput.value) || 1;
            const aviso = document.getElementById('avisoFuncionarios');

            // Limita o máximo a 20 funcionários
            if (numFuncionarios > 20) {
                numFuncionariosInput.value = 20;
                numFuncionarios = 20;
                aviso.classList.remove('hidden');
            } else {
                aviso.classList.add('hidden');
            }

            // Atualiza valores para cada plano
            ['mensal', 'semestral', 'anual'].forEach(periodicidade => {
                const valores = calcularPlano(numFuncionarios, periodicidade);
                
                // Atualiza valor mensal
                const valorMensalElement = document.querySelector(`[data-plan="${periodicidade}"] .valor-mensal`);
                if (valorMensalElement) {
                    valorMensalElement.textContent = valores.mensal.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
                
                // Atualiza valor total
                const valorTotalElement = document.querySelector(`.valor-total[data-plan="${periodicidade}"]`);
                if (valorTotalElement) {
                    valorTotalElement.textContent = `R$ ${valores.total.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })} no total`;
                }
            });

            // Calcula economia do plano anual vs mensal
            const valorMensal = calcularPlano(numFuncionarios, 'mensal');
            const valorAnual = calcularPlano(numFuncionarios, 'anual');
            const economia = (valorMensal.mensal * 12) - valorAnual.total;
           
            const economiaElement = document.getElementById('economia-anual');
            if (economiaElement) {
                economiaElement.textContent = `💰 Economize R$ ${economia.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })} no ano!`;
            }
        }

        // Event listeners
        document.getElementById('numFuncionarios').addEventListener('input', atualizarValoresPlanos);
        document.getElementById('numFuncionarios').addEventListener('change', atualizarValoresPlanos);

        // Inicializa valores
        atualizarValoresPlanos();
    });

    // Função para selecionar plano
    function selecionarPlano(periodicidade) {
        const numFuncionarios = parseInt(document.getElementById('numFuncionarios').value) || 1;
        
        // Só permite se até 7 funcionários
        /*if (numFuncionarios > 7) {
            document.getElementById('avisoFuncionarios').classList.remove('hidden');
            document.getElementById('avisoFuncionarios').scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }*/
        
        const url = `/register-tenant?plan=${encodeURIComponent(periodicidade)}&employees=${numFuncionarios}`;
        window.location.href = url;
    }
    
    // Menu Mobile
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const closeMobileMenu = document.getElementById('close-mobile-menu');
    
    if (mobileMenuBtn && mobileMenu && closeMobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.remove('opacity-0', 'pointer-events-none');
            mobileMenu.classList.add('opacity-100');
        });
        
        closeMobileMenu.addEventListener('click', () => {
            mobileMenu.classList.add('opacity-0', 'pointer-events-none');
            mobileMenu.classList.remove('opacity-100');
        });
        
        // Fechar ao clicar nos links
        const mobileLinks = mobileMenu.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('opacity-0', 'pointer-events-none');
                mobileMenu.classList.remove('opacity-100');
            });
        });
    }
</script>
</x-pagby-layout>
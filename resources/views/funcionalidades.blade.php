<x-pagby-layout>
    <header class="py-4 px-6 flex justify-between items-center">
        <div class="flex items-center">
            <a href="/"><img src="{{ asset('images/logo.png') }}" alt="Logo PagBy" class="w-24 h-18 mr-3"></a>
        </div>
        <nav class="hidden md:flex space-x-6">
            <a href="/" class="hover:text-pink-300 transition">Principal</a>
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
            <a href="/#planos" class="hover:text-pink-300 transition">Planos</a>
            <button id="close-mobile-menu" class="mt-8 text-pink-400 text-2xl"><i class="fas fa-times"></i></button>
        </div>
    </header>
    
    <main class="flex-1 flex flex-col items-center bg-gray-900 px-4 py-10">
        
        <!-- Hero Section -->
        <div class="text-center max-w-5xl w-full mb-16">
            <h1 class="text-4xl md:text-6xl font-extrabold mb-6 bg-gradient-to-r from-pink-500 via-purple-500 to-indigo-500 text-transparent bg-clip-text">
                Tudo que seu Salão Precisa em um Só Lugar
            </h1>
            <p class="text-xl md:text-2xl text-white/80 mb-8 max-w-3xl mx-auto">
                Descubra como o PagBy automatiza sua gestão, aumenta seu faturamento e libera seu tempo para focar no que realmente importa: seus clientes
            </p>
            <a href="/#planos" 
               onclick="fbq('track', 'Lead', {content_name: 'Features Hero CTA', content_category: 'Features Page'});"
               class="inline-block bg-gradient-to-r from-pink-600 to-purple-600 text-white px-10 py-4 rounded-full text-lg font-bold shadow-lg hover:from-pink-700 hover:to-purple-700 transition-all transform hover:scale-105">
                🚀 Testar Grátis por 30 Dias
            </a>
            <p class="text-white/60 text-sm mt-4">
                ✨ Sem cartão de crédito • Sem compromisso • Configuração em 5 minutos
            </p>
        </div>

        <!-- Funcionalidade em Destaque: Sistema de Fidelidade -->
        <div class="max-w-6xl w-full mb-16">
            <div class="bg-gradient-to-br from-yellow-900/40 via-orange-900/40 to-red-900/40 border-4 border-yellow-400 rounded-3xl p-8 md:p-12 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-yellow-400/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-orange-400/10 rounded-full blur-3xl"></div>
                
                <div class="relative z-10">
                    <div class="text-center mb-8">
                        <span class="inline-block bg-gradient-to-r from-yellow-400 to-orange-400 text-gray-900 px-6 py-2 rounded-full font-bold text-lg mb-4 animate-pulse">
                            🆕 LANÇAMENTO
                        </span>
                        <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-4">
                            Sistema de Fidelidade Automático
                        </h2>
                        <p class="text-xl text-white/90 max-w-3xl mx-auto mb-6">
                            A funcionalidade que vai <span class="text-yellow-300 font-bold">transformar suas vendas</span> e fazer seus clientes voltarem sempre
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-6 border-2 border-yellow-400/30">
                            <div class="flex items-center mb-4">
                                <div class="text-5xl mr-4">🎁</div>
                                <h3 class="text-2xl font-bold text-yellow-300">Como Funciona?</h3>
                            </div>
                            <ul class="space-y-3 text-white/90">
                                <li class="flex items-start">
                                    <span class="text-green-400 mr-2">✓</span>
                                    Cliente agenda serviço pelo app
                                </li>
                                <li class="flex items-start">
                                    <span class="text-green-400 mr-2">✓</span>
                                    Sistema sugere produtos relacionados automaticamente
                                </li>
                                <li class="flex items-start">
                                    <span class="text-green-400 mr-2">✓</span>
                                    Cliente compra e ganha desconto de 15-25% na hora
                                </li>
                                <li class="flex items-start">
                                    <span class="text-green-400 mr-2">✓</span>
                                    Desconto fica disponível para próxima visita
                                </li>
                                <li class="flex items-start">
                                    <span class="text-green-400 mr-2">✓</span>
                                    <strong>Zero trabalho manual para você!</strong>
                                </li>
                            </ul>
                        </div>

                        <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-6 border-2 border-yellow-400/30">
                            <div class="flex items-center mb-4">
                                <div class="text-5xl mr-4">📈</div>
                                <h3 class="text-2xl font-bold text-yellow-300">Resultados Comprovados</h3>
                            </div>
                            <div class="space-y-4">
                                <div class="bg-green-900/40 rounded-xl p-4">
                                    <div class="text-3xl font-bold text-green-300 mb-1">+35%</div>
                                    <p class="text-white/80">Aumento nas vendas de produtos</p>
                                </div>
                                <div class="bg-blue-900/40 rounded-xl p-4">
                                    <div class="text-3xl font-bold text-blue-300 mb-1">2x</div>
                                    <p class="text-white/80">Clientes retornam mais rápido</p>
                                </div>
                                <div class="bg-purple-900/40 rounded-xl p-4">
                                    <div class="text-3xl font-bold text-purple-300 mb-1">R$ 18.9K</div>
                                    <p class="text-white/80">Extra por ano (salão 5 funcionários)</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center bg-white/5 rounded-xl p-6">
                        <p class="text-white/80 text-sm mb-2">
                            💡 <strong>Exemplo prático:</strong> Um salão com 5 funcionários que vende R$ 4.500 em produtos/mês pode alcançar R$ 6.075/mês com o sistema de fidelidade ativo.
                        </p>
                        <p class="text-green-300 font-bold">
                            Potencial de R$ 18.900 extras por ano!
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grade de Funcionalidades Principais -->
        <div class="max-w-7xl w-full mb-16">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    Funcionalidades Completas para Transformar Seu Negócio
                </h2>
                <p class="text-xl text-white/70">
                    Tudo que você precisa para gerenciar seu salão com excelência
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <!-- Agenda Online 24/7 -->
                <div class="bg-gradient-to-br from-pink-900/30 to-purple-900/30 border-2 border-pink-500/30 rounded-2xl p-8 hover:scale-105 transition-transform">
                    <div class="text-6xl mb-4 text-center">📅</div>
                    <h3 class="text-2xl font-bold text-pink-300 mb-3 text-center">Agenda Online 24/7</h3>
                    <p class="text-white/80 mb-4">
                        Seus clientes agendam sozinhos a qualquer hora. Acabou aquela correria de responder mensagens o dia todo!
                    </p>
                    <div class="bg-pink-500/20 rounded-lg p-3 text-center">
                        <span class="text-pink-200 font-bold">Economize 10h/semana</span>
                    </div>
                </div>

                <!-- Lembretes Automáticos -->
                <div class="bg-gradient-to-br from-blue-900/30 to-cyan-900/30 border-2 border-blue-500/30 rounded-2xl p-8 hover:scale-105 transition-transform">
                    <div class="text-6xl mb-4 text-center">🔔</div>
                    <h3 class="text-2xl font-bold text-blue-300 mb-3 text-center">Lembretes Automáticos</h3>
                    <p class="text-white/80 mb-4">
                        Sistema envia confirmações e lembretes via WhatsApp. Seus clientes nunca mais esquecem o horário marcado.
                    </p>
                    <div class="bg-blue-500/20 rounded-lg p-3 text-center">
                        <span class="text-blue-200 font-bold">Reduza faltas em 70%</span>
                    </div>
                </div>

                <!-- Gestão de Equipe -->
                <div class="bg-gradient-to-br from-purple-900/30 to-indigo-900/30 border-2 border-purple-500/30 rounded-2xl p-8 hover:scale-105 transition-transform">
                    <div class="text-6xl mb-4 text-center">👥</div>
                    <h3 class="text-2xl font-bold text-purple-300 mb-3 text-center">Gestão de Equipe</h3>
                    <p class="text-white/80 mb-4">
                        Organize profissionais, horários e serviços. Cada funcionário acessa sua própria agenda e vê suas metas.
                    </p>
                    <div class="bg-purple-500/20 rounded-lg p-3 text-center">
                        <span class="text-purple-200 font-bold">Controle total centralizado</span>
                    </div>
                </div>

                <!-- Controle Financeiro -->
                <div class="bg-gradient-to-br from-green-900/30 to-emerald-900/30 border-2 border-green-500/30 rounded-2xl p-8 hover:scale-105 transition-transform">
                    <div class="text-6xl mb-4 text-center">💰</div>
                    <h3 class="text-2xl font-bold text-green-300 mb-3 text-center">Controle Financeiro</h3>
                    <p class="text-white/80 mb-4">
                        Acompanhe faturamento diário, mensal e por profissional. Veja exatamente de onde vem cada centavo.
                    </p>
                    <div class="bg-green-500/20 rounded-lg p-3 text-center">
                        <span class="text-green-200 font-bold">Relatórios em tempo real</span>
                    </div>
                </div>

                <!-- Gestão de Comandas -->
                <div class="bg-gradient-to-br from-orange-900/30 to-red-900/30 border-2 border-orange-500/30 rounded-2xl p-8 hover:scale-105 transition-transform">
                    <div class="text-6xl mb-4 text-center">🧾</div>
                    <h3 class="text-2xl font-bold text-orange-300 mb-3 text-center">Gestão de Comandas</h3>
                    <p class="text-white/80 mb-4">
                        Registre serviços e produtos vendidos. Aplique descontos automáticos de fidelidade. Aceite múltiplas formas de pagamento.
                    </p>
                    <div class="bg-orange-500/20 rounded-lg p-3 text-center">
                        <span class="text-orange-200 font-bold">Sem erros no caixa</span>
                    </div>
                </div>

                <!-- Múltiplas Filiais -->
                <div class="bg-gradient-to-br from-indigo-900/30 to-blue-900/30 border-2 border-indigo-500/30 rounded-2xl p-8 hover:scale-105 transition-transform">
                    <div class="text-6xl mb-4 text-center">🏢</div>
                    <h3 class="text-2xl font-bold text-indigo-300 mb-3 text-center">Múltiplas Filiais</h3>
                    <p class="text-white/80 mb-4">
                        Tem mais de um salão? Gerencie todas as unidades em um só lugar. Compare desempenho e unifique a gestão.
                    </p>
                    <div class="bg-indigo-500/20 rounded-lg p-3 text-center">
                        <span class="text-indigo-200 font-bold">Visão consolidada</span>
                    </div>
                </div>

                <!-- Cadastro de Clientes -->
                <div class="bg-gradient-to-br from-pink-900/30 to-red-900/30 border-2 border-pink-500/30 rounded-2xl p-8 hover:scale-105 transition-transform">
                    <div class="text-6xl mb-4 text-center">📝</div>
                    <h3 class="text-2xl font-bold text-pink-300 mb-3 text-center">Cadastro de Clientes</h3>
                    <p class="text-white/80 mb-4">
                        Histórico completo de cada cliente: serviços feitos, produtos comprados, preferências e observações importantes.
                    </p>
                    <div class="bg-pink-500/20 rounded-lg p-3 text-center">
                        <span class="text-pink-200 font-bold">Atendimento personalizado</span>
                    </div>
                </div>

                <!-- Relatórios Inteligentes -->
                <div class="bg-gradient-to-br from-cyan-900/30 to-teal-900/30 border-2 border-cyan-500/30 rounded-2xl p-8 hover:scale-105 transition-transform">
                    <div class="text-6xl mb-4 text-center">📊</div>
                    <h3 class="text-2xl font-bold text-cyan-300 mb-3 text-center">Relatórios Inteligentes</h3>
                    <p class="text-white/80 mb-4">
                        Dashboards com gráficos de faturamento, serviços mais vendidos, horários de pico e performance da equipe.
                    </p>
                    <div class="bg-cyan-500/20 rounded-lg p-3 text-center">
                        <span class="text-cyan-200 font-bold">Decisões baseadas em dados</span>
                    </div>
                </div>

                <!-- App para Clientes -->
                <div class="bg-gradient-to-br from-purple-900/30 to-pink-900/30 border-2 border-purple-500/30 rounded-2xl p-8 hover:scale-105 transition-transform">
                    <div class="text-6xl mb-4 text-center">📱</div>
                    <h3 class="text-2xl font-bold text-purple-300 mb-3 text-center">App para Clientes</h3>
                    <p class="text-white/80 mb-4">
                        Seus clientes acessam pelo celular, veem horários disponíveis, agendam e recebem notificações automáticas.
                    </p>
                    <div class="bg-purple-500/20 rounded-lg p-3 text-center">
                        <span class="text-purple-200 font-bold">Experiência moderna</span>
                    </div>
                </div>

                <!-- Preços Diferenciados por Filial -->
                <div class="bg-gradient-to-br from-yellow-900/30 to-orange-900/30 border-2 border-yellow-500/30 rounded-2xl p-8 hover:scale-105 transition-transform">
                    <div class="text-6xl mb-4 text-center">💵</div>
                    <h3 class="text-2xl font-bold text-yellow-300 mb-3 text-center">Preços por Filial</h3>
                    <p class="text-white/80 mb-4">
                        Configure preços diferentes para cada filial. Adapte valores de acordo com a localização e perfil de cada unidade.
                    </p>
                    <div class="bg-yellow-500/20 rounded-lg p-3 text-center">
                        <span class="text-yellow-200 font-bold">Flexibilidade total</span>
                    </div>
                </div>

                <!-- Customização da Home Page -->
                <div class="bg-gradient-to-br from-lime-900/30 to-green-900/30 border-2 border-lime-500/30 rounded-2xl p-8 hover:scale-105 transition-transform">
                    <div class="text-6xl mb-4 text-center">🎨</div>
                    <h3 class="text-2xl font-bold text-lime-300 mb-3 text-center">Personalize sua Página</h3>
                    <p class="text-white/80 mb-4">
                        Customize a aparência da sua página de agendamentos com logo, cores e informações do seu salão. Fácil e rápido!
                    </p>
                    <div class="bg-lime-500/20 rounded-lg p-3 text-center">
                        <span class="text-lime-200 font-bold">Sua identidade visual</span>
                    </div>
                </div>

                <!-- Planos de Assinatura -->
                <div class="bg-gradient-to-br from-violet-900/30 to-fuchsia-900/30 border-2 border-violet-500/30 rounded-2xl p-8 hover:scale-105 transition-transform">
                    <div class="text-6xl mb-4 text-center">🎫</div>
                    <h3 class="text-2xl font-bold text-violet-300 mb-3 text-center">Planos de Assinatura</h3>
                    <p class="text-white/80 mb-4">
                        Ofereça assinaturas mensais de serviços aos seus clientes. Receita recorrente garantida e clientes fidelizados.
                    </p>
                    <div class="bg-violet-500/20 rounded-lg p-3 text-center">
                        <span class="text-violet-200 font-bold">Renda previsível</span>
                    </div>
                </div>

            </div>
        </div>

        <!-- Seção: Como Funciona -->
        <div class="max-w-6xl w-full mb-16">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    Como Funciona na Prática?
                </h2>
                <p class="text-xl text-white/70">
                    Implementação rápida e intuitiva em 3 passos simples
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-pink-600 to-purple-600 flex items-center justify-center text-white text-3xl font-bold mx-auto mb-6 shadow-lg">
                        1
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3">Cadastre seu Salão</h3>
                    <p class="text-white/80">
                        Em menos de 5 minutos você adiciona seus profissionais, serviços e horários de funcionamento. Super simples!
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-600 to-cyan-600 flex items-center justify-center text-white text-3xl font-bold mx-auto mb-6 shadow-lg">
                        2
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3">Convide seus Clientes</h3>
                    <p class="text-white/80">
                        Compartilhe o link da sua agenda online. Seus clientes começam a agendar imediatamente pelo celular.
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-green-600 to-emerald-600 flex items-center justify-center text-white text-3xl font-bold mx-auto mb-6 shadow-lg">
                        3
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3">Relaxe e Fature Mais</h3>
                    <p class="text-white/80">
                        Sistema trabalha automaticamente: agenda, confirma, lembra e fideliza seus clientes. Você só atende!
                    </p>
                </div>
            </div>

            <div class="text-center mt-12">
                <a href="/#planos" 
                   onclick="fbq('track', 'Lead', {content_name: 'How It Works CTA', content_category: 'Features Page'});"
                   class="inline-block bg-gradient-to-r from-green-600 to-emerald-600 text-white px-10 py-5 rounded-full text-xl font-bold shadow-lg hover:from-green-700 hover:to-emerald-700 transition-all transform hover:scale-105">
                    Começar Agora - Grátis por 30 Dias →
                </a>
            </div>
        </div>

        <!-- Comparação: Sem PagBy vs Com PagBy -->
        <div class="max-w-6xl w-full mb-16">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    Veja a Diferença na Prática
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- SEM PAGBY -->
                <div class="bg-red-900/20 border-2 border-red-500/50 rounded-2xl p-8">
                    <div class="text-center mb-6">
                        <span class="inline-block bg-red-600 text-white px-6 py-2 rounded-full font-bold text-lg mb-4">
                            ❌ SEM O PAGBY
                        </span>
                    </div>
                    <ul class="space-y-4 text-white/90">
                        <li class="flex items-start">
                            <span class="text-red-400 mr-3 text-xl">✗</span>
                            <span>Horas respondendo mensagens no WhatsApp</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-red-400 mr-3 text-xl">✗</span>
                            <span>Clientes esquecem horários e não aparecem</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-red-400 mr-3 text-xl">✗</span>
                            <span>Agenda desorganizada em papel ou Excel</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-red-400 mr-3 text-xl">✗</span>
                            <span>Não sabe quanto faturou hoje ou ontem</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-red-400 mr-3 text-xl">✗</span>
                            <span>Produtos parados na prateleira sem vender</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-red-400 mr-3 text-xl">✗</span>
                            <span>Clientes demoram meses para voltar</span>
                        </li>
                    </ul>
                    <div class="mt-6 text-center p-4 bg-red-600/30 rounded-lg">
                        <p class="text-red-200 font-bold">Resultado: Stress e dinheiro perdido todo dia</p>
                    </div>
                </div>

                <!-- COM PAGBY -->
                <div class="bg-green-900/20 border-2 border-green-500/50 rounded-2xl p-8">
                    <div class="text-center mb-6">
                        <span class="inline-block bg-green-600 text-white px-6 py-2 rounded-full font-bold text-lg mb-4">
                            ✅ COM O PAGBY
                        </span>
                    </div>
                    <ul class="space-y-4 text-white/90">
                        <li class="flex items-start">
                            <span class="text-green-400 mr-3 text-xl">✓</span>
                            <span>Clientes agendam sozinhos 24/7 online</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-400 mr-3 text-xl">✓</span>
                            <span>Lembretes automáticos reduzem faltas em 70%</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-400 mr-3 text-xl">✓</span>
                            <span>Agenda digital sempre atualizada e sincronizada</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-400 mr-3 text-xl">✓</span>
                            <span>Relatórios financeiros em tempo real</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-400 mr-3 text-xl">✓</span>
                            <span>Sistema de fidelidade aumenta vendas em 35%</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-400 mr-3 text-xl">✓</span>
                            <span>Clientes voltam 2x mais rápido com descontos</span>
                        </li>
                    </ul>
                    <div class="mt-6 text-center p-4 bg-green-600/30 rounded-lg">
                        <p class="text-green-200 font-bold">Resultado: Mais tempo, mais lucro, menos stress</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Final -->
        <div class="max-w-5xl w-full mb-16">
            <div class="bg-gradient-to-r from-pink-900/40 to-purple-900/40 border-2 border-pink-500/50 rounded-3xl p-8 md:p-12 text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    Pronto para Transformar seu Salão?
                </h2>
                <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
                    Teste todas as funcionalidades profissionais sem compromisso e veja como o PagBy pode aumentar seu faturamento e reduzir o stress
                </p>
                
                <div class="bg-green-600/20 border-2 border-green-400 rounded-xl p-6 mb-6 max-w-2xl mx-auto">
                    <p class="text-2xl font-bold text-green-200 mb-2">
                        🎁 30 Dias Grátis para Testar
                    </p>
                    <p class="text-white/90">
                        Todas as funcionalidades • Sem cartão de crédito • Cancele quando quiser
                    </p>
                </div>

                <a href="/#planos" 
                   onclick="fbq('track', 'Lead', {content_name: 'Final CTA', content_category: 'Features Page'});"
                   class="inline-block bg-gradient-to-r from-pink-600 to-purple-600 text-white px-12 py-5 rounded-full text-xl font-bold shadow-2xl hover:from-pink-700 hover:to-purple-700 transition-all transform hover:scale-105 mb-4">
                    🚀 Começar Meu Teste Grátis Agora
                </a>
                
                <p class="text-white/60 text-sm">
                    ⚡ Configuração em 5 minutos • Suporte via WhatsApp incluído
                </p>
            </div>
        </div>

    </main>

    <script>
    // Rastreamento de visualização de seções importantes com Meta Pixel
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof fbq !== 'undefined') {
            // Rastreamento de seções importantes
            const sectionObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !entry.target.dataset.tracked) {
                        const sectionName = entry.target.dataset.trackName;
                        const eventType = entry.target.dataset.trackEvent;
                        
                        fbq('track', eventType, {
                            content_name: sectionName,
                            content_category: 'Features Page'
                        });
                        
                        entry.target.dataset.tracked = 'true';
                    }
                });
            }, { threshold: 0.5 });
            
            // Rastrear seção de fidelidade
            const fidelidadeSection = document.querySelector('.bg-gradient-to-br.from-yellow-900');
            if (fidelidadeSection) {
                fidelidadeSection.dataset.trackName = 'Fidelity Features Detailed';
                fidelidadeSection.dataset.trackEvent = 'ViewContent';
                sectionObserver.observe(fidelidadeSection);
            }
            
            // Rastrear grid de funcionalidades
            const functionalitiesGrid = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-3.gap-8');
            if (functionalitiesGrid) {
                functionalitiesGrid.dataset.trackName = 'Features Grid';
                functionalitiesGrid.dataset.trackEvent = 'ViewContent';
                sectionObserver.observe(functionalitiesGrid);
            }
            
            // Rastrear seção "Como Funciona"
            const howItWorksSection = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-3.gap-8');
            if (howItWorksSection && howItWorksSection.querySelector('.w-20.h-20.rounded-full')) {
                howItWorksSection.dataset.trackName = 'How It Works Section';
                howItWorksSection.dataset.trackEvent = 'ViewContent';
                sectionObserver.observe(howItWorksSection);
            }
            
            // Rastrear comparação SEM vs COM
            const comparisonSection = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-2.gap-8');
            if (comparisonSection && comparisonSection.querySelector('.bg-red-900\\/20')) {
                comparisonSection.dataset.trackName = 'Before After Comparison';
                comparisonSection.dataset.trackEvent = 'ViewContent';
                sectionObserver.observe(comparisonSection);
            }
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
    });
    </script>

</x-pagby-layout>
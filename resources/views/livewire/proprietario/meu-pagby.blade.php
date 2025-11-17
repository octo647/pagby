<div>
    {{-- Este livewire mostra o plano Pagby do proprietário --}}
    @if (session()->has('mensagem'))
        <div class="alerta-sucesso mb-4">
            {{ session('mensagem') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alerta-aviso mb-4">
            {{ session('error') }}  
            <button class="fechar-alerta" onclick="this.parentElement.style.display='none';">Fechar</button>
        </div>
    @endif
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-2xl font-semibold mb-4">Meu Pagby</h2>
        <div class="mb-4">
            <p class="text-gray-700 mb-2">Seu plano atual: <strong>{{ $planoAtual ? $planoAtual : 'Nenhum plano ativo' }}</strong></p>
            <p class="text-gray-700 mb-2">Status da assinatura: 
                @if($statusPagamento === 'authorized')
                    <span class="text-green-600 font-bold">Ativo</span>
                @elseif($statusPagamento === 'past_due')
                    <span class="text-yellow-600 font-bold">Pagamento Atrasado</span>
                @elseif($statusPagamento === 'canceled')
                    <span class="text-red-600 font-bold">Cancelado</span>   
                @else
                    <span class="text-gray-600 font-bold">Nenhum pagamento registrado</span>
                @endif
            </p>
            <p class="text-gray-700">Próximo vencimento: <strong>
                @if($proximoVencimento)
                    {{ \Carbon\Carbon::parse($proximoVencimento)->format('d/m/Y') }}
                @else
                    N/A
                @endif
            </strong></p>
        </div>
        <div class="mb-4">
            @if($planoAtual && $statusPagamento === 'authorized')
                <button wire:click="cancelarAssinatura" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                    Cancelar Assinatura
                </button>
            @endif

            @if(!$planoAtual || $statusPagamento !== 'authorized')
                <a href="{{ route('pagby-subscription.choose-plan',['plan' => 'basico']) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Ver Planos e Assinar
                </a>
            @endif
        </div>
        <!-- SEÇÃO DE PLANOS PAGBY -->
        <div id="planos" class="fade-in mt-16 max-w-6xl w-full">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4 ">
                    Planos PagBy
                </h2>
                <p class="text-xl max-w-2xl mx-auto">
                    Planos flexíveis para impulsionar seu negócio
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <!-- Plano Básico -->
                <div class="bg-white rounded-2xl shadow-xl p-8 border-2 border-purple-500 hover:border-4 transition-all duration-300 transform hover:scale-105">
                @if($planoAtual === 'basico')
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                    
                        <span class="bg-gradient-to-r from-pink-500 to-purple-500 text-white px-4 py-1 rounded-full text-sm font-semibold">
                            Seu Plano Atual
                        </span>
                   
                    </div>
                @endif
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
                @if($planoAtual === 'basico')
                    <button  
                            class="w-full bg-gray-400 text-white py-3 rounded-lg font-semibold  text-center">
                        Cancelar Assinatura
                    </button>
                @else
                <a href="{{ route('pagby-subscription.choose-plan', ['plan' => 'basico']) }}" 
                class="block w-full bg-purple-600 text-white py-3 rounded-lg font-semibold hover:bg-purple-700 transition-colors text-center">
                    Escolher Básico
                </a>
                @endif
                </div>

                <!-- Plano Premium -->
                <div class="bg-white rounded-2xl shadow-xl p-8 border-2 border-pink-500 relative transform hover:scale-105 hover:border-4 transition-all duration-300">
                @if($planoAtual === 'premium')
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                    
                        <span class="bg-gradient-to-r from-pink-500 to-purple-500 text-white px-4 py-1 rounded-full text-sm font-semibold">
                            Seu Plano Atual
                        </span>
                   
                    </div>
                @endif

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

            
        </div>

</div>
<x-app-layout>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto text-center">
        <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-8 rounded-lg mb-8">
            <svg class="mx-auto h-16 w-16 text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            
            <h1 class="text-3xl font-bold mb-4">Acesso Suspenso</h1>
            
            @if($tenant->isTrialExpired())
                <p class="text-lg mb-4">
                    Seu período de teste de 30 dias expirou em <strong>{{ $tenant->trial_ends_at->format('d/m/Y H:i') }}</strong>.
                </p>
                <p class="mb-6">
                    Para continuar usando nossa plataforma, escolha um plano pago abaixo.
                </p>
            @elseif($tenant->isSubscriptionExpired())
                <p class="text-lg mb-4">
                    Sua assinatura do plano <strong>{{ $tenant->current_plan }}</strong> expirou em <strong>{{ $tenant->subscription_ends_at->format('d/m/Y H:i') }}</strong>.
                </p>
                <p class="mb-6">
                    Para reativar seu acesso, renove sua assinatura escolhendo um plano abaixo.
                </p>
            @else
                <p class="text-lg mb-6">
                    Seu acesso está temporariamente suspenso. Entre em contato conosco ou escolha um plano para reativar.
                </p>
            @endif
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
                <form action="{{ route('pagby-subscription.select-plan') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan" value="basico">
                    <input type="hidden" name="tenant_id" value="{{ $tenant->id }}">
                    <button type="submit" 
                                class="block w-full bg-purple-600 text-white py-3 rounded-lg font-semibold hover:bg-purple-700 transition-colors">
                            Escolher Básico
                </button>
                </form>
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
                <form action="{{ route('pagby-subscription.select-plan') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan" value="premium">
                    <input type="hidden" name="tenant_id" value="{{ $tenant->id }}">
                    <button type="submit" 
                                class="block w-full bg-purple-600 text-white py-3 rounded-lg font-semibold hover:bg-purple-700 transition-colors">
                            Escolher Premium
                </button>
                </form>
                    
                </div>
            </div>

            
        </div>

        <div class="text-center mt-12">
            <p class="text-gray-600 mb-4">Precisa de ajuda?</p>
            <a href="mailto:suporte@pagby.com.br" class="text-blue-500 hover:text-blue-600 font-semibold">
                Envie-nos um email
            </a>
            <br>
            <a href="https://wa.me/5532987007302" class="text-green-500 hover:text-green-600 font-semibold">
                Ou ligue WhatsApp (32) 98700-7302
            </a>
        </div>
    </div>
</div>
</div>
</x-app-layout>


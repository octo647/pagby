{{-- resources/views/registration-success.blade.php --}}

<x-pagby-layout>
    <div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg w-full bg-white p-8 rounded-xl shadow-xl">
            <div class="text-center">
                <!-- Ícone de sucesso -->
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6">
                    <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                <h1 class="text-3xl font-bold text-gray-900 mb-4">🎉 Registro Realizado!</h1>
                
                
                <!-- Mostrar plano selecionado se existir -->
                @if(isset($selected_plan) && $selected_plan === 'trial')
                <div class="bg-gradient-to-r from-pink-500 to-indigo-500 text-white rounded-lg p-4 mb-6 shadow">
                    <h3 class="text-lg font-semibold mb-2">
                        🎉 Bem-vindo ao seu <span class="underline">período de teste grátis de 7 dias</span>!
                    </h3>
                    <p class="text-white/90 text-sm">
                        Aproveite todas as funcionalidades do PagBy sem compromisso. Ao final do período, você poderá escolher o melhor plano para seu salão e continuar usando a plataforma normalmente.
                    </p>
                </div>
                @elseif(isset($selected_plan))
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-semibold text-purple-800 mb-2">
                        📋 Plano Selecionado: <span class="capitalize">{{ $selected_plan }}</span>
                    </h3>
                    <p class="text-purple-600 text-sm">
                        Você escolheu o plano {{ $selected_plan === 'basico' ? 'Básico' : 'Premium' }}. 
                        Agora vamos finalizar sua assinatura!
                    </p>
                </div>
                @endif

                <div class="text-gray-600 mb-8">
                    <p class="mb-4">✅ Seu negócio foi registrado com sucesso em nossa plataforma!</p>
                    <p class="mb-4">📧 Você receberá um email de confirmação em breve.</p>
                    
                    @if(isset($selected_plan) && $selected_plan === 'trial')
                        <p class="text-sm font-medium text-gray-700">
                            ⏳ Você tem 7 dias para testar a plataforma. Antes do fim do período, avisaremos para que possa escolher um plano e continuar usando o PagBy sem interrupções.
                        </p>
                    @elseif(isset($selected_plan))
                        <p class="text-sm font-medium text-gray-700">
                            🚀 Próximo passo: Finalizar assinatura do plano {{ ucfirst($selected_plan) }}
                        </p>
                    @else
                        <p class="text-sm">🏢 Nossa equipe entrará em contato para configurar seu sistema.</p>
                    @endif
                </div>

                <!-- Próximos passos -->
                @if(isset($selected_plan) && $selected_plan === 'trial')
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h4 class="text-blue-800 font-semibold mb-3">🔄 Próximos Passos:</h4>
                    <ol class="text-blue-700 text-sm text-left space-y-1">
                        <li><strong>1.</strong> Ativação automática do sistema</li>
                        <li><strong>2.</strong> Receber dados de acesso por email</li>
                        <li><strong>3.</strong> Aproveitar 7 dias grátis de todas as funcionalidades</li>
                        <li><strong>4.</strong> Antes do fim do período, escolher um plano para continuar usando o PagBy</li>
                    </ol>
                </div>
                @elseif(isset($selected_plan))
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h4 class="text-blue-800 font-semibold mb-3">🔄 Próximos Passos:</h4>
                    <ol class="text-blue-700 text-sm text-left space-y-1">
                        <li><strong>1.</strong> Finalizar pagamento da assinatura</li>
                        <li><strong>2.</strong> Ativação automática do sistema</li>
                        <li><strong>3.</strong> Receber dados de acesso por email</li>
                        <li><strong>4.</strong> Começar a usar o PagBy!</li>
                    </ol>
                </div>
                @endif
                
                <!-- Botões de ação -->
                <div class="space-y-3">
                    @if(isset($selected_plan) && $selected_plan === 'trial')
                        <a href="{{ route('home') }}" 
                           class="w-full bg-gradient-to-r from-pink-600 to-indigo-600 text-white font-bold py-4 px-6 rounded-lg hover:from-pink-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 block text-center transition-all">
                            <i class="fas fa-home mr-2"></i>
                            Ir para o Início
                        </a>
                    @elseif(isset($selected_plan))
                        <!-- Botão para finalizar assinatura -->
                        <form action="{{ route('pagby-subscription.create') }}" method="POST">
                            @csrf
                            <input type="hidden" name="plan" value="{{ $selected_plan }}">
                            <input type="hidden" name="tenant_id" value="temp-{{ $contact_id ?? time() }}">
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold py-4 px-6 rounded-lg hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200">
                                <i class="fas fa-credit-card mr-2"></i>
                                Finalizar Assinatura {{ ucfirst($selected_plan) }}
                            </button>
                        </form>
                        <a href="{{ route('home') }}" 
                           class="w-full bg-gray-200 text-gray-800 font-bold py-3 px-4 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50 block text-center transition-colors">
                            ← Voltar ao Início
                        </a>
                    @else
                        <!-- Se não tem plano, mostrar opções -->
                        <a href="{{ route('home') }}#planos" 
                           class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold py-4 px-6 rounded-lg hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 block text-center transition-all">
                            <i class="fas fa-star mr-2"></i>
                            Escolher Plano de Assinatura
                        </a>
                        <a href="{{ route('home') }}" 
                           class="w-full bg-gray-200 text-gray-800 font-bold py-3 px-4 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50 block text-center transition-colors">
                            Voltar ao Início
                        </a>
                    @endif
                </div>

                <!-- Resumo do registro -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">📄 Resumo do Registro:</h4>
                    <div class="text-sm text-gray-600 space-y-1">
                        @if(isset($contact_id))
                            <p><strong>ID do Registro:</strong> #{{ $contact_id }}</p>
                        @endif
                        @if(isset($selected_plan))
                            <p><strong>Plano:</strong> {{ ucfirst($selected_plan) }}</p>
                        @endif
                        <p><strong>Data:</strong> {{ now()->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                <!-- Informações de contato -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-500 mb-2">❓ Precisa de ajuda?</p>
                    <div class="text-sm text-gray-600">
                        <p><a href="mailto:suporte@pagby.com.br" class="text-blue-500">📧 Email: suporte@pagby.com.br</a></p>
                        <p><a href="https://wa.me/5532987007302" class="text-blue-500">📱 WhatsApp: (32) 98700-7302</a></p>
                    </div>
                </div>

                <!-- Badge de segurança -->
                <div class="mt-6 flex items-center justify-center space-x-4 text-xs text-gray-500">
                    <div class="flex items-center">
                        <i class="fas fa-shield-alt mr-1 text-green-500"></i>
                        <span>Dados Protegidos</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-lock mr-1 text-blue-500"></i>
                        <span>Conexão Segura</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-pagby-layout>
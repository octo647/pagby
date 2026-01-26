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
               
                               

                <div class="text-gray-600 mb-8">
                    <p class="mb-4">✅ Seu negócio foi registrado com sucesso em nossa plataforma!</p>       
                    
                    
                </div>

                <!-- Próximos passos -->
                @if(isset($selected_plan) && $selected_plan === 'trial')
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h4 class="text-blue-800 font-semibold mb-3">🔄 Próximos Passos:</h4>
                    <ol class="text-blue-700 text-sm text-left space-y-1">
                        <li><strong>1.</strong> Assinar o plano PagBy</li>

                        <li><strong>2.</strong> Configuração do sistema</li>
                        <li><strong>3.</strong> Aproveitar 30 dias grátis de todas as funcionalidades</li>
                        
                    </ol>
                </div>
                @elseif(isset($selected_plan))
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h4 class="text-blue-800 font-semibold mb-3">🔄 Próximos Passos:</h4>
                    <ol class="text-blue-700 text-sm text-left space-y-1">
                        <li><strong>1.</strong> Finalizar pagamento da assinatura</li>
                        <li><strong>2.</strong> Ativação do sistema </li>
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
                        <!-- Botão para ir para pagamento -->
                        <a href="{{ route('registration-finalize') }}"
                           class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold py-4 px-6 rounded-lg hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200">
                            <i class="fas fa-credit-card mr-2"></i>
                            Realizar Pagamento
                        </a>
                    @else
                        <!-- Se não tem plano, mostrar opções -->
                        <a href="{{ route('home') }}#planos" 
                           class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold py-4 px-6 rounded-lg hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 block text-center transition-all">
                            <i class="fas fa-star mr-2"></i>
                            Escolher Plano de Assinatura
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
                        <p><a href="mailto:suportepagby@gmail.com" class="text-blue-500">📧 Email: suportepagby@gmail.com</a></p>
                        <p><a href="https://wa.me/{{ config('pagby.whatsapp_number') }}" class="text-blue-500">📱 WhatsApp: {{ config('pagby.whatsapp_display') }}</a></p>
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
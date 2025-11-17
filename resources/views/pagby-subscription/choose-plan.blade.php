{{-- resources/views/pagby-subscription/choose-plan.blade.php --}}

<x-pagby-layout>
    <div class="flex-1 flex flex-col items-center justify-center text-center px-4 py-10">
        <div class="max-w-3xl mx-auto">
            <!-- Plano Selecionado -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                <div class="mb-6">
                    <h1 class="text-4xl font-bold mb-2 text-gray-800">
                        Plano {{ ucfirst($plan) }}
                    </h1>
                    <p class="text-gray-600">Você escolheu o melhor para seu negócio!</p>
                </div>
                
                <div class="text-center mb-8">
                    <div class="text-6xl font-bold text-purple-600 mb-2">
                        R$ {{ number_format($planData['price'], 2, ',', '.') }}
                        <span class="text-xl text-gray-500 font-normal">/mês</span>
                    </div>
                    <div class="text-xl font-bold text-purple-600 mb-2">
                        (por profissional)                       
                    </div>
                    <p class="text-lg text-gray-600">{{ $planData['description'] }}</p>
                </div>

                <!-- Benefícios do plano -->
                <div class="bg-gray-50 rounded-xl p-6 mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">
                        ✨ O que está incluído:
                    </h3>
                    <div class="grid md:grid-cols-2 gap-4 text-left">
                        @if($plan === 'basico')
                            <div class="space-y-2">
                                <div class="flex items-center text-green-600">
                                    <i class="fas fa-check mr-2"></i>
                                    <span>Agendamentos online ilimitados</span>
                                </div>                                
                                <div class="flex items-center text-green-600">
                                    <i class="fas fa-check mr-2"></i>
                                    <span>Pagamentos via PIX e cartão</span>
                                </div>
                                <div class="flex items-center text-green-600">
                                    <i class="fas fa-check mr-2"></i>
                                    <span>Gestão de clientes completa</span>
                                </div>
                                <div class="flex items-center text-green-600">
                                    <i class="fas fa-check mr-2"></i>
                                    <span>Suporte a planos de assinatura</span>
                                </div>
                                <div class="flex items-center text-green-600">
                                    <i class="fas fa-check mr-2"></i>
                                    <span>Suporte via email</span>
                                </div>
                            </div>
                        @else
                            <div class="space-y-2">
                                <div class="flex items-center text-green-600">
                                    <i class="fas fa-check mr-2"></i>
                                    <span>Todos os recursos do Básico</span>
                                </div>
                                <div class="flex items-center text-green-600">
                                    <i class="fas fa-check mr-2"></i>
                                    <span>Relatórios avançados e analytics</span>
                                </div>
                                <div class="flex items-center text-green-600">
                                    <i class="fas fa-check mr-2"></i>
                                    <span>Múltiplas filiais</span>
                                </div>
                                <div class="flex items-center text-green-600">
                                    <i class="fas fa-check mr-2"></i>
                                    <span>Integração com WhatsApp</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-center text-green-600">
                                    <i class="fas fa-check mr-2"></i>
                                    <span>Suporte prioritário 24/7</span>
                                </div>
                                <div class="flex items-center text-green-600">
                                    <i class="fas fa-check mr-2"></i>
                                    <span>API para integrações</span>
                                </div>
                                <div class="flex items-center text-green-600">
                                    <i class="fas fa-check mr-2"></i>
                                    <span>Backup automático</span>
                                </div>
                                <div class="flex items-center text-green-600">
                                    <i class="fas fa-check mr-2"></i>
                                    <span>Domínio personalizado</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Processo de assinatura -->
                <div class="bg-blue-50 rounded-lg p-6 mb-8">
                    <h3 class="text-lg font-semibold text-blue-800 mb-4">
                        🚀 Próximos passos:
                    </h3>
                    <div class="grid md:grid-cols-3 gap-4 text-blue-700">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-blue-200 rounded-full flex items-center justify-center mx-auto mb-2">
                                <span class="font-bold">1</span>
                            </div>
                            <p class="text-sm">Criar sua conta</p>
                        </div>
                        <div class="text-center">
                            <div class="w-12 h-12 bg-blue-200 rounded-full flex items-center justify-center mx-auto mb-2">
                                <span class="font-bold">2</span>
                            </div>
                            <p class="text-sm">Registrar seu negócio</p>
                        </div>
                        <div class="text-center">
                            <div class="w-12 h-12 bg-blue-200 rounded-full flex items-center justify-center mx-auto mb-2">
                                <span class="font-bold">3</span>
                            </div>
                            <p class="text-sm">Confirmar pagamento</p>
                        </div>
                    </div>
                </div>

                <!-- Botões de ação -->
                <div class="flex flex-col gap-4">
                    <a href="{{ route('register-tenant', ['plan' => $plan]) }}" 
                       class="bg-gradient-to-r from-purple-600 to-pink-600 text-white py-4 px-8 rounded-lg font-bold text-lg hover:from-purple-700 hover:to-pink-700 transition-all transform hover:scale-105">
                        🚀 Continuar com {{ ucfirst($plan) }}
                    </a>
                    
                    <a href="{{ route('home') }}#planos" 
                       class="text-gray-600 hover:text-gray-800 transition-colors">
                        ← Voltar aos planos
                    </a>
                </div>
            </div>

            <!-- Garantia e Segurança -->
            <div class="grid md:grid-cols-3 gap-6 text-white">
                <div class="bg-gray-800/50 rounded-xl p-6 text-center">
                    <i class="fas fa-shield-alt text-2xl text-green-400 mb-3"></i>
                    <h4 class="font-bold mb-2">Segurança Total</h4>
                    <p class="text-sm text-gray-300">Seus dados protegidos com criptografia de ponta</p>
                </div>
                <div class="bg-gray-800/50 rounded-xl p-6 text-center">
                    <i class="fas fa-undo text-2xl text-blue-400 mb-3"></i>
                    <h4 class="font-bold mb-2">7 Dias Grátis</h4>
                    <p class="text-sm text-gray-300">Teste sem compromisso por uma semana</p>
                </div>
                <div class="bg-gray-800/50 rounded-xl p-6 text-center">
                    <i class="fas fa-headset text-2xl text-purple-400 mb-3"></i>
                    <h4 class="font-bold mb-2">Suporte Dedicado</h4>
                    <p class="text-sm text-gray-300">Equipe especializada para te ajudar</p>
                </div>
            </div>
        </div>
    </div>
</x-pagby-layout>
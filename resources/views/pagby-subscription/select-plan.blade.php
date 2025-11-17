{{-- resources/views/pagby-subscription/select-plan.blade.php --}}

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


                <!-- Botões de ação -->
                <div class="flex flex-col gap-4">
                    
                    <form action="{{ route('tenant.renew') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan" value="{{ $plan }}">
                        <input type="hidden" name="tenant_id" value="{{ tenant()->id }}">
                        
                        <button type="submit" 
                                class="w-full bg-purple-600 text-white py-3 rounded-lg font-semibold hover:bg-purple-700 transition-colors">
                            🚀 Confirmar pagamento
                        </button>
                    </form>


                    
                    <a href="{{ route('home') }}#planos" 
                       class="text-gray-600 hover:text-gray-800 transition-colors">
                        ← Voltar aos planos
                    </a>
                </div>
            </div>

            <!-- Garantia e Segurança -->
            <div class="grid md:grid-cols-2 gap-6 text-white flex-center">
                <div class="bg-gray-800/50 rounded-xl p-6 text-center">
                    <i class="fas fa-shield-alt text-2xl text-green-400 mb-3"></i>
                    <h4 class="font-bold mb-2">Segurança Total</h4>
                    <p class="text-sm text-gray-300">Seus dados protegidos com criptografia de ponta</p>
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
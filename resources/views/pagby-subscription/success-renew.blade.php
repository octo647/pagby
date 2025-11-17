{{-- resources/views/pagby-subscription/success.blade.php --}}

<x-pagby-layout>
    <div class="flex-1 flex flex-col items-center justify-center text-center px-4 py-10">
        <div class="max-w-2xl mx-auto">
            <!-- Animação de sucesso -->
            <div class="mb-8">
                <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100 mb-6 animate-pulse">
                    <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-4xl font-bold mb-4 text-white">🎉 Pagamento Confirmado!</h2>
                <p class="text-xl text-white/80">Sua assinatura PagBy foi reativada com sucesso!</p>
            </div>

            <!-- Detalhes da assinatura -->
            <div class="bg-white p-8 rounded-2xl shadow-xl mb-8">
                <h3 class="text-2xl font-semibold mb-6 text-gray-800">📋 Detalhes da Assinatura</h3>
                
                <div class="grid md:grid-cols-2 gap-6 text-left">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-600">Plano:</span>
                            <span class="font-bold text-purple-600 capitalize">
                                {{ session('plan') ?? $plan ?? 'Premium' }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-600">Cliente:</span>
                            <span class="font-semibold text-gray-800">
                                {{ session('tenant_name') ?? 'Novo Cliente' }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-600">Status:</span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                                Ativo
                            </span>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-600">ID Pagamento:</span>
                            <span class="font-mono text-sm text-gray-800 bg-gray-100 px-2 py-1 rounded">
                                {{ session('payment_id') ?? $payment_id ?? 'SIM_' . time() }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-600">Data:</span>
                            <span class="text-gray-800">{{ now()->format('d/m/Y H:i') }}</span>
                        </div>
                        
                        
                    </div>
                </div>
            </div>

            <!-- Próximos passos -->
            

            <!-- Botões de ação -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
                <a href="{{ route('login') }}" 
                   class="bg-gradient-to-r from-green-600 to-blue-600 text-white px-8 py-4 rounded-lg hover:from-green-700 hover:to-blue-700 transition-all font-bold text-lg transform hover:scale-105">
                    🚀 Acessar Minha Conta
                </a>
                
                <a href="{{ route('tenant.home') }}" 
                   class="bg-white text-gray-700 px-8 py-4 rounded-lg hover:bg-gray-50 transition-colors font-semibold border-2 border-gray-200">
                    ← Voltar ao Início
                </a>
            </div>

            <!-- Suporte -->
            <div class="bg-gray-800/50 rounded-xl p-6 text-white">
                <h4 class="font-bold mb-3">💬 Precisa de Ajuda?</h4>
                <p class="mb-4">Nossa equipe está pronta para te ajudar a começar!</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="https://wa.me/5532987007302" class="bg-green-600 hover:bg-green-700 px-6 py-2 rounded-lg transition-colors">
                        📱 WhatsApp: (32) 98700-7302
                    </a>
                    <a href="mailto:suporte@pagby.com.br" class="bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded-lg transition-colors">
                        📧 suporte@pagby.com.br
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-pagby-layout>
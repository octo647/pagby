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

                <h1 class="text-3xl font-bold text-gray-900 mb-4">🎉 Cadastro Realizado com Sucesso!</h1>
                
                <div class="text-gray-600 mb-8">
                    <p class="mb-4">✅ Seu negócio foi registrado e está pronto para usar!</p>       
                    <p class="text-lg font-semibold text-green-600 mb-2">
                        🎁 Você ganhou 30 dias GRÁTIS de todas as funcionalidades!
                    </p>
                    <p class="text-sm mb-2">
                        Acesse seu sistema agora e comece a gerenciar seu negócio com o PagBy.
                    </p>
                </div>

                <!-- Informações de acesso -->
                @if(isset($tenant_domain))
                <div class="bg-blue-50 border-2 border-blue-300 rounded-lg p-6 mb-6">
                    <h4 class="text-blue-900 font-bold text-lg mb-3">🔐 Dados de Acesso</h4>
                    <div class="text-blue-800 text-left space-y-3">
                        <div>
                            <strong class="block text-sm text-gray-600 mb-1">Endereço do seu sistema:</strong>
                            <a href="http://{{ $tenant_domain }}" target="_blank" 
                               class="text-blue-600 hover:text-blue-800 font-mono text-lg underline break-all">
                                {{ $tenant_domain }}
                            </a>
                        </div>
                        <div>
                            <strong class="block text-sm text-gray-600 mb-1">Email de acesso:</strong>
                            <span class="font-mono">{{ session('contact_email') ?? 'Verifique seu e-mail' }}</span>
                        </div>
                        <div class="mt-4 p-3 bg-yellow-100 border border-yellow-300 rounded">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-envelope mr-2"></i>
                                Enviamos sua senha temporária por e-mail. Verifique sua caixa de entrada!
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Próximos passos -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <h4 class="text-green-800 font-semibold mb-3">🚀 Próximos Passos:</h4>
                    <ol class="text-green-700 text-sm text-left space-y-2">
                        <li><strong>1.</strong> Acesse seu sistema usando o link acima</li>
                        <li><strong>2.</strong> Configure seus serviços e horários de atendimento</li>
                        <li><strong>3.</strong> Adicione sua equipe de profissionais</li>
                        <li><strong>4.</strong> Comece a agendar clientes!</li>
                        <li><strong>5.</strong> Após 30 dias, escolha seu plano para continuar usando</li>
                    </ol>
                </div>
                
                <!-- Botões de ação -->
                <div class="space-y-3">
                    @if(isset($tenant_domain))
                        <!-- Botão para acessar o sistema -->
                        <a href="http://{{ $tenant_domain }}" target="_blank"
                           class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white font-bold py-4 px-6 rounded-lg hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200 block text-center">
                            <i class="fas fa-rocket mr-2"></i>
                            Acessar Meu Sistema Agora
                        </a>
                        
                        <!-- Botão secundário para voltar à home -->
                        <a href="{{ route('home') }}" 
                           class="w-full bg-white text-gray-700 border-2 border-gray-300 font-semibold py-3 px-6 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition-all block text-center">
                            <i class="fas fa-home mr-2"></i>
                            Voltar para Página Inicial
                        </a>
                    @else
                        <!-- Se não tem domínio, mostrar botão para home -->
                        <a href="{{ route('home') }}" 
                           class="w-full bg-gradient-to-r from-pink-600 to-indigo-600 text-white font-bold py-4 px-6 rounded-lg hover:from-pink-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 block text-center transition-all">
                            <i class="fas fa-home mr-2"></i>
                            Ir para o Início
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
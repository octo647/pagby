<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                🚀 Bem-vindo ao Pagby!
            </h1>
            <p class="text-lg text-gray-600">
                Configure sua plataforma em {{ $totalSteps }} passos simples
            </p>
        </div>

        <!-- Progress Bar -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Progresso da Configuração</span>
                <span class="text-sm font-semibold text-indigo-600">{{ $completedSteps }}/{{ $totalSteps }} concluídos</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-3 rounded-full transition-all duration-500" 
                     style="width: {{ $progressPercentage }}%">
                </div>
            </div>
            <div class="mt-2 text-center">
                <span class="text-2xl font-bold text-indigo-600">{{ number_format($progressPercentage, 0) }}%</span>
            </div>
        </div>

        <!-- Action Button -->
        <div class="text-center mb-6">
            <button wire:click="refreshProgress" 
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Atualizar Progresso
            </button>
        </div>

        @if (session()->has('message'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded mb-6 shadow">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    {{ session('message') }}
                </div>
            </div>
        @endif

        <!-- Steps Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            @foreach($steps as $step)
                <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden border-2 {{ $step['completed'] ? 'border-green-500' : 'border-gray-200' }}">
                    <!-- Step Header -->
                    <div class="bg-gradient-to-r {{ $step['completed'] ? 'from-green-500 to-green-600' : 'from-gray-500 to-gray-600' }} text-white p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <span class="text-3xl">{{ $step['icon'] }}</span>
                                <div>
                                    <div class="text-sm opacity-90">Passo {{ $step['number'] }}</div>
                                    <h3 class="font-bold text-lg">{{ $step['title'] }}</h3>
                                </div>
                            </div>
                            @if($step['completed'])
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </div>
                    </div>

                    <!-- Step Content -->
                    <div class="p-6">
                        <p class="text-gray-600 mb-4">{{ $step['description'] }}</p>
                        
                        @if($step['completed'])
                            <div class="flex items-center text-green-600 font-semibold mb-4">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Concluído
                            </div>
                        @else
                            <div class="flex items-center text-orange-600 font-semibold mb-4">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                Pendente
                            </div>
                        @endif

                        <a href="{{ $step['route'] }}" 
                           class="inline-flex items-center justify-center w-full px-4 py-3 {{ $step['completed'] ? 'bg-gray-100 hover:bg-gray-200 text-gray-700' : 'bg-indigo-600 hover:bg-indigo-700 text-white' }} font-medium rounded-lg transition-colors duration-200">
                            {{ $step['completed'] ? 'Revisar' : 'Configurar Agora' }}
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Completion Message -->
        @if($completedSteps === $totalSteps)
            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg shadow-lg p-8 text-center">
                <div class="text-6xl mb-4">🎉</div>
                <h2 class="text-3xl font-bold mb-2">Parabéns!</h2>
                <p class="text-lg mb-6">Você concluiu todas as etapas de configuração inicial!</p>
                <button wire:click="completeOnboarding" 
                        class="inline-flex items-center px-6 py-3 bg-white text-green-600 font-bold rounded-lg hover:bg-gray-100 transition-colors duration-200 shadow-md">
                    Ir para o Dashboard
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </button>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <p class="text-gray-600">
                    Complete todos os passos para começar a usar o Pagby em sua plenitude! 💪
                </p>
            </div>
        @endif

        <!-- Help Section -->
        <div class="mt-8 bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">Precisa de ajuda?</h3>
                    <p class="text-blue-800 mb-3">
                        Siga os passos na ordem sugerida para uma configuração mais eficiente. Você pode revisar e modificar qualquer configuração a qualquer momento.
                    </p>
                    <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
                        <li>Clique em "Atualizar Progresso" após concluir cada passo</li>
                        <li>Você pode pular etapas e voltar depois se necessário</li>
                        <li>As configurações podem ser alteradas a qualquer momento no menu Cadastros</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

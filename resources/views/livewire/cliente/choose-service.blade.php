<div class="min-h-screen bg-gray-50">
    {{-- Cabeçalho da Página --}}
    <div class="bg-white border-b border-gray-200 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <h1 class="text-3xl font-bold text-gray-900">✂️ Escolha os Serviços</h1>
                <p class="mt-2 text-gray-600">Selecione os serviços que deseja agendar</p>
            </div>
        </div>
    </div>

    {{-- Conteúdo Principal --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        @if(isset($services))
            <form wire:submit="chosen" class="space-y-8">
                {{-- Lista de Serviços --}}
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-purple-600">
                        <h2 class="text-xl font-semibold text-white">Serviços Disponíveis</h2>
                        <p class="text-blue-100 text-sm">Você pode selecionar múltiplos serviços</p>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        @foreach($services as $item=>$service)  
                            <label for="checkbox-{{ $service['id'] }}" 
                                   class="flex items-center p-4 border-2 border-gray-200 rounded-xl hover:border-blue-300 hover:bg-blue-50 transition-all duration-200 cursor-pointer group">
                                
                                {{-- Checkbox Customizado --}}
                                <div class="relative flex-shrink-0">
                                    <input wire:model="chosen_services" 
                                           id="checkbox-{{ $service['id'] }}" 
                                           class="sr-only" 
                                           type="checkbox" 
                                           name="service" 
                                           value="{{$service['id']}}"> 
                                    <div class="w-6 h-6 border-2 border-gray-300 rounded-lg group-hover:border-blue-400 transition-colors duration-200 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                
                                {{-- Conteúdo do Serviço --}}
                                <div class="ml-4 flex-1 flex items-center justify-between">
                                    <div>
                                        <h3 class="font-semibold text-gray-900 group-hover:text-blue-700 transition-colors duration-200">
                                            {{$service['service']}}
                                        </h3>
                                        <p class="text-sm text-gray-500">Serviço profissional de qualidade</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-green-600">R$ {{$service['price']}}</div>
                                        <div class="text-xs text-gray-500">por sessão</div>
                                    </div>
                                </div>
                                
                                {{-- Ícone de Seleção --}}
                                <div class="ml-4 flex-shrink-0">
                                    <div class="w-10 h-10 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white opacity-75 group-hover:opacity-100 transition-opacity duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
                
                {{-- Mensagem de Erro --}}
                @error('semservico')
                    <div class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl p-4">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-red-800 font-medium">{{ $message }}</p>
                        </div>
                    </div>
                @enderror
                
                {{-- Botões de Ação --}}
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button type="submit" 
                            class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-medium rounded-xl text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                        Prosseguir
                    </button>
                    
                    <button type="button" 
                            wire:click="back"
                            class="inline-flex items-center px-8 py-4 border border-gray-300 text-lg font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 shadow-md hover:shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                        </svg>
                        Voltar
                    </button>
                    
                    <button type="button" 
                            onclick="" 
                            class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-medium rounded-xl text-white bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancelar
                    </button>
                </div>
            </form>
        @else
            {{-- Estado Vazio --}}
            <div class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Nenhum serviço disponível</h3>
                <p class="text-gray-500">Não há serviços disponíveis no momento.</p>
            </div>
        @endif
    </div>

    {{-- Estilos CSS Customizados para Checkbox --}}
    <style>
        input[type="checkbox"]:checked + div {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-color: #3b82f6;
        }
        
        input[type="checkbox"]:checked + div svg {
            opacity: 1;
        }
        
        label:has(input[type="checkbox"]:checked) {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
    </style>
</div>
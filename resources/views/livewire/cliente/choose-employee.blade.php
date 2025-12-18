
<div class="min-h-screen bg-gray-50">
    {{-- Cabeçalho da Página --}}
    <div class="bg-white border-b border-gray-200 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <h1 class="text-3xl font-bold text-gray-900">👥 Escolha o Profissional</h1>
                <p class="mt-2 text-gray-600">Selecione o profissional que irá realizar seu atendimento</p>
            </div>
        </div>
    </div>

    {{-- Conteúdo Principal --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        @if(count($funcionarios) == 0)
            {{-- Estado Vazio --}}
            <div class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Nenhum profissional disponível</h3>
                <p class="text-gray-500">Escolha uma filial para ver seus profissionais.</p>
            </div>
        @else
            {{-- Grid de Profissionais --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-purple-600">
                    <h2 class="text-xl font-semibold text-white">Profissionais Disponíveis</h2>
                    <p class="text-blue-100 text-sm">Clique no profissional para selecioná-lo</p>
                </div>
                
                <div class="p-6">
                    {{-- Se nenhum profissional foi selecionado, mostra todos --}}
                    @if(is_null($choosedEmployee))
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($funcionarios as $index => $funcionario)
                                <div wire:key="{{$index}}" 
                                     class="group relative bg-white border-2 border-gray-200 rounded-2xl p-6 hover:border-blue-300 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 cursor-pointer"
                                     wire:click="selectProfessional('{{$funcionario->id}}')">
                                    
                                    {{-- Avatar --}}
                                    <div class="flex flex-col items-center text-center">
                                        <div class="relative mb-4">
                                            <img class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-lg group-hover:scale-110 transition-transform duration-300" 
                                                 src="{{tenant_asset($funcionario->photo)}}" 
                                                 alt="Foto {{$funcionario->name}}">
                                            <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full flex items-center justify-center border-2 border-white">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                        </div>
                                        
                                        {{-- Nome --}}
                                        <h3 class="font-bold text-lg text-gray-900 group-hover:text-blue-700 transition-colors duration-200 mb-2">
                                            {{$funcionario->name}}
                                        </h3>
                                        
                                        {{-- Badge de Profissional --}}
                                        <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mb-4">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Profissional
                                        </div>
                                        
                                        {{-- Botão de Seleção --}}
                                        <button class="w-full px-3 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold text-sm lg:text-xs xl:text-xs hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform group-hover:scale-105 shadow-lg hover:shadow-xl">
                                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            Selecionar
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Profissional selecionado - mostra só ele com opção de trocar --}}
                        @php
                            $selectedUser = \App\Models\User::find($choosedEmployee);
                        @endphp
                        <div class="max-w-md mx-auto">
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-2xl p-6 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="relative mb-4">
                                        <img class="w-24 h-24 rounded-full object-cover border-4 border-green-400 shadow-lg" 
                                             src="{{tenant_asset($selectedUser->photo)}}" 
                                             alt="Foto {{$selectedUser->name}}">
                                        <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center border-2 border-white">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <h3 class="font-bold text-xl text-gray-900 mb-2">{{$selectedUser->name}}</h3>
                                    
                                    <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800 mb-4">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Profissional Selecionado
                                    </div>
                                    
                                    <button wire:click="apague()" 
                                            class="px-6 py-2 bg-white text-gray-700 border border-gray-300 rounded-xl font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 shadow-sm hover:shadow-md">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Trocar Profissional
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif


        {{-- Profissional Selecionado e Escolha de Serviços --}}
        @if(!is_null($choosedEmployee))
            @php
                $user = \App\Models\User::find($choosedEmployee);
                $name = $user->name; 
                $services = $user->services;   
            @endphp
            
            <div class="mt-8 bg-white rounded-2xl shadow-lg overflow-hidden">
                {{-- Header com Profissional Selecionado --}}
                <div class="px-6 py-4 bg-gradient-to-r from-green-500 to-emerald-600">
                    <div class="flex items-center space-x-4">
                        <img class="w-16 h-16 rounded-full object-cover border-4 border-white shadow-lg" 
                             src="{{tenant_asset($user->photo)}}" 
                             alt="Foto de {{$name}}">
                        <div>
                            <h2 class="text-xl font-semibold text-white">✅ Profissional Selecionado</h2>
                            <p class="text-green-100 text-lg font-medium">{{$name}}</p>
                        </div>
                    </div>
                </div>

                {{-- Seleção de Serviços --}}
                <div class="p-6">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">🎯 Escolha os Serviços</h3>
                        <p class="text-gray-600">Selecione os serviços que este profissional irá realizar</p>
                    </div>

                    <div class="space-y-3">
                        @foreach($user->services as $service)  
                            <label for="checkbox-{{ $service->id }}" 
                                   class="flex items-center p-4 border-2 border-gray-200 rounded-xl hover:border-blue-300 hover:bg-blue-50 transition-all duration-200 cursor-pointer group">
                                
                                {{-- Checkbox Customizado --}}
                                <div class="relative flex-shrink-0">
                                    <input wire:model="chosen_services" 
                                           id="checkbox-{{ $service->id }}" 
                                           class="sr-only" 
                                           type="checkbox" 
                                           name="service" 
                                           value="{{$service->id}}"> 
                                    <div class="w-6 h-6 border-2 border-gray-300 rounded-lg group-hover:border-blue-400 transition-colors duration-200 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                
                                {{-- Conteúdo do Serviço --}}
                                <div class="ml-4 flex-1 flex items-center justify-between">
                                    <div>
                                        <h4 class="font-semibold text-gray-900 group-hover:text-blue-700 transition-colors duration-200">
                                            {{$service->service}}
                                        </h4>
                                        <p class="text-sm text-gray-500">Serviço especializado</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xl font-bold text-green-600">R$ {{$service->price}}</div>
                                        <div class="text-xs text-gray-500">por sessão</div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    {{-- Mensagem de Erro --}}
                    @error('semservico')
                        <div class="mt-6 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl p-4">
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
                    <div class="flex flex-col sm:flex-row gap-4 mt-8">
                        <button type="button" 
                                wire:click="chosenService()" 
                                onclick="window.dispatchEvent(new CustomEvent('professionalChosen')); window.dispatchEvent(new CustomEvent('serviceChosen'));"
                                class="flex-1 inline-flex items-center justify-center px-8 py-4 border border-transparent text-lg font-medium rounded-xl text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                            Prosseguir
                        </button>
                        
                        <button type="button" 
                                wire:click="apague()" 
                                class="flex-1 inline-flex items-center justify-center px-8 py-4 border border-transparent text-lg font-medium rounded-xl text-white bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancelar
                        </button>
                    </div>
                </div>
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

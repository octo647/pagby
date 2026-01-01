<div>
    {{-- Gestão de Horários - Interface Moderna --}}

<div x-data="{ showEditPanel: @entangle('showEditPanel') }" class="min-h-screen bg-gray-50">
    
    {{-- Cabeçalho da Página --}}
    <div class="bg-white border-b border-gray-200 mb-8">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Gestão de Horários</h1>
                    <p class="text-gray-600 mt-1">Configure os horários de trabalho dos funcionários</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Container Principal --}}
    <div class="container mx-auto px-4 pb-8">
        
        {{-- Lista de Horários - Design Moderno --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            
            {{-- Cabeçalho da Lista --}}
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Horários dos Funcionários
                    </h2>
                    <div class="flex items-center gap-4">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model.live="showOnlyActive" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Apenas ativos</span>
                        </label>
                        <span class="text-sm text-gray-500">{{ count($officehours) }} funcionários</span>
                    </div>
                </div>
            </div>

            {{-- Lista de Horários --}}
            <div class="divide-y divide-gray-200">
                @forelse($officehours as $index => $officehour)
                    <div wire:key="schedule-{{$index}}" class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between">
                            
                            {{-- Informações do Funcionário --}}
                            <div class="flex items-center space-x-4 mb-4 lg:mb-0">
                                <div class="flex-shrink-0">
                                    @if(!empty($officehour['photo']))
                                        <img src="{{ tenant_asset('profile-photos/' . basename($officehour['photo'])) }}" 
                                             alt="{{ $officehour['funcionario'] }}" 
                                             class="h-12 w-12 rounded-full object-cover border-2 border-blue-500">
                                    @else
                                        <div class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                            <span class="text-lg font-medium text-white uppercase">
                                                {{ substr($officehour['funcionario'], 0, 2) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">{{ $officehour['funcionario'] }}</h3>
                                    <p class="text-sm text-gray-500">Funcionário</p>
                                </div>
                            </div>

                            {{-- Grid de Horários da Semana --}}
                            <div class="flex-1 lg:mx-6">
                                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-2">
                                    @foreach(['seg' => 'Seg', 'ter' => 'Ter', 'qua' => 'Qua', 'qui' => 'Qui', 'sex' => 'Sex', 'sab' => 'Sáb', 'dom' => 'Dom'] as $dia => $diaAbrev)
                                        @php
                                            $ini = $officehour[$dia.'_ini'] ?? '';
                                            $fim = $officehour[$dia.'_fim'] ?? '';
                                            $lunch_ini = $officehour[$dia.'_lunch_ini'] ?? '';
                                            $lunch_fim = $officehour[$dia.'_lunch_fim'] ?? '';
                                            $isWorking = $ini && $fim;
                                        @endphp
                                        <div class="text-center">
                                            <div class="text-xs font-medium text-gray-600 mb-1">{{ $diaAbrev }}</div>
                                            @if($isWorking)
                                                <div class="bg-green-100 border border-green-200 rounded-lg p-2">
                                                    <div class="text-xs font-medium text-green-800">
                                                        {{ $ini }}-{{ $fim }}
                                                    </div>
                                                    @if($lunch_ini && $lunch_fim)
                                                        <div class="text-xs text-green-600 mt-1">
                                                            Almoço: {{ $lunch_ini }}-{{ $lunch_fim }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="bg-gray-100 border border-gray-200 rounded-lg p-2">
                                                    <div class="text-xs text-gray-500">Folga</div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Botão de Editar --}}
                            <div class="mt-4 lg:mt-0">
                                <button wire:click="abrirPainelEdicao({{ $index }})" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Editar Horários
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum horário configurado</h3>
                        <p class="mt-1 text-sm text-gray-500">Não há horários definidos para funcionários.</p>
                    </div>
                @endforelse
            </div>
        </div>


        {{-- Painel Lateral de Edição Moderno --}}
        <div x-show="showEditPanel" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;">
            
            {{-- Backdrop --}}
            <div class="flex items-center justify-end min-h-screen">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                     @click="showEditPanel = false"></div>
                
                {{-- Painel Lateral --}}
                <div x-show="showEditPanel"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-x-full"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 translate-x-full"
                     class="bg-white w-full max-w-2xl h-full shadow-xl flex flex-col relative z-10">
                    
                    {{-- Cabeçalho do Painel --}}
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4 text-white">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                {{-- Foto ou Iniciais do Funcionário --}}
                                @if(!empty($editOfficehour['photo']))
                                    <img src="{{ tenant_asset('profile-photos/' . basename($editOfficehour['photo'])) }}" 
                                         alt="{{ $editOfficehour['funcionario'] ?? '' }}" 
                                         class="h-12 w-12 rounded-full object-cover border-2 border-white">
                                @else
                                    <div class="h-12 w-12 rounded-full bg-white bg-opacity-20 flex items-center justify-center border-2 border-white">
                                        <span class="text-lg font-medium text-white uppercase">
                                            {{ isset($editOfficehour['funcionario']) ? substr($editOfficehour['funcionario'], 0, 2) : '' }}
                                        </span>
                                    </div>
                                @endif
                                <div>
                                    <h2 class="text-xl font-semibold">Editar Horários</h2>
                                    <p class="text-blue-100 text-sm mt-1">{{ $editOfficehour['funcionario'] ?? '' }}</p>
                                </div>
                            </div>
                            <button wire:click="$set('showEditPanel', false)" 
                                    class="rounded-md text-blue-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Conteúdo do Painel --}}
                    <div class="flex-1 overflow-y-auto p-6">
                        
                        {{-- Botão de Ação Rápida --}}
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-medium text-yellow-800">Ação Rápida</h3>
                                    <p class="text-sm text-yellow-600 mt-1">Aplicar horários de segunda-feira para toda a semana</p>
                                </div>
                                <button wire:click="repetirSegunda"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 01-2-2v-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                                    </svg>
                                    Aplicar
                                </button>
                            </div>
                        </div>

                        {{-- Formulário de Horários --}}
                        <div class="space-y-6">
                            @foreach([
                                'seg' => ['nome' => 'Segunda-feira', 'cor' => 'blue'],
                                'ter' => ['nome' => 'Terça-feira', 'cor' => 'indigo'],
                                'qua' => ['nome' => 'Quarta-feira', 'cor' => 'purple'],
                                'qui' => ['nome' => 'Quinta-feira', 'cor' => 'pink'],
                                'sex' => ['nome' => 'Sexta-feira', 'cor' => 'red'],
                                'sab' => ['nome' => 'Sábado', 'cor' => 'orange'],
                                'dom' => ['nome' => 'Domingo', 'cor' => 'yellow']
                            ] as $dia => $config)
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <h3 class="text-sm font-medium text-gray-900 mb-4 flex items-center">
                                        <div class="w-3 h-3 rounded-full bg-{{ $config['cor'] }}-500 mr-2"></div>
                                        {{ $config['nome'] }}
                                    </h3>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        {{-- Horário de Trabalho --}}
                                        <div class="space-y-3">
                                            <label class="block text-sm font-medium text-gray-700">Horário de Trabalho</label>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-xs text-gray-500 mb-1">Início</label>
                                                    <input type="time" 
                                                           wire:model="editOfficehour.{{$dia}}_ini" 
                                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-gray-500 mb-1">Fim</label>
                                                    <input type="time" 
                                                           wire:model="editOfficehour.{{ $dia }}_fim" 
                                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        {{-- Intervalo de Almoço --}}
                                        <div class="space-y-3">
                                            <label class="block text-sm font-medium text-gray-700">Intervalo de Almoço</label>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-xs text-gray-500 mb-1">Início</label>
                                                    <input type="time" 
                                                           wire:model="editOfficehour.{{ $dia }}_lunch_ini" 
                                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-gray-500 mb-1">Fim</label>
                                                    <input type="time" 
                                                           wire:model="editOfficehour.{{ $dia }}_lunch_fim" 
                                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Rodapé com Botões --}}
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                        <div class="flex justify-end space-x-3">
                            <button wire:click="$set('showEditPanel', false)" 
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancelar
                            </button>
                            <button wire:click="salvarPainelEdicao" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Salvar Horários
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                                                                    
    </div>

</div>
</div>


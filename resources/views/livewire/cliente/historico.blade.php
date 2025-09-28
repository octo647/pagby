<div class="min-h-screen bg-gray-50">
    {{-- Cabeçalho da Página --}}
    <div class="bg-white border-b border-gray-200 mb-4 sm:mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-4 sm:py-6">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">📋 Histórico de Serviços</h1>
                <p class="mt-1 sm:mt-2 text-sm sm:text-base text-gray-600">Confira todos os serviços realizados e suas avaliações</p>
            </div>
        </div>
    </div>

    {{-- Conteúdo Principal --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8 sm:pb-12">
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
            @if($historico->isEmpty())
                {{-- Estado Vazio --}}
                <div class="text-center py-12 sm:py-16 px-4">
                    <div class="w-16 h-16 sm:w-24 sm:h-24 mx-auto mb-4 sm:mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 sm:w-12 sm:h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">Nenhum serviço encontrado</h3>
                    <p class="text-sm sm:text-base text-gray-500 max-w-md mx-auto">Você ainda não realizou nenhum serviço conosco.</p>
                </div>
            @else
                {{-- Layout Responsivo: Tabela para Desktop, Cards para Mobile --}}
                
                {{-- Tabela para Desktop (oculta em telas pequenas) --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Data</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Serviço</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Profissional</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Avaliação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($historico as $agendamento)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($agendamento->appointment_date)->format('d/m/Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($agendamento->appointment_date)->locale('pt_BR')->isoFormat('dddd') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 font-medium">{{ $agendamento->services }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center text-white font-semibold">
                                                    {{ substr($agendamento->employee->name, 0, 2) }}
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $agendamento->employee->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($agendamento->avaliacao)
                                            <div class="space-y-2">
                                                {{-- Estrelas --}}
                                                <div class="flex items-center space-x-1">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if($i <= $agendamento->avaliacao->avaliacao)
                                                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                                <polygon points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.8 4.8,17.7 9.9,14.7 15,17.7 13.8,11.8 18.2,7.6 12.2,6.6"/>
                                                            </svg>
                                                        @else
                                                            <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                                <polygon points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.8 4.8,17.7 9.9,14.7 15,17.7 13.8,11.8 18.2,7.6 12.2,6.6"/>
                                                            </svg>
                                                        @endif
                                                    @endfor
                                                    <span class="text-sm text-gray-500 ml-2">{{ $agendamento->avaliacao->avaliacao }}/5</span>
                                                </div>
                                                
                                                @if($agendamento->avaliacao->comentario)
                                                    <p class="text-sm text-gray-700 mt-1">{{ $agendamento->avaliacao->comentario }}</p>
                                                @endif
                                                
                                                <button wire:click="editarAvaliacao({{ $agendamento->id }})" 
                                                        class="inline-flex items-center space-x-1 text-blue-600 hover:text-blue-800 transition-colors duration-200" 
                                                        title="Editar Avaliação">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                    </svg>
                                                    <span class="text-xs">Editar</span>
                                                </button>
                                            </div>
                                        @else
                                            <button wire:click="avaliar({{ $agendamento->id }})" 
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-gradient-to-r from-green-400 to-green-600 hover:from-green-500 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                                                ⭐ Avaliar
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Cards para Mobile (visível apenas em telas pequenas) --}}
                <div class="md:hidden space-y-4 p-4">
                    @foreach($historico as $agendamento)
                        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow duration-200">
                            {{-- Header do Card --}}
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center text-white font-semibold text-sm">
                                        {{ substr($agendamento->employee->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <h3 class="font-medium text-gray-900 text-sm">{{ $agendamento->employee->name }}</h3>
                                        <p class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($agendamento->appointment_date)->format('d/m/Y') }} - 
                                            {{ \Carbon\Carbon::parse($agendamento->appointment_date)->locale('pt_BR')->isoFormat('dddd') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Serviço --}}
                            <div class="mb-4">
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 8.172V5L8 4z" />
                                    </svg>
                                    {{ $agendamento->services }}
                                </div>
                            </div>

                            {{-- Avaliação --}}
                            <div class="pt-3 border-t border-gray-100">
                                @if($agendamento->avaliacao)
                                    <div class="space-y-3">
                                        {{-- Estrelas --}}
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-1">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if($i <= $agendamento->avaliacao->avaliacao)
                                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <polygon points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.8 4.8,17.7 9.9,14.7 15,17.7 13.8,11.8 18.2,7.6 12.2,6.6"/>
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                            <polygon points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.8 4.8,17.7 9.9,14.7 15,17.7 13.8,11.8 18.2,7.6 12.2,6.6"/>
                                                        </svg>
                                                    @endif
                                                @endfor
                                                <span class="text-xs text-gray-500 ml-2">{{ $agendamento->avaliacao->avaliacao }}/5</span>
                                            </div>
                                            
                                            <button wire:click="editarAvaliacao({{ $agendamento->id }})" 
                                                    class="inline-flex items-center space-x-1 text-blue-600 hover:text-blue-800 transition-colors duration-200 text-xs" 
                                                    title="Editar Avaliação">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                                <span>Editar</span>
                                            </button>
                                        </div>
                                        
                                        @if($agendamento->avaliacao->comentario)
                                            <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg">{{ $agendamento->avaliacao->comentario }}</p>
                                        @endif
                                    </div>
                                @else
                                    <div class="flex justify-center">
                                        <button wire:click="avaliar({{ $agendamento->id }})" 
                                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-green-400 to-green-600 hover:from-green-500 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                                            ⭐ Avaliar Serviço
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    
    {{-- Modal de Avaliação --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            {{-- Overlay --}}
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                
                {{-- Modal Content - Responsivo --}}
                <div class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 w-full mx-4 max-w-sm sm:max-w-lg">
                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-4 sm:mb-6">
                        <div class="flex items-center space-x-2 sm:space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 sm:w-6 sm:h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <polygon points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.8 4.8,17.7 9.9,14.7 15,17.7 13.8,11.8 18.2,7.6 12.2,6.6"/>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-semibold text-gray-900">Editar Avaliação</h3>
                                <p class="text-xs sm:text-sm text-gray-500">Como foi sua experiência?</p>
                            </div>
                        </div>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors duration-200 p-1">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form wire:submit.prevent="updateAvaliacao" class="space-y-6">        
                        {{-- Avaliação com Estrelas --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Sua Avaliação</label>
                            <div class="flex items-center justify-center space-x-1 py-4 bg-gray-50 rounded-xl">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button" 
                                            wire:click="$set('avaliacao', {{ $i }})" 
                                            class="focus:outline-none transform hover:scale-110 transition-all duration-200">
                                        <svg class="w-10 h-10 {{ $avaliacao >= $i ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-300" 
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <polygon points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.8 4.8,17.7 9.9,14.7 15,17.7 13.8,11.8 18.2,7.6 12.2,6.6"/>
                                        </svg>
                                    </button>
                                @endfor
                            </div>
                            @if($avaliacao > 0)
                                <p class="text-center text-sm text-gray-600 mt-2">{{ $avaliacao }} de 5 estrelas</p>
                            @endif
                        </div>
                        
                        {{-- Comentário --}}
                        <div>
                            <label for="comentario" class="block text-sm font-medium text-gray-700 mb-2">Comentário (opcional)</label>
                            <textarea wire:model="comentario" 
                                    id="comentario" 
                                    rows="4" 
                                    placeholder="Conte-nos mais sobre sua experiência..."
                                    class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm resize-none"></textarea>
                        </div>
                        
                        {{-- Buttons --}}
                        <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 pt-4">
                            <button type="submit" 
                                    class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 py-3 rounded-xl font-medium hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105 text-sm sm:text-base">
                                💾 Salvar Avaliação
                            </button>
                            <button type="button" 
                                    wire:click="closeModal" 
                                    class="flex-1 bg-gray-100 text-gray-800 px-4 py-3 rounded-xl font-medium hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 text-sm sm:text-base">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif


</div>

<div>
    {{-- The whole world belongs to you. --}}
    <div class="p-6">
        
        <div class="bg-white shadow-md rounded-lg p-6">
            @if($historico->isEmpty())
                <p class="text-gray-500">Nenhum serviço encontrado.</p>
            @else
            
                <table class=" tabela-escura w-full text-sm text-left  rounded-lg overflow-hidden">
                    <thead>
                        <tr >
                            <th>Data</th>
                            <th>Serviço</th>
                            <th>Profissional</th>                            
                            <th>Avaliação</th>
                        </tr>
                    </thead>
                    <tbody >
                        @foreach($historico as $agendamento)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($agendamento->appointment_date)->format('d/m/y') }}</td>
                                <td >{{ $agendamento->services }}</td>
                                <td>{{ $agendamento->employee->name }}</td>
                                <td>
                                    @if($agendamento->avaliacao)
                                        <!-- Estrelas -->
        <div class="flex items-center mb-1">
            @for ($i = 1; $i <= 5; $i++)
                @if($i <= $agendamento->avaliacao->avaliacao)
                    <!-- Estrela preenchida -->
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <polygon points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.8 4.8,17.7 9.9,14.7 15,17.7 13.8,11.8 18.2,7.6 12.2,6.6"/>
                    </svg>
                @else
                    <!-- Estrela vazia -->
                    <svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                        <polygon points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.8 4.8,17.7 9.9,14.7 15,17.7 13.8,11.8 18.2,7.6 12.2,6.6"/>
                    </svg>
                @endif
            @endfor
        </div>
                                        {{ $agendamento->avaliacao->comentario }}
                                        <button wire:click="editarAvaliacao({{ $agendamento->id }})" class="inline-flex items-center text-blue-600 hover:text-blue-800" title="Editar Avaliação">
                                            <!-- Ícone de lápis (Heroicons outline) -->
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                        </button>
                                    @else
                                        <button wire:click="avaliar({{ $agendamento->id }})" class="text-green-600 underline">Avaliar</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
            @endif
        </div>
        
        @if($showModal)
            <div class="fixed inset-0 flex items-center justify-center z-50">
                <div class="bg-gray-800 bg-opacity-50 fixed inset-0"></div>
                <div class="bg-white rounded-lg p-6 z-10">
                    <h2 class="text-lg font-semibold mb-4">Editar Avaliação</h2>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Avaliação</label>
                        <div class="flex flex-row-reverse justify-end">
                            @for ($i = 5; $i >= 1; $i--)
                                <button type="button" wire:click="$set('avaliacao', {{ $i }})" class="focus:outline-none">
                                    <svg class="h-8 w-8 {{ $avaliacao >= $i ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <polygon points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.8 4.8,17.7 9.9,14.7 15,17.7 13.8,11.8 18.2,7.6 12.2,6.6"/>
                                    </svg>
                                </button>
                            @endfor
                        </div>
                    </div>
                    <form wire:submit.prevent="updateAvaliacao">        
                        <div class="mb-4">
                            <label for="comentario" class="block text-sm font-medium text-gray-700">Comentário</label>
                            <textarea wire:model="comentario" id="comentario" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                        </div>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Salvar</button>
                        <button type="button" wire:click="$set('showModal', false)" class="ml-2 bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400">Cancelar</button>
                    </form> 
                    <button wire:click="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    </div>
                    </div>
                @endif


</div>

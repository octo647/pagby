<div>
    
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="tabela-escura ">
        
            <thead class="">
            <tr>
            <th scope="col" class="px-6 py-3">
            Nome
            </th>
            <th scope="col" class="px-6 py-3">
            seg
            </th>
            <th scope="col" class="px-6 py-3">
            ter
            </th>
            <th scope="col" class="px-6 py-3">
            qua
            </th>
            <th scope="col" class="px-6 py-3">
            qui
            </th>
            <th scope="col" class="px-6 py-3">
            sex
            </th>
            <th scope="col" class="px-6 py-3">
            sab
            </th>
            <th  scope="col" class="px-6 py-3">
            dom
            </th>
            <th></th>
            </tr>
            </thead>
            <tbody>

                @foreach($officehours as $index => $officehour)
            <tr class="">
                <th scope="row" class="">{{ $officehour['funcionario'] }}</th>
                @foreach(['seg','ter','qua','qui','sex','sab','dom'] as $dia)
                    @php
                        $ini = $officehour[$dia.'_ini'] ?? '';
                        $fim = $officehour[$dia.'_fim'] ?? '';
                        $lunch_ini = $officehour[$dia.'_lunch_ini'] ?? '';
                        $lunch_fim = $officehour[$dia.'_lunch_fim'] ?? '';
                    @endphp
                    <td class="{{ !($ini && $fim) ? 'bg-gray-300 italic text-gray-500' : '' }}">
                   
                        {{-- Exibe os horários formatados --}}
                   
                       
                        {{ ($ini && $fim) ? "$ini-$fim" : 'Não programado' }}
                        <br>
                        <small>                            
                            {{ ($lunch_ini && $lunch_fim) ? "Almoço: $lunch_ini-$lunch_fim" : '' }}
                        </small>
                    </td>
                @endforeach
                    <td>
                        <button wire:click="abrirPainelEdicao({{ $index }})" title="Editar horários" class="p-1 rounded hover:bg-blue-200">
    <svg class="w-6 h-6 text-blue-800" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
        <path fill-rule="evenodd" d="M14 4.182A4.136 4.136 0 0 1 16.9 3c1.087 0 2.13.425 2.899 1.182A4.01 4.01 0 0 1 21 7.037c0 1.068-.43 2.092-1.194 2.849L18.5 11.214l-5.8-5.71 1.287-1.31.012-.012Zm-2.717 2.763L6.186 12.13l2.175 2.141 5.063-5.218-2.141-2.108Zm-6.25 6.886-1.98 5.849a.992.992 0 0 0 .245 1.026 1.03 1.03 0 0 0 1.043.242L10.282 19l-5.25-5.168Zm6.954 4.01 5.096-5.186-2.218-2.183-5.063 5.218 2.185 2.15Z" clip-rule="evenodd"/>
    </svg>
</button>
                    </td>
            </tr>
                @endforeach
            </tbody>
        </table>
        </div>

        {{-- Botão para abrir o painel de edição --}}

        {{-- Painel lateral de edição --}}
        @if($showEditPanel)
        <div 
            class="fixed inset-0 bg-black bg-opacity-30 z-40 flex justify-end transition-all"
        
        >
            <div  class="bg-white w-full max-w-md h-full shadow-lg p-6 overflow-y-auto">
                <h2 class="text-lg font-bold mb-4">
                    Editar horários de {{ $editOfficehour['funcionario'] ?? '' }}
                </h2>
                <table class="table-auto">
                    <thead class="text-xs text-white text-center uppercase bg-gray-400">
                        <tr>
                            <th class="border border-gray-300" >Dia</th>
                            <th class="border border-gray-300">Início</th>
                            <th class="border border-gray-300">Final</th>
                            <th class="border border-gray-300">Intervalo</th>
                        </tr>
                    </thead>


                @foreach(['seg','ter','qua','qui','sex','sab','dom'] as $dia)
                
                <tr>
                    <td class="border border-gray-300 font-semibold capitalize">{{ $dia }}</td>
                    <td class="border border-gray-300">
                        <input type="time" wire:model="editOfficehour.{{$dia}}_ini" class="border rounded p-1" >
                    </td>
                    <td class="border border-gray-300">
                        <input type="time" wire:model="editOfficehour.{{ $dia }}_fim" class="border rounded p-1">
                    </td>
                    <td class="border border-gray-300">
                        <div class="flex items-center gap-2">
                            <input type="time" wire:model="editOfficehour.{{ $dia }}_lunch_ini" class="border rounded p-1">
                            <span>até</span>
                            <input type="time" wire:model="editOfficehour.{{ $dia }}_lunch_fim" class="border rounded p-1">
                        </div>
                    </td>
                </tr>
                @endforeach
                </table>
                <div class="mt-4">
                    <button
                        class="bg-green-600 text-white px-3 py-1 rounded"
                        wire:click="repetirSegunda"
                        type="button"
                    >
                        Repetir horários de segunda-feira para todos os dias
                    </button>
                </div>
            
                <div class="mt-6 flex gap-2">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded" wire:click="salvarPainelEdicao">Salvar</button>
                    <button class="bg-gray-300 px-4 py-2 rounded" wire:click="$set('showEditPanel', false)">Cancelar</button>
                </div>
            </div>
        </div>
        @endif

                                                                    
    </div>
</div>


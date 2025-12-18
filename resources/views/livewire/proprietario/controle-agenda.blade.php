
<div class="p-4 md:p-8 bg-gradient-to-br from-green-50 to-green-100 min-h-screen">
    <div class="max-w-5xl mx-auto">
        <h2 class="text-3xl md:text-4xl font-extrabold text-green-900 mb-8 flex items-center gap-2">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 0V4m0 16v-4m8-4h-4m-8 0H4"/></svg>
            Controle de Agenda dos Funcionários
        </h2>
        <div class="flex flex-col md:flex-row md:items-end gap-4 mb-6">
            <div class="flex flex-col">
                <label class="font-semibold text-green-700 mb-1">Funcionário</label>
                    <select wire:model.live="selectedFuncionario" class="border border-green-300 rounded-lg lg:pr-24 px-3 py-2 focus:ring focus:ring-green-200 bg-white shadow-sm">
                    @foreach($funcionarios as $func)
                        <option value="{{ $func->id }}">{{ $func->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @php
                $carbon = \Carbon\Carbon::now();
            @endphp
            @for($i = 0; $i < 21; $i++)
                @php
                    $date = $carbon->copy()->addDays($i);
                    $dayOfWeek = $date->format('l');
                    $dateStr = $date->format('Y-m-d');
                    $indisponivel = isset($diasIndisponiveis[$dateStr]) && $diasIndisponiveis[$dateStr];
                @endphp
                <div
                    @if(!$indisponivel)
                        wire:click="openModal('{{ $dateStr }}', '{{ $dayOfWeek }}')"
                        class="bg-white border-green-200 text-green-700 rounded-xl shadow-lg border p-6 flex flex-col items-start cursor-pointer hover:scale-[1.02] hover:bg-green-50 transition-transform"
                    @else
                        class="bg-gray-200 border-gray-400 text-gray-500 rounded-xl shadow-lg border p-6 flex flex-col items-start"
                    @endif
                >
                    <div class="font-bold text-lg mb-2">
                        {{ $dias_pt[$dayOfWeek] ?? $dayOfWeek }}<br>
                        <span class="text-sm text-gray-700">{{ $date->format('d') }} de {{ $date->locale('pt_BR')->isoFormat('MMM') }}</span>
                    </div>
                    <div class="mb-2 text-sm text-gray-600">
                        @if($indisponivel)
                            Indisponível para agendamento
                        @else
                            Clique para ver os intervalos
                        @endif
                    </div>
                </div>
            @endfor
        </div>
        @if($showModal)
            <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md relative">
                    <button class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-3xl" wire:click="closeModal">&times;</button>
                    <h3 class="text-xl font-extrabold text-green-800 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 0V4m0 16v-4m8-4h-4m-8 0H4"/></svg>
                        @php
                            $dataFormatada = \Carbon\Carbon::parse($selectedDay)->format('d/m/Y');
                        @endphp
                        {{ $dataFormatada }}
                    </h3>
                    <div class="mb-2 text-gray-700 font-medium">Clique nos horários para bloquear/desbloquear</div>
                    <div class="grid grid-cols-4 gap-2">
                        @forelse($intervalosDoDia as $slot)
                            <div class="flex flex-col items-center justify-center p-2 rounded border w-full text-xs font-medium
                                @if($slot['ocupado']) border-red-300 bg-red-50 text-red-600
                                @elseif($slot['bloqueado']) border-red-400 bg-red-100 text-red-700
                                @else border-green-300 bg-green-50 text-green-700 @endif">
                                <span class="font-semibold mb-1">{{ $slot['start'] }}</span>
                                @if($slot['ocupado'])
                                    <span title="Ocupado">
                                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </span>
                                @else
                                    <button wire:click="toggleBloqueio('{{ $selectedDay }}', '{{ $slot['start'] }}', '{{ $slot['end'] }}')"
                                        class="mt-1 px-1 py-0.5 rounded text-white text-xs font-bold focus:outline-none transition-all duration-150
                                        @if($slot['bloqueado']) bg-red-600 hover:bg-red-700 @else bg-gray-400 hover:bg-gray-500 @endif"
                                        title="{{ $slot['bloqueado'] ? 'Disponibilizar' : 'Bloquear' }}">
                                        @if($slot['bloqueado'])
                                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" /></svg>
                                        @else
                                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        @endif
                                    </button>
                                @endif
                            </div>
                        @empty
                            <div class="col-span-full text-gray-500">Nenhum horário disponível para este dia.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<div>
    <div class="mb-4">
        <h3 class="text-lg font-semibold">Escolha o dia</h3>
    </div>
    @if(empty($available_times))
        <div class="text-gray-500">Selecione um funcionário e serviços para ver dias disponíveis.</div>
    @else
        <div class="flex flex-wrap gap-2 justify-center mb-6">
            @foreach($forward_days as $day)
                <button 
                    wire:click="$set('selected_day', '{{ $day }}')" 
                    class="w-16 h-16 sm:w-20 sm:h-20 flex flex-col items-center justify-center rounded-full border border-blue-300 transition-all duration-200
                        @if(isset($selected_day) && $selected_day === $day) bg-blue-600 text-white @else hover:bg-blue-200 @endif">
                    <span class="font-bold text-sm sm:text-base">{{ \Carbon\Carbon::parse($day)->format('d/m') }}</span>
                    <span class="text-xs">{{ \Carbon\Carbon::parse($day)->locale('pt_BR')->isoFormat('ddd') }}</span>
                </button>
            @endforeach
        </div>

        @if(isset($selected_day) && !empty($available_times[$selected_day]))
        
            <div class="mb-4">
                <h3 class="text-lg font-semibold">
                    Escolha o horário para {{ \Carbon\Carbon::parse($selected_day)->format('d/m') }}
                    ({{ \Carbon\Carbon::parse($selected_day)->locale('pt_BR')->isoFormat('ddd') }})
                </h3>
            </div>
            <div class="flex flex-wrap gap-2 sm:gap-4 justify-center">
                @if(!empty($available_times[$selected_day]))
                    @foreach($available_times[$selected_day] as $slot)
                        <div 
                        wire:click="selectTime('{{ $slot[0] }}')"
                        class="w-16 h-16 sm:w-20 sm:h-20 flex items-center justify-center transition-all duration-200 rounded-full hover:bg-blue-200 cursor-pointer border border-blue-100">
                            <span class="hover:text-lg sm:hover:text-xl text-center">{{ $slot[0] }}</span>
                        </div>
                    @endforeach
                @else
                    <span class="text-gray-400">Indisponível</span>
                @endif
            </div>
        @endif
        @if(isset($selected_time))
            <div class="mt-4">
                <h3 class="text-lg font-semibold">Horário selecionado: {{ $selected_time }}</h3>
            </div>
            <div class="mt-2">
                <button wire:click="confirmTime" class="bg-blue-600 text-white px-4 py-2 rounded">Confirmar horário</button>
            </div>
        @endif
        @endif
</div>

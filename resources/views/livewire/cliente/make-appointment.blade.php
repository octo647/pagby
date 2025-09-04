<div>
    @if (session()->has('assinatura-valida'))
            <div class="alerta-sucesso mb-4">
                {{ session('assinatura-valida') }}
            </div>
    @endif 
    
    
    @if(session('warning'))
    <div class="bg-yellow-200 text-yellow-800 p-2 rounded mb-2">
        {{ session('warning') }}
    </div>
@endif
    @if(empty($available_times))
        <div class="text-gray-500">Selecione um funcionário e os serviços para ver os dias disponíveis.</div>
       

    @else       
    @error('auth') <div class="alerta-aviso">{{ $message }}</div>
    @enderror
    @error('subscription') <div class="alerta-aviso">{!! $message !!}</div>
    @enderror
    @error('selected_day') <div class="alerta">{{ $message }}</div> 
    @enderror
    @error('selected_time') <div class="alerta">{{ $message }}</div>     
    @enderror
    @error('services') <div class="alerta">{{ $message }}</div>
    @enderror
    @error('professional') <div class="alerta">{{ $message }}</div>
    @enderror
     


        <div class="mb-4">
            <h3 class="text-lg font-semibold">Dias disponíveis</h3>
            <p class="text-sm text-gray-500">Selecione um dia para ver os horários disponíveis.</p>
        </div>
        @php
            $dias_pt = [
                'monday' => 'segunda',
                'tuesday' => 'terca',
                'wednesday' => 'quarta',
                'thursday' => 'quinta',
                'friday' => 'sexta',
                'saturday' => 'sabado',
                'sunday' => 'domingo',
            ];         
        
        @endphp
        @if(!empty($forward_days))
        <div class="flex flex-wrap gap-2 justify-center mb-6">
        @php
            // Verifica se todos os serviços escolhidos estão incluídos no plano
            $all_included = !empty($this->chosen_service_ids) && empty(array_diff($this->chosen_service_ids, $plan_services));
            
        @endphp
        
  
            @foreach($forward_days as $day)
                @php
                    $weekday_en = strtolower(\Carbon\Carbon::parse($day)->format('l'));
                    $weekday_pt = $dias_pt[$weekday_en] ?? $weekday_en;
                    $is_allowed = in_array($weekday_pt, $allowed_days ?? []);
                    
                    $show_as_allowed = $all_included ? $is_allowed : true;
                    
                    // Verificar se já existe agendamento neste dia
                    $has_appointment = in_array($day, $days_with_appointments ?? []);
                    
                @endphp
                
                <button 
                    wire:click="$set('selected_day', '{{ $day }}')" 
                    class="w-16 h-16 sm:w-20 sm:h-20 flex flex-col items-center justify-center rounded-full border transition-all duration-200
                        {{ $has_appointment ? 'border-red-500 bg-red-100 text-red-700 cursor-not-allowed' : 
                           ((isset($selected_day) && $selected_day === $day) ? 'bg-blue-600 text-white' : 
                           ($show_as_allowed ? 'border-blue-300 hover:bg-blue-200' : 'border-gray-400 bg-gray-200 text-gray-500 line-through')) }}"
                    @if($has_appointment)
                        disabled
                        title="Você já possui um agendamento nesta data"
                    @elseif(!$show_as_allowed)
                        title="Seu plano não cobre este dia. O serviço será cobrado normalmente."
                    @endif
                >
                    <span class="font-bold text-sm sm:text-base">{{ \Carbon\Carbon::parse($day)->format('d/m') }}</span>
                    <span class="text-xs">{{ \Carbon\Carbon::parse($day)->locale('pt_BR')->isoFormat('ddd') }}</span>
                    @if($has_appointment)
                        <span class="text-xs font-bold">AGENDADO</span>
                    @endif
                </button>
            @endforeach
        </div>
        @else
            <div class="text-gray-500">Nenhum dia disponível.</div>
        @endif

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
                        class="cursor-pointer px-4 w-13 h-15 flex flex-col items-center justify-center  border transition-all duration-200
                        
                        {{ (isset($selected_day) && $selected_time === $slot[0]) ? 'bg-blue-700 text-white' : ($show_as_allowed ? 'border-blue-300 hover:bg-blue-200' : 'border-gray-400 bg-gray-200 text-gray-500 line-through')}}">
                            <span class="text-sm text-center">{{ $slot[0] }}</span>
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
                <button 
                    wire:click="confirmTime" 
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                >
                    Confirmar horário
                </button>
            </div>
        @endif
        @endif
</div>

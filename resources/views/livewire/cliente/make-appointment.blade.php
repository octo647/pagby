<div class="min-h-screen bg-gray-50">
    {{-- Cabeçalho da Página --}}
    <div class="bg-white border-b border-gray-200 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <h1 class="text-3xl font-bold text-gray-900">🕒 Selecionar Horário</h1>
                <p class="mt-2 text-gray-600">Escolha o melhor dia e horário para seu atendimento</p>
            </div>
        </div>
    </div>

    {{-- Conteúdo Principal --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        {{-- Mensagens de Sucesso/Aviso --}}
        @if (session()->has('assinatura-valida'))
            <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-green-800 font-medium">{{ session('assinatura-valida') }}</p>
                </div>
            </div>
        @endif 

        @if(session('warning'))
            <div class="mb-6 bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl p-4">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <p class="text-yellow-800 font-medium">{{ session('warning') }}</p>
                </div>
            </div>
        @endif

        @if(empty($available_times))
            {{-- Estado Inicial --}}
            <div class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Selecione um funcionário e os serviços</h3>
                <p class="text-gray-500">Para ver os dias disponíveis, primeiro escolha o profissional e os serviços desejados.</p>
            </div>
        @else            {{-- Mensagens de Erro --}}
            @error('auth') 
                <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-red-800 font-medium">{{ $message }}</p>
                    </div>
                </div>
            @enderror
            
            @error('subscription') 
                <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-red-800 font-medium">{!! $message !!}</p>
                    </div>
                </div>
            @enderror

            @error('selected_day') 
                <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-red-800 font-medium">{{ $message }}</p>
                    </div>
                </div>
            @enderror

            @error('selected_time') 
                <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-red-800 font-medium">{{ $message }}</p>
                    </div>
                </div>
            @enderror

            @error('services') 
                <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-red-800 font-medium">{{ $message }}</p>
                    </div>
                </div>
            @enderror

            @error('professional') 
                <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-red-800 font-medium">{{ $message }}</p>
                    </div>
                </div>
            @enderror

            {{-- Seção de Seleção de Dias --}}
            <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">📅 Dias Disponíveis</h2>
                    <p class="text-gray-600">Selecione um dia para ver os horários disponíveis</p>
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
                    @php
                        // Verifica se todos os serviços escolhidos estão incluídos no plano
                        $all_included = !empty($this->chosen_service_ids) && empty(array_diff($this->chosen_service_ids, $plan_services));
                    @endphp
                    
                    <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-7 gap-4 justify-items-center">
                        @foreach($forward_days as $day)
                            @php
                                $weekday_en = strtolower(\Carbon\Carbon::parse($day)->format('l'));
                                $weekday_pt = $dias_pt[$weekday_en] ?? $weekday_en;
                                $is_allowed = in_array($weekday_pt, $allowed_days ?? []);
                                
                                $show_as_allowed = $all_included ? $is_allowed : true;
                                
                                // Verificar se já existe agendamento neste dia
                                $has_appointment = in_array($day, $days_with_appointments ?? []);
                                
                            @endphp
                            
                            @php $sem_horario = empty($available_times[$day] ?? []); @endphp
                            <button 
                                @if($sem_horario)
                                    disabled
                                    title="Sem horários disponíveis"
                                @else
                                    wire:click="$set('selected_day', '{{ $day }}')"
                                @endif
                                class="w-20 h-20 sm:w-24 sm:h-24 flex flex-col items-center justify-center rounded-2xl border-2 transition-all duration-300 transform
                                    {{ $sem_horario ? 'border-gray-200 bg-gray-100 text-gray-400 cursor-not-allowed opacity-60' :
                                        ($has_appointment ? 'border-red-300 bg-red-50 text-red-700 cursor-not-allowed' :
                                        ((isset($selected_day) && $selected_day === $day) ? 'bg-gradient-to-br from-blue-500 to-purple-600 text-white shadow-lg scale-105' :
                                        ($show_as_allowed ? 'border-gray-200 hover:border-blue-300 hover:bg-blue-50 bg-white shadow-sm' : 'border-gray-200 bg-gray-50 text-gray-400 line-through'))) }}"
                            >
                                <span class="font-bold text-base">{{ \Carbon\Carbon::parse($day)->format('d') }}</span>
                                <span class="text-xs uppercase">{{ \Carbon\Carbon::parse($day)->locale('pt_BR')->isoFormat('MMM') }}</span>
                                <span class="text-xs">{{ \Carbon\Carbon::parse($day)->locale('pt_BR')->isoFormat('ddd') }}</span>
                                @if($sem_horario)
                                    <span class="text-xs font-bold mt-1">SEM HORÁRIO</span>
                                @elseif($has_appointment)
                                    <span class="text-xs font-bold mt-1">OCUPADO</span>
                                @elseif(!$show_as_allowed)
                                    <span class="text-xs font-bold mt-1">PAGO</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-gray-500">Nenhum dia disponível</p>
                    </div>
                @endif
            </div>
            
            {{-- Seção de Seleção de Horários --}}
            @if(isset($selected_day) && !empty($available_times[$selected_day]))
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">🕐 Horários Disponíveis</h2>
                        <p class="text-gray-600">
                            Para {{ \Carbon\Carbon::parse($selected_day)->format('d/m/Y') }}
                            ({{ \Carbon\Carbon::parse($selected_day)->locale('pt_BR')->isoFormat('dddd') }})
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-4 justify-items-center">
                        @if(!empty($available_times[$selected_day]))
                            @foreach($available_times[$selected_day] as $slot)
                                <button 
                                    wire:click="selectTime('{{ $slot[0] }}')"
                                    class="w-20 h-16 flex flex-col items-center justify-center rounded-xl border-2 transition-all duration-300 transform hover:scale-105
                                    {{ (isset($selected_time) && $selected_time === $slot[0]) ? 'bg-gradient-to-br from-green-500 to-emerald-600 text-white shadow-lg border-green-400 scale-105' : 'border-gray-200 hover:border-blue-300 hover:bg-blue-50 bg-white shadow-sm' }}">
                                    <svg class="w-4 h-4 mb-1 {{ (isset($selected_time) && $selected_time === $slot[0]) ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm font-semibold">{{ $slot[0] }}</span>
                                </button>
                            @endforeach
                        @else
                            <div class="col-span-full text-center py-8">
                                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-gray-500">Nenhum horário disponível</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            
            {{-- Confirmação do Horário --}}
            @if(isset($selected_time))
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl border border-green-200 p-8 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Horário Selecionado</h3>
                    <p class="text-gray-600 mb-6">
                        {{ \Carbon\Carbon::parse($selected_day)->format('d/m/Y') }} às {{ $selected_time }}
                    </p>
                    <button 
                        wire:click="confirmTime" 
                        class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-medium rounded-xl text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Confirmar Horário
                    </button>
                </div>
            @endif
        @endif
    </div>
</div>

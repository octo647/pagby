<div class="min-h-screen bg-gray-50">
    {{-- Cabeçalho da Página --}}
    <div class="bg-white border-b border-gray-200 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <h1 class="text-3xl font-bold text-gray-900">📅 Meus Agendamentos</h1>
                <p class="mt-2 text-gray-600">Gerencie seus agendamentos e acompanhe seu status</p>
            </div>
        </div>
    </div>

    {{-- Conteúdo Principal --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        @if(count($agendamentos)>0)
            {{-- Lista de Agendamentos --}}
            <div class="space-y-6">

                @foreach($agendamentos as $index=>$schedule)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                        {{-- Header do Card --}}
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-white">Agendamento #{{$index + 1}}</h3>
                                        <p class="text-blue-100 text-sm">{{$schedule['date']}} às {{$schedule['start_time']}}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                        @if($schedule['status'] === 'Confirmado') bg-green-100 text-green-800
                                        @elseif($schedule['status'] === 'Pendente') bg-yellow-100 text-yellow-800  
                                        @elseif($schedule['status'] === 'Cancelado') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        @if($schedule['status'] === 'Confirmado') ✅
                                        @elseif($schedule['status'] === 'Pendente') ⏳
                                        @elseif($schedule['status'] === 'Cancelado') ❌
                                        @else 📋 @endif
                                        {{$schedule['status']}}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Conteúdo do Card --}}
                        <div class="p-6">
                            <div class="grid md:grid-cols-2 gap-4">
                                {{-- Coluna Esquerda --}}
                                <div class="space-y-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H7m2 0v-4a2 2 0 012-2h2a2 2 0 012 2v4" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Filial</p>
                                            <p class="text-gray-900">{{$schedule['branch']}}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start space-x-3">
                                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Profissional</p>
                                            <p class="text-gray-900">{{$schedule['professional']}}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Coluna Direita --}}
                                <div class="space-y-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Serviço(s)</p>
                                            <p class="text-gray-900">{{$schedule['services']}}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start space-x-3">
                                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Preço</p>
                                            <p class="text-gray-900 font-semibold">{{$schedule['total']}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Comandas Relacionadas --}}
                            @php
                                $comandas = $this->getComandasParaAgendamento($schedule['appointment_id']);
                            @endphp
                            @if($comandas && count($comandas) > 0)
                                <div class="mt-6 p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl border border-gray-200">
                                    <div class="flex items-center space-x-2 mb-3">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <h4 class="text-sm font-semibold text-gray-700">Comandas Relacionadas</h4>
                                    </div>
                                    <div class="space-y-2">
                                        @foreach($comandas as $comanda)
                                            @php
                                                // Verificar se há discrepância de preço
                                                $precoAgendamento = (float) str_replace(',', '.', str_replace('.', '', $schedule['total']));
                                                $precoComanda = (float) $comanda->total_geral;
                                                $temDiscrepancia = abs($precoAgendamento - $precoComanda) > 0.01;
                                            @endphp
                                            
                                            <div class="flex justify-between items-center bg-white p-3 rounded-lg border {{ $temDiscrepancia ? 'border-yellow-300 bg-yellow-50' : 'border-gray-100' }}">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-8 h-8 {{ $temDiscrepancia ? 'bg-yellow-100' : 'bg-blue-100' }} rounded-full flex items-center justify-center">
                                                        <svg class="w-4 h-4 {{ $temDiscrepancia ? 'text-yellow-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <div class="flex items-center space-x-2">
                                                            <p class="text-sm font-medium text-gray-900">Comanda #{{ $comanda->numero_comanda }}</p>
                                                            @if($temDiscrepancia)
                                                                <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" title="Preço diferente do agendamento">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                                </svg>
                                                            @endif
                                                        </div>
                                                        <div class="text-xs space-y-1">
                                                            <p class="text-gray-500">R$ {{ number_format($comanda->total_geral, 2, ',', '.') }}</p>
                                                            @if($temDiscrepancia)
                                                                <p class="text-yellow-600 font-medium">
                                                                    ⚠️ Preço atualizado (Era: R$ {{$schedule['total']}})
                                                                </p>
                                                                <p class="text-xs text-yellow-500">
                                                                    Os preços podem diferir devido a atualizações na tabela de preços
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col items-end space-y-1">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                        @if($comanda->status === 'Aberta') bg-blue-100 text-blue-800
                                                        @elseif($comanda->status === 'Finalizada') bg-green-100 text-green-800
                                                        @elseif($comanda->status === 'Cancelada') bg-red-100 text-red-800
                                                        @else bg-gray-100 text-gray-800 @endif">
                                                        {{ $comanda->status }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    {{-- Informação sobre diferenças de preço --}}
                                    @php
                                        $precoAgendamento = (float) str_replace(',', '.', str_replace('.', '', $schedule['total']));
                                        $algumTemDiscrepancia = false;
                                        foreach($comandas as $comanda) {
                                            if(abs($precoAgendamento - (float) $comanda->total_geral) > 0.01) {
                                                $algumTemDiscrepancia = true;
                                                break;
                                            }
                                        }
                                    @endphp
                                    
                                    @if($algumTemDiscrepancia)
                                        <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                            <div class="flex items-start space-x-2">
                                                <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <div>
                                                    <h5 class="text-sm font-medium text-yellow-800">Por que os preços são diferentes?</h5>
                                                    <p class="text-xs text-yellow-700 mt-1">
                                                        O preço do agendamento foi definido no momento da marcação, enquanto 
                                                        o preço da comanda reflete valores atualizados da tabela de preços. 
                                                        Isso pode acontecer quando há reajustes nos valores dos serviços.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                        
                        {{-- Footer do Card - Ações --}}
                            @if($schedule['status'] !== 'Cancelado')
                                @php
                                    // Corrigir parsing para formato brasileiro
                                    // Tenta diferentes formatos de data/hora
                                    $agendamentoData = null;
                                    try {
                                        $agendamentoData = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $schedule['date'].' '.$schedule['start_time']);
                                    } catch (Exception $e) {
                                        try {
                                            $agendamentoData = \Carbon\Carbon::createFromFormat('d/m H:i', $schedule['date'].' '.$schedule['start_time']);
                                            // Adiciona ano atual se não estiver presente
                                            $agendamentoData->year = now()->year;
                                        } catch (Exception $e2) {
                                            $agendamentoData = \Carbon\Carbon::parse($schedule['date'].' '.$schedule['start_time']);
                                        }
                                    }
                                    $agora = \Carbon\Carbon::now();
                                    $hoje = $agora->isSameDay($agendamentoData);
                                    $diffMinutos = $agendamentoData->diffInMinutes($agora, false); // negativo se ainda não chegou
                                @endphp
                                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                                    <div class="flex justify-end">
                                        @if($hoje && $diffMinutos < 0)
                                            @if($diffMinutos < -120)
                                                <div class="text-left w-full text-sm text-gray-700 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                                    Se precisar cancelar este agendamento, por favor entre em contato com o Profissional <span class="font-semibold">{{$schedule['professional']}}</span>
                                                    @if(!empty($schedule['professional_phone']))
                                                        através do whatsapp <a href="https://wa.me/{{$schedule['professional_phone']}}" class="text-blue-600 underline">{{$schedule['professional_phone']}}</a>
                                                    @endif
                                                    com pelo menos duas horas de antecedência.
                                                </div>
                                            @else
                                                <div class="text-left w-full text-sm text-gray-700 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                                    Se precisar adiar ou cancelar este agendamento, fale com <span class="font-semibold">{{$schedule['professional']}}</span>
                                                    @if(!empty($schedule['professional_phone']))
                                                        através do whatsapp <a href="https://wa.me/{{$schedule['professional_phone']}}" class="text-blue-600 underline">{{$schedule['professional_phone']}}</a>
                                                    @endif
                                                    . 
                                                </div>
                                            @endif
                                        @else
                                            <button wire:click="deleteSchedule({{$schedule['id']}})" 
                                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 transform hover:scale-105">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                Cancelar Agendamento
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endif
                    </div>
                @endforeach
            </div>
            
            {{-- Mensagem de Sucesso --}}
            @if (session('success'))
                <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-green-800 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif
        @else
            {{-- Estado Vazio --}}
            <div class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Nenhum agendamento encontrado</h3>
                <p class="text-gray-500 mb-8">Você ainda não possui agendamentos. Faça seu primeiro agendamento!</p>
            </div>
        @endif

        {{-- Botão de Novo Agendamento --}}
        <div class="text-center mt-8">
            <a href="/agendamento" 
               class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-medium rounded-xl text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Novo Agendamento
            </a>
        </div>
    </div>
</div>
    




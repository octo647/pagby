
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header com data e calendário --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-blue-700">Agenda do dia</span>
                    <span class="ml-2 px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-semibold text-lg">
                        {{ \Carbon\Carbon::parse($selectedDate ?? now())->translatedFormat('d \d\e F, l') }}
                    </span>
                </h2>
            </div>
            <div>
                <label class="inline-flex items-center cursor-pointer">
                    <svg class="w-6 h-6 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <input type="date" wire:model.live="selectedDate" class="border border-gray-300 rounded px-2 py-1 focus:ring-blue-500 focus:border-blue-500" />
                </label>
            </div>
        </div>
        {{-- Tabela para desktop --}}
        <div class="hidden md:block bg-white rounded-2xl shadow-xl overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr class="text-left">
                        {{-- <th class="px-6 py-4">Data</th> --}}
                        <th class="px-6 py-4">Horário</th>
                        <th class="px-6 py-4">Cliente</th>
                        <th class="px-6 py-4">Serviço</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agendamentos as $agendamento)
                        @php
                            $hoje = \Carbon\Carbon::parse($agendamento->appointment_date)->isSameDay(now());
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            {{-- <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($agendamento->appointment_date)->format('d/m/Y') }}</td> --}}
                            <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::createFromFormat('H:i:s', $agendamento->start_time)->format('H:i') }} - {{\Carbon\Carbon::createFromFormat('H:i:s', $agendamento->end_time)->format('H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $agendamento->customer->name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {!! collect(explode('/', $agendamento->services))->map(fn($s) => trim($s))->filter()->implode('<br>') !!}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    // Corrigir para combinar data e hora de colunas separadas
                                    // Garante que appointment_date não tenha hora embutida
                                    $data = preg_replace('/\s.*/', '', $agendamento->appointment_date);
                                    $dataHora = $data . ' ' . $agendamento->start_time;
                                    try {
                                        $agendamentoDataHora = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $dataHora);
                                    } catch (Exception $e) {
                                        try {
                                            $agendamentoDataHora = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $dataHora);
                                        } catch (Exception $e2) {
                                            $agendamentoDataHora = \Carbon\Carbon::parse($dataHora);
                                        }
                                    }
                                    $agora = \Carbon\Carbon::now();
                                    $jaPassou = $agora->greaterThan($agendamentoDataHora);
                                @endphp
                                @if(in_array($agendamento->status, ['Realizado', 'Cancelado']))
                                    <span class="inline-block px-2 py-1 rounded {{ $agendamento->status === 'Cancelado' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-gray-800' }} text-base font-medium">{{ $agendamento->status }}</span>
                                @elseif($hoje)
                                    <div class="flex flex-col gap-2 items-start">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="status_{{$agendamento->id}}" value="Cancelado" wire:click.live="confirmarCancelamento({{$agendamento->id}})" class="form-radio text-red-600">
                                            <span class="ml-2 text-red-600 font-medium">Cancelado</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="status_{{$agendamento->id}}" value="Realizado" wire:click="atualizarStatus({{$agendamento->id}}, 'Realizado')" class="form-radio text-green-600">
                                            <span class="ml-2 text-green-600 font-medium">Realizado</span>
                                        </label>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Nenhum agendamento futuro.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Cards para mobile --}}
        <div class="md:hidden space-y-4">
            @forelse($agendamentos as $agendamento)
                @php
                    $hoje = \Carbon\Carbon::parse($agendamento->appointment_date)->isSameDay(now());
                @endphp
                <div class="bg-white rounded-xl shadow p-4 flex flex-col gap-2">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::createFromFormat('H:i:s', $agendamento->start_time)->format('H:i') }} - {{\Carbon\Carbon::createFromFormat('H:i:s', $agendamento->end_time)->format('H:i') }}</span>
                        </div>
                        <span class="inline-block px-2 py-1 rounded bg-blue-100 text-blue-700 text-xs font-medium">{{ $agendamento->services }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold text-lg">
                            {{ $agendamento->customer->name ? substr($agendamento->customer->name, 0, 2) : '-' }}
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-900">{{ $agendamento->customer->name ?? '-' }}</span>
                        </div>
                    </div>
                    @php
                        // Corrigir para combinar data e hora de colunas separadas
                        $data = preg_replace('/\s.*/', '', $agendamento->appointment_date);
                        $dataHora = $data . ' ' . $agendamento->start_time;
                        try {
                            $agendamentoDataHora = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $dataHora);
                        } catch (Exception $e) {
                            try {
                                $agendamentoDataHora = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $dataHora);
                            } catch (Exception $e2) {
                                $agendamentoDataHora = \Carbon\Carbon::parse($dataHora);
                            }
                        }
                        $agora = \Carbon\Carbon::now();
                        $jaPassou = $agora->greaterThan($agendamentoDataHora);
                    @endphp
                    @if(in_array($agendamento->status, ['Realizado', 'Cancelado']))
                        <span class="inline-block px-2 py-1 rounded {{ $agendamento->status === 'Cancelado' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-gray-800' }} text-xs font-medium">{{ $agendamento->status }}</span>
                    @elseif($hoje)
                        <div class="mt-2 flex gap-2 items-center">
                            <label class="inline-flex items-center">
                                <input type="radio" name="status_mobile_{{$agendamento->id}}" value="Cancelado" wire:click="confirmarCancelamento({{$agendamento->id}})" class="form-radio text-red-600">
                                <span class="ml-2 text-red-600 font-medium">Cancelado</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="status_mobile_{{$agendamento->id}}" value="Realizado" wire:click="atualizarStatus({{$agendamento->id}}, 'Realizado')" class="form-radio text-green-600">
                                <span class="ml-2 text-green-600 font-medium">Realizado</span>
                            </label>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-xl shadow p-4 text-center text-gray-500">Nenhum agendamento futuro.</div>
            @endforelse
        </div>
    </div>
</div>

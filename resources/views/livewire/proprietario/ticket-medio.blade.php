<div>
    {{-- Análise de Ticket Médio dos Clientes --}}

    {{-- Filtros --}}
    <div class="mb-6 bg-white p-4 rounded-lg shadow-sm border">
        <h2 class="text-lg font-semibold mb-4 text-gray-800">Filtros de Análise</h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label for="periodStart" class="block text-sm font-medium text-gray-700 mb-1">Data Inicial</label>
                <input id="periodStart" type="date" wire:model.live="periodStart" class="w-full border rounded-md px-3 py-2 text-sm">
            </div>
            
            <div>
                <label for="periodEnd" class="block text-sm font-medium text-gray-700 mb-1">Data Final</label>
                <input id="periodEnd" type="date" wire:model.live="periodEnd" class="w-full border rounded-md px-3 py-2 text-sm">
            </div>
            
            <div>
                <label for="minAppointments" class="block text-sm font-medium text-gray-700 mb-1">Mínimo de Agendamentos</label>
                <input id="minAppointments" type="number" min="1" wire:model.live="minAppointments" class="w-full border rounded-md px-3 py-2 text-sm">
            </div>
        </div>
    </div>

    {{-- Versão Desktop - Tabela --}}
    <div class="hidden md:block bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="p-4 border-b bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Análise de Ticket Médio</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr class="text-left">
                        <th class="px-4 py-3 text-sm font-medium text-gray-700">Cliente</th>
                        <th class="px-4 py-3 text-sm font-medium text-gray-700">Email</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Agendamentos</th>
                        <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">Total Gasto</th>
                        <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">Ticket Médio</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $cliente)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $cliente->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $cliente->email }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $cliente->total_agendamentos }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-green-600">
                                R$ {{ number_format($cliente->total_gasto, 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="text-lg font-bold text-purple-600">
                                    R$ {{ number_format($cliente->ticket_medio, 2, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                Nenhum cliente encontrado para os critérios selecionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Versão Mobile - Cards --}}
    <div class="md:hidden space-y-4">
        @forelse($clientes as $cliente)
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                {{-- Cabeçalho do Card --}}
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-gray-900 truncate">{{ $cliente->name }}</div>
                        <div class="text-sm text-gray-600 truncate">{{ $cliente->email }}</div>
                    </div>
                    <div class="ml-3 flex-shrink-0">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $cliente->total_agendamentos }} agendamentos
                        </span>
                    </div>
                </div>

                {{-- Métricas Principais --}}
                <div class="grid grid-cols-2 gap-4 pt-3 border-t border-gray-200">
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-600 mb-1">Total Gasto</div>
                        <div class="text-lg font-bold text-green-600">
                            R$ {{ number_format($cliente->total_gasto, 2, ',', '.') }}
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-600 mb-1">Ticket Médio</div>
                        <div class="text-xl font-bold text-purple-600">
                            R$ {{ number_format($cliente->ticket_medio, 2, ',', '.') }}
                        </div>
                    </div>
                </div>

                {{-- Indicador Visual do Ticket Médio --}}
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span>Valor por agendamento</span>
                        <span class="font-medium text-purple-600">
                            {{ number_format($cliente->ticket_medio, 0) }} reais
                        </span>
                    </div>
                    <div class="mt-1 bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-purple-400 to-purple-600 h-2 rounded-full" 
                             style="width: {{ min(($cliente->ticket_medio / 200) * 100, 100) }}%"></div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-gray-500">
                <div class="text-4xl mb-2">💰</div>
                <p class="text-lg font-medium">Nenhum cliente encontrado</p>
                <p class="text-sm">Ajuste os filtros para ver os resultados.</p>
            </div>
        @endforelse
    </div>
<div class="mt-4">
    {{ $clientes->links() }}
</div>
</div>

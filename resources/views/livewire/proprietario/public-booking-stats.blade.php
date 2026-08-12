<div class="container mx-auto px-4 py-8">
    <!-- Header com Estatísticas -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold mb-4 text-gray-900">📱 Agendamentos Online</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600 mb-1">Hoje</div>
                <div class="text-2xl font-bold text-blue-600">{{ $stats['today'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600 mb-1">Esta Semana</div>
                <div class="text-2xl font-bold text-green-600">{{ $stats['week'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600 mb-1">Este Mês</div>
                <div class="text-2xl font-bold text-purple-600">{{ $stats['month'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600 mb-1">Total</div>
                <div class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</div>
            </div>
        </div>

        <!-- Componente de Compartilhamento -->
        <x-share-booking-link :url="route('public.booking')" class="mb-6" />
    </div>

    <!-- Filtros e Busca -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <!-- Filtro por Período -->
            <div class="flex gap-2">
                <button wire:click="setFilter('today')" 
                        class="px-4 py-2 rounded-lg transition {{ $filter === 'today' ? 'bg-blue-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">
                    Hoje
                </button>
                <button wire:click="setFilter('week')" 
                        class="px-4 py-2 rounded-lg transition {{ $filter === 'week' ? 'bg-blue-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">
                    Semana
                </button>
                <button wire:click="setFilter('month')" 
                        class="px-4 py-2 rounded-lg transition {{ $filter === 'month' ? 'bg-blue-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">
                    Mês
                </button>
                <button wire:click="setFilter('all')" 
                        class="px-4 py-2 rounded-lg transition {{ $filter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">
                    Todos
                </button>
            </div>

            <!-- Campo de Busca -->
            <div class="flex-1">
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Buscar por cliente..."
                       class="w-full rounded-lg border-gray-300">
            </div>
        </div>
    </div>

    <!-- Lista de Agendamentos -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($appointments->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cliente
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data/Hora
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Profissional
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Unidade
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Origem
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($appointments as $appointment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-blue-600 font-semibold">
                                                {{ strtoupper(substr($appointment->customer->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $appointment->customer->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $appointment->customer->phone }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ substr($appointment->start_time, 0, 5) }} - {{ substr($appointment->end_time, 0, 5) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $appointment->employee->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $appointment->branch->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'agendado' => 'bg-blue-100 text-blue-800',
                                            'confirmado' => 'bg-green-100 text-green-800',
                                            'concluído' => 'bg-gray-100 text-gray-800',
                                            'cancelado' => 'bg-red-100 text-red-800',
                                        ];
                                        $color = $statusColors[$appointment->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        🌐 Online
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="px-6 py-4 border-t">
                {{ $appointments->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum agendamento encontrado</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if($search)
                        Tente ajustar os filtros de busca.
                    @else
                        Compartilhe o link de agendamento para receber reservas online!
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>

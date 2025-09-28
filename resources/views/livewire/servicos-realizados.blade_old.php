<div>
    {{-- Serviços Realizados - Interface Moderna --}}

    {{-- Lista de Serviços --}}
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="p-4 border-b bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Serviços Realizados</h3>
        </div>

        {{-- Versão Desktop - Tabela --}}
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Funcionário</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Filial</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Cliente</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Serviços</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Data</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Horário</th>
                        <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agendamentos as $appointment)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a href="#" wire:click.prevent="showUserDetails({{ $appointment->employee->id }})" class="text-blue-600 hover:underline font-medium">
                                    {{ $appointment->employee->name ?? '-' }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $appointment->branch->branch_name ?? '-' }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $appointment->customer->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-gray-700">{{ $appointment->services }}</span>
                            </td>
                            <td class="px-4 py-3 text-center text-sm">
                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 text-center text-sm font-mono">
                                {{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-green-600">
                                R$ {{ number_format($appointment->total, 2, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                Nenhum serviço realizado encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Versão Mobile/Tablet - Cards --}}
        <div class="lg:hidden p-4 space-y-4">
            @forelse($agendamentos as $appointment)
                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                    {{-- Cabeçalho do Card --}}
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-gray-900">{{ $appointment->customer->name ?? '-' }}</div>
                            <div class="text-sm text-gray-600">{{ $appointment->branch->branch_name ?? '-' }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-green-600">
                                R$ {{ number_format($appointment->total, 2, ',', '.') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }} • 
                                {{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }}
                            </div>
                        </div>
                    </div>

                    {{-- Funcionário --}}
                    <div class="mb-2">
                        <span class="text-sm font-medium text-gray-600">Funcionário:</span>
                        <a href="#" wire:click.prevent="showUserDetails({{ $appointment->employee->id }})" class="text-blue-600 hover:underline font-medium ml-1">
                            {{ $appointment->employee->name ?? '-' }}
                        </a>
                    </div>

                    {{-- Serviços --}}
                    <div class="mt-2 p-3 bg-white rounded border-l-4 border-blue-500">
                        <div class="text-sm font-medium text-gray-600 mb-1">Serviços:</div>
                        <div class="text-sm text-gray-800">{{ $appointment->services }}</div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <div class="text-4xl mb-2">💼</div>
                    <p class="text-lg font-medium">Nenhum serviço encontrado</p>
                    <p class="text-sm">Os serviços realizados aparecerão aqui.</p>
                </div>
            @endforelse
        </div>
    </div>
{{-- Paginação --}}
<div class="mt-6">
    @if(method_exists($agendamentos, 'links'))
        {{ $agendamentos->links() }}
    @endif
</div>

{{-- Gráfico de Serviços --}}
<div class="mt-8 bg-white p-6 rounded-lg shadow-sm border">
    <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Serviços por Funcionário</h3>
        <p class="text-sm text-gray-600">Distribuição dos serviços realizados pelos funcionários</p>
    </div>
    
    <div class="relative h-64 sm:h-80" wire:ignore>
        <canvas id="myChart" class="w-full h-full"></canvas>
    </div>
</div>

{{-- Modal Detalhes do Usuário --}}
@if($showModal && isset($selectedUser))
<div class="fixed inset-0 flex items-center justify-center z-50">
    <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="closeModal"></div>
    <div class="bg-white rounded-lg shadow-xl z-10 max-w-2xl w-full mx-4 max-h-[90vh] overflow-hidden">
        {{-- Header do Modal --}}
        <div class="bg-gray-50 px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-900">Agendamentos de {{ $selectedUser->name }}</h2>
        </div>

        {{-- Conteúdo do Modal --}}
        <div class="p-6 max-h-96 overflow-y-auto">
            @if(isset($appointments) && $appointments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Data</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Serviço</th>
                                <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Status</th>
                                <th class="px-4 py-2 text-right text-sm font-medium text-gray-700">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $appointment)
                                <tr class="border-t">
                                    <td class="px-4 py-2 text-sm">
                                        {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-2 text-sm">{{ $appointment->services ?? '-' }}</td>
                                    <td class="px-4 py-2 text-center">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @if($appointment->status === 'confirmado') bg-green-100 text-green-800
                                            @elseif($appointment->status === 'cancelado') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-right text-sm font-semibold text-green-600">
                                        R$ {{ number_format($appointment->total ?? 0, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Paginação no Modal --}}
                @if(method_exists($appointments, 'links'))
                    <div class="mt-4">
                        {{ $appointments->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-8 text-gray-500">
                    <div class="text-4xl mb-2">📅</div>
                    <p class="text-lg font-medium">Nenhum agendamento encontrado</p>
                    <p class="text-sm">Este funcionário não possui agendamentos no período selecionado.</p>
                </div>
            @endif
        </div>

        {{-- Footer do Modal --}}
        <div class="bg-gray-50 px-6 py-4 border-t flex justify-end">
            <button wire:click="closeModal" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                Fechar
            </button>
        </div>
    </div>
</div>
@endif

<script>
window.renderChart = function() {
    const canvas = document.getElementById('myChart');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    
    // Destruir gráfico anterior se existir
    if (window.myChartInstance) {
        window.myChartInstance.destroy();
    }
    
    window.myChartInstance = new Chart(ctx, {
        type: 'bar',
        data: @json($chartData),
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'top',
                    display: false
                },
                title: { 
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    cornerRadius: 6
                }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    title: { 
                        display: true, 
                        text: 'Número de Serviços',
                        color: '#6B7280',
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    },
                    grid: {
                        color: 'rgba(107, 114, 128, 0.1)'
                    },
                    ticks: {
                        color: '#6B7280',
                        font: {
                            size: 11
                        }
                    }
                },
                x: { 
                    title: { 
                        display: true, 
                        text: 'Funcionários',
                        color: '#6B7280',
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    },
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6B7280',
                        font: {
                            size: 11
                        },
                        maxRotation: 45
                    }
                }
            },
            elements: {
                bar: {
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }
            }
        }
    });
};

// Inicializar gráfico
document.addEventListener('DOMContentLoaded', window.renderChart);
document.addEventListener('livewire:navigated', window.renderChart);

// Atualizar gráfico via Livewire se necessário
if (typeof Livewire !== 'undefined') {
    Livewire.on('updateServiceChart', function(data) {
        if (window.myChartInstance) {
            window.myChartInstance.data = data;
            window.myChartInstance.update();
        }
    });
}
</script>
</div>

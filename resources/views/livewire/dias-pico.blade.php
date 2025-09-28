<div class="min-h-screen bg-gradient-to-br from-gray-50 via-indigo-50 to-purple-100">
    {{-- Header Principal --}}
    <div class="bg-white shadow-sm border-b border-gray-200 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <nav class="flex mb-2" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <span class="text-gray-500 text-sm">Estatísticas</span>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-indigo-600 text-sm font-medium">Dias de Pico</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                        <svg class="w-7 h-7 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Análise de Dias de Pico
                    </h1>
                    <p class="text-gray-600 mt-1">Identifique os dias da semana com maior demanda de agendamentos</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        {{-- Filtros de Período --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    Filtros de Período
                </h3>
                <p class="text-sm text-gray-500 mt-1">Selecione o período para análise dos dias de pico</p>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="periodStart" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <svg class="w-4 h-4 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Data Inicial
                        </label>
                        <input id="periodStart" 
                               type="date" 
                               wire:model.live="periodStart" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors bg-white">
                    </div>
                    
                    <div>
                        <label for="periodEnd" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <svg class="w-4 h-4 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Data Final
                        </label>
                        <input id="periodEnd" 
                               type="date" 
                               wire:model.live="periodEnd" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors bg-white">
                    </div>
                </div>
            </div>
        </div>

        {{-- Mensagens de Feedback --}}
        @if(session()->has('message'))
            <div class="bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-green-800 font-medium">{{ session('message') }}</span>
                </div>
            </div>
        @endif
        
        @if(session()->has('error'))
            <div class="bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-red-800 font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Cards Resumo --}}
            <div class="lg:col-span-3">
                @php
                    $totalAgendamentos = collect($diasPico)->sum('total');
                    $diaComMaisAgendamentos = collect($diasPico)->sortByDesc('total')->first();
                    $diaComMenosAgendamentos = collect($diasPico)->sortBy('total')->first();
                    $mediaAgendamentos = count($diasPico) > 0 ? $totalAgendamentos / count($diasPico) : 0;
                @endphp
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    {{-- Total de Agendamentos --}}
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-sm p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-indigo-100 text-sm font-medium">Total de Agendamentos</p>
                                <p class="text-2xl font-bold">{{ number_format($totalAgendamentos) }}</p>
                            </div>
                            <div class="bg-indigo-400 bg-opacity-30 rounded-full p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Dia de Pico --}}
                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg shadow-sm p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium">Dia de Maior Pico</p>
                                <p class="text-xl font-bold">{{ $diaComMaisAgendamentos['dia'] ?? 'N/A' }}</p>
                                <p class="text-green-100 text-xs">{{ $diaComMaisAgendamentos['total'] ?? 0 }} agendamentos</p>
                            </div>
                            <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Dia com Menor Movimento --}}
                    <div class="bg-gradient-to-r from-orange-500 to-red-600 rounded-lg shadow-sm p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-orange-100 text-sm font-medium">Dia de Menor Movimento</p>
                                <p class="text-xl font-bold">{{ $diaComMenosAgendamentos['dia'] ?? 'N/A' }}</p>
                                <p class="text-orange-100 text-xs">{{ $diaComMenosAgendamentos['total'] ?? 0 }} agendamentos</p>
                            </div>
                            <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Média Diária --}}
                    <div class="bg-gradient-to-r from-blue-500 to-cyan-600 rounded-lg shadow-sm p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Média por Dia</p>
                                <p class="text-2xl font-bold">{{ number_format($mediaAgendamentos, 1) }}</p>
                                <p class="text-blue-100 text-xs">agendamentos/dia</p>
                            </div>
                            <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabela de Dados --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                            </svg>
                            Dados por Dia
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Ranking dos dias da semana</p>
                    </div>
                    
                    <div class="p-6">
                        @if(count($diasPico) > 0)
                            <div class="space-y-4">
                                @foreach(collect($diasPico)->sortByDesc('total') as $index => $dia)
                                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full 
                                                       {{ $index == 0 ? 'bg-yellow-100 text-yellow-800' : ($index == 1 ? 'bg-gray-100 text-gray-800' : ($index == 2 ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800')) }} 
                                                       text-sm font-bold">
                                                {{ $index + 1 }}º
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $dia['dia'] }}</p>
                                            <p class="text-xs text-gray-500">
                                                @php
                                                    $percentual = $totalAgendamentos > 0 ? ($dia['total'] / $totalAgendamentos) * 100 : 0;
                                                @endphp
                                                {{ number_format($percentual, 1) }}% do total
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-gray-900">{{ $dia['total'] }}</p>
                                        <p class="text-xs text-gray-500">agendamentos</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <p class="text-sm text-gray-500 mb-2">Nenhum dado encontrado</p>
                                <p class="text-xs text-gray-400">Selecione um período com agendamentos</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Gráfico Moderno --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Distribuição de Agendamentos por Dia
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Visualização gráfica dos picos de demanda</p>
                    </div>
                    
                    <div class="p-6">
                        <div class="w-full overflow-hidden">
                            <div class="relative" style="height: 400px;">
                                <canvas id="graficoDiasPico"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let graficoDiasPico = null;

    function criarGraficoDiasPico(labels, valores) {
        const canvas = document.getElementById('graficoDiasPico');
        if (!canvas) return;

        // Destruir gráfico existente se houver
        if (graficoDiasPico) {
            graficoDiasPico.destroy();
        }

        // Cores gradient para cada dia
        const cores = [
            'rgba(99, 102, 241, 0.8)',   // Indigo
            'rgba(139, 92, 246, 0.8)',   // Purple  
            'rgba(59, 130, 246, 0.8)',   // Blue
            'rgba(16, 185, 129, 0.8)',   // Emerald
            'rgba(245, 101, 101, 0.8)',  // Red
            'rgba(251, 146, 60, 0.8)',   // Orange
            'rgba(34, 197, 94, 0.8)'     // Green
        ];

        const coresBorder = [
            'rgba(99, 102, 241, 1)',
            'rgba(139, 92, 246, 1)',
            'rgba(59, 130, 246, 1)',
            'rgba(16, 185, 129, 1)',
            'rgba(245, 101, 101, 1)',
            'rgba(251, 146, 60, 1)',
            'rgba(34, 197, 94, 1)'
        ];

        // Criar novo gráfico
        graficoDiasPico = new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Agendamentos',
                    data: valores,
                    backgroundColor: cores.slice(0, labels.length),
                    borderColor: coresBorder.slice(0, labels.length),
                    borderWidth: 2,
                    borderRadius: {
                        topLeft: 8,
                        topRight: 8,
                        bottomLeft: 0,
                        bottomRight: 0
                    },
                    borderSkipped: false,
                    hoverBackgroundColor: cores.slice(0, labels.length).map(cor => cor.replace('0.8', '1')),
                    hoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: 'white',
                        bodyColor: 'white',
                        borderColor: 'rgba(99, 102, 241, 1)',
                        borderWidth: 2,
                        cornerRadius: 8,
                        displayColors: false,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        padding: 12,
                        callbacks: {
                            title: function(context) {
                                return context[0].label;
                            },
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentual = ((context.parsed.y / total) * 100).toFixed(1);
                                return `${context.parsed.y} agendamentos (${percentual}%)`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(156, 163, 175, 0.2)',
                            drawBorder: false
                        },
                        border: {
                            display: false
                        },
                        ticks: {
                            color: '#6B7280',
                            font: {
                                size: 12,
                                family: "'Inter', sans-serif"
                            },
                            stepSize: 1,
                            padding: 8
                        },
                        title: {
                            display: true,
                            text: 'Número de Agendamentos',
                            color: '#374151',
                            font: {
                                size: 13,
                                weight: '600',
                                family: "'Inter', sans-serif"
                            },
                            padding: 16
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        border: {
                            display: false
                        },
                        ticks: {
                            color: '#6B7280',
                            font: {
                                size: 12,
                                weight: '500',
                                family: "'Inter', sans-serif"
                            },
                            maxRotation: 0,
                            padding: 8
                        }
                    }
                },
                layout: {
                    padding: {
                        top: 20,
                        right: 20,
                        bottom: 10,
                        left: 10
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                }
            }
        });
    }

    // Criar gráfico inicialmente
    document.addEventListener('DOMContentLoaded', function() {
        const labels = @json($diasLabels);
        const valores = @json($diasValores);
        criarGraficoDiasPico(labels, valores);
    });

    // Escutar evento Livewire para atualizar gráfico
    document.addEventListener('livewire:init', () => {
        Livewire.on('atualizar-grafico-dias-pico', (event) => {
            criarGraficoDiasPico(event.labels, event.valores);
        });
    });

    // Responsividade do gráfico
    window.addEventListener('resize', function() {
        if (graficoDiasPico) {
            graficoDiasPico.resize();
        }
    });
</script>

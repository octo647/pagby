<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-100">
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
                                    <span class="text-indigo-600 text-sm font-medium">Horários de Pico</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                        <svg class="w-7 h-7 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Análise de Horários de Pico
                    </h1>
                    <p class="text-gray-600 mt-1">Identifique os horários com maior concentração de agendamentos</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Cards Resumo --}}
            <div class="lg:col-span-3">
                @php
                    $totalAgendamentos = collect($horarios)->sum('total');
                    $horarioPico = collect($horarios)->sortByDesc('total')->first();
                    $horarioMenor = collect($horarios)->sortBy('total')->first();
                    $mediaHorarios = count($horarios) > 0 ? $totalAgendamentos / count($horarios) : 0;
                @endphp
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    {{-- Total de Agendamentos --}}
                    <div class="bg-gradient-to-r from-blue-500 to-cyan-600 rounded-lg shadow-sm p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Total de Agendamentos</p>
                                <p class="text-2xl font-bold">{{ number_format($totalAgendamentos) }}</p>
                            </div>
                            <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Horário de Pico --}}
                    <div class="bg-gradient-to-r from-red-500 to-pink-600 rounded-lg shadow-sm p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-red-100 text-sm font-medium">Horário de Maior Pico</p>
                                <p class="text-xl font-bold">{{ str_pad($horarioPico->hora ?? 0, 2, '0', STR_PAD_LEFT) }}:00</p>
                                <p class="text-red-100 text-xs">{{ $horarioPico->total ?? 0 }} agendamentos</p>
                            </div>
                            <div class="bg-red-400 bg-opacity-30 rounded-full p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Horário com Menor Movimento --}}
                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg shadow-sm p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium">Horário de Menor Movimento</p>
                                <p class="text-xl font-bold">{{ str_pad($horarioMenor->hora ?? 0, 2, '0', STR_PAD_LEFT) }}:00</p>
                                <p class="text-green-100 text-xs">{{ $horarioMenor->total ?? 0 }} agendamentos</p>
                            </div>
                            <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Média por Horário --}}
                    <div class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-lg shadow-sm p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">Média por Horário</p>
                                <p class="text-2xl font-bold">{{ number_format($mediaHorarios, 1) }}</p>
                                <p class="text-purple-100 text-xs">agendamentos/hora</p>
                            </div>
                            <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lista de Horários --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Horários do Dia
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Ranking dos horários mais movimentados</p>
                    </div>
                    
                    <div class="p-6">
                        @if(count($horarios) > 0)
                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                @foreach(collect($horarios)->sortByDesc('total') as $index => $horario)
                                @php
                                    $horaFormatada = str_pad($horario->hora, 2, '0', STR_PAD_LEFT) . ':00';
                                    $percentual = $totalAgendamentos > 0 ? ($horario->total / $totalAgendamentos) * 100 : 0;
                                    $isHighPeak = $index < 3; // Top 3 como picos altos
                                @endphp
                                <div class="flex items-center justify-between p-3 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-200 hover:shadow-md transition-all">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center
                                                       {{ $isHighPeak ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">{{ $horaFormatada }}</p>
                                            <div class="flex items-center space-x-2">
                                                <p class="text-xs text-gray-500">{{ number_format($percentual, 1) }}% do total</p>
                                                @if($isHighPeak)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                        Pico
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-gray-900">{{ $horario->total }}</p>
                                        <div class="w-16 bg-gray-200 rounded-full h-1.5 mt-1">
                                            <div class="bg-gradient-to-r {{ $isHighPeak ? 'from-red-400 to-red-600' : 'from-blue-400 to-blue-600' }} h-1.5 rounded-full" 
                                                 style="width: {{ min($percentual, 100) }}%"></div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm text-gray-500 mb-2">Nenhum dado encontrado</p>
                                <p class="text-xs text-gray-400">Sem agendamentos para análise</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Gráfico Moderno --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Distribuição de Agendamentos por Horário
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Visualização gráfica dos horários de pico</p>
                    </div>
                    
                    <div class="p-6">
                        <div class="w-full overflow-hidden">
                            <div class="relative" style="height: 400px;">
                                <canvas id="graficoPico"></canvas>
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
    let graficoHorariosPico = null;

    function criarGraficoHorariosPico(labels, valores) {
        const canvas = document.getElementById('graficoPico');
        if (!canvas) return;

        // Destruir gráfico existente se houver
        if (graficoHorariosPico) {
            graficoHorariosPico.destroy();
        }

        // Função para determinar a cor baseada no valor (picos de demanda)
        function getCorPorIntensidade(valor, maxValor) {
            const intensidade = valor / maxValor;
            if (intensidade > 0.7) return { bg: 'rgba(239, 68, 68, 0.8)', border: 'rgba(239, 68, 68, 1)' }; // Vermelho - Alto pico
            if (intensidade > 0.5) return { bg: 'rgba(251, 146, 60, 0.8)', border: 'rgba(251, 146, 60, 1)' }; // Laranja - Pico médio
            if (intensidade > 0.3) return { bg: 'rgba(59, 130, 246, 0.8)', border: 'rgba(59, 130, 246, 1)' }; // Azul - Movimento normal
            return { bg: 'rgba(34, 197, 94, 0.8)', border: 'rgba(34, 197, 94, 1)' }; // Verde - Movimento baixo
        }

        const maxValor = Math.max(...valores);
        const cores = valores.map(valor => getCorPorIntensidade(valor, maxValor));

        // Criar novo gráfico
        graficoHorariosPico = new Chart(canvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Agendamentos',
                    data: valores,
                    backgroundColor: cores.map(cor => cor.bg),
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: cores.map(cor => cor.border),
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointHoverBorderWidth: 3
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
                        borderColor: 'rgba(59, 130, 246, 1)',
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
                                return context[0].label + ':00h';
                            },
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentual = ((context.parsed.y / total) * 100).toFixed(1);
                                const intensidade = context.parsed.y / maxValor;
                                let status = 'Movimento baixo';
                                if (intensidade > 0.7) status = 'Pico alto';
                                else if (intensidade > 0.5) status = 'Pico médio';
                                else if (intensidade > 0.3) status = 'Movimento normal';
                                
                                return `${context.parsed.y} agendamentos (${percentual}%) - ${status}`;
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
                            padding: 8,
                            callback: function(value, index) {
                                return this.getLabelForValue(value) + 'h';
                            }
                        },
                        title: {
                            display: true,
                            text: 'Horário do Dia',
                            color: '#374151',
                            font: {
                                size: 13,
                                weight: '600',
                                family: "'Inter', sans-serif"
                            },
                            padding: 16
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
                    duration: 1500,
                    easing: 'easeOutQuart'
                }
            }
        });
    }

    // Criar gráfico inicialmente
    document.addEventListener('DOMContentLoaded', function() {
        const dados = @json([
            'horasLabels' => $horasLabels,
            'horasValores' => $horasValores
        ]);
        criarGraficoHorariosPico(dados.horasLabels, dados.horasValores);
    });

    // Escutar evento Livewire para atualizar gráfico
    document.addEventListener('livewire:init', () => {
        Livewire.on('atualizar-grafico-horarios-pico', (event) => {
            criarGraficoHorariosPico(event.labels, event.valores);
        });
    });

    // Responsividade do gráfico
    window.addEventListener('resize', function() {
        if (graficoHorariosPico) {
            graficoHorariosPico.resize();
        }
    });
</script>

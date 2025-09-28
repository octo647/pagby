<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <!-- Header com Breadcrumb -->
    <div class="bg-white shadow-sm border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex items-center space-x-2 text-sm text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <span>Proprietário</span>
                <span>/</span>
                <span class="text-indigo-600 font-semibold">Faturamento Mensal</span>
            </nav>
        </div>
    </div>

    <!-- Container Principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Cabeçalho do Dashboard -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 mb-2">Faturamento Mensal</h1>
                    <p class="text-slate-600">Acompanhe a evolução do faturamento da empresa ao longo dos meses</p>
                </div>
            </div>
        </div>

        @php
            $faturamentoTotal = array_sum($faturamentoMensal);
            $mediaMensal = count($faturamentoMensal) > 0 ? $faturamentoTotal / count($faturamentoMensal) : 0;
            $melhorMes = count($faturamentoMensal) > 0 ? max($faturamentoMensal) : 0;
            $melhorMesNome = count($faturamentoMensal) > 0 ? array_search($melhorMes, $faturamentoMensal) : '';
            $piorMes = count($faturamentoMensal) > 0 ? min($faturamentoMensal) : 0;
        @endphp

        <!-- Cards de Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Faturamento Total -->
            <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 mb-1">Faturamento Total</p>
                        <p class="text-3xl font-bold text-slate-900">R$ {{ number_format($faturamentoTotal, 2, ',', '.') }}</p>
                        <p class="text-sm text-slate-500 mt-1">{{ count($faturamentoMensal) }} meses</p>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 p-3 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Média Mensal -->
            <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 mb-1">Média Mensal</p>
                        <p class="text-3xl font-bold text-blue-600">R$ {{ number_format($mediaMensal, 2, ',', '.') }}</p>
                        <p class="text-sm text-slate-500 mt-1">Por mês</p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-3 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Melhor Mês -->
            <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 mb-1">Melhor Mês</p>
                        <p class="text-3xl font-bold text-green-600">R$ {{ number_format($melhorMes, 2, ',', '.') }}</p>
                        @if($melhorMesNome)
                            <p class="text-sm text-slate-500 mt-1">{{ \Carbon\Carbon::createFromFormat('Y-m', $melhorMesNome)->format('m/Y') }}</p>
                        @endif
                    </div>
                    <div class="bg-gradient-to-br from-green-500 to-green-600 p-3 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Crescimento -->
            <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 mb-1">Menor Valor</p>
                        <p class="text-3xl font-bold text-orange-600">R$ {{ number_format($piorMes, 2, ',', '.') }}</p>
                        @php
                            $variacao = $melhorMes > 0 && $piorMes > 0 ? (($melhorMes - $piorMes) / $piorMes) * 100 : 0;
                        @endphp
                        <p class="text-sm text-slate-500 mt-1">{{ number_format($variacao, 1) }}% variação</p>
                    </div>
                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-3 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Layout principal com gráfico e tabela -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Gráfico (2/3 da tela) -->
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-lg border border-slate-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">Evolução do Faturamento</h3>
                        <p class="text-slate-600 text-sm mt-1">Análise temporal dos resultados mensais</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center space-x-2 text-sm">
                            <div class="w-3 h-3 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500"></div>
                            <span class="text-slate-600 font-medium">Receita Mensal</span>
                        </div>
                        <div class="bg-gradient-to-r from-indigo-100 to-purple-100 p-3 rounded-xl">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <!-- Área do gráfico com gradiente de fundo -->
                <div class="relative bg-gradient-to-br from-slate-50/50 via-indigo-50/30 to-purple-50/30 rounded-xl p-4" style="height: 400px;">
                    <canvas id="graficoFaturamento"></canvas>
                </div>
                
                <!-- Indicadores e informações adicionais -->
                <div class="flex flex-wrap items-center justify-between mt-4 pt-4 border-t border-slate-100">
                    <div class="flex items-center space-x-4 text-xs">
                        <div class="flex items-center space-x-2 text-slate-600">
                            <div class="w-2 h-2 rounded-full bg-green-500"></div>
                            <span>Crescimento</span>
                        </div>
                        <div class="flex items-center space-x-2 text-slate-600">
                            <div class="w-2 h-2 rounded-full bg-red-500"></div>
                            <span>Declínio</span>
                        </div>
                        <div class="flex items-center space-x-2 text-slate-600">
                            <div class="w-2 h-2 rounded-full bg-yellow-500"></div>
                            <span>Estável</span>
                        </div>
                    </div>
                    <div class="text-xs text-slate-400 flex items-center space-x-2">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Atualizado: {{ now()->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Tabela de dados (1/3 da tela) -->
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Dados Mensais</h3>
                        <p class="text-slate-600 text-sm mt-1">Valores detalhados</p>
                    </div>
                </div>
                
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($faturamentoMensal as $mes => $total)
                        @php
                            $porcentagemDoMelhor = $melhorMes > 0 ? ($total / $melhorMes) * 100 : 0;
                        @endphp
                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100 hover:bg-slate-100 transition-colors duration-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v10a2 2 0 002 2h4a2 2 0 002-2V11a2 2 0 00-2-2H10a2 2 0 00-2 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-slate-900">
                                        {{ \Carbon\Carbon::createFromFormat('Y-m', $mes)->locale('pt_BR')->isoFormat('MMM/YYYY') }}
                                    </div>
                                    <div class="text-xs text-slate-500">
                                        {{ number_format($porcentagemDoMelhor, 1) }}% do pico
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-slate-900">
                                    R$ {{ number_format($total, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-slate-500">
                                    {{ number_format($total, 2, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('graficoFaturamento').getContext('2d');
        
        // Dados do gráfico
        const labels = {!! json_encode(array_map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->locale('pt_BR')->isoFormat('MMM/YY'), array_keys($faturamentoMensal))) !!};
        const data = {!! json_encode(array_values($faturamentoMensal)) !!};
        
        // Função para criar gradiente
        function createGradient(ctx, chartArea) {
            const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
            gradient.addColorStop(0, 'rgba(79, 70, 229, 0.1)');
            gradient.addColorStop(0.5, 'rgba(79, 70, 229, 0.6)');
            gradient.addColorStop(1, 'rgba(79, 70, 229, 0.9)');
            return gradient;
        }
        
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Faturamento Mensal',
                    data: data,
                    borderColor: 'rgba(79, 70, 229, 1)',
                    backgroundColor: function(context) {
                        const chart = context.chart;
                        const {ctx, chartArea} = chart;
                        if (!chartArea) return null;
                        return createGradient(ctx, chartArea);
                    },
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(79, 70, 229, 1)',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: 'rgba(79, 70, 229, 1)',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 4
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
                        backgroundColor: 'rgba(30, 41, 59, 0.95)',
                        titleColor: 'white',
                        bodyColor: 'white',
                        borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 2,
                        cornerRadius: 12,
                        displayColors: false,
                        titleFont: {
                            size: 14,
                            weight: 'bold',
                            family: "'Inter', sans-serif"
                        },
                        bodyFont: {
                            size: 13,
                            family: "'Inter', sans-serif"
                        },
                        padding: 12,
                        callbacks: {
                            title: function(context) {
                                return context[0].label;
                            },
                            label: function(context) {
                                const value = context.parsed.y;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return [
                                    `Faturamento: R$ ${value.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`,
                                    `Representa: ${percentage}% do total`
                                ];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(148, 163, 184, 0.1)',
                            drawBorder: false
                        },
                        border: {
                            display: false
                        },
                        ticks: {
                            color: '#64748B',
                            font: {
                                size: 12,
                                family: "'Inter', sans-serif"
                            },
                            padding: 12,
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR', {
                                    notation: 'compact',
                                    compactDisplay: 'short'
                                });
                            }
                        },
                        title: {
                            display: true,
                            text: 'Faturamento (R$)',
                            color: '#475569',
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
                            color: '#64748B',
                            font: {
                                size: 12,
                                weight: '500',
                                family: "'Inter', sans-serif"
                            },
                            maxRotation: 0,
                            padding: 8
                        },
                        title: {
                            display: true,
                            text: 'Período',
                            color: '#475569',
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
                    duration: 2000,
                    easing: 'easeOutQuart'
                },
                elements: {
                    point: {
                        hoverRadius: 8
                    }
                }
            }
        });

        // Responsividade do gráfico
        window.addEventListener('resize', function() {
            chart.resize();
        });
    });
</script>
</div>

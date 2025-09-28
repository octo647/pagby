<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-100/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Cabeçalho --}}
        <div class="mb-8">
            {{-- Breadcrumb --}}
            <nav class="flex items-center space-x-2 text-sm text-slate-600 mb-4">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition-colors duration-200">Dashboard</a>
                <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-slate-800 font-medium">Análise de Retenção</span>
            </nav>

            {{-- Título principal --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 mb-2">🔄 Análise de Retenção de Clientes</h1>
                    <p class="text-slate-600">Acompanhe a evolução de clientes novos vs. recorrentes nos últimos 12 meses</p>
                </div>
                <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                    <div class="bg-gradient-to-r from-emerald-500 to-blue-600 p-3 rounded-xl shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Estatísticas principais --}}
        @php
            $totalNovos = array_sum($retencaoNovos);
            $totalRecorrentes = array_sum($retencaoRecorrentes);
            $totalClientes = $totalNovos + $totalRecorrentes;
            $taxaRetencao = $totalClientes > 0 ? round(($totalRecorrentes / $totalClientes) * 100, 1) : 0;
            $mediaNovosPerMes = count($retencaoNovos) > 0 ? round($totalNovos / count($retencaoNovos), 1) : 0;
            $mediaRecorrentesPerMes = count($retencaoRecorrentes) > 0 ? round($totalRecorrentes / count($retencaoRecorrentes), 1) : 0;
            
            // Tendência (comparando primeiros vs últimos 3 meses)
            $primeiros3Novos = array_sum(array_slice($retencaoNovos, 0, 3));
            $ultimos3Novos = array_sum(array_slice($retencaoNovos, -3));
            $tendenciaNovos = $primeiros3Novos > 0 ? round((($ultimos3Novos - $primeiros3Novos) / $primeiros3Novos) * 100, 1) : 0;
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total de Clientes -->
            <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 mb-1">Total de Clientes</p>
                        <p class="text-3xl font-bold text-blue-600">{{ number_format($totalClientes) }}</p>
                        <p class="text-sm text-slate-500 mt-1">Últimos 12 meses</p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-3 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m3 5.197V9a3 3 0 00-6 0v2.25"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Taxa de Retenção -->
            <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 mb-1">Taxa de Retenção</p>
                        <p class="text-3xl font-bold text-emerald-600">{{ $taxaRetencao }}%</p>
                        <p class="text-sm text-slate-500 mt-1">Clientes recorrentes</p>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 p-3 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Novos Clientes -->
            <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 mb-1">Novos Clientes</p>
                        <p class="text-3xl font-bold text-green-600">{{ number_format($totalNovos) }}</p>
                        <div class="flex items-center mt-1">
                            @if($tendenciaNovos > 0)
                                <svg class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                                <span class="text-sm text-green-600">+{{ $tendenciaNovos }}%</span>
                            @elseif($tendenciaNovos < 0)
                                <svg class="w-4 h-4 text-red-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                </svg>
                                <span class="text-sm text-red-600">{{ $tendenciaNovos }}%</span>
                            @else
                                <span class="text-sm text-slate-500">Estável</span>
                            @endif
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-green-500 to-green-600 p-3 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Média Mensal -->
            <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 mb-1">Média Mensal</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $mediaNovosPerMes }}</p>
                        <p class="text-sm text-slate-500 mt-1">Novos/mês</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-3 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Layout principal --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            
            {{-- Gráfico principal (2/3 da tela) --}}
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-lg border border-slate-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">Evolução de Clientes</h3>
                        <p class="text-slate-600 text-sm mt-1">Comparação mensal entre novos e recorrentes</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2 text-sm">
                            <div class="w-4 h-4 rounded bg-gradient-to-r from-green-400 to-green-500"></div>
                            <span class="text-slate-700 font-medium">Novos</span>
                        </div>
                        <div class="flex items-center space-x-2 text-sm">
                            <div class="w-4 h-4 rounded bg-gradient-to-r from-blue-400 to-blue-500"></div>
                            <span class="text-slate-700 font-medium">Recorrentes</span>
                        </div>
                    </div>
                </div>
                
                <div class="relative bg-gradient-to-br from-slate-50/50 via-green-50/30 to-blue-50/30 rounded-xl p-4" style="height: 420px;">
                    <canvas id="graficoRetencao"></canvas>
                </div>
                
                {{-- Indicadores do gráfico --}}
                <div class="flex flex-wrap items-center justify-between mt-4 pt-4 border-t border-slate-100">
                    <div class="flex items-center space-x-4 text-xs">
                        <div class="flex items-center space-x-2 text-slate-600">
                            <div class="w-2 h-2 rounded-full bg-green-500"></div>
                            <span>Aquisição de novos clientes</span>
                        </div>
                        <div class="flex items-center space-x-2 text-slate-600">
                            <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                            <span>Retenção e fidelização</span>
                        </div>
                    </div>
                    <div class="text-xs text-slate-400 flex items-center space-x-2">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Período: {{ now()->subMonths(11)->format('m/Y') }} - {{ now()->format('m/Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- Análise detalhada (1/3 da tela) --}}
            <div class="space-y-6">
                {{-- Resumo da retenção --}}
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-slate-900">Resumo da Retenção</h3>
                        <div class="bg-gradient-to-r from-emerald-100 to-blue-100 p-2 rounded-lg">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 bg-slate-50 rounded-lg">
                            <span class="text-sm text-slate-600">Total de Novos</span>
                            <span class="font-bold text-green-600">{{ number_format($totalNovos) }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 bg-slate-50 rounded-lg">
                            <span class="text-sm text-slate-600">Total Recorrentes</span>
                            <span class="font-bold text-blue-600">{{ number_format($totalRecorrentes) }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 bg-gradient-to-r from-emerald-50 to-blue-50 rounded-lg border">
                            <span class="text-sm font-medium text-slate-700">Taxa de Retenção</span>
                            <span class="font-bold text-lg text-emerald-600">{{ $taxaRetencao }}%</span>
                        </div>
                    </div>
                </div>

                {{-- Insights de performance --}}
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-slate-900">Performance</h3>
                        <div class="bg-gradient-to-r from-purple-100 to-pink-100 p-2 rounded-lg">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="p-3 bg-slate-50 rounded-lg">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs text-slate-500">Média Novos/Mês</span>
                                <span class="text-sm font-bold text-slate-900">{{ $mediaNovosPerMes }}</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-1.5">
                                <div class="bg-gradient-to-r from-green-400 to-green-500 h-1.5 rounded-full" style="width: {{ $totalNovos > 0 ? min(($mediaNovosPerMes / max($retencaoNovos)) * 100, 100) : 0 }}%"></div>
                            </div>
                        </div>
                        
                        <div class="p-3 bg-slate-50 rounded-lg">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs text-slate-500">Média Recorrentes/Mês</span>
                                <span class="text-sm font-bold text-slate-900">{{ $mediaRecorrentesPerMes }}</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-1.5">
                                <div class="bg-gradient-to-r from-blue-400 to-blue-500 h-1.5 rounded-full" style="width: {{ $totalRecorrentes > 0 ? min(($mediaRecorrentesPerMes / max($retencaoRecorrentes)) * 100, 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Melhor e pior mês --}}
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-100">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">Extremos do Período</h3>
                    
                    @php
                        $melhorMesIndex = array_search(max($retencaoNovos), $retencaoNovos);
                        $piorMesIndex = array_search(min($retencaoNovos), $retencaoNovos);
                        $melhorMes = $retencaoLabels[$melhorMesIndex] ?? 'N/A';
                        $piorMes = $retencaoLabels[$piorMesIndex] ?? 'N/A';
                    @endphp
                    
                    <div class="space-y-4">
                        <div class="p-3 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                    <span class="text-sm text-green-700 font-medium">Melhor Mês</span>
                                </div>
                                <span class="text-sm font-bold text-green-800">{{ $melhorMes }}</span>
                            </div>
                            <p class="text-xs text-green-600 mt-1">{{ max($retencaoNovos) }} novos clientes</p>
                        </div>
                        
                        <div class="p-3 bg-gradient-to-r from-red-50 to-orange-50 rounded-lg border border-red-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                    <span class="text-sm text-red-700 font-medium">Menor Mês</span>
                                </div>
                                <span class="text-sm font-bold text-red-800">{{ $piorMes }}</span>
                            </div>
                            <p class="text-xs text-red-600 mt-1">{{ min($retencaoNovos) }} novos clientes</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Insights e explicações --}}
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-100">
            <div class="flex items-start space-x-4">
                <div class="bg-gradient-to-r from-blue-100 to-indigo-100 p-3 rounded-xl flex-shrink-0">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-slate-900 mb-3">💡 Insights sobre Retenção de Clientes</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-slate-600">
                        <div class="space-y-2">
                            <p class="font-medium text-slate-700">📊 Sobre os Dados:</p>
                            <ul class="space-y-1 text-xs">
                                <li>• <strong>Clientes Novos:</strong> Realizaram sua primeira compra no período analisado</li>
                                <li>• <strong>Clientes Recorrentes:</strong> Já haviam realizado compras anteriormente</li>
                                <li>• <strong>Taxa de Retenção:</strong> Percentual de clientes que retornaram</li>
                            </ul>
                        </div>
                        <div class="space-y-2">
                            <p class="font-medium text-slate-700">🎯 Como Usar:</p>
                            <ul class="space-y-1 text-xs">
                                <li>• Monitore a taxa de retenção mensalmente</li>
                                <li>• Alta retenção indica satisfação dos clientes</li>
                                <li>• Balance aquisição de novos com fidelização</li>
                                <li>• Investigue quedas na retenção</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script Chart.js modernizado --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('graficoRetencao');
            if (ctx) {
                const labels = @json($retencaoLabels);
                const novos = @json($retencaoNovos);
                const recorrentes = @json($retencaoRecorrentes);

                // Função para criar gradientes
                function createGradient(ctx, color1, color2) {
                    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                    gradient.addColorStop(0, color1);
                    gradient.addColorStop(1, color2);
                    return gradient;
                }

                const chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Clientes Novos',
                                data: novos,
                                backgroundColor: function(context) {
                                    const chart = context.chart;
                                    const {ctx, chartArea} = chart;
                                    if (!chartArea) return null;
                                    return createGradient(ctx, 'rgba(34, 197, 94, 0.8)', 'rgba(34, 197, 94, 0.4)');
                                },
                                borderColor: 'rgba(34, 197, 94, 1)',
                                borderWidth: 2,
                                borderRadius: 6,
                                borderSkipped: false
                            },
                            {
                                label: 'Clientes Recorrentes',
                                data: recorrentes,
                                backgroundColor: function(context) {
                                    const chart = context.chart;
                                    const {ctx, chartArea} = chart;
                                    if (!chartArea) return null;
                                    return createGradient(ctx, 'rgba(59, 130, 246, 0.8)', 'rgba(59, 130, 246, 0.4)');
                                },
                                borderColor: 'rgba(59, 130, 246, 1)',
                                borderWidth: 2,
                                borderRadius: 6,
                                borderSkipped: false
                            }
                        ]
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
                                display: false // Já temos legenda customizada
                            },
                            tooltip: {
                                backgroundColor: 'rgba(30, 41, 59, 0.95)',
                                titleColor: 'white',
                                bodyColor: 'white',
                                borderColor: 'rgba(79, 70, 229, 1)',
                                borderWidth: 2,
                                cornerRadius: 12,
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
                                        return 'Período: ' + context[0].label;
                                    },
                                    label: function(context) {
                                        const total = novos[context.dataIndex] + recorrentes[context.dataIndex];
                                        const percentage = total > 0 ? ((context.parsed.y / total) * 100).toFixed(1) : 0;
                                        return `${context.dataset.label}: ${context.parsed.y} (${percentage}%)`;
                                    },
                                    footer: function(context) {
                                        const index = context[0].dataIndex;
                                        const total = novos[index] + recorrentes[index];
                                        return `Total: ${total} clientes`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                stacked: true,
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
                                    maxRotation: 45,
                                    padding: 8
                                },
                                title: {
                                    display: true,
                                    text: 'Período (Mês/Ano)',
                                    color: '#475569',
                                    font: {
                                        size: 13,
                                        weight: '600',
                                        family: "'Inter', sans-serif"
                                    },
                                    padding: 16
                                }
                            },
                            y: {
                                stacked: true,
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
                                        return value + ' clientes';
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Número de Clientes',
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
                        }
                    }
                });

                // Responsividade
                window.addEventListener('resize', function() {
                    chart.resize();
                });
            }
        });
    </script>
</div>

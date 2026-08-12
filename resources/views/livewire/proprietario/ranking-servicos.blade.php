<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-3 rounded-xl shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Estatísticas resumidas --}}
        @php
            $totalSolicitacoes = array_sum($rankingServicos);
            $totalServicos = count($rankingServicos);
            $servicoMaisPopular = array_key_first($rankingServicos);
            $mediaSolicitacoes = $totalServicos > 0 ? round($totalSolicitacoes / $totalServicos, 1) : 0;
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total de Solicitações -->
            <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 mb-1">Total de Solicitações</p>
                        <p class="text-3xl font-bold text-blue-600">{{ number_format($totalSolicitacoes) }}</p>
                        <p class="text-sm text-slate-500 mt-1">Todos os serviços</p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-3 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Tipos de Serviços -->
            <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 mb-1">Tipos de Serviços</p>
                        <p class="text-3xl font-bold text-indigo-600">{{ $totalServicos }}</p>
                        <p class="text-sm text-slate-500 mt-1">Serviços únicos</p>
                    </div>
                    <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 p-3 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Serviço Mais Popular -->
            <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 mb-1">Mais Popular</p>
                        <p class="text-lg font-bold text-green-600 truncate">{{ $servicoMaisPopular ?? 'N/A' }}</p>
                        <p class="text-sm text-slate-500 mt-1">{{ $rankingServicos[$servicoMaisPopular] ?? 0 }} solicitações</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-500 to-green-600 p-3 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Média por Serviço -->
            <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 mb-1">Média por Serviço</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $mediaSolicitacoes }}</p>
                        <p class="text-sm text-slate-500 mt-1">Solicitações</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-3 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Layout principal: Ranking e Gráfico --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- Ranking dos Serviços (2/3 da tela) --}}
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-lg border border-slate-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">🏆 Top 10 Serviços</h3>
                        <p class="text-slate-600 text-sm mt-1">Ranking dos serviços mais solicitados</p>
                    </div>
                    <div class="bg-gradient-to-r from-yellow-100 to-orange-100 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>

                @if(empty($rankingServicos))
                    {{-- Estado vazio --}}
                    <div class="text-center py-12">
                        <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum serviço encontrado</h3>
                        <p class="text-gray-500">Ainda não há dados de serviços solicitados para exibir.</p>
                    </div>
                @else
                    {{-- Lista de serviços com design moderno --}}
                    <div class="space-y-4">
                        @foreach($rankingServicos as $position => $servico)
                            @php
                                $posicao = $loop->iteration;
                                $percentual = $totalSolicitacoes > 0 ? ($servico / $totalSolicitacoes) * 100 : 0;
                                
                                // Cores para as posições
                                $cores = [
                                    1 => ['bg' => 'from-yellow-400 to-yellow-500', 'text' => 'text-yellow-600', 'border' => 'border-yellow-200'],
                                    2 => ['bg' => 'from-gray-300 to-gray-400', 'text' => 'text-gray-600', 'border' => 'border-gray-200'],
                                    3 => ['bg' => 'from-amber-600 to-amber-700', 'text' => 'text-amber-600', 'border' => 'border-amber-200']
                                ];
                                $cor = $cores[$posicao] ?? ['bg' => 'from-slate-100 to-slate-200', 'text' => 'text-slate-600', 'border' => 'border-slate-200'];
                            @endphp
                            
                            <div class="group relative bg-gradient-to-r from-white to-slate-50 rounded-xl p-4 border {{ $cor['border'] }} hover:shadow-md transition-all duration-300">
                                <div class="flex items-center space-x-4">
                                    {{-- Posição --}}
                                    <div class="flex-shrink-0">
                                        @if($posicao <= 3)
                                            <div class="w-12 h-12 bg-gradient-to-br {{ $cor['bg'] }} rounded-full flex items-center justify-center shadow-lg">
                                                @if($posicao == 1)
                                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @elseif($posicao == 2)
                                                    <span class="text-white font-bold text-lg">2</span>
                                                @else
                                                    <span class="text-white font-bold text-lg">3</span>
                                                @endif
                                            </div>
                                        @else
                                            <div class="w-12 h-12 bg-gradient-to-br from-slate-200 to-slate-300 rounded-full flex items-center justify-center">
                                                <span class="text-slate-600 font-bold">{{ $posicao }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Informações do serviço --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="text-lg font-semibold text-slate-900 truncate">{{ $position }}</h4>
                                            <div class="flex items-center space-x-3">
                                                <span class="text-2xl font-bold {{ $cor['text'] }}">{{ $servico }}</span>
                                                <span class="text-sm text-slate-500 bg-slate-100 px-2 py-1 rounded-full">
                                                    {{ number_format($percentual, 1) }}%
                                                </span>
                                            </div>
                                        </div>
                                        
                                        {{-- Barra de progresso --}}
                                        <div class="w-full bg-slate-200 rounded-full h-2">
                                            <div class="bg-gradient-to-r {{ $cor['bg'] }} h-2 rounded-full transition-all duration-500 group-hover:shadow-md" 
                                                 style="width: {{ $percentual }}%"></div>
                                        </div>
                                        
                                        <div class="flex justify-between items-center mt-2 text-xs text-slate-500">
                                            <span>{{ $servico }} solicitações</span>
                                            @if($posicao <= 3)
                                                <span class="font-medium {{ $cor['text'] }}">
                                                    @if($posicao == 1) 🥇 Líder
                                                    @elseif($posicao == 2) 🥈 Vice-líder  
                                                    @else 🥉 3º Lugar
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Gráfico de Pizza (1/3 da tela) --}}
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Distribuição</h3>
                        <p class="text-slate-600 text-sm mt-1">Proporção dos serviços</p>
                    </div>
                </div>

                @if(!empty($rankingServicos))
                    <div class="relative h-64 mb-4">
                        <canvas id="graficoDistribuicao"></canvas>
                    </div>
                    
                    {{-- Legenda --}}
                    <div class="space-y-2 max-h-32 overflow-y-auto">
                        @foreach(array_slice($rankingServicos, 0, 5, true) as $servico => $total)
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 rounded-full" style="background-color: {{ ['#4F46E5', '#7C3AED', '#EC4899', '#EF4444', '#F59E0B'][$loop->index] }}"></div>
                                    <span class="text-slate-700 truncate max-w-24">{{ Str::limit($servico, 15) }}</span>
                                </div>
                                <span class="text-slate-500 font-medium">{{ $total }}</span>
                            </div>
                        @endforeach
                        
                        @if(count($rankingServicos) > 5)
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 rounded-full bg-gray-400"></div>
                                    <span class="text-slate-700">Outros</span>
                                </div>
                                <span class="text-slate-500 font-medium">{{ array_sum(array_slice($rankingServicos, 5)) }}</span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500 text-sm">Sem dados para o gráfico</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Informações adicionais --}}
        <div class="mt-8 bg-white rounded-2xl p-6 shadow-lg border border-slate-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-gradient-to-r from-blue-100 to-indigo-100 p-2 rounded-lg">
                        <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-900">Análise de Popularidade</p>
                        <p class="text-xs text-slate-500">Base: {{ number_format($totalSolicitacoes) }} solicitações analisadas</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-400">Atualizado em {{ now()->format('d/m/Y H:i') }}</p>
                    <p class="text-xs text-slate-500">Top {{ count($rankingServicos) }} serviços</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Script do Chart.js para gráfico de pizza --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(!empty($rankingServicos))
                const ctx = document.getElementById('graficoDistribuicao');
                if (ctx) {
                    const data = @json(array_values(array_slice($rankingServicos, 0, 5)));
                    const labels = @json(array_keys(array_slice($rankingServicos, 0, 5)));
                    
                    // Se há mais de 5 serviços, agrupa o resto em "Outros"
                    @if(count($rankingServicos) > 5)
                        data.push({{ array_sum(array_slice($rankingServicos, 5)) }});
                        labels.push('Outros');
                    @endif

                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: data,
                                backgroundColor: [
                                    '#4F46E5', // Indigo
                                    '#7C3AED', // Purple
                                    '#EC4899', // Pink
                                    '#EF4444', // Red
                                    '#F59E0B', // Amber
                                    '#9CA3AF'  // Gray (para "Outros")
                                ],
                                borderColor: '#ffffff',
                                borderWidth: 3,
                                hoverBorderWidth: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '60%',
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(30, 41, 59, 0.95)',
                                    titleColor: 'white',
                                    bodyColor: 'white',
                                    borderColor: '#4F46E5',
                                    borderWidth: 2,
                                    cornerRadius: 8,
                                    displayColors: true,
                                    callbacks: {
                                        label: function(context) {
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                                        }
                                    }
                                }
                            },
                            animation: {
                                animateRotate: true,
                                duration: 1500
                            }
                        }
                    });
                }
            @endif
        });
    </script>
</div>

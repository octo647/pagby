<div>
    {{-- Sistema de Avaliações com Filtros e Estatísticas --}}

    {{-- Filtros --}}
    <div class="mb-6 bg-white p-4 rounded-lg shadow-sm border">
        <h2 class="text-lg font-semibold mb-4 text-gray-800">Filtros de Avaliações</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            {{-- Filtro por Funcionário --}}
            <div>
                <label for="funcionario" class="block text-sm font-medium text-gray-700 mb-1">Profissional</label>
                <select wire:model.live="funcionarioSelecionado" id="funcionario" class="w-full border rounded-md px-3 py-2 text-sm">
                    <option value="">Todos os profissionais</option>
                    @foreach($funcionarios as $funcionario)
                        <option value="{{ $funcionario->id }}">{{ $funcionario->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Data Início --}}
            <div>
                <label for="dataInicio" class="block text-sm font-medium text-gray-700 mb-1">Data Inicial</label>
                <input type="date" wire:model.live="dataInicio" id="dataInicio" class="w-full border rounded-md px-3 py-2 text-sm">
            </div>

            {{-- Data Fim --}}
            <div>
                <label for="dataFim" class="block text-sm font-medium text-gray-700 mb-1">Data Final</label>
                <input type="date" wire:model.live="dataFim" id="dataFim" class="w-full border rounded-md px-3 py-2 text-sm">
            </div>

            {{-- Botão Limpar --}}
            <div class="flex items-end">
                <button wire:click="limparFiltros" class="w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm">
                    Limpar Filtros
                </button>
            </div>
        </div>
    </div>

    {{-- Estatísticas Gerais --}}
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="text-2xl font-bold text-blue-600">{{ $estatisticas['total_avaliacoes'] ?? 0 }}</div>
            <div class="text-sm text-blue-700">Total de Avaliações</div>
        </div>
        
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="text-2xl font-bold text-green-600">{{ $estatisticas['media_geral'] ?? 0 }}</div>
            <div class="text-sm text-green-700">Média Geral</div>
            <div class="text-xs text-green-600">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= floor($estatisticas['media_geral'] ?? 0))
                        ⭐
                    @else
                        ☆
                    @endif
                @endfor
            </div>
        </div>
        
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="text-2xl font-bold text-yellow-600">{{ $estatisticas['melhor_avaliacao'] ?? 0 }}</div>
            <div class="text-sm text-yellow-700">Melhor Nota</div>
        </div>
        
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="text-2xl font-bold text-red-600">{{ $estatisticas['pior_avaliacao'] ?? 0 }}</div>
            <div class="text-sm text-red-700">Pior Nota</div>
        </div>
    </div>

    {{-- Estatísticas por Funcionário --}}
    @if(isset($estatisticas['por_funcionario']) && $estatisticas['por_funcionario']->count() > 0)
    <div class="mb-6 bg-white p-4 rounded-lg shadow-sm border">
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Desempenho por Profissional</h3>
        
        {{-- Desktop --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Profissional</th>
                        <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Total</th>
                        <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Média</th>
                        <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">5 ⭐</th>
                        <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">1 ⭐</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($estatisticas['por_funcionario'] as $funcionario)
                    <tr class="border-t">
                        <td class="px-4 py-3 font-medium">{{ $funcionario['nome'] }}</td>
                        <td class="px-4 py-3 text-center">{{ $funcionario['total'] }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-semibold text-lg">{{ $funcionario['media'] }}</span>
                            <div class="text-xs">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($funcionario['media']))
                                        ⭐
                                    @else
                                        ☆
                                    @endif
                                @endfor
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center text-green-600 font-semibold">{{ $funcionario['cinco_estrelas'] }}</td>
                        <td class="px-4 py-3 text-center text-red-600 font-semibold">{{ $funcionario['uma_estrela'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile --}}
        <div class="md:hidden space-y-3">
            @foreach($estatisticas['por_funcionario'] as $funcionario)
            <div class="bg-gray-50 rounded-lg p-3 border">
                <div class="flex justify-between items-start mb-2">
                    <div class="font-medium text-gray-800">{{ $funcionario['nome'] }}</div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-blue-600">{{ $funcionario['media'] }}</div>
                        <div class="text-xs">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($funcionario['media']))
                                    ⭐
                                @else
                                    ☆
                                @endif
                            @endfor
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-3 gap-2 text-sm">
                    <div class="text-center">
                        <div class="font-semibold">{{ $funcionario['total'] }}</div>
                        <div class="text-gray-600">Total</div>
                    </div>
                    <div class="text-center">
                        <div class="font-semibold text-green-600">{{ $funcionario['cinco_estrelas'] }}</div>
                        <div class="text-gray-600">5⭐</div>
                    </div>
                    <div class="text-center">
                        <div class="font-semibold text-red-600">{{ $funcionario['uma_estrela'] }}</div>
                        <div class="text-gray-600">1⭐</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Lista de Avaliações --}}
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Avaliações Detalhadas</h3>
        </div>

        {{-- Desktop - Tabela --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Cliente</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Serviço</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Profissional</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Nota</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Comentário</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Data</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($avaliacoes as $avaliacao)
                        <tr class="border-t">
                            <td class="px-4 py-3">{{ $avaliacao->user->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $avaliacao->appointment->services ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $avaliacao->appointment->employee->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center items-center space-x-1">
                                    <span class="font-semibold">{{ $avaliacao->avaliacao }}</span>
                                    <div class="text-yellow-400">
                                        @for($i = 1; $i <= $avaliacao->avaliacao; $i++)
                                            ⭐
                                        @endfor
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 max-w-xs truncate">{{ $avaliacao->comentario ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-sm">{{ \Carbon\Carbon::parse($avaliacao->created_at)->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                Nenhuma avaliação encontrada para os critérios selecionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile - Cards --}}
        <div class="md:hidden p-4 space-y-4">
            @forelse($avaliacoes as $avaliacao)
                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                    {{-- Cabeçalho --}}
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <div class="font-medium text-gray-800">{{ $avaliacao->user->name ?? '-' }}</div>
                            <div class="text-sm text-gray-600">{{ $avaliacao->appointment->employee->name ?? '-' }}</div>
                        </div>
                        <div class="text-right">
                            <div class="flex items-center space-x-1">
                                <span class="font-bold text-lg">{{ $avaliacao->avaliacao }}</span>
                                <div class="text-yellow-400 text-sm">
                                    @for($i = 1; $i <= $avaliacao->avaliacao; $i++)
                                        ⭐
                                    @endfor
                                </div>
                            </div>
                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($avaliacao->created_at)->format('d/m/Y') }}</div>
                        </div>
                    </div>

                    {{-- Serviço --}}
                    <div class="mb-2">
                        <span class="text-sm font-medium text-gray-600">Serviço:</span>
                        <span class="text-sm text-gray-800">{{ $avaliacao->appointment->services ?? '-' }}</span>
                    </div>

                    {{-- Comentário --}}
                    @if($avaliacao->comentario)
                    <div class="mt-2 p-2 bg-white rounded border-l-4 border-blue-500">
                        <div class="text-sm text-gray-700">"{{ $avaliacao->comentario }}"</div>
                    </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <div class="text-4xl mb-2">⭐</div>
                    <p>Nenhuma avaliação encontrada para os critérios selecionados.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

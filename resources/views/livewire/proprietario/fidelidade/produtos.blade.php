<div class="space-y-6">
    <div>
        <h3 class="text-lg font-semibold text-gray-900">Produtos Mais Vendidos</h3>
        <p class="text-sm text-gray-600 mt-1">Ranking dos produtos que mais geram recompensas</p>
    </div>

    @if($produtosMaisVendidos->isEmpty())
        <div class="bg-gray-50 rounded-xl border-2 border-dashed border-gray-300 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma venda registrada</h3>
            <p class="mt-1 text-sm text-gray-500">Produtos vendidos aparecerão aqui automaticamente</p>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Posição
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Produto
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Categoria
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Preço
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estoque
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Vendas
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Faturamento Estimado
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($produtosMaisVendidos as $index => $produto)
                            <tr class="hover:bg-gray-50 transition">
                                {{-- Posição com Medalha --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($index === 0)
                                            <span class="text-2xl">🥇</span>
                                        @elseif($index === 1)
                                            <span class="text-2xl">🥈</span>
                                        @elseif($index === 2)
                                            <span class="text-2xl">🥉</span>
                                        @else
                                            <span class="text-lg font-semibold text-gray-500">{{ $index + 1 }}º</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Produto --}}
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $produto->produto_nome }}</div>
                                    @if($produto->observacoes)
                                        <div class="text-xs text-gray-500 mt-1">{{ Str::limit($produto->observacoes, 50) }}</div>
                                    @endif
                                </td>

                                {{-- Categoria --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($produto->categoria)
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                            {{ $produto->categoria }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>

                                {{-- Preço --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">
                                        R$ {{ number_format($produto->preco_unitario, 2, ',', '.') }}
                                    </div>
                                </td>

                                {{-- Estoque --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-gray-900">{{ $produto->quantidade_atual }}</span>
                                        @if($produto->quantidade_atual <= $produto->quantidade_minima)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                ⚠️ Baixo
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Total Vendas --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-bold text-indigo-600">{{ $produto->total_vendas }}</div>
                                        <div class="ml-2 w-20 bg-gray-200 rounded-full h-2">
                                            <div class="bg-indigo-600 h-2 rounded-full" 
                                                 style="width: {{ min(100, ($produto->total_vendas / max($produtosMaisVendidos->first()->total_vendas, 1)) * 100) }}%">
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Faturamento Estimado --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-green-600">
                                        R$ {{ number_format($produto->preco_unitario * $produto->total_vendas, 2, ',', '.') }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Resumo --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                <p class="text-sm font-medium text-blue-900">Total de Produtos Vendidos</p>
                <p class="text-2xl font-bold text-blue-700 mt-1">{{ $produtosMaisVendidos->sum('total_vendas') }}</p>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                <p class="text-sm font-medium text-green-900">Faturamento Total (Produtos)</p>
                <p class="text-2xl font-bold text-green-700 mt-1">
                    R$ {{ number_format($produtosMaisVendidos->sum(fn($p) => $p->preco_unitario * $p->total_vendas), 2, ',', '.') }}
                </p>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                <p class="text-sm font-medium text-purple-900">Ticket Médio</p>
                <p class="text-2xl font-bold text-purple-700 mt-1">
                    @php
                        $totalVendas = $produtosMaisVendidos->sum('total_vendas');
                        $faturamento = $produtosMaisVendidos->sum(fn($p) => $p->preco_unitario * $p->total_vendas);
                        $ticketMedio = $totalVendas > 0 ? $faturamento / $totalVendas : 0;
                    @endphp
                    R$ {{ number_format($ticketMedio, 2, ',', '.') }}
                </p>
            </div>
        </div>
    @endif
</div>

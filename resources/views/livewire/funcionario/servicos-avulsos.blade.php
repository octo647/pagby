<div class="p-6 md:p-10 bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen">
    <div class="max-w-3xl mx-auto">
        <h2 class="text-2xl md:text-3xl font-bold text-blue-900 mb-8 flex items-center gap-2">
            <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 0V4m0 16v-4m8-4h-4m-8 0H4"/></svg>
            Honorários - Serviços Avulsos
        </h2>
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8 flex flex-col md:flex-row gap-4 md:items-end">
            <div class="flex flex-col w-full">
                <label class="font-semibold text-blue-700 mb-1">Período (início)</label>
                <input type="date" wire:model.live="periodoInicio" class="border border-blue-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200 bg-white shadow-sm">
            </div>
            <div class="flex flex-col w-full">
                <label class="font-semibold text-blue-700 mb-1">Período (fim)</label>
                <input type="date" wire:model.live="periodoFim" class="border border-blue-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200 bg-white shadow-sm">
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-blue-800 mb-4">Resumo dos Ganhos</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 rounded-lg p-4 flex flex-col items-center">
                    <span class="text-sm text-blue-700 font-medium mb-1">Serviços Executados</span>
                    <span class="text-2xl font-bold text-blue-900">R$ {{ number_format($totalServicos, 2, ',', '.') }}</span>
                </div>
                <div class="bg-blue-50 rounded-lg p-4 flex flex-col items-center">
                    <span class="text-sm text-blue-700 font-medium mb-1">Produtos Vendidos</span>
                    <span class="text-2xl font-bold text-blue-900">R$ {{ number_format($totalProdutos, 2, ',', '.') }}</span>
                </div>
                <div class="bg-green-50 rounded-lg p-4 flex flex-col items-center">
                    <span class="text-sm text-green-700 font-medium mb-1">Total a Receber</span>
                    <span class="text-2xl font-bold text-green-900">R$ {{ number_format($totalReceber, 2, ',', '.') }}</span>
                </div>
            </div>
            <h4 class="text-md font-semibold text-blue-700 mb-2">Detalhamento</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-blue-100">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-blue-700 uppercase">Tipo</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-blue-700 uppercase">Descrição</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-blue-700 uppercase">Valor</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-blue-50">
                        @foreach($servicos as $servico)
                            <tr>
                                <td class="px-4 py-2 text-blue-800 font-medium">Serviço</td>
                                <td class="px-4 py-2">{{ $servico->service->service ?? '-' }}</td>
                                <td class="px-4 py-2">R$ {{ number_format($servico->subtotal, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        @foreach($produtos as $produto)
                            <tr>
                                <td class="px-4 py-2 text-blue-800 font-medium">Produto</td>
                                <td class="px-4 py-2">{{ $produto->descricao ?? $produto->produto_nome ?? '-' }}</td>
                                <td class="px-4 py-2">R$ {{ number_format($produto->subtotal, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        @if(count($servicos) == 0 && count($produtos) == 0)
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-blue-400">Nenhum serviço ou produto encontrado no período.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

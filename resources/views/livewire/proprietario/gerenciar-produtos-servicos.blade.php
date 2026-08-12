<div class="p-6">
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Produtos Recomendados: {{ $service->service }}
        </h2>
        <p class="text-gray-600 mt-2">
            Configure quais produtos serão sugeridos automaticamente aos clientes que agendarem este serviço.
        </p>
    </div>

    {{-- Mensagens --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Info Box --}}
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Hierarquia de Sugestões:</strong><br>
                    1️⃣ Se você vincular produtos aqui, <strong>estes serão oferecidos primeiro</strong><br>
                    2️⃣ Se não houver produtos vinculados, o sistema oferece <strong>os mais vendidos automaticamente</strong>
                </p>
            </div>
        </div>
    </div>

    {{-- Produtos Vinculados --}}
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">
                Produtos Vinculados ({{ count($produtosVinculados) }})
            </h3>
            <button wire:click="abrirModal" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                + Adicionar Produto
            </button>
        </div>

        <div class="p-6">
            @if(count($produtosVinculados) > 0)
                <div class="space-y-3">
                    @foreach($produtosVinculados as $produto)
                        <div class="border rounded-lg p-4 {{ $produto['is_active'] ? 'bg-white' : 'bg-gray-100' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                            #{{ $produto['priority'] }}
                                        </span>
                                        <h4 class="font-semibold text-gray-800">
                                            {{ $produto['produto_nome'] }}
                                        </h4>
                                        @if(!$produto['is_active'])
                                            <span class="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded">Inativo</span>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-2 text-sm text-gray-600 flex gap-4">
                                        <span>💰 R$ {{ number_format($produto['preco_unitario'], 2, ',', '.') }}</span>
                                        <span>📦 Estoque: {{ $produto['quantidade_atual'] }}</span>
                                        <span>📊 {{ $produto['total_vendas'] }} vendas</span>
                                        @if($produto['discount_percentage'] > 0)
                                            <span class="text-green-600 font-semibold">
                                                🏷️ {{ $produto['discount_percentage'] }}% OFF
                                            </span>
                                        @endif
                                    </div>

                                    @if($produto['observacoes'])
                                        <p class="mt-2 text-sm text-gray-500 italic">{{ $produto['observacoes'] }}</p>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2">
                                    {{-- Prioridade --}}
                                    <select wire:change="atualizarPrioridade({{ $produto['pivot_id'] }}, $event.target.value)"
                                            class="border rounded px-2 py-1 text-sm">
                                        @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ $produto['priority'] == $i ? 'selected' : '' }}>
                                                Prioridade {{ $i }}
                                            </option>
                                        @endfor
                                    </select>

                                    {{-- Toggle Ativo --}}
                                    <button wire:click="toggleAtivo({{ $produto['pivot_id'] }})"
                                            class="px-3 py-1 rounded text-sm {{ $produto['is_active'] ? 'bg-green-100 text-green-700' : 'bg-gray-300 text-gray-600' }}">
                                        {{ $produto['is_active'] ? '✓ Ativo' : '✗ Inativo' }}
                                    </button>

                                    {{-- Remover --}}
                                    <button wire:click="removerVinculo({{ $produto['pivot_id'] }})"
                                            wire:confirm="Tem certeza que deseja remover este vínculo?"
                                            class="bg-red-100 text-red-600 hover:bg-red-200 px-3 py-1 rounded text-sm">
                                        🗑️ Remover
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <p class="mb-2">⚠️ Nenhum produto vinculado manualmente.</p>
                    <p class="text-sm">O sistema oferecerá automaticamente os <strong>produtos mais vendidos</strong>.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Produtos Mais Vendidos (Fallback) --}}
    <div class="bg-gray-50 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            📊 Produtos Mais Vendidos (Oferecidos automaticamente)
        </h3>
        <p class="text-sm text-gray-600 mb-4">
            Quando não há produtos vinculados manualmente, estes serão oferecidos:
        </p>
        <div class="space-y-2">
            @foreach($maisVendidos as $idx => $produto)
                <div class="bg-white border rounded p-3 flex justify-between items-center">
                    <div>
                        <span class="text-gray-400 font-mono text-sm">#{!! $idx + 1 !!}</span>
                        <span class="ml-3 font-semibold">{{ $produto->produto_nome }}</span>
                        <span class="ml-3 text-gray-600">R$ {{ number_format($produto->preco_unitario, 2, ',', '.') }}</span>
                    </div>
                    <span class="text-sm text-gray-500">{{ $produto->total_vendas }} vendas</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Modal Adicionar Produto --}}
    @if($showModal)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-xl font-semibold">Vincular Produto ao Serviço</h3>
                </div>

                <form wire:submit.prevent="vincularProduto" class="p-6">
                    {{-- Produto --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Produto *</label>
                        <select wire:model="selectedProdutoId" 
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Selecione um produto</option>
                            @foreach($produtosDisponiveis as $produto)
                                <option value="{{ $produto['id'] }}">
                                    {{ $produto['produto_nome'] }} - R$ {{ number_format($produto['preco_unitario'], 2, ',', '.') }}
                                    ({{ $produto['total_vendas'] }} vendas)
                                </option>
                            @endforeach
                        </select>
                        @error('selectedProdutoId') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Prioridade --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Prioridade *</label>
                        <input type="number" wire:model="priority" min="1" max="10"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">1 = será oferecido primeiro</p>
                        @error('priority') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Desconto --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Desconto Especial (%)</label>
                        <input type="number" wire:model="discount_percentage" min="0" max="100" step="0.01"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Opcional: desconto exclusivo para quem agendar este serviço</p>
                        @error('discount_percentage') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Observações --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Observações</label>
                        <textarea wire:model="observacoes" rows="3"
                                  class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Ex: Recomendar apenas para cortes premium"></textarea>
                        @error('observacoes') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Botões --}}
                    <div class="flex justify-end gap-3">
                        <button type="button" wire:click="fecharModal"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Vincular Produto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

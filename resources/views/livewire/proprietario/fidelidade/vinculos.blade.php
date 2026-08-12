<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Produtos Vinculados a Serviços</h3>
            <p class="text-sm text-gray-600 mt-1">Produtos serão sugeridos automaticamente quando o serviço for agendado</p>
        </div>
        <button wire:click="abrirModalVinculo" 
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Novo Vínculo
        </button>
    </div>

    @if($vinculos->isEmpty())
        <div class="bg-gray-50 rounded-xl border-2 border-dashed border-gray-300 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum vínculo configurado</h3>
            <p class="mt-1 text-sm text-gray-500">Comece criando seu primeiro vínculo entre produto e serviço</p>
            <div class="mt-6">
                <button wire:click="abrirModalVinculo" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    Criar Primeiro Vínculo
                </button>
            </div>
        </div>
    @else
        <div class="space-y-4">
            @foreach($vinculos as $servico)
                @if($servico->produtosRecomendados->isNotEmpty())
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        {{-- Header do Serviço --}}
                        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">{{ $servico->service }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">
                                        R$ {{ number_format($servico->price, 2, ',', '.') }} • {{ $servico->time }} min
                                    </p>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ $servico->produtosRecomendados->count() }} produto(s)
                                </span>
                            </div>
                        </div>

                        {{-- Lista de Produtos --}}
                        <div class="divide-y divide-gray-200">
                            @foreach($servico->produtosRecomendados as $produto)
                                <div class="px-6 py-4 hover:bg-gray-50 transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-4 flex-1">
                                            <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                                <span class="text-indigo-600 font-bold text-sm">{{ $produto->pivot->priority }}</span>
                                            </div>
                                            
                                            <div class="flex-1">
                                                <h5 class="text-sm font-medium text-gray-900">{{ $produto->produto_nome }}</h5>
                                                <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                                                    <span>💰 R$ {{ number_format($produto->preco_unitario, 2, ',', '.') }}</span>
                                                    <span>📦 Estoque: {{ $produto->quantidade_atual }}</span>
                                                    <span>📊 Vendas: {{ $produto->total_vendas }}</span>
                                                    @if($produto->pivot->discount_percentage)
                                                        <span class="text-green-600 font-medium">🏷️ {{ $produto->pivot->discount_percentage }}% OFF</span>
                                                    @endif
                                                </div>
                                                @if($produto->pivot->observacoes)
                                                    <p class="text-xs text-gray-600 mt-1">{{ $produto->pivot->observacoes }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            {{-- Toggle Ativo/Inativo --}}
                                            <button wire:click="toggleAtivo({{ $servico->id }}, {{ $produto->id }})"
                                                    class="px-3 py-1 rounded-lg text-xs font-medium transition
                                                           @if($produto->pivot->is_active) 
                                                               bg-green-100 text-green-700 hover:bg-green-200
                                                           @else 
                                                               bg-gray-100 text-gray-600 hover:bg-gray-200
                                                           @endif">
                                                @if($produto->pivot->is_active)
                                                    ✓ Ativo
                                                @else
                                                    ✕ Inativo
                                                @endif
                                            </button>

                                            {{-- Botão Remover --}}
                                            <button wire:click="removerVinculo({{ $servico->id }}, {{ $produto->id }})"
                                                    wire:confirm="Tem certeza que deseja remover este vínculo?"
                                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>

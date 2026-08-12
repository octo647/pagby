<div>
    @if($mostrarSugestoes && count($produtosSugeridos) > 0)
        <div class="mt-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border-2 border-blue-200 shadow-lg">
            {{-- Header --}}
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-blue-600 rounded-full p-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">
                        🛍️ Produtos Recomendados para Você
                    </h3>
                    <p class="text-sm text-gray-600">
                        Aproveite nossos produtos selecionados especialmente para este serviço
                    </p>
                </div>
            </div>

            {{-- Info sobre recompensa --}}
            <div class="bg-green-50 border-l-4 border-green-500 p-3 mb-4 rounded">
                <p class="text-sm text-green-800 font-semibold">
                    🎁 <strong>Ganhe 20% em créditos</strong> ao comprar qualquer produto! 
                    Use em seus próximos serviços.
                </p>
            </div>

            {{-- Lista de Produtos --}}
            <div class="space-y-3">
                @foreach($produtosSugeridos as $produto)
                    <div wire:click="toggleProduto({{ $produto['id'] }})"
                         class="cursor-pointer transition-all duration-200 
                                {{ $this->isProdutoSelecionado($produto['id']) 
                                    ? 'bg-blue-100 border-blue-400 shadow-md scale-[1.02]' 
                                    : 'bg-white border-gray-200 hover:border-blue-300 hover:shadow' }}
                                border-2 rounded-lg p-4">
                        
                        <div class="flex items-start justify-between">
                            {{-- Info do Produto --}}
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    {{-- Checkbox Visual --}}
                                    <div class="flex-shrink-0 w-6 h-6 rounded border-2 flex items-center justify-center
                                                {{ $this->isProdutoSelecionado($produto['id']) 
                                                    ? 'bg-blue-600 border-blue-600' 
                                                    : 'bg-white border-gray-300' }}">
                                        @if($this->isProdutoSelecionado($produto['id']))
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @endif
                                    </div>

                                    <h4 class="font-semibold text-gray-800 text-lg">
                                        {{ $produto['nome'] }}
                                    </h4>
                                </div>

                                @if($produto['categoria'])
                                    <span class="inline-block bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded mb-2">
                                        {{ $produto['categoria'] }}
                                    </span>
                                @endif

                                <div class="text-sm text-gray-600 flex items-center gap-2">
                                    <span>📦 {{ $produto['disponivel'] }} em estoque</span>
                                    
                                    {{-- Ganho de crédito --}}
                                    <span class="text-green-600 font-semibold">
                                        | 🎁 Ganhe R$ {{ number_format($produto['preco_final'] * 0.20, 2, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            {{-- Preço --}}
                            <div class="text-right ml-4">
                                @if($produto['desconto'] > 0)
                                    {{-- Preço com desconto --}}
                                    <div class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded mb-1">
                                        -{{ $produto['desconto'] }}% OFF
                                    </div>
                                    <div class="text-gray-400 line-through text-sm">
                                        R$ {{ number_format($produto['preco'], 2, ',', '.') }}
                                    </div>
                                    <div class="text-2xl font-bold text-green-600">
                                        R$ {{ number_format($produto['preco_final'], 2, ',', '.') }}
                                    </div>
                                @else
                                    {{-- Preço normal --}}
                                    <div class="text-2xl font-bold text-gray-800">
                                        R$ {{ number_format($produto['preco'], 2, ',', '.') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Indicador de seleção --}}
                        @if($this->isProdutoSelecionado($produto['id']))
                            <div class="mt-2 text-blue-700 text-sm font-semibold flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Adicionado ao pedido
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Total dos produtos selecionados --}}
            @if(count($produtosSelecionados) > 0)
                <div class="mt-4 bg-blue-600 text-white rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm opacity-90">Total em Produtos ({{ count($produtosSelecionados) }})</p>
                            <p class="text-xs opacity-75 mt-1">
                                + R$ {{ number_format($this->getValorTotalProdutos() * 0.20, 2, ',', '.') }} em créditos
                            </p>
                        </div>
                        <p class="text-2xl font-bold">
                            R$ {{ number_format($this->getValorTotalProdutos(), 2, ',', '.') }}
                        </p>
                    </div>
                </div>
            @endif

            {{-- Dica --}}
            <div class="mt-4 text-center">
                <p class="text-xs text-gray-500 italic">
                    💡 Dica: Os produtos serão adicionados à sua comanda e você pode pagar tudo junto no final.
                </p>
            </div>
        </div>
    @endif
</div>

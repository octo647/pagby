<div>
    @if (session()->has('mensagem'))
        <div class="alerta-sucesso mb-4">
            {{ session('mensagem') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alerta-aviso mb-4">
            {{ session('error') }}  
            <button class="fechar-alerta" onclick="this.parentElement.style.display='none';">Fechar</button>
        </div>
    @endif

    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Controle de Estoque</h2>
            <button wire:click="abrirModal" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                + Adicionar Produto
            </button>
        </div>

        {{-- Filtros --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium mb-1">Filial:</label>
                <select wire:model.live="branch_id" class="border rounded px-3 py-2 w-full">
                    <option value="">Todas as filiais</option>
                    @foreach($filiais as $filial)
                        <option value="{{ $filial->id }}">{{ $filial->branch_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Categoria:</label>
                <select wire:model.live="categoria_filtro" class="border rounded px-3 py-2 w-full">
                    <option value="">Todas as categorias</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria }}">{{ $categoria }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Status:</label>
                <select wire:model.live="filtro_status" class="border rounded px-3 py-2 w-full">
                    <option value="todos">Todos</option>
                    <option value="estoque_baixo">Estoque Baixo</option>
                    <option value="vencidos">Vencidos</option>
                    <option value="vencendo">Vencendo em Breve</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Buscar Produto:</label>
                <input type="text" wire:model.live="busca" placeholder="Nome do produto..." 
                       class="border rounded px-3 py-2 w-full">
            </div>
        </div>

        {{-- Tabela --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 border text-left">Produto</th>
                        <th class="px-4 py-3 border text-left">Filial</th>
                        <th class="px-4 py-3 border text-left">Categoria</th>
                        <th class="px-4 py-3 border text-center">Qtd. Atual</th>
                        <th class="px-4 py-3 border text-center">Qtd. Mínima</th>
                        <th class="px-4 py-3 border text-right">Preço Unit.</th>
                        <th class="px-4 py-3 border text-right">Valor Total</th>
                        <th class="px-4 py-3 border text-center">Validade</th>
                        <th class="px-4 py-3 border text-center">Status</th>
                        <th class="px-4 py-3 border text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($estoque as $item)
                        <tr class="hover:bg-gray-50 
                            @if($item->isEstoqueBaixo()) bg-yellow-50 
                            @elseif($item->isVencido()) bg-red-50 
                            @elseif($item->venceEmBreve()) bg-orange-50 
                            @endif">
                            <td class="px-4 py-3 border">
                                <div class="font-medium">{{ $item->produto_nome }}</div>
                                @if($item->fornecedor)
                                    <div class="text-sm text-gray-500">{{ $item->fornecedor }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 border">{{ $item->branch->branch_name }}</td>
                            <td class="px-4 py-3 border">{{ $item->categoria ?: '-' }}</td>
                            <td class="px-4 py-3 border text-center">
                                <span class="@if($item->isEstoqueBaixo()) text-red-600 font-bold @endif">
                                    {{ $item->quantidade_atual }}
                                </span>
                            </td>
                            <td class="px-4 py-3 border text-center">{{ $item->quantidade_minima }}</td>
                            <td class="px-4 py-3 border text-right">
                                @if($item->preco_unitario)
                                    R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 border text-right">
                                @if($item->preco_unitario)
                                    R$ {{ number_format($item->valor_total, 2, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 border text-center">
                                @if($item->data_validade)
                                    <span class="@if($item->isVencido()) text-red-600 @elseif($item->venceEmBreve()) text-orange-600 @endif">
                                        {{ $item->data_validade->format('d/m/Y') }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 border text-center">
                                @if($item->isVencido())
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Vencido</span>
                                @elseif($item->venceEmBreve())
                                    <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded text-xs">Vence em Breve</span>
                                @elseif($item->isEstoqueBaixo())
                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Estoque Baixo</span>
                                @else
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Normal</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 border text-center">
                                <div class="flex justify-center space-x-2">
                                    <button wire:click="editarEstoque({{ $item->id }})" 
                                            class="bg-blue-500 text-white px-2 py-1 rounded text-sm hover:bg-blue-600">
                                        Editar
                                    </button>
                                    <button wire:click="excluir({{ $item->id }})" 
                                            onclick="return confirm('Tem certeza que deseja excluir este produto?')"
                                            class="bg-red-500 text-white px-2 py-1 rounded text-sm hover:bg-red-600">
                                        Excluir
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                                Nenhum produto encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        <div class="mt-4">
            {{ $estoque->links() }}
        </div>
    </div>

    {{-- Modal --}}
    @if($modalAberto)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-screen overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">
                        {{ $editando ? 'Editar Produto' : 'Adicionar Produto' }}
                    </h3>
                    <button wire:click="fecharModal" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="salvar">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1">Filial *</label>
                            <select wire:model="branch_id" class="border rounded px-3 py-2 w-full">
                                <option value="">Selecione uma filial</option>
                                @foreach($filiais as $filial)
                                    <option value="{{ $filial->id }}">{{ $filial->branch_name }}</option>
                                @endforeach
                            </select>
                            @error('branch_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1">Nome do Produto *</label>
                            <input type="text" wire:model="produto_nome" class="border rounded px-3 py-2 w-full">
                            @error('produto_nome') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Categoria</label>
                            <input type="text" wire:model="categoria" class="border rounded px-3 py-2 w-full">
                            @error('categoria') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Fornecedor</label>
                            <input type="text" wire:model="fornecedor" class="border rounded px-3 py-2 w-full">
                            @error('fornecedor') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Quantidade Atual *</label>
                            <input type="number" wire:model="quantidade_atual" min="0" class="border rounded px-3 py-2 w-full">
                            @error('quantidade_atual') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Quantidade Mínima *</label>
                            <input type="number" wire:model="quantidade_minima" min="0" class="border rounded px-3 py-2 w-full">
                            @error('quantidade_minima') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Preço Unitário</label>
                            <input type="number" wire:model="preco_unitario" step="0.01" min="0" class="border rounded px-3 py-2 w-full">
                            @error('preco_unitario') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Data de Validade</label>
                            <input type="date" wire:model="data_validade" class="border rounded px-3 py-2 w-full">
                            @error('data_validade') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1">Observações</label>
                            <textarea wire:model="observacoes" rows="3" class="border rounded px-3 py-2 w-full"></textarea>
                            @error('observacoes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" wire:click="fecharModal" 
                                class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            {{ $editando ? 'Atualizar' : 'Salvar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
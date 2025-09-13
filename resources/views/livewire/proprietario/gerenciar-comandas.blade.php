<div >
    <!-- Filtros -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Filtros</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Buscar</label>
                <input wire:model.live="search" type="text" placeholder="Número, cliente, telefone..." 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="filtro_branch" class="block text-sm font-medium text-gray-700">Filial</label>
                <select wire:model.live="filtro_branch" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Todas</option>
                    @if($branches)
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div>
                <label for="filtro_status" class="block text-sm font-medium text-gray-700">Status</label>
                <select wire:model.live="filtro_status" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="Aberta">Aberta</option>
                    <option value="Finalizada">Finalizada</option>
                    <option value="Cancelada">Cancelada</option>
                </select>
            </div>

            <div>
                <label for="filtro_funcionario" class="block text-sm font-medium text-gray-700">Funcionário</label>
                <select wire:model.live="filtro_funcionario" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Todos ({{ $users ? count($users) : 0 }})</option>
                    @if($users)
                        @foreach($users as $funcionario)
                            <option value="{{ $funcionario->id }}">{{ $funcionario->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div>
                <label for="data_inicio" class="block text-sm font-medium text-gray-700">Data Início</label>
                <input wire:model.live="data_inicio" type="date" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="data_fim" class="block text-sm font-medium text-gray-700">Data Fim</label>
                <input wire:model.live="data_fim" type="date" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        <div class="mt-4 flex space-x-2">
            <button wire:click="limparFiltros" 
                    class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200">
                Limpar Filtros
            </button>
        </div>
    </div>

    <!-- Cabeçalho com botão de nova comanda -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">Controle de Comandas</h2>
        <button wire:click="abrirModal" 
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Nova Comanda
        </button>
    </div>

    <!-- Lista de Comandas -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Número
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cliente
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Funcionário
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Data/Hora
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($comandas as $comanda)
                        <tr class="hover:bg-gray-50 
                            @if($comanda->status === 'Aberta') bg-blue-50 @endif
                            @if($comanda->status === 'Finalizada') bg-green-50 @endif
                            @if($comanda->status === 'Cancelada') bg-red-50 @endif">
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $comanda->numero_comanda }}</div>
                                <div class="text-sm text-gray-500">{{ $comanda->branch->name ?? 'N/A' }}</div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $comanda->cliente_nome }}</div>
                                @if($comanda->cliente_telefone)
                                    <div class="text-sm text-gray-500">{{ $comanda->cliente_telefone }}</div>
                                @endif
                                
                                <!-- Resumo dos itens -->
                                @if($comanda->comandaServicos->count() > 0 || $comanda->comandaProdutos->count() > 0)
                                    <div class="mt-2 text-xs text-gray-600">
                                        @if($comanda->comandaServicos->count() > 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $comanda->comandaServicos->count() }} serviço(s)
                                            </span>
                                        @endif
                                        @if($comanda->comandaProdutos->count() > 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 ml-1">
                                                {{ $comanda->comandaProdutos->count() }} produto(s)
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $comanda->funcionario->name ?? 'N/A' }}
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                {!! $comanda->status_badge !!}
                                @if($comanda->status === 'Aberta')
                                    <div class="text-xs text-gray-500 mt-1">{{ $comanda->tempo_aberto }}</div>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">R$ {{ number_format($comanda->total_geral, 2, ',', '.') }}</div>
                                <div class="text-xs text-gray-500">
                                    Serv: R$ {{ number_format($comanda->subtotal_servicos, 2, ',', '.') }} | 
                                    Prod: R$ {{ number_format($comanda->subtotal_produtos, 2, ',', '.') }}
                                </div>
                                @if($comanda->desconto > 0)
                                    <div class="text-xs text-red-500">Desc: R$ {{ number_format($comanda->desconto, 2, ',', '.') }}</div>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $comanda->data_abertura->format('d/m/Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $comanda->data_abertura->format('H:i') }}</div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <div class="flex justify-end space-x-1">
                                    <!-- Ver Detalhes -->
                                    <button wire:click="abrirPainelDetalhes({{ $comanda->id }})" 
                                            class="inline-flex items-center px-2 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50" 
                                            title="Ver Detalhes"
                                            onclick="console.log('Clicou no botão para comanda {{ $comanda->id }}')">
                                        👁️ Ver (ID: {{ $comanda->id }})
                                    </button>
                                    
                                    @if($comanda->status === 'Aberta')
                                        <!-- Adicionar Serviço -->
                                        <button wire:click="abrirModalServico({{ $comanda->id }})" 
                                                class="text-green-600 hover:text-green-900 p-1" title="Adicionar Serviço">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                        </button>
                                        
                                        <!-- Adicionar Produto -->
                                        <button wire:click="abrirModalProduto({{ $comanda->id }})" 
                                                class="text-purple-600 hover:text-purple-900 p-1" title="Adicionar Produto">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M3.375 3C2.339 3 1.5 3.84 1.5 4.875v.75c0 1.036.84 1.875 1.875 1.875h17.25c1.035 0 1.875-.84 1.875-1.875v-.75C22.5 3.839 21.66 3 20.625 3H3.375Z" />
                                                <path fill-rule="evenodd" d="m3.087 9 .54 9.176A3 3 0 0 0 6.62 21h10.757a3 3 0 0 0 2.995-2.824L20.913 9H3.087Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        
                                        <!-- Editar -->
                                        <button wire:click="abrirModal({{ $comanda->id }})" 
                                                class="text-blue-600 hover:text-blue-900 p-1" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        
                                        <!-- Finalizar -->
                                        <button wire:click="finalizarComanda({{ $comanda->id }})" 
                                                onclick="return confirm('Finalizar esta comanda?')"
                                                class="text-green-600 hover:text-green-900 p-1" title="Finalizar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                        
                                        <!-- Cancelar -->
                                        <button wire:click="cancelarComanda({{ $comanda->id }})" 
                                                onclick="return confirm('Cancelar esta comanda?')"
                                                class="text-red-600 hover:text-red-900 p-1" title="Cancelar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        

                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Nenhuma comanda encontrada
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        <div class="px-6 py-3 bg-gray-50">
            {{ $comandas->links() }}
        </div>
    </div>

    <!-- Modal da Comanda -->
    @if($mostrar_modal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <form wire:submit="salvar">
                        <div class="mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                {{ $editando_id ? 'Editar Comanda' : 'Nova Comanda' }}
                            </h3>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Cliente *</label>
                                <input wire:model="cliente_nome" type="text" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('cliente_nome') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Telefone</label>
                                <input wire:model="cliente_telefone" type="text"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('cliente_telefone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Filial *</label>
                                <select wire:model="branch_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Selecione...</option>
                                    @if($branches)
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('branch_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Funcionário Responsável *</label>
                                <select wire:model="funcionario_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Selecione...</option>
                                    @if($users)
                                        @foreach($users as $funcionario)
                                            <option value="{{ $funcionario->id }}">{{ $funcionario->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('funcionario_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Desconto (R$)</label>
                                <input wire:model="desconto" type="number" step="0.01" min="0"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('desconto') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Observações</label>
                                <textarea wire:model="observacoes" rows="3"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                                @error('observacoes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex space-x-3 justify-end">
                            <button type="button" wire:click="fecharModal" 
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Cancelar
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                                {{ $editando_id ? 'Atualizar' : 'Criar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Adicionar Serviço -->
    @if($mostrar_modal_servico)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <form wire:submit="adicionarServico">
                        <div class="mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Adicionar Serviço</h3>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Funcionário *</label>
                                <select wire:model.live="funcionario_servico_id" wire:change="atualizarFuncionarioServico" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Selecione o funcionário da filial...</option>
                                    @php $funcionarios_disponiveis = $funcionarios_da_filial && $funcionarios_da_filial->count() > 0 ? $funcionarios_da_filial : $users; @endphp
                                    @if($funcionarios_disponiveis)
                                        @foreach($funcionarios_disponiveis as $funcionario)
                                            <option value="{{ $funcionario->id }}">{{ $funcionario->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('funcionario_servico_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            @if($funcionario_servico_id)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Serviço *</label>
                                <select wire:model.live="service_id" wire:change="atualizarPrecoServico" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Selecione...</option>
                                    @foreach($servicosDisponiveis as $branchService)
                                        <option value="{{ $branchService->service_id }}">
                                            {{ $branchService->service->service }} - {{ $branchService->formatted_price }}
                                            @php
                                                $customDuration = null;
                                                if($funcionario_id) {
                                                    $customDuration = \App\Models\User::find($funcionario_id)->customServices()
                                                        ->where('service_id', $branchService->service_id)
                                                        ->first()?->pivot?->custom_duration_minutes;
                                                }
                                            @endphp
                                            @if($customDuration)
                                                - {{ $customDuration }} min (personalizado)
                                            @else
                                                - {{ $branchService->formatted_duration }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            @endif

                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Quantidade *</label>
                                    <input wire:model="quantidade_servico" type="number" min="1" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('quantidade_servico') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Duração (min)</label>
                                    <input wire:model="tempo_servico" type="number" min="1" readonly
                                           class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Preço Unitário *</label>
                                    <input wire:model="preco_servico" type="number" step="0.01" min="0" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('preco_servico') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Observações</label>
                                <textarea wire:model="obs_servico" rows="2"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex space-x-3 justify-end">
                            <button type="button" wire:click="fecharModalServico" 
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Cancelar
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700">
                                Adicionar Serviço
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Adicionar Produto -->
    @if($mostrar_modal_produto)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <form wire:submit="adicionarProduto">
                        <div class="mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Adicionar Produto</h3>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Produto *</label>
                                <select wire:model.live="estoque_id" wire:change="atualizarPrecoProduto" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Selecione...</option>
                                    @foreach($produtos_estoque as $produto)
                                        <option value="{{ $produto->id }}">
                                            {{ $produto->produto_nome }} 
                                            ({{ $produto->branch->name }})
                                            - Estoque: {{ $produto->quantidade_atual }}
                                            - R$ {{ number_format($produto->preco_unitario, 2, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('estoque_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Quantidade *</label>
                                    <input wire:model="quantidade_produto" type="number" min="1" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('quantidade_produto') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Preço Unitário *</label>
                                    <input wire:model="preco_produto" type="number" step="0.01" min="0" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('preco_produto') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Observações</label>
                                <textarea wire:model="obs_produto" rows="2"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex space-x-3 justify-end">
                            <button type="button" wire:click="fecharModalProduto" 
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Cancelar
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-md hover:bg-purple-700">
                                Adicionar Produto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Mensagens Flash -->
    @if (session()->has('message'))
        <div class="fixed top-4 right-4 z-50 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-4 right-4 z-50 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Painel Lateral de Detalhes da Comanda -->
    @if($mostrar_painel_detalhes && $comanda_detalhes)
    <div class="fixed inset-0 z-50 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <!-- Background overlay -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="fecharPainelDetalhes"></div>
            
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div class="pointer-events-auto relative w-screen max-w-2xl">
                    <!-- Header do painel -->
                    <div class="flex h-full flex-col overflow-y-scroll bg-white py-6 shadow-xl">
                        <div class="px-4 sm:px-6">
                            <div class="flex items-start justify-between">
                                <h2 class="text-lg font-medium text-gray-900">Detalhes da Comanda #{{ $comanda_detalhes->numero_comanda }}</h2>
                                <div class="ml-3 flex h-7 items-center">
                                    <button type="button" wire:click="fecharPainelDetalhes"
                                            class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                        <span class="sr-only">Fechar painel</span>
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Conteúdo do painel -->
                        <div class="relative mt-6 flex-1 px-4 sm:px-6">
                            <!-- Informações da comanda -->
                            <div class="mb-6 rounded-lg bg-gray-50 p-4">
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="font-medium text-gray-700">Cliente:</span>
                                        <div>{{ $comanda_detalhes->cliente_nome }}</div>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Status:</span>
                                        <div>
                                            {!! $comanda_detalhes->status_badge !!}
                                            @if($comanda_detalhes->status !== 'Aberta')
                                                <div class="text-xs text-gray-500 mt-1">Comanda não pode ser editada</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Filial:</span>
                                        <div>{{ $comanda_detalhes->branch->branch_name ?? 'N/A' }}</div>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Funcionário:</span>
                                        <div>{{ $comanda_detalhes->funcionario->name ?? 'N/A' }}</div>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Total:</span>
                                        <div class="text-lg font-semibold text-green-600">
                                            R$ {{ number_format($comanda_detalhes->total_geral, 2, ',', '.') }}
                                        </div>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Data:</span>
                                        <div>{{ $comanda_detalhes->data_abertura->format('d/m/Y H:i') }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Seção de Serviços -->
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        Serviços
                                        @if($comanda_detalhes->comandaServicos->count() > 0)
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $comanda_detalhes->comandaServicos->count() }}
                                            </span>
                                        @endif
                                    </h3>
                                    @if($comanda_detalhes->status === 'Aberta')
                                        <button wire:click="$toggle('mostrandoFormServico')"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            {{ $mostrandoFormServico ? 'Cancelar' : 'Adicionar Serviço' }}
                                        </button>
                                    @endif
                                </div>
                                
                                <!-- Formulário inline para adicionar serviço -->
                                @if($comanda_detalhes->status === 'Aberta' && $mostrandoFormServico)
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                        <div class="mb-3 p-2 bg-blue-100 rounded text-sm text-blue-800">
                                            <strong>📍 Filial:</strong> {{ $comanda_detalhes->branch->branch_name }} - Apenas funcionários desta filial podem ser selecionados.
                                        </div>
                                        <form wire:submit="adicionarServicoPainel">
                                            <div class="space-y-3">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Funcionário *</label>
                                                    <select wire:model.live="funcionario_servico_id" wire:change="atualizarFuncionarioServico" required
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                                        <option value="">Selecione o funcionário desta filial...</option>
                                                        @php 
                                                            $funcionarios_painel = $funcionarios_da_filial && $funcionarios_da_filial->count() > 0 ? 
                                                                                  $funcionarios_da_filial : 
                                                                                  ($users ?? collect());
                                                        @endphp
                                                        @foreach($funcionarios_painel as $user)
                                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                @if($funcionario_servico_id)
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Serviço *</label>
                                                    <select wire:model.live="service_id" wire:change="atualizarPrecoServico" required
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                                        <option value="">Selecione...</option>
                                                        @foreach($servicosDisponiveis as $branchService)
                                                            <option value="{{ $branchService->service_id }}" 
                                                                    data-price="{{ $branchService->price }}"
                                                                    data-duration="{{ $branchService->duration_minutes }}">
                                                                {{ $branchService->service->service }} 
                                                                - {{ $branchService->formatted_price }}
                                                                @if($tempo_servico)
                                                                    - {{ $tempo_servico }} min (personalizado)
                                                                @else
                                                                    - {{ $branchService->formatted_duration }}
                                                                @endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @endif

                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Quantidade *</label>
                                                        <input wire:model="quantidade_servico" type="number" min="1" required
                                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Duração (min)</label>
                                                        <input wire:model="tempo_servico" type="number" min="1" readonly
                                                               class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm text-sm">
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Preço Unitário *</label>
                                                    <input wire:model="preco_servico" type="number" step="0.01" min="0" required
                                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Observações</label>
                                                    <textarea wire:model="obs_servico" rows="2"
                                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></textarea>
                                                </div>

                                                <div class="flex space-x-2 pt-2">
                                                    <button type="submit" 
                                                            class="flex-1 bg-blue-600 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                        Adicionar Serviço
                                                    </button>
                                                    <button type="button" wire:click="$set('mostrandoFormServico', false)" 
                                                            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                                        Cancelar
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                                
                                @if($comanda_detalhes->comandaServicos->count() > 0)
                                    <div class="space-y-2">
                                        @foreach($comanda_detalhes->comandaServicos as $servico)
                                            <div class="flex items-center justify-between bg-white border rounded-lg p-3">
                                                <div class="flex-1">
                                                    <div class="font-medium text-gray-900">{{ $servico->service->service ?? 'N/A' }}</div>
                                                    <div class="text-sm text-gray-500">
                                                        Funcionário: {{ $servico->funcionario->name ?? 'N/A' }}
                                                        @if($servico->quantidade > 1)
                                                            | Qtd: {{ $servico->quantidade }}
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <span class="font-medium text-gray-900">
                                                        R$ {{ number_format($servico->subtotal, 2, ',', '.') }}
                                                    </span>
                                                    @if($comanda_detalhes->status === 'Aberta')
                                                        <button wire:click="excluirServico({{ $servico->id }})" 
                                                                onclick="return confirm('Remover este serviço?')"
                                                                class="text-red-600 hover:text-red-900">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8 text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2z" />
                                        </svg>
                                        Nenhum serviço adicionado
                                    </div>
                                @endif
                            </div>

                            <!-- Seção de Produtos -->
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        Produtos
                                        @if($comanda_detalhes->comandaProdutos->count() > 0)
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                {{ $comanda_detalhes->comandaProdutos->count() }}
                                            </span>
                                        @endif
                                    </h3>
                                    @if($comanda_detalhes->status === 'Aberta')
                                        <button wire:click="$toggle('mostrandoFormProduto')"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                            <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            {{ $mostrandoFormProduto ? 'Cancelar' : 'Adicionar Produto' }}
                                        </button>
                                    @endif
                                </div>
                                
                                <!-- Formulário inline para adicionar produto -->
                                @if($comanda_detalhes->status === 'Aberta' && $mostrandoFormProduto)
                                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-4">
                                        <div class="mb-3 p-2 bg-purple-100 rounded text-sm text-purple-800">
                                            <strong>📍 Filial:</strong> {{ $comanda_detalhes->branch->branch_name }} - Apenas produtos do estoque desta filial podem ser selecionados.
                                        </div>
                                        <form wire:submit="adicionarProdutoPainel">
                                            <div class="space-y-3">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Produto *</label>
                                                    <select wire:model.live="estoque_id" wire:change="atualizarPrecoProduto" required
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm">
                                                        <option value="">Selecione...</option>
                                                        @foreach($produtos_estoque as $produto)
                                                            <option value="{{ $produto->id }}">
                                                                {{ $produto->produto_nome }} 
                                                                ({{ $produto->branch->name }})
                                                                - Estoque: {{ $produto->quantidade_atual }}
                                                                - R$ {{ number_format($produto->preco_unitario, 2, ',', '.') }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Quantidade *</label>
                                                        <input wire:model="quantidade_produto" type="number" min="1" required
                                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm">
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Preço Unitário *</label>
                                                        <input wire:model="preco_produto" type="number" step="0.01" min="0" required
                                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm">
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Observações</label>
                                                    <textarea wire:model="obs_produto" rows="2"
                                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm"></textarea>
                                                </div>

                                                <div class="flex space-x-2 pt-2">
                                                    <button type="submit" 
                                                            class="flex-1 bg-purple-600 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                                        Adicionar Produto
                                                    </button>
                                                    <button type="button" wire:click="$set('mostrandoFormProduto', false)" 
                                                            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                                        Cancelar
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                                
                                @if($comanda_detalhes->comandaProdutos->count() > 0)
                                    <div class="space-y-2">
                                        @foreach($comanda_detalhes->comandaProdutos as $produto)
                                            <div class="flex items-center justify-between bg-white border rounded-lg p-3">
                                                <div class="flex-1">
                                                    <div class="font-medium text-gray-900">{{ $produto->estoque->produto_nome ?? 'N/A' }}</div>
                                                    <div class="text-sm text-gray-500">
                                                        Categoria: {{ $produto->estoque->categoria ?? 'N/A' }}
                                                        | Qtd: {{ $produto->quantidade }}
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <span class="font-medium text-gray-900">
                                                        R$ {{ number_format($produto->subtotal, 2, ',', '.') }}
                                                    </span>
                                                    @if($comanda_detalhes->status === 'Aberta')
                                                        <button wire:click="excluirProduto({{ $produto->id }})" 
                                                                onclick="return confirm('Remover este produto?')"
                                                                class="text-red-600 hover:text-red-900">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8 text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        Nenhum produto adicionado
                                    </div>
                                @endif
                            </div>

                            <!-- Ações da comanda -->
                            @if($comanda_detalhes->status === 'Aberta')
                                <div class="border-t pt-6">
                                    <div class="flex space-x-3">
                                        <button wire:click="finalizarComanda({{ $comanda_detalhes->id }})" 
                                                onclick="return confirm('Finalizar esta comanda?')"
                                                class="flex-1 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                            Finalizar Comanda
                                        </button>
                                        <button wire:click="cancelarComanda({{ $comanda_detalhes->id }})" 
                                                onclick="return confirm('Cancelar esta comanda?')"
                                                class="flex-1 bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                            Cancelar Comanda
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    // Auto-hide flash messages
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    });
</script>

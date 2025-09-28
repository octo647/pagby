<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <!-- Header com Breadcrumb -->
    <div class="bg-white shadow-sm border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex items-center space-x-2 text-sm text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2V7"></path>
                </svg>
                <span>Proprietário</span>
                <span>/</span>
                <span class="text-indigo-600 font-semibold">Controle de Estoque</span>
            </nav>
        </div>
    </div>

    <!-- Notificações -->
    @if (session()->has('mensagem'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <p class="text-green-800 font-medium">{{ session('mensagem') }}</p>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
                <button onclick="this.parentElement.parentElement.style.display='none';" class="text-red-400 hover:text-red-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <!-- Container Principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Cabeçalho do Dashboard -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 mb-2">Controle de Estoque</h1>
                    <p class="text-slate-600">Gerencie seus produtos, monitore estoques e controle validades</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <button wire:click="abrirModal" 
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-semibold rounded-xl hover:from-emerald-700 hover:to-emerald-800 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Adicionar Produto
                    </button>
                </div>
            </div>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total de Produtos -->
            <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 mb-1">Total de Produtos</p>
                        <p class="text-3xl font-bold text-slate-900">{{ $estoque->total() }}</p>
                        <p class="text-sm text-slate-500 mt-1">
                            @php
                                $valorTotal = $estoque->sum('valor_total');
                            @endphp
                            Valor: R$ {{ number_format($valorTotal, 2, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-3 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Estoque Baixo -->
            <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 mb-1">Estoque Baixo</p>
                        @php
                            $estoqueBaixo = collect($estoque->items())->filter(function($item) {
                                return $item->isEstoqueBaixo();
                            })->count();
                        @endphp
                        <p class="text-3xl font-bold text-orange-600">{{ $estoqueBaixo }}</p>
                        <p class="text-sm text-orange-500 mt-1">Requer atenção</p>
                    </div>
                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-3 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Produtos Vencidos -->
            <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 mb-1">Produtos Vencidos</p>
                        @php
                            $vencidos = collect($estoque->items())->filter(function($item) {
                                return $item->isVencido();
                            })->count();
                        @endphp
                        <p class="text-3xl font-bold text-red-600">{{ $vencidos }}</p>
                        <p class="text-sm text-red-500 mt-1">Ação urgente</p>
                    </div>
                    <div class="bg-gradient-to-br from-red-500 to-red-600 p-3 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Vencem em Breve -->
            <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 mb-1">Vence em Breve</p>
                        @php
                            $venceEmBreve = collect($estoque->items())->filter(function($item) {
                                return $item->venceEmBreve();
                            })->count();
                        @endphp
                        <p class="text-3xl font-bold text-yellow-600">{{ $venceEmBreve }}</p>
                        <p class="text-sm text-yellow-500 mt-1">Próximos 30 dias</p>
                    </div>
                    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 p-3 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h5v2H4v-2zM4 12h11v2H4v-2zM4 5h16v2H4V5z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros e Busca -->
        <div class="bg-white rounded-2xl p-6 shadow-lg mb-8 border border-slate-100">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Filtros e Busca</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Filial:</label>
                    <select wire:model.live="branch_id" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <option value="">Todas as filiais</option>
                        @foreach($filiais as $filial)
                            <option value="{{ $filial->id }}">{{ $filial->branch_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Categoria:</label>
                    <select wire:model.live="categoria_filtro" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <option value="">Todas as categorias</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria }}">{{ $categoria }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Status:</label>
                    <select wire:model.live="filtro_status" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <option value="todos">Todos os produtos</option>
                        <option value="estoque_baixo">Estoque Baixo</option>
                        <option value="vencidos">Produtos Vencidos</option>
                        <option value="vencendo">Vence em Breve</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Buscar Produto:</label>
                    <div class="relative">
                        <input type="text" wire:model.live="busca" placeholder="Digite o nome do produto..." 
                               class="w-full px-4 py-3 pl-10 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Produtos - Desktop -->
    <div class="hidden lg:block bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-100 max-w-full">
            <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Lista de Produtos</h3>
            </div>
            
            <div class="overflow-x-auto w-full">
                <table class="min-w-full w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Produto</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Filial</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Categoria</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">Estoque</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">Preço</th>
                            
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">Validade</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse($estoque as $item)
                            <tr wire:key="desktop-{{$item->id}}" class="hover:bg-slate-50 transition-colors duration-200
                                @if($item->isEstoqueBaixo()) bg-orange-50 border-l-4 border-l-orange-400 
                                @elseif($item->isVencido()) bg-red-50 border-l-4 border-l-red-500 
                                @elseif($item->venceEmBreve()) bg-yellow-50 border-l-4 border-l-yellow-400 
                                @else hover:bg-slate-50 @endif">
                                
                                <!-- Produto -->
                                <td class="px-6 py-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-900">{{ $item->produto_nome }}</div>
                                            @if($item->fornecedor)
                                                <div class="text-sm text-slate-500 mt-1">
                                                    <span class="inline-flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16l-1 10H5L4 7z"></path>
                                                        </svg>
                                                        {{ $item->fornecedor }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Filial -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"></path>
                                            </svg>
                                        </div>
                                        <span class="text-sm font-medium text-slate-900">{{ $item->branch->branch_name }}</span>
                                    </div>
                                </td>

                                <!-- Categoria -->
                                <td class="px-6 py-4">
                                    @if($item->categoria)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                            {{ $item->categoria }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-sm">Sem categoria</span>
                                    @endif
                                </td>

                                <!-- Estoque -->
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex flex-col items-center">
                                        <span class="text-lg font-bold @if($item->isEstoqueBaixo()) text-orange-600 @else text-slate-900 @endif">
                                            {{ $item->quantidade_atual }}
                                        </span>
                                        <span class="text-xs text-slate-500">mín: {{ $item->quantidade_minima }}</span>
                                        
                                        <!-- Barra de Progresso do Estoque -->
                                        @php
                                            $percentualEstoque = $item->quantidade_minima > 0 ? 
                                                min(100, ($item->quantidade_atual / ($item->quantidade_minima * 2)) * 100) : 100;
                                        @endphp
                                        <div class="w-16 bg-slate-200 rounded-full h-1.5 mt-1">
                                            <div class="h-1.5 rounded-full @if($item->isEstoqueBaixo()) bg-orange-500 @elseif($percentualEstoque > 70) bg-green-500 @else bg-yellow-500 @endif" 
                                                 style="width: {{ $percentualEstoque }}%"></div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Preço -->
                                <td class="px-6 py-4 text-right">
                                    @if($item->preco_unitario)
                                        <div class="text-sm font-semibold text-slate-900">
                                            R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}
                                        </div>
                                        <div class="text-xs text-slate-500">por unidade</div>
                                    @else
                                        <span class="text-slate-400 text-sm">Não definido</span>
                                    @endif
                                </td>

                                

                                <!-- Validade -->
                                <td class="px-6 py-4 text-center">
                                    @if($item->data_validade)
                                        <div class="flex flex-col items-center">
                                            <span class="text-sm font-medium @if($item->isVencido()) text-red-600 @elseif($item->venceEmBreve()) text-yellow-600 @else text-slate-900 @endif">
                                                {{ $item->data_validade->format('d/m/Y') }}
                                            </span>
                                            @if($item->isVencido())
                                                <span class="text-xs text-red-500 mt-1">Vencido</span>
                                            @elseif($item->venceEmBreve())
                                                <span class="text-xs text-yellow-600 mt-1">
                                                    {{ $item->data_validade->diffInDays(now()) }} dias
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-sm">Sem validade</span>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 text-center">
                                    @if($item->isVencido())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            Vencido
                                        </span>
                                    @elseif($item->venceEmBreve())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                            </svg>
                                            Vence em Breve
                                        </span>
                                    @elseif($item->isEstoqueBaixo())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            Estoque Baixo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Normal
                                        </span>
                                    @endif
                                </td>

                                <!-- Ações -->
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        <button wire:click="editarEstoque({{ $item->id }})" 
                                                class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs font-semibold rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 transform hover:scale-105 shadow-sm">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Editar
                                        </button>
                                        <button wire:click="excluir({{ $item->id }})" 
                                                onclick="return confirm('Tem certeza que deseja excluir este produto?\n\nEsta ação não pode ser desfeita!')"
                                                class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs font-semibold rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-200 transform hover:scale-105 shadow-sm">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Excluir
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                        <h3 class="text-lg font-medium text-slate-900 mb-2">Nenhum produto encontrado</h3>
                                        <p class="text-slate-500 mb-4">Não há produtos que correspondam aos filtros aplicados.</p>
                                        <button wire:click="abrirModal" 
                                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-semibold rounded-lg hover:from-emerald-700 hover:to-emerald-800 transition-all duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Adicionar primeiro produto
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Lista de Produtos - Mobile -->
        <div class="lg:hidden space-y-4">
            @forelse($estoque as $item)
                <div wire:key="mobile-{{$item->id}}" class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden transition-all duration-300 hover:shadow-xl
                    @if($item->isEstoqueBaixo()) border-l-4 border-l-orange-400 
                    @elseif($item->isVencido()) border-l-4 border-l-red-500 
                    @elseif($item->venceEmBreve()) border-l-4 border-l-yellow-400 
                    @else border-l-4 border-l-green-500 
                    @endif">
                    
                    <!-- Cabeçalho do Card -->
                    <div class="bg-gradient-to-r from-slate-50 to-slate-100 px-5 py-4 border-b border-slate-200">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 flex items-start space-x-3">
                                <!-- Ícone do produto -->
                                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-slate-900 text-lg leading-tight">{{ $item->produto_nome }}</h3>
                                    @if($item->fornecedor)
                                        <p class="text-sm text-slate-600 mt-1 flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16l-1 10H5L4 7z"></path>
                                            </svg>
                                            {{ $item->fornecedor }}
                                        </p>
                                    @endif
                                    <p class="text-xs text-slate-500 mt-1 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"></path>
                                        </svg>
                                        {{ $item->branch->branch_name }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Badge de Status -->
                            <div class="flex-shrink-0 ml-3">
                                @if($item->isVencido())
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        Vencido
                                    </span>
                                @elseif($item->venceEmBreve())
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                        Vence em Breve
                                    </span>
                                @elseif($item->isEstoqueBaixo())
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        Estoque Baixo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Normal
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Conteúdo Principal -->
                    <div class="p-5">
                        <!-- Informações de Estoque -->
                        <div class="grid grid-cols-2 gap-4 mb-5">
                            <!-- Quantidade e Status -->
                            <div class="bg-slate-50 rounded-xl p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Estoque</span>
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div class="flex items-baseline space-x-2">
                                    <span class="text-2xl font-bold @if($item->isEstoqueBaixo()) text-orange-600 @else text-slate-900 @endif">
                                        {{ $item->quantidade_atual }}
                                    </span>
                                    <span class="text-sm text-slate-500">/ {{ $item->quantidade_minima }} mín</span>
                                </div>
                                
                                <!-- Barra de Progresso -->
                                @php
                                    $percentualEstoque = $item->quantidade_minima > 0 ? 
                                        min(100, ($item->quantidade_atual / ($item->quantidade_minima * 2)) * 100) : 100;
                                @endphp
                                <div class="mt-2">
                                    <div class="w-full bg-slate-200 rounded-full h-2">
                                        <div class="h-2 rounded-full @if($item->isEstoqueBaixo()) bg-orange-500 @elseif($percentualEstoque > 70) bg-green-500 @else bg-yellow-500 @endif transition-all duration-300" 
                                             style="width: {{ $percentualEstoque }}%"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Categoria -->
                            <div class="bg-indigo-50 rounded-xl p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Categoria</span>
                                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                                <div class="text-sm font-medium text-slate-900">
                                    {{ $item->categoria ?: 'Sem categoria' }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Valores Financeiros -->
                        <div class="grid grid-cols-2 gap-4 mb-5">
                            <!-- Preço Unitário -->
                            <div class="bg-emerald-50 rounded-xl p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Preço Unit.</span>
                                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                                <div class="text-lg font-bold text-emerald-600">
                                    @if($item->preco_unitario)
                                        R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}
                                    @else
                                        <span class="text-slate-400 text-sm">Não definido</span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Valor Total -->
                            <div class="bg-blue-50 rounded-xl p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Valor Total</span>
                                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div class="text-lg font-bold text-blue-600">
                                    @if($item->preco_unitario)
                                        R$ {{ number_format($item->valor_total, 2, ',', '.') }}
                                    @else
                                        <span class="text-slate-400 text-sm">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Data de Validade (se existir) -->
                        @if($item->data_validade)
                            <div class="bg-yellow-50 rounded-xl p-4 mb-5">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Validade</span>
                                    <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v10a2 2 0 002 2h4a2 2 0 002-2V11a2 2 0 00-2-2H10a2 2 0 00-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-bold @if($item->isVencido()) text-red-600 @elseif($item->venceEmBreve()) text-yellow-600 @else text-slate-900 @endif">
                                        {{ $item->data_validade->format('d/m/Y') }}
                                    </span>
                                    @if($item->venceEmBreve() && !$item->isVencido())
                                        <span class="text-xs text-yellow-600 font-medium">
                                            {{ $item->data_validade->diffInDays(now()) }} dias restantes
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        <!-- Botões de Ação -->
                        <div class="flex space-x-3 pt-4 border-t border-slate-200">
                            <button wire:click="editarEstoque({{ $item->id }})" 
                                    class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-200 transform hover:scale-[1.02] shadow-lg">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Editar
                            </button>
                            <button wire:click="excluir({{ $item->id }})" 
                                    onclick="return confirm('Tem certeza que deseja excluir este produto?\n\nEsta ação não pode ser desfeita!')"
                                    class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-200 transform hover:scale-[1.02] shadow-lg">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Excluir
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-lg p-8 text-center border border-slate-100">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-20 h-20 bg-gradient-to-br from-slate-200 to-slate-300 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Nenhum produto encontrado</h3>
                        <p class="text-slate-500 mb-6">Não há produtos que correspondam aos filtros aplicados.</p>
                        <button wire:click="abrirModal" 
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-semibold rounded-xl hover:from-emerald-700 hover:to-emerald-800 transition-all duration-200 shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Adicionar primeiro produto
                        </button>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Paginação -->
        <div class="mt-8 flex justify-center">
            <div class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden">
                {{ $estoque->links() }}
            </div>
        </div>
    </div>

    <!-- Modal de Cadastro/Edição -->
    @if($modalAberto)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
                <!-- Cabeçalho do Modal -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-700 px-6 py-5 flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">
                                {{ $editando ? 'Editar Produto' : 'Adicionar Novo Produto' }}
                            </h3>
                            <p class="text-indigo-100 text-sm">
                                {{ $editando ? 'Atualize as informações do produto' : 'Preencha os dados para cadastrar um novo produto' }}
                            </p>
                        </div>
                    </div>
                    <button wire:click="fecharModal" 
                            class="text-white hover:bg-white hover:bg-opacity-20 rounded-xl p-2 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Corpo do Modal -->
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
                    <form wire:submit.prevent="salvar" class="space-y-6">
                        <!-- Seção: Informações Básicas -->
                        <div class="bg-slate-50 rounded-2xl p-6">
                            <h4 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Informações Básicas
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Filial *</label>
                                    <select wire:model="branch_id" 
                                            class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors bg-white">
                                        <option value="">Selecione uma filial</option>
                                        @foreach($filiais as $filial)
                                            <option value="{{ $filial->id }}">{{ $filial->branch_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('branch_id') 
                                        <p class="text-red-500 text-sm mt-1 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nome do Produto *</label>
                                    <input type="text" wire:model="produto_nome" placeholder="Ex: Shampoo Profissional 500ml"
                                           class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                    @error('produto_nome') 
                                        <p class="text-red-500 text-sm mt-1 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Categoria</label>
                                    <input type="text" wire:model="categoria" placeholder="Ex: Produtos Capilares"
                                           class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                    @error('categoria') 
                                        <p class="text-red-500 text-sm mt-1 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Fornecedor</label>
                                    <input type="text" wire:model="fornecedor" placeholder="Ex: Beauty Supply LTDA"
                                           class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                    @error('fornecedor') 
                                        <p class="text-red-500 text-sm mt-1 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Seção: Estoque e Preços -->
                        <div class="bg-emerald-50 rounded-2xl p-6">
                            <h4 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Estoque e Preços
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Quantidade Atual *</label>
                                    <input type="number" wire:model="quantidade_atual" min="0" placeholder="0"
                                           class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                                    @error('quantidade_atual') 
                                        <p class="text-red-500 text-sm mt-1 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Quantidade Mínima *</label>
                                    <input type="number" wire:model="quantidade_minima" min="0" placeholder="0"
                                           class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                                    @error('quantidade_minima') 
                                        <p class="text-red-500 text-sm mt-1 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Preço Unitário</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-3 text-slate-500">R$</span>
                                        <input type="number" wire:model="preco_unitario" step="0.01" min="0" placeholder="0,00"
                                               class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                                    </div>
                                    @error('preco_unitario') 
                                        <p class="text-red-500 text-sm mt-1 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Data de Validade</label>
                                    <input type="date" wire:model="data_validade"
                                           class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                                    @error('data_validade') 
                                        <p class="text-red-500 text-sm mt-1 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Seção: Observações -->
                        <div class="bg-blue-50 rounded-2xl p-6">
                            <h4 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Informações Adicionais
                            </h4>
                            
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Observações</label>
                                <textarea wire:model="observacoes" rows="4" 
                                          placeholder="Adicione observações sobre o produto, instruções especiais, etc."
                                          class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none"></textarea>
                                @error('observacoes') 
                                    <p class="text-red-500 text-sm mt-1 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-slate-200">
                            <button type="button" wire:click="fecharModal" 
                                    class="px-6 py-3 border-2 border-slate-300 text-slate-700 font-semibold rounded-xl hover:bg-slate-50 transition-all duration-200">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancelar
                            </button>
                            <button type="submit" 
                                    class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-700 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-800 transition-all duration-200 transform hover:scale-105 shadow-lg">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ $editando ? 'Atualizar Produto' : 'Salvar Produto' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
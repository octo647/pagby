
<div>
    {{-- Planos de Assinatura - Interface Moderna --}}
    
    {{-- Notificações de Sucesso/Aviso --}}
    @if (session()->has('message') || session()->has('warning'))
        <div class="mb-6">
            <div class="flex items-center p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200" role="alert">
                <svg class="shrink-0 inline w-5 h-5 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <span class="sr-only">Info</span>
                <div>
                    <span class="font-medium">{{ session('message') ?? session('warning') }}</span>
                </div>
            </div>
        </div>
    @endif

<div x-data="{ 
    open: @entangle('modalAberto'), 
    openServicos: false, 
    openServicosAdicionais: false, 
    openNovoPlano: @entangle('modalNovoPlano'),
    openServicosNovoPlano: false,
    openServicosAdicionaisNovoPlano: false,
    // Função para sincronizar descontos quando serviços adicionais mudam
    syncDescontos() {
        // Trigger do Livewire para atualizar descontos
        @this.call('sincronizarDescontos');
    }
}" class="min-h-screen bg-gray-50">
    
    {{-- Cabeçalho da Página --}}
    <div class="bg-white border-b border-gray-200 mb-8">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Planos de Assinatura</h1>
                    @can('Proprietário')
                        <p class="text-gray-600 mt-1">Gerencie os planos disponíveis para seus clientes</p>
                    @else
                        <p class="text-gray-600 mt-1">Escolha o plano ideal para suas necessidades</p>
                    @endcan
                </div>
                
                @can('Proprietário')
                <div class="mt-4 sm:mt-0">
                    <button @click="openNovoPlano = true" wire:click="abrirModalNovoPlano" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition-colors shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Novo Plano
                    </button>
                </div>
                @endcan
            </div>
        </div>
    </div>

    {{-- Grid de Planos --}}
    <div class="container mx-auto px-4 pb-8">
        @if(count($planos) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                @foreach($planos as $plano)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-300
                        @if(auth()->user()->hasRole('Cliente') && auth()->user()->currentSubscription() && auth()->user()->currentSubscription()->plan_id == $plano['id']) ring-2 ring-blue-500 @endif">
                        
                        {{-- Header do Plano --}}
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 text-white">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-xl font-bold">{{ $plano['name'] }}</h3>
                                    @if(auth()->user()->hasRole('Cliente') && auth()->user()->currentSubscription() && auth()->user()->currentSubscription()->plan_id == $plano['id'])
                                        <span class="inline-flex px-2 py-1 mt-1 text-xs font-medium bg-white text-blue-600 rounded-full">
                                            Plano Atual
                                        </span>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <div class="text-3xl font-bold">R$ {{ number_format($plano['price'], 0, ',', '.') }}</div>
                                    <div class="text-blue-100 text-sm">por {{ $plano['duration'] ?? 30 }} dias</div>
                                </div>
                            </div>
                        </div>

                        {{-- Conteúdo do Plano --}}
                        <div class="p-6 space-y-6">
                            {{-- Serviços Incluídos --}}
                            @if(count($plano['services']) > 0)
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Serviços Incluídos
                                </h4>
                                <ul class="space-y-2">
                                    @foreach($plano['services'] as $service)
                                        <li class="flex items-center text-sm text-gray-700">
                                            <div class="w-1.5 h-1.5 bg-green-500 rounded-full mr-3"></div>
                                            {{ $service }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            {{-- Serviços Adicionais --}}
                            @if(count($plano['additional_services_with_discounts'] ?? []) > 0)
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                    <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Serviços Adicionais
                                </h4>
                                <ul class="space-y-2">
                                    @foreach($plano['additional_services_with_discounts'] as $additionalService)
                                        <li class="flex items-center justify-between text-sm">
                                            <div class="flex items-center text-gray-700">
                                                <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-3"></div>
                                                {{ $additionalService['name'] }}
                                            </div>
                                            @if($additionalService['discount'] > 0)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                    </svg>
                                                    {{ $additionalService['discount'] }}% OFF
                                                </span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            {{-- Recursos/Features --}}
                            @if(count($plano['features']) > 0)
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                    <svg class="w-5 h-5 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    Recursos
                                </h4>
                                <div class="space-y-2">
                                    @foreach($plano['features'] as $key => $feature)
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-700">{{ $key }}</span>
                                            <span class="font-medium text-purple-600">{{ $feature }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>

                        {{-- Footer com Ações --}}
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                            @if(auth()->user()->hasRole('Cliente'))
                                @if(auth()->user()->currentSubscription() && auth()->user()->currentSubscription()->plan_id == $plano['id'])
                                    <div class="space-y-3">
                                        <p class="text-sm text-blue-600 font-medium text-center">✓ Você está assinando este plano</p>
                                        <button wire:click="cancelarAssinatura" 
                                                class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors font-medium">
                                            Cancelar Assinatura
                                        </button>
                                    </div>
                                @elseif(!auth()->user()->currentSubscription())
                                    <div class="space-y-3">
                                        <p class="text-xs text-gray-500 text-center">Você não possui um plano ativo</p>
                                        <button wire:click="assinarPlano('{{ $plano['id'] }}')" 
                                                class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors font-medium">
                                            Assinar Plano
                                        </button>
                                    </div>
                                @else
                                    <div class="text-center">
                                        <p class="text-xs text-gray-500">Você já possui uma assinatura ativa</p>
                                    </div>
                                @endif
                            @endif

                            @if(auth()->user()->hasRole('Proprietário'))
                                <div class="flex space-x-2">
                                    <button wire:click="editPlan('{{ $plano['id'] }}')" 
                                            class="flex-1 bg-green-600 text-white px-3 py-2 rounded-md hover:bg-green-700 transition-colors text-sm font-medium">
                                        Editar
                                    </button>
                                    <button wire:click="deletePlan('{{ $plano['id'] }}')" 
                                            class="flex-1 bg-red-600 text-white px-3 py-2 rounded-md hover:bg-red-700 transition-colors text-sm font-medium">
                                        Excluir
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Estado Vazio --}}
            <div class="text-center py-16">
                <div class="text-6xl mb-4">📋</div>
                <h3 class="text-xl font-medium text-gray-900 mb-2">Nenhum plano disponível</h3>
                <p class="text-gray-600 mb-6">Não há planos de assinatura cadastrados no momento.</p>
                @can('Proprietário')
                <button @click="openNovoPlano = true" wire:click="abrirModalNovoPlano" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Criar Primeiro Plano
                </button>
                @endcan
            </div>
        @endif
    </div>
    {{-- Modal de Edição de Plano --}}
    @if($modalAberto)
    <div x-show="open" x-cloak class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-hidden">
            {{-- Header do Modal --}}
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold">Editar Plano</h2>
                        <p class="text-blue-100 text-sm">{{ $nomePlano }}</p>
                    </div>
                    <button @click="open = false" class="text-white hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Conteúdo do Modal --}}
            <div class="p-6 max-h-96 overflow-y-auto">
                <form wire:submit.prevent="updatePlan" class="space-y-6">
                    {{-- Nome do Plano --}}
                    <div>
                        <label for="nomePlano" class="block text-sm font-medium text-gray-700 mb-1">Nome do Plano</label>
                        <input type="text" id="nomePlano" wire:model="nomePlano" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               required>
                    </div>
                    {{-- Serviços Incluídos --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Serviços Incluídos</label>
                        <div class="border border-gray-300 rounded-md p-3 bg-gray-50 min-h-[60px] flex items-center justify-between">
                            <div class="flex-1">
                                @if(count($servicosIncluidos))
                                    <div class="text-sm text-gray-700">
                                        {{ collect($todosServicos)->whereIn('id', $servicosIncluidos)->pluck('service')->join(', ') }}
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">Nenhum serviço selecionado</span>
                                @endif
                            </div>
                            <button type="button" @click="openServicos = true" 
                                    class="ml-3 inline-flex items-center px-3 py-1 border border-blue-300 text-sm font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Editar
                            </button>
                        </div>
                    </div>

                    {{-- Serviços Adicionais --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Serviços Adicionais</label>
                        <div class="border border-gray-300 rounded-md p-3 bg-gray-50 min-h-[60px] flex items-center justify-between">
                            <div class="flex-1">
                                @if(count($servicosAdicionais))
                                    <div class="text-sm text-gray-700">
                                        {{ collect($todosServicos)->whereIn('id', $servicosAdicionais)->pluck('service')->join(', ') }}
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">Nenhum serviço adicional selecionado</span>
                                @endif
                            </div>
                            <button type="button" @click="openServicosAdicionais = true" 
                                    class="ml-3 inline-flex items-center px-3 py-1 border border-blue-300 text-sm font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Editar
                            </button>
                        </div>
                        
                        {{-- Configuração de Descontos para Serviços Adicionais --}}
                        @if(count($servicosAdicionais))
                            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
                                <h5 class="text-sm font-medium text-blue-900 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    Descontos para Serviços Adicionais
                                </h5>
                                <div class="space-y-3">
                                    @foreach(collect($todosServicos)->whereIn('id', $servicosAdicionais) as $servico)
                                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                                            <span class="text-sm font-medium text-gray-700">{{ $servico->service }}</span>
                                            <div class="flex items-center space-x-2">
                                                <input type="number" 
                                                       wire:model="servicosAdicionaisDescontos.{{ $servico->id }}"
                                                       class="w-20 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" 
                                                       placeholder="0"
                                                       min="0" 
                                                       max="100"
                                                       step="0.01">
                                                <span class="text-sm text-gray-500">%</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-3 p-2 bg-blue-100 border border-blue-200 rounded text-xs">
                                    <div class="flex items-center text-blue-800 mb-1">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        <strong>Como funciona o desconto:</strong>
                                    </div>
                                    <ul class="text-blue-700 space-y-1 ml-4">
                                        <li>• Configure o percentual de desconto (0% a 100%)</li>
                                        <li>• O desconto é aplicado sobre o preço padrão do serviço</li>
                                        <li>• Exemplo: Serviço R$ 50 com 20% de desconto = R$ 40</li>
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                    {{-- Dias Permitidos --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dias permitidos para agendamento:</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            @foreach(['segunda','terca','quarta','quinta','sexta','sabado','domingo'] as $dia)
                                <label class="flex items-center p-2 border border-gray-200 rounded-md hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" wire:model="allowedDays" value="{{ $dia }}" class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm">{{ ucfirst($dia) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Preço e Duração --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="preco" class="block text-sm font-medium text-gray-700 mb-1">Preço (R$)</label>
                            <input type="number" id="preco" wire:model="preco" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   step="0.01" min="0" required>
                        </div>
                        <div>
                            <label for="duracaoDias" class="block text-sm font-medium text-gray-700 mb-1">Duração (dias)</label>
                            <input type="number" id="duracaoDias" wire:model="duracaoDias" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   min="1" required>
                        </div>
                    </div>
                    {{-- Recursos/Features --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Recursos do Plano</label>
                        <div class="space-y-3">
                            @if(is_array($planoSelecionado->features) && count($planoSelecionado->features))
                                @foreach($features_keys as $index => $key)
                                    <div class="flex items-center space-x-2 p-3 bg-gray-50 rounded-md border">
                                        <input type="text" 
                                               wire:model="features_keys.{{ $index }}" 
                                               class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" 
                                               placeholder="Nome do recurso">
                                        <input type="text" 
                                               wire:model="features_values.{{ $index }}" 
                                               class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" 
                                               placeholder="Valor">
                                        <button type="button" wire:click="removeFeature({{ $index }})" 
                                                class="text-red-600 hover:text-red-800 p-1">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4 text-gray-400">
                                    <p class="text-sm">Nenhum recurso cadastrado</p>
                                </div>
                            @endif
                            
                            <button type="button" wire:click="addFeature" 
                                    class="w-full py-2 border-2 border-dashed border-gray-300 rounded-md text-gray-600 hover:border-blue-300 hover:text-blue-600 transition-colors">
                                <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Adicionar Recurso
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Footer do Modal --}}
            <div class="bg-gray-50 px-6 py-4 border-t flex justify-end space-x-3">
                <button type="button" 
                        @click="open = false" 
                        wire:click="$set('modalAberto', false)" 
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button wire:click="updatePlan" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors font-medium">
                    Salvar Alterações
                </button>
            </div>
        </div>
    </div>


    {{-- Modal de Seleção de Serviços Incluídos --}}
    <div x-show="openServicos" x-cloak class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold">Serviços Incluídos</h2>
                        <p class="text-green-100 text-sm">Selecione os serviços que fazem parte do plano</p>
                    </div>
                    <button @click="openServicos = false" class="text-white hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Conteúdo --}}
            <div class="p-6">
                <div class="grid grid-cols-1 gap-2 max-h-80 overflow-y-auto">
                    @foreach($todosServicos->whereNotIn('id', $servicosAdicionais) as $servico)
                        <label class="flex items-center p-3 border border-gray-200 rounded-md hover:bg-gray-50 cursor-pointer transition-colors">
                            <input type="checkbox" 
                                   wire:model.live="servicosIncluidos" 
                                   value="{{ $servico->id }}" 
                                   class="mr-3 rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <span class="text-sm font-medium text-gray-900">{{ $servico->service }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Footer --}}
            <div class="bg-gray-50 px-6 py-4 border-t flex justify-end">
                <button @click="openServicos = false" 
                        class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors font-medium">
                    Confirmar Seleção
                </button>
            </div>
        </div>
    </div>
    {{-- Modal de Seleção de Serviços Adicionais --}}
    <div x-show="openServicosAdicionais" x-cloak class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold">Serviços Adicionais</h2>
                        <p class="text-purple-100 text-sm">Selecione serviços extras que podem ser contratados</p>
                    </div>
                    <button @click="openServicosAdicionais = false" class="text-white hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Conteúdo --}}
            <div class="p-6">
                <div class="grid grid-cols-1 gap-2 max-h-80 overflow-y-auto">
                    @foreach($todosServicos->whereNotIn('id', $servicosIncluidos) as $servico)
                        <label class="flex items-center p-3 border border-gray-200 rounded-md hover:bg-gray-50 cursor-pointer transition-colors">
                            <input type="checkbox" 
                                   wire:model.live="servicosAdicionais" 
                                   value="{{ $servico->id }}" 
                                   class="mr-3 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            <span class="text-sm font-medium text-gray-900">{{ $servico->service }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Footer --}}
            <div class="bg-gray-50 px-6 py-4 border-t flex justify-end">
                <button @click="openServicosAdicionais = false; syncDescontos();" 
                        class="px-6 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors font-medium">
                    Confirmar Seleção
                </button>
            </div>
        </div>
    </div>
    @endif
    {{-- Modal de Criação de Novo Plano --}}
    @if($modalNovoPlano)
    <div x-show="openNovoPlano" x-cloak class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-hidden">
            {{-- Header do Modal --}}
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold">Criar Novo Plano</h2>
                        <p class="text-green-100 text-sm">Configure os detalhes do novo plano de assinatura</p>
                    </div>
                    <button @click="openNovoPlano = false" class="text-white hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Conteúdo do Modal --}}
            <div class="p-6 max-h-96 overflow-y-auto">
                <form wire:submit.prevent="salvarNovoPlano" class="space-y-6">
                    {{-- Nome do Plano --}}
                    <div>
                        <label for="nomeNovoPlano" class="block text-sm font-medium text-gray-700 mb-1">Nome do Plano</label>
                        <input type="text" id="nomeNovoPlano" wire:model="nomePlano" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                               placeholder="Ex: Plano Básico, Premium, etc." required>
                    </div>
                <div class="mb-4">
                <div class="flex items-center">
                
                    <span class="block text-sm font-medium text-gray-700 mb-1 mr-2">Serviços incluídos: </span>
                    </div>
                
                    <div class="mt-1 px-2 py-1 border rounded-md bg-gray-50">
                       
                            <span class="size-5 text-blue-500 hover:text-blue-700 text-sm "> 
                                {{ collect($todosServicos)->whereIn('id', $servicosIncluidos)->pluck('service')->join(', ') }}
                                <button type="button" @click="openServicosNovoPlano = true" class="p-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 text-blue-500 hover:text-blue-700">
                                    <path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
                                    <path d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
                                </svg>
                            </button>
                            </span>
                        
                    </div>

                </div>

                <div class="mb-4">
                     <div class="flex items-center">
                            <span class="block text-sm font-medium text-gray-700 mb-1 mr-2">Serviços Adicionais:</span>
                            
                        </div>

                    <div class="mt-2 px-2 py-1 border rounded-md bg-gray-50">
                                 <span class="size-5 text-blue-500 hover:text-blue-700 text-sm"> 
                                {{ collect($todosServicos)->whereIn('id', $servicosAdicionais)->pluck('service')->join(', ') }}
                                <button type="button" @click="openServicosAdicionaisNovoPlano = true" class="p-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 text-blue-500 hover:text-blue-700">
                                    <path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
                                    <path d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
                                </svg>
                            </button>
                            </span>
                        
                    </div>
                    
                    {{-- Configuração de Descontos para Serviços Adicionais --}}
                    @if(count($servicosAdicionais))
                        <div class="mt-4 p-4 bg-purple-50 border border-purple-200 rounded-md animate-fade-in">
                            <h5 class="text-sm font-medium text-purple-900 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                Descontos para Serviços Adicionais
                            </h5>
                            <div class="space-y-3">
                                @foreach(collect($todosServicos)->whereIn('id', $servicosAdicionais) as $servico)
                                    <div class="flex items-center justify-between p-2 bg-white rounded border">
                                        <span class="text-sm font-medium text-gray-700">{{ $servico->service }}</span>
                                        <div class="flex items-center space-x-2">
                                            <input type="number" 
                                                   wire:model.blur="servicosAdicionaisDescontos.{{ $servico->id }}"
                                                   class="w-20 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-purple-500" 
                                                   placeholder="0"
                                                   min="0" 
                                                   max="100"
                                                   step="0.01"
                                                   title="Digite o percentual de desconto (0-100%)"
                                                   x-on:input="if($event.target.value < 0) $event.target.value = 0; if($event.target.value > 100) $event.target.value = 100;">
                                            <span class="text-sm text-gray-500">%</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-3 p-2 bg-purple-100 border border-purple-200 rounded text-xs">
                                <div class="flex items-center text-purple-800 mb-1">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                    <strong>Como funciona o desconto:</strong>
                                </div>
                                <ul class="text-purple-700 space-y-1 ml-4">
                                    <li>• Configure o percentual de desconto (0% a 100%)</li>
                                    <li>• O desconto é aplicado sobre o preço padrão do serviço</li>
                                    <li>• Exemplo: Serviço R$ 50 com 20% de desconto = R$ 40</li>
                                </ul>
                            </div>
                        </div>
                    @endif
                    
                </div>
                    {{-- Dias Permitidos --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dias permitidos para agendamento:</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            @foreach(['segunda','terca','quarta','quinta','sexta','sabado','domingo'] as $dia)
                                <label class="flex items-center p-2 border border-gray-200 rounded-md hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" wire:model="allowedDays" value="{{ $dia }}" class="mr-2 rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="text-sm">{{ ucfirst($dia) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Preço e Duração --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="precoNovo" class="block text-sm font-medium text-gray-700 mb-1">Preço (R$)</label>
                            <input type="number" id="precoNovo" wire:model="preco" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                   step="0.01" min="0" placeholder="0,00" required>
                        </div>
                        <div>
                            <label for="duracaoDiasNovo" class="block text-sm font-medium text-gray-700 mb-1">Duração (dias)</label>
                            <input type="number" id="duracaoDiasNovo" wire:model="duracaoDias" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                   min="1" placeholder="30" required>
                        </div>
                    </div>
                    {{-- Recursos/Features --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Recursos do Plano</label>
                        <div class="space-y-3">
                            @if(count($features_keys))
                                @foreach($features_keys as $index => $key)
                                    <div class="flex items-center space-x-2 p-3 bg-gray-50 rounded-md border">
                                        <input type="text" 
                                               wire:model="features_keys.{{ $index }}" 
                                               class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-green-500" 
                                               placeholder="Nome do recurso">
                                        <input type="text" 
                                               wire:model="features_values.{{ $index }}" 
                                               class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-green-500" 
                                               placeholder="Valor">
                                        <button type="button" wire:click="removeFeature({{ $index }})" 
                                                class="text-red-600 hover:text-red-800 p-1">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4 text-gray-400">
                                    <p class="text-sm">Nenhum recurso cadastrado</p>
                                </div>
                            @endif
                            
                            <button type="button" wire:click="addFeature" 
                                    class="w-full py-2 border-2 border-dashed border-gray-300 rounded-md text-gray-600 hover:border-green-300 hover:text-green-600 transition-colors">
                                <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Adicionar Recurso
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Footer do Modal --}}
            <div class="bg-gray-50 px-6 py-4 border-t flex justify-end space-x-3">
                <button type="button" 
                        @click="openNovoPlano = false" 
                        wire:click="$set('modalNovoPlano', false)" 
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button wire:click="salvarNovoPlano" 
                        class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors font-medium">
                    Criar Plano
                </button>
            </div>
        </div>
    </div>

    {{-- Modal de Seleção de Serviços Incluídos para Novo Plano --}}
    <div x-show="openServicosNovoPlano" x-cloak class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold">Serviços Incluídos - Novo Plano</h2>
                        <p class="text-green-100 text-sm">Selecione os serviços que fazem parte do plano</p>
                    </div>
                    <button @click="openServicosNovoPlano = false" class="text-white hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Conteúdo --}}
            <div class="p-6">
                <div class="grid grid-cols-1 gap-2 max-h-80 overflow-y-auto">
                    @foreach($todosServicos as $servico)
                        <label class="flex items-center p-3 border border-gray-200 rounded-md hover:bg-gray-50 cursor-pointer transition-colors">
                            <input type="checkbox" 
                                   wire:model.live="servicosIncluidos" 
                                   value="{{ $servico->id }}" 
                                   class="mr-3 rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <span class="text-sm font-medium text-gray-900">{{ $servico->service }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Footer --}}
            <div class="bg-gray-50 px-6 py-4 border-t flex justify-end">
                <button @click="openServicosNovoPlano = false" 
                        class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors font-medium">
                    Confirmar Seleção
                </button>
            </div>
        </div>
    </div>

    {{-- Modal de Seleção de Serviços Adicionais para Novo Plano --}}
    <div x-show="openServicosAdicionaisNovoPlano" x-cloak class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold">Serviços Adicionais - Novo Plano</h2>
                        <p class="text-purple-100 text-sm">Selecione serviços extras que podem ser contratados</p>
                    </div>
                    <button @click="openServicosAdicionaisNovoPlano = false" class="text-white hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Conteúdo --}}
            <div class="p-6">
                <div class="grid grid-cols-1 gap-2 max-h-80 overflow-y-auto">
                    @foreach($todosServicos->whereNotIn('id', $servicosIncluidos) as $servico)
                        <label class="flex items-center p-3 border border-gray-200 rounded-md hover:bg-gray-50 cursor-pointer transition-colors">
                            <input type="checkbox" 
                                   wire:model.live="servicosAdicionais" 
                                   value="{{ $servico->id }}" 
                                   class="mr-3 rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                                   x-on:change="setTimeout(() => syncDescontos(), 100)">
                            <span class="text-sm font-medium text-gray-900">{{ $servico->service }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Footer --}}
            <div class="bg-gray-50 px-6 py-4 border-t flex justify-end">
                <button @click="openServicosAdicionaisNovoPlano = false; syncDescontos();" 
                        class="px-6 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors font-medium">
                    Confirmar Seleção
                </button>
            </div>
        </div>
    </div>
@endif

</div>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
</div>

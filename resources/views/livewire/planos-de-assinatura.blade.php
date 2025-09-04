
<div>
    
    @if (session()->has('message') || session()->has('warning'))

            <div class="flex items-center p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
  <svg class="shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
  </svg>
  <span class="sr-only">Info</span>
  <div>
    <span class="font-medium">{{ session('message') }}</span>
    <span class="font-medium">{{ session('warning') }}</span>
  </div>
</div>

    @endif



<div x-data="{ open: @entangle('modalAberto'), openServicos: false , openServicosAdicionais: false, openNovoPlano: @entangle('modalNovoPlano'),
openServicosNovoPlano: false,
openServicosAdicionaisNovoPlano: false, }" class="min-h-screen bg-gray-100">
    <div class="container mx-auto px-4 py-8">
    @can('Proprietário')
        @include('includes.messages')
    <div class="mb-4 flex justify-end">
        <button @click="openNovoPlano = true" wire:click="abrirModalNovoPlano" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            + Novo Plano
        </button>
    </div>
    @endcan

        
            <h1 class="text-2xl text-center font-bold mb-6">Planos de Assinatura</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    
            @foreach($planos as $plano)
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-xl font-semibold">Plano: {{ $plano['name'] }}</h2>
                    <p class="text-gray-600">Serviços incluídos:</p>
                    <ul class="list-disc list-inside">
                        @foreach($plano['services'] as $service)
                            <li>{{ $service }}</li>
                        @endforeach
                    </ul>
                    <p class="text-gray-600">Serviços adicionais:</p>
                    <ul class="list-disc list-inside">
                        @foreach($plano['additional_services'] as $additionalService)
                            <li>{{ $additionalService }}</li>
                        @endforeach
                    </ul>
                    
                    <p class="text-gray-600">Mais Informações:</p>
                    <ul class="list-disc list-inside">
                    @foreach($plano['features'] as $index=>$feature)
                        <li >{{$index}}:  {{ $feature }}</li>
                    @endforeach   
                    </ul>                
                    
                    <p class="text-lg font-bold mb-4">R$ {{ number_format($plano['price'], 2, ',', '.') }}</p>
                    @if(auth()->user()->hasRole('Cliente')  && auth()->user()->currentSubscription() && auth()->user()->currentSubscription()->plan_id == $plano['id'])
                        <p class="text-lg font-bold text-blue-500 mb-4">Você assina atualmente este plano</p>
                        <button wire:click="cancelarAssinatura" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                            Cancelar Assinatura
                        </button>
                    @elseif(auth()->user()->hasRole('Cliente') && !auth()->user()->currentSubscription())
                        <p class="text-sm text-gray-500 mb-4">Você não possui um plano ativo.</p>
                    <button  wire:click="assinarPlano('{{ $plano['id'] }}')" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Assinar
                    </button>
                    @endif
                    @if(auth()->user()->hasRole('Proprietário') )
                        <button wire:click="editPlan('{{ $plano['id'] }}')" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                            Editar
                        </button>
                        <button wire:click="deletePlan('{{ $plano['id'] }}')" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                            Excluir
                        </button>
                        
                    @endif
                    
                </div>
            @endforeach
        </div>
    </div>
  @if($modalAberto)
    <div 
        x-data="{ open: @entangle('modalAberto') }" 
        x-show="open" 
        x-cloak 
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
    >
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold">Editar Plano: {{ $nomePlano }}</h2>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>
            <form wire:submit.prevent="updatePlan">
                <div class="mb-4">
                    <label for="nomePlano" class="block text-sm font-medium text-gray-700">Nome do Plano</label>
                    <input type="text" id="nomePlano" wire:model="nomePlano" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                    </div>
                <div class="mb-4">
                <div class="flex items-center">
                
                    <span class="block text-sm font-medium text-gray-700 mb-1 mr-2">Serviços incluídos: </span>
                    </div>
                
                    <div class="mt-1 px-2 py-1 border rounded-md bg-gray-50">
                        @if(count($servicosIncluidos))
                            <span class="size-5 text-blue-500 hover:text-blue-700 text-sm "> 
                                {{ collect($todosServicos)->whereIn('id', $servicosIncluidos)->pluck('service')->join(', ') }}
                                <button type="button" @click="openServicos = true" class="p-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 text-blue-500 hover:text-blue-700">
                                    <path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
                                    <path d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
                                </svg>
                            </button>
                            </span>
                        @else
                            <span class="text-sm text-gray-400">Nenhum serviço selecionado</span>
                        @endif
                    </div>

                </div>

                <div class="mb-4">
                     <div class="flex items-center">
                            <span class="block text-sm font-medium text-gray-700 mb-1 mr-2">Serviços Adicionais:</span>
                            
                        </div>

                    <div class="mt-2 px-2 py-1 border rounded-md bg-gray-50">
                        @if(count($servicosAdicionais))
                            <span class="size-5 text-blue-500 hover:text-blue-700 text-sm"> 
                                {{ collect($todosServicos)->whereIn('id', $servicosAdicionais)->pluck('service')->join(', ') }}
                                <button type="button" @click="openServicosAdicionais = true" class="p-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 text-blue-500 hover:text-blue-700">
                                    <path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
                                    <path d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
                                </svg>
                            </button>
                            </span>
                        @else
                            <span class=" size-5 text-blue-500 hover:text-blue-700 text-sm ">Nenhum serviço adicional selecionado</span>
                        @endif
                    </div>
                <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Dias permitidos para agendamento:</label>
                <div class="flex flex-wrap gap-2 mt-2">
                    @foreach(['segunda','terca','quarta','quinta','sexta','sabado','domingo'] as $dia)
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="allowedDays" value="{{ $dia }}" class="mr-1">
                            {{ ucfirst($dia) }}
                        </label>
                    @endforeach
                </div>
                    
                </div>
                   
                    <label for="preco" class="block text-sm font-medium text-gray-700">Preço</label>
                    <input type="number" id="preco" wire:model="preco" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                    <label for="duracaoDias" class="block text-sm font-medium text-gray-700">Duração (dias)</label>
                    <input type="number" id="duracaoDias" wire:model="duracaoDias" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                    <div class="mb-4">
                        <label for="features" class="block text-sm font-medium text-gray-700">Recursos</label>
                        @if(is_array($planoSelecionado->features) && count($planoSelecionado->features))
                        @foreach($features_keys as $index => $key)
                        <div class="flex items-center mb-2">
                            <input 
                            type="text" 
                            wire:model="features_keys.{{ $index }}" class="w-1/2 border-gray-300 rounded-md shadow-sm mr-2 text-sm" placeholder="Nome"
                            />
                            <input 
                            type="text" 
                            wire:model="features_values.{{ $index }}" class="w-1/3  border-gray-300 rounded-md shadow-sm mr-2 text-sm" 
                            placeholder="Valor"
                            />
                            <button type="button" wire:click="removeFeature({{ $index }})" class="text-red-500 hover:text-red-700">&times;</button>
                        </div>
                        @endforeach
                        @else
                            <span class="text-gray-400">Nenhuma feature cadastrada</span>
                        @endif
                        <button type="button" wire:click="addFeature" class="mt-2 px-2 py-1 bg-blue-500 text-white rounded">Adicionar Feature</button>
                    </div>
               
                <!-- Adicione outros campos conforme necessário -->
                <div class="flex justify-end">
                    <button type="button" 
                    @click="open = false" 
                    wire:click="$set('modalAberto', false)" 
                    class="mr-2 px-4 py-2 rounded bg-gray-200">Cancelar</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Salvar</button>
                </div>
            </form>
        </div>
    </div>


    <!-- Modal de seleção de serviços incluídos -->
    <div 
        
        x-show="openServicos" 
        x-cloak 
        :key="JSON.stringify(@entangle('servicosIncluidos'))"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
    >
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold">Selecione os Serviços Incluídos</h2>
                <button @click="openServicos = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>
            <div class="grid grid-cols-2 gap-2 mb-4">
                @foreach($todosServicos->whereNotIn('id', $servicosAdicionais) as $servico)
                    <label class="flex items-center">
                        <input type="checkbox" wire:model.live="servicosIncluidos" value="{{ $servico->id }}" class="mr-2">
                        {{ $servico->service }}
                    </label>
                @endforeach
            </div>
            <div class="flex justify-end">
                <button @click="openServicos = false" class="px-4 py-2 rounded bg-blue-500 text-white">OK</button>
            </div>
        </div>
    </div>
    <!-- Modal de seleção de serviços adicionais -->
    <div 
        
        x-show="openServicosAdicionais" 
        x-cloak 
        :key="JSON.stringify(@entangle('servicosAdicionais'))"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
    >
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold">Selecione os Serviços Adicionais</h2>
                <button @click="openServicosAdicionais = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>
            <div class="grid grid-cols-2 gap-2 mb-4">
                @foreach($todosServicos->whereNotIn('id', $servicosIncluidos) as $servico)
                    <label class="flex items-center">
                        <input type="checkbox" wire:model.live="servicosAdicionais" value="{{ $servico->id }}" class="mr-2">
                        {{ $servico->service }}
                    </label>
                @endforeach
            </div>
            <div class="flex justify-end">
                <button @click="openServicosAdicionais = false" class="px-4 py-2 rounded bg-blue-500 text-white">OK</button>
            </div>
        </div>
    </div>
    @endif
    @if($modalNovoPlano)
<!-- Modal para criar um novo plano -->
    <div 
    x-show="openNovoPlano"
    x-cloak
    :key="$modalNovoPlano"
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
>
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold">Novo Plano</h2>
            <button @click="openNovoPlano = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <form wire:submit.prevent="salvarNovoPlano">
       
            <!-- Repita os campos do modal de edição, mas usando wire:model nos mesmos atributos -->
            <!-- ...nomePlano, preco, duracaoDias, seleção de serviços, features... -->
            <!-- Use os mesmos campos e lógica do modal de edição -->
            
            
        <!--    <form wire:submit.prevent="updatePlan"> -->
                <div class="mb-4">
                    <label for="nomePlano" class="block text-sm font-medium text-gray-700">Nome do Plano</label>
                    <input type="text" id="nomePlano" wire:model="nomePlano" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
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
                    
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Dias permitidos para agendamento:</label>
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach(['segunda','terca','quarta','quinta','sexta','sabado','domingo'] as $dia)
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="allowedDays" value="{{ $dia }}" class="mr-1">
                                {{ ucfirst($dia) }}
                            </label>
                        @endforeach
                    </div>
                </div>
                   
                    <label for="preco" class="block text-sm font-medium text-gray-700">Preço</label>
                    <input type="number" id="preco" wire:model="preco" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                    <label for="duracaoDias" class="block text-sm font-medium text-gray-700">Duração (dias)</label>
                    <input type="number" id="duracaoDias" wire:model="duracaoDias" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                    <div class="mb-4">
                        <label for="features" class="block text-sm font-medium text-gray-700">Recursos</label>
                        @if(count($features_keys))
                    @foreach($features_keys as $index => $key)
                        <div class="flex items-center mb-2">
                            <input 
                                type="text" 
                                wire:model="features_keys.{{ $index }}" 
                                class="w-1/2 border-gray-300 rounded-md shadow-sm mr-2 text-sm" 
                                placeholder="Nome"
                            />
                            <input 
                                type="text" 
                                wire:model="features_values.{{ $index }}" 
                                class="w-1/2 border-gray-300 rounded-md shadow-sm mr-2 text-sm" 
                                placeholder="Valor"
                            />
                            <button type="button" wire:click="removeFeature({{ $index }})" class="text-red-500 hover:text-red-700">&times;</button>
                        </div>
                    @endforeach
                @else
                    <span class="text-gray-400">Nenhuma feature cadastrada</span>
                @endif
                        
                        
                        
                        <button type="button" wire:click="addFeature" class="mt-2 px-2 py-1 bg-blue-500 text-white rounded">Adicionar Feature</button>




            <div class="flex justify-end">
                <button type="button" 
                @click="openNovoPlano = false" 
                wire:click="$set('modalNovoPlano', false)" 
                class="mr-2 px-4 py-2 rounded bg-gray-200">Cancelar</button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Salvar</button>
            </div>
        </form>
    </div>

    <!-- Modal de seleção de serviços incluídos para novo plano -->
<div 
    x-show="openServicosNovoPlano"
    x-cloak
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
>
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold">Selecione os Serviços Incluídos</h2>
            <button @click="openServicosNovoPlano = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <div class="grid grid-cols-2 gap-2 mb-4">
            @foreach($todosServicos as $servico)
                <label class="flex items-center">
                    <input type="checkbox" wire:model.live="servicosIncluidos" value="{{ $servico->id }}" class="mr-2">
                    {{ $servico->service }}
                </label>
            @endforeach
        </div>
        <div class="flex justify-end">
            <button @click="openServicosNovoPlano = false" class="px-4 py-2 rounded bg-blue-500 text-white">OK</button>
        </div>
    </div>
</div>

<!-- Modal de seleção de serviços adicionais para novo plano -->
<div 
    x-show="openServicosAdicionaisNovoPlano"
    x-cloak
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
>
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold">Selecione os Serviços Adicionais</h2>
            <button @click="openServicosAdicionaisNovoPlano = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <div class="grid grid-cols-2 gap-2 mb-4">
            @foreach($todosServicos->whereNotIn('id', $servicosIncluidos) as $servico)
                <label class="flex items-center">
                    <input type="checkbox" wire:model.live="servicosAdicionais" value="{{ $servico->id }}" class="mr-2">
                    {{ $servico->service }}
                </label>
            @endforeach
        </div>
        <div class="flex justify-end">
            <button @click="openServicosAdicionaisNovoPlano = false" class="px-4 py-2 rounded bg-blue-500 text-white">OK</button>
        </div>
    </div>
</div>
@endif

</div>
</div>

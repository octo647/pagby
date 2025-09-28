<div>
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Layout em Cards para todos os serviços -->
    <div class="space-y-6 p-4">
        @foreach($salon_serv as $index=>$service)
            @php
                $hasAnyBranchPricingOpen = collect($showBranchPricing ?? [])->contains(true);
                $isThisServiceBranchPricingOpen = $showBranchPricing[$service['id']] ?? false;
            @endphp
            
            <!-- Mostrar apenas o serviço sendo editado OU configurado por filial OU todos quando nenhum está ativo -->
            @if($editedServiceIndex === $index || 
                ($hasAnyBranchPricingOpen && $isThisServiceBranchPricingOpen) ||
                ($editedServiceIndex === null && !$hasAnyBranchPricingOpen))
            <div wire:key="{{$service['id']}}" class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <!-- Cabeçalho do Card -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <!-- Foto do Serviço -->
                            <div class="flex-shrink-0">
                                @if(isset($service['photo']) && $service['photo'] instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                                    <img src="{{ $service['photo']->temporaryUrl() }}" class="w-16 h-16 object-cover rounded-lg border-2 border-gray-200" />
                                @elseif(!empty($service['photo']))
                                    <img src="{{ asset('/services/' . $service['photo']) }}" alt="Foto do serviço" class="w-16 h-16 object-cover rounded-lg border-2 border-gray-200" />
                                @else
                                    <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center border-2 border-gray-200">
                                        <span class="text-gray-400 text-2xl">📋</span>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Nome do Serviço -->
                            <div>
                                @if($editedServiceIndex !== $index)
                                    <h3 class="text-lg font-semibold text-gray-900">{{$service['service']}}</h3>
                                    <div class="flex space-x-4 mt-1">
                                        <span class="text-sm text-gray-600">💰 R$ {{number_format($service['price'], 2, ',', '.')}}</span>
                                        <span class="text-sm text-gray-600">⏰ {{$service['time']}} min</span>
                                    </div>
                                @else
                                    <input class="text-lg font-semibold bg-white border border-gray-300 rounded px-3 py-1 w-full max-w-md" type='text'  wire:model.defer='salon_serv.{{$index}}.service' placeholder="Nome do serviço">
                                @endif
                            </div>
                        </div>
                        
                        <!-- Botões de Ação -->
                        <div class="flex space-x-2">
                            @if($editedServiceIndex !== $index)
                                <button wire:click.prevent="editService({{$index}})" class="inline-flex items-center px-3 py-1 border border-blue-300 text-sm font-medium rounded text-blue-700 bg-blue-50 hover:bg-blue-100">
                                    ✏️ Editar
                                </button>
                                <button wire:click.prevent="deleteService({{$index}})" class="inline-flex items-center px-3 py-1 border border-red-300 text-sm font-medium rounded text-red-700 bg-red-50 hover:bg-red-100" onclick="return confirm('Tem certeza que deseja apagar este serviço?')">
                                    🗑️ Apagar
                                </button>
                            @else
                                <button wire:click.prevent="updateService({{$index}})" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded text-white bg-green-600 hover:bg-green-700">
                                    💾 Salvar
                                </button>
                                <button wire:click.prevent="$set('editedServiceIndex', null)" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                    ❌ Cancelar
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Conteúdo do Card -->
                <div class="px-6 py-4">
                    @if($editedServiceIndex === $index)
                        <!-- Formulário de Edição -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Preço Padrão (R$)</label>
                                <input class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" type='text'  wire:model.defer='salon_serv.{{$index}}.price' placeholder="0,00">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tempo (minutos)</label>
                                <input class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" type='text'  wire:model.defer='salon_serv.{{$index}}.time' placeholder="60">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nova Foto</label>
                                <input type="file" wire:model="salon_serv.{{$index}}.photo" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" accept="image/*">
                            </div>
                        </div>
                    @endif
                    
                    <!-- Configurações por Filial -->
                    @if($service['id'] && !empty($branches))
                        <div class="border-t border-gray-200 pt-4">
                            <button 
                                type="button" 
                                class="inline-flex items-center px-4 py-2 border border-blue-300 text-sm font-medium rounded text-blue-700 bg-blue-50 hover:bg-blue-100 mb-3"
                                wire:click="toggleBranchPricing({{ $service['id'] }})"
                            >
                                @if($showBranchPricing[$service['id']] ?? false)
                                    🔽 Ocultar configurações por filial
                                @else
                                    ⚙️ Configurar preços por filial
                                @endif
                            </button>
                            
                            @if($showBranchPricing[$service['id']] ?? false)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h4 class="font-medium text-gray-900 mb-4">Preços específicos por filial: {{ $service['service'] }}</h4>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($branches as $branch)
                                            @php
                                                $branchPrice = $branchServices[$service['id']][$branch->id]['price'] ?? '';
                                                $branchDuration = $branchServices[$service['id']][$branch->id]['duration_minutes'] ?? '';
                                            @endphp
                                            
                                            <div class="border border-gray-200 rounded-lg p-3 bg-white">
                                                <div class="flex items-center justify-between mb-2">
                                                    <h5 class="font-medium text-sm text-gray-800">{{ $branch->branch_name }}</h5>
                                                    @if($branchPrice)
                                                        <button 
                                                            type="button" 
                                                            class="text-red-600 hover:text-red-900 text-xs"
                                                            wire:click="removeBranchPrice({{ $service['id'] }}, {{ $branch->id }})"
                                                            onclick="return confirm('Remover configuração desta filial?')"
                                                        >
                                                            ❌ Remover
                                                        </button>
                                                    @endif
                                                </div>
                                                
                                                <div class="space-y-2">
                                                    <div>
                                                        <label class="block text-xs text-gray-600 mb-1">Preço (R$)</label>
                                                        <input 
                                                            type="number" 
                                                            step="0.01" 
                                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                                            wire:model.defer="branchPrices.{{ $service['id'] }}.{{ $branch->id }}.price"
                                                            placeholder="@if($branchPrice){{ $branchPrice }}@else{{ $service['price'] }} (padrão)@endif"
                                                        >
                                                        @if($branchPrice)
                                                            <small class="text-xs text-green-600">✓ Configurado: R$ {{ number_format($branchPrice, 2, ',', '.') }}</small>
                                                        @endif
                                                    </div>
                                                    
                                                    <div>
                                                        <label class="block text-xs text-gray-600 mb-1">Duração (min)</label>
                                                        <input 
                                                            type="number" 
                                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                                            wire:model.defer="branchDurations.{{ $service['id'] }}.{{ $branch->id }}.duration"
                                                            placeholder="@if($branchDuration){{ $branchDuration }}@else{{ $service['time'] }} (padrão)@endif"
                                                        >
                                                        @if($branchDuration)
                                                            <small class="text-xs text-green-600">✓ Configurado: {{ $branchDuration }} min</small>
                                                        @endif
                                                    </div>
                                                    
                                                    <button 
                                                        type="button" 
                                                        class="w-full bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700"
                                                        wire:click="saveBranchConfiguration({{ $service['id'] }}, {{ $branch->id }})"
                                                    >
                                                        @if($branchPrice || $branchDuration)
                                                            💾 Atualizar Configuração
                                                        @else
                                                            💾 Criar Configuração
                                                        @endif
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            @endif
        @endforeach
        
        <!-- Botão Adicionar Serviço -->
        @if($editedServiceIndex === null && !collect($showBranchPricing ?? [])->contains(true))
        <div class="text-center py-6">
            <button type="button" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 shadow-sm" wire:click.prevent="addService({{($salon_serv ? count($salon_serv) : 0)}})">
                ➕ Adicionar novo serviço
            </button>   
        </div>
        @endif
    </div>
</div>

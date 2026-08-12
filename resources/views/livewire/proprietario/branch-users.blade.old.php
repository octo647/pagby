<style>
@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.2; }
}
.animate-blink {
    animation: blink 1s infinite;
}
</style>
<div>
    {{-- Gestão de Funcionários - Interface Moderna --}}
    
    {{-- Notificações de Sucesso/Erro --}}
    @if (session()->has('message') || session()->has('error'))
        <div class="mb-6">
            @if(session()->has('message'))
                <div class="flex items-center p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200" role="alert">
                    <svg class="shrink-0 inline w-5 h-5 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <span class="sr-only">Info</span>
                    <div><span class="font-medium">{{ session('message') }}</span></div>
                </div>
            @endif
            @if(session()->has('error'))
                <div class="flex items-center p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
                    <svg class="shrink-0 inline w-5 h-5 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <span class="sr-only">Error</span>
                    <div><span class="font-medium">{{ session('error') }}</span></div>
                </div>
            @endif
        </div>
    @endif

    <div x-data="{ editingUser: @entangle('editedUserIndex') }" class="min-h-screen bg-gray-50">
    </div>
    
    {{-- Cabeçalho da Página --}}
    <div class="bg-white border-b border-gray-200 mb-8">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900">Gestão de Funcionários</h1>
                    <p class="text-gray-600 mt-1">Gerencie funcionários e suas filiais de atuação</p>
                </div>
                
                {{-- Indicador de Limite de Funcionários --}}
                <div class="flex items-center gap-3">
                    <div class="inline-flex items-center px-4 py-2 rounded-lg {{ $currentEmployeeCount >= $employeeLimit ? 'bg-red-100 border border-red-300' : 'bg-blue-100 border border-blue-300' }}">
                        <svg class="w-5 h-5 mr-2 {{ $currentEmployeeCount >= $employeeLimit ? 'text-red-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <div class="text-left">
                            <span class="text-sm font-semibold {{ $currentEmployeeCount >= $employeeLimit ? 'text-red-700' : 'text-blue-700' }} block">
                                Funcionários: {{ $currentEmployeeCount }}/{{ $employeeLimit }}
                            </span>
                            @if($currentEmployeeCount >= $employeeLimit)
                                <span class="text-xs {{ $currentEmployeeCount >= $employeeLimit ? 'text-red-600' : 'text-blue-600' }}">Limite atingido</span>
                            @endif
                        </div>
                    </div>
                    
                    <button wire:click="openAddModal" 
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg shadow-md hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Adicionar Funcionário
                </button>
            </div>
        </div>
    </div>

    {{-- Container Principal --}}
    <div class="container mx-auto px-4 pb-8">
        {{-- Lista de Funcionários - Design Moderno --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            
            {{-- Cabeçalho da Lista com Busca --}}
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h2 class="text-lg font-medium text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Funcionários
                        <span class="ml-2 text-sm text-gray-500">({{ count($branchUsersList) }})</span>
                    </h2>
                    <div class="relative flex items-center gap-4">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" 
                               wire:model.live="search" 
                               placeholder="Buscar por nome ou email..."
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <label class="flex items-center ml-4 select-none cursor-pointer">
                            <input type="checkbox" wire:model.live="showOnlyActive" class="form-checkbox h-4 w-4 text-blue-600 transition duration-150 ease-in-out">
                            <span class="ml-2 text-sm text-gray-700">Apenas ativos</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Lista de Funcionários --}}
            <div class="divide-y divide-gray-200">
          
                @forelse($branchUsersList as $index => $user)
                    <div wire:key="user-{{$index}}" class="p-6 hover:bg-gray-50 transition-colors">
                        
                        {{-- Modo Visualização --}}
                        @if($editedUserIndex !== $index)
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-center space-x-4 flex-1">
                                    {{-- Avatar --}}
                                    <div class="flex-shrink-0">
                                        @if(isset($user['user_photo']) && $user['user_photo'])
                                            @php
                                                $isExternal = Str::startsWith($user['user_photo'], ['http://', 'https://']);
                                            @endphp
                                            <img src="{{ $isExternal ? $user['user_photo'] : tenant_asset('profile-photos/' . basename($user['user_photo'])) }}" 
                                                class="h-12 w-12 rounded-full object-cover border-2 border-gray-200" 
                                                alt="Foto">
                                        @else
                                            <div class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                                <span class="text-lg font-medium text-white uppercase">
                                                    {{ substr($user['user_name'], 0, 2) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Informações do Funcionário --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-3">
                                            <h3 class="text-lg font-medium text-gray-900">{{ $user['user_name'] }}</h3>
                                            
                                            {{-- Badge de Status --}}
                                            @if(isset($user['status']) && $user['status'] === 'Ativo')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Ativo
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Inativo
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-500">{{ $user['user_email'] }}</p>
                                        
                                        {{-- Filial --}}
                                        <div class="mt-2 flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            @if($user['branch_name'])
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    {{ $user['branch_name'] }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 animate-blink">
                                                    <svg class="w-3 h-3 mr-1 text-yellow-600" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8"/></svg>
                                                    Sem filial
                                                </span>
                                            </div>
                                            
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Botões de Ação --}}
                                <div class="mt-4 sm:mt-0 flex flex-col sm:flex-row gap-2">
                                    {{-- Toggle Status --}}
                                    <button wire:click="toggleUserStatus({{ $user['user_id'] }})" 
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md {{ isset($user['status']) && $user['status'] === 'Ativo' ? 'text-red-700 bg-red-50 hover:bg-red-100' : 'text-green-700 bg-green-50 hover:bg-green-100' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                                        @if(isset($user['status']) && $user['status'] === 'Ativo')
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                            </svg>
                                            Desativar
                                        @else
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Ativar
                                        @endif
                                    </button>
                                    
                                    {{-- Botão Editar --}}
                                    <button wire:click="editUser({{$index}})" 
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Editar
                                    </button>
                                </div>
                            </div>
                        
                        {{-- Modo Edição --}}
                        @else
                            <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg p-6 border border-blue-200">
                                <div class="flex items-center mb-4">
                                    <div class="flex-shrink-0">
                                        @if(isset($user['user_photo']))
                                                <img src="{{ tenant_asset('profile-photos/' . basename($user['user_photo'])) }}" 
                                                    class="h-12 w-12 rounded-full object-cover border-2 border-blue-200" 
                                                    alt="Foto do funcionário">
                                        @else
                                            <div class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                                <span class="text-lg font-medium text-white uppercase">
                                                    {{ substr($user['user_name'], 0, 2) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-medium text-gray-900">Editando: {{ $user['user_name'] }}</h3>
                                        <p class="text-sm text-gray-600">{{ $user['user_email'] }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {{-- Seleção de Filial --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            Filial
                                        </label>
                                        <select wire:model='branchUsers.{{$index}}.branch_id'
                                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-white">
                                            <option value="">Escolha uma filial</option>
                                            @foreach($branches as $branch)
                                                <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                                            @endforeach
                                @endif
                                        </select>
                                    </div>
                                    
                                    {{-- Upload de Foto --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            Foto do Funcionário
                                        </label>
                                        <div class="mt-1 flex items-center space-x-4">
                                            @if(isset($user['photo']))
                                                   <img src="{{ $user['photo']->temporaryUrl() }}" 
                                                       class="w-16 h-16 rounded-full object-cover border-2 border-gray-200" 
                                                       alt="Foto atual">
                                            @endif
                                            <div class="flex-1">
                                                <label class="relative cursor-pointer bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500 transition-colors">
                                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                    </svg>
                                                    Escolher foto
                                                    <input type="file" wire:model="branchUsers.{{$index}}.photo" 
                                                           class="sr-only" accept="image/*">
                                                </label>
                                                <p class="mt-1 text-xs text-gray-500">PNG, JPG, JPEG até 2MB</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Botões de Ação --}}
                                <div class="mt-6 flex justify-end space-x-3">
                                    <button wire:click="cancelEdit" 
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Cancelar
                                    </button>
                                    <button wire:click="updateBranchUser({{ $user['user_id'] }}, 'null')" 
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Salvar
                                    </button>
                                </div>
                            </div>
                        
                    </div>
                @empty
                    <div class="px-6 py-12 text-center text-gray-500">
                        Nenhum funcionário encontrado.
                    </div>                
                @endforelse
            </div>
        </div>
    </div>
    
    {{-- Modal de Adicionar Funcionário será adicionado aqui se necessário --}}
    

</div>
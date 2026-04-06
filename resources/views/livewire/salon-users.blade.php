<div>
   
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            
    
    {{-- Notificações de Sucesso/Aviso --}}
    @if (session()->has('message') || session()->has('warning') || session()->has('error'))
        <div class="mb-6">
            <div class="flex items-center p-4 text-sm {{ session()->has('error') ? 'text-red-800 bg-red-50 border-red-200' : 'text-green-800 bg-green-50 border-green-200' }} rounded-lg border" role="alert">
                <svg class="shrink-0 inline w-5 h-5 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <span class="sr-only">Info</span>
                <div>
                    <span class="font-medium">{{ session('message') ?? session('warning') ?? session('error') }}</span>
                </div>
            </div>
        </div>
    @endif

<div x-data="{ showModal: @entangle('showModal') }" class="min-h-screen bg-gray-50">
    
    {{-- Cabeçalho da Página --}}
    

    {{-- Container Principal --}}
    <div class="container mx-auto px-4 pb-8">

        <div class="mb-4">
        
   
    
        <div class="relative">
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
            <svg class="w-4 h-4 text-gray-500 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
            </svg>
        </div>
        <input type="text" wire:model.live="searchTerm" id="default-search" class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Pesquisar usuário..." required />
        </div>
        </div>

        {{-- Lista de Usuários - Design Moderno --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            
            {{-- Cabeçalho da Tabela --}}
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200 hidden md:block">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</div>
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Status</div>
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</div>
                </div>
            </div>

            {{-- Lista de Usuários --}}
            <div class="divide-y divide-gray-200">
                @forelse($salon_users as $user)
                    <div class="p-6 hover:bg-gray-50 transition-colors cursor-pointer"
                         x-data
                         @click="
                            if (!($event.target.closest('button') || $event.target.closest('select') || $event.target.closest('input'))) {
                                $wire.showUserDetails({{ $user->id }});
                            }
                         "
                    >
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                            
                            {{-- Nome do Usuário --}}
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    @if($user->photo)
                                        @php
                                            $isExternal = Str::startsWith($user->photo, ['http://', 'https://']);
                                        @endphp
                                        <img src="{{ $isExternal ? $user->photo : tenant_asset($user->photo) }}" 
                                             alt="{{ $user->name }}"
                                             class="h-10 w-10 rounded-full object-cover border-2 border-gray-200">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white uppercase">
                                                {{ substr($user->name, 0, 2) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $user->name }}
                                    </div>
                                    <p class="text-sm text-gray-500 truncate">
                                        {{ $user->email ?? 'Email não informado' }}
                                    </p>
                                </div>
                            </div>

                            {{-- Status e Papéis --}}
                            <div>
                                @if($editingUserId === $user->id)
                                    <div class="mb-2">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                                        <select wire:model="editingStatus" 
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            <option value="Ativo">Ativo</option>
                                            <option value="Inativo">Inativo</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Papéis</label>
                                        <select wire:model.defer="editingRoles" multiple
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            @foreach($roles as $role)
                                                <option value="{{ $role }}">{{ $role }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-gray-400">Segure Ctrl (Windows) ou Command (Mac) para selecionar múltiplos papéis</small>
                                    </div>
                                @else
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center">
                                            @if($user->status === 'Ativo')
                                                <div class="h-2 w-2 bg-green-400 rounded-full mr-2"></div>
                                                <span class="text-sm font-medium text-green-800">Ativo</span>
                                            @else
                                                <div class="h-2 w-2 bg-red-400 rounded-full mr-2"></div>
                                                <span class="text-sm font-medium text-red-800">Inativo</span>
                                            @endif
                                        </div>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach($user->roles as $role)
                                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ $role->role }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Ações --}}
                            <div class="flex items-center space-x-2">
                                @if($editingUserId === $user->id)
                                    <button wire:click.stop="saveUser" 
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Salvar
                                    </button>
                                    <button wire:click.stop="cancelEdit" 
                                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Cancelar
                                    </button>
                                @else
                                    <button wire:click.stop="editUser({{ $user->id }})" 
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Editar
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum usuário encontrado</h3>
                        <p class="mt-1 text-sm text-gray-500">Não há usuários correspondentes aos critérios de pesquisa.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Paginação Moderna --}}
    <div class="mt-6">               
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 rounded-lg shadow-sm">
            {{ $salon_users->links() }}
        </div>
    </div>
    {{-- Modal de Detalhes do Usuário - Design Moderno --}}
    <div x-show="showModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        
        {{-- Backdrop --}}
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false"></div>
            
            {{-- Centralizador --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            {{-- Modal --}}
            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6">
                
                {{-- Cabeçalho do Modal --}}
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            @if(isset($userDetails['photo']) && $userDetails['photo'])
                                @php
                                    $isExternal = Str::startsWith($userDetails['photo'], ['http://', 'https://']);
                                @endphp
                                <img src="{{ $isExternal ? $userDetails['photo'] : tenant_asset($userDetails['photo']) }}" 
                                     alt="{{ $userDetails['nome'] ?? 'Usuário' }}"
                                     class="h-12 w-12 rounded-full object-cover border-2 border-gray-200">
                            @else
                                <div class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                    <span class="text-lg font-medium text-white uppercase">
                                        @if(isset($userDetails['nome']))
                                            {{ substr($userDetails['nome'], 0, 2) }}
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Detalhes do Usuário</h3>
                            <p class="text-sm text-gray-500">Informações completas do perfil</p>
                        </div>
                    </div>
                    <button wire:click="closeModal" 
                            class="rounded-md text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Conteúdo do Modal --}}
                <div class="mt-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Informações Pessoais --}}
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Informações Pessoais
                            </h4>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Nome:</span>
                                    <span class="text-sm text-gray-900">{{ $userDetails['nome'] ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Email:</span>
                                    <span class="text-sm text-gray-900">{{ $userDetails['email'] ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">WhatsApp:</span>
                                    <span class="text-sm text-gray-900">{{ $userDetails['phone'] ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Papéis:</span>
                                    <span class="flex flex-wrap gap-1">
                                        @if(isset($userDetails['funcoes']) && is_array($userDetails['funcoes']) && count($userDetails['funcoes']))
                                            @foreach($userDetails['funcoes'] as $funcao)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ $funcao }}</span>
                                            @endforeach
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-500">N/A</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Informações de Agendamentos --}}
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Agendamentos
                            </h4>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Total:</span>
                                    <span class="text-sm text-gray-900">{{ $userDetails['agendamentos'] ?? '0' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Último:</span>
                                    <span class="text-sm text-gray-900">{{ $userDetails['ultimo_agendamento'] ?? 'Nunca' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Agendado:</span>
                                    @if($userDetails['tem_agendamento'] ?? false)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Sim
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Não
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Informações do Plano --}}
                        <div class="bg-gray-50 rounded-lg p-4 md:col-span-2">
                            <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Plano de Assinatura
                            </h4>
                            @if(isset($userDetails['plano']) && $userDetails['plano'] !== 'Nenhum plano ativo')
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="text-center">
                                        <span class="text-sm font-medium text-gray-600 block">Plano Atual</span>
                                        <span class="text-sm text-gray-900 mt-1 block">{{ $userDetails['plano'] }}</span>
                                    </div>
                                    <div class="text-center">
                                        <span class="text-sm font-medium text-gray-600 block">Início</span>
                                        <span class="text-sm text-gray-900 mt-1 block">{{ $userDetails['plano_inicio'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="text-center">
                                        <span class="text-sm font-medium text-gray-600 block">Fim</span>
                                        <span class="text-sm text-gray-900 mt-1 block">{{ $userDetails['plano_fim'] ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <span class="text-sm text-gray-500">{{ $userDetails['plano'] ?? 'Nenhum plano ativo' }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Rodapé do Modal --}}
                <div class="mt-6 flex justify-end">
                    <button wire:click="closeModal" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
</div>


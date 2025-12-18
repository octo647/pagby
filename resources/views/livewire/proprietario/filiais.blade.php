<div>
    <div class="flex justify-between items-center mb-4">
        <div>
            <h2 class="text-xl font-semibold">Gerenciar Filiais</h2>
            @if($showForm)
                <p class="text-sm text-gray-600 mt-1 md:hidden">
                    {{ $isEditing ? 'Editando filial' : 'Criando nova filial' }}
                </p>
            @endif
        </div>
        @if(!$showForm)
            <button wire:click="$set('showForm', true)" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Nova Filial
            </button>
        @endif
    </div>
    
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <!-- Formulário para criar/editar filial -->
    @if($showForm)
    <div class="bg-gray-50 p-6 rounded-lg mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">
                {{ $isEditing ? 'Editar Filial' : 'Nova Filial' }}
            </h3>
            <!-- Botão voltar apenas em telas pequenas -->
            <button wire:click="cancelForm" class="md:hidden text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nome da Filial</label>
                <input type="text" wire:model="branch.branch_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('branch.branch_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">CNPJ</label>
                <input type="text" wire:model="branch.cnpj" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('branch.cnpj') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Endereço</label>
                <input type="text" wire:model="branch.address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('branch.address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Complemento</label>
                <input type="text" wire:model="branch.complement" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('branch.complement') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Cidade</label>
                <input type="text" wire:model="branch.city" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('branch.city') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Estado</label>
                <input type="text" wire:model="branch.state" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('branch.state') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Telefone</label>
                <input type="text" wire:model="branch.phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('branch.phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">WhatsApp</label>
                <input type="text" wire:model="branch.whatsapp" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('branch.whatsapp') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" wire:model="branch.email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('branch.email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div class="md:col-span-2">
                <label class="flex items-center">
                    <input type="checkbox" wire:model="branch.require_advance_payment" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Requer pagamento antecipado</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" wire:model.live="branch.require_commission" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500
                    @if($branch['require_commission']) mb-2 checked @endif">
                    <span class="ml-2 text-sm text-gray-700">Definir comissão para funcionários?</span>
                </label>
                @if($branch['require_commission'])
                <label class="flex items-center">                    
                    <span class="ml-2 text-sm text-gray-700">Valor da comissão: &nbsp;</span> <input type="text" wire:model="branch.commission" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" size="3">&nbsp;%
                </label>
                @endif
                @error('branch.require_advance_payment') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
        
        <div class="mt-4 flex space-x-2">
            <button wire:click="save" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                {{ $isEditing ? 'Atualizar' : 'Criar' }}
            </button>
            
            <button wire:click="cancelForm" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Cancelar
            </button>
        </div>
    </div>
    @endif

    <!-- Lista de filiais -->
    
    <!-- Versão Desktop - Tabela (oculta em telas pequenas) -->
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-4 border-b font-medium text-gray-600 text-left">ID</th>
                    <th class="py-3 px-4 border-b font-medium text-gray-600 text-left">Nome</th>
                    <th class="py-3 px-4 border-b font-medium text-gray-600 text-left">CNPJ</th>
                    <th class="py-3 px-4 border-b font-medium text-gray-600 text-left">Cidade</th>
                    <th class="py-3 px-4 border-b font-medium text-gray-600 text-left">Telefone</th>
                    <th class="py-3 px-4 border-b font-medium text-gray-600 text-left">Email</th>
                    <th class="py-3 px-4 border-b font-medium text-gray-600 text-left">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($branches as $branch)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 border-b">{{ $branch->id }}</td>
                        <td class="py-3 px-4 border-b font-medium">{{ $branch->branch_name }}</td>
                        <td class="py-3 px-4 border-b">{{ $branch->cnpj }}</td>
                        <td class="py-3 px-4 border-b">{{ $branch->city }}, {{ $branch->state }}</td>
                        <td class="py-3 px-4 border-b">{{ $branch->phone }}</td>
                        <td class="py-3 px-4 border-b">{{ $branch->email }}</td>
                        <td class="py-3 px-4 border-b">
                            <button wire:click="edit({{ $branch->id }})" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm mr-2">
                               Editar&nbsp; 
                            </button>
                            <button wire:click="delete({{ $branch->id }})" onclick="return confirm('Tem certeza que deseja excluir esta filial?')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">
                                Excluir
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-8 px-4 text-center text-gray-500">
                            Nenhuma filial encontrada. Crie a primeira filial usando o formulário acima.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Versão Mobile - Cards (visível apenas em telas pequenas e quando não estiver editando) -->
    <div class="md:hidden space-y-4 @if($showForm) hidden @endif">
        @forelse($branches as $branch)
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4">
                <!-- Header do Card -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-2">
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">
                            ID: {{ $branch->id }}
                        </span>
                        <h3 class="font-semibold text-gray-900">{{ $branch->branch_name }}</h3>
                    </div>
                </div>
                
                <!-- Informações Principais -->
                <div class="grid grid-cols-1 gap-2 mb-4">
                    @if($branch->cnpj)
                        <div class="flex items-center text-sm">
                            <span class="text-gray-500 w-16 flex-shrink-0">CNPJ:</span>
                            <span class="text-gray-900">{{ $branch->cnpj }}</span>
                        </div>
                    @endif
                    
                    @if($branch->city || $branch->state)
                        <div class="flex items-center text-sm">
                            <span class="text-gray-500 w-16 flex-shrink-0">Local:</span>
                            <span class="text-gray-900">{{ $branch->city }}@if($branch->city && $branch->state), @endif{{ $branch->state }}</span>
                        </div>
                    @endif
                    
                    @if($branch->address)
                        <div class="flex items-start text-sm">
                            <span class="text-gray-500 w-16 flex-shrink-0">Endereço:</span>
                            <span class="text-gray-900">
                                {{ $branch->address }}@if($branch->complement), {{ $branch->complement }}@endif
                            </span>
                        </div>
                    @endif
                </div>
                
                <!-- Contatos -->
                @if($branch->phone || $branch->whatsapp || $branch->email)
                    <div class="border-t pt-3 mb-4">
                        <h4 class="text-xs font-medium text-gray-700 uppercase tracking-wide mb-2">Contatos</h4>
                        <div class="space-y-1">
                            @if($branch->phone)
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-500 w-20 flex-shrink-0">📞 Telefone:</span>
                                    <span class="text-gray-900">{{ $branch->phone }}</span>
                                </div>
                            @endif
                            
                            @if($branch->whatsapp)
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-500 w-20 flex-shrink-0">💬 WhatsApp:</span>
                                    <span class="text-gray-900">{{ $branch->whatsapp }}</span>
                                </div>
                            @endif
                            
                            @if($branch->email)
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-500 w-20 flex-shrink-0">✉️ Email:</span>
                                    <span class="text-gray-900">{{ $branch->email }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                
                <!-- Configurações Especiais -->
                @if($branch->require_advance_payment || $branch->require_commission)
                    <div class="border-t pt-3 mb-4">
                        <h4 class="text-xs font-medium text-gray-700 uppercase tracking-wide mb-2">Configurações</h4>
                        <div class="space-y-1">
                            @if($branch->require_advance_payment)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    💳 Pagamento Antecipado
                                </span>
                            @endif
                            
                            @if($branch->require_commission)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    💰 Comissão: {{ $branch->commission }}%
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
                
                <!-- Ações -->
                <div class="flex space-x-2 pt-3 border-t">
                    <button wire:click="edit({{ $branch->id }})" class="flex-1 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                        ✏️ Editar
                    </button>
                    <button wire:click="delete({{ $branch->id }})" onclick="return confirm('Tem certeza que deseja excluir esta filial?')" class="flex-1 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm">
                        🗑️ Excluir
                    </button>
                </div>
            </div>
        @empty
            <!-- Empty State para Mobile -->
            <div class="text-center py-12">
                <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
                    🏢
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-1">Nenhuma filial encontrada</h3>
                <p class="text-sm text-gray-500 mb-4">Crie a primeira filial usando o formulário acima.</p>
            </div>
        @endforelse
    </div>
</div>
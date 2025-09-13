<div>
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">Gerenciar Filiais</h2>
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
        <h3 class="text-lg font-semibold mb-4">
            {{ $isEditing ? 'Editar Filial' : 'Nova Filial' }}
        </h3>
        
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
                    <input type="checkbox" wire:model.live="branch.require_comission" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Definir comissão para funcionários?</span>
                </label>
                @if($branch['require_comission'])
                <label class="flex items-center">                    
                    <span class="ml-2 text-sm text-gray-700">Valor da comissão: &nbsp;</span> <input type="text" wire:model="branch.comission" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" size="2">&nbsp;%
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
    <div class="overflow-x-auto">
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
                                Editar
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
</div>
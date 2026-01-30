<div>
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <div class="mb-4">
        @if(!$showAddForm && !$showEditForm)
            <button wire:click="toggleAddForm" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Adicionar Novo Contato Booksy
            </button>
        @endif
    </div>

    @if($showAddForm)
        <div class="bg-gray-50 p-6 rounded-lg mb-6">
            <h3 class="text-lg font-semibold mb-4">Novo Contato Booksy</h3>
            <form wire:submit.prevent="addContact">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nome do Proprietário</label>
                        <input type="text" wire:model="newContact.owner_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('newContact.owner_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nome do Salão</label>
                        <input type="text" wire:model="newContact.salon_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('newContact.salon_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo de Salão</label>
                        <select wire:model="newContact.salon_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Selecione...</option>
                            <option value="Barbearia">Barbearia</option>
                            <option value="Salão de Beleza">Salão de Beleza</option>
                            <option value="Outro">Outro</option>
                        </select>
                        @error('newContact.salon_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Número de Funcionários</label>
                        <input type="number" wire:model="newContact.employee_count" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('newContact.employee_count') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Telefone</label>
                        <input type="text" wire:model="newContact.phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('newContact.phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Endereço</label>
                        <input type="text" wire:model="newContact.address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('newContact.address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Anotações</label>
                        <textarea wire:model="newContact.notes" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        @error('newContact.notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="mt-4 flex space-x-2">
                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Salvar
                    </button>
                    <button type="button" wire:click="cancelAddForm" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    @endif

    @if($showEditForm)
        <div class="bg-gray-50 p-6 rounded-lg mb-6">
            <h3 class="text-lg font-semibold mb-4">Editar Contato Booksy</h3>
            <form wire:submit.prevent="updateContact">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nome do Proprietário</label>
                        <input type="text" wire:model="editingContact.owner_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('editingContact.owner_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nome do Salão</label>
                        <input type="text" wire:model="editingContact.salon_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('editingContact.salon_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo de Salão</label>
                        <select wire:model="editingContact.salon_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Selecione...</option>
                            <option value="Barbearia">Barbearia</option>
                            <option value="Salão de Beleza">Salão de Beleza</option>
                            <option value="Outro">Outro</option>
                        </select>
                        @error('editingContact.salon_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Número de Funcionários</label>
                        <input type="number" wire:model="editingContact.employee_count" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('editingContact.employee_count') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Telefone</label>
                        <input type="text" wire:model="editingContact.phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('editingContact.phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Endereço</label>
                        <input type="text" wire:model="editingContact.address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('editingContact.address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Anotações</label>
                        <textarea wire:model="editingContact.notes" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        @error('editingContact.notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="mt-4 flex space-x-2">
                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Atualizar
                    </button>
                    <button type="button" wire:click="cancelEditForm" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="w-full">
        {{-- Mobile: Cards --}}
        <div class="block md:hidden">
            <div class="space-y-4">
                @foreach ($contacts as $contact)
                    <div class="bg-white rounded-lg shadow p-4 flex flex-col gap-2">
                        <div class="font-bold text-lg">{{ $contact['owner_name'] ?? $contact->owner_name }}</div>
                        <div><span class="font-semibold">Salão:</span> {{ $contact['salon_name'] ?? $contact->salon_name }}</div>
                        <div><span class="font-semibold">Tipo:</span> {{ $contact['salon_type'] ?? $contact->salon_type }}</div>
                        <div><span class="font-semibold">Telefone:</span> {{ $contact['phone'] ?? $contact->phone }}</div>
                        <div><span class="font-semibold">Funcionários:</span> {{ $contact['employee_count'] ?? $contact->employee_count }}</div>
                        <div><span class="font-semibold">Endereço:</span> {{ $contact['address'] ?? $contact->address }}</div>
                        <div><span class="font-semibold">Notas:</span> {{ $contact['notes'] ?? $contact->notes }}</div>
                        <div class="flex gap-2 mt-2">
                            <button wire:click="editContact({{ $loop->index }})" class="bg-blue-500 text-white px-3 py-1 rounded w-1/2">Editar</button>
                            <button wire:click="deleteContact({{ $loop->index }})" class="bg-red-500 text-white px-3 py-1 rounded w-1/2">Apagar</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        {{-- Desktop: Table --}}
        <div class="hidden md:block">
            <div class="overflow-x-auto w-full shadow-md rounded-lg">
                <table class="min-w-full text-xs sm:text-sm text-left text-gray-500 whitespace-nowrap">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="py-3 px-4 text-left">Proprietário</th>
                            <th class="py-3 px-4 text-left">Salão</th>
                            <th class="py-3 px-4 text-left">Tipo</th>
                            <th class="py-3 px-4 text-left hidden md:table-cell">Funcionários</th>
                            <th class="py-3 px-4 text-left hidden sm:table-cell">Telefone</th>                            
                            <th class="py-3 px-4 text-left hidden md:table-cell">Endereço</th>
                            <th class="py-3 px-4 text-left hidden md:table-cell">Anotações</th>
                            <th class="py-3 px-4 text-left">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contacts as $index=>$contact)
                        <tr wire:key="{{$index}}" class="bg-white border-b hover:bg-gray-50">
                            <td class="py-3 px-4">
                                {!! nl2br(e(str_replace('/', "\n", $contact['owner_name'] ?? ''))) !!}
                            </td>
                            <td class="py-3 px-4">{{$contact['salon_name']}}</td>
                            <td class="py-3 px-4">{{$contact['salon_type']}}</td>
                            <td class="py-3 px-4 hidden md:table-cell">{{$contact['employee_count']}}</td>

                            <td class="py-3 px-4 hidden sm:table-cell">{{$contact['phone']}}</td>
                            <td class="py-3 px-4 hidden md:table-cell" style="max-width: 200px; word-break: break-word; white-space: pre-line;">
                                {{ Str::wordWrap($contact['address'] ?? '', 20, "\n", true) }}
                            </td>
                            <td class="py-3 px-4 hidden md:table-cell" style="max-width: 200px; word-break: break-word; white-space: pre-line;">
                                {{ Str::wordWrap($contact['notes'] ?? '', 20, "\n", true) }}
                            </td>
                            <td class="py-3 px-4 flex flex-col sm:flex-row gap-2">
                                <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs sm:text-sm sm:w-24 w-full" wire:click="editContact({{$index}})">
                                    Editar
                                </button>
                                <button type="button" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs sm:text-sm sm:w-24 w-full" wire:click="deleteContact({{$index}})">
                                    Apagar
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

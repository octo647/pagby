<div>
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <div class="mb-4">
        @if(!$showAddForm && !$showEditForm)
            <button wire:click="toggleAddForm" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Adicionar Novo Contato
            </button>
        @endif
    </div>

    @if($showAddForm)
        <div class="bg-gray-50 p-6 rounded-lg mb-6">
            <h3 class="text-lg font-semibold mb-4">Novo Contato</h3>
            <form wire:submit.prevent="addContact">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nome do Proprietário</label>
                        <input type="text" wire:model="newContact.owner_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('newContact.owner_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" wire:model="newContact.email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('newContact.email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Telefone</label>
                        <input type="text" wire:model="newContact.phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('newContact.phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo</label>
                        <select wire:model="newContact.tipo" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Selecione...</option>
                            <option value="Barbearia">Barbearia</option>
                            <option value="Salão de Beleza">Salão de Beleza</option>
                            <option value="Outro">Outro</option>
                        </select>
                        @error('newContact.tipo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nome do Salão</label>
                        <input type="text" wire:model="newContact.tenant_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('newContact.tenant_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Endereço</label>
                        <input type="text" wire:model="newContact.address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('newContact.address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Número</label>
                        <input type="text" wire:model="newContact.number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('newContact.number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Bairro</label>
                        <input type="text" wire:model="newContact.neighborhood" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('newContact.neighborhood') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cidade</label>
                        <input type="text" wire:model="newContact.city" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('newContact.city') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Estado</label>
                        <input type="text" wire:model="newContact.state" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('newContact.state') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Notas</label>
                        <textarea wire:model="newContact.notas" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        @error('newContact.notas') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
            <h3 class="text-lg font-semibold mb-4">Editar Contato</h3>
            <form wire:submit.prevent="updateContact">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nome do Proprietário</label>
                        <input type="text" wire:model="editingContact.owner_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('editingContact.owner_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" wire:model="editingContact.email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('editingContact.email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Telefone</label>
                        <input type="text" wire:model="editingContact.phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('editingContact.phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo</label>
                        <select wire:model="editingContact.tipo" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Selecione...</option>
                            <option value="Barbearia">Barbearia</option>
                            <option value="Salão de Beleza">Salão de Beleza</option>
                            <option value="Outro">Outro</option>
                        </select>
                        @error('editingContact.tipo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nome do Salão</label>
                        <input type="text" wire:model="editingContact.tenant_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('editingContact.tenant_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Endereço</label>
                        <input type="text" wire:model="editingContact.address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('editingContact.address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Número</label>
                        <input type="text" wire:model="editingContact.number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('editingContact.number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Bairro</label>
                        <input type="text" wire:model="editingContact.neighborhood" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('editingContact.neighborhood') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cidade</label>
                        <input type="text" wire:model="editingContact.city" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('editingContact.city') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Estado</label>
                        <input type="text" wire:model="editingContact.state" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('editingContact.state') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                   <div>
                        <label class="block text-sm font-medium text-gray-700">Notas</label>
                        <textarea wire:model="editingContact.notas" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        @error('editingContact.notas') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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

    <div class="relative overflow-x-auto shadow-md rounded-lg">
     
    <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th class="py-3 px-4 text-left">Nome</th>
               <!-- <th class="py-3 px-4 text-left">Email</th>
                <th class="py-3 px-4 text-left">Telefone</th> -->
                <th class="py-3 px-4 text-left">Tipo</th>
                <th class="py-3 px-4 text-left">Salão</th>
                <th class="py-3 px-4 text-left">Cidade</th>
                <th class="py-3 px-4 text-left">Plano</th>
                <th class="py-3 px-4 text-left">Último Pagamento</th>
                <th class="py-3 px-4 text-left">Data Vencimento</th>
                <th class="py-3 px-4 text-left">Núm. Funcionários</th>
                <th class="py-3 px-4 text-left">Valor</th>
                <th class="py-3 px-4 text-left">Status Pgto</th>
                <th class="py-3 px-4 text-left">Ações</th>
            </tr>
        </thead>
        
        <tbody>
        @foreach($contacts as $index=>$contact)
       
        <tr wire:key="{{$index}}" class="bg-white border-b hover:bg-gray-50">
            <td class="py-3 px-4">{{$contact['owner_name'] ?? $contact['name'] ?? ''}}</td>
           <!-- <td class="py-3 px-4">{{$contact['email']}}</td>
            <td class="py-3 px-4">{{$contact['phone']}}</td> -->
            <td class="py-3 px-4">{{$contact['tipo']}}</td>
            <td class="py-3 px-4">{{$contact['tenant_name']}}</td>
            <td class="py-3 px-4">{{$contact['city']."/".$contact['state']}}</td>
            <td class="py-3 px-4">{{$contact['last_payment_plan'] ?? 'N/A'}}</td>
            <td class="py-3 px-4">{{$contact['last_payment_date'] ?? 'N/A'}}</td>
            <td class="py-3 px-4">{{$contact['due_date'] ?? 'N/A'}}</td>
            <td class="py-3 px-4">{{$contact['last_payment_employee_count'] ?? 'N/A'}}</td>
            <td class="py-3 px-4">{{$contact['last_payment_amount'] ?? 'N/A'}}</td>

            <td class="py-3 px-4">
                @if($contact['has_paid'])
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Pago
                    </span>
                @elseif($contact['payment_count'] > 0)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800" title="Status: {{$contact['last_payment_status'] ?? 'desconhecido'}}">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        Pendente
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        Não pago
                    </span>
                @endif
            </td>
            <td class="py-3 px-4">
                <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm mr-2" wire:click="editContact({{$index}})">
                    Editar
                </button>
                <button type="button" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm" wire:click="deleteContact({{$index}})">
                    Apagar
                </button>
            </td>
        </tr>
        @endforeach

    </table>
</div>
</div>

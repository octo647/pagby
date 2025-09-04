<div>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <br>
         @if(session()->has('message'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
            {{ session('message') }}
        </div>
        @endif

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

    
   
        <table class="tabela-escura w-full text-sm text-left text-gray-500  rounded-lg overflow-hidden">
          
            <thead>
            <tr>
            <th>Nome</th>            
            <th>função</th>
            <th>status</th>
            <th>ações</th>
            </tr>
            </thead>
            <tbody>
            {{-- Verifica se há usuários --}}

            @foreach($salon_users as $user)
            <tr>
                <td>
                <a href="#" wire:click.prevent="showUserDetails({{ $user->id }})" class="hover:underline">
                 {{ $user->name }}
                </a>
                </td>
                
                <td class="">
                    {{-- Condição para verificar se o usuário está sendo editado --}}
                    @if($editingUserId === $user->id)                        
                        <select  class="rounded-lg dark:bg-gray-400 dark:text-gray-800" id="funcao" wire:model='editingRole'>
                            @foreach($roles as $role)
                                <option value="{{ $role }}">{{ $role }}</option>
                            @endforeach
                        </select>
                    @else
                        {{ $user->roles->first()->role ?? '' }}
                    @endif
                </td>
                <td class="">
                    {{-- Condição para verificar se o usuário está sendo editado --}}
                    @if($editingUserId === $user->id)
                        <select wire:model="editingStatus" class="">
                            <option value="Ativo">Ativo</option>
                            <option value="Inativo">Inativo</option>
                        </select>
                    @else
                    <div class="flex items-center">
                        @if($user->status === 'Ativo')
                            <div class="h-2.5 w-2.5 rounded-full bg-green-500 me-2"></div> {{ $user->status }} 
                        @else
                            <div class="h-2.5 w-2.5 rounded-full bg-red-500 me-2"></div> {{ $user->status }} 
                        @endif
                    </div>
                    @endif
                                     
                </td>

                <td>
                    @if($editingUserId === $user->id)
                        <button wire:click="saveUser" class="text-green-600">Salvar</button>
                        <button wire:click="cancelEdit" class="text-gray-600">Cancelar</button>
                    @else
                        <button wire:click="editUser({{ $user->id }})" class="text-blue-600">Editar</button>
                    @endif
                </td>
            </tr>

            @endforeach
            </tbody>

           

        </table>
    </div>
<div class="mt-4">               
  {{ $salon_users->links() }}
</div>
@if($showModal)
<div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-lg">
        <h2 class="text-xl font-bold mb-4">Detalhes do Usuário</h2>
        <ul class="mb-4">
            <li><strong>Nome:</strong> {{ $userDetails['nome'] }}</li>
            <li><strong>Email:</strong> {{ $userDetails['email'] }}</li>
            <li><strong>WhatsApp:</strong> {{ $userDetails['phone'] }}</li>
            <li><strong>Função:</strong> {{ $userDetails['funcao'] }}</li>
            <li><strong>Agendamentos:</strong> {{ $userDetails['agendamentos'] }}</li>
            <li><strong>Último Agendamento:</strong> {{ $userDetails['ultimo_agendamento'] }}</li>
            <li><strong>Tem agendamento marcado?</strong> {{ $userDetails['tem_agendamento'] }}</li>
            <li><strong>Plano:</strong> {{ $userDetails['plano'] }}</li>
            <li><strong>Início do Plano:</strong> {{ $userDetails['plano_inicio'] }}</li>
            <li><strong>Fim do Plano:</strong> {{ $userDetails['plano_fim'] }}</li>
        </ul>
        <button wire:click="closeModal" class="bg-pink-600 text-white px-4 py-2 rounded">Fechar</button>
    </div>
</div>
@endif

</div>


<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}
    <div class="relative overflow-x-auto shadow-md rounded-lg">
        <br>
        

        <table class="tabela-escura w-full text-left text-sm text-gray-500 rounded-lg overflow-hidden">
        
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr><th>Nome</th><th>Email</th><th>Filial</th><th></th></tr>
            </thead>

            @foreach($branchUsers as $index=>$user)
            <tr wire:key="{{$index}}" >
                <td class="flex items-center gap-2">
                    @if(isset($user['user_photo']))
                        <img src="{{ tenant_asset($user['user_photo']) }}" class="w-8 h-8 rounded-full object-cover" alt="Foto do funcionário">
                    @endif
                    {{$user['user_name']}}
                </td>
                <td>
                    {{$user['user_email']}}
                </td>
                <td>
                    @if($editedUserIndex !== $index)
                    {{$user['branch_name'] ?? 'Nenhuma filial atribuída'}}
                    @else
                    <select class="bg-blue-700 rounded-lg text-gray-200" id="filial" wire:model='branchUsers.{{$index}}.branch_id'>
                        <option value="">Escolha uma filial</option>
                        @foreach($branches as $branch)
                        <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                        @endforeach
                    </select>
                    <div class="mt-2 flex items-center gap-2">
        @if(isset($user['photo']))
            <img src="{{ asset('storage/' . $user['photo']) }}" class="w-12 h-12 rounded-full object-cover" alt="Foto do funcionário">
        @endif
        <input type="file" wire:model="branchUsers.{{$index}}.photo" class="block text-sm text-gray-500" accept="image/*">
    </div>
                    
                    @endif
                </td>
                <td>
                    @if($editedUserIndex !== $index)
                    <a href="#" wire:click="editUser({{$index}})">
                    Editar Filial
                    </a>
                    
                    @else
                     <a href="#" wire:click="updateBranchUser({{ $user['user_id'] }},  'null' )">
                    Salvar
                    </a>
                    
                    @endif
                </td>
            </tr>
            @endforeach

        </table>
        <div class="mt-4">
            @if(session()->has('message'))
                <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                    {{ session('message') }}
                </div>
            @endif
            @if(session()->has('error'))
                <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif  
            </div>
        
</div>

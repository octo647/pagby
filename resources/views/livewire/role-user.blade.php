<div>
    <div class="relative overflow-x-auto shadow-md rounded-lg">
        
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr><th>Nome</th><th>email</th><th>salão</th><th>função</th><th></th></tr>
            </thead>
           
            @foreach($usuarios as $index=>$user)                   
            <tr wire:key="{{$index}}" class="bg-white border-b hover:bg-gray-50">
                <td>                
                    
                    {{$user['nome']}}
                   
                </td>
                <td>                
                    
                    {{$user['email']}}
                   
                </td>
                <td>
                  
                    {{$user['salao']}}
                 
                </td>
                <td>
                    @if($editedUserIndex !== $index)
                    {{$user['funcao']}}
                    @else
                    
                    <select id="funcao" wire:model.defer='usuarios.{{$index}}.funcao'>
                        <option disabled selected>Escolha uma opção</option>
                        <option value="Admin">Admin</option>
                        <option value="Proprietário">Proprietário</option>
                        <option value="Funcionário">Funcionário</option>
                        <option value="Cliente">Cliente</option>
                    </select>
                     
                    @endif
                </td>
                
                <td>
                    @if($editedUserIndex !== $index)
                    <button type="button" class="btn btn-primary" wire:click.prevent="editUser({{$index}})">
                        Editar
                    </button>
                    <button type="button" class="btn btn-primary" wire:click.prevent="deleteUser({{$index}})">
                        Apagar
                    </button>
                    @else
                    <button  wire:click.prevent="updateRole({{$index}})">
                        Save
                    </button>               
                    @endif
                </td>
                <tr>
                    
            @endforeach
           
    
        </table>
    </div>
</div>

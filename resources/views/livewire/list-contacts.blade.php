<div>
    <div class="relative overflow-x-auto shadow-md rounded-lg">
        
    <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
        <tr><th>Nome</th><th>email</th><th>Telefone</th><th>status</th><th>Cidade</th>
            </tr>
        </thead>
        
        @foreach($contacts as $index=>$contact)
        <tr wire:key="{{$index}}" class="bg-white border-b hover:bg-gray-50">
            <td>                
                @if($editedContactIndex !== $index)
                {{$contact['name']}}
                @else
                <input type='text' wire:model.defer='contacts.{{$index}}.name'>
                @endif
            </td>
            <td>
                @if($editedContactIndex !== $index)
                {{$contact['email']}}
                @else
                <input type='text' wire:model.defer='contacts.{{$index}}.email'>
                @endif
            </td>
            <td>
                @if($editedContactIndex !== $index)
                {{$contact['phone']}}
                @else
                <input type='text' wire:model.defer='contacts.{{$index}}.phone'>
                @endif
            </td>
            <td>
                @if($editedContactIndex !== $index)
                {{$contact['status']}}
                @else
                <input type='text' wire:model.defer='contacts.{{$index}}.status'>
                @endif
            </td>
            <td>
                
                {{$contact['city']."/".$contact['state']}} 
               
            </td>
            <td>
                @if($editedContactIndex !== $index)
                <button type="button" class="btn btn-primary" wire:click.prevent="editContact({{$index}})">
                    Editar
                </button>
                <button type="button" class="btn btn-primary" wire:click.prevent="deleteContact({{$index}})">
                    Apagar
                </button>
                @else
                <button  wire:click.prevent="saveContact({{$index}})">
                    Save
                </button>               
                @endif
            </td>
            <tr>
        @endforeach

    </table>
</div>
</div>

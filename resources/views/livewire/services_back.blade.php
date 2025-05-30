<div>
    <div class="relative overflow-x-auto shadow-md rounded-lg">
        <br>
        
        <table class="table-auto w-full text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr><th>Salão</th><th>Serviço</th><th>Preço</th><th>Tempo</th><th>
                
            </th></tr>
            </thead>
           
            @foreach($salon_serv as $index=>$service)                   
            <tr wire:key="{{$index}}" class="bg-white border-b hover:bg-gray-50">
                <td>                
                    @if($editedServiceIndex !== $index)
                    {{$service['salon']}}           
                    @else                    
                    <select wire:model.defer='salon_serv.{{$index}}.salon_id'>
                        <option disabled selected>Escolha uma opção</option>
                        @foreach($salons as $ind=>$salon)                        
                        <option value="{{$salon['id']}}">{{$salon['salon']}}</option>
                        @endforeach
                    </select>
                    @endif           
                </td>
                <td>                
                    @if($editedServiceIndex !== $index)
                    {{$service['service']}}
                    @else                    
                    <input type='text'  wire:model.defer='salon_serv.{{$index}}.service'>
                    @endif    
                </td>
                
                <td >
                    @if($editedServiceIndex !== $index)
                    {{$service['price']}}
                    @else
                    <input type='text'  wire:model.defer='salon_serv.{{$index}}.price'>
                    @endif
                </td>
                <td>
                    @if($editedServiceIndex !== $index)
                    {{$service['time']}}
                    @else
                    <input type='text'  wire:model.defer='salon_serv.{{$index}}.time'>
                    @endif

                </td>
                
                <td>
                    @if($editedServiceIndex !== $index)
                    <button type="button" class="btn btn-primary" wire:click.prevent="editService({{$index}})">
                        Editar
                    </button>
                    <button type="button" class="btn btn-primary" wire:click.prevent="deleteService({{$index}})">
                        Apagar
                    </button>
                    @else
                    <button  wire:click.prevent="updateService({{$index}})">
                        Save
                    </button>               
                    @endif
                </td>
                <tr>
                    
            @endforeach
            
            <tr><th>
                <button type="button" class="btn btn-primary" wire:click.prevent="addService({{$index+1}})">
                    Adicionar serviço
                </button>   

            </th></tr>
           
            
        </table>
    </div>
</div>

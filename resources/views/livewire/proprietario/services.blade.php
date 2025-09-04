<div>
    <div class="relative overflow-x-auto shadow-md rounded-lg">
        <br>
        
        <table class="tabela-escura w-full text-sm text-left text-gray-500  rounded-lg overflow-hidden">
        
            <thead class="">
            <tr>
            <th>Foto</th><th>Serviço</th><th>Preço</th><th>Tempo</th><th>
                
            </th></tr>
            </thead>
            <tbody>
            
            @foreach($salon_serv as $index=>$service)                   
            <tr wire:key="{{$service['id']}}" class="">
                <td >
                   @if(isset($service['photo']) && $service['photo'] instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                        <img src="{{ $service['photo']->temporaryUrl() }}" class="w-16 h-16 object-cover rounded" />
                    @elseif(!empty($service['photo']))
                        <img src="{{ asset('/services/' . $service['photo']) }}" alt="Foto do serviço" class="w-16 h-16" />
                    @endif

                    @if($editedServiceIndex === $index)
                        <input type="file" wire:model="salon_serv.{{$index}}.photo" class="w-16 h-16" placeholder="Foto do serviço">
                    @endif
                </td>
                
                
                <td>                
                    @if($editedServiceIndex !== $index)
                    {{$service['service']}}
                    @else                    
                    <input class="" type='text'  wire:model.defer='salon_serv.{{$index}}.service'>
                    @endif    
                </td>
                
                <td >
                    @if($editedServiceIndex !== $index)
                    {{$service['price']}}
                    @else
                    <input class="w-20 h-10 " type='text'  wire:model.defer='salon_serv.{{$index}}.price'>
                    @endif
                </td>
                <td>
                    
                    @if($editedServiceIndex !== $index)
                    {{$service['time']}}
                    @else
                    <input class="w-20 h-10" type='text'  wire:model.defer='salon_serv.{{$index}}.time'>
                    @endif

                </td>
                
                <td>
                    @if($editedServiceIndex !== $index)
                    <a href="#" wire:click.prevent="editService({{$index}})">
                        Editar
                    </a>
                    <a href="#" wire:click.prevent="deleteService({{$index}})">
                        Apagar
                    </a>
                    @else
                       
                    <a href="#" wire:click.prevent="updateService({{$index}})">
                        Salvar
                    </a>               
                    @endif
                </td>
            </tr>
                    
            @endforeach
            
            <tr><th></th><th></th><th></th><th></th><th>
                <button type="button" class="btn btn-primary" wire:click.prevent="addService({{$index+1}})">
                    Adicionar serviço
                </button>   

            </th></tr>
            </tbody>
           
            
        </table>
    </div>
</div>

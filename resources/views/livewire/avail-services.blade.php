<div>
    {{-- The best athlete wants his opponent at his best. --}}
    <h1>Escolha um ou mais serviços:</h1>
    <div class="relative overflow-x-auto shadow-md rounded-lg">
        <br>
        
        <table class="table-auto w-full text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr><th>Serviço</th><th>Preço</th><th>Tempo(minutos)</th><th>
                
            </th></tr>
            </thead>
            <form wire:submit="selectedServices">
            @csrf
            @foreach($salon_serv as $index=>$service)                   
            <tr wire:key="{{$index}}" class="bg-white border-b hover:bg-gray-50">
                
                <td>                
                    {{$service['service']}}
                </td>
                
                <td >
                    {{$service['price']}}
                </td>
                <td>
                    {{$service['time']}}
                </td>
                
                <td>
                    <input type='checkbox' name = "{{$index}}" wire:model.defer="selected_serv" value="{{$index}}">
                </td>
            </tr>
            @endforeach
       
            <tr>
                <th></th><th></th><th></th><th>
                    <button type="submit" class="btn btn-primary">
                        Prosseguir
                    </button> 
                </th>
            </tr>
            </form>     

                    
            
            
            
           
            
        </table>
        
    </div>
</div>

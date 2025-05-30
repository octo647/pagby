<div>
    <div class="relative overflow-x-auto shadow-md rounded-lg">
        <br>
        
        <table class="table-auto w-full text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr><th>Serviços oferecidos</th>
            @foreach ($employees as $index=>$employee)
            <th>{{$employee['name']}}</th>
            @endforeach
            </tr>
            </thead>
           
            @foreach($services as $service)                   
            <tr>            
                <td>                
                    {{$service['service']}}
                </td>
                @foreach ($employees as $ind=>$employee)
                <td>
                    <input 
                    @foreach($employee['services'] as $ind2=>$service_id)
                        @if($service_id == $service['id'])                         
                        {{"placeholder = Algo"}} 
                        wire:click="changeService({{$service_id}}, {{$employee['id']}})"          
                        @endif                       
                    @endforeach
                    @foreach($employee['services'] as $ind2=>$service_id)
                        @if($service_id != $service['id'])                         
                        {{"placeholder = Não"}} 
                        wire:click="changeService({{$service['id']}}, {{$employee['id']}})"          
                        @endif                       
                    @endforeach
                    class='bg-red-200' type='text'>                    
                </td>             
                    @endforeach
            </tr>
            @endforeach
            
            
        </table>
    </div>
</div>


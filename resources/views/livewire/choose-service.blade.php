<div>
    <div>Escolha o serviço</div>
    @if(isset($services))
    
     <form wire:submit="chosen" wire:submit="chosen_services" multiple>
    @foreach($services as $item=>$service)  
    <div class="flex items-center">
        
    <input   wire:model="chosen_services" id="checkbox" class="w-4 h-4 text-blue-600 rounded" 
    type="checkbox" name="service" value="{{$service['id']}}"> 
    
     <label for="checkbox" class="ms-2 text-sm font-medium">
        {{$service['service']}} -- {{$service['price']}}
    </label>
    </div>
    @endforeach
    <button type="submit" class="text-white bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:ring-red-300 font-medium
    rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center mr-2">Prossiga</button>
</form>
   
    @endif
   


        
    


 </div>
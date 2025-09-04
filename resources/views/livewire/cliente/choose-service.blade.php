<div>
    <div>Escolha o serviço</div>
    @if(isset($services))
    
     <form wire:submit="chosen">
    @foreach($services as $item=>$service)  
    <div class="flex items-center p-1 mb-1 bg-gray-100 rounded">
        
    <input   wire:model="chosen_services" id="checkbox-{{ $service['id'] }}" class="w-5 h-5 text-blue-600 rounded" 
    type="checkbox" name="service" value="{{$service['id']}}"> 
    
     <label for="checkbox" class="ms-2 text-sm font-medium">
        {{$service['service']}} -- R$ {{$service['price']}}
    </label>
    </div>
    @endforeach
    @error('semservico')
        <div class="alerta-aviso mb-4">
            {{ $message }}
        </div>
    @enderror
    <button type="submit" class="text-white bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:ring-red-300 font-medium
    rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center mr-2">Prossiga</button>
    <button type="button" onclick="" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium
    rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center">Cancelar</button>
    <button type="button" wire:click="back" class="text-white bg-gray-600 hover:bg-gray-800 focus:ring-4 focus:ring-red-300 font-medium
    rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center">Voltar</button>
    <br>
</form>
   
    @endif
   


        
    


 </div>
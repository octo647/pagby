
<div>
    <div class="card bg-white border-2 p-4 w-full max-w-2xl mx-auto">
    @if(count($funcionarios) == 0)
       <div class="text-gray-500">Escolha uma filial para ver seus profissionais.</div>
    @else
    <h1 class="text-xl text-center font-bold mb-4">Escolha o Profissional</h1>
         <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($funcionarios as $index => $funcionario)
            
                <div wire:key="{{$index}}" class="flex flex-col items-center p-4 bg-white rounded shadow hover:shadow-lg transition-shadow duration-200">
                    <img wire:click="selectProfessional('{{$funcionario->id}}')" 
                         class="w-14 h-14 rounded-full object-cover mb-2 cursor-pointer" 
                         src="{{tenant_asset($funcionario->photo)}}" 
                         alt="Foto {{$funcionario->name}}">
                    <p class="font-bold ">{{$funcionario->name}}</p>
                    <button wire:click="selectProfessional('{{$funcionario->id}}')" 
                            class="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors duration-200">
                        Selecionar
                    </button>
                </div>
            @endforeach
        </div>
    @endif
    </div>

<div class="card bg-white border-2 p-4 w-full max-w-2xl mx-auto">

        
 
<div id="professionals" class="card bg-white">
    @if(!is_null($choosedEmployee))

      @php
      $user = \App\Models\User::find($choosedEmployee);
      $name = $user->name; 
      $services = $user->services;   
      @endphp
      <div class="flex flex-col sm:flex-row items-center gap-4">
      <div class="text-xl font-bold mb-4">Profissional: {{$name}}</div>
    
        <img class="w-24 h-24 rounded-full object-cover border" src="{{tenant_asset($user->photo)}}" alt="Foto de {{$name}}">
        
        <div class="text-blue-600 text-xl" id="escolha">
            Escolha o serviço   
            @foreach($user->services as $service)  
            <div class="flex items-center p-1 mb-1 bg-gray-200 rounded">        <input wire:model="chosen_services" id="checkbox-{{ $service->id }}" class="w-5 h-5 text-blue-600 rounded" type="checkbox" name="service" value="{{$service->id}}">             
            <label for="checkbox" class="ms-2 text-sm font-medium">
                {{$service->service}} -- R$ {{$service->price}}
            </label>
        </div>
        
            @endforeach
        
        @error('semservico')
            <div class="alerta-aviso mb-4">
                {{ $message }}
            </div>
        @enderror
    
            
        </div>
      </div>   
      
      <div class="flex justify-between mt-4">
        <button type="button" wire:click="apague()" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium
        rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center">Cancelar</button>
        <button type="button" wire:click="chosenService()" class="text-white bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:ring-red-300 font-medium
        rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center">Prosseguir</button>
        </div>

      @endif
      </div>
    </div>
       


        



     


   
    
     
   





</div>

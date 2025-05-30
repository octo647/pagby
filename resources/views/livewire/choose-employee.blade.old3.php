<div 
class="card bg-white">

 
    <div id="professionals" class="">
      @if(isset($choosedEmployee))
      Funcionário escolhido: {{$choosedEmployee}}
      <img class="card-img-top" src="images/{{$choosedEmployee}}.jpeg" 
      alt="">
      
      @else
      <div class="text-blue-600 text-xl" id="escolha">
        Escolha o profissional
      </div>   
        @foreach($employees as $index=>$employee)     
        
        <div wire:key="{{$index}}" id="professional-{{$index}}" class="flex flex-row p-6 text-sm card professional" style="width: 20rem;">
    <div class="basis-1/2">
        <img onclick="selectProfessional('{{$index}}')" 
             class="card-img-top" 
             src="images/{{$employee['employee_id']}}.jpeg" 
             alt="Foto {{$employee['employee_name']}}">
        <p class="font-bold card-title">{{$employee['employee_name']}}</p>
        <div class="hidden" id="professional_{{$index}}">
            <a wire:click="showServices({{$employee['employee_id']}})" href="#"  
               class="text-white bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:ring-red-300 font-medium
                      rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center mr-2">
                Prossiga
            </a>
        </div>  
    </div>

        <!-- Opções de serviço -->
    <div id="{{$index}}" class="hidden basis-1/2">
        <img class="card-img-top size-1/3" src="images/{{$employee['employee_id']}}.jpeg" alt="Foto {{$employee['employee_name']}}">
        <div class="text-sm text-black">Escolha um ou mais serviços abaixo:</div>
        <div class="mb-4 flex-col">
            {{-- Exemplo de serviços --}}
          {{--  @foreach($employee['service'] as $service)
                <div class="p-1 text-sm text-black">
                    <input id="service" wire:model.defer="chosen_services"
                           value="{{$service['service'].',  R$ '. $service['service_price']}}"
                           type="checkbox" class="text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500
                           dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"/>
                    <label for="service" class="p-2ms-2 dark:text-gray-300">
                        {{$service['service']}},
                        <span>
                            R$ {{$service['service_price']}}
                        </span>
                    </label>
                </div>
            @endforeach --}}
        </div>
        <a href="#" wire:click="chooseEmployee('{{$index}}')"
           class="text-white bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:ring-red-300 font-medium
                  rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center mr-2">
            Prosseguir
        </a> 
        <a href="#" wire:click="apague()" 
           class="text-gray-900 bg-white hover:bg-gray-100 focus:ring-4 focus:ring-cyan-200 border
                  border-gray-200 font-medium inline-flex items-center rounded-lg text-base px-3 py-2.5 text-center"
           data-modal-toggle="delete-user-modal">
            Cancelar
        </a>
    </div>
</div>
@endforeach
@endif

         <div id="modalConfirm" class="fixed inset-0 z-50 hidden w-full h-full px-4 overflow-y-auto bg-gray-900 modal bg-opacity-60">
             <div class="relative max-w-md mx-auto bg-white rounded-md shadow-xl top-40">
                 <div class="flex justify-end p-2">
                     <button onclick="closeModal('modalConfirm')" type="button"
                         class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                         <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                             <path fill-rule="evenodd"
                                 d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                 clip-rule="evenodd"></path>
                         </svg>
                     </button>
                 </div>
                 <div class="p-2 pt-0 text-center">
                    <div id="services" class="mt-5 mb-6 text-xl font-normal text-gray-500 text-start">
                        //esta parte é ocupada pelos serviços de cada funcionário

                    </div>



                 </div>

             </div>
         </div>



     <script type="text/javascript">
         window.openModal = function(modalId, id) {


             document.getElementById('services').innerHTML = document.getElementById(id).innerHTML
             document.getElementById(modalId).style.display = 'block'
             document.getElementsByTagName('body')[0].classList.add('overflow-y-hidden')
         }

         window.abraModal = function(id) {

             document.getElementById(id).style.display = 'block'
             document.getElementsByTagName('body')[0].classList.add('overflow-y-hidden')
         }
    window.selectProfessional = function(id) {
    var professionals = document.getElementsByClassName('professional');
    for (var i = 0; i < professionals.length; i++) {
        professionals[i].style.display = 'none';
    }
    professionals[id].style.display = 'block';
    document.getElementById('escolha').innerHTML = "Profissional escolhido:";
    document.getElementById('professional_' + id).style.display = 'block';
    @this.call('showServices', "{{ $employee['employee_id'] }}"); // Chama o método Livewire diretamente

    // Exibe as opções de serviço imediatamente
    document.getElementById(id).style.display = 'block';
};
        window.closeModal = function(modalId) {
             document.getElementById(modalId).style.display = 'none'
             document.getElementsByTagName('body')[0].classList.remove('overflow-y-hidden')
         }
         window.confirmService = function(modalId) {
             document.getElementById(modalId).style.display = 'none'
             document.getElementsByTagName('body')[0].classList.remove('overflow-y-hidden')
         }

         // Close all modals when press ESC
         document.onkeydown = function(event) {
             event = event || window.event;
             if (event.keyCode === 27) {
                 document.getElementsByTagName('body')[0].classList.remove('overflow-y-hidden')
                 let modals = document.getElementsByClassName('modal');
                 Array.prototype.slice.call(modals).forEach(i => {
                     i.style.display = 'none'
                 })
             }
         };
     </script>
    </div>

   
    {{-- <div id="confirmation" class="">

        @if($choosedEmployee !== null && empty($chosen_services))
            <div class="p-1">
            {{$employees[$choosedEmployee]['employee_name']}}
            </div>
            <div class="p-1">
            Você deve selecionar pelo menos um serviço
            </div>

        @endif

        @if($choosedEmployee !== null && !empty($chosen_services) && count($chosen_services)===1)
             <div class="p-1">
                 {{$employees[$choosedEmployee]['employee_name']}}
             </div>
             
            <div class="p-1">
            Serviço selecionado:
                @foreach($chosen_services as $item=>$service)
                    {{$service}}
                @endforeach
            </div>
        @endif
         @if($choosedEmployee !== null && !empty($chosen_services) && count($chosen_services)>1)
             <div class="p-1">
                <div class="font-bold">{{$employees[$choosedEmployee]['employee_name']}}</div>
             </div>
             
            <div class="p-1">
            Serviços selecionados:<br>
                @foreach($chosen_services as $item=>$service)
                   - {{$service}} <br>
                @endforeach
            </div>
        @endif
        @if(isset($chosen_branch))
        @if($choosedEmployee !== null || !empty($chosen_services) )
        <button wire:click="chosenService('{{serialize($chosen_services)}}')"
        onclick = "closeModal('professionals')" class="rounded bg-green-300 text-white p-1">Confirmar</button>
        <button wire:click="apague()" class="rounded bg-red-300 text-white p-1">Cancelar</button>
            @endif
        @endif
    </div>
     --}}
   





</div>

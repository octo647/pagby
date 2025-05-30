<div wire:ignore>
 
    {{-- Do your work, then step back. --}}
    <select wire:model="chosen_branch" name="branch" id="branch" onchange="on()">
    
    @foreach ($branches as $branch ) 
    @if($branch['branch_name']== null)
       <option value="">Selecione a filial</option>
    @else
       <option  value="{{$branch['branch_name']}}" >{{$branch['branch_name']}}</option>
    @endif       
    @endforeach       
    </select>
    
    <button hidden id="ok" wire:click="chosenBranch(document.getElementById('branch').value)"
                 class="text-white bg-blue-600 hover:bg-blue-800 focus:ring-4  font-medium
                         rounded-lg text-base  items-center px-3 py-2.5 text-center mr-2">
        ok
    </button> 
    
    


<script>
function on() {
    document.getElementById("ok").hidden =false;
 }
 function show() {
    document.getElementById("choose-employee").hidden =false;
    document.getElementById("prossiga").hidden =true;
 }
    
 
</script>
    
</div>

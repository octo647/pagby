<div>
    {{-- Do your work, then step back. --}}
    <select wire:model="chosen_branch" name="branch" id="branch" onchange="chosenBranch(this.value)" 
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        <option value="" selected>Selecione a filial</option>
        @foreach ($branches as $branch) 
           @if(!empty($branch['branch_name'])) 
                <option value="{{$branch['branch_name']}}">{{$branch['branch_name']}}</option>
           @endif    
        @endforeach       
    </select>

    <script>
        function chosenBranch(value) {
            if (!value) {
                console.log("Nenhuma filial selecionada.");
                return;
            }
            console.log("Filial selecionada:", value);
            @this.call('chosenBranch', value); // Chama o método Livewire diretamente
        }

        function show() {
            const chooseEmployee = document.getElementById("choose-employee");
            if (chooseEmployee) {
                chooseEmployee.hidden = false;
            }
            const prossiga = document.getElementById("prossiga");
            if (prossiga) {
                prossiga.hidden = true;
            }
        }
    </script>
</div>
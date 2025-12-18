<div>
    {{-- Do your work, then step back. --}}

    @php $branchCount = count($branches); @endphp
    <script>
        window.dispatchEvent(new CustomEvent('branchesCount', { detail: { count: {{ $branchCount }} } }));
    </script>

    @if($branchCount === 1)
        <div class="font-semibold text-lg text-gray-700">
            {{ $branches[0]['branch_name'] ?? '' }}
        </div>
        <script>
            // Informa ao Alpine/Blade pai que já há filial escolhida
            window.dispatchEvent(new CustomEvent('branchChosen', { detail: { branch: "{{ $branches[0]['branch_name'] ?? '' }}" } }));
            // Garante que os demais componentes Livewire recebam o evento e estado
            window.__branch_auto_applied__ = window.__branch_auto_applied__ || false;
            if (!window.__branch_auto_applied__) {
                window.__branch_auto_applied__ = true;
                if (typeof window.Livewire !== 'undefined') {
                    // Atualiza o estado do próprio componente e dispara eventos internos
                    @this.call('chosenBranch', "{{ $branches[0]['branch_name'] ?? '' }}");
                }
            }
        </script>
    @elseif($branchCount > 1)
        <select wire:model="chosen_branch" name="branch" id="branch" onchange="chosenBranch(this.value)" 
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            <option value="" selected>Selecione a filial</option>
            @foreach ($branches as $branch) 
               @if(!empty($branch['branch_name'])) 
                    <option value="{{$branch['branch_name']}}">{{$branch['branch_name']}}</option>
               @endif    
            @endforeach       
        </select>
    @endif

    <script>
        function chosenBranch(value) {
            if (!value) {
                console.log("Nenhuma filial selecionada.");
                return;
            }
            console.log("Filial selecionada:", value);
            window.dispatchEvent(new CustomEvent('branchChosen', { detail: { branch: value } }));
            @this.call('chosenBranch', value); // Chama o método Livewire diretamente
        }
    </script>
</div>
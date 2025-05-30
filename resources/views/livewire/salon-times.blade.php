<div>
    <div class="relative overflow-x-auto shadow-md rounded-lg">

        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr><th>Nome</th><th>seg</th><th>ter</th><th>qua</th><th>qui</th><th>sex</th><th>sab</th><th>dom</th><th></th>
            </tr>
            </thead>

            @foreach($officehours as $index=>$officehour)
                <tr wire:key="{{$index}}" class="bg-white border-b hover:bg-gray-50">
                    <td>

                        {{$officehour['funcionario']}}

                    </td>
                    <td>
                        @if($editedIndex !== $index)
                            {{$officehour['seg_ini'] ."-". $officehour['seg_fim']}}
                        @else
                            <input type='time'  size='4' wire:model.defer='officehours.{{$index}}.seg_ini'>
                            <input type='time'  size='4' wire:model.defer='officehours.{{$index}}.seg_fim'>
                        @endif
                    </td>
                    <td>
                        @if($editedIndex !== $index)
                            {{$officehour['ter_ini'] ."-". $officehour['ter_fim']}}
                        @else
                            <input type='time' size='4'  wire:model.defer='officehours.{{$index}}.ter_ini'>
                            <input type='time' size='4'  wire:model.defer='officehours.{{$index}}.ter_fim'>
                        @endif
                    </td>
                    <td>
                        @if($editedIndex !== $index)
                            {{$officehour['qua_ini'] ."-". $officehour['qua_fim']}}
                        @else
                            <input type='time'  size='4' wire:model.defer='officehours.{{$index}}.qua_ini'>
                            <input type='time'  size='4' wire:model.defer='officehours.{{$index}}.qua_fim'>
                        @endif
                    </td>
                    <td>
                        @if($editedIndex !== $index)
                            {{$officehour['qui_ini'] ."-". $officehour['qui_fim']}}
                        @else
                            <input type='time' size='4' wire:model.defer='officehours.{{$index}}.qui_ini'>
                            <input type='time' size='4' wire:model.defer='officehours.{{$index}}.qui_fim'>
                        @endif
                    </td>
                    <td>
                        @if($editedIndex !== $index)
                            {{$officehour['sex_ini'] ."-". $officehour['sex_fim']}}
                        @else
                            <input type='time' size='4'  wire:model.defer='officehours.{{$index}}.sex_ini'>
                            <input type='time' size='4'  wire:model.defer='officehours.{{$index}}.sex_fim'>
                        @endif
                    </td>
                    <td>
                        @if($editedIndex !== $index)
                            {{$officehour['sab_ini'] ."-". $officehour['sab_fim']}}
                        @else
                            <input type='time'  size='4' wire:model.defer='officehours.{{$index}}.sab_ini'>
                            <input type='time'  size='4' wire:model.defer='officehours.{{$index}}.sab_fim'>
                        @endif
                    </td>
                    <td>
                        @if($editedIndex !== $index)
                            {{$officehour['dom_ini'] ."-". $officehour['dom_fim']}}
                        @else
                            <input type='time' size='4'  wire:model.defer='officehours.{{$index}}.dom_ini'>
                            <input type='time' size='4'  wire:model.defer='officehours.{{$index}}.dom_fim'>
                        @endif
                    </td>
                    <td>
                        @if($editedIndex !== $index)
                            <button type="button" class="btn btn-primary" wire:click.prevent="editMT({{$index}})">
                                Editar
                            </button>

                        @else
                            <button  wire:click="saveMT({{$index}})">
                                Salvar
                            </button>
                        @endif
                    </td>
                <tr>
            @endforeach

        </table>
    </div>
</div>


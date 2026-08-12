<div>
    <div class="relative overflow-x-auto shadow-md rounded-lg">

        <table class="table-auto w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr><th></th>
                    @foreach($intervals as $index=>$interval)
                        <td wire:key="{{$index}}" class="bg-white border-b hover:bg-gray-50">
                            {{$interval['funcionario']}}
                        </td>
                    @endforeach
                </tr>
            </thead>
                <tr>
                    <th>seg</th>
                        @foreach($intervals as $index=>$interval)
                        <td>
                            @if($editedIndex !== $index)
                                <span  STYLE="font-size:8.0pt">({{$interval['seg_int1']}})</span><br>
                                <span  STYLE="font-size:8.0pt">({{$interval['seg_int2']}})</span>
                            @else
                                <input type='text'   wire:model.defer='intervals.{{$index}}.seg_int1'>
                                <input type='text'   wire:model.defer='intervals.{{$index}}.seg_int2'>
                            @endif
                        </td>
                        @endforeach
                </tr>
                <tr>
                    <th>ter</th>
                        @foreach($intervals as $index=>$interval)
                        <td>
                            @if($editedIndex !== $index)
                                <span  STYLE="font-size:8.0pt">({{$interval['ter_int1']}})</span><br>
                                <span  STYLE="font-size:8.0pt">({{$interval['ter_int2']}})</span>
                            @else
                                <input type='text'  wire:model.defer='intervals.{{$index}}.ter_int1'>
                                <input type='text'  wire:model.defer='intervals.{{$index}}.ter_int2'>
                            @endif
                        </td>
                        @endforeach
                </tr>
                <tr>
                    <th>qua</th>
                    @foreach($intervals as $index=>$interval)
                    <td>
                        @if($editedIndex !== $index)
                            <span  STYLE="font-size:8.0pt">({{$interval['qua_int1']}})</span><br>
                            <span  STYLE="font-size:8.0pt">({{$interval['qua_int2']}})</span>
                        @else
                            <input type='text' wire:model.defer='intervals.{{$index}}.qua_int1'>
                            <input type='text' wire:model.defer='intervals.{{$index}}.qua_int2'>
                        @endif
                    </td>
                    @endforeach
                </tr>
                <tr>
                    <th>qui</th>
                    @foreach($intervals as $index=>$interval)
                        <td>
                        @if($editedIndex !== $index)
                            <span  STYLE="font-size:8.0pt">({{$interval['qui_int1']}})</span><br>
                            <span  STYLE="font-size:8.0pt">({{$interval['qui_int2']}})</span>
                        @else
                            <input type='text' wire:model.defer='intervals.{{$index}}.qui_int1'>
                            <input type='text' wire:model.defer='intervals.{{$index}}.qui_int2'>
                        @endif
                        </td>
                    @endforeach
                <tr>
                    <th>sex</th>
                    @foreach($intervals as $index=>$interval)
                        <td>
                            @if($editedIndex !== $index)
                             <span  STYLE="font-size:8.0pt">({{$interval['sex_int1']}})</span><br>
                             <span  STYLE="font-size:8.0pt">({{$interval['sex_int2']}})</span>
                            @else
                                <input type='text'  wire:model.defer='intervals.{{$index}}.sex_int1'>
                                <input type='text'  wire:model.defer='intervals.{{$index}}.sex_int2'>
                            @endif
                        </td>
                    @endforeach
                </tr>
                <tr>
                    <th>sab</th>
                    @foreach($intervals as $index=>$interval)
                        <td>
                            @if($editedIndex !== $index)
                             <span  STYLE="font-size:8.0pt">({{$interval['sab_int1']}})</span><br>
                             <span  STYLE="font-size:8.0pt">({{$interval['sab_int2']}})</span>
                            @else
                                <input type='text' wire:model.defer='intervals.{{$index}}.sab_int1'>
                                <input type='text' wire:model.defer='intervals.{{$index}}.sab_int2'>
                            @endif
                        </td>
                    @endforeach
                </tr>
                <tr>
                    <th>dom</th>
                    @foreach($intervals as $index=>$interval)
                        <td>
                            @if($editedIndex !== $index)
                             <span  STYLE="font-size:8.0pt">({{$interval['dom_int1']}})</span><br>
                             <span  STYLE="font-size:8.0pt">({{$interval['dom_int2']}})</span>
                            @else
                                <input type='text'  wire:model.defer='intervals.{{$index}}.dom_int1'>
                                <input type='text'  wire:model.defer='intervals.{{$index}}.dom_int2'>
                            @endif
                        </td>
                    @endforeach
                </tr>
                <tr>
                    <th></th>
                    @foreach($intervals as $index=>$interval)
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
                    @endforeach
            </tr>

        </table>
    </div>
</div>


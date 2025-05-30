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
                @foreach ($employees as $index=>$employee)
                <td>
                    @if(in_array($service['id'], $employee['services']))
                        <a wire:click="changeService({{$service['id']}}, {{$employee['id']}})" >&#10004;</a>
                    @else
                        <a wire:click="changeService({{$service['id']}}, {{$employee['id']}})" >Adicionar</a>
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach


        </table>
    </div>
</div>


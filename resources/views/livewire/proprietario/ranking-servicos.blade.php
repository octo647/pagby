<div>
    {{-- Stop trying to control. --}}
   
<table class="min-w-full tabela-escura">
    <thead>
        <tr class="text-left">
            <th>Serviço</th>
            <th>Total de Solicitações</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rankingServicos as $servico => $total)
            <tr>
                <td>{{ $servico }}</td>
                <td>{{ $total }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
</div>

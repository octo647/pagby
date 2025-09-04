<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}
 
<table class="tabela-escura text-left">
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Serviço</th>
            <th>Profissional</th>
            <th>Nota</th>
            <th>Comentário</th>
            <th>Data</th>
        </tr>
    </thead>
    <tbody>
        @foreach($avaliacoes as $avaliacao)
            <tr>
                <td>{{ $avaliacao->user->name ?? '-' }}</td>
                <td>{{ $avaliacao->appointment->services ?? '-' }}</td>
                <td>{{ $avaliacao->appointment->employee->name ?? '-' }}</td>
                <td>{{ $avaliacao->avaliacao }}</td>
                <td>{{ $avaliacao->comentario ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($avaliacao->created_at)->format('d/m/Y') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
</div>

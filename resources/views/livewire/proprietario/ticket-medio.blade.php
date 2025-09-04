<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
    <div class="flex gap-4 mb-4">
    <div class="flex flex-col">
        <label for="periodStart" class="mb-1 text-sm">Data inicial</label>
        <input id="periodStart" type="date" wire:model.live="periodStart" class="border rounded p-2">
    </div>
    <div class="flex flex-col mb-4">
        <label for="periodEnd" class="mb-1 text-sm">Data final</label>
        <input id="periodEnd" type="date" wire:model.live="periodEnd" class="border rounded p-2">
    </div>
    <div class="flex flex-col mb-4">
        <label for="minAppointments" class="mb-1 text-sm">Mínimo de agendamentos</label>
    <input id="minAppointments" type="number" min="1" wire:model.live="minAppointments" class="border rounded p-2 w-32">
    </div>
</div>
    
<table class="min-w-full tabela-escura">
    <thead>
        <tr class="text-left">
            <th>Nome</th>
            <th>Email</th>
            <th>Total de Agendamentos</th>
            <th>Total Gasto</th>
            <th>Ticket Médio</th>
        </tr>
    </thead>
    <tbody>
        @foreach($clientes as $cliente)
            <tr>
                <td>{{ $cliente->name }}</td>
                <td>{{ $cliente->email }}</td>
                <td>{{ $cliente->total_agendamentos }}</td>
                <td>R$ {{ number_format($cliente->total_gasto, 2, ',', '.') }}</td>
                <td>R$ {{ number_format($cliente->ticket_medio, 2, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<div class="mt-4">
    {{ $clientes->links() }}
</div>
</div>

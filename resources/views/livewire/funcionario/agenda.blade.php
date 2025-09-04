<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
    
<table class="min-w-full bg-white rounded shadow">
    <thead>
        <tr class="text-left">
            <th class="px-4 py-2">Data</th>
            <th class="px-4 py-2">Horário</th>
            <th class="px-4 py-2">Cliente</th>
            <th class="px-4 py-2">Serviço</th>
        </tr>
    </thead>
    <tbody>
        @forelse($agendamentos as $agendamento)
            <tr>
                <td class="px-4 py-2">{{ \Carbon\Carbon::parse($agendamento->appointment_date)->format('d/m/Y') }}</td>
              
                <td class="px-4 py-2">{{ \Carbon\Carbon::createFromFormat('H:i:s', $agendamento->start_time)->format('H:i') }} - {{\Carbon\Carbon::createFromFormat('H:i:s', $agendamento->end_time)->format('H:i') }} </td>
                <td class="px-4 py-2">{{ $agendamento->customer->name ?? '-' }}</td>
                <td class="px-4 py-2">{{ $agendamento->services }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="px-4 py-2 text-center text-gray-500">Nenhum agendamento futuro.</td>
            </tr>
        @endforelse
    </tbody>
</table>
</div>

<div>
    
    <div class="mb-4 flex gap-4">
    <div class="flex flex-col">
        <label for="search" class="mb-1 text-sm">Buscar nome ou e-mail</label>
        <input id="search" type="text" wire:model.live="search" class="border rounded p-2">
    </div>
    <div class="flex flex-col">
        <label for="minAppointments" class="mb-1 text-sm">Mínimo de agendamentos</label>
        <input id="minAppointments" type="number" wire:model.live="minAppointments" min="1" class="border rounded p-2">
    </div>
    <div class="flex flex-col">
        <label for="periodStart" class="mb-1 text-sm">Data inicial</label>
        <input id="periodStart" type="date" wire:model.live="periodStart" class="border rounded p-2">
    </div>
    <div class="flex flex-col">
        <label for="periodEnd" class="mb-1 text-sm">Data final</label>
        <input id="periodEnd" type="date" wire:model.live="periodEnd" class="border rounded p-2">
    </div>
    <div class="flex flex-col">
        <label for="daysSinceLast" class="mb-1 text-sm">Dias sem agendar</label>
        <input id="daysSinceLast" type="number" wire:model.live="daysSinceLast" min="1" class="border rounded p-2">
    </div>
</div>

{{-- Os dados de $clientes já devem estar processados no componente Livewire --}}
    <table class="min-w-full tabela-escura">
        <thead>
            <tr class="text-left">
                <th>Nome</th>
                <th>Email</th>
                <th>WhatsApp</th>
                <th>Total de Agendamentos</th>
                <th>Primeiro Agendamento</th>
                <th>Último Agendamento</th>
                <th>Dias desde o último</th>
            </tr>
        </thead>
        <tbody>
       
            @foreach($clientes as $cliente) 
            
                <tr>
                    <td>{{ $cliente->name }}</td>
                    <td>                   
                        <a href="mailto:{{ $cliente->email }}">{{ $cliente->email }}</a>

                    </td>
                    <td>
                    @if($cliente->whatsapp)
                    <a href="https://wa.me/{{ $cliente['phone'] }}" target"_blank">{{ $cliente['phone'] }}</a>
                    @endif
                    </td>
                    <td>
                        {{ $cliente->client_appointments_count }}
                    </td>
                    <td>
                      {{ optional($cliente->clientAppointments->sortBy('appointment_date')->first())->appointment_date
                        ? \Carbon\Carbon::parse($cliente->clientAppointments->sortBy('appointment_date')->first()->appointment_date)->format('d/m/Y')
                        : '-' }}                    
                    </td>
                    <td>
                        {{ optional($cliente->clientAppointments->sortByDesc('appointment_date')->first())->appointment_date
                        ? \Carbon\Carbon::parse($cliente->clientAppointments->sortByDesc('appointment_date')->first()->appointment_date)->format('d/m/Y')
                        : '-' }}                        
                    </td>
                    <td>
                     @php
                        $ultimo = $cliente->clientAppointments->sortByDesc('appointment_date')->first();
                        $dias = $ultimo
                            ? ($ultimo->appointment_date < now()
                                ? \Carbon\Carbon::parse($ultimo->appointment_date)->diffInDays(now())
                                : -now()->diffInDays($ultimo->appointment_date))
                            : null;
                    @endphp
                    
                        {{ round(abs($dias), 0) }}
                    




                        
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <label> 
    <div class="mt-4">
    <input type="checkbox" wire:model.live="showAll">
    Mostrar todos os resultados em uma página
    </label>
    @if(!$showAll)   
    {{ $clientes->links() }} 
    @endif
    </div>   

</div>

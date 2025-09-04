<div>
    {{-- Stop trying to control. --}}
    
    <table class="tabela-escura w-full text-sm text-left">
    <thead>
        <tr>
            
            <th>Funcionário</th>
            <th>Filial</th>
            <th>Cliente</th>
            <th>Serviços</th>
            <th>Data</th>
            <th>Hora</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($agendamentos as $appointment)
            <tr>
                
                <td>
                <a href="#" wire:click.prevent="showUserDetails({{ $appointment->employee->id }})" class="text-blue-600 hover:underline">
                {{ $appointment->employee->name ?? '-' }}
                </a>               
                </td>
                <td>{{ $appointment->branch->branch_name ?? '-' }}</td>
                <td>{{ $appointment->customer->name ?? '-' }}</td>
                
                <td>
                <span class="">{{ $appointment->services }}
                </span>                   
                </td>
                <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }}</td>
                <td>R$ {{ number_format($appointment->total, 2, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>

</table>
@if(method_exists($appointments, 'links'))
<div class="mt-4">
   oi    {{ $appointments->links() }}
</div>
@endif
<div class="mt-8" wire:ignore>
    <canvas id="myChart" height="60" width="300"></canvas>
</div>
<script>
window.renderChart = function() {
    const canvas = document.getElementById('myChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (window.myChartInstance) window.myChartInstance.destroy();
    window.myChartInstance = new Chart(ctx, {
        type: 'bar',
        data: @json($chartData),
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'Serviços realizados por funcionário (últimos 6 meses)' }
            },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Serviços' } },
                x: { title: { display: true, text: 'Mês/Ano' } }
            }
        }
    });
};
document.addEventListener('DOMContentLoaded', window.renderChart);
document.addEventListener('livewire:navigated', window.renderChart);
</script>
@if($appointments->count() > 0 && $showModal && $user)
<div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-lg">
    <div class="modal">
        <h2>Agendamentos de {{ $user->name }}</h2>
        <table>
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-4 py-2">Data</th>
                    <th class="px-4 py-2">Serviço</th>
                    <th class="px-4 py-2">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $appointment)
                    <tr>
                        <td>{{ $appointment->appointment_date->format('d/m/Y') }}</td>
                        <td>{{ $appointment->services ?? '' }}</td>
                        <td>{{ $appointment->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if(method_exists($appointments, 'links'))
        {{ $appointments->links() }}
        @endif
    <button wire:click="closeModal" class="bg-pink-600 text-white px-4 py-2 rounded">Fechar</button>
       
    </div>
</div>
@endif
</div>

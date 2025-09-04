<div>
    {{-- Because she competes with no one, no one can compete with her. --}}
   
    <div class="flex gap-4 mb-4">
    <div class="flex flex-col">
        <label for="periodStart" class="mb-1 text-sm">Data inicial</label>
        <input id="periodStart" type="date" wire:model.live="periodStart" class="border rounded p-2">
    </div>
    <div class="flex flex-col">
        <label for="periodEnd" class="mb-1 text-sm">Data final</label>
        <input id="periodEnd" type="date" wire:model.live="periodEnd" class="border rounded p-2">
    </div>
</div>
<table class="min-w-full tabela-escura">
    <thead>
        <tr class="text-left">
            <th>Dia da Semana</th>
            <th>Total de Agendamentos</th>
        </tr>
    </thead>
    <tbody>
        @foreach($diasPico as $dia)
            <tr>
                <td>{{ $dia['dia'] }}</td>
                <td>{{ $dia['total'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<div class="text-center mt-4 w-[500px]">
    @if(session()->has('message'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif
  

<canvas id="graficoDiasPico" height="120"></canvas>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const labels = @json($diasLabels);
        const valores = @json($diasValores);

        new Chart(document.getElementById('graficoDiasPico').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Agendamentos',
                    data: valores,
                    backgroundColor: '#3b82f6'
                }]
            }
        });
    });
</script>
</div>
</div>

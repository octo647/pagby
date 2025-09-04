<div>
    {{-- Do your work, then step back. --}}
 
<table class="min-w-full tabela-escura">
    <thead>
        <tr >
            <th class="text-left">Hora</th>
            <th class="test-center">Total de Agendamentos</th>
        </tr>
    </thead>

    <tbody>
        @foreach($horarios as $horario)
            <tr>
                <td>{{ str_pad($horario->hora, 2, '0', STR_PAD_LEFT) }}:00</td>
                <td class="text-center">{{ $horario->total }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<div class="w-[400px] mx-auto">
    {{-- Verifica se há horários --}}
    <h3 class="text-lg font-semibold mt-6 mb-2">Gráfico de Horários de Pico</h3>


<canvas id="graficoPico" height="100"></canvas>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const dados = @json([
        'horasLabels' => $horasLabels,
        'horasValores' => $horasValores
    ]);
    new Chart(document.getElementById('graficoPico').getContext('2d'), {
        type: 'bar',
        data: {
            labels: dados.horasLabels,
            datasets: [{
                label: 'Agendamentos',
                data: dados.horasValores,
                backgroundColor: '#3b82f6'
            }]
        }
    });
</script>
</div>
</div>

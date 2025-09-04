<div>
    {{-- Because she competes with no one, no one can compete with her. --}}
    
<table class="min-w-full tabela-escura">
    <thead>
        <tr>
            <th class="text-left">Mês</th>
            <th class="text-left">Faturamento</th>
        </tr>
    </thead>
    <tbody>
        @foreach($faturamentoMensal as $mes => $total)
            <tr>
                <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $mes)->format('m/Y') }}</td>
                <td>R$ {{ number_format($total, 2, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<canvas id="graficoFaturamento" height="50"></canvas>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('graficoFaturamento').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->format('m/Y'), array_keys($faturamentoMensal))) !!},
            datasets: [{
                label: 'Faturamento (R$)',
                data: {!! json_encode(array_values($faturamentoMensal)) !!},
                backgroundColor: '#3b82f6'
            }]
        }
    });
</script>
</div>

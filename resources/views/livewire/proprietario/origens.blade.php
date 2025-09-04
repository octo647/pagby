<div>
    {{-- Because she competes with no one, no one can compete with her. --}}
    
<table class="min-w-full tabela-escura">
    <thead>
        <tr>
            <th class="text-left">Origem</th>
            <th class="text-left">Total de Clientes</th>
        </tr>
    </thead>
    <tbody>
        @foreach($origensClientes as $origem)
            <tr>
                <td>{{ $origem->origin ?? 'Não informado' }}</td>
                <td>{{ $origem->total }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<div class="w-[330px] mx-auto mt-6">
   

<canvas id="graficoOrigemClientes" height="100"></canvas>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('graficoOrigemClientes').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: @json($origensClientesLabels),
            datasets: [{
                data: @json($origensClientesValores),
                backgroundColor: [
                    '#3b82f6', '#f59e42', '#10b981', '#ef4444', '#a78bfa', '#f472b6', '#facc15', '#34d399', '#6366f1', '#f87171'
                ]
            }]
        },
        options: {
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
</div>
 
</div>

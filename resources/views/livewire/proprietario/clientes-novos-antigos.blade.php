<div>
    {{-- esta página mostra os clientes novos e os clientes recorrentes
    em um período --}}
 
    <canvas id="graficoRetencao" height="120"></canvas>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const labels = @json($retencaoLabels);
        const novos = @json($retencaoNovos);
        const recorrentes = @json($retencaoRecorrentes);

        new Chart(document.getElementById('graficoRetencao').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Novos',
                        data: novos,
                        backgroundColor: '#34d399'
                    },
                    {
                        label: 'Recorrentes',
                        data: recorrentes,
                        backgroundColor: '#3b82f6'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    x: { stacked: true },
                    y: { stacked: true }
                }
            }
        });
    });
</script>
    <div class="mt-4">
        <p class="text-sm text-gray-500">
            Este gráfico mostra a quantidade de clientes novos e antigos
            que utilizaram os serviços da empresa no período selecionado.
            Clientes novos são aqueles que realizaram sua primeira compra
            no período, enquanto clientes antigos são aqueles que já haviam
            realizado compras anteriormente.
        </p>        
        <p class="text-sm text-gray-500 mt-2">
            A análise desses dados pode ajudar a entender o comportamento
            dos clientes e a eficácia das estratégias de marketing e
            retenção de clientes.
        </p>    
        </div>
    
</div>

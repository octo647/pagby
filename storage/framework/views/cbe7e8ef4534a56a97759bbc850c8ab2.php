<div>
    
    <div class="mb-6 bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="p-4 border-b bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Origem dos Clientes</h3>
        </div>
        
        
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Origem</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Total de Clientes</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Percentual</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $totalGeral = $origensClientes->sum('total');
                    ?>
                    <?php $__empty_1 = true; $__currentLoopData = $origensClientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $origem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $percentual = $totalGeral > 0 ? round(($origem->total / $totalGeral) * 100, 1) : 0;
                        ?>
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">
                                <?php echo e($origem->origin ?? 'Não informado'); ?>

                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <?php echo e($origem->total); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3 text-center font-semibold text-purple-600">
                                <?php echo e($percentual); ?>%
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                                Nenhum dado de origem encontrado.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <div class="md:hidden p-4 space-y-3">
            <?php
                $totalGeral = $origensClientes->sum('total');
            ?>
            <?php $__empty_1 = true; $__currentLoopData = $origensClientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $origem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $percentual = $totalGeral > 0 ? round(($origem->total / $totalGeral) * 100, 1) : 0;
                    $cores = ['bg-blue-100 text-blue-800', 'bg-orange-100 text-orange-800', 'bg-green-100 text-green-800', 'bg-red-100 text-red-800', 'bg-purple-100 text-purple-800', 'bg-pink-100 text-pink-800', 'bg-yellow-100 text-yellow-800', 'bg-emerald-100 text-emerald-800', 'bg-indigo-100 text-indigo-800', 'bg-rose-100 text-rose-800'];
                    $cor = $cores[$index % count($cores)];
                ?>
                <div class="border border-gray-200 rounded-lg p-3 <?php echo e(explode(' ', $cor)[0]); ?> <?php echo e(str_replace('text-', 'border-', explode(' ', $cor)[1])); ?>">
                    <div class="flex justify-between items-center">
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">
                                <?php echo e($origem->origin ?? 'Não informado'); ?>

                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold <?php echo e(explode(' ', $cor)[1]); ?>">
                                <?php echo e($origem->total); ?>

                            </div>
                            <div class="text-sm font-medium <?php echo e(explode(' ', $cor)[1]); ?>">
                                <?php echo e($percentual); ?>%
                            </div>
                        </div>
                    </div>
                    
                    
                    <div class="mt-2">
                        <div class="bg-white bg-opacity-50 rounded-full h-2">
                            <div class="<?php echo e(str_replace('100', '500', explode(' ', $cor)[0])); ?> h-2 rounded-full" 
                                 style="width: <?php echo e($percentual); ?>%"></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-8 text-gray-500">
                    <div class="text-4xl mb-2">📊</div>
                    <p>Nenhum dado de origem encontrado.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="bg-white p-4 rounded-lg shadow-sm border">
        <h3 class="text-lg font-semibold mb-4 text-gray-800 text-center">Distribuição por Origem</h3>
        <div class="w-full overflow-hidden">
            <div class="relative" style="height: 400px;">
                <canvas id="graficoOrigemClientes"></canvas>
            </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('graficoOrigemClientes').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($origensClientesLabels, 15, 512) ?>,
            datasets: [{
                data: <?php echo json_encode($origensClientesValores, 15, 512) ?>,
                backgroundColor: [
                    '#3b82f6', '#f59e0b', '#10b981', '#ef4444', '#8b5cf6', '#ec4899', '#f59e0b', '#06d6a0', '#6366f1', '#f87171'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: window.innerWidth < 768 ? 'bottom' : 'right',
                    labels: {
                        padding: 15,
                        font: {
                            size: window.innerWidth < 768 ? 11 : 12
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed * 100) / total).toFixed(1);
                            return context.label + ': ' + context.parsed + ' clientes (' + percentage + '%)';
                        }
                    }
                }
            },
            layout: {
                padding: {
                    top: 10,
                    bottom: 10,
                    left: 10,
                    right: 10
                }
            }
        }
    });
</script>
</div>
 
</div>
<?php /**PATH /var/www/pagby/resources/views/livewire/proprietario/origens.blade.php ENDPATH**/ ?>
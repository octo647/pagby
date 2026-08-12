<div>
    <h2 class="text-2xl font-bold mb-6">Controle de Pagamentos dos Planos</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2 border">ID</th>
                    <th class="px-4 py-2 border">Salão</th>
                    <th class="px-4 py-2 border">Plano</th>
                    <th class="px-4 py-2 border">Início</th>
                    <th class="px-4 py-2 border">Valor</th>
                    <th class="px-4 py-2 border">Status</th>
                    <th class="px-4 py-2 border">ID Pagamento</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="px-4 py-2 border text-center"><?php echo e($payment->id); ?></td>
                    <td class="px-4 py-2 border"><?php echo e($payment->tenant->name ?? '-'); ?></td>
                    <td class="px-4 py-2 border"><?php echo e($payment->plan); ?></td>
                    <td class="px-4 py-2 border"><?php echo e($payment->created_at ? $payment->created_at->format('d/m/Y H:i') : '-'); ?></td>
                    <td class="px-4 py-2 border text-right">R$ <?php echo e(number_format($payment->amount, 2, ',', '.')); ?></td>
                    <td class="px-4 py-2 border"><?php echo e($payment->status); ?></td>
                    <td class="px-4 py-2 border">
                        <?php if($payment->asaas_payment_id): ?>
                            <a href="<?php echo e(url('/admin/asaas/verificar/' . $payment->asaas_payment_id)); ?>" class="text-blue-600 underline hover:text-blue-800" target="_blank">
                                <?php echo e($payment->asaas_payment_id); ?>

                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="px-4 py-2 border text-center">Nenhum pagamento encontrado.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php /**PATH /var/www/pagby/resources/views/livewire/admin/planos.blade.php ENDPATH**/ ?>